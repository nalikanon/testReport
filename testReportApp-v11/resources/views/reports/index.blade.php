<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Reports</title>
    <!-- Use Bootstrap 5 CDN for easy styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Maintenance Reports</h2>
            <p class="text-muted">Overview of all machine maintenance logs.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row align-items-end">
                <!-- Keyword Search -->
                <div class="col-md-2 mb-3">
                    <label for="keyword" class="form-label">Search</label>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Machine, Issue, Tech Name..." value="{{ request('keyword') }}">
                </div>

                <!-- Status Filter -->
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Maintenance Type Filter -->
                <div class="col-md-2 mb-3">
                    <label for="maintenance_type" class="form-label">Type</label>
                    <select name="maintenance_type" id="maintenance_type" class="form-select">
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
                <div class="col-md-1 mb-3 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2">Filter</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Results ({{ $logs->total() }})</h5>
                
                <!-- Per Page Selector -->
                <form action="{{ route('reports.index') }}" method="GET" class="d-inline-flex align-items-center">
                    <!-- Preserve existing filters -->
                    @foreach(request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    
                    <label for="per_page" class="me-2 mb-0 text-nowrap">Show:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 80px;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Machine</th>
                            <th>Issue Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Technician</th>
                            <th>Cost (Spare/Labor)</th>
                            <th>Action</th>
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
                                    @php
                                        $typeBadge = $log->maintenance_type == 'Preventive' ? 'bg-info text-dark' : 'bg-warning text-dark';
                                    @endphp
                                    <span class="badge {{ $typeBadge }}">
                                        {{ $log->maintenance_type }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusBadge = $log->status == 'Completed' ? 'bg-success' : ($log->status == 'In Progress' ? 'bg-primary' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($log->request_date)->format('d M Y H:i') }}</td>
                                <td>{{ $log->technician->tech_name ?? 'N/A' }}</td>
                                <td>
                                    Spare: ฿{{ number_format($log->spare_parts_cost, 2) }}<br>
                                    Labor: ฿{{ number_format($log->labor_cost, 2) }}
                                </td>
                                <td>
                                    <form action="{{ route('reports.updateStatus', $log->repair_id) }}" method="POST" class="d-flex align-items-center" style="gap: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                            <option value="In Progress" {{ $log->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Completed" {{ $log->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                    </form>
                                    @if($log->end_date)
                                        <small class="text-muted mt-1 d-block">Ended: {{ \Carbon\Carbon::parse($log->end_date)->format('d M H:i') }}</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No records found matching your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
