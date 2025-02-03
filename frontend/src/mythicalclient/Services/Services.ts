interface Service {
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
    price: number;
}

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

interface ApiResponse {
    code: number;
    error: null | string;
    message: string;
    success: boolean;
    core: CoreInfo;
    services: Service[];
}

interface CacheItem<T> {
    data: T;
    timestamp: number;
}

export class Services {
    private static cache: Map<string, CacheItem<Service[]>> = new Map();
    private static CACHE_DURATION = 5 * 60 * 1000; // 5 minutes in milliseconds

    /**
     * Get services by category with caching
     * @param category - The category to get services for
     * @returns - The services
     */
    public static async getServicesByCategory(category: string): Promise<Service[]> {
        const cacheKey = `services_${category}`;

        // Check cache first
        const cachedData = this.getFromCache(cacheKey);
        if (cachedData) {
            return cachedData;
        }

        // If not in cache, fetch from API
        const response = await fetch(`/api/user/services/${category}/services`, {
            method: 'GET',
        });
        const data = (await response.json()) as ApiResponse;

        // Store in cache
        this.setCache(cacheKey, data.services);

        return data.services;
    }

    /**
     * Get data from cache if it exists and is not expired
     * @param key - Cache key
     * @returns - Cached data or null if expired/not found
     */
    private static getFromCache(key: string): Service[] | null {
        const cached = this.cache.get(key);

        if (!cached) {
            return null;
        }

        const now = Date.now();
        if (now - cached.timestamp > this.CACHE_DURATION) {
            // Cache expired, remove it
            this.cache.delete(key);
            return null;
        }

        return cached.data;
    }

    /**
     * Store data in cache
     * @param key - Cache key
     * @param data - Data to cache
     */
    private static setCache(key: string, data: Service[]): void {
        this.cache.set(key, {
            data,
            timestamp: Date.now(),
        });
    }

    /**
     * Clear all cached data
     */
    public static clearCache(): void {
        this.cache.clear();
    }

    /**
     * Clear cache for a specific category
     * @param category - Category to clear cache for
     */
    public static clearCategoryCache(category: string): void {
        this.cache.delete(`services_${category}`);
    }
}

export type { Service, ApiResponse };
export default Services;
