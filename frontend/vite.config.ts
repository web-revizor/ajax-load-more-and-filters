import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import * as path from 'node:path';

const isWatch = process.argv.includes('--watch') || process.argv.includes('-w');

export default defineConfig({
  plugins: [react()],
  define: {
    'process.env': {},
    'process.env.NODE_ENV': '"production"',
  },
  build: {
    outDir: '../dist',
    emptyOutDir: false,
    minify: isWatch ? 'esbuild' : 'terser',
    terserOptions: isWatch
      ? undefined
      : {
          compress: {
            drop_console: false,
            drop_debugger: true,
            passes: 2,
          },
        },
    lib: {
      entry: './src/index.tsx',
      name: 'WebRevizorAjaxLoadMore',
      formats: ['iife'],
      fileName: () => 'app.js',
    },
    rollupOptions: {
      external: ['react', 'react-dom'],
      output: {
        globals: {
          react: 'React',
          'react-dom': 'ReactDOM',
        },
        inlineDynamicImports: true,
        // The lib build otherwise names the CSS asset after the entry
        // ("index.css"); force it to style.css to match what class-admin.php enqueues.
        assetFileNames: 'style.css',
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './'),
    },
  },
});
