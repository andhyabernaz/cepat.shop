<script setup>
// import { Api } from 'src/boot/axios';
// import { onMounted, ref } from 'vue';
import ProductCard from './ProductCard.vue';
// import ProductCardSkeleton from './ProductCardSkeleton.vue';

const props = defineProps(['products'])
</script>

<template>
   <div class="block-container q-pt-md">
      <div class="block-title q-mb-sm">
         <h3>Produk Serupa</h3>
      </div>
      <div class="product-grid items-stretch">
         <!-- <div v-for="a in 4" :key="a">
            <ProductCardSkeleton :width="skeletonWidth" />
         </div> -->

         <div v-for="(product, index) in products" :key="index" class="">
            <ProductCard :product="product" />

         </div>

      </div>
   </div>
</template>