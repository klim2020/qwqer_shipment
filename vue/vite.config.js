import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  build: {
    rollupOptions: {
        output: {
            //dir: 'plugin/dist',
            entryFileNames: 'qwqer_shipping.js',
            assetFileNames: 'qwqer_shipping.css',
            chunkFileNames: "qwqer_chunk.js",
            manualChunks: undefined,
        }
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, '/src'),
      '~bootstrap': 'bootstrap'
    }
  }
})
