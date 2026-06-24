<?php
namespace App\Services;

use App\Models\Stock;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function increaseStock(
        int $productId,
        int $warehouseId,
        int $locationId,
        float $qty
    ): void {
        if ($qty <= 0) {
            return;
        }

        $stock = Stock::where('ref_prd_id', $productId)
            ->where('ref_wh_id', $warehouseId)
            ->where('ref_loc_id', $locationId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->increment('qty_on_hand', $qty);
            return;
        }

        Stock::create([
            'ref_prd_id' => $productId,
            'ref_wh_id' => $warehouseId,
            'ref_loc_id' => $locationId,
            'qty_on_hand' => $qty,
            'qty_reserved' => 0,
        ]);
    }

    public function reserveStock(
        int $productId,
        int $warehouseId,
        int $locationId,
        float $qty
    ): void {
        if ($qty <= 0) return;

        $stock = Stock::where('ref_prd_id', $productId)
            ->where('ref_wh_id', $warehouseId)
            ->where('ref_loc_id', $locationId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            throw ValidationException::withMessages([
                'stock' => 'Stock tidak ditemukan.'
            ]);
        }

        $available = $stock->qty_on_hand - $stock->qty_reserved;

        if ($qty > $available) {
            throw ValidationException::withMessages([
                'stock' => 'Qty request melebihi available stock.'
            ]);
        }

        $stock->increment('qty_reserved', $qty);
    }

    public function releaseReservedStock(
        int $productId,
        int $warehouseId,
        int $locationId,
        float $qty
    ): void {
        if ($qty <= 0) return;

        $stock = Stock::where('ref_prd_id', $productId)
            ->where('ref_wh_id', $warehouseId)
            ->where('ref_loc_id', $locationId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($qty > $stock->qty_reserved) {
            throw ValidationException::withMessages([
                'stock' => 'Qty release melebihi reserved stock.'
            ]);
        }

        $stock->decrement('qty_reserved', $qty);
    }

    public function confirmOutboundStock(
        int $productId,
        int $warehouseId,
        int $locationId,
        float $qty
    ): void {
        if ($qty <= 0) return;

        $stock = Stock::where('ref_prd_id', $productId)
            ->where('ref_wh_id', $warehouseId)
            ->where('ref_loc_id', $locationId)
            ->lockForUpdate()
            ->firstOrFail();

        if ($qty > $stock->qty_reserved) {
            throw ValidationException::withMessages([
                'stock' => 'Qty picked melebihi reserved stock.'
            ]);
        }

        if ($qty > $stock->qty_on_hand) {
            throw ValidationException::withMessages([
                'stock' => 'Qty picked melebihi stock on hand.'
            ]);
        }

        $stock->decrement('qty_reserved', $qty);
        $stock->decrement('qty_on_hand', $qty);
    }

}