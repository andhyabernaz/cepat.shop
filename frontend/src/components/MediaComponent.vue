<script>
import { BaseApi } from 'boot/axios'
import SimplePagination from 'components/SimplePagination.vue';
export default {
   components: { SimplePagination },
   props: {
      selectable: Boolean,
      deletable: Boolean,
      closeable: Boolean,
      multiple: Boolean,
      per_page: {
         type: [String, Number],
         default: 30
      },
      selected: {
         default: []
      },
      contentHeight: {
         default: '100%'
      },
      imageWidth: {
         default: '126px'
      }
   },
   data() {
      return {
         modal: false,
         form: {
            images: []
         },
         queryParams: {
            per_page: 30
         },
         images: [],
         selectedAssetId: null
      }
   },
   computed: {
      assets: {
         get() {
            return this.$store.state.assets
         },
         set(val) {
            this.$store.commit('SET_ASSETS', val)
         }
      },
      imagePreview() {
         if (this.images.length) {
            return this.images.map(el => {
               return URL.createObjectURL(el)
            })
         }
         return []
      },
      loading() {
         return this.$store.state.loading
      }
   },
   methods: {
      getData(url = null) {
          this.$q.loading.show()
         if (!url) {
            this.queryParams.per_page = this.per_page
            url = `assets?${new URLSearchParams(this.queryParams).toString()}`
         }
         BaseApi.get(url).then(res => {
            this.assets = res.data.data
         }).finally(() => {
             this.$q.loading.hide()
         })
      },
      uploadAsset() {
         this.$q.loading.show()
         const formData = new FormData()

         for (let i = 0; i < this.images.length; i++) {
            formData.append(`images[${i}]`, this.images[i])
         }

         BaseApi.post('assets', formData, { headers: { 'content-Type': 'multipart/formData' } }).then(() => {
            this.modal = false
            this.getData()
         }).finally(() => {
             this.$q.loading.hide()
         })
      },
      destroyAsset(asset) {
         this.$q.dialog({
            title: 'Konfirmasi',
            message: 'Data yg dihapus tidak dapat dikembalikan',
            cancel: true
         }).onOk(() => {
            this.$q.loading.show()
            BaseApi.delete(`assets/${asset.id}`).then(() => {
               this.handleremoveAsset(asset)
               this.getData()
            }).finally(() => {
                this.$q.loading.hide()
            })
         })
      },
      handleUploadButton() {
         const input = document.getElementById('upload')

         input.value = ''
         input.click()
      },
      handleUpdateInput(e) {
         let files = e.target.files
         for (let i = 0; i < files.length; i++) {
            this.images.push(files[i])
         }
         this.uploadAsset()
      },
      close() {
         this.images = []
         this.modal = false
      },
      handleremoveAsset(asset) {
         if (this.hasSelected(asset)) {
            this.$emit('onRemove', asset.id)
         } else {
            this.$emit('onSelect', asset)
            if (this.multiple == false) {
               this.$emit('onClose')
            }
         }
      },
      hasSelected(asset) {
         if (this.selected.length) {
            return this.selected.includes(asset.id)
         }
         return false
      }


   },

   mounted() {
      if (!this.assets.data.length) {
         this.getData()
      }
   }
}


</script>

<template>
   <div>
      <q-card class="section shadow" style="width:100%">
         <q-card-section>
            <div class="card-title q-mb-md flex justify-between items-center">
               <div>Media</div>
               <div class="row q-gutter-x-sm q-pb-sm">
                  <q-btn v-if="$can('upload-media')" icon="upload" color="primary" label="Upload"
                     @click="handleUploadButton"></q-btn>
                  <q-btn v-if="closeable" outline icon="close" label="Close" color="red"
                     @click="$emit('onClose')"></q-btn>
               </div>
            </div>
            <div id="media-container" :style="`height:${contentHeight}`" style="overflow-y:auto;">
               <div class="media-inner relative thumbnail" v-for="asset in assets.data" :key="asset.id"
                  :class="{ 'media-selected': hasSelected(asset) }" @mouseover="selectedAssetId = asset.id"
                  @mouseleave="selectedAssetId = null">
                  <img :src="asset.url" />

                  <div class=" absolute-bottom asset-btn" v-if="selectable">
                     <q-btn v-show="selectedAssetId == asset.id" size="sm" unelevated
                        :color="hasSelected(asset) ? 'grey-8' : 'teal'" class="full-width z-100"
                        @click="handleremoveAsset(asset)">{{ hasSelected(asset) ? 'Unselect' : 'Select' }}</q-btn>

                  </div>
                  <div class="absolute-top" v-if="deletable && $can('delete-media')">
                     <q-btn v-show="selectedAssetId == asset.id" size=" sm" unelevated color="red" class="z-100"
                        icon="delete" dense @click="destroyAsset(asset)"></q-btn>
                  </div>
               </div>
            </div>
            <SimplePagination v-bind="assets" @loadUrl="getData" class="q-mt-sm"></SimplePagination>
         </q-card-section>
      </q-card>
      <input type="file" accept="image/*" class="hidden" id="upload" multiple @change="handleUpdateInput">

      <q-dialog v-model="modal" persistent no-shake>
         <q-card class="card-lg">
            <q-card-section>
               <div class="row q-gutter-sm">
                  <q-img v-for="src in imagePreview" :key="src" :src="src" width="150px">

                  </q-img>
               </div>
               <div class="card-action">
                  <q-btn :loading="loading" label=" Upload" color="primary" unelevated @click="uploadAsset"></q-btn>
                  <q-btn :disable="loading" label=" Cancel" color="primary" outline @click="close"></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>

   </div>
</template>