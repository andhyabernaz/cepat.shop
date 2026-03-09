<template>
  <div class="overflow-hidden">
    <swiper ref="mySwiper" :options="swiperOptions">
      <swiper-slide v-for="product in products.items" :key="product.id">
        <ProductCard :product="product" />
      </swiper-slide>
      <swiper-slide class="swiper-more">
        <div class="q-gutter-y-md text-center">
          <q-btn icon="eva-arrow-forward" round size="17px" color="primary"
            :to="{ name: 'ProductCategory', params: { id: products.category_id } }"></q-btn>
          <div class="text-weight-medium">Selengkapnya <br>di {{ products.title }}</div>
        </div>
      </swiper-slide>
    </swiper>
  </div>
</template>

<script>
import ProductCard from 'components/ProductCard'
export default {
  name: 'SwipperProduct',
  props: {
    products: Object
  },
  components: { ProductCard },
  data() {
    return {
      swiperOptions: {
        slidesPerView: 2,
        spaceBetween: 8,
      }
    }
  },
}
</script>
