import { http } from './http';
import type { User } from './types';


export async function fetchUsers(params?: { available?: boolean }): Promise<User[]> {
    const { data } = await http.get<User[]>('/users', { params });
    return data;
}

export async function toggleUserAvailability(userId: number): Promise<{ id: number, available: boolean }> {
    const { data } = await http.patch<{ id: number; available: boolean }>(`/users/${userId}/availability`);
    return data;
}