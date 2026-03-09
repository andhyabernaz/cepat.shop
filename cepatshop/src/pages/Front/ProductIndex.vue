<template>
   <q-page padding class="q-pb-xl bg-grey-1">
      <MobileHeader :title="title"></MobileHeader>
      <div class="main-content">
         <ProductSection :title="title" :products="products" @loadUrl="paginate"></ProductSection>
      </div>
   </q-page>
</template>

<script>
import ProductSection from 'components/ProductSection.vue'
import { Api } from 'boot/axios'
import { createMetaMixin } from 'quasar'
export default {
   name: 'ProductIndex',
   components: { ProductSection },
   mixins: [
      createMetaMixin(function () {
         return {
            title: 'Katalog Produk',
            meta: {
               description: { name: 'description', content: this.description },
               ogDescription: { name: 'og:description', content: this.description },
               ogTitle: { name: 'og:title', content: this.title },
               ogUrl: { name: 'og:url', content: location.href },
            }
         }
      })
   ],
   data() {
      return {
         title: 'Katalog Produk',
         description: this.$store.state.meta.description,
         isLoadmore: false,
         isFilter: true,
         showTotop: false
      }
   },
   computed: {
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
      products() {
         return this.$store.state.front.product_list
      },
      config() {
         return this.$store.state.config
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
      paginate(url) {
         this.$store.commit('SET_LOADING', true)

         Api.get(url).then(response => {
            if (response.status == 200) {
               this.$store.commit('front/PAGINATE_PRODUCTS', response.data)
            }
         }).finally(() => this.isLoadmore = false)
      },
      handleMenuCategory() {
         this.isMenuCategory = !this.isMenuCategory
      }
   },
   created() {
      if (this.$route.query.q) {
         this.title = 'Produk ' + this.$route.query.q
      }

      if (!this.products.data.length) {
         this.$store.dispatch('front/getProducts', {
            source: 'catalog'
         })
      }
   },
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