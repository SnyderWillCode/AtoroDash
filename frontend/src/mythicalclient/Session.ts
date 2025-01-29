import router from '@/router';
import Swal from 'sweetalert2';
import Settings from '@/mythicalclient/Settings';
import { useI18n } from 'vue-i18n';

interface SessionResponse {
    success: boolean;
    error_code?: string;
    user_info: Record<string, unknown>;
    billing: Record<string, unknown>;
}

class Session {
    private static sessionData: Record<string, unknown> = {};
    private static updateInterval: number | null = null;
    private static initPromise: Promise<void> | null = null;

    /**
     * Checks if the current session is valid by looking for the user_token cookie
     */
    static isSessionValid(): boolean {
        return document.cookie.split(';').some((cookie) => cookie.trim().startsWith('user_token='));
    }

    /**
     * Gets session information from memory or localStorage
     */
    static getInfo(key: string): string {
        // Try memory first
        if (this.sessionData[key] !== undefined) {
            return this.sessionData[key] as string;
        }

        // Fall back to localStorage
        const item = localStorage.getItem(key);
        if (item) {
            try {
                const value = JSON.parse(item);
                this.sessionData[key] = value; // Cache in memory
                return value;
            } catch {
                return item;
            }
        }
        return '';
    }

    /**
     * Fetches session data from the server
     */
    private static async fetchSessionData(): Promise<SessionResponse> {
        const response = await fetch('/api/user/session');
        const data = await response.json();

        if (!data.success && this.isSessionValid()) {
            await this.handleSessionError(data);
        }

        return data;
    }

    /**
     * Handles session errors and redirects
     */
    private static async handleSessionError(data: SessionResponse): Promise<void> {
        const { t } = useI18n();
        await Settings.initializeSettings();

        if (data.error_code === 'TW0_FA_BLOCKED') {
            router.push('/auth/2fa/verify');
        } else {
            await Swal.fire({
                title: t('auth.logic.errors.title'),
                text: t('auth.logic.errors.expired'),
                footer: t('auth.logic.errors.footer'),
                icon: 'error',
                confirmButtonText: 'OK',
            });
            router.push('/auth/login');
        }
    }

    /**
     * Updates session information in memory and localStorage
     */
    private static updateSessionStorage(data: SessionResponse): void {
        const { user_info, billing } = data;

        // Update memory cache
        this.sessionData = {
            ...this.sessionData,
            ...user_info,
            ...billing,
        };

        // Update localStorage
        Object.entries(user_info).forEach(([key, value]) => {
            localStorage.setItem(key, JSON.stringify(value));
        });
        Object.entries(billing).forEach(([key, value]) => {
            localStorage.setItem(key, JSON.stringify(value));
        });
    }

    /**
     * Ensures session is initialized only once
     */
    private static async ensureInitialized(): Promise<void> {
        if (!this.initPromise) {
            this.initPromise = this.initialize();
        }
        return this.initPromise;
    }

    /**
     * Initializes the session
     */
    private static async initialize(): Promise<void> {
        try {
            const data = await this.fetchSessionData();
            if (data.success) {
                this.updateSessionStorage(data);
            }
        } catch (error) {
            console.error('Error initializing session:', error);
            throw error;
        }
    }

    /**
     * Starts the session and sets up periodic updates
     */
    static async startSession(): Promise<void> {
        // Rest of your existing startSession code...
        if (this.updateInterval !== null) {
            clearInterval(this.updateInterval);
        }

        await this.ensureInitialized();

        this.updateInterval = window.setInterval(async () => {
            try {
                const data = await this.fetchSessionData();
                if (data.success) {
                    this.updateSessionStorage(data);
                }
            } catch (error) {
                console.error('Error updating session:', error);
            }
        }, 60000);
    }

    /**
     * Cleans up session resources
     */
    static cleanup(): void {
        if (this.updateInterval !== null) {
            clearInterval(this.updateInterval);
            this.updateInterval = null;
        }
        this.sessionData = {};
        this.initPromise = null;
    }
}

export default Session;
