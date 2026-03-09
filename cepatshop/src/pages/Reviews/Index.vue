<template>
   <q-page padding>
      <AppHeader title="Ulasan Produk"></AppHeader>
      <div class="bg-white q-mb-sm">
         <q-tabs v-model="is_approved" active-color="primary" align="left">
            <q-tab :name="0" label="Menunggu Moderasi"></q-tab>
            <q-tab :name="1" label="Di Publish"></q-tab>
         </q-tabs>
      </div>

      <q-card class="section shadow">
         <q-card-section>
            <div class="table-responsive">
               <table class="table bordered aligned">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Reviewer</th>
                        <th>Comment</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, i) in reviews.data" :key="i">
                        <td>{{ reviews.from + i }}</td>
                        <td>
                           {{ item.product_name }}
                           <q-item-label caption v-if="item.product_varian">
                              {{ item.product_varian }}
                           </q-item-label>
                           <div>
                              <q-rating data-nosnippet="true" readonly v-model="item.rating" color="accent"
                              class="no-wrap"
                                 icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half"
                                 size="1.2rem" />
                           </div>
                        </td>
                        <td>

                           <div class="q-mt-xs text-sm">
                              {{ item.name }}
                           </div>
                           <q-item-label class="text-nowrap" caption>
                              {{ dateFormat(item.created_at) }}</q-item-label>
                        </td>
                        <td>
                           {{ item.comment }}
                        </td>
                        <td class="flex no-wrap q-gutter-xs">
                           <q-btn v-if="$can('delete-review')" round size="11px" color="red" icon="delete"
                              @click="handleDeleteReview(item.id)">
                              <q-tooltip>Delete</q-tooltip>
                           </q-btn>
                           <q-btn v-if="!item.is_approved && $can('manage-review')" round size="11px" icon="check" color="green"
                              @click="handleApproveReview(item)">
                              <q-tooltip>Approved</q-tooltip>
                           </q-btn>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div v-if="!reviews.total" class="text-center bg-white q-pa-md">
               Tidak ada ulasan
            </div>
            <SimplePagination autoHide v-bind="reviews" @loadUrl="getReviews"></SimplePagination>
         </q-card-section>
      </q-card>

      <!-- <q-card v-for="item in reviews.data" :key="item.id" flat bordered class="q-mb-sm">
         <q-card-section>

            <div class="q-mb-xs">
               <div class="flex justify-between no-wrap">
                  <div class="q-mb-xs">
                     <q-item-label class="text-md">
                        {{ item.product_name }}
                     </q-item-label>
                     <q-item-label caption v-if="item.product_varian">
                        {{ item.product_varian }}
                     </q-item-label>
                     <q-item-label class="q-mt-sm text-teal text-nowrap" caption>{{ item.name }}, {{
                        dateFormat(item.created_at)}}</q-item-label>
                  </div>
                  <div>
                     <q-rating data-nosnippet="true" readonly v-model="item.rating" color="accent"
                        icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half" size="1.2rem" />
                  </div>
               </div>
            </div>

            <q-item-label class="q-px-sm q-py-md bg-grey-1 text-grey-7">
               {{ item.comment }}
            </q-item-label>

            <div class="flex justify-between q-px-xs q-pt-sm">
               <q-item-label class="text-nowrap" caption>{{ item.name }} ~
                  {{ dateFormat(item.created_at) }}</q-item-label>
               <div class="flex justify-end q-gutter-x-sm">
                  <q-btn unelevated size="sm" color="red-7" label="Delete" @click="handleDeleteReview(item.id)">
                  </q-btn>
                  <q-btn v-if="!item.is_approved" unelevated size="sm" label="Approved" color="green"
                     @click="handleApproveReview(item)">
                  </q-btn>
               </div>
            </div>
         </q-card-section>
      </q-card> -->

   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   name: "ReviewIndex",
   data() {
      return {
         reviews: {
            data: [],
            total: 0,
            current_page: 1
         },
         is_approved: 0,
         take: 8,
      };
   },
   mounted() {
      this.getReviews();
   },
   watch: {
      is_approved: function () {
         this.getReviews();
      },
   },
   methods: {
      getReviews(url = false) {
         this.$store.commit("SET_LOADING", true);
         if (!url) {
            url = `reviews?is_approved=${this.is_approved}&page=${this.reviews.current_page}`
         }
         BaseApi.get(url).then((response) => {
            if (response.status == 200) {
               this.reviews = { ...this.reviews, ...response.data.data }
            }
         });
      },
      handleDeleteReview(id) {
         this.$q
            .dialog({
               title: "Konfirmasi Penghapusan",
               message: "Yakin akan menghapus ulasan ini?",
               cancel: true,
            })
            .onOk(() => {
               this.deleteReview(id);
            });
      },
      handleApproveReview(item) {
         this.$q
            .dialog({
               title: "Konfirmasi",
               message: "Ingin menyetujui ulasan ini?",
               cancel: true,
            })
            .onOk(() => {
               this.approveReview(item);
            });
      },
      deleteReview(id) {
         this.$store.commit("SET_LOADING", true);
         BaseApi.delete("reviews/" + id).finally(() => {
            this.getReviews();
         });
      },
      approveReview(item) {
         this.$store.commit("SET_LOADING", true);
         BaseApi.post("reviews", {
            id: item.id,
            product_id: item.product_id,
         }).finally(() => {
            this.getReviews();
         });
      },
   },
};
</script>
