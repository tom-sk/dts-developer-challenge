<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected function casts()
    {
        return [
            'status' => 'boolean',
            'due' => 'datetime',
        ];
    }
}
