<template>
   <div>


      <form @submit.prevent="submitOrderConfig">
         <q-card flat class="section">
            <q-card-section>
               <div class="q-mb-xs">
                  <div class="card-subtitle">
                     <q-item-label>Order Expired</q-item-label>
                     <q-item-label caption>Status order akan otomatis di batalkan ketika melewati waktu
                        tersebut. NOTE: task ini akan dijalankan ketika cronjob diaktifkan</q-item-label>
                  </div>
               </div>
               <q-input filled type="number" min="1" v-model="form.order_expired_time" label="Order Expired Time"
                  suffix="JAM" />

            </q-card-section>
         </q-card>
         <q-card flat class="section q-mt-md">
            <q-card-section>
               <div class="card-subtitle flex justify-between items-start">
                  <div>
                     <q-item-label>Service Fee</q-item-label>
                     <q-item-label caption>
                        Biaya tambahan penggunaan layanan, akan ditambahkan pada pesanan
                        pelanggan
                     </q-item-label>
                  </div>
                  <q-toggle v-model="form.is_service_fee" :label="form.is_service_fee ? 'Active' : 'Disabled'"
                     left-label color="teal" class="text-grey-8"></q-toggle>
               </div>
               <div>

                  <q-input class="q-mb-sm" filled v-model="form.service_fee_label" required label="Label" />
                  <q-input filled v-model="form.service_fee" type="number" required min="0" label="Biaya Layanan" />
               </div>
            </q-card-section>
         </q-card>

         <q-card flat class="section q-mt-md">
            <q-card-section>
               <div class="card-subtitle">Pengaturan Checkout</div>
               <q-list class="q-mt-md">
                  <q-item class="q-px-xs">
                     <q-item-section>
                        <q-item-label>Whatsapp Checkout</q-item-label>
                        <q-item-label caption>Memungkinan user untuk bisa checkout langsung via whatsapp (
                           Order
                           tidak
                           tersimpan di
                           database )</q-item-label>
                     </q-item-section>
                     <q-item-section side>
                        <q-toggle v-model="form.is_whatsapp_checkout"
                           :label="form.is_whatsapp_checkout ? 'Active' : 'Disabled'" left-label
                           color="teal"></q-toggle>
                     </q-item-section>
                  </q-item>
                  <q-item class="q-px-xs">
                     <q-item-section>
                        <q-item-label>Guest Checkout</q-item-label>
                        <q-item-label caption>Memunginkan checkout tanpa harus login / register</q-item-label>
                     </q-item-section>
                     <q-item-section side>
                        <q-toggle v-model="form.is_guest_checkout"
                           :label="form.is_guest_checkout ? 'Active' : 'Disabled'" left-label color="teal"></q-toggle>
                     </q-item-section>
                  </q-item>
               </q-list>

            </q-card-section>
         </q-card>
         <div class="q-pt-lg">
            <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
         </div>
      </form>


   </div>
</template>

<script>
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         form: {
            order_expired_time: 48,
            is_service_fee: false,
            service_fee: 0,
            service_fee_label: "",
            is_whatsapp_checkout: false,
            is_guest_checkout: true
         },
      }
   },
   computed: {
      config: function () {
         return this.$store.state.config
      }
   },
   mounted() {
      if (this.config) {
         this.setConfig()
      }
   },
   methods: {
      setConfig() {
         if (this.config) {
            this.form.order_expired_time = this.config.order_expired_time
            this.form.is_service_fee = this.config.is_service_fee;
            this.form.service_fee = this.config.service_fee;
            this.form.service_fee_label = this.config.service_fee_label;
            this.form.is_whatsapp_checkout = this.config.is_whatsapp_checkout
            this.form.is_guest_checkout = this.config.is_guest_checkout
         }

      },
      submitOrderConfig() {
         if (this.order_expired_time <= 0) return
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
         })
      },
      updateDate() {
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
         })
      },
   }
}
</script>