import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		fs: {
			allow: [
				// Default allowed locations
				'C:/xampp/htdocs/sugarcafepos/src/lib',
				'C:/xampp/htdocs/sugarcafepos/src/routes',
				'C:/xampp/htdocs/sugarcafepos/.svelte-kit',
				'C:/xampp/htdocs/sugarcafepos/src',
				'C:/xampp/htdocs/sugarcafepos/node_modules',
				// Add the API directory
				'C:/xampp/htdocs/sugarcafepos/sugarcafeapi'
			]
		},
		proxy: {
			'/api': {
				target: 'http://localhost/sugarcafeapi',
				changeOrigin: true,
				rewrite: (path) => path.replace(/^\/api/, '')
			}
		}
	}
});