<template>
   <q-page class="checkout-page">
      <q-header class="text-grey-9 bg-white box-shadow">
         <q-toolbar>
            <q-btn @click="goBack" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">Checkout Langsung</q-toolbar-title>
         </q-toolbar>
      </q-header>

      <div id="checkout" v-if="cart_order_form.items.length" ref="top" class="q-pb-md main-content page-sm">
         <div class="cart-content">
            <CartOrderDetail
               source="directCheckout"
               :reset-note-on-mount="false"
               :editable="true"
            ></CartOrderDetail>
         </div>

         <div class="checkout-content">
            <q-banner dense rounded class="bg-red-1 text-negative q-mt-md" v-if="submitError">
               {{ submitError }}
            </q-banner>

            <q-card id="customer_detail" class="section q-mt-md shadow">
               <q-card-section>
                  <div class="q-gutter-y-sm">
                     <div class="card-subtitle">Detail Pelanggan</div>
                     <q-input
                        filled
                        label="Nama Lengkap *"
                        v-model="customer_name"
                        :error="!!fieldErrors.customer_name"
                        :error-message="fieldErrors.customer_name"
                        @update:model-value="validateField('customer_name')"
                     ></q-input>
                     <q-input
                        filled
                        label="Nomor WhatsApp *"
                        v-model="customer_phone"
                        :error="!!fieldErrors.customer_phone"
                        :error-message="fieldErrors.customer_phone"
                        @update:model-value="validateField('customer_phone')"
                     ></q-input>
                     <q-input
                        filled
                        type="email"
                        label="Alamat Email *"
                        v-model="customer_email"
                        :error="!!fieldErrors.customer_email"
                        :error-message="fieldErrors.customer_email"
                        @update:model-value="validateField('customer_email')"
                     ></q-input>
                     <q-input
                        filled
                        type="textarea"
                        rows="3"
                        label="Alamat Pengiriman Lengkap *"
                        v-model="shipping_address"
                        :error="!!fieldErrors.shipping_address"
                        :error-message="fieldErrors.shipping_address"
                        @update:model-value="validateField('shipping_address')"
                     ></q-input>
                  </div>
               </q-card-section>
            </q-card>

            <q-card id="shipping_section" class="section q-mt-md shadow">
               <q-card-section>
                  <div class="card-subtitle q-mb-md">Metode Pengiriman</div>
                  <q-list bordered separator class="rounded-borders overflow-hidden">
                     <q-item
                        v-for="item in shippingOptions"
                        :key="item.id"
                        clickable
                        @click="selectShippingMethod(item)"
                        :class="{ 'bg-green-1': isSelectedShipping(item.id) }"
                        class="q-py-md"
                     >
                        <q-item-section side top>
                           <q-icon
                              size="sm"
                              :name="isSelectedShipping(item.id) ? 'radio_button_checked' : 'radio_button_unchecked'"
                              :color="isSelectedShipping(item.id) ? 'green' : 'grey-8'"
                           ></q-icon>
                        </q-item-section>
                        <q-item-section>
                           <q-item-label class="text-weight-medium">{{ item.courier_name }}</q-item-label>
                           <q-item-label caption>{{ item.courier_service_name }}</q-item-label>
                        </q-item-section>
                        <q-item-section side class="text-weight-medium">
                           {{ moneyIdr(item.price || 0) }}
                        </q-item-section>
                     </q-item>
                  </q-list>
                  <div class="text-red text-xs q-pt-sm" v-if="fieldErrors.shipping_method">
                     {{ fieldErrors.shipping_method }}
                  </div>
               </q-card-section>
            </q-card>

            <PaymentList source="directCheckout" />
            <VoucherDscount source="directCheckout" v-if="!cart_order_form.is_deposit"></VoucherDscount>
            <ReviewOrder source="directCheckout"></ReviewOrder>

            <div class="bg-white q-px-md q-pt-sm q-pb-md q-gutter-y-sm column sticky-bottom q-mt-md">
               <q-btn
                  :disable="loading || !cart_order_form.items.length"
                  @click="submitOrder"
                  no-caps
                  unelevated
                  label="Proses Pesanan"
                  color="primary"
               ></q-btn>
            </div>
         </div>
      </div>

      <q-inner-loading :showing="loading"></q-inner-loading>
   </q-page>
</template>

<script>
import CartOrderDetail from './CartOrderDetail.vue'
import PaymentList from './PaymentList.vue'
import ReviewOrder from './ReviewOrder.vue'
import VoucherDscount from './VoucherDscount.vue'

export default {
   name: 'DirectCheckoutPage',
   components: { CartOrderDetail, PaymentList, ReviewOrder, VoucherDscount },
   data() {
      return {
         submitError: '',
         fieldErrors: {
            customer_name: '',
            customer_phone: '',
            customer_email: '',
            shipping_address: '',
            shipping_method: '',
            payment: '',
         }
      }
   },
   computed: {
      cart_order_form() {
         return this.$store.getters['directCheckout/getChartOrderForm']
      },
      config() {
         return this.$store.state.config
      },
      user() {
         return this.$store.state.user.user
      },
      loading() {
         return this.$store.state.loading
      },
      directCheckoutCustomer() {
         return this.$store.state.directCheckout.customer
      },
      customer_name: {
         get() {
            return this.directCheckoutCustomer.customer_name
         },
         set(value) {
            this.$store.commit('directCheckout/SET_CUSTOMER_FIELD', { key: 'customer_name', value })
         }
      },
      customer_phone: {
         get() {
            return this.directCheckoutCustomer.customer_phone
         },
         set(value) {
            this.$store.commit('directCheckout/SET_CUSTOMER_FIELD', { key: 'customer_phone', value })
         }
      },
      customer_email: {
         get() {
            return this.directCheckoutCustomer.customer_email
         },
         set(value) {
            this.$store.commit('directCheckout/SET_CUSTOMER_FIELD', { key: 'customer_email', value })
         }
      },
      shipping_address: {
         get() {
            return this.directCheckoutCustomer.shipping_address
         },
         set(value) {
            this.$store.commit('directCheckout/SET_CUSTOMER_FIELD', { key: 'shipping_address', value })
         }
      },
      shippingOptions() {
         const zeroCost = 0

         return [
            {
               id: 'WHATSAPP',
               courier_code: 'WHATSAPP',
               courier_name: 'Kirim via WhatsApp',
               courier_service_name: 'Konfirmasi dan pengiriman ke nomor WhatsApp',
               price: zeroCost,
               shipping_type: 'DIGITAL',
            },
            {
               id: 'EMAIL',
               courier_code: 'EMAIL',
               courier_name: 'Kirim via Email',
               courier_service_name: 'Konfirmasi dan detail pesanan dikirim ke email',
               price: zeroCost,
               shipping_type: 'DIGITAL',
            }
         ]
      }
   },
   created() {
      this.$store.dispatch('getConfig')
   },
   mounted() {
      if (!this.cart_order_form.items.length) {
         this.$router.push({ name: 'ProductIndex' })
         return
      }

      this.prefillCustomer()
      this.ensureShippingMethod()
   },
   watch: {
      'cart_order_form.items.length'(value) {
         if (value === 0) {
            this.$router.push({ name: 'ProductIndex' })
         }
      },
      'cart_order_form.payment'(value) {
         this.fieldErrors.payment = value ? '' : this.fieldErrors.payment
      },
      'cart_order_form.courier'(value) {
         this.fieldErrors.shipping_method = value ? '' : this.fieldErrors.shipping_method
      },
      shippingOptions() {
         this.ensureShippingMethod()
      }
   },
   methods: {
      goBack() {
         this.$router.back()
      },
      prefillCustomer() {
         if (!this.user) {
            return
         }

         this.$store.commit('directCheckout/SET_CUSTOMER', {
            customer_name: this.customer_name || this.user.name || '',
            customer_phone: this.customer_phone || this.user.phone || '',
            customer_email: this.customer_email || this.user.email || '',
         })
      },
      ensureShippingMethod() {
         if (!this.cart_order_form.items.length || !this.shippingOptions.length) {
            return
         }

         const current = this.cart_order_form.courier
         const exists = current && this.shippingOptions.some(item => item.id == current.id)
         if (!exists) {
            this.$store.commit('directCheckout/SET_SHIPPING_METHOD', this.shippingOptions[0])
         }
      },
      isSelectedShipping(id) {
         return this.cart_order_form.courier?.id == id
      },
      selectShippingMethod(item) {
         this.$store.commit('directCheckout/SET_SHIPPING_METHOD', item)
         this.validateField('shipping_method')
      },
      validateField(field) {
         const validators = {
            customer_name: () => {
               if (!this.customer_name || this.customer_name.trim().length < 3) {
                  return 'Nama lengkap minimal 3 karakter.'
               }
               return ''
            },
            customer_phone: () => {
               const normalized = String(this.customer_phone || '').replace(/\D/g, '')
               if (!normalized) {
                  return 'Nomor WhatsApp wajib diisi.'
               }
               if (normalized.length < 9) {
                  return 'Nomor WhatsApp tidak valid.'
               }
               return ''
            },
            customer_email: () => {
               if (!this.customer_email) {
                  return 'Alamat email wajib diisi.'
               }
               if (!/^.+@.+\..+$/.test(this.customer_email)) {
                  return 'Format email tidak valid.'
               }
               return ''
            },
            shipping_address: () => {
               if (!this.shipping_address || this.shipping_address.trim().length < 10) {
                  return 'Alamat pengiriman harus diisi lengkap.'
               }
               return ''
            },
            shipping_method: () => {
               if (!this.cart_order_form.courier) {
                  return 'Metode pengiriman belum dipilih.'
               }
               return ''
            },
            payment: () => {
               if (!this.cart_order_form.payment) {
                  return 'Metode pembayaran belum dipilih.'
               }
               return ''
            }
         }

         const message = validators[field] ? validators[field]() : ''
         this.fieldErrors[field] = message
         return !message
      },
      validateForm() {
         this.submitError = ''

         const fields = ['customer_name', 'customer_phone', 'customer_email', 'shipping_address', 'shipping_method', 'payment']
         const invalidField = fields.find(field => !this.validateField(field))

         if (invalidField) {
            this.scrollToSection(invalidField)
            return false
         }

         return true
      },
      scrollToSection(field) {
         const targetMap = {
            customer_name: 'customer_detail',
            customer_phone: 'customer_detail',
            customer_email: 'customer_detail',
            shipping_address: 'customer_detail',
            shipping_method: 'shipping_section',
            payment: 'payment_section',
         }

         const targetId = targetMap[field]
         const element = document.getElementById(targetId)
         if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' })
         }
      },
      generateFormOrder() {
         const data = this.cart_order_form
         const courier = data.courier

         return {
            order_source: 'direct_checkout',
            product_type: data.product_type,
            customer_name: this.customer_name,
            customer_phone: this.customer_phone,
            customer_email: this.customer_email,
            shipping_address: this.shipping_address,
            shipping_type: courier?.shipping_type || 'DIGITAL',
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
            shipping_courier_id: courier?.courier_code || null,
            shipping_courier_name: courier?.courier_name || null,
            shipping_courier_service: courier?.courier_service_name || null,
            shipping_cost: data.shipping_cost,
            voucher_discount: data.voucher_discount,
            shipping_discount: data.shipping_discount,
            voucher_id: data.voucher ? data.voucher.id : null,
            customer_note: data.customer_note,
            shipping_coordinate: '',
         }
      },
      submitOrder() {
         if (!this.validateForm()) {
            return
         }

         const payload = this.generateFormOrder()
         this.submitError = ''

         this.$q.loading.show()
         this.$store.dispatch('order/storeOrder', payload)
            .then((response) => {
               if (response.status == 200) {
                  this.$store.dispatch('directCheckout/clearCheckout')
                  this.$router.push({
                     name: 'UserInvoice',
                     params: { order_ref: response.data.data.order_ref },
                     query: { pay: true }
                  })
               }
            })
            .catch((error) => {
               const message = error?.response?.data?.message || 'Gagal memproses checkout, silahkan coba lagi.'
               this.submitError = message

               if (message && (message.startsWith('Kuota') || message.startsWith('Masa'))) {
                  this.$store.commit('directCheckout/SET_VOUCHER', null)
               }
            })
            .finally(() => {
               this.$q.loading.hide()
            })
      }
   },
   meta() {
      return {
         title: 'Checkout Langsung'
      }
   }
}
</script>
