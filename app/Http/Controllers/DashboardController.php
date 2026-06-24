<?php
namespace App\Http\Controllers;

use App\Models\ApprovalTransaction;
use App\Models\InboundMaster;
use App\Models\OutboundMaster;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $pageTitle = 'Dashboard';

        return view('dashboard', compact('title', 'pageTitle'));
    }

    public function summary()
    {
        return response()->json([
            'cards' => [
                'inbound_today' => InboundMaster::whereDate('created_at', today())->count(),
                'outbound_today' => OutboundMaster::whereDate('created_at', today())->count(),
                'pending_approval' => ApprovalTransaction::where('status', 'PENDING')->count(),
                'low_stock' => Stock::whereRaw('(qty_on_hand - qty_reserved) <= 5')->count(),
            ],

            'inbound_status' => InboundMaster::select('inb_stat', DB::raw('COUNT(*) as total'))
                ->groupBy('inb_stat')
                ->pluck('total', 'inb_stat'),

            'outbound_status' => OutboundMaster::select('outb_stat', DB::raw('COUNT(*) as total'))
                ->groupBy('outb_stat')
                ->pluck('total', 'outb_stat'),

            'recent_inbound' => InboundMaster::latest()
                ->limit(5)
                ->get(['inb_code', 'inb_supplier', 'inb_stat', 'created_at']),

            'recent_outbound' => OutboundMaster::latest()
                ->limit(5)
                ->get(['outb_code', 'outb_customer', 'outb_stat', 'created_at']),

            'stock_by_warehouse' => Stock::join('warehouses as wh', 'wh.wh_id', '=', 'stocks.ref_wh_id')
                ->select('wh.wh_name', DB::raw('SUM(qty_on_hand - qty_reserved) as available_qty'))
                ->groupBy('wh.wh_name')
                ->get(),
        ]);
    }
}