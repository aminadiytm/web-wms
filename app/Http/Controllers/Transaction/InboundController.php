<?php

namespace App\Http\Controllers\Transaction;

use App\Helpers\MenuPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\ApprovalRoute;
use App\Models\InboundDet;
use App\Models\InboundMaster;
use App\Models\Location;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\ApprovalService;
use App\Services\DocumentNumberService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InboundController extends Controller
{
    public function index() {
        $title = 'Inbound Master';
        $pageTitle = 'Transaction / Inbound Master';

        return view('transaction.inbound.inbound', compact('title', 'pageTitle'));
    }

    public function list() {
        $query = InboundMaster::leftJoin('warehouses as wh', 'wh.wh_id', '=', 'inb_mstr.ref_wh_id')
                ->select([
                    'inb_id as id',
                    'inb_code as code',
                    'ref_wh_id as wh',
                    'wh.wh_name as whnm',
                    'inb_supplier as supp',
                    'inb_stat as status',
                    'inb_rcv as rcv',
                    'inb_add_by as add',
                    'inb_upd_by as upd',
                    'inb_mstr.created_at as crea',
                    'inb_mstr.updated_at as upda',
                ])->orderBy('inb_mstr.created_at', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $status = strtoupper($row->status ?? 'DRAFT');
                $canEdit = MenuPermissionHelper::canEdit('transaction.inbIndex');
            
                if ($status === 'DRAFT') {
                    $html = '<div class="d-flex justify-content-center gap-2">';
            
                    if ($canEdit) {
                        $html .= '
                            <a href="' . route('transaction.inbEdit', $row->id) . '"
                               class="action-icon action-icon-edit"
                               title="Edit Inbound">
                                <i class="fas fa-edit"></i>
                            </a>
                        ';
                    }
            
                    $html .= '
                        <span class="text-muted" title="Waiting approval">
                            <i class="fas fa-clock"></i>
                        </span>
                    ';
            
                    $html .= '</div>';
            
                    return $html;
                }
            
                if (in_array($status, ['ISSUED', 'PARTIAL'])) {
                    if (!$canEdit) {
                        return '
                            <span class="text-muted" title="No action permission">
                                <i class="fas fa-lock"></i>
                            </span>
                        ';
                    }
            
                    return '
                        <a href="' . route('transaction.inbReceive', $row->id) . '"
                           class="action-icon action-icon-receive"
                           title="Receive Inbound">
                            <i class="fas fa-truck-loading"></i>
                        </a>
                    ';
                }
            
                if ($status === 'RECEIVED') {
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
                $badgeClass = match(($row->status ?? 'Draft')) {
                    'RECEIVED' => 'bg-gradient-success',
                    'PARTIAL'  => 'bg-gradient-warning',
                    'ISSUED'   => 'bg-gradient-warning',
                    default    => 'bg-gradient-secondary',
                };

                return '<span class="badge badge-sm ' . $badgeClass . '">' . e($row->status ?? 'Draft') . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return optional($row->crea)->format('d/m/Y, H:i');
            })
            ->editColumn('updated_at', function ($row) {
                return optional($row->upda)->format('d/m/Y, H:i');
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function detList(Request $request)
    {
        $request->validate([
            'inb_id' => 'required'
        ]);

        $query = InboundDet::query()
            ->leftJoin('products as prd', 'prd.prd_id', '=', 'inbd_det.ref_prd_id')
            ->leftJoin('locations as loc', 'loc.loc_id', '=', 'inbd_det.ref_loc_id')
            ->where('inbd_det.ref_inb_id', $request->inb_id)
            ->select([
                'inbd_det.inbd_id as id',
                'prd.prd_name as product_name',
                'loc.loc_desc as location_name',
                'inbd_det.qty_order',
                'inbd_det.qty_rcv',
            ])
            ->orderBy('inbd_det.inbd_id', 'asc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->toJson();
    }

    public function __construct(
        private ApprovalService $approvalService,
        private StockService $stockService
    ) {}

    public function create()
    {
        return $this->form('create');
    }

    public function edit($id)
    {
        $inbound = InboundMaster::findOrFail($id);

        if ($inbound->inb_stat !== 'DRAFT') {
            return redirect()
                ->route('transaction.inbIndex')
                ->with('error', 'Inbound yang sudah diproses approval tidak bisa diedit.');
        }

        return $this->form('edit', $id);
    }

    public function receive($id)
    {
        $inbound = InboundMaster::findOrFail($id);

        if (!in_array($inbound->inb_stat, ['ISSUED', 'PARTIAL'])) {
            return redirect()
                ->route('transaction.inbIndex')
                ->with('error', 'Inbound belum bisa direceive.');
        }

        return $this->form('receive', $id);

    }

    private function form($mode = 'create', $id = null)
    {
        $isCreate = $mode === 'create';
        $isEdit = $mode === 'edit';
        $isReceive = $mode === 'receive';

        $title = match ($mode) {
            'edit' => 'Edit Inbound',
            'receive' => 'Receive Inbound',
            default => 'Create Inbound',
        };


        $pageTitle = 'Transaction / Inbound Master';

        $inbound = $id
            ? InboundMaster::with(['inbounddets.product', 'inbounddets.location'])->findOrFail($id)
            : null;

        if ($id && $inbound) {
            $title .= ' - ' . $inbound->inb_code;
        }

        $approvalRoutes = ApprovalRoute::where('transaction_type', 'INBOUND')
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->get();

        return view('transaction.inbound.create2.inbCreate', compact(
            'title',
            'pageTitle',
            'inbound',
            'mode',
            'isCreate',
            'isEdit',
            'isReceive',
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
            'inb_supplier' => 'required|string|max:255',
            'inb_stat' => 'required|in:DRAFT,RECEIVED,PARTIAL',
            'inb_rcv' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required',
            'details.*.location_id' => 'required',
            'details.*.qty_order' => 'required|numeric|min:0',
            'details.*.qty_receive' => 'required|numeric|min:0',
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
            $inbound = InboundMaster::findOrFail($id);

            if ($inbound->inb_stat !== 'DRAFT') {
                return response()->json([
                    'success' => false,
                    'message' => 'Inbound yang sudah diproses approval tidak bisa diedit.',
                ], 422);
            }

            $inbound->update([
                'inb_supplier' => $request->inb_supplier,
                'inb_rcv' => $request->inb_rcv,
                'inb_upd_by' => $user,
            ]);

            InboundDet::where('ref_inb_id', $inbound->inb_id)->delete();

            $message = 'Inbound draft berhasil diupdate.';
        } else {
            $inbCode = DocumentNumberService::generate('INB');

            $inbound = InboundMaster::create([
                'inb_code'     => $inbCode,
                'ref_wh_id'    => $request->wh,
                'inb_supplier' => $request->inb_supplier,
                'inb_stat'     => 'DRAFT',
                'inb_rcv'      => $request->inb_rcv,
                'inb_add_by'   => $user,
            ]);

            $message = 'Inbound berhasil disimpan dengan kode ' . $inbCode;
        }

            foreach ($request->details as $detail) {
                InboundDet::create([
                    'ref_inb_id'  => $inbound->inb_id,
                    'ref_prd_id'  => $detail['product_id'],
                    'ref_loc_id'  => $detail['location_id'],
                    'qty_order'   => $detail['qty_order'],
                    'qty_rcv'     => 0,
                    'inbd_add_by' => $user,
                ]);
            }

            if(!$isEdit) {
                $this->approvalService->createInboundApproval(
                    $inbound,
                    $request->approval_route_id
                );
            }
            
            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => $message,
                'redirect' => route('transaction.inbIndex'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            Log::error($th);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan inbound: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function getEditData($id)
    {
        $inbound = InboundMaster::with([
            'inbounddets.product',
            'inbounddets.location'
        ])->findOrFail($id);
    
        return response()->json($inbound);
    }

    public function receiveUpdate(Request $request, $id)
    {
        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.id' => 'required|exists:inbd_det,inbd_id',
            'details.*.qty_receive' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {
            $inbound = InboundMaster::with('inbounddets')->findOrFail($id);
            if (!in_array($inbound->inb_stat, ['ISSUED', 'PARTIAL'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'inbound' => 'Inbound belum bisa direceive.'
                ]);
            }

            foreach ($request->details as $row) {
                $detail = InboundDet::where('ref_inb_id', $inbound->inb_id)
                ->where('inbd_id', $row['id'])
                ->firstOrFail();
                
                $receiveQty = (float) $row['qty_receive'];
                $outstanding = $detail->qty_order - $detail->qty_rcv;

                if ($receiveQty > $outstanding) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'qty_receive' => 'Qty receive tidak boleh melebihi outstanding.'
                    ]);
                }

                if ($receiveQty <= 0) {
                    continue;
                }

                $detail->update([
                    'qty_rcv' => $detail->qty_rcv + $receiveQty,
                    'inbd_upd_by' => Auth::user()?->name,
                ]);

                $this->stockService->increaseStock(
                    $detail->ref_prd_id,
                    $inbound->ref_wh_id,
                    $detail->ref_loc_id,
                    $receiveQty
                );
            }

            $inbound->refresh();

            $totalOrder = $inbound->inbounddets()->sum('qty_order');
            $totalReceive = $inbound->inbounddets()->sum('qty_rcv');

            $status = 'DRAFT';

            if ($totalReceive >= $totalOrder) {
                $status = 'RECEIVED';
            } elseif ($totalReceive > 0) {
                $status = 'PARTIAL';
            }

            $inbound->update([
                'inb_stat' => $status,
                'inb_upd_by' => Auth::user()?->name,
            ]);

        });

        return response()->json([
            'success' => true,
            'message' => 'Inbound berhasil direceive',
            'redirect' => route('transaction.inbIndex'),
        ]);
    }

}
