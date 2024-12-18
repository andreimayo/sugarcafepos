import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		fs: {
			allow: [
				// Default allowed locations
				'src/lib',
				'src/routes',
				'.svelte-kit',
				'src',
				'node_modules',
				// Add API directory to allowed locations
				'api'
			]
		},
		proxy: {
			// Proxy API requests to avoid CORS issues
			'/api': {
				target: 'http://localhost:sugarcafepos',
				changeOrigin: true
			}
		}
	}
});