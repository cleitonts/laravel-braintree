<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Transactions extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public static function getListByActiveUser()
    {
        $user = Auth::user()->id;
        return DB::table('transactions')
            ->leftJoin('customers', 'customers.id', '=', 'transactions.customer_id')
            ->select('transactions.*')
            ->where('customers.user_id', '=', $user)
            ->get();
    }
}
