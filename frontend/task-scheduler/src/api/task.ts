import {http} from './http';
import {
    type Task,
    type TaskStatus,
    type CreateTaskPayload,
    TASKS_TYPE_LIST,
    TASKS_TYPE_KANBAN,
    type KanbanColumn,
} from './types';


export function fetchTasks(
    type: typeof TASKS_TYPE_LIST,
    search?: string,
    assignedUserId?: number,
    status?: TaskStatus
): Promise<Task[]>;

export function fetchTasks(
    type: typeof TASKS_TYPE_KANBAN,
    search?: string,
    assignedUserId?: number,
    status?: TaskStatus
): Promise<KanbanColumn[]>;

export async function fetchTasks(
    type: typeof TASKS_TYPE_LIST | typeof TASKS_TYPE_KANBAN,
    search?: string,
    assignedUserId?: number,
    status?: TaskStatus
): Promise<Task[] | KanbanColumn[]> {
    const params: Record<string, unknown> = {};
    if (search !== undefined) params.search = search;
    if (assignedUserId !== undefined) params.assigned_user_id = assignedUserId;
    if (status !== undefined) params.status = status;
    return type === TASKS_TYPE_LIST ? fetchTasksList(params) : fetchTasksKanban(params);
}

async function fetchTasksList(params: Record<string, unknown>): Promise<Task[]> {
    const {data} = await http.get<Task[]>('/tasks', {params});
    return data;
}

async function fetchTasksKanban(params: Record<string, unknown>): Promise<KanbanColumn[]> {
    const {data} = await http.get<KanbanColumn[]>('/tasks/kanban', {params}); // фикс: ведущий '/'
    return data;
}

export async function fetchTask(id: number): Promise<Task> {
    const {data} = await http.get<Task>(`/tasks/${id}`);
    return data;
}

export async function createTask(payload: CreateTaskPayload): Promise<Task> {
    const {data} = await http.post<Task>('/tasks', payload);
    return data;
}

export async function updateTask(id: number, payload: Partial<CreateTaskPayload>): Promise<Task> {
    const {data} = await http.patch<Task>(`/tasks/${id}`, payload);
    return data;
}

export async function deleteTask(id: number): Promise<void> {
    await http.delete(`/tasks/${id}`);
}
