<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'status'
    ];

    /**
     * Timesheets that belong to the project.
     */
    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    /**
     * The users that belong to the project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The attributes that belong to the project.
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'entity_id');
    }

    /**
     * The attributes values that belong to the project.
     */
    public function attribute_values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'entity_id');
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->where(function ($query) use ($filters) {
            foreach ($filters as $key => $value) {
                if (empty($value)) continue; // Skip empty values
    
                if (in_array($key, ['name', 'status'])) {
                    // Direct fields filtering
                    if (is_array($value)) {
                        $query->whereIn($key, $value);
                    } else {
                        $query->where($key, 'LIKE', "%$value%");
                    }
                    continue;
                }
    
                // EAV attribute filtering
                $query->whereHas('attributes', function ($attr) use ($key, $value) {
                    $attr->where('name', $key)->whereHas('value', function ($val) use ($value) {
                        if (is_array($value)) {
                            // If an array, filter by multiple values
                            $val->whereIn('value', $value);
                        } elseif (is_string($value) && strtotime($value) !== false) {
                            // If it's a date
                            $val->whereDate('value', $value);
                        } else {
                            $val->where('value', 'LIKE', "%$value%");
                        }
                    });
                });
            }
        });
    }
    
}
