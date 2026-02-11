import { defineConfig } from 'vite';
import legacy from '@vitejs/plugin-legacy';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import { resolve } from 'path';

export default defineConfig({
  // Build configuration
  build: {
    // Output directory
    outDir: 'public',
    // Don't clean the entire public directory (Symfony uses it for other things)
    emptyOutDir: false,
    // Generate manifest for asset mapping
    manifest: true,
    // Configure rollup options
    rollupOptions: {
      input: {
        // Main site CSS and JS
        app: resolve(__dirname, 'assets/main.js'),
        // Control panel CSS and JS
        controlPanel: resolve(__dirname, 'assets/control-panel.js'),
      },
      output: {
        // Output structure matching current Gulp setup
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          // CSS files go to css/ directory
          if (assetInfo.name.endsWith('.css')) {
            return 'css/[name][extname]';
          }
          // Other assets to appropriate directories
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
  },

  // CSS configuration
  css: {
    preprocessorOptions: {
      scss: {
        // Allow relative imports from assets/scss
        includePaths: [resolve(__dirname, 'assets/scss')],
      },
    },
  },

  // Development server configuration
  server: {
    // Don't open browser automatically
    open: false,
    // Port for dev server
    port: 3000,
    // Serve static files from public/
    publicDir: false,
  },

  // Resolve configuration
  resolve: {
    alias: {
      // Alias for easier imports
      '@': resolve(__dirname, 'assets'),
      '@scss': resolve(__dirname, 'assets/scss'),
      '@js': resolve(__dirname, 'assets/js'),
    },
  },

  // Plugins
  plugins: [
    // Legacy browser support (transpile to ES5, polyfills)
    legacy({
      targets: ['defaults', 'not IE 11'],
      additionalLegacyPolyfills: ['regenerator-runtime/runtime'],
    }),

    // Static asset copying
    viteStaticCopy({
      targets: [
        // Copy images
        {
          src: 'assets/images/*',
          dest: 'images',
        },
        // Copy static files (PDFs, etc.)
        {
          src: 'assets/files/*',
          dest: 'files',
        },
        // Copy FontAwesome webfonts
        {
          src: 'node_modules/@fortawesome/fontawesome-free/webfonts/*',
          dest: 'webfonts',
        },
        // Copy CKEditor
        {
          src: 'node_modules/ckeditor/**/*',
          dest: 'vendor/ckeditor',
        },
        // Copy custom CKEditor config
        {
          src: 'assets/js/ckeditor/**/*',
          dest: 'vendor/ckeditor',
        },
        // Copy Dropzone
        {
          src: 'node_modules/dropzone/**/*',
          dest: 'vendor/dropzone',
        },
        // Copy CropperJS
        {
          src: 'node_modules/cropperjs/dist/*',
          dest: 'vendor/cropperjs',
        },
        // Copy CoreUI JS to vendor
        {
          src: 'node_modules/@coreui/coreui/dist/js/coreui.min.js',
          dest: 'vendor',
        },
        // Copy individual vendor JS files (for backwards compatibility)
        {
          src: 'node_modules/bootstrap/dist/js/bootstrap.min.js',
          dest: 'js',
        },
        {
          src: 'node_modules/jquery/dist/jquery.min.js',
          dest: 'js',
        },
      ],
    }),
  ],

  // Optimization
  optimizeDeps: {
    include: [
      'jquery',
      'bootstrap',
      'popper.js',
      'moment',
    ],
  },
});
