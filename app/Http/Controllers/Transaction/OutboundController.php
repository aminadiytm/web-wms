<?php

namespace App\Http\Controllers\Transaction;

use App\Helpers\MenuPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\ApprovalRoute;
use App\Models\OutboundDet;
use App\Models\OutboundMaster;
use App\Services\ApprovalService;
use App\Services\DocumentNumberService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class OutboundController extends Controller
{
    public function __construct(
        private ApprovalService $approvalService,
        private StockService $stockService
    ) {}

    public function index()
    {
        $title = 'Outbound Master';
        $pageTitle = 'Transaction / Outbound Master';

        return view('transaction.outbound.outbound', compact('title', 'pageTitle'));
    }

    public function list()
    {
        $query = OutboundMaster::leftJoin('warehouses as wh', 'wh.wh_id', '=', 'outb_mstr.ref_wh_id')
            ->select([
                'outb_id as id',
                'outb_code as code',
                'ref_wh_id as wh',
                'wh.wh_name as whnm',
                'outb_customer as customer',
                'outb_stat as status',
                'outb_shipped as shipped',
                'outb_add_by as add',
                'outb_upd_by as upd',
                'outb_mstr.created_at as crea',
                'outb_mstr.updated_at as upda',
            ])
            ->orderBy('outb_mstr.created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $status = strtoupper($row->status ?? 'DRAFT');
                $canEdit = MenuPermissionHelper::canEdit('transaction.outbIndex');

                if ($status === 'DRAFT') {
                    if($canEdit)
                        {
                            return '
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="' . route('transaction.outbEdit', $row->id) . '" class="action-icon action-icon-edit" title="Edit Outbound">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <span class="text-muted" title="Waiting Approval">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>
                            ';
                        }
                }

                if (in_array($status, ['ISSUED', 'PARTIAL'])) {
                    if($canEdit)
                        {
                            return '
                                <a href="' . route('transaction.outbConfirm', $row->id) . '" class="action-icon action-icon-receive" title="Confirm Picking">
                                    <i class="fas fa-check-double"></i>
                                </a>
                            ';
                        }
                }

                if ($status === 'SHIPPED') {
                    return '
                        <span class="text-success" title="Completed">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </span>
                    ';
                }

                if ($status === 'CANCEL') {
                    return '
                        <span class="text-danger" title="Cancelled">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </span>
                    ';
                }

                return '
                    <span class="text-muted" title="No action permission">
                        <i class="fas fa-lock"></i>
                    </span>
                ';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = match(strtoupper($row->status ?? 'DRAFT')) {
                    'ISSUED' => 'bg-gradient-info',
                    'PARTIAL' => 'bg-gradient-warning',
                    'SHIPPED' => 'bg-gradient-success',
                    'CANCEL' => 'bg-gradient-danger',
                    default => 'bg-gradient-secondary',
                };

                return '<span class="badge badge-sm ' . $badgeClass . '">' . e($row->status ?? 'DRAFT') . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function detList(Request $request)
    {
        $request->validate([
            'outb_id' => 'required'
        ]);

        $query = OutboundDet::query()
            ->leftJoin('products as prd', 'prd.prd_id', '=', 'outbd_det.ref_prd_id')
            ->leftJoin('locations as loc', 'loc.loc_id', '=', 'outbd_det.ref_loc_id')
            ->where('outbd_det.ref_outb_id', $request->outb_id)
            ->select([
                'outbd_det.outbd_id as id',
                'prd.prd_name as product_name',
                'loc.loc_desc as location_name',
                'outbd_det.qty_req',
                'outbd_det.qty_picked',
            ])
            ->orderBy('outbd_det.outbd_id', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->toJson();
    }

    public function create()
    {
        return $this->form('create');
    }

    public function edit($id)
    {
        $outbound = OutboundMaster::findOrFail($id);

        if ($outbound->outb_stat !== 'DRAFT') {
            return redirect()
                ->route('transaction.outbIndex')
                ->with('error', 'Outbound yang sudah diproses approval tidak bisa diedit.');
        }

        return $this->form('edit', $id);
    }

    public function confirm($id)
    {
        $outbound = OutboundMaster::findOrFail($id);

        if (!in_array($outbound->outb_stat, ['ISSUED', 'PARTIAL'])) {
            return redirect()
                ->route('transaction.outbIndex')
                ->with('error', 'Outbound belum bisa dikonfirmasi.');
        }

        return $this->form('confirm', $id);
    }

    private function form($mode = 'create', $id = null)
    {
        $isCreate = $mode === 'create';
        $isEdit = $mode === 'edit';
        $isConfirm = $mode === 'confirm';

        $outbound = $id
            ? OutboundMaster::with(['outbounddets.product', 'outbounddets.location'])->findOrFail($id)
            : null;

        $title = match ($mode) {
            'edit' => 'Edit Outbound - ' . $outbound->outb_code,
            'confirm' => 'Confirm Outbound - ' . $outbound->outb_code,
            default => 'Create Outbound',
        };

        $pageTitle = 'Transaction / Outbound Master';

        $approvalRoutes = ApprovalRoute::where('transaction_type', 'OUTBOUND')
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->get();

        return view('transaction.outbound.create.outbCreate', compact(
            'title',
            'pageTitle',
            'outbound',
            'mode',
            'isCreate',
            'isEdit',
            'isConfirm',
            'approvalRoutes'
        ));
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function update(Request $request, $id)
    {
        return $this->save($request, $id);
    }

    private function save(Request $request, $id = null)
    {
        $isEdit = filled($id);

        $rules = [
            'outb_customer' => 'required|string|max:255',
            'outb_shipped' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required',
            'details.*.location_id' => 'required',
            'details.*.qty_req' => 'required|numeric|min:0.01',
        ];

        if (!$isEdit) {
            $rules['wh'] = 'required|exists:warehouses,wh_id';
            $rules['approval_route_id'] = 'required|exists:approval_routes,approval_route_id';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $user = Auth::user()?->name;

            if ($isEdit) {
                $outbound = OutboundMaster::with('outbounddets')->findOrFail($id);

                if ($outbound->outb_stat !== 'DRAFT') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Outbound yang sudah diproses approval tidak bisa diedit.',
                    ], 422);
                }

                foreach ($outbound->outbounddets as $oldDetail) {
                    $this->stockService->releaseReservedStock(
                        $oldDetail->ref_prd_id,
                        $outbound->ref_wh_id,
                        $oldDetail->ref_loc_id,
                        $oldDetail->qty_req
                    );
                }

                OutboundDet::where('ref_outb_id', $outbound->outb_id)->delete();

                $outbound->update([
                    'outb_customer' => $request->outb_customer,
                    'outb_shipped' => $request->outb_shipped,
                    'outb_upd_by' => $user,
                ]);

                $message = 'Outbound draft berhasil diupdate.';
            } else {
                $outbCode = DocumentNumberService::generate('OUTB');

                $outbound = OutboundMaster::create([
                    'outb_code' => $outbCode,
                    'ref_wh_id' => $request->wh,
                    'outb_customer' => $request->outb_customer,
                    'outb_stat' => 'DRAFT',
                    'outb_shipped' => $request->outb_shipped,
                    'outb_add_by' => $user,
                ]);

                $message = 'Outbound berhasil disimpan dengan kode ' . $outbCode;
            }

            foreach ($request->details as $detail) {
                $this->stockService->reserveStock(
                    $detail['product_id'],
                    $outbound->ref_wh_id,
                    $detail['location_id'],
                    $detail['qty_req']
                );

                OutboundDet::create([
                    'ref_outb_id' => $outbound->outb_id,
                    'ref_prd_id' => $detail['product_id'],
                    'ref_loc_id' => $detail['location_id'],
                    'qty_req' => $detail['qty_req'],
                    'qty_picked' => 0,
                    'outbd_add_by' => $user,
                ]);
            }

            if (!$isEdit) {
                $this->approvalService->createOutboundApproval(
                    $outbound,
                    $request->approval_route_id
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('transaction.outbIndex'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan outbound: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function getEditData($id)
    {
        $outbound = OutboundMaster::with([
            'outbounddets.product',
            'outbounddets.location'
        ])->findOrFail($id);

        return response()->json($outbound);
    }

    public function confirmUpdate(Request $request, $id)
    {
        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.id' => 'required|exists:outbd_det,outbd_id',
            'details.*.qty_picked' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $outbound = OutboundMaster::with('outbounddets')
                ->lockForUpdate()
                ->findOrFail($id);

            if (!in_array($outbound->outb_stat, ['ISSUED', 'PARTIAL'])) {
                throw ValidationException::withMessages([
                    'outbound' => 'Outbound belum bisa dikonfirmasi.'
                ]);
            }

            foreach ($request->details as $row) {
                $detail = OutboundDet::where('ref_outb_id', $outbound->outb_id)
                    ->where('outbd_id', $row['id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $pickedQty = (float) $row['qty_picked'];
                $outstanding = $detail->qty_req - $detail->qty_picked;

                if ($pickedQty > $outstanding) {
                    throw ValidationException::withMessages([
                        'qty_picked' => 'Qty picked tidak boleh melebihi outstanding.'
                    ]);
                }

                if ($pickedQty <= 0) {
                    continue;
                }

                $detail->update([
                    'qty_picked' => $detail->qty_picked + $pickedQty,
                    'outbd_confrm_by' => Auth::user()?->name,
                    'outbd_upd_by' => Auth::user()?->name,
                ]);

                $this->stockService->confirmOutboundStock(
                    $detail->ref_prd_id,
                    $outbound->ref_wh_id,
                    $detail->ref_loc_id,
                    $pickedQty
                );
            }

            $totalReq = $outbound->outbounddets()->sum('qty_req');
            $totalPicked = $outbound->outbounddets()->sum('qty_picked');

            $outbound->update([
                'outb_stat' => $totalPicked >= $totalReq ? 'SHIPPED' : 'PARTIAL',
                'outb_upd_by' => Auth::user()?->name,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Outbound berhasil dikonfirmasi.',
            'redirect' => route('transaction.outbIndex'),
        ]);
    }
}
