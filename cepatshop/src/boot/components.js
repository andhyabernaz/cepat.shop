import { boot } from 'quasar/wrappers'
import SimplePagination from 'src/components/SimplePagination.vue'
import AppHeader from 'src/components/AppHeader.vue'
import MoneyFormatter from 'src/components/MoneyFormatter.vue'
import MobileHeader from 'src/components/MobileHeader.vue'

// "async" is optional;
// more info on params: https://v2.quasar.dev/quasar-cli/boot-files
export default boot(async ({ app }) => {
   app.component('SimplePagination', SimplePagination)
   app.component('AppHeader', AppHeader)
   app.component('MoneyFormatter', MoneyFormatter)
   app.component('MobileHeader', MobileHeader)
})
