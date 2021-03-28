<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'code'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public static function getListByActiveUser()
    {
        $user = Auth::user()->id;
        return DB::table('subscriptions')
            ->leftJoin('customers', 'customers.id', '=', 'subscriptions.customer_id')
            ->select('subscriptions.*')
            ->where('customers.user_id', '=', $user)
            ->whereNull('subscriptions.deleted_at')
            ->get();
    }
}
