<template>
   <q-page padding>
      <AppHeader title="Vouchers">
         <q-btn v-if="$can('create-voucher')" color="white" text-color="dark" @click="voucherTypeModal = true"
            icon="add" label="Voucher"></q-btn>
      </AppHeader>

      <div class="bg-white q-mb-sm">
         <q-tabs v-model="status" align="left" indicator-color="primary">
            <q-tab v-for="status in statuses" :key="status.value" :name="status.value">{{ status.label }}</q-tab>
         </q-tabs>
      </div>

      <q-card flat square class="section shadow">
         <q-card-section>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in [
                           '#',
                           'Label',
                           'Diskon',
                           'Maksimum',
                           'Kode Voucher',
                           'Kuota',
                           'Action',
                        ]" :key="h">
                           {{ h }}
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, idx) in vouchers" :key="idx">
                        <td>{{ idx + 1 }}</td>
                        <td style="min-width: 120px">
                           <q-item-label>{{ item.name }}</q-item-label>
                           <q-item-label caption>{{ item.detail_label }}</q-item-label>
                        </td>
                        <td class="text-nowrap">{{ getNominalDiscount(item) }}</td>
                        <td class="text-nowrap">
                           {{
                              item.max_discount_amount
                                 ? moneyIdr(item.max_discount_amount)
                                 : "-"
                           }}
                        </td>
                        <td class="text-nowrap">
                           {{ item.voucher_code }}
                           <q-btn v-if="item.voucher_code" label="salin" color="teal" padding="2px 8px" flat
                              @click="copyString(item.voucher_code)"></q-btn>
                        </td>
                        <td>
                           {{ item.orders_count }} /
                           {{ item.usage_quota ? item.usage_quota : "&infin;" }}
                        </td>
                        <td class="flex no-wrap q-gutter-xs">
                           <q-btn v-if="$can('delete-voucher')" round size="11px" icon="delete"
                              @click="deleteData(item)" color="red"></q-btn>
                           <q-btn round size="11px" icon="edit"
                              v-if="item.status != 'expired' && $can('update-voucher')" :to="{
                                 name: 'VoucherEdit',
                                 params: { voucher_id: item.id },
                              }" color="blue"></q-btn>
                           <q-btn round size="11px" icon="visibility" @click="handleShowDetail(item)"
                              color="teal"></q-btn>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="text-center q-py-lg" v-if="!vouchers.length">
               Tidak ada data
            </div>
         </q-card-section>
      </q-card>

      <q-dialog v-model="detailModal" persistent no-shake>
         <q-card class="card-lg">
            <q-card-section v-if="selectedVoucher">
               <div class="card-subtitle flex justify-between items-center">
                  <div>{{ selectedVoucher.type_label }}</div>
                  <q-btn icon="close" flat dense v-close-popup></q-btn>
               </div>
               <q-list separator>
                  <q-item>
                     <q-item-section>Label</q-item-section>
                     <q-item-section>{{ selectedVoucher.name }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Kode Voucher</q-item-section>
                     <q-item-section>
                        <q-item-label>
                           {{ selectedVoucher.voucher_code }}
                           <q-btn v-if="selectedVoucher.voucher_code" label="salin" color="teal" padding="2px 8px" flat
                              @click="copyString(selectedVoucher.voucher_code)"></q-btn>
                        </q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Waktu Mulai</q-item-section>
                     <q-item-section>{{ selectedVoucher.start_date }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Waktu Berakhir</q-item-section>
                     <q-item-section>{{ selectedVoucher.end_date }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Kuota Pemakaian</q-item-section>
                     <q-item-section>
                        {{ selectedVoucher.orders_count }} /
                        {{
                           selectedVoucher.usage_quota
                              ? selectedVoucher.usage_quota
                              : "&infin;"
                        }}
                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Minimum Belanja</q-item-section>
                     <q-item-section>{{
                        moneyIdr(selectedVoucher.min_transaction)
                     }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Nominal Diskon</q-item-section>
                     <q-item-section>{{
                        getNominalDiscount(selectedVoucher)
                     }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Maksimum Diskon</q-item-section>
                     <q-item-section>{{
                        selectedVoucher.max_discount_amount
                           ? moneyIdr(selectedVoucher.max_discount_amount)
                           : "-"
                     }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Detail</q-item-section>
                     <q-item-section>{{
                        selectedVoucher.detail_label
                     }}</q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>
      </q-dialog>

      <q-dialog v-model="voucherTypeModal">
         <q-card>
            <q-card-section>
               <div class="card-subtitle flex justify-between items-center">
                  <div>Tambah Voucher</div>
                  <q-btn icon="close" v-close-popup dense flat></q-btn>
               </div>
               <q-list>
                  <q-item clickable @click="$router.push({ name: 'VoucherCreate' })">
                     <q-item-section avatar>
                        <q-icon color="amber-9" name="local_offer" size="xl"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <div class="text-md">Voucher Belanja</div>
                        <q-item-label caption>Voucher diskon untuk transaksi pembelian</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item clickable @click="
                     $router.push({
                        name: 'VoucherCreate',
                        query: { is_type_shipping: true },
                     })
                     ">
                     <q-item-section avatar>
                        <q-icon color="teal" name="local_shipping" size="xl"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <div class="text-md">Voucher Ongkos Kirim</div>
                        <q-item-label caption>Voucher diskon untuk ongkos kirim</q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   data() {
      return {
         voucherTypeModal: false,
         vouchers: [],
         status: this.$route.query.status || "active",
         statuses: [
            { label: "Berjalan", value: "active" },
            { label: "Akan Datang", value: "later" },
            { label: "Kadaluarsa", value: "expired" },
         ],
         selectedVoucher: null,
         detailModal: false,
      };
   },
   watch: {
      status(newVal, oldVal) {
         if (oldVal != newVal) {
            this.$router.replace({
               name: "VoucherIndex",
               query: { status: newVal },
            });
            this.getData(newVal);
         }
      },
   },
   created() {
      this.getData();
   },
   mounted() {
      this.$canAccess('view-voucher')
   },
   methods: {
      getData(status = "active") {
         let path = `vouchers?status=${status}`;

         this.$store.commit('SET_LOADING', true)

         BaseApi.get(path).then((res) => {
            this.vouchers = res.data.data;
         });
      },
      getNominalDiscount(voucher) {
         // let max_disc = voucher.max_discount_amount > 0 ? ` max ${this.moneyIdr(voucher.max_discount_amount)}` : ''
         return voucher.discount_type == "nominal"
            ? this.moneyIdr(voucher.discount_amount)
            : `${voucher.discount_amount}%`;
      },
      deleteData(data) {
         this.$q
            .dialog({
               title: "Konfimasi",
               message:
                  "Data yang dihapus tidak dapat dikembalikan, yakin akan menghapus data?",
               cancel: true,
               ok: { label: "Hapus", flat: true },
            })
            .onOk(() => {
               BaseApi.delete("vouchers/" + data.id).then(() => {
                  this.getData(this.status);
               });
            });
      },
      handleShowDetail(item) {
         this.selectedVoucher = item;
         this.detailModal = true;
      },
   },
};
</script>