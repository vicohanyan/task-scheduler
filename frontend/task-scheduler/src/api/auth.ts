import { http } from './http';
import type { User } from './types';

export async function register(payload: {
    name: string
    email: string
    password: string
    password_confirmation: string
}): Promise<{ user: User; token: string }> {
    const { data } = await http.post<{ user: User; token: string }>('/auth/register', payload);
    return data;
}

export async function login(email: string, password: string): Promise<{ user: User; token: string }> {
    const { data } = await http.post<{ user: User; token: string }>('/auth/login', { email, password });
    return data;
}

export async function logout(): Promise<{ message: string }> {
    const { data } = await http.post<{ message: string }>('/auth/logout');
    return data;
}