<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $primaryKey = 'repair_id';
    public $timestamps = false;
    
    protected $fillable = [
        'machine_id', 'request_date', 'start_date', 'end_date',
        'issue_description', 'maintenance_type', 'status',
        'spare_parts_cost', 'labor_cost', 'tech_id'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'machine_id', 'machine_id');
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class, 'tech_id', 'tech_id');
    }

    // Query Scopes for Clean Controller
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('issue_description', 'like', "%{$keyword}%")
                  ->orWhereHas('machine', function($q) use ($keyword) {
                      $q->where('machine_name', 'like', "%{$keyword}%")
                        ->orWhere('machine_id', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('technician', function($q) use ($keyword) {
                      $q->where('tech_name', 'like', "%{$keyword}%");
                  });
            });
        }
        return $query;
    }

    public function scopeStatus($query, $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeMaintenanceType($query, $type)
    {
        if ($type) {
            $query->where('maintenance_type', $type);
        }
        return $query;
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $query->whereBetween('request_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->where('request_date', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('request_date', '<=', $endDate . ' 23:59:59');
        }
        return $query;
    }
}
