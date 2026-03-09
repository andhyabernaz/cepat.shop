<template>
   <div>
      <form @submit.prevent="updateData">
         <q-card class="section shadow">

            <q-card-section>
               <div class="card-subtitle">
                  <q-item-label>Pengaturan Kurir toko</q-item-label>

               </div>
               <q-list>

                  <q-item>
                     <q-item-section side>
                        <q-toggle class=" text-grey-8" color="teal" v-model="form.is_local_shipping">
                        </q-toggle>

                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Kurir Toko</q-item-label>
                        <q-item-label caption>Opsi pengiriman menggunakan kurir toko</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section side>
                        <q-toggle :disable="form.is_local_shipping == false" class="text-grey-8" color="teal"
                           v-model="form.is_cod_payment"> </q-toggle>

                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Pembayaran Ditempat</q-item-label>
                        <q-item-label caption>Opsi pembayaran ditempat oleh kurir toko</q-item-label>
                     </q-item-section>
                     
                  </q-item>
                  <q-item>
                     <q-item-section>
                        <q-item-label>
                         Custom Label
                        </q-item-label>
                        <div class="q-mt-sm">
                           <q-input :error="isError" error-message="Bidang tidak boleh kosong" filled v-model="form.local_shipping_label" dense maxlength="60"></q-input>
                        </div>

                     </q-item-section>                     
                  </q-item>
               </q-list>

               <q-card flat>
                  <q-card-section>
                     <div class="">
                        <div class="text-md text-weight-bold">
                           Pengiriman dan Koordinat Toko
                        </div>
                        <div class="text-grey-8 q-pt-sm text-13">
                           Gunakan tombol lokasi terkini atau klik didalam map untuk
                           mendapatkan lokasi toko atau geser - geser ikon toko untuk
                           medapatkan lokasi yang sesuai
                        </div>
                     </div>

                     <div class="q-mt-md">
                        <div class="warehouse-map" v-if="config">
                           <MainMap ref="theMap" :config="config" :coordinate="form.warehouse_coordinate"
                              :radius="map_radius" @onEmitMap="onEmitMap" />
                        </div>
                     </div>
                     <!-- <div class="q-mt-md">
                        <div class="text-weight-medium text-md q-py-xs">
                           Mapbox Access Token (opsional)
                        </div>
                        <q-input dense v-model="form.mapbox_access_token" outlined></q-input>
                        <div class="text-caption text-grey-7 q-mt-sm">
                           Silahkan daftar di mapbox.com untuk mendapatkan akses token (opsional). Map tetap dapat
                           digunakan apabila token dikosongkan
                           yaitu menggunakan layanan gratis dari openstreetmap
                        </div>
                     </div> -->
                     <div class="q-mt-lg">
                        <div class="flex justify-between no-wrap items-center q-py-sm">
                           <div>
                              <div class="text-weight-medium text-md">Aturan Ongkos Kirim</div>
                              <p class="text-13 text-grey-7">
                                 Perhatikan baik - baik saat menambahkan aturan ongkos kirim,
                                 jarak yang diluar aturan akan diabaikan dan menjadi diluar
                                 jangkauan kurir.
                              </p>
                           </div>
                           <q-btn label="Tambah Aturan" color="teal" unelevated class="btn-action" @click="addRule"
                              no-caps></q-btn>
                        </div>
                        <div class="table-responsive">
                           <table class="table aligned dense">
                              <thead>
                                 <tr>
                                    <th>Flat Ongkir *</th>
                                    <th>Radius (KM)</th>
                                    <th>Biaya</th>
                                    <th>Delete</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr v-for="(item, idx) in form.local_shipping_costs" :key="idx" class="q-px-none">
                                    <td>
                                       <q-checkbox class="q-py-xs" outlined dense
                                          v-model="form.local_shipping_costs[idx].flat"></q-checkbox>
                                    </td>
                                    <td>
                                       <q-input required class="q-py-xs" outlined dense
                                          v-model="form.local_shipping_costs[idx].radius" label="Radius (KM)" mask="###"
                                          suffix="KM"></q-input>
                                    </td>
                                    <td>
                                       <q-input required class="q-py-xs" outlined dense
                                          v-model="form.local_shipping_costs[idx].cost"
                                          :label="form.local_shipping_costs[idx].flat ? 'Ongkos Kirim Flat' : 'Ongkos Kirim /km'"
                                          mask="##########"></q-input>
                                    </td>
                                    <td>
                                       <div>
                                          <q-btn icon="delete" round flat padding="xs" color="red"
                                             @click="removeCost(idx)" />
                                       </div>
                                    </td>
                                 </tr>

                              </tbody>
                           </table>

                        </div>
                        <div class="text-center q-py-md" v-if="!form.local_shipping_costs.length">Tidak ada data</div>
                        <div class="text-caption q-py-md">
                           * Flat ongkir adalah tarif tetap dalam radius tertentu alias tidak dihitung per km
                        </div>
                     </div>
                  </q-card-section>
               </q-card>
               <div class="q-pt-lg">
                  <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
               </div>
            </q-card-section>
         </q-card>


         <div class="q-pt-md">
            <div class="q-py-sm">

            </div>
            <div v-if="is_unconfig || is_payment_warning">
               <ul>
                  <li v-if="is_unconfig" class="text-caption text-yellow-10">Pengaturan koordinat atau ongkos kirim
                     belum
                     lengkap
                  </li>
                  <li v-if="is_payment_warning" class="text-caption text-yellow-10">Pengaturan metode pembayaran belum
                     ada,
                     tambahkan metode
                     Bayar Ditempat /
                     Akun Bank /
                     Payment Gateway agar dapat digunakan</li>
               </ul>
            </div>
         </div>
      </form>
   </div>
</template>

<script>
import { BaseApi } from "boot/axios";
import MainMap from 'components/MainMap.vue'
export default {
   components: { MainMap },
   data() {
      return {
         inputModal: false,
         codListModal: false,
         subdistrictOptions: [],
         modal: false,
         search: "",
         searchLoading: false,
         cod_lists: [],
         form: {
            cod_list: [],
            is_cod_payment: false,
            is_local_shipping: false,
            local_shipping_costs: [],
            mapbox_access_token: '',
            warehouse_coordinate: [],
            local_shipping_label: ''
         },
         isError: false,
         errorMessage: ''
      };
   },
   created() {
      this.getConfig()
   },
   watch: {
      'form.is_local_shipping'(val) {
         if (val == false) {
            this.form.is_cod_payment = false
         }
      },
   },
   computed: {
      config() {
         return this.$store.state.config;
      },
      map_radius() {
         if (this.form.local_shipping_costs.length) {
            let r = this.form.local_shipping_costs[this.form.local_shipping_costs.length - 1].radius
            return parseInt(r * 1000)
         }
         return 0
      },
      is_unconfig() {
         if (!this.form.warehouse_coordinate.length || !this.form.is_local_shipping || !this.form.local_shipping_costs.length) {
            return true
         }
         return false
      },
      is_payment_warning() {
         if (this.config) {
            if (this.form.is_local_shipping) {
               if (!this.config.is_bank_ready && !this.config.is_tripay_ready && !this.form.is_cod_payment) {

                  return true
               }
            }
         }
         return false
      }
   },
   methods: {
      getConfig() {
         BaseApi.get("config").then((response) => {
            if (response.status == 200) {
               this.setConfig(response.data.data);
            }
         });
      },
      setConfig(item) {

         this.form.is_cod_payment = item.is_cod_payment;
         this.form.is_local_shipping = item.is_local_shipping;
         this.form.local_shipping_label = item.local_shipping_label;

         this.form.warehouse_coordinate = item.warehouse_coordinate
            ? item.warehouse_coordinate
            : [];
         this.form.local_shipping_costs = item.local_shipping_costs
            ? item.local_shipping_costs
            : [];
         this.form.mapbox_access_token = item.mapbox_access_token;
      },
      onEmitMap(evt) {
         this.form.warehouse_coordinate[0] = evt[0];
         this.form.warehouse_coordinate[1] = evt[1];
      },

      removeCost(idx) {
         this.form.local_shipping_costs.splice(idx, 1);
      },
      
      addRule() {
         let start = 0;

         if (this.form.local_shipping_costs.length) {
            let endItem = this.form.local_shipping_costs[this.form.local_shipping_costs.length - 1];

            start = parseInt(endItem.end) + 1;
         }
         this.form.local_shipping_costs.push({ cost: 500, radius: start, flat: false });
      },
      updateData() {
         this.isError = false
         if(!this.form.local_shipping_label) {
            this.isError = true
            return
         }
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
      setLoading(status) {
         this.$store.commit("SET_LOADING", status);
      },
      selectCodItemData(data) {
         let hasData = this.form.cod_list.filter(
            (elj) => elj.vendor_id == data.vendor_id
         );
         if (hasData.length) {
            this.form.cod_list = this.form.cod_list.filter(
               (elm) => elm.vendor_id != data.vendor_id
            );
         }
         this.form.cod_list.push({
            ...data,
            price: 0,
            courier_service: this.config.courier_default,
         });
         this.search = "";
         this.subdistrictOptions = [];
         this.inputModal = false
      },
      hasCodData(item) {
         let has = this.form.cod_list.filter(
            (en) => en.vendor_id == item.vendor_id
         );
         if (has.length) {
            return true;
         } else {
            return false;
         }
      },
      removeCodList(index) {
         this.form.cod_list.splice(index, 1);
      },
      searchCodData() {
         this.findSubdistrict();
      },
      getAdminConfig() {
         BaseApi.get("config").then((response) => {
            if (response.status == 200) {
               this.setConfig(response.data.data);
            }
         });
      },
      findSubdistrict() {
         this.subdistrictOptions = [];
         if (this.search.length < 3) return;
         this.searchLoading = true;
         this.$store
            .dispatch("searchAddress", this.search)
            .then((response) => {
               if (response.status == 200) {
                  if (response.data.success) {
                     this.subdistrictOptions = response.data.data;
                  } else {
                     this.$q.notify({
                        type: "negative",
                        message: response.data.message,
                     });
                  }
               }
            })
            .finally(() => {
               this.searchLoading = false;
            });
      },
      closeSubdistrictBox() {
         this.subdistrictOptions = [];
         this.search = "";
      },
   },
};
</script>
