<template>
   <section>
      <div class="product-list-container">
         <template v-if="products.length">
            <ProductList v-for="(product, index) in products" :key="index" :product="product" />
         </template>
         <template v-else>
            <ProductListSkeleton v-for="i in 6" :key="i" />
         </template>
      </div>
   </section>
</template>

<script>
import ProductList from 'components/ProductList.vue'
import ProductListSkeleton from 'components/ProductListSkeleton.vue'
export default {
   name: 'ProductListHome',
   components: { ProductList, ProductListSkeleton },
   props: {
      products: Array,
      title: String,
      path: {
         type: String,
         default: ''
      },
      query: {
         type: String,
         default: ''
      },
      ready: {
         type: Boolean,
         default: false
      }
   },
   methods: {
      detail() {
         if (this.query) {
            this.$router.push({ name: this.path, query: { q: this.query } })
         } else {
            this.$router.push({ name: this.path })
         }
      }
   }


}
</script>