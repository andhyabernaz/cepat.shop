<template>
  <div class="overflow-hidden glide-product">
    <vue-glide :options="glideOptions">
      <vue-glide-slide v-for="product in products.items" :key="product.id" class="relative box-shadow">
        <ProductCard :product="product" />
      </vue-glide-slide>
      <!-- <vue-glide-slide class="relative ">
        <div class="full-height flex column relative text-center justify-center items-center">
          <div>
            <q-btn unelevated icon="eva-arrow-forward" round size="16px" color="primary"
              :to="{ name: 'ProductCategory', params: { id: products.category_id } }"></q-btn>
            <div class="q-pt-md">Selengkapnya <br>di {{ products.title }}</div>
          </div>
        </div>
      </vue-glide-slide>
    -->
    </vue-glide>
  </div>
</template>

<script>
import ProductCard from 'components/ProductCard.vue'
export default {
  name: 'GlideProduct',
  props: {
    products: Object
  },
  components: { ProductCard },
  data() {
    return {
      glideOptions: {
        dragThreshold: 20,
        swipeThreshold: 10,
        touchAngle: 30,
        rewind: false,
        perView: 2,
        gap: 8,
        bound: true,
        peek: {
          before: 0,
          after: 30
        },
        breakpoints: {
          3000: {
            perView: 3,
            peek: {
              before: 0,
              after: 50
            },
          },
          900: {
            perView: 2,
            peek: {
              before: 0,
              after: 30
            },
          },
          400: {
            perView: 2,
            peek: {
              before: 0,
              after: 30
            },
          },
        }
      }
    }
  }
}
</script>
