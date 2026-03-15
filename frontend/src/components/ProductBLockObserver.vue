<template>
   <BlockObserver @onObserve="handleonObserve">
      <div class="auto-padding block-header">
         <div class="row items-end justify-between">
            <div class="block-title featured">
               <h2>{{ category.title }}</h2>
            </div>
            <q-btn flat no-caps color="primary" padding="4px"
               :to="{ name: 'ProductByCategory', params: { id: category.slug } }">
               <span>Lihat Semua</span>
               <q-icon name="eva-arrow-forward" size="16px"></q-icon>
            </q-btn>
         </div>
      </div>
      <div class="block-content">
         <div v-if="config && config.home_view_mode == 'list'">
            <ProductListSectionHome :products="products.data" />
         </div>
         <div v-else>
            <CarouselContainer :banner="category" :products="products.data"
               :loadmore="{ title: category.title, category: category.id }" />
         </div>
      </div>
   </BlockObserver>
</template>

<script>
import CarouselContainer from 'components/CarouselContainer.vue'
import ProductListSectionHome from 'components/ProductListSectionHome.vue'
import BlockObserver from 'src/components/BlockObserver.vue'
import { Api } from 'boot/axios'
export default {
   props: ['category'],
   components: {
      CarouselContainer,
      ProductListSectionHome,
      BlockObserver
   },
   data() {
      return {
         success: true,
         is_load_observer: true
      }
   },
   computed: {
      config() {
         return this.$store.state.config
      },
      products() {
         return this.$store.getters['front/getProductsByCategory'](parseInt(this.category.id))
      }
   },
   methods: {
      handleonObserve() {
         if (!this.products.is_done) {
            this.getProducts()
         }
      },
      async getProducts() {
         // return
         let params = {
            category_id: this.category.id,
             source: 'home'
         }
         let response = await Api.get(`getProducts?${new URLSearchParams(params).toString()}`)

         if (response.status == 200) {
            let data = {
               category_id: this.category.id,
               product_items: response.data.data
            }
            this.$store.commit('front/SET_PRODUCT_CATEGORY', data)
         }
      }
   }
}
</script>
