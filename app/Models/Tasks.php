<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'assign_to',
        'status',
        'created_by',
    ];

     // User who is assigned the task
    public function assign_to()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    // User who created the task
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
