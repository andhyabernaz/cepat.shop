<template>
   <q-page padding>
      <AppHeader title="List Pesanan">
         <q-btn v-if="$can('export-order')" color="white" text-color="dark" icon="download" label="Export"
            @click="exportModal = true"></q-btn>
      </AppHeader>
      <div class="box-shadow bg-white text-dark q-mb-sm">
         <q-tabs v-model="queryParams.status" active-color="primary" outside-arrows mobile-arrows
            @update:model-value="changeTab">
            <q-tab v-for="option in order_menu" :key="option.value" :name="option.value" :label="option.label"
               no-caps></q-tab>
         </q-tabs>
      </div>
      <div class="q-mb-sm">
         <div class="q-gutter-x-sm">
            <q-input v-model="queryParams.search" placeholder="No invoice, nama, email atau ponsel customer" dense
               clearable outlined @keypress.enter="handleSearchOrder" @clear="clearSearch" class="bg-white">
               <template v-slot:append>
                  <q-icon name="eva-search" class="cursor-pointer" @click="handleSearchOrder"></q-icon>
               </template>
            </q-input>
         </div>
      </div>
      <div v-if="orders.total">
         <q-card class="q-mb-md shadow" v-for="(order, index) in orders.data" :key="index" square flat>
            <q-card-section>
               <div class="flex justify-between items-start q-pb-sm">
                  <div class="q-pt-xs" style="font-size:16px;font-weight:500;">#{{ orders.from + index }} {{
                     order.order_ref
                     }}</div>
                  <q-btn icon="more_vert" unelevated round color="grey-1" text-color="dark" padding="6px">
                     <q-menu auto-close>
                        <q-list separator>
                           <q-item class="" unelevated no-caps padding="6px 12px" size="12px" color="purple" :to="{
                              name: 'OrderDetail',
                              params: { id: order.id },
                           }">
                              <q-item-section>Detail Pesanan</q-item-section>
                           </q-item>

                           <q-item clickable class="" unelevated no-caps padding="6px 12px" size="12px"
                              v-if="order.can_confirm_payment && $can('accept-payment-order')" color="blue"
                              @click="handleConfirmationOrder(order)">
                              <q-item-section>Konfirmasi Pembayaran</q-item-section>
                           </q-item>

                           <q-item v-if="$can('follow-up-order')" clickable class="" unelevated no-caps
                              padding="6px 12px" size="12px" @click="handleFollowUp(order)" color="amber-9">
                              <q-item-section>{{
                                 messageButtonLabel(order.order_status)
                                 }}</q-item-section>
                           </q-item>

                           <q-item v-if="order.transaction.payment_proof" clickable class="" unelevated no-caps
                              padding="6px 12px" size="12px" color="grey-8" @click="
                                 lihatBuktiTransfer(order.transaction.payment_proof_url)
                                 ">
                              <q-item-section>Lihat Bukti Transfer</q-item-section>
                           </q-item>
                           <q-item clickable class="" unelevated no-caps padding="6px 12px" size="12px" color="grey-8"
                              @click="copyString(getInvoiceRoutePath(order.order_ref))">
                              <q-item-section>Salin Invoice Link</q-item-section>
                           </q-item>
                           <q-item clickable class="" unelevated no-caps padding="6px 12px" size="12px"
                              v-if="order.can_completed && $can('finish-order')" color="green"
                              @click="handleCompletionOrder(order)">
                              <q-item-section>Selesaikan Pesanan</q-item-section>
                           </q-item>
                           <q-item clickable class="" unelevated no-caps padding="6px 12px" size="12px"
                              v-if="order.can_cancel_order && $can('cancel-order')" color="red"
                              @click="handleCancelOrder(order)">
                              <q-item-section>Batalkan Pesanan</q-item-section>
                           </q-item>
                           <q-item clickable class="" unelevated no-caps padding="6px 12px" size="12px"
                              v-if="order.can_delete_order && $can('delete-order')" color="red"
                              @click="handleDeleteOrder(order)">
                              <q-item-section>Hapus Pesanan</q-item-section>
                           </q-item>
                        </q-list>
                     </q-menu>
                  </q-btn>
                  <!-- <q-btn-dropdown auto-close label="Action" color="blue" padding="3px 10px" no-caps>
                           </q-btn-dropdown> -->
               </div>
               <div>
                  <table class="dense">
                     <tbody>
                        <tr>
                           <td>Produk Tipe</td>
                           <td>
                              <q-badge class="text-nowrap" :color="getProductTypeCOlor(order.product_type)">
                                 {{ order.product_type }}
                              </q-badge>
                           </td>
                        </tr>

                        <tr>
                           <td>Tanggal</td>
                           <td>{{ dateFormat(order.created_at, { hour: 'numeric', minute: 'numeric' }) }}</td>
                        </tr>
                        <tr>
                           <td>Customer</td>
                           <td>{{ order.customer_name }}</td>
                        </tr>
                        <tr>
                           <td>Total</td>
                           <td>{{ moneyIdr(order.billing_total) }}</td>
                        </tr>

                        <tr>
                           <td>Status Order</td>
                           <td>
                              <q-badge :color="getOrderStatusColor(order.order_status)">{{
                                 order.admin_status.label
                              }}</q-badge>
                           </td>
                        </tr>

                        <tr>
                           <td>Status Pembayaran</td>
                           <td>
                              <q-badge :color="getOrderStatusColor(order.transaction.status)">
                                 {{ order.transaction.status_label }}
                              </q-badge>
                           </td>
                        </tr>
                        <tr>
                           <td>Metode Pembayaran</td>
                           <td>
                              {{ order.transaction.payment_type.split('_').join(' ') }}
                           </td>
                        </tr>
                        <tr v-if="['PENDING', 'TOSHIP'].includes(order.order_status)">
                           <td>Expired At</td>
                           <td>{{ dateFormat(order.expired_at, { hour: 'numeric', minute: 'numeric' }) }}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </q-card-section>
         </q-card>
      </div>
      <div class="q-my-md">
         <SimplePagination v-bind="orders" @loadUrl="loadmore" autoHide></SimplePagination>
      </div>

      <div v-if="!orders.total" class="text-center q-pt-xl">
         Tidak ada data
      </div>
      <q-dialog v-model="followUpModal" persistent>
         <follow-up @close="followUpModal = false" :order="currentOrder" />
      </q-dialog>

      <q-dialog v-model="exportModal" position="right">
         <q-card class="card-md box-shadow" square style="width: 320px">
            <div class="flex q-px-md q-py-sm">
               <q-toolbar-title>Export Order</q-toolbar-title>
               <q-space></q-space>
               <q-btn flat round dense icon="close" v-close-popup />
            </div>
            <q-separator></q-separator>
            <div class="q-px-md q-pb-md q-pt-sm q-gutter-y-sm">
               <q-select v-model="exportForm.status" :options="order_menu" emit-value map-options
                  label="Order Status"></q-select>
               <q-input stack-label type="date" v-model="exportForm.start_date" label="Start Date"></q-input>
               <q-input stack-label type="date" v-model="exportForm.end_date" label="End Date"></q-input>
            </div>
            <div class="q-pa-md">
               <q-btn label="Export" class="full-width" color="primary" @click="exportData"></q-btn>
            </div>
         </q-card>
      </q-dialog>
      <q-dialog v-model="buktiModal">
         <img :src="buktiTransferImg" class="payment-proof" />
      </q-dialog>
   </q-page>
</template>

<script>
import { mapState, mapActions } from "vuex";
import FollowUp from "./FollowUp.vue";
import { BaseApi } from "boot/axios";
import SimplePagination from "components/SimplePagination.vue";
export default {
   name: "OrderIndex",
   components: { FollowUp, SimplePagination },
   data() {
      return {
         isFilter: true,
         exportModal: false,
         buktiModal: false,
         buktiTransferImg: null,
         exportForm: {
            status: "ALL",
            start_date: "",
            end_date: "",
         },
         followUpModal: false,
         currentOrder: null,
         queryParams: {
            search: "",
            status: this.$route.query.status ?? "ALL",
            per_page: 10,
         },
      };
   },
   computed: {
      ...mapState({
         orders: (state) => state.order.orders,
      }),
      isMobile() {
         return window.innerWidth <= 800;
      },
      order_menu() {
         return this.$store.state.order.order_menu
      }
   },
   created() {
      if (!this.orders.total || localStorage.getItem('load_order')) {
         if (this.$route.query.status) {
            this.queryParams.status = this.$route.query.status;
         } else if (localStorage.getItem("order_filter_v4")) {
            this.queryParams.status = localStorage.getItem("order_filter_v4");
         }
         this.getData();

         localStorage.removeItem('load_order')
      }
      if (!this.order_menu.length) {
         this.getStatusOptions()
      }
   },
   mounted() {
      this.$canAccess('view-order')
   },
   methods: {
      ...mapActions("order", [
         "getOrders",
         "destroyOrder",
         "acceptPayment",
         "updateStatusOrder",
         "cancelOrder",
         "destroyOrder",
         'getStatusOptions',
         'completionOrder'
      ]),
      getProductTypeCOlor(type) {
         if (type.includes('Digital')) {
            return 'purple'
         }
         if (type.includes('Deposit')) {
            return 'amber-9'
         }
         return 'teal'
      },
      loadmore(url) {
         this.getData(url);
      },
      getData(url = null) {
         this.$store.commit('SET_LOADING', true)
         if (!url) {
            url = `orders?${new URLSearchParams(this.queryParams).toString()}`;
         }
         this.getOrders(url);
      },
      lihatBuktiTransfer(img) {
         this.buktiTransferImg = img;
         this.buktiModal = true;
      },
      clearSearch() {
         this.queryParams.status = "ALL";
         localStorage.setItem("order_filter", "ALL");
         this.changeTab("ALL");
      },
      changeTab(status) {
         this.queryParams.search = "";
         this.queryParams.status = status;
         this.$router.replace({ name: "OrderIndex", query: { status: status } });
         this.getData();
      },
      getInvoiceRoutePath(ref) {
         let props = this.$router.resolve({
            name: "UserInvoice",
            params: { order_ref: ref },
         });

         return location.origin + props.href;
      },
      handleUpdateStatusOrder() {
         this.$store.commit("SET_LOADING", true);
         this.updateStatusOrder(this.form).then(() => {
            this.getData();
         });
      },
      handleCancelOrder(order) {
         let msg = "Perubahan ini tidak dapat dikembalikan"
         if (order.transaction.payment_type == 'SALDO_BALANCE') {
            msg += " dan dana akan dikembalikan ke pembeli"
         }
         msg += ", Silahkan input alasan pembatalan"
         this.$q
            .dialog({
               title: "Konfirmasi Pembatalan order",
               message: msg,
               cancel: true,
               prompt: {
                  model: '',
                  type: 'text', // optional
                  isValid: val => val.length >= 5, // << here is the magic
               },
            })
            .onOk((data) => {
               let params = {
                  order_id: order.id,
                  cancellation_reason: data
               }
               this.cancelOrder(params).then(() => {
                  this.getData();
               });
            });
      },
      handleCompletionOrder(order) {
         this.$q
            .dialog({
               title: "Konfirmasi",
               message:
                  'Ini akan merubah status pesanan menjadi "selesai" dan status pembayaran jadi "Dibayar" apabila menggunakan pembayaran COD/CASH',
               cancel: true,
            })
            .onOk(() => {
               this.completionOrder(order.id).then(() => {
                  this.getData()
               })
            });
      },
      handleSearchOrder() {
         if (this.queryParams.search) {
            this.queryParams.status = "ALL";
            this.$router.replace({ name: "OrderIndex" });
            this.getData();
         }
      },
      messageButtonLabel(status) {
         if (status == "PENDING") return "Follow Up";
         return "Pesan Whatsapp";
      },
      handleDeleteOrder(order) {
         this.$q
            .dialog({
               title: "Yakin akan menghapus data?",
               message: "data yang dihapus tidak dapat dikembalikan.",
               ok: { label: "Hapus", flat: true, "no-caps": true, color: "red-7" },
               cancel: { label: "Batal", flat: true, "no-caps": true },
            })
            .onOk(() => {
               this.destroyOrder(order.id).then((response) => {
                  if (response.status == 200) {
                     this.getData();
                  }
               });
            })
            .onCancel(() => { })
            .onDismiss(() => { });
      },
      handleConfirmationOrder(order) {
         let msg = "Pastikan pembayaran telah anda terima dengan baik";
         let title = "Konfirmasi";
         if (order.transaction.payment_type == "PAYMENT_GATEWAY") {
            msg =
               "<b>WARNING!</b>, Metode pembayaran menggunakan payment gateway, Seharusnya status pesanan dan status pembayaran akan <b>otomatis</b> berubah jika sudah terjadi pembayaran di pihak payment gateway. Sangat tidak disarankan untuk merubah status secara manual";

            title = "Konfirmasi Manual";
         }
         this.$q
            .dialog({
               title: "Konfirmasi pembayaran",
               message: msg,
               cancel: true,
               ok: { label: title, flat: true },
               html: true,
            })
            .onOk(() => {
               this.acceptPayment(order.id).then(() => {
                  this.$store.dispatch('user/getUser')
                  this.getData();
               });
            })
      },
      handleFollowUp(data) {
         this.currentOrder = data;
         this.followUpModal = true;
      },
      exportData() {
         let url = "export/orders";

         url += `?${new URLSearchParams(this.exportForm)}`;

         this.$q.loading.show();
         BaseApi.get(url, { responseType: "blob" })
            .then((response) => {
               let date = new Date();

               let filename = `orders-export-${this.exportForm.status}-${date.toISOString().split("T")[0]
                  }.xlsx`;

               const href = window.URL.createObjectURL(response.data);

               // create "a" HTML element with href to file & click
               const link = document.createElement("a");
               link.href = href;
               link.setAttribute("download", filename); //or any other extension
               document.body.appendChild(link);
               link.click();

               // clean up "a" element & remove ObjectURL
               document.body.removeChild(link);
               window.URL.revokeObjectURL(href);
            })
            .finally(() => this.$q.loading.hide());
      },
   },
   meta() {
      return {
         title: "Order List",
      };
   },
};
</script>
