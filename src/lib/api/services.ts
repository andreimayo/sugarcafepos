import { apiClient } from './client';
import { ENDPOINTS } from './config';
import type {
    Product,
    Order,
    Category,
    LoginCredentials,
    AuthResponse,
    ApiResponse,
    SalesReport
} from './types';

// Auth Services
export const authService = {
    async login(credentials: LoginCredentials): Promise<ApiResponse<AuthResponse>> {
        const response = await apiClient.post<AuthResponse>(ENDPOINTS.AUTH.LOGIN, credentials);
        return response;
    },

    async logout(): Promise<ApiResponse<void>> {
        const response = await apiClient.post<void>(ENDPOINTS.AUTH.LOGOUT, {});
        apiClient.clearToken();
        return response;
    }
};

// Product Services
export const productService = {
    async getProducts(): Promise<ApiResponse<Product[]>> {
        return apiClient.get<Product[]>(ENDPOINTS.PRODUCTS.LIST);
    },

    async getProduct(id: string): Promise<ApiResponse<Product>> {
        return apiClient.get<Product>(ENDPOINTS.PRODUCTS.DETAIL(id));
    },

    async createProduct(product: Omit<Product, 'id' | 'created_at' | 'updated_at'>): Promise<ApiResponse<Product>> {
        return apiClient.post<Product>(ENDPOINTS.PRODUCTS.CREATE, product);
    },

    async updateProduct(id: string, product: Partial<Product>): Promise<ApiResponse<Product>> {
        return apiClient.put<Product>(ENDPOINTS.PRODUCTS.UPDATE(id), product);
    },

    async deleteProduct(id: string): Promise<ApiResponse<void>> {
        return apiClient.delete<void>(ENDPOINTS.PRODUCTS.DELETE(id));
    }
};

// Order Services
export const orderService = {
    async getOrders(): Promise<ApiResponse<Order[]>> {
        return apiClient.get<Order[]>(ENDPOINTS.ORDERS.LIST);
    },

    async getOrder(id: string): Promise<ApiResponse<Order>> {
        return apiClient.get<Order>(ENDPOINTS.ORDERS.DETAIL(id));
    },

    async createOrder(order: Omit<Order, 'id' | 'created_at'>): Promise<ApiResponse<Order>> {
        return apiClient.post<Order>(ENDPOINTS.ORDERS.CREATE, order);
    }
};

// Category Services
export const categoryService = {
    async getCategories(): Promise<ApiResponse<Category[]>> {
        return apiClient.get<Category[]>(ENDPOINTS.CATEGORIES.LIST);
    },

    async getCategory(id: string): Promise<ApiResponse<Category>> {
        return apiClient.get<Category>(ENDPOINTS.CATEGORIES.DETAIL(id));
    },

    async createCategory(category: Omit<Category, 'id'>): Promise<ApiResponse<Category>> {
        return apiClient.post<Category>(ENDPOINTS.CATEGORIES.CREATE, category);
    },

    async updateCategory(id: string, category: Partial<Category>): Promise<ApiResponse<Category>> {
        return apiClient.put<Category>(ENDPOINTS.CATEGORIES.UPDATE(id), category);
    },

    async deleteCategory(id: string): Promise<ApiResponse<void>> {
        return apiClient.delete<void>(ENDPOINTS.CATEGORIES.DELETE(id));
    }
};

// Report Services
export const reportService = {
    async getSalesReport(startDate?: string, endDate?: string): Promise<ApiResponse<SalesReport>> {
        const query = new URLSearchParams();
        if (startDate) query.append('start_date', startDate);
        if (endDate) query.append('end_date', endDate);
        
        const endpoint = `${ENDPOINTS.REPORTS.SALES}?${query.toString()}`;
        return apiClient.get<SalesReport>(endpoint);
    }
};