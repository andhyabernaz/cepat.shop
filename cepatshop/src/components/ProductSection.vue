<template>
   <section>
      <div v-if="!noMenu">
         <div class="post-title row justify-between items-center">
            <h1>{{title}}</h1>
            <div class="q-gutter-x-xs" v-if="config">
               <q-btn @click="handleMenuCategory" icon="eva-funnel-outline" dense outline size="13px" color="grey-7">
               </q-btn>
               <q-btn @click="changeViewMode('grid')" :outline="config.product_view_mode != 'grid'" unelevated size="13px"
                  :color="config.product_view_mode == 'grid' ? 'secondary' : 'grey-7'" dense icon="eva-grid"></q-btn>
               <q-btn @click="changeViewMode('list')" :outline="config.product_view_mode != 'list'" unelevated size="13px"
                  :color="config.product_view_mode == 'list' ? 'secondary' : 'grey-7'" dense icon="eva-list"></q-btn>
            </div>
         </div>
      </div>
      <h1 v-else class="post-title">{{title}}</h1>
      <div class="product-list-container" v-if="config && config.product_view_mode == 'list'">
         <template v-if="products.ready">
            <ProductList v-for="(product, index) in products.data" :key="index" :product="product" />
         </template>
         <template v-else>
            <div :class="{ 'col-6 q-pa-xs': page_width >= 668 }" v-for="a in 8" :key="a">
               <ProductListSkeleton />
            </div>
         </template>
      </div>
      <div class="product-grid items-stretch" v-else>
         <template v-if="products.ready">
            <div v-for="(product, index) in products.data" :key="index" :product="product" class="">
               <ProductCard :product="product" />

            </div>
         </template>
         <template v-else>
            <div v-for="a in 8" :key="a">
               <ProductCardSkeleton :width="skeletonWidth" />
            </div>
         </template>
      </div>
      <div ref="loader" class="loader">
         <div class="loader__inner" v-show="loading"></div>
      </div>
      <q-dialog v-model="isMenuCategory" position="bottom">
         <CategoryMenu />
      </q-dialog>
   </section>
</template>

<script>
import ProductCard from 'components/ProductCard.vue'
import ProductCardSkeleton from 'components/ProductCardSkeleton.vue'
import ProductList from 'components/ProductList.vue'
import ProductListSkeleton from 'components/ProductListSkeleton.vue'
import CategoryMenu from 'components/CategoryMenu.vue'

export default {
   name: 'ProductSection',
   components: {
      ProductCard,
      ProductList,
      ProductCardSkeleton,
      ProductListSkeleton,
      CategoryMenu
   },
   props: {
      products: Object,
      title: String,
      noMenu: Boolean
   },
   data() {
      return {
         viewMode: 'grid',
      }
   },
   mounted() {
      this.$nextTick(() => {
         this.intersecObserve()
      })
   },
   computed: {
      config() {
         return this.$store.state.config
      },
      loading() {
         return this.$store.state.loading
      },
      page_width() {
         return this.$store.state.page_width
      },
      skeletonWidth() {
         if (this.page_width >= 768) {
            return 768 / 3.5
         }

         return this.page_width / 3
      },
      isMenuCategory: {
         get() {
            return this.$store.state.isMenuCategory
         },
         set(status) {
            this.$store.commit('SET_MENU_CATEGORY', status)
         }
      }
   },
   methods: {
      changeViewMode(str) {
         this.$store.commit('SET_PRODUCT_VIEW_MODE', str)
      },
      handleMenuCategory() {
         this.isMenuCategory = !this.isMenuCategory
      },
      intersecObserve() {
         let el = this.$refs.loader

         let opts = {
            rootMargin: '0px',
            threshold: 0,
         }

         let previousY = 0
         let previousRatio = 0

         let clb = (entries) => {

            entries.forEach(entry => {

               const currentY = entry.boundingClientRect.y
               const currentRatio = entry.intersectionRatio
               const isIntersecting = entry.isIntersecting
               let is_scroll_down = false

               // Scrolling down/up
               if (currentY < previousY) {
                  if (currentRatio > previousRatio && isIntersecting) {
                     // console.log("Scrolling down enter")
                     is_scroll_down = true
                  } else {
                     is_scroll_down = false
                     // console.log("Scrolling down leave")
                  }
               } else if (currentY > previousY) {
                  if (currentRatio < previousRatio) {
                     // console.log("Scrolling up leave")
                     is_scroll_down = false
                  } else {
                     // console.log("Scrolling up enter")
                     is_scroll_down = false
                  }
               }

               previousY = currentY
               previousRatio = currentRatio

               if (!entry.isIntersecting) {

                  return

               } else {

                  if (is_scroll_down && !this.loading && this.products?.links?.next) {
                     this.$emit('loadUrl', this.products.links.next)
                  }
               }
            });
         };

         let observer = new IntersectionObserver(clb, opts);
         observer.observe(el)
      },

   }


}
</script>
