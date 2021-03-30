<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Answers extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question',
        'user_id',
        'answer',
    ];

    public static $question_arr = [
        'php' => 'required|integer',
        'laravel' => 'required|integer',
        'dev_tech' => 'required|integer',
        'sql' => 'required|integer',
        'code' => 'required|integer',
        'docker' => 'required|integer',
        'azure' => 'required|integer',
        'new_things' => 'required|integer',
        'vue' => 'required|integer',
        'frontend' => 'required|integer',
        'english' => 'required|integer',
        'considerations' => 'required',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}

