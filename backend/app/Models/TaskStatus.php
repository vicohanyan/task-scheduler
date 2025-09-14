<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TaskStatus stores user-facing labels for task states.
 *
 * Codes are fixed in TaskStatusEnum, ensuring type safety in code,
 * while display names can be customized or localized for users.
 * This makes the system less dynamic but more reliable and less
 * error-prone.
 */
class TaskStatus extends Model
{
    public $timestamps = false;
    protected $fillable = ['code','display_name'];
}
