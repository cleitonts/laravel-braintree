<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'user_id',
        'gateway_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function gateway()
    {
        return $this->belongsTo('App\Models\Gateway');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transactions');
    }

    public function subscription()
    {
        return $this->hasMany('App\Models\Subscription');
    }
}
