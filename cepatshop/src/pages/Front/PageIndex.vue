<template>
   <q-page class="mainpage">
      <div class="main-content">
         <template v-if="!loading">
            <div v-if="sliders.data.length">
               <SplideSlider :sliders="sliders.data" />
            </div>
            <BannerContainer :data="banners" class="banner-bottom" />
            <CategoryCarousel />
            <div id="product-promo" v-if="product_promo.length">
               <ProductPromo :product_promo="product_promo" />
            </div>
            <InstallApp spacing />
            <ProductSectionObserver />
            <BannerContainer :data="featured" class="banner-bottom" />
            <FrontPostBlock />
            <!-- <InstallApp spacing /> -->
         </template>
      </div>
      <q-inner-loading :showing="loading" label="Please wait...">
         <q-spinner-facebook size="50px" color="brand" />
      </q-inner-loading>

   </q-page>
</template>

<script>
import { createMetaMixin } from 'quasar'
import { mapState } from 'vuex'
import SplideSlider from 'components/SplideSlider.vue'
import BannerContainer from 'components/BannerContainer.vue'
import CategoryCarousel from 'components/CategoryCarousel.vue'
import ProductPromo from 'components/ProductPromo.vue'
import ProductSectionObserver from 'components/ProductSectionObserver.vue'
import FrontPostBlock from 'components/FrontPostBlock.vue'
import InstallApp from 'components/InstallApp.vue'

export default {
   components: {
      SplideSlider,
      CategoryCarousel,
      ProductPromo,
      ProductSectionObserver,
      BannerContainer,
      FrontPostBlock,
      InstallApp
   },
   data() {
      return {
         showLoading: true
      }
   },
   mixins: [
      createMetaMixin(function () {
         return {
            title: this.title
         }
      })
   ],
   computed: {
      ...mapState({
         loading: state => state.loading,
         shop: state => state.shop,
         config: state => state.config,
         product_promo: state => state.front.product_promo,
         banners: state => state.front.blocks.banner,
         featured: state => state.front.blocks.featured,
      }),
      sliders() {
         return this.$store.state.front.sliders
      },
      title() {
         if (this.shop) {
            return `Beranda - ${this.shop.name}`;
         }
         return "Beranda";
      },
      is_loaded() {
         return this.$store.state.front.is_loaded;
      },
   },
   created() {
      if (!this.is_loaded || this.$route.query.is_update) {
         this.$store.dispatch("getInitialData");
      }
   },
}
</script>
