import { defineStore } from 'pinia';
import { ref } from 'vue';

interface SettingsResponse {
  success: boolean;
  message?: string;
  settings: Record<string, string>;
  core: Record<string, string>;
}

interface CachedSettings {
  value: Record<string, string>;
  timestamp: number;
}

export const useSettingsStore = defineStore('settings', () => {
  const settings = ref<Record<string, string>>({});
  const isInitialized = ref(false);
  const CACHE_DURATION = 30 * 60 * 1000; // 30 minutes in milliseconds
  const CACHE_KEY = 'mythicalclient_settings_cache';

  function loadFromCache(): boolean {
    const cached = localStorage.getItem(CACHE_KEY);
    if (!cached) return false;

    try {
      const { value, timestamp }: CachedSettings = JSON.parse(cached);
      const now = Date.now();

      if (now - timestamp < CACHE_DURATION) {
        settings.value = value;
        isInitialized.value = true;
        return true;
      }

      // Clear expired cache
      localStorage.removeItem(CACHE_KEY);
      return false;
    } catch {
      return false;
    }
  }

  function saveToCache(settingsData: Record<string, string>) {
    const cache: CachedSettings = {
      value: settingsData,
      timestamp: Date.now()
    };
    localStorage.setItem(CACHE_KEY, JSON.stringify(cache));
  }

  const errorTemplate = (title: string, message: string) => `
        <div style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #333; color: #fff; font-family: Arial, sans-serif;">
            <div style="text-align: center;">
                <h1 style="font-size: 3em; margin-bottom: 0.5em;">${title}</h1>
                <p style="font-size: 1.5em;">${message}</p>
                <button onclick="location.reload()" style="margin-top: 1em; padding: 0.5em 1em; font-size: 1em; color: #fff; background-color: #444; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
            </div>
        </div>
    `;

  async function fetchSettings(): Promise<SettingsResponse> {
    const response = await fetch('/api/system/settings');
    if (response.status === 503) {
      document.body.innerHTML = errorTemplate(
        'Rate Limited',
        'You have been rate limited. Please try again later.'
      );
      throw new Error('Rate limited');
    }

    const data = await response.json();
    if (!data.success) {
      throw new Error(data.message || 'Failed to fetch settings');
    }
    return data;
  }

  async function initialize() {
    if (isInitialized.value) return;

    // Try loading from cache first
    if (loadFromCache()) {
      return;
    }
    try {
      const data = await fetchSettings();
      settings.value = { ...data.settings, ...data.core };

      // Store in localStorage for persistence
      saveToCache(settings.value);

      Object.entries(settings.value).forEach(([key, value]) => {
        localStorage.setItem(key, JSON.stringify(value));
      });

      isInitialized.value = true;
    } catch (error) {
      console.error('Failed to initialize settings:', error);
      document.body.innerHTML = errorTemplate(
        'We are so sorry',
        'Our backend is down at this moment :('
      );
      throw error;
    }
  }

  function getSetting(key: string): string {
    // Try to get from store first
    if (settings.value[key] !== undefined) {
      return settings.value[key];
    }

    // Try to get from localStorage
    const item = localStorage.getItem(key);
    if (item) {
      try {
        const value = JSON.parse(item);
        settings.value[key] = value; // Cache in store
        return value;
      } catch {
        return item;
      }
    }

    // If not found and not initialized, trigger initialization
    if (!isInitialized.value) {
      const loadingElement = document.createElement('div');
      loadingElement.id = 'loading-animation';
      loadingElement.innerHTML = '<p>Loading...</p>';
      document.body.appendChild(loadingElement);

      initialize().finally(() => {
        document.body.removeChild(loadingElement);
      });
    }

    return 'Fetching settings...';
  }
  // Function to force refresh settings
  async function refreshSettings() {
    try {
      const data = await fetchSettings();
      settings.value = { ...data.settings, ...data.core };
      saveToCache(settings.value);

      // Update individual localStorage items
      Object.entries(settings.value).forEach(([key, value]) => {
        localStorage.setItem(key, JSON.stringify(value));
      });
    } catch (error) {
      console.error('Failed to refresh settings:', error);
      throw error;
    }
  }

  return {
    settings,
    isInitialized,
    initialize,
    getSetting,
    refreshSettings
  };
});
