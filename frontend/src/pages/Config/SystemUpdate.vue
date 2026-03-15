<template>
   <q-card flat class="section">
      <q-card-section>
         <div class="card-subtitle">System & Update</div>
         <div class="text-caption text-grey-7">Pengaturan sistem update dan cache aplikasi</div>
         <q-list class="q-py-md" separator>

            <q-item class="q-px-xs">
               <q-item-section>
                  <q-item-label>Cronjob Status</q-item-label>
                  <q-item-label class="text-orange-9 text-xs">*Sangat disarankan</q-item-label>
               </q-item-section>
               <q-item-section side>
                  <q-item-label>
                     <q-chip size="12px" :color="config.cron_status ? 'green' : 'grey-6'" text-color="white"
                        :icon="config.cron_status ? 'check_circle' : 'error'"> {{
                           config.cron_status ?
                              'Active' :
                              'Inactive' }}</q-chip>
                  </q-item-label>
               </q-item-section>
            </q-item>
            <q-item class="q-px-xs">
               <q-item-section>
                  <q-item-label>Generate PWA</q-item-label>
               </q-item-section>
               <q-item-section side>
                  <q-item-label>

                     <q-btn rounded no-caps :loading="pwaLoading" style="min-width:140px;" @click="generatePwa"
                        label="Generate PWA" unelevated color="blue" size=".8rem"></q-btn>
                  </q-item-label>
               </q-item-section>
            </q-item>
            <q-item class="q-px-xs">
               <q-item-section>
                  <q-item-label>Clear Cache</q-item-label>
               </q-item-section>
               <q-item-section side>
                  <q-item-label>

                     <q-btn rounded no-caps style="min-width:140px;" :loading="loadingClearChace" @click="clearCache"
                        label="Clear Cache" unelevated color="blue" size=".8rem"></q-btn>
                  </q-item-label>
               </q-item-section>
            </q-item>

            <q-item class="q-px-xs">
               <q-item-section>
                  <q-item-label v-if="needUpdate">
                     <q-badge color="green-7">{{ siteData }}</q-badge> Pending Update
                  </q-item-label>
                  <q-item-label v-else>Update App</q-item-label>
               </q-item-section>
               <q-item-section side>
                  <q-item-label>

                     <q-btn rounded no-caps style="min-width:140px;" :loading="loading" :disable="!needUpdate"
                        @click="updateSystem" :label="needUpdate ? 'Update Now' : 'No updated found'" unelevated
                        :color="needUpdate ? 'blue' : 'grey-6'" size=".8rem"></q-btn>
                  </q-item-label>
               </q-item-section>
            </q-item>

         </q-list>
      </q-card-section>
   </q-card>
</template>

<script>
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         siteData: 0,
         loading: false,
         loadingClearChace: false,
         pwaLoading: false
      }
   },
   computed: {
      needUpdate() {
         return this.siteData > 0 ? true : false
      },
      config() {
         return this.$store.state.config
      }
   },
   created() {
      this.getUpdate()
   },
   mounted() {
      this.$store.dispatch('getConfig')
   },
   methods: {
      getUpdate() {
         BaseApi.get('update').then(response => {
            if (response.status == 200) {
               this.siteData = response.data.data > 0 ? response.data.data : 0
            }
         })
      },
      generatePwa() {
         this.pwaLoading = true
         BaseApi.get('generate-pwa').then(res => {
            this.$q.notify({
               type: 'positive',
               message: 'Berhasil update system'
            })
         }).finally(() => this.pwaLoading = false)
      },
      updateSystem() {
         this.loading = true
         BaseApi.post('update').then(response => {
            if (response.status == 200) {
               this.getUpdate()
               this.$q.notify({
                  type: 'positive',
                  message: 'Berhasil update system'
               })
            }
         }).catch(() => {
            this.$q.notify({
               type: 'negative',
               message: 'Gagal update system, silahkan hubungi pengembang aplikasi.'
            })
         }).finally(() => {
            this.loading = false
            setTimeout(() => {
               location.reload()
            }, 500)
         })
      },
      clearCache() {
         this.loadingClearChace = true
         BaseApi.post('clearCache').then(response => {
            if (response.status == 200) {
               this.$q.notify({
                  type: 'positive',
                  message: 'Berhasil menghapus cache'
               })
            }
         }).catch(() => {
            this.$q.notify({
               type: 'negative',
               message: 'Gagal menghapus cache, silahkan ulangi lagi'
            })
         }).finally(() => {
            this.loadingClearChace = false
         })
      },

   }
}
</script>
