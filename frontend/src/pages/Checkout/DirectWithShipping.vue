<template>
   <q-page>
      <q-header class="text-primary bg-white box-shadow">
         <q-toolbar>
            <q-btn @click="handleBackButton" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">Checkout</q-toolbar-title>
         </q-toolbar>
      </q-header>

      <div id="checkout" v-if="cart_form_order.items.length">
         <CartOrderDetail></CartOrderDetail>
         <ShippingAddress></ShippingAddress>
         <VoucherDscount></VoucherDscount>
         <ReviewOrder></ReviewOrder>
      </div>
      <q-footer class="bg-white q-pa-md">
         <q-btn @click="checkout" color="green-6" class="full-width q-mt-sm" no-caps :disable="loading">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 50 50"
               width="20px" height="20px">
               <g id="surface1441897">
                  <path style=" stroke:none;fill-rule:nonzero;fill:currentColor;fill-opacity:1;"
                     d="M 25 2 C 12.316406 2 2 12.316406 2 25 C 2 28.960938 3.023438 32.855469 4.964844 36.289062 L 2.035156 46.730469 C 1.941406 47.074219 2.035156 47.441406 2.28125 47.695312 C 2.472656 47.894531 2.734375 48 3 48 C 3.078125 48 3.160156 47.988281 3.238281 47.972656 L 14.136719 45.273438 C 17.464844 47.058594 21.210938 48 25 48 C 37.683594 48 48 37.683594 48 25 C 48 12.316406 37.683594 2 25 2 Z M 36.570312 33.117188 C 36.078125 34.476562 33.71875 35.722656 32.585938 35.886719 C 31.566406 36.035156 30.277344 36.101562 28.863281 35.65625 C 28.007812 35.386719 26.90625 35.027344 25.496094 34.429688 C 19.574219 31.902344 15.707031 26.011719 15.410156 25.625 C 15.117188 25.234375 13 22.464844 13 19.59375 C 13 16.726562 14.523438 15.3125 15.066406 14.730469 C 15.609375 14.144531 16.246094 14 16.640625 14 C 17.035156 14 17.429688 14.003906 17.773438 14.019531 C 18.136719 14.039062 18.625 13.882812 19.101562 15.023438 C 19.59375 16.191406 20.777344 19.058594 20.921875 19.351562 C 21.070312 19.644531 21.167969 19.984375 20.972656 20.375 C 20.777344 20.761719 20.679688 21.007812 20.382812 21.347656 C 20.085938 21.6875 19.761719 22.105469 19.496094 22.367188 C 19.199219 22.660156 18.894531 22.976562 19.238281 23.558594 C 19.582031 24.144531 20.765625 26.050781 22.523438 27.597656 C 24.777344 29.585938 26.679688 30.199219 27.269531 30.492188 C 27.859375 30.785156 28.203125 30.734375 28.550781 30.347656 C 28.894531 29.957031 30.023438 28.644531 30.417969 28.058594 C 30.8125 27.476562 31.203125 27.574219 31.746094 27.769531 C 32.289062 27.960938 35.191406 29.371094 35.78125 29.664062 C 36.371094 29.957031 36.765625 30.101562 36.914062 30.34375 C 37.0625 30.585938 37.0625 31.753906 36.570312 33.117188 Z M 36.570312 33.117188 " />
               </g>
            </svg>
            <span class="q-ml-sm">
               {{ isOk ? 'Proses Pesanan' : 'Lengkapi data untuk Order' }}
            </span>
         </q-btn>
      </q-footer>

   </q-page>
</template>

<script>
import CartOrderDetail from './CartOrderDetail.vue'
import ShippingAddress from './ShippingAddress.vue'
import ReviewOrder from './ReviewOrder.vue'
import VoucherDscount from './VoucherDscount.vue'
export default {
   components: { CartOrderDetail, ShippingAddress, ReviewOrder, VoucherDscount },
   name: 'CheckoutDirectWithShipping',
   data() {
      return {
         isAvailableOldAddress: false,
      }
   },
   computed: {
      carts() {
         return this.$store.getters['cart/getCarts']
      },
      cart_form_order() {
         return this.$store.getters['cart/getChartOrderForm']
      },
      config() {
         return this.$store.state.config
      },
      errors() {
         return this.$store.state.errors
      },
      loading() {
         return this.$store.state.loading
      },
      isOk() {
         if (this.cart_form_order.customer && this.cart_form_order.courier) {
            return true
         }

         return false
      },
      shop() {
         return this.$store.state.shop
      },
   },
   mounted() {
      if (!this.cart_form_order.items.length) {
         this.$router.back()
      }
   },
   created() {
      this.$store.commit('cart/SET_CUSTOMER', null)
      this.$store.commit('cart/SET_PAYMENT', null)
      this.$store.commit('cart/SET_COURIER', null)
   },
   methods: {
      handleBackButton() {
         this.$router.push({ name: 'Cart' })
      },
      formatPhoneNumber(number) {
         let formatted = number.replace(/\D/g, '')

         if (formatted.startsWith('0')) {
            formatted = '62' + formatted.substr(1)
         }

         return formatted;
      },
      getRoutePath(orderRef) {
         let props = this.$router.resolve({
            name: 'UserInvoice',
            params: { order_ref: orderRef },
         });

         return location.origin + props.href;
      },
      formatAddressCod(addr) {
         let arr = addr.split('<br>')
         return arr.join('\n')
      },
      checkout() {
         if (!this.isOk) return

         this.$store.commit('CLEAR_ERRORS')

         if (!this.cart_form_order.customer) {
            let msg = 'Alamat pengiriman belum diisi'
            this.$store.commit('SET_ERRORS', { customer: msg })
            this.jumpTo('shipping_section')
            this.$q.notify({
               type: 'negative',
               message: msg
            })
            return false
         }
         if (!this.cart_form_order.courier) {
            let msg = 'Kurir & ongkos kirim belum dipilih'
            this.$store.commit('SET_ERRORS', { courier: msg })
            this.jumpTo('courier_section')
            this.$q.notify({
               type: 'negative',
               message: msg
            })
            return false
         }


         this.$store.commit('SET_LOADING', true)

         this.$q.loading.show({
            message: 'Order sedang diproses. Silahkan tunggu...',
         })

         this.redirectWhatsapp()

      },
      redirectWhatsapp() {

         let whatsapp = this.formatPhoneNumber(this.shop.phone)

         let str = `Halo kak, saya mau order:\n\n`

         let order = this.cart_form_order

         let numb = 1;
         order.items.forEach(el => {
            str += `*${numb}). ${el.name}*\n`
            if (el.note) {
               str += `${el.note}\n`
            }
            str += `Qty: ${el.quantity}\nHarga (@): ${this.moneyIdr(el.price)}\nHarga Total: ${this.moneyIdr(el.quantity * el.price)}\n\n`
            numb++
         })

         let shipping_cost = order.courier.price
         let shipping_discount = order.shipping_discount
         let voucher_discount = order.voucher_discount
         let service_fee = order.service_fee
         let service_fee_label = this.config.service_fee_label
         let subtotal = order.subtotal
         let billing_total = order.billing_total
         let receiver_name = order.customer.receiver_name
         let receiver_phone = order.customer.receiver_phone
         let full_address = order.customer.full_address
         let courier_name = order.courier.courier_name
         let courier_service_name = order.courier.courier_service_name

         str += `Subtotal Produk: *${this.moneyIdr(subtotal)}*\n`
         str += `Subtotal Ongkir: *${this.moneyIdr(shipping_cost)}*\n`
         if (shipping_discount) {
            str += `Diskon Ongkir: *- ${this.moneyIdr(shipping_discount)}*\n`
         }
         if (service_fee) {
            str += `${service_fee_label}: *${this.moneyIdr(service_fee)}*\n`
         }

         if (voucher_discount) {
            str += `Voucher Diskon: *- ${this.moneyIdr(voucher_discount)}*\n`
         }
         str += `Total Pesanan: *${this.moneyIdr(billing_total)}*\n`
         str += `-----------------------------------\n\n`
         str += `*Nama:*\n ${receiver_name}\n`
         str += `*Telp:*\n ${receiver_phone}\n\n`
         str += `*Alamat:*\n${this.formatAddressCod(full_address)}\n\n`
         str += `Kurir: ${courier_name}\n`
         str += `Servis: ${courier_service_name}\n`

         let link = this.currentWhatsappUrl + '/send?phone=' + whatsapp + '&text=' + encodeURI(str);

         setTimeout(() => {
            this.$store.commit('SET_LOADING', false)
            this.$q.loading.hide()
         }, 1000)

         setTimeout(() => {
            this.$store.dispatch('cart/clearCart', this.currentSessionId)
            this.$router.push('/')
         }, 5000)
         window.open(link, '_blank');
      }
   }
}
</script>