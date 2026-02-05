<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var array
     */
    protected $fillable = ['value'];

    /**
     * The attributes that should be cast
     * @var array
     */
    protected $casts = [
        'value' => 'integer',
    ];
}
