<template>
   <q-page padding>
      <AppHeader title="Ulasan Saya"></AppHeader>

        <div class="box-column flat">
           <div class="tabe-responsive">
              <table class="table bordered aligned">
                 <thead>
                    <tr>
                       <th>#</th>
                       <th>Produk</th>
                       <th>Comment</th>
                       <th>Reviewer</th>
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
                             <q-rating class="no-wrap" data-nosnippet="true" readonly v-model="item.rating" color="accent"
                                icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half"
                                size="1.2rem" />
                          </div>
                          <div class="q-mt-sm reviews-image-thumbs ">
                           <q-img @click="handlePreviewImage(img.src)" class="img-thumbnail" v-for="(img, i) in item.review_images" :key="i" :ratio="1" :src="img.src" width="50px"></q-img>
                          </div>
                          
                       </td>
                       <td>
                          {{ item.comment }}
                       </td>
                       <td>
   
                          <div class="q-mt-xs text-sm">
                             {{ item.name }}
                          </div>
                          <q-item-label class="text-nowrap" caption>
                             {{ dateFormat(item.created_at) }}</q-item-label>
                       </td>
                    </tr>
                 </tbody>
              </table>
           </div>
           <div v-if="!reviews.total" class="text-center bg-white q-pa-md">
              Tidak ada ulasan
           </div>
           <SimplePagination autoHide v-bind="reviews" @loadUrl="getData"></SimplePagination>

        </div>

        <q-dialog v-model="previewModal">
         <img :src="previewImage" alt="">
        </q-dialog>

   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
import SimplePagination from "components/SimplePagination.vue";
import { dateFormat } from "src/utils";
export default {
   name: "CustomerReviews",
   components: { SimplePagination },
   data() {
      return {
         queryParams: {
            status: "ALL",
            per_page: 6,
         },
         reviews: {
            data: [],
            total: 0,
            ready: false,
         },
         previewImage: null,
         previewModal: false
      };
   },
   computed: {
      loading() {
         return this.$store.state.loading;
      },
   },
   mounted() {
      this.getData();
   },
   methods: {
      paginate(url) {
         this.getData(url);
      },
      handlePreviewImage(src) {
         this.previewImage = src
         this.previewModal = true
      },
      getData(url = "customer/getReviews") {
         this.$store.commit("SET_LOADING", true);
         BaseApi.get(url)
            .then((res) => {
               this.reviews = { ...this.reviews, ...res.data.data };
               this.reviews.ready = true;
            })
            .finally(() => {
               this.$store.commit("SET_LOADING", false);
            });
      },
   },
};
</script>
