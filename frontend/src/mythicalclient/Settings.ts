class Settings {
    static settings = {};

    static async grabSettings() {
        try {
            const response = await fetch('/api/system/settings');
            const data = await response.json();
            if (data.success) {
                Settings.settings = data.settings;
                return Settings.settings;
            } else {
                throw new Error(data.message || 'Failed to fetch settings');
            }
        } catch (error) {
            console.error('Error fetching settings:', error);
            throw error;
        }
    }

    static async grabCore() {
        try {
            const response = await fetch('/api/system/settings');
            const data = await response.json();
            if (data.success) {
                return data.core;
            } else {
                throw new Error(data.message || 'Failed to fetch core');
            }
        } catch (error) {
            console.error('Error fetching core:', error);
            throw error;
        }
    }
    static async initializeSettings() {
        const response = await fetch('/api/system/settings');
        if (response.status === 503) {
            document.body.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #333; color: #fff; font-family: Arial, sans-serif;">
        <div style="text-align: center;">
        <h1 style="font-size: 3em; margin-bottom: 0.5em;">Rate Limited</h1>
        <p style="font-size: 1.5em;">You have been rate limited. Please try again later.</p>
        <button onclick="location.reload()" style="margin-top: 1em; padding: 0.5em 1em; font-size: 1em; color: #fff; background-color: #444; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
        </div>
        </div>
        `;
            return;
        }
        try {
            const fetchedSettings = await Settings.grabSettings();
            const fetchedCore = await Settings.grabCore();
            console.log('Settings fetched:', fetchedSettings);
            console.log('Core fetched:', fetchedCore);
            for (const [key, value] of Object.entries(fetchedSettings)) {
                localStorage.setItem(key, JSON.stringify(value));
            }

            for (const [key, value] of Object.entries(fetchedCore)) {
                localStorage.setItem(key, JSON.stringify(value));
            }
        } catch (error) {
            console.error('Failed to initialize settings:', error);
            document.body.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #333; color: #fff; font-family: Arial, sans-serif;">
        <div style="text-align: center;">
          <h1 style="font-size: 3em; margin-bottom: 0.5em;">We are so sorry</h1>
          <p style="font-size: 1.5em;">Our backend is down at this moment :(</p>
          <button onclick="location.reload()" style="margin-top: 1em; padding: 0.5em 1em; font-size: 1em; color: #fff; background-color: #444; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
        </div>
        </div>
      `;
        }
    }

    static getSetting(key: string) {
        const appName = localStorage.getItem('app_name');
        if (!appName) {
            // Display loading animation
            const loadingElement = document.createElement('div');
            loadingElement.id = 'loading-animation';
            loadingElement.innerHTML = '<p>Loading...</p>';
            document.body.appendChild(loadingElement);

            // Fetch settings synchronously
            Settings.initializeSettings();

            // Remove loading animation
            document.body.removeChild(loadingElement);
        } else {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : 'Fetching settings...';
        }
    }
}

export default Settings;
