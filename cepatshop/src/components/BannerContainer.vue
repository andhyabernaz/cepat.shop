<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import CarouselSection from 'components/CarouselSection.vue';
import { useRouter } from 'vue-router';

const router = useRouter()

defineProps({
   data: Array
})

const store = useStore()

const page_width = computed(() => store.state.page_width)

const maxPerView = computed(() => {
   if (page_width.value <= 800) {
      return 3
   }

   return 4
})

const showPost = (item) => {
   if (item.post) {
      router.push({ name: 'FrontPostShow', params: { slug: item.post.slug } })
   }
}

</script>

<template>
   <CarouselSection v-if="data && data.length" fluid class="banner-container block-container auto-padding-side"
      :maxPerView="maxPerView">
      <div v-for="banner in data" :key="banner.id" class="featured-banner">
         <div class="column col items-center text-center q-gutter-y-xs featured cursor-pointer"
            @click="showPost(banner)">
            <img v-if="banner.image" :src="banner.image_url" :alt="banner.label" />
               <div class="text-sm text-weight-medium">{{ banner.label }}</div>
               <div v-if="banner.description" class="text-grey-7 text-auto description">{{ banner.description }}
               </div>
         </div>
      </div>
   </CarouselSection>

</template>