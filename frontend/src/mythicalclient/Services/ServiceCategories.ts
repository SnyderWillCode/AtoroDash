class ServiceCategories {
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
}

export default ServiceCategories;
