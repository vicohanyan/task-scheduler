<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'priority',
        'assigned_user_id',
        'due_date',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
        'due_date' => 'datetime',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function statusInfo(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'status', 'code');
    }
}
