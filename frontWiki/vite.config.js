import { defineConfig } from 'vite'
import path from 'path'

export default defineConfig({
  base: './',
  build: {
    outDir: '../public/assets', 
    emptyOutDir: true,
    rollupOptions: {
      input: './src/main.js',
      output: {
        entryFileNames: 'main.js',
        assetFileNames: '[name].[ext]'
      }
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      '@assets': path.resolve(__dirname, './assets'),
    },
  },
});
