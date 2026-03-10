<template>
   <div>
      <div id="categories" v-if="categories && categories.ready && categories.data && categories.data.length" class="block-container">
         <div class="auto-padding">
            <div class="block-title featured q-mb-md">
               <h2>Kategori</h2>
            </div>
            <div class="block-content q-pb-sm">
               <GridContainer>
                  <div v-for="cat in categories.data" :key="cat.id" class="bg-white q-pa-sm">
                     <q-img v-if="cat.src" :src="cat.src" ratio="1" @click="openCategory(cat)"
                        class="cursor-pointer rounded-borders">
                     </q-img>
                     <div class="text-category-auto text-center q-mt-xs">{{ cat.title }}</div>
                  </div>
               </GridContainer>
            </div>
         </div>
      </div>
   </div>
</template>

<script>
import GridContainer from 'components/GridContainer.vue';
export default {
   components: { GridContainer },
   computed: {
      categories() {
         return this.$store.state.front.categories
      },
   },
   methods: {
      openCategory(cat) {
         if (cat) {
            this.$router.push({ name: 'ProductByCategory', params: { id: cat.slug } })
         }
      }
   }

}
</script>