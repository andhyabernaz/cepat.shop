<template>
   <div v-if="blocks.featured.length" class="overflow-hidden auto-padding block-container q-pt-md">
      <div class="featured-carousel overflow-hidden auto-padding">
         <CarouselSection :maxPerView="maxView">
            <div v-for="feature in blocks.featured" :key="feature.id">
               <div class="column col items-center text-center q-gutter-y-xs q-pa-sm featured cursor-pointer"
                  @click="showPost(feature)">
                  <q-img v-if="feature.image" :src="feature.image_url" :alt="feature.title" :ratio="16 / 9" />
                  <div class="text-sm text-weight-medium">{{ feature.label }}</div>
                  <div v-if="feature.description" class="text-grey-7 text-auto description">{{ feature.description }}
                  </div>
               </div>
            </div>
         </CarouselSection>
      </div>
   </div>
</template>

<script>
import CarouselSection from 'components/CarouselSection.vue';
export default {
   components: { CarouselSection },
   computed: {
      blocks() {
         return this.$store.state.front.blocks
      },
      page_width() {
         return this.$store.state.page_width
      },
      maxView() {
         if (this.page_width <= 768) {
            return 2
         }
         if (this.page_width > 768 && this.page_width <= 1024) {
            return 3
         }
         if (this.page_width > 1024 && this.page_width <= 1200) {
            return 4
         }

         return 5
      }
   },
   methods: {
      showPost(featured) {
         if (featured.post) {
            this.$router.push({ name: 'FrontPostShow', params: { slug: featured.post.slug } })
         }
      }
   }
}
</script>
