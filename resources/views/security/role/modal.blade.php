<style>
    /* =========================
       PREMIUM ROLE MODAL
    ========================== */

    .premium-role-modal .modal-dialog {
        max-width: 90%;
    }

    .premium-role-modal .modal-content {
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(15, 23, 42, .22);
    }

    .premium-role-modal .modal-header {
        padding: 24px 30px;
        background: linear-gradient(135deg, #f8faff, #eef4ff);
        border-bottom: 1px solid #e5ebf3;
    }

    .premium-role-modal .modal-title {
        font-size: 24px;
        font-weight: 800;
        color: #243b64;
    }

    .premium-role-modal .modal-body {
        padding: 24px 30px;
        background: #fbfcff;
        max-height: 72vh;
        overflow-y: auto;
    }

    .premium-role-modal .modal-footer {
        padding: 18px 30px;
        border-top: 1px solid #edf1f7;
        background: #fff;
    }

    /* =========================
       INPUT
    ========================== */

    .role-name-input {
        height: 48px;
        border-radius: 12px;
        border: 1px solid #d8e0ec;
        font-weight: 600;
        padding: 0 16px;
    }

    .role-name-input:focus {
        border-color: #5b7cfa;
        box-shadow: 0 0 0 4px rgba(91, 124, 250, .12);
    }

    /* =========================
       TABLE
    ========================== */

    .permission-table-wrapper {
        border: 1px solid #e5ebf3;
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
    }

    .permission-table {
        margin-bottom: 0;
    }

    .permission-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f5f7fb;
        color: #536381;
        font-size: 14px;
        font-weight: 800;
        padding: 16px;
        border-bottom: 1px solid #e5ebf3;
    }

    .permission-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: #edf1f7;
    }

    /* =========================
       GROUP HEADER
    ========================== */

    .permission-group-row td {
        background: linear-gradient(135deg, #e8edf5, #f4f7fb);
        font-size: 16px;
        font-weight: 800;
        color: #1f2937;
        padding: 14px 16px !important;
    }

    /* =========================
       MENU CELL
    ========================== */

    .menu-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .menu-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        font-size: 15px;
    }

    .menu-title {
        font-size: 15px;
        font-weight: 700;
        color: #60708f;
        line-height: 1.2;
    }

    .menu-code {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 2px;
    }

    /* =========================
       PREMIUM CHECKBOX
    ========================== */

    .permission-check {
        appearance: none;
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 8px;
        border: 2px solid #cbd5e1;
        background: #fff;
        cursor: pointer;
        transition: all .2s ease;
        position: relative;
    }

    .permission-check:hover {
        border-color: #22c55e;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, .10);
    }

    .permission-check:checked {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: #16a34a;
        box-shadow: 0 8px 18px rgba(34, 197, 94, .22);
    }

    .permission-check:checked::after {
        content: "\f00c";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: #fff;
        font-size: 11px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* =========================
       BUTTONS
    ========================== */

    .btn-premium-save {
        border-radius: 12px;
        padding: 10px 22px;
        font-weight: 700;
    }

    .btn-premium-cancel {
        border-radius: 12px;
        padding: 10px 22px;
        font-weight: 700;
    }

    /* =========================
       SCROLLBAR
    ========================== */

    .premium-role-modal .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .premium-role-modal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }
</style>

<div class="modal fade premium-role-modal" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form id="role-form" class="w-100">
            @csrf
            <input type="hidden" id="role_id">

            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <div class="text-primary fw-bold text-uppercase mb-1"
                             style="font-size:11px;letter-spacing:.08em;">
                            Security Management
                        </div>

                        <h5 class="modal-title mb-1">
                            Role Permission
                        </h5>

                        <small class="text-muted">
                            Manage menu access and action permissions.
                        </small>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">
                            Role Name
                        </label>

                        <input type="text"
                               id="role_name"
                               class="form-control role-name-input"
                               placeholder="Enter role name"
                               required>
                    </div>

                    <div class="permission-table-wrapper">

                        <table class="table permission-table align-middle">

                    <thead>
                        <tr>
                            <th style="width:38%;">
                                Menu
                            </th>
                        
                            <th class="text-center">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <span>View</span>
                                
                                    <input
                                        type="checkbox"
                                        class="permission-check select-column"
                                        data-action="view"
                                    >
                                </div>
                            </th>
                        
                            <th class="text-center">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <span>Create</span>
                                
                                    <input
                                        type="checkbox"
                                        class="permission-check select-column"
                                        data-action="create"
                                    >
                                </div>
                            </th>
                        
                            <th class="text-center">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <span>Edit</span>
                                
                                    <input
                                        type="checkbox"
                                        class="permission-check select-column"
                                        data-action="edit"
                                    >
                                </div>
                            </th>
                        
                            <th class="text-center">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <span>Delete</span>
                                
                                    <input
                                        type="checkbox"
                                        class="permission-check select-column"
                                        data-action="delete"
                                    >
                                </div>
                            </th>
                        </tr>
                    </thead>

                            <tbody>

                                @foreach($menus as $groupName => $groupMenus)

                                    <tr class="permission-group-row">
                                        <td colspan="5">
                                            {{ $groupName }}
                                        </td>
                                    </tr>

                                    @foreach($groupMenus as $menu)

                                        <tr>

                                            <td>
                                                <div class="menu-cell">

                                                    <div class="menu-icon-box">
                                                        <i class="{{ $menu->menu_icon }}"></i>
                                                    </div>

                                                    <div>
                                                        <div class="menu-title">
                                                            {{ $menu->menu_name }}
                                                        </div>

                                                        <div class="menu-code">
                                                            {{ $menu->menu_code }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>

                                            @foreach(['view', 'create', 'edit', 'delete'] as $action)

                                                <td class="text-center">

                                                    <input
                                                        type="checkbox"
                                                        class="permission-check permission-checkbox"
                                                        data-action="{{ $action }}"
                                                        value="{{ $menu->menu_code . '_' . $action }}"
                                                    >

                                                </td>

                                            @endforeach

                                        </tr>

                                    @endforeach

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-light btn-premium-cancel"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary btn-premium-save">
                        <i class="fas fa-save me-2"></i>
                        Save Role
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>