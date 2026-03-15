<script setup>
import { Api } from 'src/boot/axios';
import { onMounted, ref } from 'vue';

const marketplaces = ref([])

const getData = () => {
   Api.get('marketplaces').then(res => {
      marketplaces.value = res.data.data
   })
}

onMounted(() => {
   getData()
})
</script>

<template>
   <div v-if="marketplaces.length" flat class="q-pa-md">
      <div class="text-md text-weight-bold q-mb-sm">
         Kunjungi Toko Kami
      </div>
      <div class="q-gutter-sm row">
         <a v-for="item in marketplaces" :key="item.id" :href="item.url" class="mp_link" target="new">
            <img :src="item.icon" :alt="`Beli via ${item.provider}`">
         </a>
      </div>
   </div>
</template>