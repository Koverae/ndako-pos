<?php

namespace Modules\Pos\Models\Pos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Modules\Pos\Models\Order\PosOrder;

class PosSession extends Model
{
    use HasFactory;


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $number = PosSession::isCompany(current_company()->id)->max('id') + 1;
            $model->reference = make_reference_id('POS', $number);
        });

        static::creating(function ($pos) {
            $pos->unique_token  = Str::uuid();
        });
    }

    public function scopeIsCompany(Builder $query, $company_id)
    {
        return $query->where('company_id', $company_id);
    }

    public function scopeIsPos(Builder $query, $pos_id)
    {
        return $query->where('pos_id', $pos_id);
    }

    public function scopeIsOpened(Builder $query)
    {
        return $query->where('status', 'active');
    }

    // Get Pos
    public function pos() {
        return $this->belongsTo(Pos::class, 'pos_id', 'id');
    }

    public function orders() {
        return $this->hasMany(PosOrder::class, 'pos_session_id', 'id');
    }

}
