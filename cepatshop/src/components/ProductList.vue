<template>
   <q-list class="relative bg-white">
      <div v-if="getDiscountPercent" class="absolute top-5 z-50 bg-secondary text-white"
         style="padding:2px;font-size:13px;">{{ getDiscountPercent }}%</div>
      <q-item class="q-pa-md relative bg-white">
         <q-item-section avatar top
            @click.prevent="$router.push({ name: 'ProductShow', params: { slug: product.slug } })"
            class="cursor-pointer column items-center q-gutter-y-sm">
            <q-img v-if="product.assets.length" :src="product.assets[0].src" ratio="1"
               class="image-list rounded-borders">
               <template v-slot:error>
                  <div class="absolute-full flex flex-center bg-grey-6 text-white text-center">
                     Cannot load image
                  </div>
               </template>
            </q-img>
            <q-img v-else src="/static/no_image.png" ratio="1" class="image-list rounded-borders" />
         </q-item-section>
         <q-item-section top class="overflow-hidden">
            <div class="cursor-pointer full-width"
               @click.prevent="$router.push({ name: 'ProductShow', params: { slug: product.slug } })">
               <q-item-label class="ellipsis-2-lines text-subtitle2 text-weight-medium">
                  {{ product.title }}
               </q-item-label>
               <div class="row justify-between items-center">
                  <q-rating data-nosnippet="true" readonly v-model="rating" color="accent" icon="ion-star-outline"
                     icon-selected="ion-star" icon-half="ion-star-half" size="1.1rem" />
                  <favorite-button :product_id="product.id" />
               </div>
               <q-item-label class="q-mt-xs" lines="2">{{ product.short_description }}</q-item-label>
               <div class="flex items-center q-gutter-xs q-mt-auto q-pt-sm">
                  <div class="text-subtitle1 text-secondary text-weight-bold">{{
                     moneyIdr(parseInt(product.pricing.default_price) - getDIscountAmount) }}</div>
                  <div v-if="getDiscountPercent" class="text-subtitle2 text-weight-medium text-strike text-grey-8">{{
                     moneyIdr(product.pricing.default_price) }}</div>
                  <q-item-label class="q-pl-xs text-nowrap" caption
                     v-if="config && config.display_product_sold && product.sold != 0">{{
                        product.sold
                     }}</q-item-label>
               </div>
            </div>
         </q-item-section>
      </q-item>
   </q-list>
</template>
<script>
import FavoriteButton from 'components/FavoriteButton.vue'
export default {
   name: 'ProductCard',
   props: { product: Object },
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
      page_width() {
         return this.$store.state.page_width
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
      }
   },
   methods: {
      show(id) {
         this.$router.push({ name: 'ProductShow', params: { id: id } })
      },
      getTeaser(html) {
         if (html) {
            let strippedString = html.replace(/(<([^>]+)>)/gi, "");
            return strippedString.substr(0, 120)
         } else {
            return ''
         }
      },
   }
}
</script>