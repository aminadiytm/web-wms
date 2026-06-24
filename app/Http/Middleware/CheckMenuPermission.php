<?php

namespace App\Http\Middleware;

use App\Models\MenuList;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $routeName = $request->route()?->getName();

        if (!$user || !$routeName) {
            abort(403, 'Unauthorized.');
        }

        $indexRoute = $this->resolveIndexRoute($routeName);

        $menu = MenuList::where('menu_route', $indexRoute)
            ->where('is_active', true)
            ->first();

        if (!$menu) {
            abort(403, 'Menu permission not configured.');
        }

        $suffix = $this->resolveActionSuffix($routeName);

        if (!$suffix) {
            abort(403, 'Action permission not configured.');
        }

        $permission = $menu->menu_code . $suffix;

        if (!$user->can($permission)) {
            abort(403, "You don't have permission to this action.");
        }

        return $next($request);
    }

    private function resolveIndexRoute(string $routeName): string
    {
        $map = [
            // CATEGORY
            'masterdata.catList' => 'masterdata.catIndex',
            'masterdata.catCreate' => 'masterdata.catIndex',
            'masterdata.catStore' => 'masterdata.catIndex',
            'masterdata.catEdit' => 'masterdata.catIndex',
            'masterdata.catUpdate' => 'masterdata.catIndex',
            'masterdata.catDelete' => 'masterdata.catIndex',

            // PRODUCT
            'masterdata.prdList' => 'masterdata.prdIndex',
            'masterdata.prdCreate' => 'masterdata.prdIndex',
            'masterdata.prdStore' => 'masterdata.prdIndex',
            'masterdata.prdEdit' => 'masterdata.prdIndex',
            'masterdata.prdUpdate' => 'masterdata.prdIndex',
            'masterdata.prdDelete' => 'masterdata.prdIndex',

            // WAREHOUSE
            'masterdata.whList' => 'masterdata.whIndex',
            'masterdata.whCreate' => 'masterdata.whIndex',
            'masterdata.whStore' => 'masterdata.whIndex',
            'masterdata.whEdit' => 'masterdata.whIndex',
            'masterdata.whUpdate' => 'masterdata.whIndex',
            'masterdata.whDelete' => 'masterdata.whIndex',

            // LOCATION
            'masterdata.locList' => 'masterdata.locIndex',
            'masterdata.locCreate' => 'masterdata.locIndex',
            'masterdata.locStore' => 'masterdata.locIndex',
            'masterdata.locEdit' => 'masterdata.locIndex',
            'masterdata.locUpdate' => 'masterdata.locIndex',
            'masterdata.locDelete' => 'masterdata.locIndex',

            // APPROVAL ROUTE
            'masterdata.approvalRouteList' => 'masterdata.approvalRouteIndex',
            'masterdata.approvalRouteStore' => 'masterdata.approvalRouteIndex',
            'masterdata.approvalRouteEdit' => 'masterdata.approvalRouteIndex',
            'masterdata.approvalRouteUpdate' => 'masterdata.approvalRouteIndex',
            'masterdata.approvalRouteDelete' => 'masterdata.approvalRouteIndex',

            // INBOUND
            'transaction.inbList' => 'transaction.inbIndex',
            'transaction.inbdetList' => 'transaction.inbIndex',
            'transaction.inbCreate' => 'transaction.inbIndex',
            'transaction.inbStore' => 'transaction.inbIndex',
            'transaction.inbEdit' => 'transaction.inbIndex',
            'transaction.inbUpdate' => 'transaction.inbIndex',
            'transaction.inbReceive' => 'transaction.inbIndex',
            'transaction.inbReceiveUpdate' => 'transaction.inbIndex',
            'transaction.inbData' => 'transaction.inbIndex',

            // OUTBOUND
            'transaction.outbList' => 'transaction.outbIndex',
            'transaction.outbdetList' => 'transaction.outbIndex',
            'transaction.outbCreate' => 'transaction.outbIndex',
            'transaction.outbStore' => 'transaction.outbIndex',
            'transaction.outbEdit' => 'transaction.outbIndex',
            'transaction.outbUpdate' => 'transaction.outbIndex',
            'transaction.outbConfirm' => 'transaction.outbIndex',
            'transaction.outbConfirmUpdate' => 'transaction.outbIndex',
            'transaction.outbData' => 'transaction.outbIndex',

            // STOCK
            'inventory.stockList' => 'inventory.stockIndex',
            'inventory.stockAvailable' => 'inventory.stockIndex',

            // ROLE
            'security.roleList' => 'security.roleIndex',
            'security.roleStore' => 'security.roleIndex',
            'security.roleEdit' => 'security.roleIndex',
            'security.roleUpdate' => 'security.roleIndex',
            'security.roleDelete' => 'security.roleIndex',

            // ADMIN USERS
            'admin.users.usrList' => 'admin.users.usrIndex',
            'admin.users.usrStore' => 'admin.users.usrIndex',
            'admin.users.usrEdit' => 'admin.users.usrIndex',
            'admin.users.usrUpdate' => 'admin.users.usrIndex',
            'admin.users.usrDelete' => 'admin.users.usrIndex',
        ];

        return $map[$routeName] ?? $routeName;
    }

    private function resolveActionSuffix(string $routeName): ?string
    {
        if (
            str_ends_with($routeName, 'Index') ||
            str_ends_with($routeName, 'List') ||
            str_ends_with($routeName, 'Data') ||
            str_ends_with($routeName, 'Available')
        ) {
            return '_view';
        }

        if (
            str_ends_with($routeName, 'Create') ||
            str_ends_with($routeName, 'Store')
        ) {
            return '_create';
        }

        if (
            str_ends_with($routeName, 'Edit') ||
            str_ends_with($routeName, 'Update') ||
            str_ends_with($routeName, 'Receive') ||
            str_ends_with($routeName, 'ReceiveUpdate') ||
            str_ends_with($routeName, 'Confirm') ||
            str_ends_with($routeName, 'ConfirmUpdate')
        ) {
            return '_edit';
        }

        if (
            str_ends_with($routeName, 'Delete') ||
            str_ends_with($routeName, 'Destroy')
        ) {
            return '_delete';
        }

        return null;

    }
}
