interface SettingsResponse {
    success: boolean;
    message?: string;
    settings: Record<string, string>;
    core: Record<string, string>;
}

class Settings {
    private static instance: Settings;
    private static settings: Record<string, string> = {};
    private static initPromise: Promise<void> | null = null;
    private static errorTemplate = (title: string, message: string) => `
        <div style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #333; color: #fff; font-family: Arial, sans-serif;">
            <div style="text-align: center;">
                <h1 style="font-size: 3em; margin-bottom: 0.5em;">${title}</h1>
                <p style="font-size: 1.5em;">${message}</p>
                <button onclick="location.reload()" style="margin-top: 1em; padding: 0.5em 1em; font-size: 1em; color: #fff; background-color: #444; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
            </div>
        </div>
    `;

    private constructor() {}

    private static async fetchSettings(): Promise<SettingsResponse> {
        const response = await fetch('/api/system/settings');
        if (response.status === 503) {
            document.body.innerHTML = this.errorTemplate(
                'Rate Limited',
                'You have been rate limited. Please try again later.',
            );
            throw new Error('Rate limited');
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'Failed to fetch settings');
        }
        return data;
    }

    static async grabSettings() {
        try {
            await this.ensureInitialized();
            return this.settings;
        } catch (error) {
            console.error('Error fetching settings:', error);
            throw error;
        }
    }

    static async grabCore() {
        try {
            await this.ensureInitialized();
            return this.settings.core;
        } catch (error) {
            console.error('Error fetching core:', error);
            throw error;
        }
    }

    private static async ensureInitialized(): Promise<void> {
        if (!this.initPromise) {
            this.initPromise = this.initialize();
        }
        return this.initPromise;
    }

    private static async initialize(): Promise<void> {
        try {
            const data = await this.fetchSettings();

            // Store all settings in memory
            this.settings = {
                ...(data.settings as Record<string, string>),
                core: JSON.stringify(data.core),
            };

            // Store in localStorage for persistence
            Object.entries(data.settings).forEach(([key, value]) => {
                localStorage.setItem(key, JSON.stringify(value));
            });
            Object.entries(data.core).forEach(([key, value]) => {
                localStorage.setItem(key, JSON.stringify(value));
            });
        } catch (error) {
            console.error('Failed to initialize settings:', error);
            document.body.innerHTML = this.errorTemplate('We are so sorry', 'Our backend is down at this moment :(');
            throw error;
        }
    }

    static async initializeSettings() {
        return this.ensureInitialized();
    }

    static getSetting(key: string): string {
        // Try to get from memory first
        if (this.settings[key] !== undefined) {
            return this.settings[key];
        }

        // Try to get from localStorage
        const item = localStorage.getItem(key);
        if (item) {
            try {
                const value = JSON.parse(item);
                this.settings[key] = value; // Cache in memory
                return value;
            } catch {
                return item;
            }
        }

        // If not found and not initialized, trigger initialization
        if (!this.initPromise) {
            const loadingElement = document.createElement('div');
            loadingElement.id = 'loading-animation';
            loadingElement.innerHTML = '<p>Loading...</p>';
            document.body.appendChild(loadingElement);

            this.ensureInitialized().finally(() => {
                document.body.removeChild(loadingElement);
            });
        }

        return 'Fetching settings...';
    }
}

export default Settings;
