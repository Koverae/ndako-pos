<?php

namespace Modules\Pos\Models\Order;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\ChannelManager\Models\Guest\Guest;
use Modules\Pos\Models\Pos\PosSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Pos\Models\Pos\Pos;

class PosOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pos) {
            $pos->unique_token = Str::uuid();
        });

        static::creating(function ($model) {
            $number = PosOrder::isCompany(current_company()->id)->max('id') + 1;
            $model->reference = make_reference_id($model->pos->name, $number);
        });
    }

    public function scopeIsCompany(Builder $query, $company_id)
    {
        return $query->where('company_id', $company_id);
    }

    public function scopeFindByToken(Builder $query, $unique_token)
    {
        return $query->where('unique_token', $unique_token);
    }

    public function scopeIsPos(Builder $query, $pos_id)
    {
        return $query->where('pos_id', $pos_id);
    }

    public function scopeIsSession(Builder $query, $pos_session_id)
    {
        return $query->where('pos_session_id', $pos_session_id);
    }

    // Get Pos
    public function pos() {
        return $this->belongsTo(Pos::class, 'pos_id', 'id');
    }
    // Session
    public function session() {
        return $this->belongsTo(PosSession::class, 'pos_session_id', 'id');
    }

    // Get Customer
    public function customer() {
        return $this->belongsTo(Guest::class, 'customer_id', 'id');
    }

    public function details() {
        return $this->hasMany(PosOrderDetail::class, 'pos_order_id', 'id');
    }

    // Get Pos
    public function cashier() {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
