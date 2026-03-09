<template>
   <div class="">
      <div id="courier" ref="courier">
         <div id="">

            <q-card square id="shipping_section" class="fancy-border q-pa-md shadow q-mt-sm" style="min-height:130px">

               <q-item-label class="card-subtitle flex justify-between items-center">
                  Alamat Pengiriman
               </q-item-label>
               <q-list padding>
                  <q-item class="bg-grey-1 q-py-lg" clickable @click="handleOpenAddressModal">
                     <q-item-section avatar top class="q-pt-sm">
                        <q-avatar icon="place" size="lg" color="grey-3"></q-avatar>
                     </q-item-section>
                     <q-item-section v-if="cart_order_form.customer">

                        <div>
                           <div class="text-weight-bold">{{ cart_order_form.customer.receiver_name }}</div>
                           <div>{{ cart_order_form.customer.receiver_phone }}</div>
                           <div class="text-grey-7">{{ cart_order_form.customer.full_address }}</div>

                        </div>
                     </q-item-section>
                     <q-item-section v-else>
                        <q-item-label class="text-md">Pilih Alamat Pengiriman</q-item-label>
                        <q-item-label caption>Pilih atau tambahkan alamat tujuan pengiriman</q-item-label>
                     </q-item-section>
                     <q-item-section side>
                        <q-icon name="arrow_forward_ios" size="sm" flat dense></q-icon>
                     </q-item-section>
                  </q-item>
               </q-list>
               <div class="text-red q-pa-xs text-xs" v-if="errors.customer">{{ errors.customer }}</div>
            </q-card>

            <q-card square flat bordered id="courier_section" class="q-pa-md shadow q-mt-sm" style="min-height:130px">
               <q-item-label class="card-subtitle flex justify-between items-center">
                  Metode Pengiriman
               </q-item-label>

               <q-list v-if="!cod_unavailable">

                  <q-item :clickable="render_shipping_costs.length > 0 || config.is_shippable" class="bg-grey-1 q-py-md"
                     @click="handleOpenCostModal">
                     <q-item-section avatar top class="q-pt-sm">
                        <q-avatar v-if="cart_order_form.courier"
                           :icon="getShippingIcon(cart_order_form.courier.courier_service_code)" 
                           color="grey-3"
                           size="lg"></q-avatar>
                        <q-avatar v-else icon="local_shipping" color="grey-3" size="lg"></q-avatar>
                     </q-item-section>
                     <q-item-section v-if="cart_order_form.courier">

                        <q-item-label>
                           {{ cart_order_form.courier.courier_name }}
                        </q-item-label>
                        <q-item-label class="text-grey-8">
                           {{ cart_order_form.courier.courier_service_name }}
                        </q-item-label>
                        <q-item-label caption v-if="cart_order_form.courier.duration">
                           Perkiraan tiba {{ cart_order_form.courier.duration }}
                        </q-item-label>

                        <q-item-label class="q-gutter-x-sm flex no-wrap">
                           <span
                              v-if="cart_order_form.voucher && cart_order_form.voucher.is_type_shipping && cart_order_form.courier.price"
                              class="text-nowrap text-strike">
                              {{ moneyFormat(cart_order_form.courier.price) }} IDR
                           </span>
                           <span class="text-nowrap text-primary text-weight-bold">
                              {{ moneyFormat(calculate_ongkir(cart_order_form.courier.price)) }} IDR

                           </span>
                        </q-item-label>
                     </q-item-section>
                     <q-item-section v-else>
                        <q-item-label class="text-md">Pilih Metode Pengiriman</q-item-label>
                        <q-item-label caption>Pilih salah satu metode pengiriman dan ongkos kirim</q-item-label>
                     </q-item-section>
                     <q-item-section side v-if="render_shipping_costs.length > 0 || config.is_shippable">
                        <q-icon name="arrow_forward_ios" size="sm" flat dense></q-icon>
                     </q-item-section>

                  </q-item>
               </q-list>
               <div v-if="cod_unavailable">
                  <div class="q-py-sm text-grey-7">
                     <div class="text-weight-medium text-red">Alamat di luar jangkauan.</div>
                     <div class="q-mt-xs" style="max-width:400px">Maaf, alamat yang dipilih diluar jangkauan kurir
                        kami</div>

                     <ol v-if="config.cod_list.length">
                        <li v-for="(an, i) in config.cod_list" :key="i">
                           {{ an.name }} ({{ moneyIdr(an.price) }})
                        </li>
                     </ol>

                  </div>
               </div>
               <div class="text-red text-xs q-pt-xs" v-if="errors.courier">
                  {{ errors.courier }}
               </div>
               <q-inner-loading :showing="get_cost_loading" label="Please wait..." color="teal"
                  label-class="text-teal"></q-inner-loading>
            </q-card>

            <q-dialog v-model="costModal" position="bottom">
               <q-card class="max-width-mobile">
                  <div style="min-height:400px">
                     <div class="sticky-top">
                        <div class="card-header">
                           <div>Metode Pengiriman</div>
                           <q-btn flat icon="close" v-close-popup color="white"></q-btn>
                        </div>

                     </div>

                     <q-list v-if="render_shipping_costs.length" separator class="q-pb-xl">

                        <q-expansion-item default-opened header-class="font-weight-bold text-md q-py-md
                        " label="Kurir Toko" v-if="other_couriers.length">
                           <q-list separator>
                              <q-item v-for="(item, idx) in other_couriers" :key="idx" v-ripple
                                 @click="selectShippingCost(item)" clickable
                                 :class="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'bg-green-1' : 'bg-grey-1'">
                                 <q-item-section avatar>
                                    <q-avatar :icon="getShippingIcon(item.courier_service_code)"
                                       :color="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'green' : 'grey-3'"
                                       :text-color="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'white' : 'grey-9'"
                                       size="lg"></q-avatar>
                                 </q-item-section>
                                 <q-item-section>

                                    <q-item-label>
                                       {{ item.courier_name }}
                                    </q-item-label>
                                      <q-item-label class="text-grey-8">
                                       {{ item.courier_service_name }}
                                    </q-item-label>
                                    <q-item-label caption v-if="item.duration">
                                       Perkiraan tiba {{ item.duration }}
                                    </q-item-label>

                                 </q-item-section>
                                 <q-item-section side>
                                    <q-item-label class="q-gutter-x-sm flex no-wrap">
                                       <span
                                          v-if="cart_order_form.voucher && cart_order_form.voucher.is_type_shipping && item.price"
                                          class="text-nowrap text-strike">
                                          {{ moneyFormat(item.price) }} IDR
                                       </span>
                                       <span class="text-nowrap text-primary text-weight-bold">
                                          {{ moneyFormat(calculate_ongkir(item.price)) }} IDR

                                       </span>
                                    </q-item-label>
                                 </q-item-section>
                              </q-item>
                           </q-list>
                        </q-expansion-item>
                        <q-expansion-item default-opened header-class="font-weight-bold text-md q-py-md
                        " label="Kurir Instan" v-if="instant_couriers.length">
                           <q-list separator>
                              <q-item v-for="(item, idx) in instant_couriers" :key="idx" v-ripple
                                 @click="selectShippingCost(item)" clickable
                                 :class="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'bg-green-1' : 'bg-grey-1'">
                                 <q-item-section avatar>
                                    <q-avatar :icon="getShippingIcon(item.courier_service_code)"
                                       :color="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'green' : 'grey-3'"
                                       :text-color="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'white' : 'grey-9'"
                                       size="lg"></q-avatar>
                                 </q-item-section>
                                 <q-item-section>

                                    <q-item-label>
                                       {{ item.courier_name }}
                                    </q-item-label>
                                      <q-item-label class="text-grey-8">
                                       {{ item.courier_service_name }}
                                    </q-item-label>
                                    <q-item-label caption v-if="item.duration">
                                       Perkiraan tiba {{ item.duration }}
                                    </q-item-label>

                                 </q-item-section>
                                 <q-item-section side>
                                    <q-item-label class="q-gutter-x-sm flex no-wrap">
                                       <span
                                          v-if="cart_order_form.voucher && cart_order_form.voucher.is_type_shipping && item.price"
                                          class="text-nowrap text-strike">
                                          {{ moneyFormat(item.price) }} IDR
                                       </span>
                                       <span class="text-nowrap text-primary text-weight-bold">
                                          {{ moneyFormat(calculate_ongkir(item.price)) }} IDR

                                       </span>
                                    </q-item-label>
                                 </q-item-section>
                              </q-item>

                           </q-list>

                        </q-expansion-item>
                        <q-expansion-item default-opened header-class="font-weight-bold text-md q-py-md
                        " label="Kurir Ekspedisi" v-if="expedisi_couriers.length">
                           <q-list separator>

                              <q-item v-for="(item, idx) in expedisi_couriers" :key="idx" v-ripple
                                 @click="selectShippingCost(item)" clickable
                                 :class="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'bg-green-1' : 'bg-grey-1'">
                                 <q-item-section avatar>
                                    <q-avatar :icon="getShippingIcon(item.courier_service_code)"
                                       :color="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'green' : 'grey-3'"
                                       :text-color="cart_order_form.courier && cart_order_form.courier.id == item.id ? 'white' : 'grey-9'"
                                       size="lg"></q-avatar>
                                 </q-item-section>
                                 <q-item-section>

                                    <q-item-label >
                                       {{ item.courier_name }}
                                    </q-item-label>
                                    <q-item-label class="text-grey-8">
                                       {{ item.courier_service_name }}
                                    </q-item-label>
                                    <q-item-label caption v-if="item.duration">
                                       Perkiraan tiba {{ item.duration }}
                                    </q-item-label>

                                 </q-item-section>
                                 <q-item-section side>
                                    <q-item-label class="q-gutter-x-sm flex no-wrap">
                                       <span
                                          v-if="cart_order_form.voucher && cart_order_form.voucher.is_type_shipping && item.price"
                                          class="text-nowrap text-strike">
                                          {{ moneyFormat(item.price) }} IDR
                                       </span>
                                       <span class="text-nowrap text-primary text-weight-bold">
                                          {{ moneyFormat(calculate_ongkir(item.price)) }} IDR

                                       </span>
                                    </q-item-label>
                                 </q-item-section>
                              </q-item>
                           </q-list>
                        </q-expansion-item>

                     </q-list>
                     <div v-if="costNotFound" class="q-pa-lg text-center">
                        <div class="text-red">
                           Ongkos kirim tidak ditemukan
                        </div>
                        <div class="text-grey-8">Silahkan ganti ekspedisi yang lain atau hubungi admin jika ongkos kirim
                           tetap tidak ditemukan</div>
                     </div>
                     <div v-if="!render_shipping_costs.length && !get_cost_loading && !costNotFound"
                        class="text-center q-pa-xl">
                        <div class="text-md text-weight-bold">Ongkos kirim belum tersedia</div>
                        <div class="text-grey-8" v-if="cart_order_form.customer">Pilihan kurir dan ongkos kirim belum
                           tersedia</div>
                        <div class="text-grey-8" else>Silahkan pilih alamat pengiriman terlebih dahulu untuk
                           mendapatkan detail ongkos kirim</div>
                     </div>
                     <div ref="courier_skeleton">
                        <q-list v-if="get_cost_loading" class="q-py-md">
                           <q-item>
                              <q-item-section avatar top>
                                 <div class="q-pa-xs">
                                    <q-skeleton width="35px" height="35px" class="round"></q-skeleton>
                                 </div>
                              </q-item-section>
                              <q-item-section>
                                 <q-skeleton type="text" width="80%" height="30px"></q-skeleton>
                                 <q-skeleton type="text" width="60%"></q-skeleton>
                                 <q-skeleton type="text" width="45%"></q-skeleton>
                              </q-item-section>
                           </q-item>
                        </q-list>
                     </div>
                  </div>


               </q-card>
            </q-dialog>

         </div>

      </div>

      <UserAddressForm autoSelectModal ref="userAddressForm" @onSelectAddress="handleSelectAddress" />

      <div id="mapView"></div>
   </div>
</template>

<script>
import { Api } from 'boot/axios'
import UserAddressForm from 'src/components/UserAddressForm.vue'
import { sortByKey } from 'src/utils'
import L from 'leaflet'
import 'leaflet-routing-machine'
export default {
   name: 'ShippingAddress',
   components: { UserAddressForm },
   props: {
      canEmail: {
         type: Boolean,
         default: false
      },
   },
   data() {
      return {
         costNotFound: false,
         shipping_costs: [],
         costModal: false,
         get_cost_loading: false,
         kurir: '',
         local_cost: null,
         provider: null,
         routeControl: null,
         originMarker: null,
         destinationMarker: null,
         destinationCenter: null,
         map: null
      }
   },
   computed: {
      errors() {
         return this.$store.state.errors
      },
      user_address() {
         return this.$store.state.user.address
      },
      cart_order_form() {
         return this.$store.getters['cart/getChartOrderForm']
      },
      loading() {
         return this.$store.state.loading
      },
      config() {
         return this.$store.state.config
      },
      is_shippable() {
         if (this.config && this.config.is_shippable) {
            return true
         }
         return false
      },
      cod_unavailable() {
         if (!this.cart_order_form.customer) {
            return false
         }
         if (!this.is_shippable && !this.render_shipping_costs.length) {
            return true
         }

         return false
      },
      canGetCost() {
         if (this.get_cost_loading) {
            return false;
         }
         return true
      },
      render_shipping_costs() {

         let data = [];

         if (this.cart_order_form.customer) {

            if (this.config.can_checkout_pickup) {
               data.push({
                  id: 'PICKUP',
                  courier_code: "PICKUP",
                  courier_name: "Ambil Di Toko",
                  courier_service_name: "Diambil sendiri oleh pelanggan",
                  courier_service_code: "PICKUP",
                  price: 0,
                  type: 'other'
               })
            }
         }

         if (this.local_cost) {
            data = [...data, this.local_cost]

         }


         return [...data, ...this.shipping_costs];
      },
      other_couriers() {
         return this.render_shipping_costs.filter(el => {
            if (el.type && el.type == 'other') {
               return el
            }
         })
      },
      expedisi_couriers() {
         return this.render_shipping_costs.filter(el => {
            if(!el.type) {
               return el
            }
            if (!el.type.includes('instant') && !el.type.includes('same_day') && !el.type.includes('other') && !el.type.includes('motorcycle')) {
               return el
            }
         })
      },
      instant_couriers() {
         return this.render_shipping_costs.filter(el => {
            if (el.type && ( el.type.includes('instant') || el.type.includes('same_day') || el.type.includes('motorcycle'))) {
               return el
            }
         })
      },
   },
   methods: {
      handleAddAddress() {
         this.$refs.userAddressForm.handleAddAddress()
      },
      handleOpenAddressModal() {
         this.$refs.userAddressForm.handleOpenAddressModal()
      },
      handleOpenCostModal() {
         this.costModal = true
      },
      getShippingIcon(type) {

         type = type.toLowerCase()
         if (type.includes('car')) return 'directions_car'
         if (type.includes('motor')) return 'motorcycle'
         if (type.includes('motor')) return 'motorcycle'
         if (type.includes('instant')) return 'motorcycle'
         if ('cod' == type) return 'motorcycle'
         if ('same_day' == type) return 'motorcycle'
         if ('pickup' == type) return 'directions_walk'

         return 'local_shipping'
      },
      calculate_ongkir(price) {

         if (this.cart_order_form.voucher && this.cart_order_form.voucher.is_type_shipping) {

            let disc_amount = this.cart_order_form.voucher.discount_amount

            let disc = price > disc_amount ? disc_amount : price

            return price - disc
         }
         return price
      },
      selectShippingCost(item) {
         this.$store.commit('cart/SET_COURIER', item)
         this.costNotFound = false
         this.$store.commit('CLEAR_ERRORS');
         this.kurir = ''
         this.costModal = false
      },
      handleSelectAddress(item) {

         this.$store.commit('cart/SET_CUSTOMER', item)
         this.shipping_costs = []
         this.costNotFound = false
         this.$store.commit('CLEAR_ERRORS');
         if (this.config.is_shippable && item) {
            setTimeout(() => {
               this.getCost();
            }, 300)
         }

         if (this.config.can_checkout_local && item && item.coordinate && this.config.warehouse_coordinate) {
            this.getLocalCost(item.coordinate)
         }
      },
      handleSelectKurir() {
         this.shipping_costs = []
         this.getCost()
      },
      getCost() {

         if (!this.canGetCost) {
            return
         }
         this.costNotFound = false

         this.$store.commit('cart/SET_COURIER', null)

         let payload = {
            destination: this.cart_order_form.customer.destination_id,
            items: this.cart_order_form.items,
            weight: this.cart_order_form.weight,
         }

         const coordinate = this.cart_order_form.customer.coordinate

         if (coordinate && coordinate.length) {
            payload.destination_latitude = coordinate[0]
            payload.destination_longitude = coordinate[1]
         }

         this.get_cost_loading = true
         Api.post('shipping/costs', payload).then(response => {
            if (response.status == 200) {

               let data = response.data.data;
               this.shipping_costs = data
               // this.shipping_costs = this.this.(data, 'price')

               if (!this.render_shipping_costs.length) {
                  this.costNotFound = true
               } 
            }
         }).catch(() => {
            this.$store.commit('SET_LOADING', false)
            if (!this.render_shipping_costs.length) {
               this.costNotFound = true
            }
         }).finally(() => {
            this.get_cost_loading = false
         })

      },
      getLocalCost(coords) {

         if (!this.map) {
            this.map = L.map('mapView', {
               center: this.config.warehouse_coordinate,
            });
         }

         let distanceInMeter = this.map.distance(this.config.warehouse_coordinate, coords)

         let total_distance = (distanceInMeter / 1000).toFixed(1);

         // console.log('direct distance: in M', distanceInMeter);
         // console.log('direct distance: in KM', total_distance);


         let localCosts = sortByKey(this.config.local_shipping_costs, 'radius')

         // Get max distance from last item

         let lastItem = localCosts[localCosts.length - 1]

         let max_distance = parseInt(lastItem.end)

         if (total_distance > max_distance) {

            this.local_cost = null

            return
         }

         // Get Cost by current distance

         let selectedRule = null

         for (let i = 0; i < localCosts.length; i++) {

            let currentRule = localCosts[i]

            let radius = parseInt(currentRule.radius)

            if (total_distance > radius) {

               continue;

            } else {
               selectedRule = currentRule
               break;
            }
         }

         if (!selectedRule) {
            this.local_cost = null
            return
         }

         let ongkir = 0

         if (selectedRule.flat) {
            ongkir = parseInt(selectedRule.cost)
         } else {
            if (total_distance > 0) {
               ongkir = Math.round(total_distance * parseInt(selectedRule.cost))
            } else {
               ongkir = parseInt(selectedRule.cost)
            }
         }

         let lb = 'Via Kurir Toko'
         if(this.config && this.config.local_shipping_label) {
            lb = this.config.local_shipping_label
         }

         this.local_cost = {
            id: 'COD',
            courier_code: "COD",
            courier_name: lb,
            courier_service_name: "Diantar oleh kurir toko",
            courier_service_code: "COD",
            price: ongkir,
            type: 'other'
         }



      },
   }
}
</script>