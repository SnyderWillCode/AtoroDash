import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import vueDevTools from 'vite-plugin-vue-devtools'
import ViteYaml from '@modyfi/vite-plugin-yaml';
import { visualizer } from 'rollup-plugin-visualizer';
import tailwindcss from "@tailwindcss/vite";

// https://vite.dev/config/
export default defineConfig({
	plugins: [
		ViteYaml(),
		vue(),
		vueJsx(),
		vueDevTools(),
		visualizer({ filename: './dist/stats.html' }),
		tailwindcss(),
	],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url))
		},
	},
	server: {
		proxy: {
			'/api': {
				target: 'http://localhost:9000',
				changeOrigin: true,
				rewrite: (path) => path
			}
		}
	},
	build: {
		rollupOptions: {
			output: {
				manualChunks(id) {
					if (id.includes('node_modules')) {
						const packageName = id.toString().split('node_modules/')[1].split('/')[0];
						if (packageName && packageName !== 'vue' && packageName !== 'vue-demi' && packageName !== 'hookable' && packageName !== 'perfect-debounce' && packageName !== 'vue-router' && packageName !== 'pinia' && packageName !== 'birpc') {
							return `pkg-${packageName}`;
						}
					}
					if (id.includes('/src/')) {
						return 'app';
					}
				}
			},
		},
		chunkSizeWarningLimit: 1000,
	},
})
