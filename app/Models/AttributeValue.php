<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AttributeValue extends Model
{
    /** @use HasFactory<\Database\Factories\AtributeValueFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'attribute_id',
        'entity_id',
        'value',
    ];

    public function attribute() : BelongsTo {
        return $this->belongsTo(Attribute::class);
    }

    public function project() : BelongsTo {
        return $this->belongsTo(Project::class,'entitiy_id');
    }
}
