<template>
   <div>
      <q-dialog v-model="addressModal" persistent no-shake :position="user_address.length ? 'bottom' : 'standard'">
         <q-card class="max-width-mobile">
            <div>
               <div class="card-header flex justify-between items-center sticky-top">
                  <div>Pilih Alamat</div>
                  <q-btn flat icon="close" v-close-popup></q-btn>
               </div>
               <div class="bg-grey-2 q-pa-md column" style="min-height:300px">

                  <div v-if="user_address.length">
                     <q-card v-for="(item, idx) in user_address" :key="idx" class="shadow q-mb-sm card-address" bordered
                        square
                        :class="{ 'active': cart_order_form.customer && cart_order_form.customer.id == item.id }">
                        <q-card-section>
                           <q-list>
                              <q-item clickable @click="selectAddress(item)" class="q-pt-md">
                                 <q-item-section class="text-grey-8">
                                    <q-item-label class="row items-center q-gutter-x-sm q-pb-sm">
                                       <div class="text-weight-bold text-dark text-uppercase">{{ item.title }} </div>
                                       <q-badge color="green" v-if="item.is_primary" label="Utama"></q-badge>
                                    </q-item-label>
                                    <q-item-label class="text-weight-bold text-md">{{ item.receiver_name
                                       }}</q-item-label>
                                    <q-item-label>{{ item.receiver_phone }}</q-item-label>
                                    <q-item-label>{{ item.full_address }}</q-item-label>
                                    <q-item-label v-if="!item.is_complete"> <q-badge color="red-4"
                                          outline>incomplete</q-badge></q-item-label>
                                 </q-item-section>

                              </q-item>
                           </q-list>
                           <div class="flex q-pa-sm q-gutter-x-sm">
                              <q-btn class="btn-action" outline unelevated color="teal" no-caps label="Pilih Alamat"
                                 @click="selectAddress(item)"></q-btn>
                              <q-btn class="btn-action" outline unelevated color="blue" no-caps label="Edit"
                                 @click="handleEditAddress(item)"></q-btn>
                              <q-btn class="btn-action" outline unelevated color="red" no-caps label="Hapus"
                                 @click="handleDeleteAddress(item.id)"></q-btn>
                           </div>
                        </q-card-section>
                     </q-card>
                  </div>
                  <div class="text-center text-grey-7 margin-auto" v-else>
                     <div class="q-pb-sm">Belum ada alamat tersimpan</div>
                     <q-btn icon="add" no-caps label="Tambah Alamat" color="primary" @click="handleAddAddress"></q-btn>
                  </div>
               </div>
               <div class="q-pa-md sticky-bottom bg-white" v-if="user_address.length">
                  <q-btn label="Tambah Alamat" class="full-width" color="primary" @click="handleAddAddress"></q-btn>
               </div>
            </div>

         </q-card>
      </q-dialog>
      <q-dialog v-model="formAddressModal" persistent no-shake position="bottom">
         <q-card class="max-width-mobile" flat>
            <q-card-section class="q-pa-none" style="min-height:320px">
               <div class="card-header flex justify-between q-pb-md sticky-top">
                  <div>{{ formAddress._method == 'PUT' ? 'Edit' : 'Tambah' }} Alamat</div>
                  <q-btn icon="close" flat v-close-popup></q-btn>
               </div>
               <form @submit.prevent="submitAddress" class="q-pa-md">
                  <div class="q-py-sm">
                     <div class="q-gutter-y-sm">
                        <q-input filled required label="Label Alamat" v-model="formAddress.title"
                           placeholder="eg: Rumah, Kantor"></q-input>
                        <q-input filled required v-model="formAddress.receiver_name" label="Nama Penerima"></q-input>
                        <q-input filled required v-model="formAddress.receiver_phone" label="No Telp" lazy-rules
                           :rules="[requiredRules, validPhoneRules]"></q-input>
                        <q-select required filled v-model="current_selected_address" use-input hide-selected fill-input
                           option-label="name" input-debounce="1000" label="Cari Kecamatan"
                           @update:model-value="selectCurrentAddress" :options="subdistrict_options" @filter="filterFn">
                           <template v-slot:option="scope">
                              <q-item v-bind="scope.itemProps">
                                 <q-item-section>
                                    <q-item-label>{{ scope.opt.name }}</q-item-label>
                                    <q-item-label caption>{{ scope.opt.country_name }}</q-item-label>
                                 </q-item-section>
                              </q-item>
                           </template>
                           <template v-slot:no-option>
                              <q-item>
                                 <q-item-section class="text-grey">
                                    No results
                                 </q-item-section>
                              </q-item>
                           </template>
                        </q-select>

                        <div>
                           <q-input filled required type="textarea" v-model="formAddress.address_street" rows="4"
                              label="Nama jalan / Rt / Rw / kelurahan / patokan"></q-input>
                           <div class="q-pa-sm q-mt-sm bg-grey-2" v-if="formAddress.subdistrict">
                              {{ formAddress.subdistrict }},
                              {{ formAddress.city }},
                              {{ formAddress.province }}
                           </div>
                        </div>


                        <div v-if="config" class="q-mt-md q-pa-sm">
                           <div class="card-subtitle">
                              <div>Titik Koordinat</div>
                              <q-item-label caption>
                                 Klik pada map atau geser posisi ikon marker untuk mendapatkan koordinat yang tepat
                              </q-item-label>
                           </div>
                           <div>
                              <q-select multiple class="q-mb-sm" filled readonly dense required
                                 v-model="formAddress.coordinate"
                                 label="Titik Koordinat (latitude, longitude)"></q-select>
                              <ClientMap ref="clientMap" :config="config" :coordinate="formAddress.coordinate"
                                 :is_client="true" @onEmitMap="onEmitMap" @onError="(m) => error_map = m" />
                              <div class="text-amber-10 q-pa-xs text-sm" v-if="error_map">{{ error_map }}</div>

                           </div>
                        </div>
                        <q-checkbox v-if="user_address.length" label="Gunakan sebagai alamat utama"
                           v-model="formAddress.is_primary"></q-checkbox>
                     </div>

                  </div>
                  <div class="card-action q-py-md sticky-bottom bg-white" style="z-index:1000">
                     <q-btn label="Simpan Alamat" class="full-width" color="primary" type="submit"></q-btn>
                  </div>
               </form>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { BaseApi } from 'boot/axios'
import ClientMap from 'components/ClientMap.vue'
export default {
   components: { ClientMap },
   props: {
      autoSelectModal: {
         type: Boolean,
         default: false
      }
   },
   data() {
      return {
         error_map: '',
         addressModal: false,
         formAddressModal: false,
         formAddress: {
            _method: '',
            vendor_id: '',
            id: '',
            title: '',
            is_primary: false,
            address_street: '',
            subdistrict_id: '',
            city_id: '',
            receiver_name: '',
            receiver_phone: '',
            receiver_email: '',
            subdistrict: '',
            city: '',
            province: '',
            postal_code: '',
            coordinate: []
         },
         subdistrict_options: [],
         current_selected_address: null,
      }
   },
   watch: {
      current_selected_address(val) {
         if (val) {
            this.formAddress.subdistrict = val.subdistrict
            this.formAddress.city = val.city
            this.formAddress.city_id = val.city_id
            this.formAddress.postal_code = val.postal_code
            this.formAddress.vendor_id = val.vendor_id
            this.formAddress.country_name = val.country_name
            this.formAddress.name = val.name
         } else {
            this.formAddress.subdistrict = ''
            this.formAddress.subdistrict_id = ''
            this.formAddress.city_id = ''
            this.formAddress.city = ''
            this.formAddress.province = ''
            this.formAddress.postal_code = ''
            this.formAddress.coordinate = []
         }
      }
   },
   mounted() {
      this.error_map = ''
      if (this.user_address.length) {
         this.selectPrimaryAddress()
      } else {
         this.getUserAddress()
      }
   },
   computed: {
      user_address() {
         return this.$store.state.user.address
      },
      user() {
         return this.$store.state.user.user
      },
      errors() {
         return this.$store.state.errors
      },
      cart_order_form() {
         return this.$store.getters['cart/getChartOrderForm']
      },
      config() {
         return this.$store.state.config
      }
   },
   methods: {
      onEmitMap(center) {
         this.formAddress.coordinate = center;
      },
      getUserAddress() {
         if (this.user) {

            this.$store.dispatch('user/getUserAddress').then(res => {
               if (res.status == 200) {
                  this.$store.commit('user/SET_USER_ADDRESS', res.data.data)

                  setTimeout(() => {
                     this.selectPrimaryAddress()
                  }, 500)
               }
            })
         }
      },
      selectPrimaryAddress() {
         let prim = this.user_address.find(e => e.is_primary == true && e.is_complete == true)

         if (prim != undefined) {
            this.selectAddress(prim)
         } else {
            this.selectAddress(this.user_address[0])
         }
      },
      selectAddress(item) {

         this.$store.commit('CLEAR_ERRORS')

         this.$emit('onSelectAddress', item)
         this.addressModal = false

      },
      selectCurrentAddress(item) {
         this.error_map = ''
         let search = `${item.subdistrict} ${item.city.replace('Kabupaten ', '')}`
         this.$refs.clientMap.autoSearch(search)
      },
      submitAddress() {

         if (this.user) {

            if (this.formAddress._method == 'PUT') {
               BaseApi.post('user-address/' + this.formAddress.id, this.formAddress).then(() => {
                  this.getUserAddress()
                  this.formAddressModal = false
               })
            } else {
               BaseApi.post('user-address', this.formAddress).then(() => {
                  this.getUserAddress()
                  this.formAddressModal = false
               })
            }
         } else {
            this.handleSavelocalAddress()

            setTimeout(() => {
               if (this.user_address.length == 1) {
                  this.selectAddress(this.user_address[0])
               }
            }, 500)
         }
      },
      handleSavelocalAddress() {
         if (!this.formAddress.id) {

            this.formAddress.id = this.getRandomString(29)
         }
         this.formAddress.is_complete = true

         const data = { ...this.formAddress }

         if (!data.destination_id) {
            data.destination_id = data.city_id

         }

         data.full_address = data.address_street + ' ' + data.name

         this.$store.commit('user/PUSH_ADDRESS', data)

         this.formAddressModal = false
      },
      handleOpenAddressModal() {

         if (this.user_address.length) {
            this.clearForm()
            this.addressModal = true
         } else {
            this.handleAddAddress()
         }
      },
      clearForm() {
         this.formAddress.id = ''
         this.formAddress.title = ''
         this.formAddress.is_primary = false
         this.formAddress.address_street = ''
         this.formAddress.subdistrict_id = ''
         this.formAddress.city_id = ''
         this.formAddress.receiver_name = ''
         this.formAddress.receiver_phone = ''
         this.formAddress.receiver_email = ''
         this.formAddress.subdistrict = ''
         this.formAddress.city = ''
         this.formAddress.province = ''
         this.formAddress.postal_code = ''
         this.formAddress.coordinate = []
      },
      handleAddAddress() {
         this.clearForm()
         this.formAddress._method = 'POST'
         this.current_selected_address = null
         this.formAddressModal = true
         this.addressModal = false
         if (this.user) {
            this.formAddress.receiver_name = this.user.name
            this.formAddress.receiver_phone = this.user.phone
         }

         if (!this.user_address.length) {
            this.formAddress.title = 'Rumah'
         }

         setTimeout(() => {
            this.$refs.clientMap.getCurrentLocation()
         }, 3000)
      },

      handleEditAddress(item) {
         this.clearForm()
         const data = { ...item }
         this.formAddress = { ...this.formAddress, ...data }
         this.subdistrict_options = [data]
         this.current_selected_address = data
         this.formAddress._method = 'PUT'
         this.formAddressModal = true
      },
      async filterFn(val, update, abort) {

         if (val.length >= 3) {

            let response = await this.$store.dispatch('searchAddress', val)

            if (response.status == 200) {
               update(() => {
                  this.subdistrict_options = response.data.data
               })
            }

         } else {
            update()
         }
      },
      handleDeleteAddress(id) {
         this.$q.dialog({
            title: 'Konfirmasi',
            message: 'Yakin Akan menghapus data?',
            cancel: true,
         }).onOk(() => {
            if (this.user) {

               BaseApi.delete('user-address/' + id).then(() => {
                  this.getUserAddress()
               })
            } else {
               this.$store.commit('user/DELETE_ADDRESS', id)
               this.$emit('onSelectAddress', null)
            }

            setTimeout(() => {
               if (!this.user_address.length) {
                  this.addressModal = false
               }
            }, 1000)
         })
      }
   }
}
</script>