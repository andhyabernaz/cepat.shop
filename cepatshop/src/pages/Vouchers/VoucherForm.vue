<template>
   <q-page padding>
      <AppHeader title="Voucher Form" goBack>

      </AppHeader>

      <q-form @submit.prevent="submitData">

         <q-card class="section shadow">
            <q-card-section>

               <div class="">
                  <div class="flex no-wrap q-gutter-sm q-mb-md" v-if=!form.is_type_shipping>
                     <q-card flat bordered :class="{ 'card-active': form.discount_type == 'nominal' }"
                        class="cursor-pointer card-select" @click="form.discount_type = 'nominal'">
                        <q-card-section class="q-pa-sm">
                           <q-list>
                              <q-item>
                                 <q-item-section side>
                                    <q-icon color="amber-9" name="attach_money" size=md></q-icon>
                                 </q-item-section>
                                 <q-item-section>
                                    <div class="text-md">Nominal Diskon</div>

                                 </q-item-section>
                              </q-item>
                           </q-list>
                        </q-card-section>
                     </q-card>
                     <q-card flat bordered :class="{ 'card-active': form.discount_type == 'percent' }"
                        class="cursor-pointer card-select" @click="form.discount_type = 'percent'">
                        <q-card-section class="q-pa-sm">
                           <q-list>
                              <q-item>
                                 <q-item-section side>
                                    <q-icon color="teal" name="percent" size=md></q-icon>
                                 </q-item-section>
                                 <q-item-section>
                                    <div class="text-md">Persentase Diskon</div>

                                 </q-item-section>
                              </q-item>
                           </q-list>
                        </q-card-section>
                     </q-card>
                  </div>
                  <q-input class="q-mb-md" stack-label filled label="Nama Diskon ( Tidak ditampikan ke pembeli )"
                     v-model="form.name" required></q-input>
                  <q-input class=" q-mb-md" stack-label filled type="number" min="0" label="Minimum Belanja"
                     v-model="form.min_transaction" required
                     hint="Minimum belanja agar dapat menggunakan voucher ini, set 0 tanpa minimum belanja"></q-input>
                  <q-input class="q-mb-md" stack-label filled v-if="form.discount_type == 'percent'" type="number"
                     min="1" max="99" label="Persentase Diskon" suffix="%" v-model="form.discount_amount"
                     required></q-input>

                  <q-input class="q-mb-md" stack-label filled v-if="form.discount_type == 'percent'" type="number"
                     min="0" label="Maksimum Diskon" v-model="form.max_discount_amount" required
                     hint="Nilai maksiumum diskon yang akan diberikan, 0 tanpa batas"></q-input>

                  <q-input class="q-mb-md" stack-label filled v-if="form.discount_type == 'nominal'" type="number"
                     min="1" label="Nominal Diskon" v-model="form.discount_amount" required></q-input>

                  <q-input class="q-mb-md" stack-label filled label="Waktu Mulai" v-model="form.start_date" readonly
                     required>
                     <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-date v-model="form.start_date" mask="YYYY-MM-DD HH:mm">
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-date>
                           </q-popup-proxy>
                        </q-icon>
                        <q-icon name="access_time" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-time v-model="form.start_date" mask="YYYY-MM-DD HH:mm" format24h>
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-time>
                           </q-popup-proxy>
                        </q-icon>
                     </template>
                  </q-input>
                  <q-input class="q-mb-md" stack-label filled label="Waktu Berakhir" v-model="form.end_date" readonly
                     required>
                     <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-date v-model="form.end_date" mask="YYYY-MM-DD HH:mm">
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-date>
                           </q-popup-proxy>
                        </q-icon>
                        <q-icon name="access_time" class="cursor-pointer">
                           <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                              <q-time v-model="form.end_date" mask="YYYY-MM-DD HH:mm" format24h>
                                 <div class="row items-center justify-end">
                                    <q-btn v-close-popup label="Close" color="primary" flat />
                                 </div>
                              </q-time>
                           </q-popup-proxy>
                        </q-icon>
                     </template>
                  </q-input>


                  <q-input min="0" stack-label filled label="Kuota Pemakaian" v-model="form.usage_quota" required
                     hint="Kuota seluruh pemakaian voucher, 0 untuk tanpa batas"></q-input>

                  <q-input class="q-mt-sm" stack-label filled :loading="is_loading" label="Kode Voucher"
                     v-model="form.voucher_code" required :rules="[validationLength, validationExists]"></q-input>
               </div>
            </q-card-section>
         </q-card>


         <div class="card-action">
            <q-btn label="Submit Data" class="full-width" type="submit" color="primary"></q-btn>
         </div>

      </q-form>

   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   data() {
      return {
         current_voucher: null,
         discount_options: [
            { label: 'Nominal', value: 'nominal' },
            { label: 'Persent', value: 'percent' },
         ],
         form: {
            name: '',
            voucher_code: '',
            start_date: '',
            end_date: '',
            discount_type: 'nominal',
            discount_amount: 0,
            max_discount_amount: 0,
            min_transaction: 0,
            usage_quota: 0,
            is_type_shipping: false,
         },
         is_loading: false,
         timeout: null
      }
   },
   mounted() {
      if (this.$route.name == 'VoucherEdit') {
         this.getData()
      }
      if (this.$route.query.is_type_shipping) {
         this.form.is_type_shipping = true
      }
      this.getVoucherCode()
   },
   watch: {
      'form.voucher_code'(val) {
         this.form.voucher_code = this.replaceString(val)
      },
   },
   methods: {
      getData() {
         let path = `vouchers/${this.$route.params.voucher_id}`;

         BaseApi.get(path).then(res => {
            this.current_voucher = res.data.data
            this.form = { ...res.data.data }

         })
      },
      getVoucherCode() {
         BaseApi.get('vouchers/generate').then(res => {
            this.form.voucher_code = res.data.data.code
         })
      },
      changeDiscountType(val, old) {
         if (val != old) {
            this.form.discount_amount = 0
         }
      },
      requiredRule(val) {
         if (val.length > 0) {
            return true
         }
         return 'Bidang tidak boleh kosong'
      },
      validationLength(val) {
         if (val.length > 4) {
            return true
         }
         return 'Kode terlalu pendek, minimal 5 karakter'
      },
      validationExists(val) {
         if (this.current_voucher) {
            if (this.current_voucher.voucher_code == val) {
               return true
            }
         }
         return new Promise((resolve, reject) => {
            this.is_loading = true
            clearTimeout(this.timeout)
            this.timeout = setTimeout(() => {
               BaseApi.get('vouchers/checkCode/' + val).then(res => {
                  let voucher_count = res.data.data.voucher_count
                  if (voucher_count > 0) {
                     resolve('Kode voucher exists')
                  } else {
                     resolve(true)
                  }
               }).finally(() => this.is_loading = false)
            }, 500)
         })
      },
      submitData() {
         this.$q.loading.show()
         if (this.$route.name == 'VoucherCreate') {

            BaseApi.post('vouchers', this.form).then(res => {
               if (res.status == 200) {
                  this.$router.push({ name: 'VoucherIndex' })
               }
            }).finally(() => {
               this.$q.loading.hide()
            })
         }
         if (this.$route.name == 'VoucherEdit') {

            BaseApi.put('vouchers/' + this.form.id, this.form).then(res => {
               if (res.status == 200) {
                  this.$router.push({ name: 'VoucherIndex' })
               }
            }).finally(() => {
               this.$q.loading.hide()
            })
         }
      }
   }
}
</script>