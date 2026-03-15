<template>
   <q-page class="q-pb-md bg-grey-2">
      <q-header class="no-print box-shadow bg-white text-dark">
         <q-toolbar>
            <q-btn @click="goBack" flat round dense icon="eva-arrow-back" />
            <q-toolbar-title class="text-weight-bold brand">
               <span>Rincian Pesanan</span>
            </q-toolbar-title>
            <q-btn v-if="invoice && invoice.can_review" label="Beri Ulasan" color="accent" :to="{
               name: 'OrderProductReview',
               params: { invoice_ref: invoice.order_ref },
            }"></q-btn>
         </q-toolbar>
      </q-header>
      <div v-if="invoice">
         <q-card dark class="bg-teal" square flat>
            <q-card-section>
               <div class="q-pa-sm flex justify-between items-center no-wrap">
                  <div>
                     <div class="text-md text-weight-bold q-mb-sm">
                        {{ invoice.customer_status.title }}
                     </div>
                     <div style="max-width: 500px">{{ invoice.customer_status.desc }}</div>
                  </div>
                  <div class="q-pa-sm">
                     <q-icon :name="invoice.customer_status.icon" size="lg" color="grey-2"></q-icon>
                  </div>
               </div>
            </q-card-section>
         </q-card>
         <div v-if="invoice">
            <InvoiceDetail :invoice="invoice"></InvoiceDetail>
         </div>

         <q-footer class="bg-white q-pa-md" v-if="invoice.order_status == 'PENDING'">
            <div class="text-center text-grey-7 column q-gutter-y-sm">
               <UploadPaymentProof :invoice="invoice"></UploadPaymentProof>
               <q-btn v-if="shop && shop.phone" ref="chatAdmin" label="Chat admin" name="eva-message-circle" no-caps
                  @click="chatToAdmin" color="blue-7"></q-btn>
            </div>
         </q-footer>
      </div>
      <q-inner-loading :showing="loading" class="no-print"> </q-inner-loading>
   </q-page>
</template>

<script>
import { mapState } from "vuex";
import { copyToClipboard } from "quasar";
import QRCode from "qrcode";
import InvoiceDetail from "components/InvoiceDetail.vue";
import UploadPaymentProof from './UploadPaymentProof.vue'
import { formatPhoneNumber } from "src/utils";
export default {
   name: "InvoiceIndex",
   components: { InvoiceDetail, UploadPaymentProof },
   data() {
      return {
         modalPayment: false,
         throtle: 1,
         interval: null,
         timeout: null,
         requestCount: 1,
         qrData: "",
         autoShowModal: false,
         pollingInterval: null,
         sse: null,
         usingSSE: false
      };
   },
   computed: {
      ...mapState({
         loading: (state) => state.loading,
         shop: (state) => state.shop,
         invoice: (state) => state.order.invoice,
         config: (state) => state.config,
         user: (state) => state.user.user
      }),
      isPaymentType: function () {
         let paymentType = this.invoice.transaction.payment_type;
         if (paymentType == "DIRECT" || paymentType == "DIRECT_TRANSFER") {
            return "DirectPayment";
         }
         return "PaymentGateway";
      },
      status_description() {
         let str = "";
         if (this.invoice) {
            if (this.invoice.order_status == "PENDING") {
               str = `Segera lakukan pembayaran sebelum ${this.formatDateFromTimestamp(
                  this.invoice.transaction.expired_time
               )}`;
            }

            if (this.invoice.order_status == "TOSHIP") {
               str = "Pesanan sedang dikemas menunggu diserahkan ke ekspedisi.";
            }
            if (this.invoice.order_status == "SHIPPING") {
               str = "Pesanan sedang dalam pengiriman";
               if (this.invoice.transaction.payment_type == "COD") {
                  str += ", Silahkan lakukan pembayaran saat terima pesanan.";
               }
            }

            if (this.invoice.order_status == "COMPLETE") {
               str = "Pesanan selesai, terimakasih sudah berbelanja.";
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
      title() {
         if (this.invoice) {
            return `Tagihan ${this.invoice.order_ref}`;
         }
         return "Tagihan";
      },
      shop() {
         return this.$store.state.shop
      },
   },
   created() {
      this.getData();
   },
   methods: {
      generateQr() {
         let opts = {
            errorCorrectionLevel: "H",
            type: "image/jpeg",
            quality: 0.3,
            margin: 1,
         };
         QRCode.toDataURL(this.getRoutePath(), opts, (err, url) => {
            if (err) throw err;

            this.qrData = url;
         });
      },
      chatToAdmin() {
         if (this.shop.phone) {
            let text = `Halo ${this.shop.name},\nMohon pesanan saya untuk segera di proses.\nTerima Kasih.\n\nReferensi Order:\n${location.href}`;
            let url = `${this.currentWhatsappUrl
               }/send?phone=${formatPhoneNumber(
                  this.shop.phone
               )}&text=${encodeURI(text)}`;

            window.open(url, "_blank");
         }
      },
      getRoutePath() {
         let props = this.$router.resolve({
            name: "UserInvoice",
            params: { order_ref: this.invoice.order_ref },
         });

         return location.origin + props.href;
      },
      getData() {
         if (this.$route.params.order_ref) {
            this.$store.commit("order/REMOVE_INVOICE");
            this.$store.commit("SET_LOADING", true);
            this.$store
               .dispatch("order/getInvoice", this.$route.params.order_ref)
               .then((response) => {
                  let inv = response.data.data
                  this.$store.commit("order/SET_INVOICE", inv);

                  if (!this.qrData) {
                     setTimeout(() => {
                        this.generateQr();
                     }, 1000);
                  }
               })
               .finally(() => {
                   this.$store.commit("SET_LOADING", false);
               })
               .catch(() => {
                  this.$router.push({ name: "Cart" });
               });
         } else {
            this.$router.push({ name: "Cart" });
         }
      },
      goBack() {
         if (this.$route.query._rdr) {
            this.$router.push(this.$route.query._rdr);
         } else {
            if (this.user) {
               this.$router.push({ name: "CustomerDashboard" });
            } else {
               this.$router.push("/");
            }
         }
      },
      copy(str) {
         copyToClipboard(str)
            .then(() => {
               this.$q.notify({
                  type: "positive",
                  message: "Berhasil menyalin data",
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
      closeModalPayment() {
         this.modalPayment = false;
         this.autoShowModal = false;
      },
   },
   meta() {
      return {
         title: this.title,
         meta: {
            ogTitle: { property: "og:title", content: this.title },
         },
      };
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
         background-color: #666;
         color: #ccc;
      }

      td {
         border-bottom: 1px solid #eee;
      }
   }
}

.print-packing,
.print-invoice {
   display: none;
}

@media print {
   .no-print {
      display: none;
   }

   .print-packing,
   .print-invoice {
      display: block;
   }

   .border {
      border: 1px solid #eee;
   }

   .table-order-item {
      width: 100%;
      border-spacing: inherit;

      tr {

         th,
         td {
            padding: none;
            border: 1px solid #eee;
         }
      }
   }
}
</style>