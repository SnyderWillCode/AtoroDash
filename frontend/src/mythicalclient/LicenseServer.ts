import { useSettingsStore } from '@/stores/settings';

interface LicenseResponse {
    success: boolean;
    message: string;
    error?: string;
}

interface LicenseCache {
    valid: boolean;
    timestamp: number;
}

export class LicenseServer {
    private static readonly CACHE_KEY = 'license';
    private static readonly CACHE_DURATION = 5 * 60 * 1000; // 5 minutes in milliseconds

    /**
     * Validates the license with the server and caches the result
     */
    public static async validateLicense(): Promise<boolean> {
        try {
            // Check cache first
            const cachedLicense = this.getLicenseFromCache();
            if (cachedLicense !== null) {
                return cachedLicense.valid;
            }

            // If no cache or expired, fetch from server
            const response = await fetch('/api/system/license');
            const data: LicenseResponse = await response.json();

            // Cache the result
            this.cacheLicense(data.success);

            return data.success;
        } catch (error) {
            console.error('License validation failed:', error);
            return false;
        }
    }

    /**
     * Gets the license status from cache
     */
    private static getLicenseFromCache(): LicenseCache | null {
        try {
            const cached = localStorage.getItem(this.CACHE_KEY);
            if (!cached) return null;

            const license: LicenseCache = JSON.parse(cached);
            const now = Date.now();

            // Check if cache is expired
            if (now - license.timestamp > this.CACHE_DURATION) {
                localStorage.removeItem(this.CACHE_KEY);
                return null;
            }

            return license;
        } catch {
            return null;
        }
    }

    /**
     * Caches the license status
     */
    private static cacheLicense(isValid: boolean): void {
        const license: LicenseCache = {
            valid: isValid,
            timestamp: Date.now(),
        };
        localStorage.setItem(this.CACHE_KEY, JSON.stringify(license));
    }

    /**
     * Checks if the license is valid (from cache or server)
     */
    public static async isLicenseValid(): Promise<boolean> {
        const Settings = useSettingsStore();
        const appName = Settings.getSetting('app_name');
        console.log('Checking license for ' + appName);

        const isValid = await this.validateLicense();
        console.log('License is valid:', isValid);
        return isValid;
    }

    /**
     * Clears the license cache
     */
    public static clearCache(): void {
        localStorage.removeItem(this.CACHE_KEY);
    }

    /**
     * Forces a refresh of the license status
     */
    public static async refreshLicense(): Promise<boolean> {
        this.clearCache();
        return await this.validateLicense();
    }
}
