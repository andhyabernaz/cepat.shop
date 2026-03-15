const path = require('path')
const { defineConfig } = require('vitest/config')
const vue = require('@vitejs/plugin-vue')

module.exports = defineConfig({
   plugins: [vue()],
   resolve: {
      alias: {
         src: path.resolve(__dirname, './src'),
         boot: path.resolve(__dirname, './src/boot'),
         components: path.resolve(__dirname, './src/components'),
         layouts: path.resolve(__dirname, './src/layouts'),
         pages: path.resolve(__dirname, './src/pages'),
         assets: path.resolve(__dirname, './src/assets'),
      }
   },
   test: {
      environment: 'jsdom',
      globals: true,
      include: ['tests/unit/**/*.spec.js'],
      setupFiles: ['tests/vitest.setup.js'],
   }
})
