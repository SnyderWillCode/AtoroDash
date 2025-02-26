interface CoreDebug {
    useRedis: boolean;
    rateLimit: {
        enabled: boolean;
        limit: number;
    };
}

interface CoreInfo {
    debug_os: string;
    debug_os_kernel: string;
    debug_name: string;
    debug_debug: boolean;
    debug_version: string;
    debug_telemetry: boolean;
    debug: CoreDebug;
}

interface Category {
    id: number;
    name: string;
    uri: string;
    headline: string;
    tagline: string;
    enabled: string;
    deleted: string;
    locked: string;
    date: string;
}

interface ServiceDetails {
    id: number;
    category: number;
    name: string;
    tagline: string;
    quantity: number;
    stock: number;
    stock_enabled: string;
    uri: string;
    shortdescription: string;
    description: string;
    setup_fee: number;
    provider: number;
    enabled: string;
    deleted: string;
    locked: string;
    date: string;
}

interface PluginProviderRequirements {
    TEXT: 'text';
    NUMBER: 'number';
    PASSWORD: 'password';
    EMAIL: 'email';
    SELECT: 'select';
    CHECKBOX: 'checkbox';
    TEXT_AREA: 'textarea';
}

interface ServiceResponse {
    category: Category;
    service: ServiceDetails;
    requirements: Record<
        string,
        {
            type: PluginProviderRequirements[keyof PluginProviderRequirements];
            label: string;
            required: boolean;
            placeholder: string;
            options: Record<string, string>[]; // Only for type 'select'
        }
    >;
}

export interface Service {
    id: number;
    category: number;
    name: string;
    tagline: string;
    quantity: number;
    price: number;
    stock: number;
    stock_enabled: string;
    uri: string;
    shortdescription: string;
    description: string;
    setup_fee: number;
    provider: number;
    enabled: string;
    deleted: string;
    locked: string;
    date: string;
}

export interface OrderInterface {
    id: number;
    user: string;
    service: Service;
    provider: number;
    status: 'processing' | 'processed';
    days_left: number;
    deleted: string;
    locked: string;
    date: string;
}

export interface Orders {
    orders: Order[];
}

export interface OrderResponse {
    code: number;
    error: null | string;
    message: string;
    success: boolean;
    core: CoreInfo;
    service: {
        service: ServiceDetails;
        price: number | null;
        requirements: Record<
            string,
            {
                type: PluginProviderRequirements[keyof PluginProviderRequirements];
                label: string;
                required: boolean;
                placeholder: string;
                options: Record<string, string>[];
            }
        >;
    };
}

class Order {
    /**
     * Get the order requirements for a service
     *
     * @param category - The category of the service
     * @param service - The service to get the order requirements for
     *
     * @returns - The order requirements
     */
    public static async getOrder(category: string, service: string): Promise<OrderResponse> {
        try {
            const response = await fetch(`/api/user/services/${category}/${service}/order`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to fetch order details');
            }

            return data;
        } catch (error) {
            console.error('Error fetching order:', error);
            throw error;
        }
    }

    /**
     * Submit an order
     */
    public static async submitOrder(category: string, service: string, formData: Record<string, string>) {
        try {
            const response = await fetch(`/api/user/services/${category}/${service}/order`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to submit order');
            }

            return data;
        } catch (error) {
            console.error('Error submitting order:', error);
            throw error;
        }
    }

    /**
     * Get all orders for the current user
     */
    public static async getOrders(): Promise<{ success: boolean; orders: OrderInterface[]; error?: string }> {
        try {
            const response = await fetch('/api/user/orders', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to fetch orders');
            }

            return data;
        } catch (error) {
            console.error('Error fetching orders:', error);
            throw error;
        }
    }
}

export default Order;
export type { ServiceResponse, ServiceDetails };
