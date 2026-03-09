<template>
   <div class="bg-white max-width-mobile q-pb-xl" style="min-height: 280px">
      <q-list separator v-if="category_menu.length">
         <q-item class="sticky-top bg-primary text-white">
            <q-item-section side>
               <q-icon name="eva-funnel-outline" color="white"></q-icon>
            </q-item-section>
            <q-item-section>
               <q-item-label class="text-md">Kategori</q-item-label>
            </q-item-section>
            <q-item-section side>
               <q-btn flat icon="eva-close" dense @click="closeCategory" color="white"></q-btn>
            </q-item-section>
         </q-item>
         <q-expansion-item v-for="category in category_menu" :key="category.id" group="cat">
            <template v-slot:header>
               <q-item-section avatar>
                  <q-avatar>
                     <q-img :src="category.src"></q-img>
                  </q-avatar>
               </q-item-section>
               <q-item-section>
                  <q-item-label>{{ category.title }}</q-item-label>
               </q-item-section>
            </template>

            <q-list class="bg-grey-1" separator>
               <q-item clickable @click="handleShowCategory(category.slug)">
                  <q-item-section avatar>
                     <q-avatar>
                        <q-icon color="grey-7" name="eva-radio-button-off-outline"></q-icon>
                     </q-avatar>
                  </q-item-section>
                  <q-item-section>
                     <q-item-label>Semua di {{ category.title }} </q-item-label>
                  </q-item-section>
               </q-item>
               <q-item v-for="subcategory in category.childs" :key="subcategory.id" clickable
                  @click="handleShowCategory(subcategory.slug, true)">
                  <q-item-section avatar>
                     <q-avatar>
                        <q-icon color="grey-7" name="eva-radio-button-off-outline"></q-icon>
                     </q-avatar>
                  </q-item-section>
                  <q-item-section>
                     <q-item-label>{{ subcategory.title }}</q-item-label>
                  </q-item-section>
               </q-item>
            </q-list>
         </q-expansion-item>
      </q-list>
   </div>
</template>

<script>
export default {
   data() {
      return {
         loading: false,
      };
   },
   computed: {
      category_menu() {
         return this.$store.state.front.category_menu;
      },
      config() {
         return this.$store.state.config;
      },
   },
   methods: {
      handleShowCategory(id, subcategory = false) {
         this.closeCategory();

         if (id != this.$route.params.id) {
            let param = {
               category_id: id,
               subcategory: subcategory,
               per_page: this.config.catalog_product_limit,
               order_by: this.config.catalog_product_sort,
            };
            this.$emit('onSelect', param)
            this.$store.dispatch("product/productsByCategory", param);
            this.$router.push({ name: "ProductByCategory", params: { id: id } });
         }
      },
      closeCategory() {
         this.$store.commit("SET_MENU_CATEGORY", false);
      },
   },
   mounted() {
      if (!this.category_menu.length) {
         this.$store.commit("SET_LOADING", true);
         this.$store
            .dispatch("front/getCategories", { with: "child" })
            .then((res) => {
               this.$store.commit("front/SET_CATEGORY_MENU", res.data.data);
            })
            .finally(() => {
               this.$store.commit("SET_LOADING", false);
            });
      }
   },
};
</script>
