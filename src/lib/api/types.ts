// API Response Types
export interface ApiResponse<T = any> {
    status: number;
    message: string;
    data?: T;
}

export interface ApiClient {
    post: <T>(endpoint: string, data: any) => Promise<ApiResponse<T>>;
    get: <T>(endpoint: string) => Promise<ApiResponse<T>>;
    put: <T>(endpoint: string, data: any) => Promise<ApiResponse<T>>;
    delete: <T>(endpoint: string) => Promise<ApiResponse<T>>;
    clearToken: () => void;
}

// Auth Types
export interface LoginCredentials {
    username: string;
    password: string;
}

export interface AuthResponse {
    session_id: string;
    role: string;
}

// Product Types
export interface Product {
    id: number;
    name: string;
    price: number;
    category_id?: number;
    description?: string;
    stock: number;
    created_at: string;
    updated_at: string;
}

// Order Types
export interface Order {
    id: number;
    total_amount: number;
    payment_method: string;
    status: string;
    created_at: string;
    items?: OrderItem[];
}

export interface OrderItem {
    id: number;
    order_id: number;
    product_id: number;
    quantity: number;
    unit_price: number;
    subtotal: number;
    product_name?: string;
}

// Category Types
export interface Category {
    id: number;
    name: string;
    description?: string;
}

// Report Types
export interface SalesReport {
    total_sales: number;
    total_orders: number;
    average_order_value: number;
    sales_by_category: {
        category: string;
        total: number;
    }[];
    sales_by_product: {
        product: string;
        quantity: number;
        total: number;
    }[];
}