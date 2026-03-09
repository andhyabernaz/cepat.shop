<template>
   <q-page padding>
      <AppHeader title="Program Affiliasi">
      </AppHeader>
      <div v-if="affiliate">
         <div v-if="affiliate.is_active">
            <AffiliateMenu />
            <div class="box-column flat">
               <div>
                  <div class="card-subtitle flex justify-between">
                     <div>Detail Affiliasi</div>
                     <div class="q-gutter-sm">
                        <q-btn @click="witdrawModal = true" label="Tarik Dana" color="teal" icon="add_card"></q-btn>
                        <!-- <q-btn color="teal" @click="openCouponModal">Edit Kode</q-btn> -->
                     </div>
                  </div>
                  <div class="table-responsive">
                     <table class="table bordered aligned">
                        <tbody v-if="affiliate" separator>
                           <tr>
                              <td>Saldo Affiliasi</td>
                              <td class="text-weight-bold text-green text-md">{{ moneyIdr(user.affiliate_saldo) }}</td>
                              <td>
                              </td>
                           </tr>
                           <tr>
                              <td>Kode Affiliasi</td>
                              <td>
                                 {{ affiliate.code }}
                              </td>
                              <td>
                                 <div>
                                    <q-btn class="btn-action" flat color="teal" @click="openCouponModal">Edit
                                       Kode</q-btn>
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <td>Status Affiliasi</td>
                              <td> <span>
                                    <q-badge>{{ affiliate.status_label }}</q-badge>
                                 </span></td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Tgl Terdaftar</td>
                              <td>
                                 {{ dateFormat(affiliate.created_at) }}
                              </td>
                              <td></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <div class="box-column flat q-mt-md">

               <div>
                  <div class="card-subtitle flex justify-between items-center">
                     <div>Transaksi</div>
                     <q-btn :to="{ name: 'CustomerMutasiSaldo', query: { category: 'Affiliate' } }" label="Selengkapnya"
                        flat dense no-caps></q-btn>
                  </div>
                  <MutasisaldoTable :data="mutasiSaldo"></MutasisaldoTable>
                  <div class="text-center q-py-lg text-grey-6" v-if="!mutasiSaldo.total">Tidak ada data</div>
                  <SimplePagination autoHide v-bind="mutasiSaldo" @loadUrl="getMutasiSaldo" />

               </div>
            </div>
            <q-dialog v-model="couponModal" persistent>
               <q-card class="card-lg">
                  <q-card-section v-if="affiliate">
                     <div class="card-title">Affiliate Code</div>
                     <div class="q-pa-sm bg-yellow-2 q-mb-md" v-if="affiliate.code">
                        <div class="text-weight-bold text-yellow-10">WARNING!</div> Kode anda sudah aktif, Jika anda
                        mengubahnya maka kode sebelumnya tidak berlaku lagi.
                     </div>
                     <q-form @submit.prevent="submitData">
                        <!-- <div class="label q-py-xs">Affiliate Code</div> -->
                        <q-input outlined v-model="form.code" hint="Minimal 6 Karakter" :debounce="700"
                           @update:model-value="handleCheckAvailable" stack-label :error="is_exist"
                           error-message="Kode sudah terdaftar silahkan ganti dengan yang lain"
                           :loading="is_checked_loading"></q-input>
                        <!-- <div class="q-pa-xs text-xs" v-if="is_message" :class="is_exist ? 'text-red' : 'text-green-9'">{{ is_exist ? 'Kode Kupon sudah terdafar' : 'Kode Kupon tersedia' }}</div> -->
                        <div class="q-mt-lg q-gutter-x-sm">
                           <q-btn label="Submit" type="submit" color="primary" :disable="!is_ready"></q-btn>
                           <q-btn label="Batal" outline color="primary" v-close-popup></q-btn>
                        </div>
                     </q-form>

                  </q-card-section>
               </q-card>
            </q-dialog>

            <q-dialog v-model="witdrawModal" persistent>
               <q-card class="card-lg">
                  <q-card-section>
                     <div class="card-title flex justify-between">
                        <div>Penarikan Dana</div>
                        <q-btn v-if="saldoConfig && saldoConfig.withdraw_description" icon="help_center" dense round
                           color="info" unelevated flat size="17px"
                           @click="openDialog(saldoConfig.withdraw_description)">
                           <q-tooltip>Panduan Penarikan</q-tooltip>
                        </q-btn>
                     </div>
                     <form @submit.prevent="submitWithdrawal">
                        <div class="row">
                           <div class="col q-pa-sm">
                              <div class="text-label">Nominal</div>
                              <money-formatter v-model="form_withdraw.amount" outlined required prefix="Rp"
                                 label="Nominal" :min="minimumWithdraw" />

                              <ul class="q-mt-sm text-grey-7" v-if="saldoConfig">
                                 <li>Minimum Penarikan {{ moneyIdr(minimumWithdraw) }}</li>
                                 <li>Biaya Penarikan {{ moneyIdr(this.saldoConfig.withdraw_fee) }}</li>
                              </ul>


                              <div v-if="saldoConfig" class="q-mt-md">
                                 <q-select outlined required class="q-mt-md" :options="saldoConfig.withdraw_channels"
                                    label="Tujuan Withdraw" v-model="form_withdraw.target_vendor"></q-select>
                                 <q-input outlined required class="q-mt-md" v-model="form_withdraw.target_number"
                                    label="Nomor Akun" />
                                 <q-input outlined required class="q-mt-md" v-model="form_withdraw.target_account"
                                    label="Nama Pemilik Akun" />
                                 <q-input type="textarea" outlined class="q-mt-md" rows="2" v-model="form_withdraw.note"
                                    label="Catatan (opsional)" />
                              </div>

                           </div>

                        </div>
                        <div class="flex q-mt-md q-gutter-x-sm">
                           <q-btn :disable="loading" label="Submit" color="primary" unelevated type="submit"></q-btn>
                           <q-btn :disable="loading" label="Batal" v-close-popup color="primary" outline></q-btn>
                        </div>
                     </form>
                  </q-card-section>
               </q-card>
            </q-dialog>
         </div>
         <div class="box-column flat" v-if="affiliate.is_inactive">
            <div v-html="affiliate_config.welcome_message"></div>
         </div>
         <div class="box-column flat" v-if="affiliate.is_suspended">
          <div v-html="affiliate_config.suspend_message"></div>
         </div>
      </div>
   </q-page>
</template>

<script>
import { Api, BaseApi } from "boot/axios";
import AffiliateMenu from "./AffiliateMenu.vue";
// import ProductAffiliate from './ProductAffiliate.vue'
import MutasisaldoTable from 'components/MutasisaldoTable.vue'
import { dateFormat, moneyFormat } from "src/utils";
import { Loading } from "quasar";
export default {
   name: "AffiliateIndex",
   components: { AffiliateMenu, MutasisaldoTable },
   data() {
      return {
         tab1: 'Link',
         couponModal: false,
         is_checked_loading: false,
         is_exist: false,
         is_ready: false,
         is_message: false,
         saldoConfig: null,
         form: {
            code: ""
         },
         witdrawModal: false,
         form_withdraw: {
            amount: 0,
            target_account: '',
            target_number: '',
            target_vendor: '',
            note: ''
         },
         timeout: null,
         mutasiSaldo: {
            data: [],
            total: 0,
            from: 1,
         }
      };
   },
   watch: {
      'form.code'(val) {
         if (val) {
            this.form.code = val.replace(/[^a-zA-Z0-9]/g, '');
         }
      }
   },
   created() {
      if (!this.affiliate) {
         this.getData();
      }

      this.getMutasiSaldo()
      this.$store.dispatch('user/getUser')

      this.getSaldoConfig()
   },
   mounted() {
      setTimeout(() => {
         if (this.affiliate_config && !this.affiliate_config.is_active) {
            this.$router.back()
         }
      }, 1000)
   },
   computed: {
      minimumWithdraw() {
         if (this.saldoConfig) {
            return parseInt(this.saldoConfig.min_withdraw_amount)
         }
         return 10000
      },
      loading() {
         return this.$store.state.loading
      },
      affiliate() {
         return this.$store.state.affiliate.affiliate
      },
      affiliate_config() {
         return this.$store.state.affiliate_config
      },
      site_has_affiliate() {
         if (this.affiliate_config && !this.affiliate_config.is_active) {
            return false
         }
         return true
      },
      user() {
         return this.$store.state.user.user
      }
   },
   methods: {
      getMutasiSaldo(url = null) {
         if (!url) {
            url = 'customer/mutasi-saldos?category=Affiliate&per_page=6'
         }
         BaseApi.get(url).then(res => {
            this.mutasiSaldo = { ...res.data.data }
         })
      },
      getData() {
         this.$store.commit('SET_LOADING', true)
         BaseApi.get("affiliate").then(res => {
            if (res.data.data == null) {
               return this.$router.push({ name: 'CustomerAffiliateRegister' })
            }
            this.$store.commit('affiliate/SET_AFFILIATE', res.data.data)
            setTimeout(() => {
               this.form.code = this.affiliate.code ?? ''
            }, 1000)

         })
      },
      submitData() {
         BaseApi.put("affiliate/" + this.affiliate.id, this.form).then(res => {
            this.$store.commit('affiliate/SET_AFFILIATE', res.data.data);
            this.couponModal = false
         });
      },
      openCouponModal() {
         this.is_exist = false
         this.form.code = this.affiliate.code ?? ''
         this.couponModal = true
      },
      handleCheckAvailable() {
         if (this.affiliate.code == this.form.code) return
         this.is_message = false;
         this.is_ready = false;

         if (this.form.code.length <= 5) {
            return;
         }

         clearTimeout(this.timeout)

         this.timeout = setTimeout(() => {

            this.is_checked_loading = true

            BaseApi.get("affiliate/check/" + this.form.code).then(res => {
               this.is_exist = res.data.is_exist;
               this.is_message = true;
               if (this.is_exist == false) {
                  this.is_ready = true;
               }
            }).finally(() => this.is_checked_loading = false)
         }, 1000)
      },
      getSaldoConfig() {
         Api.get('getSaldoConfig').then(res => {
            this.saldoConfig = res.data.data
         })
      },
      submitWithdrawal() {

         let nominal = parseInt(this.form_withdraw.amount)
         let userSaldo = parseInt(this.user.affiliate_saldo)
         let fee = parseInt(this.saldoConfig.withdraw_fee)
         let max = fee + userSaldo;

         if (nominal > max) {
            let message = `Gagal! Saldo tidak cukup, sisa saldo anda  ${moneyFormat(userSaldo)}`
            if (fee > 0) {
               message += `, + biaya penarikan ${moneyFormat(fee)}`
            }
            this.$q.notify({
               type: 'negative',
               message: message,
               timeout: 5000,
            })

            return
         }

         if (nominal < parseInt(this.saldoConfig.min_withdraw_amount)) {
            this.$q.notify({
               type: 'negative',
               message: `Minimum penarikan saldo ${moneyFormat(this.saldoConfig.min_withdraw_amount)}`,
            })
            return
         }

         if (parseInt(this.saldoConfig.max_withdraw_amount) > 0
            && nominal > parseInt(this.saldoConfig.max_withdraw_amount)) {
            this.$q.notify({
               type: 'negative',
               message: `Maksimum penarikan saldo ${moneyFormat(this.saldoConfig.max_withdraw_amount)}`
            })
            return
         }

         this.$store.commit('SET_LOADING', true)

         BaseApi.post('withdrawal', this.form_withdraw).then(res => {
            if (res.status == 200) {
               this.witdrawModal = false
               this.$store.dispatch('user/getUser')
               this.getMutasiSaldo()

               this.$q.notify({
                  type: 'positive',
                  message: `Permintaan withdrawal berhasil, proses pencairan akan memerlukan waktu 2x24jam `
               })
            }
         })

      },
      openDialog(msg) {
         this.$q.dialog({
            message: msg,
            html: true
         })
      }
   },
}
</script>
