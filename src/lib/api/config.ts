// API Configuration
export const API_BASE_URL = 'http://localhost:8000/sugarcafe-api';

// API Endpoints
export const ENDPOINTS = {
    AUTH: {
        LOGIN: '/auth/login',
        LOGOUT: '/auth/logout'
    },
    PRODUCTS: {
        LIST: '/products',
        DETAIL: (id: string) => `/products/${id}`,
        CREATE: '/products',
        UPDATE: (id: string) => `/products/${id}`,
        DELETE: (id: string) => `/products/${id}`
    },
    ORDERS: {
        LIST: '/orders',
        DETAIL: (id: string) => `/orders/${id}`,
        CREATE: '/orders'
    },
    CATEGORIES: {
        LIST: '/categories',
        DETAIL: (id: string) => `/categories/${id}`,
        CREATE: '/categories',
        UPDATE: (id: string) => `/categories/${id}`,
        DELETE: (id: string) => `/categories/${id}`
    },
    REPORTS: {
        SALES: '/reports/sales'
    }
};

