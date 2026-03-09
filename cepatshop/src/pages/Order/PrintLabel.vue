<template>
   <div v-if="invoice">
      <div class="flex justify-center no-print q-mb-sm">
         <q-btn no-caps label="Print Invoice" @click="printInvoice" color="primary"></q-btn>
      </div>
      <div style="background-color: #ffffff" class="print">
         <div class="flex q-gutter-x-xs">
            <img :src="shop.logo" ratio="1" v-if="shop.logo" height="40" />
            <!-- <div style="font-size: 1.5rem; font-weight: 500">{{ shop.name }}</div> -->
         </div>
         <hr />
         <div class="invoice_detail" style="
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

                  <tr>
                     <td style="padding: 1px 5px" align="left">Total Pesanan</td>
                     <td style="padding: 1px 5px">:</td>
                     <td style="padding: 1px 5px">
                        Rp {{ moneyFormat(invoice.billing_total) }}
                     </td>
                  </tr>

                  <tr v-if="invoice.shipping_courier_code">
                     <td style="padding: 1px 5px" align="left">No Resi</td>
                     <td style="padding: 1px 5px">:</td>
                     <td style="padding: 1px 5px">
                        {{ invoice.shipping_courier_code }}
                     </td>
                  </tr>
               </tbody>
            </table>
            <div v-if="qrData" class="">
               <img :src="qrData" width="95" height="95" />
            </div>
         </div>

         <div v-if="invoice.shipping_courier_name || invoice.shipping_courier_service" class="courier_detail">

            <hr />
            <table>
               <tbody>


                  <tr v-if="invoice.shipping_courier_name">
                     <td style="padding: 1px 5px" align="left">Kurir</td>
                     <td style="padding: 1px 5px">:</td>
                     <td style="padding: 1px 5px">
                        {{ invoice.shipping_courier_name }}
                     </td>
                  </tr>
                  <tr v-if="invoice.shipping_courier_service">
                     <td style="padding: 1px 5px" align="left">Servis</td>
                     <td style="padding: 1px 5px">:</td>
                     <td style="padding: 1px 5px">
                        {{ invoice.shipping_courier_service }}
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <hr />

         <div class="shipper_detail">
            <table>
               <tbody>
                  <tr>
                     <td  style="width: 50%; vertical-align: baseline">
                        <div style="margin-bottom: 4px; font-weight: 500">Pengirim</div>
                        <div style="font-weight: 600">{{ shop.name }}</div>
                        <div>{{ shop.phone }}</div>
                        <div class="text-uppercase" v-html="shop.address" style="margin-top: 4px"></div>
                     </td>

                  </tr>
               </tbody>
            </table>

         </div>

           <hr />

         <div class="customer_detail">
            <table>
               <tbody>

                  <tr>

                     <td style="width: 50%; vertical-align: baseline">
                        <div style="margin-bottom: 4px; font-weight: 500">Penerima</div>
                        <div style="font-weight: 600">{{ invoice.customer_name }}</div>
                        <div>{{ invoice.customer_whatsapp }}</div>
                        <div class="text-uppercase" v-html="invoice.shipping_address" style="margin-top: 4px"></div>
                     </td>
                  </tr>
               </tbody>
            </table>

         </div>

         <hr />
         <div style="margin-top: 8px" class="item_detail">
            <div style="font-weight: 500; margin-bottom: 5px; margin-top: 5px">
               Detil Pesanan:
            </div>
            <table v-if="invoice.items" style="width: 100%">
               <tbody>


                  <tr>
                     <th style="color:#555555" align="left">Item</th>
                     <th style="color:#555555" align="right">qty</th>
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

         <div v-if="barcode.code">
            <hr />
            <div class="flex justify-center items-center">
               <div>
                  <VueBarcode :value="barcode.code" :options="barcode.options" style="max-width:450px">
                     Can't load barcode
                  </VueBarcode>
               </div>
            </div>
         </div>

      </div>


   </div>
</template>

<script>
import QRCode from "qrcode";
import VueBarcode from '@chenfengyuan/vue-barcode';
export default {
   name: "InvoiceIndex",
   components: { VueBarcode },
   data() {
      return {
         qrData: "",
         invoice: null,
         barcode: {
            code: null,
            options: {
               height: 70,
               width: 2,
               format: "CODE128",
               fontSize: 17
            }
         }
      };
   },

   created() {
      this.getOrder();
   },

   watch: {
      '$route.params.order_ref'() {
         this.getOrder();
      }
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
            params: { order_ref: this.$route.params.order_ref },
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
                  if (this.invoice.shipping_courier_code) {
                     this.barcode.code = this.invoice.shipping_courier_code
                  }
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
   padding: 10px;
   width: 600px;
   display: block;
   margin: 0 auto;
   // border: 1px solid;
   font-size: 17px;
}  

.item_detail {
   tr th,
   tr td {
      border-bottom: 1px solid;
   }
}

@media print {
   .no-print {
      display: none;
   }

   .print {
      width: 100%;
      font-size: 17pt;
   }

}
</style>