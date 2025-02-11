import { useSettingsStore } from '@/stores/settings';
interface MythicalClientOptions {
    timeout?: number;
    theme?: 'dark' | 'light';
    accentColor?: string;
}

interface InstallStep {
    message: string;
    duration: number;
    icon?: string;
}

export class MythicalClient {
    private static instance: MythicalClient | null = null;
    private container: HTMLDivElement | null = null;
    private terminal: HTMLDivElement | null = null;
    private options: Required<MythicalClientOptions>;

    private installSteps: InstallStep[] = [
        {
            message: 'Initializing MythicalClient environment',
            duration: 800,
            icon: '🚀',
        },
        {
            message: 'Configuring client instance',
            duration: 1000,
            icon: '⚙️',
        },
        {
            message: 'Fetching user preferences',
            duration: 1200,
            icon: '🔍',
        },
        {
            message: 'Optimizing cache storage',
            duration: 900,
            icon: '💾',
        },
        {
            message: 'Applying system settings',
            duration: 700,
            icon: '🔧',
        },
        {
            message: 'Preparing UI components',
            duration: 1100,
            icon: '🎨',
        },
        {
            message: 'Launch sequence initiated',
            duration: 1000,
            icon: '✨',
        },
    ];

    private defaultOptions: Required<MythicalClientOptions> = {
        timeout: 7000,
        theme: 'dark',
        accentColor: '#6366f1', // Indigo color
    };

    private constructor(options: MythicalClientOptions = {}) {
        this.options = { ...this.defaultOptions, ...options };
    }

    public static getInstance(options?: MythicalClientOptions): MythicalClient {
        if (!MythicalClient.instance) {
            MythicalClient.instance = new MythicalClient(options);
        }
        return MythicalClient.instance;
    }

    private createStyles(): string {
        const isDark = this.options.theme === 'dark';
        const accent = this.options.accentColor;
        return `
          .mythical-container {
              position: fixed;
              top: 0;
              left: 0;
              width: 100vw;
              height: 100vh;
              display: flex;
              justify-content: center;
              align-items: center;
              background-color: ${isDark ? '#0f172a' : '#f8fafc'};
              color: ${isDark ? '#e2e8f0' : '#334155'};
              z-index: 9999;
              font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
              transition: all 0.3s ease-in-out;
          }

          .mythical-window {
              background-color: ${isDark ? '#1e293b' : '#ffffff'};
              border-radius: 16px;
              padding: 24px;
              width: 500px;
              max-width: 90%;
              box-shadow: 0 8px 32px rgba(0, 0, 0, ${isDark ? '0.4' : '0.1'});
              border: 1px solid ${isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'};
              backdrop-filter: blur(8px);
          }

          .mythical-header {
              display: flex;
              align-items: center;
              margin-bottom: 24px;
          }

          .mythical-logo {
              font-size: 24px;
              font-weight: 700;
              background: linear-gradient(135deg, ${accent}, #a855f7);
              -webkit-background-clip: text;
              -webkit-text-fill-color: transparent;
              margin-right: 12px;
          }

          .mythical-title {
              font-size: 20px;
              font-weight: 600;
              color: ${isDark ? '#f1f5f9' : '#1e293b'};
          }

          .mythical-content {
              min-height: 240px;
          }

          .mythical-step {
              display: flex;
              align-items: center;
              padding: 12px 16px;
              margin: 8px 0;
              background-color: ${isDark ? 'rgba(255, 255, 255, 0.03)' : 'rgba(0, 0, 0, 0.02)'};
              border-radius: 12px;
              opacity: 0;
              transform: translateY(10px);
              transition: all 0.3s ease;
          }

          .mythical-step.visible {
              opacity: 1;
              transform: translateY(0);
          }

          .mythical-step-icon {
              font-size: 20px;
              margin-right: 12px;
              min-width: 24px;
              text-align: center;
          }

          .mythical-step-message {
              grow: 1;
              font-size: 14px;
              font-weight: 500;
          }

          .mythical-progress {
              height: 2px;
              background-color: ${isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'};
              border-radius: 1px;
              margin-top: 24px;
              overflow: hidden;
          }

          .mythical-progress-bar {
              height: 100%;
              background: linear-gradient(90deg, ${accent}, #a855f7);
              width: 0%;
              transition: width 0.3s ease-in-out;
              animation: progress 6s ease-in-out forwards;
          }

          @keyframes progress {
              0% { width: 0%; }
              100% { width: 100%; }
          }
      `;
    }

    private createInterface(): void {
        const style = document.createElement('style');
        style.textContent = this.createStyles();
        document.head.appendChild(style);

        this.container = document.createElement('div');
        this.container.className = 'mythical-container';

        this.container.innerHTML = `
          <div class="mythical-window">
              <div class="mythical-header">
                  <div class="mythical-logo">✨</div>
                  <div class="mythical-title">Downloading client..</div>
              </div>
              <div class="mythical-content"></div>
              <div class="mythical-progress">
                  <div class="mythical-progress-bar"></div>
              </div>
          </div>
      `;

        document.body.appendChild(this.container);
        this.terminal = this.container.querySelector('.mythical-content');
    }

    private addStep(step: InstallStep): void {
        if (!this.terminal) return;

        const stepElement = document.createElement('div');
        stepElement.className = 'mythical-step';
        stepElement.innerHTML = `
          <div class="mythical-step-icon">${step.icon}</div>
          <div class="mythical-step-message">${step.message}</div>
      `;

        this.terminal.appendChild(stepElement);
        setTimeout(() => stepElement.classList.add('visible'), 50);
    }

    private async runInstallation(): Promise<void> {
        for (const step of this.installSteps) {
            this.addStep(step);
            await new Promise((resolve) => setTimeout(resolve, step.duration));
        }

        setTimeout(() => this.cleanup(), 1000);
    }

    private cleanup(): void {
        if (this.container && document.body.contains(this.container)) {
            this.container.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(this.container!);
                location.reload();
            }, 300);
        }
    }

    public download(): void {
        const hasDownloaded = localStorage.getItem('mythical_downloaded');
        if (!this.container && !hasDownloaded) {
            this.createInterface();
            localStorage.setItem('mythical_downloaded', 'true');
            this.runInstallation();
        }
    }

    public static download(options?: MythicalClientOptions): void {
        const downloadStyle = ['color: #9333ea', 'font-size: 14px', 'font-weight: bold', 'padding: 5px'].join(';');

        console.log('%c📥 Starting MythicalClient download...', downloadStyle);
        console.log('%c🔍 Verifying download options...', downloadStyle);

        MythicalClient.getInstance(options).download();

        console.log('%c✅ Download initiated successfully!', downloadStyle);

        MythicalClient.startClient();
    }

    public static startClient(): void {
        const styles = {
            title: [
                'color: #9333ea',
                'font-size: 20px',
                'font-weight: bold',
                'text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3)',
                'padding: 10px',
            ].join(';'),

            subtitle: ['color: #a855f7', 'font-size: 14px', 'font-style: italic', 'padding: 5px'].join(';'),

            info: ['color: #e9d5ff', 'font-size: 12px', 'padding: 5px'].join(';'),

            warning: ['color: #fbbf24', 'font-weight: bold', 'padding: 5px'].join(';'),

            success: ['color: #34d399', 'font-weight: bold', 'padding: 5px'].join(';'),
        };

        // ASCII Art Logo
        console.log(
            `%c
    ███╗   ███╗██╗   ██╗████████╗██╗  ██╗██╗ ██████╗ █████╗ ██╗
    ████╗ ████║╚██╗ ██╔╝╚══██╔══╝██║  ██║██║██╔════╝██╔══██╗██║
    ██╔████╔██║ ╚████╔╝    ██║   ███████║██║██║     ███████║██║
    ██║╚██╔╝██║  ╚██╔╝     ██║   ██╔══██║██║██║     ██╔══██║██║
    ██║ ╚═╝ ██║   ██║      ██║   ██║  ██║██║╚██████╗██║  ██║███████╗
    ╚═╝     ╚═╝   ╚═╝      ╚═╝   ╚═╝  ╚═╝╚═╝ ╚═════╝╚═╝  ╚═╝╚══════╝
    `,
            styles.title,
        );

        // Welcome Message
        console.log('%cWelcome to MythicalClient! 🚀', styles.subtitle);

        // Environment Info
        console.log('%c🌐 Running in production mode', styles.info);
        console.log(`%c📅 Started at: ${new Date().toLocaleString()}`, styles.info);

        // Security Notice
        console.log('%c⚠️ This is a browser feature intended for developers.', styles.warning);
        console.log('%c⚠️ Do not paste any code here that you do not understand.', styles.warning);

        // Fun Message
        const randomMessages = [
            '🎮 Level up your hosting experience!',
            '🌟 Making hosting magical since 2024',
            '🚀 To infinity and beyond!',
            '🎯 Your success is our mission',
            '🌈 Where dreams become reality',
            '⚡ Powered by pure magic',
        ];

        console.log(`%c${randomMessages[Math.floor(Math.random() * randomMessages.length)]}`, styles.success);

        // Version Info
        const Settings = useSettingsStore();
        console.log('%c📦 Version: ' + Settings.getSetting('version'));

        // Credits
        console.log('%c💜 Made with love by the MythicalClient Team', styles.subtitle);

        MythicalClient.warnNotPaste();
    }

    public static warnNotPaste(): void {
        const styles = {
            title: [
                'color: #9333ea',
                'font-size: 20px',
                'font-weight: bold',
                'text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3)',
                'padding: 10px',
            ].join(';'),

            subtitle: ['color: #a855f7', 'font-size: 14px', 'font-style: italic', 'padding: 5px'].join(';'),

            info: ['color: #e9d5ff', 'font-size: 12px', 'padding: 5px'].join(';'),

            warning: ['color: #fbbf24', 'font-weight: bold', 'padding: 5px'].join(';'),

            success: ['color: #34d399', 'font-weight: bold', 'padding: 5px'].join(';'),
        };
        const warningInterval = setInterval(() => {
            console.log('%c⚠️ WARNING! WARNING! WARNING!', styles.warning);
            console.log('%c⚠️ DO NOT PASTE ANY CODE HERE THAT YOU DO NOT UNDERSTAND!', styles.warning);
            console.log('%c⚠️ PASTING UNKNOWN CODE CAN COMPROMISE YOUR ACCOUNT AND DATA!', styles.warning);
            console.log('%c⚠️ WARNING! WARNING! WARNING!', styles.warning);
        }, 100000);

        // Clean up interval when component unmounts
        window.addEventListener('beforeunload', () => {
            clearInterval(warningInterval);
        });
    }
}

export default MythicalClient;
