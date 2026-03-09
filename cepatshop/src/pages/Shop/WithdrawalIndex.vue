<template>
   <q-page padding>
      <AppHeader title="Withdrawal"></AppHeader>
      <div class="box-column flat">
         <div>
            <div class="card-subtitle flex justify-between items-center">
               <div>Daftar Penarikan</div>
               <q-select style="min-width:125px" filled dense :options="['ALL', 'PENDING', 'SUCCESS', 'CANCELED']"
                  v-model="queryParams.filter" label="Status"></q-select>
            </div>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Account</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, index) in withdrawal.data" :key="index">
                        <td>{{ withdrawal.from + index }}</td>
                        <td>
                           <q-item-label>{{ dateFormat(item.created_at) }}</q-item-label>
                        </td>
                        <td>{{ item.ref_code }}</td>
                        <td>{{ item.user.name }}</td>
                        <td>{{ item.target_account }}</td>
                        <td>
                           <q-item-label>{{ moneyIdr(item.amount) }}</q-item-label>
                        </td>
                        <td>
                           <div>
                              <q-badge :color="getColorBadge(item.status)">{{ item.status }}</q-badge>

                           </div>
                        </td>
                        <td>
                           <q-btn icon="more_vert" unelevated round>
                              <q-menu auto-close>
                                 <q-list>
                                    <q-item clickable @click="openDetail(item)">
                                       <q-item-section side>
                                          <q-icon name="open_in_new"></q-icon>
                                       </q-item-section>
                                       <q-item-section>Detail</q-item-section>
                                    </q-item>
                                    <q-item clickable v-if="item.can_process" @click="openProcess(item)">
                                       <q-item-section side>
                                          <q-icon name="settings"></q-icon>
                                       </q-item-section>
                                       <q-item-section>Proses</q-item-section>
                                    </q-item>
                                    <q-item clickable v-if="item.can_abort" @click="openAbort(item)">
                                       <q-item-section side>
                                          <q-icon name="close"></q-icon>
                                       </q-item-section>
                                       <q-item-section>Batalkan</q-item-section>
                                    </q-item>
                                 </q-list>
                              </q-menu>
                           </q-btn>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div v-if="!withdrawal.total && !loading" class="text-center q-pa-sm">Tidak ada Data</div>
          <SimplePagination v-bind="withdrawal" @loadUrl="loadData"></SimplePagination>
         </div>
      </div>
      <q-dialog v-model="evidenceModal">
         <template v-if="evidence">
            <img :src="evidence.src" style="max-height:60%" />
         </template>
      </q-dialog>
      <q-dialog v-model="settingModal" persistent>
         <q-card class="card-md">
            <q-card-section>
               <div class="card-title flex justify-between">
                  <div>{{ is_process ? 'Process Data' : 'Detail' }}</div>
                  <q-btn round flat icon="close" v-close-popup dense></q-btn>
               </div>
               <form @submit.prevent="processWithdraw">
                  <div v-if="temp_data">
                     <q-list separator>
                        <q-item class="q-px-none">
                           <q-item-section>Amount</q-item-section>
                           <q-item-section class="text-weight-bold">{{ moneyIdr(temp_data.amount) }}</q-item-section>
                        </q-item>
                        <q-item class="q-px-none">
                           <q-item-section>Vendor</q-item-section>
                           <q-item-section class="text-weight-bold">{{ temp_data.target_vendor }}</q-item-section>
                        </q-item>
                        <q-item class="q-px-none">
                           <q-item-section>Account Number</q-item-section>
                           <q-item-section class="text-weight-bold">{{ temp_data.target_number }}</q-item-section>
                        </q-item>
                        <q-item class="q-px-none">
                           <q-item-section>Account Name</q-item-section>
                           <q-item-section class="text-weight-bold">{{ temp_data.target_account }}</q-item-section>
                        </q-item>
                        <q-item class="q-px-none">
                           <q-item-section>Status</q-item-section>
                           <q-item-section class="text-weight-bold">{{ temp_data.status }}</q-item-section>
                        </q-item>
                        <q-item v-if="temp_data.reason">
                           <q-item-section top>Reason</q-item-section>
                           <q-item-section>
                              <q-item-label>{{ temp_data.reason }}</q-item-label>
                           </q-item-section>
                        </q-item>
                        <q-item v-if="temp_data.evidence">
                           <q-item-section top>Bukti Pembayaran</q-item-section>
                           <q-item-section>
                              <img :src="temp_data.evidence.src" style="width:50px;height:auto"
                                 class="thumbnail cursor-pointer" @click="openEvidence(temp_data.evidence.src)" />
                           </q-item-section>
                        </q-item>
                     </q-list>
                  </div>
                  <div class="q-mt-md">
                     <div v-if="is_process">
                        <div class="label q-mb-xs">Upload Bukti Pembayaran</div>
                        <input type="file" @input="handleUpdateImage" ref="image" required />
                        <div>
                           <img v-if="imagePreview" :src="imagePreview" style="width:100px;height:auto" class="q-mt-md" />
                        </div>
                     </div>
                     <div class="card-action" v-if="is_process">
                        <q-btn :disable="loading" label="Submit" type="submit" color="primary" unelevated></q-btn>
                        <q-btn :disable="loading" label="Cancel" v-close-popup color="primary" outline></q-btn>
                     </div>
                  </div>
               </form>
            </q-card-section>
         </q-card>
      </q-dialog>
      <q-dialog v-model="evidenceModal">
         <img :src="imagePreview" v-if="imagePreview" style="max-height:100%" />
      </q-dialog>
   </q-page>
</template>

<script>
import { BaseApi } from 'boot/axios'
import { dateFormat } from 'src/utils';
export default {
   data() {
      return {
         temp_data: '',
         evidenceModal: false,
         evidence: null,
         settingModal: false,
         withdrawal: {
            data: [],
            next_page_url: null,
            prev_page_url: null,
            total: 0,
            current_page: 1
         },
         form: {
            image: ''
         },
         imagePreview: '',
         is_process: false,
         per_page: 3,
         queryParams: {
            filter: 'ALL',
            per_page: 10,
            page: ''
         }
      }
   },
   computed: {
      loading() {
         return this.$store.state.loading
      }
   },
   mounted() {
      this.getData()
   },
   watch: {
      'queryParams.filter'(val) {
         this.queryParams.page = ''
         this.queryParams.filter = val
         this.getData()
      },
      'queryParams.per_page'(val) {
         this.queryParams.page = ''
         this.queryParams.per_page = val
         this.getData()
      }
   },
   methods: {
      handleInputPerpage(e) {
         let val = e.target.value
         this.per_page = val
         this.getData()
      },
      openAbort(item) {
         this.$q.dialog({
            title: 'Alasan Pembatalan',
            message: 'Input alasan pembatalan withdrawal*',
            prompt: {
               model: '',
               isValid: val => val.length > 5, // << here is the magic
               type: 'text' // optional
            },
            cancel: true,
            ok: { label: 'Submit', flat: true },
            persistent: true
         }).onOk(data => {
            this.abortData({
               id: item.id,
               reason: data
            })
         })
      },
      abortData(data) {
         BaseApi.post('withdrawal-abort/' + data.id, { reason: data.reason }).then(res => {
            if (res.status == 200) {
               this.getData()
            }
         })
      },
      updateItem(data) {
         const idx = this.withdrawal.data.findIndex(el => el.id == data.id)
         this.withdrawal.data[idx] = data
      },
      processWithdraw() {
         this.$store.commit('SET_LOADING', true)
         const form = new FormData()

         form.append('image', this.form.image)

         BaseApi.post('withdraw-process/' + this.temp_data.id, form, { headers: { 'content-Type': 'multipart/formData' } })
            .then(res => {
               if (res.status == 200) {
                  this.updateItem(res.data.data)
                  this.settingModal = false
               }
            }).finally(() => this.$store.commit('SET_LOADING', false))
      },
      openDetail(data) {
         this.imagePreview = data.evidence ? data.evidence.src : ''
         this.is_process = false
         this.temp_data = data
         this.settingModal = true
      },
      handleUpdateImage(evt) {
         const file = this.$refs.image.files[0]

         if (!file) return

         this.form.image = file

         const reader = new FileReader();

         reader.onload = (e) => {
            this.imagePreview = e.target.result;
         };

         reader.readAsDataURL(file);
      },
      handleUploadImage(evt) {
         this.$refs.image.value = ''
         this.$refs.image.click()

      },
      openProcess(data) {
         this.form.image = ''
         this.imagePreview = ''
         this.is_process = true
         this.temp_data = data
         this.settingModal = true
      },
      openEvidence(src) {
         this.imagePreview = src
         this.evidenceModal = true
      },
      openDialog(title, msg) {
         this.$q.dialog({
            title: title,
            message: msg
         })
      },
      getData() {
         this.$store.commit('SET_LOADING', true)
         let url = 'withdrawal'

         let params = {}

         Object.keys(this.queryParams).map(key => {
            if (this.queryParams[key] && this.queryParams[key] != 'ALL') {
               params[key] = this.queryParams[key]
            }
         })

         url += `?${new URLSearchParams(params).toString()}`

         BaseApi.get(url).then(res => {
            if (res.status == 200) {
               this.withdrawal = res.data.data
            }
         }).finally(() => this.$store.commit('SET_LOADING', false))
      },
      loadData(url = null) {

         setTimeout(() => {
            this.getData(url)
         }, 300)
      }
   }
}
</script>
