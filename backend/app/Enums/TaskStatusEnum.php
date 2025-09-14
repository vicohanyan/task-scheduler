<?php
namespace App\Enums;

/**
 * TaskStatusEnum defines the fixed set of internal task states.
 *
 * Using a strict enum guarantees type safety and prevents
 * invalid or unexpected status values in code. This makes
 * the backend logic more reliable and less error-prone,
 * while user-facing labels are provided by TaskStatus model.
 */
enum TaskStatusEnum: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case BLOCKED = 'blocked';
}
