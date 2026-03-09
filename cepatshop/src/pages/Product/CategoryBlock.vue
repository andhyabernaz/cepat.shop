<template>
   <div>
      <q-select readonly v-model="category_id" label="Kategori" @click="categoryModal = true" hide-dropdown-icon
         :options="category_options" emit-value map-options>
      </q-select>

      <q-dialog v-model="categoryModal">
         <q-card class="card-lg">
            <div class="text-weight-bold text-md q-pa-md">Pilih Kategori</div>
            <q-separator></q-separator>
            <q-list v-if="categories.data.length">
               <q-expansion-item v-for="cat in categories.data" :key="cat.id" :label="cat.title" group="cat"
                  expand-separator>
                  <q-item class="q-px-lg bg-grey-2" clickable @click="selectCategory(cat)">
                     <q-item-section side>
                        <q-icon :name="category_id == cat.id ? 'radio_button_checked' : 'radio_button_unchecked'"
                           :color="category_id == cat.id ? 'green' : 'grey-8'"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>{{ cat.title }} ( Parent )</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item v-for="child in cat.childs" :key="child.id" class="q-px-lg bg-grey-2" clickable
                     @click="selectCategory(child)">
                     <q-item-section side>
                        <q-icon :name="category_id == child.id ? 'radio_button_checked' : 'radio_button_unchecked'"
                           :color="category_id == child.id ? 'green' : 'grey-8'"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>{{ child.title }}</q-item-label>
                     </q-item-section>
                  </q-item>
                  <!-- <div v-if="!cat.childs.length" class="text-center q-pa-md bg-grey-2">Tidak ada subkategori</div> -->
               </q-expansion-item>
            </q-list>
            <div class="flex justify-end q-pa-md">
               <q-btn v-close-popup flat label="Close" color="primary"></q-btn>
            </div>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import categories from 'src/store/categories'


export default {
   props: ['modelValue'],
   data() {
      return {
         categoryModal: false,
         select_options: [],
      }
   },
   mounted() {
      if (!this.all_categories.data.length) {
         this.$store.dispatch('category/getAllCategories')
      }
      if (!this.categories.data.length) {
         this.$store.dispatch('category/getCategoriesWithChilds')
      }
   },
   computed: {
      category_id: {
         get() {
            return this.modelValue
         },
         set(val) {
            this.$emit('update:modelValue', val)
         }
      },
      all_categories() {
         return this.$store.state.category.all_categories
      },
      category_options() {
         if (this.all_categories.data.length) {
            return this.all_categories.data.map(el => ({ label: el.title, value: el.id }))
         }
         return []
      },
      categories() {
         return this.$store.state.category.category_with_childs
      },
   },
   methods: {
      selectCategory(item) {
         this.select_options = [{ label: item.title, value: item.id }]
         if (this.category_id == item.id) {
            this.category_id = ''
         } else {
            this.categoryModal = false
            this.category_id = item.id
         }
      },
   }
}
</script>
