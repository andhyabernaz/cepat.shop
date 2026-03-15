<template>
   <q-page class="mainpage bio-home">
      <div class="main-content">
         <template v-if="!loading">
            <div class="bio-hero q-pa-md">
               <q-card class="bio-hero-card">
                  <q-card-section class="q-pa-md">
                     <div class="row items-center no-wrap q-gutter-md">
                        <q-avatar size="64px" rounded>
                           <img :src="shop?.logo ? shop.logo : '/icon/icon-192x192.png'" alt="Logo" />
                        </q-avatar>
                        <div class="col overflow-hidden">
                           <div class="text-h6 text-weight-bold ellipsis">
                              {{ shop?.name || 'Beranda' }}
                           </div>
                           <div v-if="shop?.slogan" class="text-subtitle2 text-grey-7 ellipsis">
                              {{ shop.slogan }}
                           </div>
                        </div>
                     </div>
                     <div v-if="shop?.description" class="text-body2 text-grey-8 q-mt-sm">
                        {{ shop.description }}
                     </div>

                     <div class="row q-col-gutter-sm q-mt-md">
                        <div class="col-12 col-sm-6">
                           <q-btn unelevated no-caps color="primary" class="full-width" icon="eva-shopping-bag-outline"
                              :to="{ name: 'ProductIndex' }" label="Lihat Katalog" />
                        </div>
                        <div class="col-12 col-sm-6">
                           <q-btn outline no-caps color="primary" class="full-width" icon="eva-message-circle-outline"
                              label="Chat WhatsApp" @click="openWhatsapp" />
                        </div>
                     </div>
                  </q-card-section>
               </q-card>
            </div>

            <div class="q-px-md q-mt-sm" v-if="featured && featured.length">
               <div class="row items-center justify-between q-mb-sm">
                  <div class="text-subtitle1 text-weight-bold">Link Cepat</div>
               </div>
               <div class="column q-gutter-sm">
                  <q-card v-for="item in featured" :key="item.id" class="bio-link-card cursor-pointer"
                     @click="showPost(item)">
                     <q-card-section class="q-pa-sm">
                        <div class="row items-center no-wrap q-gutter-sm">
                           <q-avatar v-if="item.image_url" size="42px" rounded>
                              <img v-lazy-img="item.image_url" :alt="item.title" />
                           </q-avatar>
                           <div class="col overflow-hidden">
                              <div class="text-weight-medium ellipsis">{{ item.label || item.title }}</div>
                              <div v-if="item.description" class="text-caption text-grey-7 ellipsis">
                                 {{ item.description }}
                              </div>
                           </div>
                           <q-icon name="eva-arrow-forward-outline" color="grey-6" />
                        </div>
                     </q-card-section>
                  </q-card>
               </div>
            </div>

            <section class="q-px-md q-mt-lg">
               <div class="row items-center justify-between q-mb-sm">
                  <div class="text-subtitle1 text-weight-bold">Katalog Produk</div>
                  <q-btn flat no-caps color="primary" :to="{ name: 'ProductIndex' }" label="Lihat Semua" />
               </div>

               <div class="bio-filter row no-wrap items-center q-gutter-sm q-mb-md">
                  <q-btn no-caps rounded size="sm" :outline="selectedCategorySlug !== null" color="primary"
                     :unelevated="selectedCategorySlug === null" label="Semua" @click="selectCategory(null)" />
                  <q-btn v-for="cat in categories" :key="cat.id" no-caps rounded size="sm"
                     :outline="selectedCategorySlug !== cat.slug" color="primary"
                     :unelevated="selectedCategorySlug === cat.slug" :label="cat.title"
                     @click="selectCategory(cat.slug)" />
               </div>

               <div class="row q-col-gutter-sm">
                  <template v-if="productList.ready">
                     <div v-for="product in productList.data" :key="product.id" class="col-6 col-sm-4 col-md-3">
                        <q-card class="bio-product-card full-height">
                           <q-responsive :ratio="1">
                              <img v-lazy-img="getProductThumb(product)" :alt="product.title"
                                 class="bio-product-img" />
                           </q-responsive>
                           <q-card-section class="q-pa-sm">
                              <div class="text-subtitle2 ellipsis-2-lines">{{ product.title }}</div>
                              <div class="text-caption text-grey-7 ellipsis-2-lines q-mt-xs">
                                 {{ getShortDescription(product.description) }}
                              </div>
                              <div class="text-weight-bold text-primary q-mt-sm">
                                 Rp{{ moneyFormat(getProductPrice(product)) }}
                              </div>
                           </q-card-section>
                           <q-card-actions class="q-pa-sm q-pt-none">
                              <q-btn unelevated no-caps color="primary" class="full-width" label="Lihat Produk"
                                 @click="goToProduct(product)" />
                           </q-card-actions>
                        </q-card>
                     </div>
                  </template>
                  <template v-else>
                     <div v-for="i in 8" :key="i" class="col-6 col-sm-4 col-md-3">
                        <q-card class="bio-product-card">
                           <q-skeleton height="160px" square />
                           <q-card-section class="q-pa-sm">
                              <q-skeleton type="text" />
                              <q-skeleton type="text" width="70%" />
                              <q-skeleton type="text" width="55%" class="q-mt-sm" />
                              <q-skeleton type="QBtn" class="q-mt-sm" />
                           </q-card-section>
                        </q-card>
                     </div>
                  </template>
               </div>

               <div class="q-mt-md" v-if="productList.ready && productList.links && productList.links.next">
                  <q-btn outline no-caps color="primary" class="full-width" label="Muat Lagi" :loading="isLoadMore"
                     @click="loadMoreProducts" />
               </div>
            </section>

            <div v-if="sliders && sliders.data && sliders.data.length" class="q-px-md q-mt-lg">
               <SplideSlider :sliders="sliders.data" />
            </div>

            <div class="q-px-md q-mt-lg" v-if="banners && banners.length">
               <BannerContainer :data="banners" class="banner-bottom" />
            </div>

            <div id="product-promo" v-if="product_promo && product_promo.length" class="q-mt-lg">
               <ProductPromo :product_promo="product_promo" />
            </div>

            <InstallApp spacing />
            <FrontPostBlock />
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
import { Api } from 'boot/axios'
import SplideSlider from 'components/SplideSlider.vue'
import BannerContainer from 'components/BannerContainer.vue'
import ProductPromo from 'components/ProductPromo.vue'
import FrontPostBlock from 'components/FrontPostBlock.vue'
import InstallApp from 'components/InstallApp.vue'

export default {
   components: {
      SplideSlider,
      ProductPromo,
      BannerContainer,
      FrontPostBlock,
      InstallApp
   },
   data() {
      return {
         selectedCategorySlug: null,
         isLoadMore: false,
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
      categories() {
         const list = this.$store.state.front.categories.data || []
         return list.filter(cat => cat?.is_front && (Number(cat?.products_count) > 0 || Number(cat?.child_products_count) > 0))
      },
      productList() {
         return this.$store.state.front.product_list
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
      openWhatsapp() {
         if (!this.shop?.phone) return
         const phone = String(this.shop.phone).replace(/\D/g, '')
         if (!phone) return
         window.open(`https://wa.me/${phone}`, '_blank', 'noopener')
      },
      showPost(item) {
         if (item.post) {
            this.$router.push({ name: 'FrontPostShow', params: { slug: item.post.slug } })
         }
      },
      selectCategory(slug) {
         if (this.selectedCategorySlug === slug) return
         this.selectedCategorySlug = slug
         this.fetchProducts()
      },
      fetchProducts() {
         const params = { source: 'home' }
         if (this.selectedCategorySlug) {
            params.category_id = this.selectedCategorySlug
         }
         this.$store.dispatch('front/getProducts', params)
      },
      async loadMoreProducts() {
         if (!this.productList?.links?.next || this.isLoadMore) return
         this.isLoadMore = true
         try {
            const response = await Api.get(this.productList.links.next)
            if (response.status === 200) {
               this.$store.commit('front/PAGINATE_PRODUCTS', response.data)
            }
         } finally {
            this.isLoadMore = false
         }
      },
      stripHtml(str) {
         return String(str || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
      },
      getShortDescription(html) {
         const text = this.stripHtml(html)
         if (!text) return ''
         if (text.length <= 90) return text
         return text.slice(0, 90).trim() + '...'
      },
      getProductThumb(product) {
         if (product?.assets?.length) return product.assets[0].src
         return '/static/no_image.png'
      },
      getProductPrice(product) {
         const p = product?.pricing?.default_price ?? product?.price ?? 0
         return parseInt(p) || 0
      },
      goToProduct(product) {
         Api.post('product-click', {
            product_id: product?.id ?? null,
            product_slug: product?.slug ?? null,
            category_id: product?.category?.id ?? null,
            category_slug: product?.category?.slug ?? null,
            source: 'home_biolink',
            referrer: document.referrer || null,
         }).catch(() => { })
         this.$router.push({ name: 'ProductShow', params: { slug: product.slug } })
      }
   },
   async created() {
      if (!this.is_loaded || this.$route.query.is_update) {
         await this.$store.dispatch("getInitialData");
      }
      this.fetchProducts()
   },
}
</script>

<style lang="scss">
.bio-home {
   .bio-hero-card {
      border-radius: 16px;
      overflow: hidden;
   }

   .bio-link-card {
      border-radius: 14px;
   }

   .bio-filter {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 4px;
   }

   .bio-product-card {
      border-radius: 14px;
      overflow: hidden;
   }

   .bio-product-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
   }
}
</style>
