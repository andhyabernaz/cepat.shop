<script>
import MediaComponent from 'components/MediaComponent.vue'
import { VueDraggableNext } from 'vue-draggable-next'
export default {
   components: { VueDraggableNext, MediaComponent },
   props: ['modelValue'],
   data() {
      return {
         modal: false,
      }
   },
   computed: {
      assets: {
         get() {
            return this.modelValue
         },
         set(val) {
            this.$emit('update:modelValue', val)
         }
      },
      selectedIds() {
         return this.assets.map(el => el.id)
      }
   },
   methods: {
      selectAsset(asset) {
         this.assets.push(asset)
      },
      removeAsset(id) {
         this.assets = this.assets.filter(el => el.id != id)
      }
   }
}

</script>

<template>
   <div>
      <div class="text-weight-medium text-md q-mb-sm text-grey-9">Product Images</div>
      <div class="q-py-md">
         <div class="">
            <div class="">


               <VueDraggableNext handle=".img_handle" v-model="assets" group="asset" class="box-image-container">
                  <div v-for="(element, idx) in assets" class="box-image relative cursor-pointer relative" :key="idx"
                     :class="{ 'feature-image-selected': assets[0].id == element.id }">
                     <img :src="element.url" class="bg-white img_handle" />
                     <div class="box-image-close-btn">
                        <q-btn dense @click="removeAsset(element.id)" size="10px" unelevated icon="close" color="red"
                           padding="1px" />
                     </div>
                     <!-- <q-badge floating>{{ idx + 1 }}</q-badge> -->
                  </div>
                  <div class="box-image outline cursor-pointer flex justify-center items-center" @click="modal = true">
                     <q-icon name="add_a_photo" size="lg" color="grey"></q-icon>
                  </div>
               </VueDraggableNext>
            </div>
            <div class="text-xs text-grey q-mt-md" v-if="assets.length">Geser image untuk mengurutkan</div>
         </div>
      </div>
      <q-dialog v-model=modal>
         <q-card class="" style="width:100%;max-width:1024px">
            <MediaComponent selectable closeable @onSelect="selectAsset" @onRemove="removeAsset"
               @onClose="modal = false" :selected="selectedIds" multiple per_page="21" />

         </q-card>
      </q-dialog>
   </div>
</template>