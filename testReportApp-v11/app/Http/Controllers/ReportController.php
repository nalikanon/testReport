<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceLog;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Validate incoming request parameters
        $request->validate([
            'per_page' => 'nullable|integer|in:10,20,50,100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $perPage = $request->input('per_page', 10);
        
        // 2. Build the query using Eloquent ORM and Scopes
        $logs = MaintenanceLog::with(['machine', 'technician'])
            ->search($request->input('keyword'))
            ->status($request->input('status'))
            ->maintenanceType($request->input('maintenance_type'))
            ->dateRange($request->input('start_date'), $request->input('end_date'))
            ->orderBy('request_date', 'desc')
            ->paginate($perPage)
            ->appends($request->query()); // Keep filter parameters in pagination links

        // 3. Fetch unique filter options for the UI
        $statuses = MaintenanceLog::select('status')->distinct()->whereNotNull('status')->pluck('status');
        $maintenanceTypes = MaintenanceLog::select('maintenance_type')->distinct()->whereNotNull('maintenance_type')->pluck('maintenance_type');

        return view('reports.index', compact('logs', 'statuses', 'maintenanceTypes'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:In Progress,Completed'
        ]);

        $log = MaintenanceLog::findOrFail($id);
        $log->status = $request->status;
        $log->end_date = now(); // Set end_date to current time
        $log->save();

        return redirect()->back()->with('success', 'อัปเดตสถานะเป็น ' . $request->status . ' สำเร็จ (เวลา End Date อัปเดตเป็นปัจจุบัน)');
    }
}
