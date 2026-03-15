<template>
   <div>
      <q-form @submit.prevent="updateData">
         <q-card flat class="section">
            <q-card-section>
               <div class="card-subtitle">Pengaturan Ekspedisi</div>
               <div>
                  <q-list bordered class="q-mb-sm">
                     <q-item>
                        <q-item-section>
                           <div class="">Status</div>
                        </q-item-section>
                        <q-item-section side>
                           <q-toggle v-model="formdata.is_shipping_active"
                              :label="formdata.is_shipping_active ? 'Active' : 'Disabled'" left-label color="teal"
                              class="text-grey-8"></q-toggle>
                        </q-item-section>
                     </q-item>
                  </q-list>
                  <q-select outlined :options="courierServices" v-model="formdata.courier_default" label="Provider"
                     @update:model-value="changeProvider"></q-select>
               </div>
               <div class="q-mt-md">
                  <div v-if="formdata.courier_default == 'Rajaongkir'">
                     <q-card flat bordered>
                        <q-card-section>
                           <div class="card-subtitle">Rajaongkir Config</div>
                           <div class="text-caption text-grey-7">
                              <div>Pengaturan ongkir otomatis by Rajaongkir</div>
                              <div>
                                 Silahkan daftar di rajaongkir.com untuk mendapatkan apikey
                              </div>
                           </div>

                           <div class="q-py-md">
                              <!-- <div class="input-label">PAKET</div> -->
                              <q-select label="Raja Ongkir Paket" filled :options="rajaongkirtypes"
                                 v-model="formdata.rajaongkir_type" @update:model-value="selectCourierType"></q-select>
                              <div class="input-label q-mt-md">API KEY</div>
                              <q-input @update:model-value="selectApiKey" required filled
                                 v-model="formdata.rajaongkir_apikey" label="Rajaongkir API KEY">
                              </q-input>
                           </div>
                        </q-card-section>
                     </q-card>

                     <q-card class="q-mt-md" flat bordered v-if="config.rajaongkir_apikey">
                        <q-card-section>
                           <div class="card-subtitle">Gudang Pengiriman</div>
                           <div>

                              <q-select required filled v-model="formdata.warehouse_address" use-input hide-selected
                                 clearable fill-input option-label="name" input-debounce="1000" label="Cari Kecamatan"
                                 :options="subdistrict_options" @filter="filterFn">
                                 <template v-slot:option="scope">
                                    <q-item v-bind="scope.itemProps" v-on="scope.itemEvents">
                                       <q-item-section>
                                          <q-item-label>{{ scope.opt.name }}</q-item-label>
                                          <q-item-label caption>{{
                                             scope.opt.country_name
                                          }}</q-item-label>
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
                              <div class="q-mt-md">
                                 <div class="input-label">
                                    <div>Kurir Aktif</div>
                                    <div class="text-grey-7 text-caption">
                                       Aktifkan kurir yang yang akan digunakan
                                    </div>
                                 </div>
                                 <q-select :rules="[requiredRules]" lazy-rules :options="all_couriers"
                                    v-model="formdata.rajaongkir_couriers" use-chips filled multiple></q-select>
                              </div>
                           </div>
                        </q-card-section>

                     </q-card>
                  </div>
                  <div v-if="formdata.courier_default == 'Biteship'">
                     <q-card flat bordered>
                        <q-card-section>
                           <div class="card-subtitle">Biteship Config</div>
                           <div class="text-caption text-grey-7">
                              <div>Pengaturan ongkir otomatis by Biteship</div>
                              <div>
                                 Silahkan daftar di Biteship.com untuk mendapatkan apikey
                              </div>
                           </div>
                           <div class="q-mt-md">
                              <div class="input-label">API KEY</div>
                              <q-input @update:model-value="selectApiKey" filled v-model="formdata.biteship_apikey"
                                 label="Biteship API KEY">
                              </q-input>

                           </div>
                        </q-card-section>

                     </q-card>
                     <q-card v-if="config.biteship_apikey" class="q-mt-md" flat bordered>
                        <q-card-section>

                           <div>

                              <div>
                                 <div class="input-label">Gudang Pengiriman</div>

                                 <q-select filled v-model="formdata.biteship_warehouse" use-input hide-selected
                                    fill-input clearable option-label="name" input-debounce="1000"
                                    label="Cari Kecamatan" :options="subdistrict_options" @filter="filterFn">
                                    <template v-slot:option="scope">
                                       <q-item v-bind="scope.itemProps" v-on="scope.itemEvents">
                                          <q-item-section>
                                             <q-item-label>{{ scope.opt.name }}</q-item-label>
                                             <q-item-label caption>{{
                                                scope.opt.country_name
                                             }}</q-item-label>
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
                                 <div class="q-mt-md">
                                    <div class="input-label">
                                       <div>Kurir Aktif</div>
                                       <div class="text-grey-7 text-caption">
                                          Aktifkan kurir yang yang akan digunakan
                                       </div>
                                    </div>
                                    <q-select :options="all_couriers" v-model="formdata.biteship_couriers" use-chips
                                       filled multiple></q-select>
                                 </div>
                              </div>
                           </div>
                        </q-card-section>

                     </q-card>
                  </div>
               </div>
               <div class="q-pt-lg">
                  <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-form>
      <q-dialog v-model="modal">
         <q-card style="width: 100%; max-width: 500px">
            <q-card-section>
               <div class="flex items-center justify-between">
                  <div class="text-md text-weight-bold q-mb-sm">
                     Pilih Gudang Pengiriman
                  </div>
                  <q-btn flat icon="close" v-close-popup></q-btn>
               </div>
               <div class="q-pa-sm q-gutter-y-sm">
                  <q-input ref="warehouse" :loading="searchLoading && searchType == 'warehouse'"
                     placeholder="Ketik nama kecamatan" v-model="search" debounce="1000"
                     @update:model-value="searchWarehouseData"></q-input>
                  <transition appear enter-active-class="animated fadeIn" leave-active-class="animated fadeOut">
                     <div style="min-height: 100px; max-height: 300px; overflow-y: auto" class="relative bg-grey-2"
                        v-if="searchType == 'warehouse' && subdistrictOptions.length">
                        <q-list>
                           <q-item v-for="item in subdistrictOptions" :key="item.id" clickable
                              @click="selectSubdistrict(item)">
                              <q-item-section>
                                 <q-item-label>{{ item.subdistrict_name }} - {{ item.type }}
                                    {{ item.city }}</q-item-label>
                              </q-item-section>
                           </q-item>
                        </q-list>
                     </div>
                  </transition>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { Api, BaseApi } from "boot/axios";
import { requiredRules } from "src/utils";
export default {
   data() {
      return {
         codListModal: false,
         modal: false,
         rajaongkirtypes: ["starter", "pro"],
         subdistrictOptions: [],
         courierServices: ["Rajaongkir", "Biteship"],
         search: "",
         isWarehouseSearch: false,
         searchType: "cod",
         searchLoading: false,
         all_couriers: [],
         formdata: {
            is_shipping_active: false,
            rajaongkir_type: "",
            rajaongkir_apikey: "",
            warehouse_id: "",
            warehouse_address: "",
            rajaongkir_couriers: [],
            courier_default: "",
            biteship_apikey: "",
            biteship_couriers: [],
            biteship_warehouse: "",
         },
         subdistrict_options: [],
      };
   },
   computed: {
      config: function () {
         return this.$store.state.config;
      },
      isCouriersValueActive() {
         if (this.formdata.courier_default == "Rajaongkir") {
            return this.formdata.rajaongkir_couriers.map((t) => t.value);
         } else {
            return this.formdata.biteship_couriers.map((t) => t.value);
         }
      },
   },
   created() {
      this.getAdminConfig();
   },

   methods: {
      setConfig(item) {
         this.formdata.is_shipping_active = item.is_shipping_active;
         this.formdata.rajaongkir_type = item.rajaongkir_type;
         this.formdata.rajaongkir_apikey = item.rajaongkir_apikey;
         this.formdata.rajaongkir_couriers =
            item.rajaongkir_couriers && item.rajaongkir_couriers.length
               ? item.rajaongkir_couriers
               : [];
         this.formdata.warehouse_address = item.warehouse_address;
         this.formdata.warehouse_id = item.warehouse_id;
         this.formdata.biteship_apikey = item.biteship_apikey;
         this.formdata.courier_default = item.courier_default;
         this.formdata.biteship_couriers =
            item.biteship_couriers && item.biteship_couriers.length
               ? item.biteship_couriers
               : [];
         this.formdata.biteship_warehouse = item.biteship_warehouse;

         this.getCouriers();
      },
      changeProvider() {
         setTimeout(() => {
            this.updateData()
         }, 300)
      },
      selectApiKey() {
         setTimeout(() => {
            this.getCouriers()
         }, 300)
      },
      selectCourierType() {
         this.formdata.rajaongkir_couriers = [];
         this.getCouriers()
      },
      isCourierActive(name) {
         return this.isCouriersValueActive.includes(name.value);
      },
      handleSelectCourier(evt) {
         let already = this.formdata.rajaongkir_couriers.find(
            (i) => i.value == evt.value
         );

         if (already != undefined) {
            this.formdata.rajaongkir_couriers =
               this.formdata.rajaongkir_couriers.filter(
                  (el) => el.value != already.value
               );
         } else {
            this.formdata.rajaongkir_couriers.push(evt);
         }
      },
      updateData() {
         BaseApi.post("config", this.formdata)
            .then((res) => {
               this.getAdminConfig();
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
      async filterFn(val, update, abort) {
         if (val.length >= 3) {
            let params = {
               q: val,
               rajaongkir_apikey: this.formdata.rajaongkir_apikey,
               rajaongkir_type: this.formdata.rajaongkir_type,
               biteship_apikey: this.formdata.biteship_apikey,
               courier_default: this.formdata.courier_default,
            }
            let response = await BaseApi.get(`shipping/searchAddress?${new URLSearchParams(params).toString()}`)

            if (response.status == 200) {
               update(() => {
                  this.subdistrict_options = response.data.data;
               });
            }
         } else {
            update();
         }
      },
      getCouriers() {
         let params = {
            rajaongkir_apikey: this.formdata.rajaongkir_apikey,
            rajaongkir_type: this.formdata.rajaongkir_type,
            biteship_apikey: this.formdata.biteship_apikey,
            courier_default: this.formdata.courier_default,
         }
         BaseApi.get(`shipping/getCouriers?${new URLSearchParams(params).toString()}`).then((res) => {
            this.all_couriers = res.data.data;
         });
      },
      submitWarehouse() {
         this.updateData();
      },
      setLoading(status) {
         this.$store.commit("SET_LOADING", status);
      },
      selectSubdistrict(item) {
         this.formdata.warehouse_id = item.city_id;
         this.formdata.warehouse_address = item;
         this.modal = false;
         this.subdistrictOptions = [];
         this.search = "";
      },
      searchWarehouseData() {
         this.searchType = "warehouse";
         this.findSubdistrict();
      },
      getAdminConfig() {
         BaseApi.get("config").then((response) => {
            if (response.status == 200) {
               this.$store.commit('SET_CONFIG', response.data.data)
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
