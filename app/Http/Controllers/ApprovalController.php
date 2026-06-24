<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLog;
use App\Models\ApprovalTransaction;
use App\Models\InboundMaster;
use App\Models\OutboundMaster;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApprovalController extends Controller
{
    public function view($token)
    {
        $title = 'Approval View';
        $pageTitle = 'Approval';

        $approval = ApprovalTransaction::where('token', $token)
            ->firstOrFail();

        $this->authorizeApprovalUser($approval);

        if ($approval->transaction_type === 'INBOUND') {
            $inbound = InboundMaster::with([
                'warehouse',
                'inbounddets.product',
                'inbounddets.location',
            ])->findOrFail($approval->transaction_id);

            ApprovalLog::create([
                'approval_id' => $approval->approval_id,
                'action' => 'VIEWED',
                'user_id' => Auth::id(),
            ]);

            return view('approval.inbound-view', compact('title', 'pageTitle', 'approval', 'inbound'));
        }

        abort(404);
    }

    public function approve($token)
    {
        $pageTitle = 'Approval';

        $approval = ApprovalTransaction::where('token', $token)->firstOrFail();

        if ($approval->status !== 'PENDING') {
            return view('approval.result', [
                'status' => $approval->status,
                'title' => 'Approval Already Processed',
                'pageTitle' => $pageTitle,
                'message' => 'Dokumen ini sudah diproses sebelumnya dengan status: ' . $approval->status,
            ]);
        }

        DB::transaction(function () use ($approval) {
            $approval = ApprovalTransaction::where('approval_id', $approval->approval_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->authorizeApprovalUser($approval);
            $this->validateApproval($approval);

            $approval->update([
                'status' => 'APPROVED',
                'approved_at' => now(),
            ]);

            if ($approval->transaction_type === 'INBOUND') {
                InboundMaster::where('inb_id', $approval->transaction_id)
                    ->where('inb_stat', 'DRAFT')
                    ->update([
                        'inb_stat' => 'ISSUED',
                        'inb_upd_by' => Auth::user()?->name,
                    ]);
            }

            if ($approval->transaction_type === 'OUTBOUND') {
                OutboundMaster::where('outb_id', $approval->transaction_id)
                    ->where('outb_stat', 'DRAFT')
                    ->update([
                        'outb_stat' => 'ISSUED',
                        'outb_upd_by' => Auth::user()?->name,
                    ]);
            }

            ApprovalLog::create([
                'approval_id' => $approval->approval_id,
                'action' => 'APPROVED',
                'user_id' => Auth::id(),
            ]);
        });

        return view('approval.result', [
            'status' => 'APPROVED',
            'title' => 'Approval Success',
            'pageTitle' => $pageTitle,
            'message' => 'Dokumen berhasil di-approve.',
        ]);
    }

    public function __construct(
        private StockService $stockService
    ) {}

    public function reject($token)
    {
        $pageTitle = 'Approval';
    
        $approval = ApprovalTransaction::where('token', $token)->firstOrFail();
    
        if ($approval->status !== 'PENDING') {
            return view('approval.result', [
                'status' => $approval->status,
                'title' => 'Approval Already Processed',
                'pageTitle' => $pageTitle,
                'message' => 'Dokumen ini sudah diproses sebelumnya dengan status: ' . $approval->status,
            ]);
        }
    
        DB::transaction(function () use ($approval) {
            $approval = ApprovalTransaction::where('approval_id', $approval->approval_id)
                ->lockForUpdate()
                ->firstOrFail();
    
            if ($approval->status !== 'PENDING') {
                return;
            }
    
            $this->authorizeApprovalUser($approval);
            $this->validateApproval($approval);
    
            $approval->update([
                'status' => 'REJECTED',
                'rejected_at' => now(),
            ]);
    
            if ($approval->transaction_type === 'INBOUND') {
                InboundMaster::where('inb_id', $approval->transaction_id)
                    ->where('inb_stat', 'DRAFT')
                    ->update([
                        'inb_stat' => 'CANCEL',
                        'inb_upd_by' => Auth::user()?->name,
                    ]);
            }
            
            if ($approval->transaction_type === 'OUTBOUND') {
                $outbound = OutboundMaster::with('outbounddets')
                    ->where('outb_id', $approval->transaction_id)
                    ->where('outb_stat', 'DRAFT')
                    ->firstOrFail();

                foreach ($outbound->outbounddets as $detail) {
                    $this->stockService->releaseReservedStock(
                        $detail->ref_prd_id,
                        $outbound->ref_wh_id,
                        $detail->ref_loc_id,
                        $detail->qty_req
                    );
                }

                $outbound->update([
                    'outb_stat' => 'CANCEL',
                    'outb_upd_by' => Auth::user()?->name,
                ]);
            }
    
            ApprovalLog::create([
                'approval_id' => $approval->approval_id,
                'action' => 'REJECTED',
                'user_id' => Auth::id(),
            ]);
        });
    
        return view('approval.result', [
            'status' => 'REJECTED',
            'title' => 'Not Approved',
            'pageTitle' => $pageTitle,
            'message' => 'Dokumen berhasil di-not approve.',
        ]);
    }

    private function authorizeApprovalUser(ApprovalTransaction $approval): void
    {
        if ((int) $approval->approver_user_id !== (int) Auth::id()) {
            abort(403, 'Anda tidak memiliki akses approval ini.');
        }
    }

    private function validateApproval(ApprovalTransaction $approval): void
    {
        if ($approval->expired_at && now()->greaterThan($approval->expired_at)) {
            throw ValidationException::withMessages([
                'approval' => 'Approval link sudah expired.',
            ]);
        }
    }
}