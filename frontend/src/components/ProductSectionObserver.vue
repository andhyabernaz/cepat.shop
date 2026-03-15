<template>
  <div id="product-block" v-if="categories.available">
    <template v-for="category in categories.data">
      <div :key="category.id" v-if="canGetProduct(category)" 
        class="block-container bg-linear">
        <ProductBlockObserver :category="category" />
      </div>
    </template>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import ProductBlockObserver from './ProductBLockObserver.vue'
export default {
  props: ['products'],
  components: { ProductBlockObserver },
  computed: {
    ...mapState({
      categories: state => state.front.categories
    })
  },
  methods: {
    canGetProduct(category) {
      if(category.is_front) {
        if(category.child_products_count > 0 || category.products_count > 0) {
          return true
        }
      }
      return false
    }
  }
}
</script>
