<template>
   <q-page class="q-pb-lg bg-grey-1" padding>
      <AppHeader title="Rincian Pesanan" goBack>
         <div class="flex q-gutter-sm">
            <q-btn v-if="$can('accept-payment-order') && invoice && invoice.can_confirm_payment" no-caps color="green" label="Konfirmasi Pembayaran"
               @click="handleConfirmationOrder">
            </q-btn>
            <q-btn no-caps color="teal" v-if="invoice" icon="print" @click="printInvoice"
               label="Print"></q-btn>

         </div>
      </AppHeader>
      <div v-if="invoice" class="no-print">
         <q-card dark class="bg-teal" square flat>
            <q-card-section>
               <div class="q-pa-sm flex justify-between items-center no-wrap">
                  <div>
                     <div class="text-md text-weight-bold q-mb-sm">
                        {{ invoice.admin_status.title }}
                     </div>
                     <div style="max-width: 500px">{{ invoice.admin_status.desc }}</div>
                  </div>
                  <div class="q-pa-sm">
                     <q-icon :name="invoice.admin_status.icon" size="lg" color="grey-2"></q-icon>
                  </div>
               </div>
            </q-card-section>
         </q-card>
         <InvoiceDetail :invoice="invoice"></InvoiceDetail>
      </div>

   </q-page>
</template>

<script>
import { copyToClipboard } from "quasar";
import { mapActions, mapState } from "vuex";
import InvoiceDetail from "components/InvoiceDetail.vue";
export default {
   name: "InvoiceIndex",
   components: { InvoiceDetail },
   data() {
      return {
         modalPayment: false,
         throtle: 1,
         timeout: null,
         interval: null
      };
   },
   computed: {
      ...mapState({
         loading: (state) => state.loading,
         shop: (state) => state.shop,
         invoice: (state) => state.order.invoice,
         config: (state) => state.config,
      }),
      isPaid() {
         return this.invoice.transaction.status == "PAID" ? true : false;
      },
      isPaymentType: function () {
         return this.invoice.transaction.payment_type == "DIRECT"
            ? "DirectPayment"
            : "PaymentGateway";
      },
      status_description() {
         let str = "";
         if (this.invoice) {
            if (this.invoice.order_status == "PENDING") {
               str = `Menunggu konsumen melakukan pembayaran, pembayaran akan kadaluarsa pada ${this.formatDateFromTimestamp(
                  this.invoice.transaction.expired_time
               )}`;
            }

            if (this.invoice.order_status == "TOSHIP") {
               str = `Segera kemas pesanan dan kirim ke konsumen via ${this.invoice.shipping_courier_name}`;
            }
            if (this.invoice.order_status == "SHIPPING") {
               str = "Pesanan sedang dalam pengiriman";
               if (this.invoice.transaction.payment_type == "COD") {
                  str += ", Konsumen harus membayar pesanan ketika pesanan tiba.";
               }
            }

            if (this.invoice.order_status == "COMPLETE") {
               str = "Pesanan selesai.";
            }
            if (this.invoice.order_status == "CANCELED") {
               str = "Pesanan dibatalkan";

               if (this.invoice.cancellation_reason) {
                  str += ` :  ${this.invoice.cancellation_reason}`
               }
            }
         }
         return str;
      },
   },
   created() {
      this.getData();
   },

   methods: {
      ...mapActions("order", ["shippingWaybill", 'acceptPayment']),
      getPrintPath() {
         let props = this.$router.resolve({
            name: "OrderPrintLabel",
            params: { order_ref: this.invoice.order_ref },
         });

         return location.origin + props.href;
      },
      handleConfirmationOrder() {
         let msg = "Pastikan pembayaran telah anda terima dengan baik";
         let title = "Konfirmasi";
         if (this.invoice.transaction.payment_type == "PAYMENT_GATEWAY") {
            msg =
               "<b>WARNING!</b>, Metode pembayaran menggunakan payment gateway, Seharusnya status pesanan dan status pembayaran akan <b>otomatis</b> berubah jika sudah terjadi pembayaran di pihak payment gateway. Sangat tidak disarankan untuk merubah status secara manual";

            title = "Konfirmasi Manual";
         }
         const that = this
         this.$q
            .dialog({
               title: "Konfirmasi pembayaran",
               message: msg,
               cancel: true,
               ok: { label: title, flat: true },
               html: true,
            })
            .onOk(() => {
               this.acceptPayment(this.invoice.id).then(() => {
                  that.getData()
                  localStorage.setItem('load_order', 1)
               });
            })
      },
      printInvoice() {
         window.open(this.getPrintPath(), 'new')
      },
      getData() {
         this.$store.commit("SET_LOADING", true);
         if (this.$route.params.id) {
            this.$store.dispatch('order/getOrder', this.$route.params.id)
               .then((response) => {
                  if (response.status == 200) {
                     this.$store.commit("order/SET_INVOICE", response.data.data);
                  }
                  this.$store.commit("SET_LOADING", false);
               })
               .catch(() => {
                  // this.$router.push({ name: "Cart" });
               });
         } else {
            // this.$router.push({ name: "Cart" });
         }
      },
      copy(str) {
         copyToClipboard(str)
            .then(() => {
               this.$q.notify({
                  type: "positive",
                  message: "Berhasil menyalin",
               });
            })
            .catch(() => {
               this.$q.notify({
                  type: "negative",
                  message: "Browser anda tidak support copy to clipboard",
               });
            });
      },
      handlePaymentModal() {
         this.modalPayment = true;
      },
      copyAddress() {
         let addr = `${this.invoice.customer_name}\n${this.invoice.customer_whatsapp}\n${this.invoice.shipping_address}`;
         this.copy(addr);
      },

   },
   beforeUnmount() {
      clearTimeout(this.timeout);
      clearInterval(this.interval);
   },
};
</script>

<style lang="scss">
.table-order-item {
   width: 100%;
   border-spacing: inherit;

   tr {

      th,
      td {
         padding: 0.5rem;
      }

      th {
         background-color: rgb(151, 250, 196);
      }

      td {
         border-bottom: 1px solid #eee;
      }
   }
}

.print {
   display: none;
   width: 100%;
   padding: 0px;
   margin-left: 0 !important;
   font-size: 1rem;
}

@media print {
   .no-print {
      display: none;
   }

   .print {
      display: block;
      // max-width: 600px;
      font-size: 16pt;
   }

   .border {
      border: 1px solid #eee;
   }
}
</style>