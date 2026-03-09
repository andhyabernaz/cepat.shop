<template>
   <div v-if="invoice">
      <div class="flex justify-center no-print q-mb-sm">
         <q-btn no-caps label="Print Invoice" @click="printInvoice" color="primary"></q-btn>
      </div>
      <div style="background-color: #ffffff" class="print">
         <div class="flex q-gutter-x-xs">
            <img :src="shop.logo" ratio="1" v-if="shop.logo" height="40" />
            <div style="font-size: 1.6rem; font-weight: 500">{{ shop.name }}</div>
         </div>
         <hr />
         <div style="
               display: flex;
               justify-content: space-between;
               align-items: start;
               margin-bottom: 10px;
             ">
            <table>
               <tbody>
               <tr>
                  <td style="padding: 1px 5px" align="left">No. Pesanan</td>
                  <td style="padding: 1px 5px">:</td>
                  <td style="padding: 1px 5px" align="left">
                     {{ invoice.order_ref }}
                  </td>
               </tr>
               <tr>
                  <td style="padding: 1px 5px" align="left">Tanggal</td>
                  <td style="padding: 1px 5px">:</td>
                  <td style="padding: 1px 5px">
                     {{ dateFormat(invoice.created_at) }}
                  </td>
               </tr>

               <tr v-if="invoice.shipping_courier_code">
                  <td style="padding: 1px 5px" align="left">No Resi</td>
                  <td style="padding: 1px 5px">:</td>
                  <td style="padding: 1px 5px">
                     {{ invoice.shipping_courier_code }}
                  </td>
               </tr>
               <tr>
                  <td style="padding: 1px 5px" align="left">Total Pesanan</td>
                  <td style="padding: 1px 5px">:</td>
                  <td style="padding: 1px 5px">
                     Rp {{ moneyFormat(invoice.billing_total) }}
                  </td>
               </tr>
               </tbody>
            </table>
            <div v-if="qrData" class="">
               <img :src="qrData" width="95" height="95" />
            </div>
         </div>
         <hr />
         <table>
            <tbody>
            <tr>
               <td class="" style="width: 50%; vertical-align: baseline">
                  <div style="margin-bottom: 4px; font-weight: 500">Pengirim</div>
                  <div style="font-weight: 600">{{ shop.name }}</div>
                  <div>{{ shop.phone }}</div>
                  <div v-html="shop.address" style="margin-top: 4px"></div>
               </td>

               <td class="" style="width: 50%; vertical-align: baseline">
                  <div style="margin-bottom: 4px; font-weight: 500">Penerima</div>
                  <div style="font-weight: 600">{{ invoice.customer_name }}</div>
                  <div>{{ invoice.customer_whatsapp }}</div>
                  <div v-html="invoice.shipping_address" style="margin-top: 4px"></div>
               </td>
            </tr>
            </tbody>
         </table>
         <hr />
         <div style="margin-top: 8px">
            <div style="font-weight: 500; margin-bottom: 5px; margin-top: 5px">
               Detil Pesanan:
            </div>
            <table v-if="invoice.items" style="width: 100%" class="table-order-item">
               <tbody>
               <tr>
                  <th align="left">Item</th>
                  <th align="right">qty</th>
               </tr>
               <tr v-for="(item, index) in invoice.items" :key="index">
                  <td>
                     <div>{{ item.name }}</div>
                     <div>{{ item.note }}</div>
                  </td>
                  <td align="right">{{ item.quantity }}</td>
               </tr>
               </tbody>
            </table>
         </div>
      </div>

   </div>
</template>

<script>
import QRCode from "qrcode";
export default {
   name: "InvoiceIndex",
   data() {
      return {
         qrData: "",
         invoice: null
      };
   },

   mounted() {
      this.getOrder();
   },

   computed: {
      shop() {
         return this.$store.state.shop
      },
      config() {
         return this.$store.state.config
      }
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
      getRoutePath() {
         let props = this.$router.resolve({
            name: "UserInvoice",
            params: { order_ref: this.invoice.order_ref },
         });

         return location.origin + props.href;
      },

      getOrder() {
         this.$q.loading.show()
         if (this.$route.params.order_ref) {
            this.$store.dispatch('order/getInvoice', this.$route.params.order_ref)
               .then((res) => {
                  this.invoice = res.data.data
                  this.generateQr()
               }).finally(() => {
                  this.$q.loading.hide()
               })

         }
      },
      printInvoice() {
         if (!this.qrData) {
            this.generateQr();
         }

         let today = new Date();
         today = this.dateFormat(today, { weekday: 'long' })
         let title = `INVOICE #${this.invoice.order_ref} ${today}`;
         document.title = title

         this.$q.loading.hide()
         setTimeout(() => {
            window.print();
         }, 500)

      },
   },
};
</script>

<style lang="scss">
.print {
   width: 100%;
   padding: 10px;
   max-width: 600px;
   display: block;
   margin: 0 auto;
   // max-width: 600px;
}


@media print {
   .no-print {
      display: none;
   }

   .print {
      padding: 0px;
      margin-left: 0 !important;
      max-width: 800px;
      display: block;
      // max-width: 600px;
      font-size: 16pt;
   }

   .table-order-item {
      width: 100%;
      border-spacing: inherit;

      tr {

         th,
         td {
            padding: 0.5rem;
         }

         th {
            background-color: #dddddd;
            border-top: 1px solid #cfcfcf;
            border-bottom: 1px solid #cfcfcf;
         }

         td {
            border-bottom: 1px solid #cfcfcf;
         }
      }
   }

}
</style>