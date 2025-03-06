<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timesheet extends Model
{
    /** @use HasFactory<\Database\Factories\TimesheetFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_name',
        'date',
        'hours',
        'project_id',
        'user_id',
    ];
    /**
     * The user that belong to the timesheet.
     */
    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * The project that belong to the timesheet.
     */
    public function project() : BelongsTo {
        return $this->belongsTo(Project::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(isset($filters['task_name']), function ($q) use ($filters) {
            $q->where('task_name', 'LIKE', "%{$filters['task_name']}%");
        })
        ->when(isset($filters['status']), function ($q) use ($filters) {
            $q->where('status', $filters['status']);
        })
        ->when(isset($filters['date']), function ($q) use ($filters) {
            $q->whereDate('date', $filters['date']);
        })
        ->when(isset($filters['hours']), function ($q) use ($filters) {
            $q->where('hours', $filters['hours']);
        })
        ->when(isset($filters['project_id']), function ($q) use ($filters) {
            $q->where('project_id', $filters['project_id']);
        });
    }
    
        
}
