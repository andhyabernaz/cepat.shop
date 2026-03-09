<template>
  <div>
    <template v-for="promo in product_promo">
      <div :key="promo.id" v-if="promo" class="block-container bg-linear">
        <div class="block-content">
          <div>
            <ProductPromoContainer :promo="promo" />
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import ProductPromoContainer from 'components/ProductPromoContainer.vue'
export default {
  props: ['product_promo'],
  components: { ProductPromoContainer },
}
</script>
