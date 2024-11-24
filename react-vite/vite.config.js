import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  build: {
    outDir: 'build', // Output root directory
    rollupOptions: {
      output: {
        entryFileNames: 'static/js/[name].js', // Entry JS files
        chunkFileNames: 'static/js/[name]-[hash].js', // Chunk JS files
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'static/css/main.[hash][extname]'; // CSS files
          }
          return 'static/assets/[name]-[hash][extname]'; // Other assets
        },
      },
    },
  },
})
