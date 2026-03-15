<template>
   <q-page class="mainpage">
      <div class="main-content">
         <template v-if="!loading">

            <!-- <SwiperSlider></SwiperSlider> -->

            <div v-if="sliders && sliders.data && sliders.data.length">

               <SplideSlider :sliders="sliders.data" />
            </div>

            <CategoryCarousel />
            <BannerContainer>
               <q-img :ratio="16 / 9" v-for="item in banners" :key="item.id" :src="item.image_url" :alt="item.title" />
            </BannerContainer>

            <div id="product-promo" v-if="product_promo && product_promo.length">
               <ProductPromo :product_promo="product_promo" />
            </div>

            <ProductSectionObserver />

            <BannerContainer>
               <div v-for="feature in blocks.featured" :key="feature.id">
                  <div class="column col items-center text-center q-gutter-y-xs q-pa-sm featured cursor-pointer"
                     @click="showPost(feature)">
                     <q-img v-if="feature.image" :src="feature.image_url" :alt="feature.title" :ratio="16 / 9" />
                     <div class="text-sm text-weight-medium q-mt-sm">{{ feature.label }}</div>
                     <div v-if="feature.description" class="text-grey-7 text-auto description">{{ feature.description }}
                     </div>
                  </div>
               </div>
            </BannerContainer>

            <FrontPostBlock />

            <InstallApp />

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
// import SwiperSlider from './block/SwiperSlider.vue'
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
         blocks: state => state.front.blocks,
      }),
      page_width() {
         return this.$store.state.page_width
      },
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
   methods: {

      showPost(item) {
         if (item.post) {
            this.$router.push({ name: 'FrontPostShow', params: { slug: item.post.slug } })
         }
      },
   },
   created() {
      if (!this.is_loaded || this.$route.query.is_update) {
         this.$store.dispatch("getInitialData");
      }
   },
}
</script>
