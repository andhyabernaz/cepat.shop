<template>
   <q-page padding :class="{ 'flex flex-center': !products.available }" class="product-favorite-container  q-pb-xl bg-grey-1">
      <MobileHeader title="Produk Favorite"></MobileHeader>
      <div class="main-content">
         <div v-if="products.available">
            <product-section title="Produk Favorit" :products="products"></product-section>
         </div>
         <div v-if="!products.available">
               <EmptyData routeName="ProductIndex" title="Produk Katalog"></EmptyData>
         </div>
      </div>
   </q-page>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import ProductSection from 'components/ProductSection.vue'
import EmptyData from 'src/components/EmptyData.vue'
export default {
   name: 'ProductFavorite',
   components: { ProductSection, EmptyData },
   computed: {
      ...mapState({
         favorites: state => state.product.favorites,
         products: state => state.product.productFavorites
      }),
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
   },
   methods: {
      ...mapActions('product', ['getProductsFavorites']),
      backButton() {
         this.$router.push({ name: 'ProductIndex' })
      },
   },
   created() {
      if (this.favorites.length) {
         this.$store.commit('product/CLEAR_PRODUCT_FAVORITE')
         this.getProductsFavorites({ pids: this.favorites })
      } else {
         this.$store.commit('product/SET_PRODUCT_FAVORITE_STATUS', { ready: true, available: false })
      }
   },
   meta() {
      return {
         title: 'Produk Favorit',
         meta: {
            ogTitle: { name: 'og:title', content: 'Produk Favorit' },
            ogUrl: { name: 'og:url', content: location.href },
            ogImage: { name: 'og:image', content: this.shop?.logo ? this.shop.logo : '' },
         }

      }
   }
}
</script>
