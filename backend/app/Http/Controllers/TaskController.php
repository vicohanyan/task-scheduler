<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Jobs\SendTaskCreatedNotification;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{

    /**
     * Get tasks with filters
     * GET /tasks
     * @param TaskIndexRequest $request
     * @return JsonResponse
     */
    public function index(TaskIndexRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $taskQuery = Task::query()
            ->with([
                'assignee:id,name',
                'statusInfo:code,display_name',
            ]);

        if (!empty($validatedData['search'])) {
            $searchTerm = trim($validatedData['search']);
            $taskQuery->where(function ($searchSubquery) use ($searchTerm) {
                $searchSubquery
                    ->where('name', 'like', $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        if (!empty($validatedData['status'])) {
            $taskQuery->where('status', $validatedData['status']);
        }
        if (array_key_exists('assigned_user_id', $validatedData) && !empty($validatedData['assigned_user_id'])) {
            $uid = (int)$validatedData['assigned_user_id'];
            if ($uid === -1) {
                // "Unassigned"
                $taskQuery->whereNull('assigned_user_id');
            } else {
                $taskQuery->where('assigned_user_id', $uid);
            }
        }
        return response()->json($taskQuery->get());
    }


    /**
     *  Get tasks with filters for kanban board
     *  GET /kanban
     * @param TaskIndexRequest $request
     * @return JsonResponse
     */
    public function kanban(TaskIndexRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $params = [];
        $where = [];

        if (!empty($validatedData['search'])) {
            $search = trim($validatedData['search']);
            $params['searchName'] = $search . '%';
            $params['searchDesc'] = '%' . $search . '%';
            $where[] = '(tasks.name LIKE :searchName OR tasks.description LIKE :searchDesc)';
        }
        if (!empty($validatedData['assigned_user_id'])) {
            $uid = (int)$validatedData['assigned_user_id'];
            if ($uid === -1) {
                // Unassigned
                $where[] = 'tasks.assigned_user_id IS NULL';
            } else {
                $params['assigned_user_id'] = $uid;
                $where[] = 'tasks.assigned_user_id = :assigned_user_id';
            }
        }

        $whereSql = $where ? ' AND ' . implode(' AND ', $where) : '';
        $sql = <<<SQL
            SELECT JSON_OBJECT(
                               'code', task_statuses.code,
                               'display_name', task_statuses.display_name,
                               'tasks', COALESCE(tasks, JSON_ARRAY())
                       ) AS payload
                FROM task_statuses
                         LEFT JOIN (SELECT tasks.status,
                                           JSON_ARRAYAGG(
                                                   JSON_OBJECT(
                                                           'id', tasks.id,
                                                           'name', tasks.name,
                                                           'description', tasks.description,
                                                           'status', tasks.status,
                                                           'priority', tasks.priority,
                                                           'due_date', tasks.due_date,
                                                           'assigned_user_id', tasks.assigned_user_id,
                                                           'assignee',
                                                           CASE
                                                               WHEN users.id IS NULL THEN NULL
                                                               ELSE JSON_OBJECT('id', users.id, 'name', users.name)
                                                           END
                                                   )
                                           ) AS tasks
                                    FROM tasks
                                        LEFT JOIN users ON users.id = tasks.assigned_user_id
                                    WHERE TRUE {$whereSql}
                                    GROUP BY tasks.status)
                         AS tasks ON tasks.status = task_statuses.code
                ORDER BY task_statuses.priority;
        SQL;
        $rows = DB::select($sql, $params);
        $jsonData = json_decode('[' . implode(',', array_column($rows, 'payload')) . ']');
        return response()->json($jsonData);
    }

    /**
     * POST /tasks
     * @param TaskStoreRequest $request
     * @return JsonResponse
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        SendTaskCreatedNotification::dispatch(
            $task->name,
            $task->assignee?->name,
            $task->statusInfo?->display_name ?? $task->status->value,
        );
        $task->refresh()->load(['assignee:id,name', 'statusInfo:code,display_name']);
        return response()->json($task, 201);
    }


    /**
     * GET /tasks/{task}
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        $task->load(['assignee:id,name', 'statusInfo:code,display_name']);
        return response()->json($task);
    }


    /**
     * PUT|PATCH /tasks/{task}
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $oldAssigneeId = $task->assigned_user_id;
        $task->update($request->validated());
        if ($oldAssigneeId !== $task->assigned_user_id && $task->assigned_user_id) {
            $task->load(['assignee:id,name', 'statusInfo:code,display_name']);
            SendTaskCreatedNotification::dispatch(
                $task->name,
                $task->assignee?->name,
                $task->statusInfo?->display_name ?? $task->status->value,
            );
        }

        $task->refresh()->load(['assignee:id,name', 'statusInfo:code,display_name']);
        return response()->json($task);
    }

    /**
     * Delete /tasks/{task}
     * @param Task $task
     * @return Response
     */
    public function destroy(Task $task): Response
    {
        $task->delete();
        return response()->noContent();
    }
}
