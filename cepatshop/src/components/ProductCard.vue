<template>
   <div class="card-item column full-height relative bg-white box-shadow overflow-hidden">
      <q-img :src="getImageSrc" :ratio="getImageRatio" :fit="getImageFit"
         @click="show(product.slug)" class="cursor-pointer">
         <div class="absolute-full text-subtitle2 flex flex-center" v-if="outOfStock">
            <div class="stock-out-badge">Habis</div>
          </div>
      </q-img>
      <div class="relative col column q-gutter-y-xs justify-between q-pb-md q-px-sm q-pt-sm overflow-hidden full-width">

         <div>
            <div class="flex justify-between items-center q-mb-sm q-mt-xs">
               <q-rating data-nosnippet="true" readonly v-model="rating" color="accent" icon="ion-star-outline"
                  icon-selected="ion-star" icon-half="ion-star-half" :size="ratingSize" class="flex no-wrap" />

               <q-item-label class="product_sold" caption
                  v-if="config && config.display_product_sold && product.sold != 0">{{
                     product.sold }}</q-item-label>
            </div>
            <div class="full-width q-mt-xs">
               <q-item-label class="text-subtitle ellipsis-2-lines cursor-pointer" @click="show(product.slug)">{{
                  product.title }}</q-item-label>
            </div>
         </div>
         <div class="card-price-container">
            <div class="card-price text-secondary">
               <span class="prefix">Rp</span>
               <span class="amount">{{ moneyFormat(parseInt(product.pricing.default_price) - getDIscountAmount)
               }}</span>
            </div>
            <div v-if="getDiscountPercent" class="card-discount text-strike text-grey-8">
               <span class="prefix">Rp</span>
               <span class="amount">{{ moneyFormat(parseInt(product.pricing.default_price)) }}</span>
            </div>
         </div>

      </div>
      <div v-if="getDiscountPercent" class="discount-badge">
         {{ getDiscountPercent }}%</div>
      <div class="absolute-right q-pa-xs">
         <favorite-button :product_id="product.id" />
      </div>
   </div>
</template>
<script>
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
   name: 'ProductCard',
   props: {
      product: Object,
      grabbing: Boolean
   },
   components: { FavoriteButton },
   data() {
      return {
         rating: this.product.rating ? parseFloat(this.product.rating) : 0.0
      }
   },
   computed: {
      config() {
         return this.$store.state.config
      },
      outOfStock() {
         if(this.product) {
            if(this.product.is_unlimited_stock) {
               return false
            }
            if(Number(this.product.total_stock) == 0) {
               return true
            }
         }
         return false
      },
      getImageSrc() {

         if (this.product && this.product.assets.length) {
            return this.product.assets[0].src
         }

         return '/static/no_image.png'
      },
      getImageRatio() {
         if (this.config && this.config.card_img_ratio) {
            return this.getRatio(this.config.card_img_ratio)
         }
         return '1'
      },
      getImageFit() {
         if (this.config && this.config.card_img_fit) {
            return this.config.card_img_fit
         }
         return 'cover'
      },
      getDIscountAmount() {
         if (this.product.pricing.is_discount) {
            if (this.product.pricing.discount_type == 'PERCENT') {
               return (parseInt(this.product.pricing.default_price) * parseInt(this.product.pricing.discount_amount)) / 100
            } else {
               return parseInt(this.product.pricing.discount_amount)
            }
         }
         return 0
      },
      getDiscountPercent() {
         if (this.product.pricing.is_discount) {
            if (this.product.pricing.discount_type == 'PERCENT') {
               return parseInt(this.product.pricing.discount_amount)
            } else {
               return parseInt((parseInt(this.product.pricing.discount_amount) / parseInt(this.product.pricing.default_price)) * 100)
            }
         }
         return 0
      },
      page_width() {
         return this.$store.state.page_width
      },
      ratingSize() {
         if (this.page_width < 481) {
            return '.9rem'
         }
         return '1.1rem'
      }
   },
   methods: {
      show(slug) {
         if (this.grabbing) return
         this.$router.push({ name: 'ProductShow', params: { slug: slug } })
      },
   }
}
</script>