<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. High Performance Caching for stats
        // We catch "total logs" for 10 minutes to avoid COUNT(*) on huge table every refresh
        $totalLogs = Cache::remember('audit_logs_count', 600, function () {
            return AuditLog::count();
        });

        // 2. Optimized Query with Eager Loading and Indexing
        $query = AuditLog::with('user');

        // Filters
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('model') && $request->model) {
            $query->where('auditable_type', 'like', '%' . $request->model . '%');
        }

        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        // Date Range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 3. Paginate (Limit 20 per page for speed)
        $logs = $query->latest()->paginate(20);

        return view('admin.audit_logs.index', compact('logs', 'totalLogs'));
    }
}
