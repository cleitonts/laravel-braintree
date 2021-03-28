<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Gateway extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'environment',
        'merchant_id',
        'public_key',
        'private_key',
        'uri'
    ];

    public function customer()
    {
        return $this->hasMany('App\Models\Customer');
    }
}
