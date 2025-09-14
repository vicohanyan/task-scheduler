export type TaskStatus = 'todo' | 'in_progress' | 'done' | 'blocked';
export const TASKS_TYPE_LIST = 'list';
export const TASKS_TYPE_KANBAN = 'kanban';

export interface User {
    id: number
    name: string
    email: string
    available: boolean
}

export interface TaskStatusInfo {
    code: TaskStatus
    display_name: string
}

export interface Task {
    id: number
    name: string
    description?: string | null
    status: TaskStatus
    priority: number
    due_date?: string | null
    assigned_user_id?: number | null
    assignee?: Pick<User, 'id' | 'name'> | null
    status_info?: TaskStatusInfo
}

export interface KanbanTask {
    id: number;
    name: string;
    description: string | null;
    status: TaskStatus;
    priority: number;
    due_date: string | null;
    assigned_user_id: number | null;
    assignee?: Pick<User, 'id' | 'name'> | null
}

export interface KanbanColumn {
    code: TaskStatus;
    display_name: string;
    tasks?: (KanbanTask|null)[] | null;
}

export interface CreateTaskPayload {
    name: string
    description?: string
    status?: TaskStatus
    priority?: number
    due_date?: string
    assigned_user_id?: number
}
