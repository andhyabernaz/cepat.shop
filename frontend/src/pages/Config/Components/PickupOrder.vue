<template>
   <div>
      <form @submit.prevent="updateData">
         <q-card class="section shadow">
            <q-card-section>
               <div class="card-subtitle">Pengaturan Ambil ditoko</div>
               <q-list>

                  <q-item>
                     <q-item-section side>
                        <q-toggle class="text-grey-8" color="teal" v-model="form.is_order_pickup">
                        </q-toggle>

                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Ambil ditoko</q-item-label>
                        <q-item-label caption>Opsi pengambilan pesanan ditoko oleh pelanggan</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section side>
                        <q-toggle :disable="form.is_order_pickup == false" class=" text-grey-8" color="teal"
                           v-model="form.is_cash_payment">
                        </q-toggle>

                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Bayar cash ditoko</q-item-label>
                        <q-item-label caption>Opsi pembayaran pesanan ditoko</q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
               <div class="q-pt-lg">
                  <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
               </div>
               <div class="q-pt-md">
                  <ul v-if="is_payment_warning">
                     <li v-if="is_payment_warning" class="text-caption text-yellow-10">Pengaturan belum lengkap,
                        tambahkan metode
                        Bayar cash ditoko /
                        Akun Bank /
                        Payment Gateway agar dapat digunakan </li>
                  </ul>
               </div>
            </q-card-section>

         </q-card>
      </form>
   </div>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   data() {
      return {
         form: {
            is_order_pickup: false,
            is_cash_payment: false
         },
      };
   },
   computed: {
      config: function () {
         return this.$store.state.config;
      },
      is_payment_warning() {
         if (this.config) {
            if (this.form.is_order_pickup) {
               if (!this.config.is_bank_ready && !this.config.is_tripay_ready && !this.form.is_cash_payment) {

                  return true
               }
            }
         }
         return false
      }
   },
   mounted() {
      if (!this.config) {
         this.getAdminConfig();
      } else {
         this.setConfig();
      }
   },
   watch: {
      'form.is_order_pickup'(val) {
         if (val == false) {
            this.form.is_cash_payment = false
         }
      },
   },
   methods: {
      setConfig() {
         this.form.is_order_pickup = this.config.is_order_pickup;
         this.form.is_cash_payment = this.config.is_cash_payment;
      },
      updateData() {
         BaseApi.post("config", this.form)
            .then(() => {
               this.$store.dispatch("getAdminConfig");
               this.$q.notify({
                  type: "positive",
                  message: "Berhasil memperbarui data",
               });
            })
            .catch(() => {
               this.$q.notify({
                  type: "negative",
                  message: "Gagal memperbarui data",
               });
            });
      },
   },
};
</script>
