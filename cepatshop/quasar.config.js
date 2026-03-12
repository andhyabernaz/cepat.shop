/* eslint-env node */

/*
 * This file runs in a Node context (it's NOT transpiled by Babel), so use only
 * the ES6 features that are supported by your Node version. https://node.green/
 */

// Configuration for your app
// https://v2.quasar.dev/quasar-cli-webpack/quasar-config-js


const ESLintPlugin = require('eslint-webpack-plugin')

const path = require('path')
const fse = require('fs-extra');


const { configure } = require('quasar/wrappers');

module.exports = configure(function (ctx) {
   return {
      // https://v2.quasar.dev/quasar-cli-webpack/supporting-ts
      supportTS: false,

      // https://v2.quasar.dev/quasar-cli-webpack/prefetch-feature
      // preFetch: true,

      // app boot file (/src/boot)
      // --> boot files are part of "main.js"
      // https://v2.quasar.dev/quasar-cli-webpack/boot-files
      boot: [

         'axios', 'helpers', 'functions', 'components', 'swiper', 'lazy-img'
      ],

      // https://v2.quasar.dev/quasar-cli-webpack/quasar-config-js#Property%3A-css
      css: [
         'app.scss'
      ],

      // https://github.com/quasarframework/quasar/tree/dev/extras
      extras: [
         // 'ionicons-v4', // Removed - use eva-icons only to reduce bundle
         // 'mdi-v7',
         // 'fontawesome-v6',
         'eva-icons',
         // 'themify',
         // 'line-awesome',
         // 'roboto-font-latin-ext', // this or either 'roboto-font', NEVER both!

         // 'roboto-font', // Removed - using Inter from Google Fonts instead
         'material-icons', // optional, you are not bound to it
      ],

      // Full list of options: https://v2.quasar.dev/quasar-cli-webpack/quasar-config-js#Property%3A-build
      build: {
         vueRouterMode: ctx.dev ? 'hash' : 'history', // available values: 'hash', 'history'

         env: {
            API: ctx.dev
               ? 'http://localhost:8000/api'
               : 'api',
            PUBLIC_API: ctx.dev
               ? 'http://localhost:8000/api-public/'
               : 'api-public/',
         },

         htmlFilename: ctx.dev ? 'index.html' : 'index.php',

         // transpile: false,
         publicPath: '/',

         // Add dependencies for transpiling with Babel (Array of string/regex)
         // (from node_modules, which are by default not transpiled).
         // Applies only if "transpile" is set to true.
         // transpileDependencies: [],

         // rtl: true, // https://quasar.dev/options/rtl-support
         // preloadChunks: true,
         // showProgress: false,
         // gzip: true,
         // analyze: true,

         // Options below are automatically set depending on the env, set them if you want to override
         // extractCSS: false,

         // https://v2.quasar.dev/quasar-cli-webpack/handling-webpack
         // "chain" is a webpack-chain object https://github.com/neutrinojs/webpack-chain

         // Enable gzip compression for production builds
         gzip: !ctx.dev,

         chainWebpack(chain) {
            chain.plugin('eslint-webpack-plugin')
               .use(ESLintPlugin, [{ extensions: ['js', 'vue'] }])

            if (!ctx.dev) {
               chain.output.publicPath('/')

               // Optimize chunk splitting for better caching
               chain.optimization.splitChunks({
                  chunks: 'all',
                  maxInitialRequests: 10,
                  maxAsyncRequests: 10,
                  minSize: 20000,
                  maxSize: 244000,
                  cacheGroups: {
                     vendor: {
                        test: /[\\/]node_modules[\\/]/,
                        name: 'vendor',
                        chunks: 'all',
                        priority: 10,
                     },
                     quasar: {
                        test: /[\\/]node_modules[\\/]quasar[\\/]/,
                        name: 'quasar',
                        chunks: 'all',
                        priority: 20,
                     },
                     swiper: {
                        test: /[\\/]node_modules[\\/]swiper[\\/]/,
                        name: 'swiper',
                        chunks: 'all',
                        priority: 20,
                     },
                     common: {
                        minChunks: 2,
                        priority: -10,
                        reuseExistingChunk: true,
                     },
                  },
               })

               // Minimize CSS
               chain.optimization.minimize(true)
            }
         },

         distDir: 'build',

         afterBuild: ({ quasarConf }) => {
            const indexFilename = 'index.php'
            const appFileName = 'app.blade.php'
            const composeDistPath = (src) => path.join(quasarConf.build.distDir, src);
            const composeServerPath = (src) =>
               path.join(__dirname, '../public', src);
            const composeViewPath = (src) =>
               path.join(__dirname, '../resources/views', src);

            for (const fileName of fse.readdirSync(quasarConf.build.distDir)) {
               if (fileName != indexFilename) {
                  fse.removeSync(composeServerPath(fileName));
                  fse.copySync(composeDistPath(fileName), composeServerPath(fileName));
               }
            }
            const distIndexPath = composeDistPath(indexFilename);
            let html = fse.readFileSync(distIndexPath, 'utf8');

            const bladeBaseTag = '<base href="{{ url(\'/\') }}/">';
            const hasBase = /<base\b/i.test(html);
            if (hasBase) {
               html = html.replace(/<base\b[^>]*>/i, bladeBaseTag);
            } else {
               html = html.replace(/<head(\s[^>]*)?>/i, (match) => `${match}${bladeBaseTag}`);
            }

            fse.removeSync(composeViewPath(appFileName));
            fse.writeFileSync(composeViewPath(appFileName), html, 'utf8');

         },

      },

      sourceFiles: {
         // rootComponent: 'src/App.vue',
         // router: 'src/router',
         // store: 'src/store',
         indexHtmlTemplate: ctx.dev ? 'src/index.template.html' : 'src/prod.template.html',
         // registerServiceWorker: 'src-pwa/register-service-worker.js',
         // serviceWorker: 'src-pwa/custom-service-worker.js',
         // electronMain: 'src-electron/electron-main.js',
         // electronPreload: 'src-electron/electron-preload.js'
      },

      htmlVariables: {
         meta_head: "@include('partial/meta_head')",
         meta_seo: "@include('partial/meta_seo')",
         json_schema: "@include('partial/json_schema')",
         meta_body:
            "@include('partial/meta_body')",
      },

      // Full list of options: https://v2.quasar.dev/quasar-cli-webpack/quasar-config-js#Property%3A-devServer
      devServer: {
         server: {
            type: 'http'
         },
         port: 8080,
         open: true // opens browser window automatically
      },

      // https://v2.quasar.dev/quasar-cli-webpack/quasar-config-js#Property%3A-framework
      framework: {
         config: {
            notify: {
               position: 'top',
               timeout: 2500,
               // progress: true,
            }
         },

         iconSet: 'eva-icons', // Quasar icon set
         // lang: 'en-US', // Quasar language pack

         // For special cases outside of where the auto-import strategy can have an impact
         // (like functional components as one of the examples),
         // you can manually specify Quasar components/directives to be available everywhere:
         //
         // components: [],
         // directives: [],

         // Quasar plugins
         plugins: [
            'Dialog', 'Notify', 'Meta', 'AddressbarColor', 'Loading', 'Cookies'
         ]
      },

      // animations: 'all', // --- includes all animations
      // https://quasar.dev/options/animations
      animations: [],

      // https://v2.quasar.dev/quasar-cli-webpack/developing-ssr/configuring-ssr
      ssr: {
         pwa: false,

         // manualStoreHydration: true,
         // manualPostHydrationTrigger: true,

         prodPort: 3000, // The default port that the production server should use
         // (gets superseded if process.env.PORT is specified at runtime)

         maxAge: 1000 * 60 * 60 * 24 * 30,
         // Tell browser when a file from the server should expire from cache (in ms)


         chainWebpackWebserver(chain) {
            chain.plugin('eslint-webpack-plugin')
               .use(ESLintPlugin, [{ extensions: ['js'] }])
         },


         middlewares: [
            ctx.prod ? 'compression' : '',
            'render' // keep this as last one
         ]
      },

      // https://v2.quasar.dev/quasar-cli-webpack/developing-pwa/configuring-pwa
      pwa: {
         workboxPluginMode: 'GenerateSW', // 'GenerateSW' or 'InjectManifest'
         workboxOptions: {}, // only for GenerateSW

         // for the custom service worker ONLY (/src-pwa/custom-service-worker.[js|ts])
         // if using workbox in InjectManifest mode

         chainWebpackCustomSW(chain) {
            chain.plugin('eslint-webpack-plugin')
               .use(ESLintPlugin, [{ extensions: ['js'] }])
         },

         metaVariables: {
            mobileWebAppCapable: 'yes',
            appleMobileWebAppStatusBarStyle: 'default',
            appleTouchIcon120: 'icon/icon-120x120.png',
            appleTouchIcon180: 'icon/icon-180x180.png',
            appleTouchIcon152: 'icon/icon-152x152.png',
            appleTouchIcon167: 'icon/icon-167x167.png',
            appleSafariPinnedTab: 'icon/icon-120x120.png',
            msapplicationTileImage: 'icon/icon-144x144.png',
            msapplicationTileColor: '#000000'
         },
         manifest: {
            name: `Cepatshop App`,
            short_name: `Cepatshop App`,
            description: `The next generation online shop apps`,
            display: 'standalone',
            orientation: 'portrait',
            background_color: '#ffffff',
            theme_color: '#01a833ff',
            icons: [
               {
                  src: 'icon/icon-128x128.png',
                  sizes: '128x128',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-144x144.png',
                  sizes: '144x144',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-152x152.png',
                  sizes: '152x152',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-167x167.png',
                  sizes: '167x167',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-180x180.png',
                  sizes: '180x180',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-192x192.png',
                  sizes: '192x192',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-256x256.png',
                  sizes: '256x256',
                  type: 'image/png'
               },
               {
                  src: 'icon/icon-384x384.png',
                  sizes: '384x384',
                  type: 'image/png'
               },
            ]
         }
      },

      // Full list of options: https://v2.quasar.dev/quasar-cli-webpack/developing-cordova-apps/configuring-cordova
      cordova: {
         // noIosLegacyBuildFlag: true, // uncomment only if you know what you are doing
      },

      // Full list of options: https://v2.quasar.dev/quasar-cli-webpack/developing-capacitor-apps/configuring-capacitor
      capacitor: {
         hideSplashscreen: true
      },

      // Full list of options: https://v2.quasar.dev/quasar-cli-webpack/developing-electron-apps/configuring-electron
      electron: {
         bundler: 'packager', // 'packager' or 'builder'

         packager: {
            // https://github.com/electron-userland/electron-packager/blob/master/docs/api.md#options

            // OS X / Mac App Store
            // appBundleId: '',
            // appCategoryType: '',
            // osxSign: '',
            // protocol: 'myapp://path',

            // Windows only
            // win32metadata: { ... }
         },

         builder: {
            // https://www.electron.build/configuration/configuration

            appId: 'cepatshop'
         },

         // "chain" is a webpack-chain object https://github.com/neutrinojs/webpack-chain

         chainWebpackMain(chain) {
            chain.plugin('eslint-webpack-plugin')
               .use(ESLintPlugin, [{ extensions: ['js'] }])
         },



         chainWebpackPreload(chain) {
            chain.plugin('eslint-webpack-plugin')
               .use(ESLintPlugin, [{ extensions: ['js'] }])
         },

      }
   }
});
