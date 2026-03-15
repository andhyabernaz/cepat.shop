<template>
   <div class="carousel-container relative overflow-hidden" :class="{ 'auto-padding-side': !isBgBanner }">
      <router-link :to="{ name: 'ProductByCategory', params: { id: banner.slug } }" v-if="banner && banner.banner_src">
         <div class="category-banner" :class="{ 'is-bg-banner': isBgBanner, 'is-shadow-banner': isShadowBanner }">
            <img ref="banner" :src="banner.banner_src" />
         </div>
      </router-link>

      <div class="carousel-items" ref="carousel" @mousedown="handleMouseDown" @mouseleave="handleMouseLeave"
         @mouseup="handleMouseUp" @mousemove="handleMouseMove" :style="styles">
         <template v-if="products.length">
            <div v-for="(product,) in products" :key="product.id" class="carousel-item"
               :style="`width: ${cardWidth}px;`">
               <ProductCard :product="product" :grabbing="is_grab" />
            </div>
            <div class="carousel-item" v-if="showLoadmore" :style="`width: ${cardWidth}px`">
               <div class="full-height flex column relative text-center justify-center items-center">
                  <div>
                     <q-btn unelevated icon="eva-arrow-forward" round size="16px" color="primary"
                        :to="{ name: 'ProductByCategory', params: { id: banner.slug } }"></q-btn>
                     <div class="q-pt-md">Selengkapnya <br>di {{ banner.title }}</div>
                  </div>
               </div>
            </div>
         </template>
         <template v-else>

            <div v-for="i in 6" :key="i" class="carousel-item" :style="cardWidthString">
               <ProductCardSkeleton :width="cardWidth" />
            </div>
         </template>
      </div>
   </div>
</template>

<script>
import ProductCard from 'components/ProductCard.vue'
import ProductCardSkeleton from 'components/ProductCardSkeleton.vue'
export default {
   name: 'SwipperProduct',
   components: { ProductCard, ProductCardSkeleton },
   props: {
      products: Array,
      loadmore: Object,
      ready: Boolean,
      gap: {
         default: 8
      },
      banner: Object
   },
   data() {
      return {
         isGrab: false,
         carousel: null,
         isDown: false,
         startX: 0,
         scrollLeft: 0,
         movementX: 0,
         pageX: 0,
         containerWidth: 768,
         offsetLeft: 0,
         backgroundStyle: '',
         preview: '',
         isShadowBanner: false,
      }
   },
   computed: {
      page_width() {
         return this.$store.state.page_width
      },
      is_grab() {
         return this.movementX != 0
      },
      cardWidth() {
         if (this.page_width > 1024) {
            return 225
         }

         if (this.page_width > 800) {
            return 210
         }

         if (this.page_width > 400) {
            return 180
         }

         return 160
      },
      isBgBanner() {
         if (this.banner) {
            if (this.banner.is_background_banner && this.banner.banner_src) {
               return true
            }
         }
         return false
      },
      carouselPadding() {
         if (this.isBgBanner) {
            let w = this.containerWidth * 33 / 100


            if (this.containerWidth < 600 && this.containerWidth > 400) {
               return 200
            }

            if (w < 180) {
               return 180
            }
            if (w > 370) {
               return 370
            }

            return w

         }
         return 0
      },
      styles() {
         return `padding-left: ${this.carouselPadding}px;gap: ${this.gap}px`
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
      showLoadmore() {
         if (this.banner) {

            if (this.is_mode_desktop) {
               if (this.products.length > 6) {
                  return true
               }
               return false
            }
            if (this.products.length > 3) {
               return true

            }
         }
         return false
      },
      cardWidthString() {
         return `width: ${this.cardWidth}px`
      }
   },
   mounted() {
      if (this.is_mode_desktop) {
         this.containerWidth = this.page_width
      } else {
         this.containerWidth = 768
      }
      this.$nextTick(() => {
         this.carousel = this.$refs.carousel

         window.addEventListener('resize', this.setContainerWidth)
         setTimeout(() => {
            this.setContainerWidth()
         }, 200)

         if (this.isBgBanner) {
            this.carousel.classList.add('bg-banner')

         }
      })

   },
   methods: {
      setContainerWidth() {
         this.containerWidth = this.carousel.clientWidth

         setTimeout(() => {
            if (this.isBgBanner) {
               if (this.$refs.banner && this.$refs.banner.clientWidth < this.page_width) {
                  this.isShadowBanner = true
               }else {
                  this.isShadowBanner = false
               }
            }
         }, 500)
      },
      handleMouseDown(e) {
         this.isDown = true;

         // this.carousel.classList.add('active');
         this.startPageX = e.pageX
         this.startX = e.pageX - this.carousel.offsetLeft;

         this.scrollLeft = this.carousel.scrollLeft;
      },
      handleMouseLeave(e) {
         this.isDown = false;
         // this.carousel.classList.remove('active');
         this.movementX = 0
      },
      handleMouseUp(e) {
         this.isDown = false;
         // this.carousel.classList.remove('active');
         setTimeout(() => {
            this.movementX = 0
         }, 100);
      },
      handleMouseMove(e) {
         if (!this.isDown) return;

         this.movementX = e.movementX

         e.preventDefault();
         this.pageX = e.pageX - this.carousel.offsetLeft;
         const walk = (this.pageX - this.startX) * 1.2; //scroll-fast
         this.carousel.scrollLeft = this.scrollLeft - walk;


      },
   },
   unmounted() {
      window.removeEventListener('resize', this.setContainerWidth)
   }
}
</script>
