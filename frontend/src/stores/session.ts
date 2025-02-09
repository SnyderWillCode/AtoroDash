import { defineStore } from 'pinia';
import { ref } from 'vue';
import router from '@/router';

interface SessionResponse {
    success: boolean;
    error_code?: string;
    user_info: Record<string, unknown>;
    billing: Record<string, unknown>;
}

export const useSessionStore = defineStore('session', () => {
    const sessionInfo = ref<Record<string, unknown>>({});
    const isValid = ref(false);

    async function startSession() {
        try {
            const response = await fetch('/api/user/session');
            const data: SessionResponse = await response.json();

            if (data.success) {
                sessionInfo.value = { ...data.user_info, ...data.billing };
                isValid.value = true;
            } else {
                isValid.value = false;
                if (data.error_code === 'SESSION_EXPIRED') {
                    router.push('/auth/login');
                }
            }
        } catch (error) {
            console.error('Session error:', error);
            isValid.value = false;
            router.push('/auth/login');
        }
    }

    function getInfo(key: string): string {
        return (sessionInfo.value[key] as string) || '';
    }

    function isSessionValid(): boolean {
        return isValid.value;
    }

    return {
        sessionInfo,
        isValid,
        startSession,
        getInfo,
        isSessionValid,
    };
});
