import { boot } from 'quasar/wrappers'
import { computed } from 'vue'
import { Platform } from 'quasar'

// "async" is optional;
// more info on params: https://v2.quasar.dev/quasar-cli/boot-files
export default boot(async ({ app, router, store }) => {

   const getHeaderColorBrand = computed(() => {
      return store.getters['getHeaderColorBrand']
   })
   app.config.globalProperties.getHeaderColorBrand = getHeaderColorBrand

   const currentWhatsappUrl = Platform.is.desktop ? 'https://web.whatsapp.com' : 'https://api.whatsapp.com'

   app.config.globalProperties.currentWhatsappUrl = currentWhatsappUrl

})
