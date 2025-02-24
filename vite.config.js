import UnheadVite from "@unhead/addons/vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        // "./vendor/tomatophp/filament-simple-theme/resources/css/theme.css",
        "resources/js/app.js",
      ],
      publicDirectory: "public",
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    UnheadVite(), // @see {@link https://unhead.unjs.io/setup/unhead/introduction}
  ],
  server: {
    hmr: {
      host: "localhost",
    },
  },
  build: {
    minify: true,
  },
});
