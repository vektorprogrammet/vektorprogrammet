import { defineConfig } from 'vite';
import legacy from '@vitejs/plugin-legacy';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import autoprefixer from 'autoprefixer';
import { resolve } from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';
import path from 'path';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig(({ mode }) => {
  const isDev = mode === 'development';

  return {
    // Root directory
    root: __dirname,
    base: '/',

    // Build configuration
    build: {
      // Output directory
      outDir: 'public',
      // Don't clean the entire public directory (Symfony uses it for other things)
      emptyOutDir: false,
      // Generate manifest for asset mapping
      manifest: true,
      // Source maps: enabled in dev mode for debugging, disabled in prod
      sourcemap: isDev,
      // Minification: disabled in dev (faster builds), terser in prod (JS + CSS via cssnano)
      minify: isDev ? false : 'terser',

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
            // CSS files go to css/ directory with naming matching Gulp output
            if (assetInfo.name && assetInfo.name.endsWith('.css')) {
              // Map controlPanel -> control_panel to match Gulp naming (app.css, control_panel.css)
              const name = assetInfo.name.replace('controlPanel', 'control_panel');
              return `css/${name}`;
            }
            // Other assets to appropriate directories
            return 'assets/[name]-[hash][extname]';
          },
        },
      },

      // Terser options for production
      terserOptions: {
        compress: {
          drop_console: !isDev,
        },
      },
    },

    // CSS configuration
    css: {
      preprocessorOptions: {
        scss: {
          // Allow relative imports from assets/scss and resolve node_modules from project root
          // This allows CoreUI and other SCSS files to find Bootstrap via "node_modules/bootstrap/..."
          loadPaths: [
            resolve(__dirname, 'assets/scss'),
            resolve(__dirname, 'node_modules'),
            __dirname,
          ],
        },
      },
      postcss: {
        plugins: [
          // Autoprefixer matching gulpfile.js configuration
          // Gulp uses autoprefixer@3.1.1 which uses defaults (> 1%, last 2 versions, Firefox ESR)
          autoprefixer({
            overrideBrowserslist: ['> 1%', 'last 2 versions', 'not dead'],
          }),
        ],
      },
      // Source maps for CSS: enabled in dev mode, disabled in production
      devSourcemap: isDev,
      // CSS minification: handled automatically by Vite in production (via cssnano, like Gulp)
      // In dev mode, CSS is not minified for easier debugging
    },

    // Development server configuration
    server: {
      // Don't open browser automatically
      open: false,
      // Port for dev server
      port: 5173,
      strictPort: false,
      // Serve static files from public/
      publicDir: false,
      // Watch configuration
      watch: {
        usePolling: false,
        include: ['assets/**'],
      },
    },

    // Resolve configuration
    resolve: {
      alias: {
        // Alias for easier imports
        '@': resolve(__dirname, 'assets'),
        '@scss': resolve(__dirname, 'assets/scss'),
        '@js': resolve(__dirname, 'assets/js'),
        '~bootstrap': resolve(__dirname, 'node_modules/bootstrap'),
        '~@fortawesome': resolve(__dirname, 'node_modules/@fortawesome'),
      },
    },

    // Plugins
    plugins: [
      // Legacy browser support (transpile to ES5, polyfills)
      legacy({
        targets: ['defaults', 'not IE 11'],
        polyfills: true,
        modernPolyfills: true,
        renderLegacyChunks: true,
        additionalLegacyPolyfills: ['regenerator-runtime/runtime'],
      }),

      // Static asset copying - matches Gulp's file/icon/vendor tasks
      viteStaticCopy({
        targets: [
          // Copy images
          {
            src: 'assets/images/**/*',
            dest: 'images',
          },
          // Copy static files (PDFs, etc.)
          {
            src: 'assets/files/*',
            dest: 'files',
          },
          // Copy FontAwesome webfonts
          {
            src: 'node_modules/@fortawesome/fontawesome-free/webfonts/**.*',
            dest: 'webfonts',
          },
          // Copy CKEditor (dist files only to avoid .idea permission issues)
          {
            src: 'node_modules/ckeditor/*.js',
            dest: 'vendor/ckeditor',
          },
          {
            src: 'node_modules/ckeditor/*.css',
            dest: 'vendor/ckeditor',
          },
          {
            src: 'node_modules/ckeditor/lang/**/*',
            dest: 'vendor/ckeditor/lang',
          },
          {
            src: 'node_modules/ckeditor/skins/**/*',
            dest: 'vendor/ckeditor/skins',
          },
          {
            src: 'node_modules/ckeditor/plugins/**/*',
            dest: 'vendor/ckeditor/plugins',
          },
          // Copy custom CKEditor config and plugins to vendor (for CKEditor runtime)
          {
            src: 'assets/js/ckeditor/**/*',
            dest: 'vendor/ckeditor',
          },
          // Copy CKEditor helper scripts to js/ (for template script tags)
          // Templates reference: js/ckeditor/createCkeditorInstances.js
          {
            src: 'assets/js/ckeditor/*.js',
            dest: 'js/ckeditor',
          },
          // Copy Dropzone (dist directory only to avoid .idea permission issues)
          {
            src: 'node_modules/dropzone/dist/**/*',
            dest: 'vendor/dropzone/dist',
          },
          // Copy CropperJS
          {
            src: 'node_modules/cropperjs/dist/*',
            dest: 'vendor/cropperjs',
          },
          // Copy custom CoreUI
          {
            src: 'assets/js/coreui.js',
            dest: 'vendor',
          },
          // Copy CoreUI minified
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

      // Custom plugin to create vendor.js bundle matching gulpfile.js vendor task
      // Note: jQuery automatically exposes window.$ and window.jQuery when loaded via script tag
      {
        name: 'vendor-bundle',
        closeBundle() {
          // Create vendor.js bundle from jQuery, Popper, Bootstrap, Moment
          // Bundle order is critical: jQuery first, then Popper, then Bootstrap (depends on both), then Moment
          const vendorFiles = [
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/popper.js/dist/umd/popper.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/moment/min/moment.min.js',
          ];

          const vendorBundle = vendorFiles
            .map((file) => {
              const fullPath = resolve(__dirname, file);
              if (!fs.existsSync(fullPath)) {
                console.warn(`Warning: Vendor file not found: ${file}`);
                return '';
              }
              return fs.readFileSync(fullPath, 'utf-8');
            })
            .filter((content) => content.length > 0)
            .join('\n\n');

          const outputPath = resolve(__dirname, 'public/js/vendor.js');
          fs.mkdirSync(path.dirname(outputPath), { recursive: true });
          fs.writeFileSync(outputPath, vendorBundle);

          console.log('Created vendor.js bundle (jQuery + Popper + Bootstrap + Moment)');
        },
      },
    ],

    // Optimization
    optimizeDeps: {
      include: ['jquery', 'bootstrap', 'popper.js', 'moment', '@coreui/coreui'],
    },
  };
});
