<template>
   <div>
      <div class="pwa-container" v-if="canInstall">
         <div class="pwa-inner">
            <q-list>
               <q-item>
                  <q-item-section avatar>
                     <img v-if="shop && shop.icon" :src="shop.icon" width="50" height="50" />
                     <img v-else src="/icon/icon-384x384.png" width="50" height="50" />
                  </q-item-section>
                  <q-item-section top>
                     <div class="pwa-title">{{ shop.app_name ? shop.app_name : shop.name }}
                     </div>
                     <q-item-label class="pwa-caption" lines="2" v-if="shop.download_desc">{{ shop.download_desc }}</q-item-label>
                     <q-item-label class="pwa-caption" lines="2" v-else>Berbelanja akan lebih mudah dan cepat dengan menggunakan
                        aplikasi.</q-item-label>
                  </q-item-section>
                  <q-item-section side v-if="!isMini">
                     <div>
                        <q-btn icon="eva-download" unelevated rounded @click="installNow" no-caps unzelevated
                           class="pwa-button">
                           <span>Install Sekarang</span>
                        </q-btn>
                     </div>
                  </q-item-section>
               </q-item>
               <q-item v-if="isMini">
                  <q-item-section>
                     <q-btn unelevated @click="installNow" no-caps unzelevated class="pwa-button">
                        <span>Install Sekarang</span>
                     </q-btn>
                  </q-item-section>
               </q-item>
            </q-list>
         </div>
      </div>
   </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
   props: {
      spacing: {
         type: Boolean,
         default: false
      },
      dense: {
         type: Boolean,
         default: false
      },
   },
   computed: {
      ...mapState({
         shop: state => state.shop,
         deferredPrompt: state => state.deferredPrompt
      }),
      window_width() {
         return this.$store.state.window_width
      },
      isMini()  {
         return this.window_width < 768 ? true : false
      },
      canInstall: function () {
         if (this.shop && this.shop.download_url) {
            return true;
         } else {
            return this.deferredPrompt ? true : false
         }
      },
   },

   methods: {
      installNow() {
         if (this.shop.download_url) {
            window.open(this.shop.download_url, '_blank')
         } else {
            this.deferredPrompt.prompt();
         }
      },
   },

}
</script>
