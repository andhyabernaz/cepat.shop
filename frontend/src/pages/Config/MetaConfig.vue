<template>
   <div>
      <q-card flat class="section">
         <q-card-section>
            <div class="card-subtitle">FB Pixel & Google Tag Manager</div>
            <form @submit.prevent="updateData">
               <div class="q-gutter-y-sm">
                  <q-input filled v-model="form.fb_pixel" label="FACEBOOK PIXEL ID" />
                  <q-input filled v-model="form.gtm" label="GOOGLE TAG MANAGER ID" />
                  <q-input type="textarea" filled v-model="form.custom_css" label="Custom CSS" />
               </div>
               <div class="q-pt-lg">
                  <q-btn :disable="isLoading" class="full-width" type="submit" label="Simpan Pengaturan"
                     color="primary"></q-btn>
               </div>
            </form>
         </q-card-section>
      </q-card>
   </div>
</template>

<script>
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         form: {
            fb_pixel: '',
            gtm: '',
            custom_css: ''
         },
         isLoading: false,
      }
   },
   computed: {
      config: function () {
         return this.$store.state.config
      },
      shop: function () {
         return this.$store.state.shop
      },
   },
   mounted() {
      this.setData()
   },
   methods: {
      updateData() {
         if(this.form.custom_css) {
            let css = this.css_sanitize(this.form.custom_css)
            this.form.custom_css = css
         }
         this.isLoading = true
         BaseApi.post('config', this.form).then(() => {
            this.$q.notify({
               type: 'positive',
               message: 'Berhasil memperbarui data'
            })
            this.$store.dispatch('getAdminConfig')
         }).catch(() => {
            this.$q.notify({
               type: 'negative',
               message: 'Gagal memperbarui data'
            })
         }).finally(() => {
            this.isLoading = false
         })
      },
      setData() {
         if (this.config) {
            this.form.fb_pixel = this.config.fb_pixel
            this.form.gtm = this.config.gtm
            this.form.custom_css = this.config.custom_css
         }
      },
      css_sanitize(css) {
         const iframe = document.createElement("iframe");
         iframe.style.display = "none";
         iframe.style.width = "10px"; //make small in case display:none fails
         iframe.style.height = "10px";
         document.body.appendChild(iframe);
         const style = iframe.contentDocument.createElement('style');
         style.innerHTML = css;
         iframe.contentDocument.head.appendChild(style);
         const sheet = style.sheet,
            result = Array.from(style.sheet.cssRules).map(rule => rule.cssText || '').join('\n');
         iframe.remove();
         return result;
      }
   }
}
</script>
