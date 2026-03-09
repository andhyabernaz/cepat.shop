<template>
   <q-page padding class="q-pb-xl bg-grey-1">
      <MobileHeader :title="title"></MobileHeader>
      <div class="main-content">

         <product-section :title="title" :products="products" @loadUrl="paginate"></product-section>
         <div v-if="!products.available">
            <EmptyData title="Produk Katalog" routeName="ProductIndex"></EmptyData>
         </div>

      </div>
      <q-inner-loading :showing="loading"></q-inner-loading>
   </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ProductSection from 'components/ProductSection.vue'
import { Api } from 'boot/axios'
import { createMetaMixin } from 'quasar'
import EmptyData from 'src/components/EmptyData.vue'
export default {
   components: { ProductSection, EmptyData },
   mixins: [
      createMetaMixin(function () {
         return {
            title: this.title,
            meta: {
               description: { name: 'description', content: this.description },
               ogDescription: { name: 'og:description', content: this.description },
               ogTitle: { name: 'og:title', content: this.title },
               ogUrl: { name: 'og:url', content: location.href },
               ogImage: { name: 'og:image', content: this.shop?.logo ? this.shop.logo : '' },
            }
         }
      })
   ],
   data() {
      return {
         likes: [],
         description: this.$store.state.meta.description,
         shop: this.$store.state.shop,
         isLoadmore: false,
         categoryId: this.$route.params.id
      }
   },
   watch: {
      '$route.params.id'(val) {
         this.categoryId = val
      }
   },
   computed: {
      favorites: function () {
         return this.$store.state.product.favorites
      },
      products() {
         return this.$store.state.product.productsByCategory
      },
      categories() {
         return this.$store.state.front.all_categories
      },
      title() {
         if (this.categories.length) {
            let item = this.categories.find(el => el.slug == this.categoryId)
            if (item) {
               return item.title
            }
         }
         return 'Katalog Produk'
      },
      config() {
         return this.$store.state.config
      },
      loading() {
         return this.$store.state.loading
      },
      isMenuCategory: {
         get() {
            return this.$store.state.isMenuCategory
         },
         set(status) {
            this.$store.commit('SET_MENU_CATEGORY', status)
         }
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },

   },
   methods: {
      ...mapActions('product', ['productsByCategory']),
      backButton() {
         this.$router.push({ name: 'ProductIndex' })
      },
      handleMenuCategory() {
         this.isMenuCategory = !this.isMenuCategory
      },
      paginate(url) {
         this.$store.commit('SET_LOADING', true)
         Api.get(url).then(response => {
            if (response.status == 200) {
               this.$store.commit('product/PAGINATE_PRODUCT_CATEGORY', response.data)
            }
         })
      },
      isCurrentProducts() {

         if (this.products.data.length) {

            let cat = this.products.data[0].category;

            if (cat.id == this.$route.params.id) {
               return true
            }

         }

         return false

      },
      getProducts() {
         if (this.isCurrentProducts()) return

         let params = {
            category_id: this.$route.params.id,
            source: 'catalog'
         }

         this.$store.commit('product/CLEAR_PRODUCT_CATEGORY')
         this.productsByCategory(params)
      }
   },
   created() {
      this.getProducts()
      this.categoryId = this.$route.params.id

      if (!this.categories.length) {
         this.$store.dispatch('front/getAllCategories')
      }
   }
}
</script>
<style lang="scss">
.relative {
   position: relative;
}

.absolute {
   position: absolute;

   &__top-right {
      top: 0;
      right: 0;
   }
}

.mini .q-field__marginal,
.mini .q-field__control {
   height: 30px;
}
</style>