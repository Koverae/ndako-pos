<?php

namespace Modules\Pos\Models\Floor;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Pos\Models\Pos\Pos;

// use Modules\Pos\Database\Factories\Floor/TableFactory;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    public function scopeIsPos(Builder $query, $pos_id)
    {
        return $query->where('pos_id', $pos_id);
    }

    public function scopeIsPlan(Builder $query, $floor_plan_id)
    {
        return $query->where('floor_plan_id', $floor_plan_id);
    }

    // Get Pos
    public function pos() {
        return $this->belongsTo(Pos::class, 'pos_id', 'id');
    }


    // Get Pos
    public function floor() {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id', 'id');
    }
}
