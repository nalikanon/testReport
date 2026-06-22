@extends('layouts.app') <!-- Assuming you have a basic layout, otherwise we can wrap this in full HTML -->

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Maintenance Reports</h2>
            <p class="text-muted">Overview of all machine maintenance logs.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row">
                <!-- Keyword Search -->
                <div class="col-md-3 mb-3">
                    <label for="keyword" class="form-label">Search</label>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Machine, Issue, Tech Name..." value="{{ request('keyword') }}">
                </div>

                <!-- Status Filter -->
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Maintenance Type Filter -->
                <div class="col-md-2 mb-3">
                    <label for="maintenance_type" class="form-label">Type</label>
                    <select name="maintenance_type" id="maintenance_type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($maintenanceTypes as $type)
                            <option value="{{ $type }}" {{ request('maintenance_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="col-md-2 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary w-100 ml-2">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Results ({{ $logs->total() }})</h5>
                
                <!-- Per Page Selector -->
                <form action="{{ route('reports.index') }}" method="GET" class="form-inline">
                    <!-- Preserve existing filters -->
                    @foreach(request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page" class="mr-2">Show:</label>
                    <select name="per_page" id="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Machine</th>
                            <th>Issue Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Technician</th>
                            <th>Cost (Spare/Labor)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->repair_id }}</td>
                                <td>
                                    <strong>{{ $log->machine->machine_name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $log->machine_id }} | {{ $log->machine->department ?? '' }}</small>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($log->issue_description, 50) }}</td>
                                <td>
                                    <span class="badge badge-{{ $log->maintenance_type == 'Preventive' ? 'info' : 'warning' }}">
                                        {{ $log->maintenance_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $log->status == 'Completed' ? 'success' : ($log->status == 'In Progress' ? 'primary' : 'secondary') }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($log->request_date)->format('d M Y H:i') }}</td>
                                <td>{{ $log->technician->tech_name ?? 'N/A' }}</td>
                                <td>
                                    Spare: ฿{{ number_format($log->spare_parts_cost, 2) }}<br>
                                    Labor: ฿{{ number_format($log->labor_cost, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No records found matching your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
