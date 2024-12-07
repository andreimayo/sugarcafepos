import type { ApiResponse, ApiClient } from './types';
import { ENDPOINTS } from './config';

class ApiClientImpl implements ApiClient {
    private static instance: ApiClientImpl;
    private token: string | null = null;
    private baseUrl: string = 'http://localhost:8000/api';

    private constructor() {}

    public static getInstance(): ApiClientImpl {
        if (!ApiClientImpl.instance) {
            ApiClientImpl.instance = new ApiClientImpl();
        }
        return ApiClientImpl.instance;
    }

    private setToken(token: string) {
        this.token = token;
        localStorage.setItem('auth_token', token);
    }

    private getToken(): string | null {
        if (!this.token) {
            this.token = localStorage.getItem('auth_token');
        }
        return this.token;
    }

    public clearToken() {
        this.token = null;
        localStorage.removeItem('auth_token');
    }

    private async request<T>(
        endpoint: string,
        options: RequestInit = {}
    ): Promise<ApiResponse<T>> {
        const token = this.getToken();
        const headers: HeadersInit = {
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
            ...options.headers
        };

        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, {
                ...options,
                headers
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'An error occurred');
            }

            return data;
        } catch (error) {
            if (error instanceof Error) {
                throw new Error(`API Error: ${error.message}`);
            }
            throw new Error('An unknown error occurred');
        }
    }

    public async get<T>(endpoint: string): Promise<ApiResponse<T>> {
        return this.request<T>(endpoint);
    }

    public async post<T>(endpoint: string, body: any): Promise<ApiResponse<T>> {
        return this.request<T>(endpoint, {
            method: 'POST',
            body: JSON.stringify(body)
        });
    }

    public async put<T>(endpoint: string, body: any): Promise<ApiResponse<T>> {
        return this.request<T>(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body)
        });
    }

    public async delete<T>(endpoint: string): Promise<ApiResponse<T>> {
        return this.request<T>(endpoint, {
            method: 'DELETE'
        });
    }
}

export const apiClient = ApiClientImpl.getInstance();