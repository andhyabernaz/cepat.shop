<template>
   <q-page class="checkout-page">
      <q-header class="text-grey-9 bg-white box-shadow">
         <q-toolbar>
            <q-btn @click="$router.push({ name: 'Cart' })" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">Checkout</q-toolbar-title>
         </q-toolbar>
      </q-header>
      <div id="checkout" v-if="cart_order_form.items.length" ref="top" class="q-pb-md main-content page-sm">

         <div class="cart-content">

            <CartOrderDetail></CartOrderDetail>
         </div>
         <div class="checkout-content">

            <q-card id="customer_detail" class="section q-mt-md shadow" v-if="cart_order_form.is_digital">
               <q-card-section>
                  <div class="q-gutter-y-sm">
                     <div class="card-subtitle">Detail Pelanggan</div>
                     <q-input filled label="Nama" v-model=form.receiver_name></q-input>
                     <q-input filled label="No Whatstapp" v-model=form.receiver_phone></q-input>


                  </div>
                  <div class="text-red q-pa-xs text-xs" v-if="errors.customer">{{ errors.customer }}</div>
               </q-card-section>
            </q-card>

            <ShippingAddress v-if="!cart_order_form.is_digital" canEmail ref="shippingAddress"></ShippingAddress>

            <PaymentList />
            <VoucherDscount ref="voucher" v-if="!cart_order_form.is_deposit"></VoucherDscount>

            <ReviewOrder></ReviewOrder>
            <div class="bg-white q-px-md q-pt-sm q-pb-md q-gutter-y-sm column sticky-bottom q-mt-md">
               <q-btn :disable="loading" @click="submitOrder" no-caps unelevated label="Proses Pesanan"
                  color="primary"></q-btn>
            </div>

         </div>
      </div>
      <q-inner-loading :showing="loading">

      </q-inner-loading>
   </q-page>
</template>

<script>
import CartOrderDetail from './CartOrderDetail.vue'
import ShippingAddress from './ShippingAddress.vue'
import PaymentList from './PaymentList.vue'
import ReviewOrder from './ReviewOrder.vue'
import VoucherDscount from './VoucherDscount.vue'
export default {
   components: { CartOrderDetail, ShippingAddress, PaymentList, ReviewOrder, VoucherDscount },
   name: 'CheckoutPage',
   data() {
      return {
         formLoading: false,
         form: {
            receiver_name: '',
            receiver_phone: '',
            order_note: ''
         }
      }
   },
   computed: {
      carts() {
         return this.$store.state.cart.carts
      },
      cart_order_form() {
         return this.$store.getters['cart/getChartOrderForm']
      },
      shop() {
         return this.$store.state.shop
      },
      config() {
         return this.$store.state.config
      },
      loading() {
         return this.$store.state.loading
      },
      user() {
         return this.$store.state.user.user
      },
      session_id() {
         return this.$store.state.session_id
      },
      errors() {
         return this.$store.state.errors
      },
   },
   created() {
      this.$store.commit('cart/SET_CUSTOMER', null)
      this.$store.commit('cart/SET_PAYMENT', null)
      this.$store.commit('cart/SET_COURIER', null)

      this.$store.dispatch('getConfig')
   },
   mounted() {
      if (!this.cart_order_form.items.length) {
         this.$router.push({ name: 'Cart' })
         return
      }
      if (this.config) {
         if (!this.config.can_checkout_local && !this.config.can_checkout_pickup && !this.config.can_checkout_courier && !this.config.can_checkout_digital) {
            this.$router.back()
         }
      }
      if (this.user) {
         this.form.receiver_name = this.user.name
         this.form.receiver_phone = this.user.phone
      }
   },
   methods: {
      submitOrder() {
         let form = this.generateFormOrder()

         if (form == false) {
            return
         }

         this.$q.loading.show()
         this.$store.dispatch('order/storeOrder', form)
            .then(response => {
               this.$store.commit('SET_LOADING', false)

               if (response.status == 200) {

                  setTimeout(() => {
                     this.$store.dispatch('cart/clearCart', this.session_id)
                  }, 2000)

                  this.$router.push({ name: 'UserInvoice', params: { order_ref: response.data.data.order_ref }, query: { pay: true } })

               }
            })
            .finally(() => {
               this.$q.loading.hide()
            })
            .catch((err) => {
               let msg = err?.response?.data?.message
               if (msg && (msg.startsWith('Kuota') || msg.startsWith('Masa'))) {
                  this.$store.commit('cart/SET_VOUCHER', null)
                  this.$refs.voucher.getVouchers()
               }
               this.ready = false
            })
      },
      generateFormOrder() {
         this.$store.commit('CLEAR_ERRORS')

         let customer = null

         if (this.cart_order_form.is_digital) {

            if (!this.form.receiver_name || !this.form.receiver_phone) {
               let msg = 'Detail pelanggan belum diisi'
               this.$store.commit('SET_ERRORS', { customer: msg })
               this.jumpTo('customer_detail')
               this.$q.notify({
                  type: 'negative',
                  message: msg
               })
               return false
            }

            customer = this.form
         } else {

            if (!this.cart_order_form.customer) {
               let msg = 'Alamat pengiriman belum diisi'
               this.$store.commit('SET_ERRORS', { customer: msg })
               this.jumpTo('shipping_section')
               this.$q.notify({
                  type: 'negative',
                  message: msg
               })
               return false
            }
            if (!this.cart_order_form.courier) {
               let msg = 'Kurir & ongkos kirim belum dipilih'
               this.$store.commit('SET_ERRORS', { courier: msg })
               this.jumpTo('courier_section')
               this.$q.notify({
                  type: 'negative',
                  message: msg
               })
               return false
            }

            customer = this.cart_order_form.customer
         }
         if (!this.cart_order_form.payment) {
            let msg = 'Metode pembayaran belum dipilih'
            this.$store.commit('SET_ERRORS', { payment: msg })
            this.jumpTo('payment_section')
            this.$q.notify({
               type: 'negative',
               message: msg
            })
            return false
         }

         let data = this.cart_order_form
         let form = {
            product_type: data.product_type,
            customer_name: customer.receiver_name,
            customer_phone: customer.receiver_phone,
            customer_email: customer.receiver_email ?? null,
            shipping_address: customer.full_address ?? null,
            payment_type: data.payment.payment_type,
            payment_method: data.payment.payment_method,
            payment_name: data.payment.payment_name,
            payment_code: data.payment.payment_code,
            payment_fee: data.payment.payment_fee,
            order_items: data.items,
            order_qty: data.qty,
            order_weight: data.weight,
            order_unique_code: data.unique_code,
            service_fee: data.service_fee,
            order_subtotal: data.subtotal,
            order_total: data.total,
            grand_total: data.grand_total,
            shipping_courier_id: data.courier?.courier_code || null,
            shipping_courier_name: data.courier?.courier_name || null,
            shipping_courier_service: data.courier?.courier_service_name || null,
            shipping_cost: data.courier?.price || 0,
            voucher_discount: data.voucher_discount,
            shipping_discount: data.shipping_discount,
            voucher_id: data.voucher ? data.voucher.id : null,
            customer_note: data.customer_note,
            shipping_coordinate: data.customer && data.customer.coordinate ? data.customer.coordinate.join(',') : '',
         }

         return form

      },
   },
   meta() {
      return {
         title: 'Checkout'
      }
   }
}
</script>
