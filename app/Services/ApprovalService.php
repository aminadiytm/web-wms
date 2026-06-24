<?php
namespace App\Services;

use App\Models\ApprovalLog;
use App\Models\ApprovalRoute;
use App\Models\ApprovalTransaction;
use App\Models\InboundMaster;
use App\Models\OutboundMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApprovalService
{
    public function __construct(
        private FonnteService $fonnteService
    ) {}

    public function createInboundApproval(InboundMaster $inbound, int $approvalRouteId): ApprovalTransaction
    {
        return DB::transaction(function () use ($inbound, $approvalRouteId) {
            $route = ApprovalRoute::where('approval_route_id', $approvalRouteId)
                ->where('transaction_type', 'INBOUND')
                ->where('is_active', true)
                ->firstOrFail();

            $approval = ApprovalTransaction::create([
                'transaction_type' => 'INBOUND',
                'transaction_id' => $inbound->inb_id,
                'transaction_code' => $inbound->inb_code,
                'approval_route_id' => $route->approval_route_id,
                'approver_user_id' => $route->approver_user_id,
                'status' => 'PENDING',
                'token' => Str::random(64),
                'expired_at' => now()->addDays(3),
                'wa_target' => $route->approver->phone,
                'created_by' => Auth::id(),
            ]);

            ApprovalLog::create([
                'approval_id' => $approval->approval_id,
                'action' => 'CREATED',
                'user_id' => Auth::id(),
            ]);

            $this->sendApprovalWa($approval);

            return $approval;
        });
    }

    public function createOutboundApproval(OutboundMaster $outbound, int $approvalRouteId): ApprovalTransaction
    {
        return DB::transaction(function () use ($outbound, $approvalRouteId) {
            $route = ApprovalRoute::where('approval_route_id', $approvalRouteId)
                ->where('transaction_type', 'OUTBOUND')
                ->where('is_active', true)
                ->firstOrFail();

            $approval = ApprovalTransaction::create([
                'transaction_type' => 'OUTBOUND',
                'transaction_id' => $outbound->outb_id,
                'transaction_code' => $outbound->outb_code,
                'approval_route_id' => $route->approval_route_id,
                'approver_user_id' => $route->approver_user_id,
                'status' => 'PENDING',
                'token' => \Illuminate\Support\Str::random(64),
                'expired_at' => now()->addDays(3),
                'wa_target' => $route->approver->phone,
                'created_by' => Auth::id(),
            ]);

            ApprovalLog::create([
                'approval_id' => $approval->approval_id,
                'action' => 'CREATED',
                'user_id' => Auth::id(),
            ]);

            $this->sendApprovalWa($approval);

            return $approval;
        });
    }


    public function sendApprovalWa(ApprovalTransaction $approval): void
    {
        $message = $this->buildInboundMessage($approval);

        $sent = $this->fonnteService->sendMessage(
            $approval->wa_target,
            $message
        );

        $approval->update([
            'wa_sent_at' => $sent ? now() : null,
        ]);

        ApprovalLog::create([
            'approval_id' => $approval->approval_id,
            'action' => $sent ? 'SENT_WA' : 'FAILED_WA',
            'user_id' => Auth::id(),
            'notes' => $sent ? null : 'Failed to send WhatsApp message',
        ]);
    }

    private function buildInboundMessage(ApprovalTransaction $approval): string
    {
        $viewUrl = route('approval.view', $approval->token);
        $approveUrl = route('approval.approve', $approval->token);
        $rejectUrl = route('approval.reject', $approval->token);

        return
        "Approval Inbound {$approval->transaction_type} {$approval->transaction_code}

        Silakan review dokumen berikut.

        View:
        {$viewUrl}

        Approve:
        {$approveUrl}

        Not Approve:
        {$rejectUrl}";
    }

}