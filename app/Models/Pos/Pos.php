<?php

namespace Modules\Pos\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Pos extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pos) {
            $pos->private_key = Str::uuid();
        });
    }

    public function scopeIsCompany(Builder $query, $company_id)
    {
        return $query->where('company_id', $company_id);
    }

    public function sessions() {
        return $this->hasMany(PosSession::class, 'pos_id', 'id');
    }

    public function activeSession(){
        return $this->hasOne(PosSession::class, 'id', 'active_session_id');
    }

    public function setting() {
        return $this->hasOne(PosSetting::class, 'pos_id', 'id');
    }

}
