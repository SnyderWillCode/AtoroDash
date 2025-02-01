class ServiceCategories {
    /**
     * Get the service categories from the cache or fetch them from the server
     * @returns - The service categories
     */
    public static async getCategoriesCache() {
        const cacheKey = 'serviceCategories';
        const cachedData = localStorage.getItem(cacheKey);
        const cacheExpiryKey = `${cacheKey}_expiry`;
        const cachedExpiry = localStorage.getItem(cacheExpiryKey);
        const now = new Date().getTime();

        if (cachedData && cachedExpiry && now < parseInt(cachedExpiry, 10)) {
            return JSON.parse(cachedData);
        }

        const response = await fetch('/api/user/services/categories', {
            method: 'GET',
        });
        const data = await response.json();
        const expiryTime = now + 15 * 60 * 1000; // 15 minutes from now

        localStorage.setItem(cacheKey, JSON.stringify(data.categories));
        localStorage.setItem(cacheExpiryKey, expiryTime.toString());
        return data.categories;
    }
    /**
     * Check if a service category exists
     * @param name - The name of the service category
     * @returns - True if the service category exists, false otherwise
     */
    public static async doesExist(name: string) {
        try {
            const categories = await this.getCategoriesCache();
            if (!Array.isArray(categories)) {
                return false;
            }
            return categories.some(
                (category: { name: string }) => category && typeof category === 'object' && category.name === name,
            );
        } catch (error) {
            console.error('Error in doesExist:', error);
            return false;
        }
    }

    /**
     * Check if a service category URI exists
     * @param uri - The URI of the service category
     * @returns - True if the service category URI exists, false otherwise
     */
    public static async doesUriExist(uri: string) {
        try {
            const categories = await this.getCategoriesCache();
            if (!Array.isArray(categories)) {
                return false;
            }
            return categories.some(
                (category: { uri: string }) => category && typeof category === 'object' && category.uri === uri,
            );
        } catch (error) {
            console.error('Error in doesUriExist:', error);
            return false;
        }
    }

    /**
     * Get the service category by name
     * @param name - The name of the service category
     * @returns - The service category
     */
    public static async getCategoryByName(name: string) {
        try {
            const categories = await this.getCategoriesCache();
            if (!Array.isArray(categories)) {
                return null;
            }
            return (
                categories.find(
                    (category: { name: string }) => category && typeof category === 'object' && category.name === name,
                ) || null
            );
        } catch (error) {
            console.error('Error in getCategoryByName:', error);
            return null;
        }
    }

    /**
     * Get the service category by URI
     * @param uri - The URI of the service category
     * @returns - The service category
     */
    public static async getCategoryByUri(uri: string) {
        try {
            const categories = await this.getCategoriesCache();
            if (!Array.isArray(categories)) {
                return null;
            }
            return (
                categories.find(
                    (category: { uri: string }) => category && typeof category === 'object' && category.uri === uri,
                ) || null
            );
        } catch (error) {
            console.error('Error in getCategoryByUri:', error);
            return null;
        }
    }


    /**
     * Get the service category ID by URI
     * @param uri - The URI of the service category
     * @returns - The service category ID or null if not found
     */
    public static async getCategoryIdByUri(uri: string) {
        try {
            const category = await this.getCategoryByUri(uri);
            if (!category || typeof category !== 'object') {
                return null;
            }
            return category.id || null;
        } catch (error) {
            console.error('Error in getCategoryIdByUri:', error);
            return null;
        }
    }

    private static categoryInfoCache: Map<number, {
        id: number;
        name: string;
        uri: string;
        description: string;
        futures: Array<{
            id: number;
            category: number;
            name: string;
        }>;
    }> = new Map();

    public static async getCategoryInfo(id: number) {
        try {
            // Check cache first
            if (this.categoryInfoCache.has(id)) {
                return this.categoryInfoCache.get(id);
            }

            const response = await fetch(`/api/user/services/category/${id}/info`, {
                method: 'GET',
            });
            const data = await response.json();

            // Cache the result
            this.categoryInfoCache.set(id, data.category);

            return data.category;
        } catch (error) {
            console.error('Error in getCategoryInfo:', error);
            return null;
        }
    }


}

export default ServiceCategories;
