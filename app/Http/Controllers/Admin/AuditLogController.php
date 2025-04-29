<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminAuditLog;


class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AdminAuditLog::query()
            ->when($request->has('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($logs);
    }

    public function show($id)
    {
        $log = AdminAuditLog::findOrFail($id);
        return response()->json($log);
    }

    public function clear()
    {
        AdminAuditLog::where('created_at', '<', now()->subMonths(6))->delete();
        return response()->json(['message' => 'Old logs cleared successfully.']);
    }
}
