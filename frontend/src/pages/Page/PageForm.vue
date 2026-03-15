<template>
   <q-page padding>
      <AppHeader :title="title" goBack>
      </AppHeader>
      <form @submit.prevent="submitPage">
         <q-card class="section shadow">
            <q-card-section>
               <div class="q-gutter-y-md">
                  <q-input outlined v-model="form.title" label="Title" required></q-input>
                  <q-input outlined v-model="form.slug" label="Slug"></q-input>
                  <q-input outlined v-model="form.category" label="Kategori"></q-input>

                  <q-select outlined emit-value map-options v-model="form.status" label="Status" :options="statusOptions" />

                  <q-select outlined emit-value map-options v-model="form.theme" label="Theme" :options="themeOptions" />

                  <q-input outlined type="textarea" autogrow v-model="form.meta_description" label="Meta Description" />

                  <div>
                     <div class="label-text">
                        Konten
                     </div>
                     <ContentEditor v-model="form.content" />
                  </div>

                  <q-expansion-item dense expand-separator label="Facebook Pixel (Opsional)">
                     <div class="q-gutter-y-md q-pt-md">
                        <q-input outlined v-model="form.pixel_id" label="Pixel ID" />
                        <q-input outlined v-model="form.pixel_access_token" label="Pixel Access Token" />
                        <q-input outlined v-model="form.pixel_test_event_code" label="Pixel Test Event Code" />
                     </div>
                  </q-expansion-item>
               </div>

               <div style="min-height: 100px;">
                  <q-btn label="Upload Gambar" size="sm" color="primary" icon="eva-upload" class="q-mt-md" type="button"
                     @click.prevent="selectNewImage" />
                  <div class="text-yellow-10 q-py-sm">Untuk hasil terbaik gunakan format gambar dengan rasio 16:9</div>
                  <q-list v-if="imagePreview" class="q-py-md">
                     <q-item>
                        <q-item-section>
                           <img :src="imagePreview" class="shadow-4 q-pa-xs bg-white"
                              style="width:100px;height:70px;object-fit:cover;" />
                        </q-item-section>
                        <q-space />
                        <q-item-section side>
                           <q-btn @click="removeImage" size="sm" color="red" glossy round icon="eva-trash-2" />
                        </q-item-section>
                     </q-item>
                  </q-list>
               </div>
            </q-card-section>
         </q-card>

         <div class="card-action">
            <q-btn :loading="loading" label="Simpan Data" type="submit" color="primary" class="full-width"></q-btn>
         </div>
      </form>
      <input type="file" class="hidden" ref="image" accept="image/*" @change="updateImagePreview" />
   </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import ContentEditor from 'components/ContentEditor.vue'
export default {
   name: 'PageCreate',
   components: { ContentEditor },
   data() {
      return {
         statusOptions: [
            { label: 'Draft', value: 'draft' },
            { label: 'Published', value: 'published' },
         ],
         themeOptions: [
            { label: 'Light', value: 'light' },
            { label: 'Dark', value: 'dark' },
         ],
         form: {
            id: '',
            _method: 'POST',
            title: '',
            slug: '',
            category: '',
            status: 'draft',
            theme: 'light',
            meta_description: '',
            content: '',
            featured_image: null,
            remove_featured_image: false,
            pixel_id: '',
            pixel_access_token: '',
            pixel_test_event_code: '',
         },
         existingAssetSrc: '',
         is_edit_mode: false
      }
   },
   created() {
      if (this.$route.name == 'PageEdit') {
         this.is_edit_mode = true
         this.getData()
      }
   },
   computed: {
      title() {
         return this.is_edit_mode ? 'Edit Halaman' : 'Tambah Halaman'
      },
      loading() {
         return this.$store.state.loading
      },
      imagePreview() {
         if (this.form.featured_image) {
            return URL.createObjectURL(this.form.featured_image)
         }
         return this.existingAssetSrc || ''
      }
   },
   methods: {
      ...mapActions('page', ['addPage', 'updatePage', 'getSinglePage']),
      submitPage() {
         this.$store.commit('SET_LOADING', true)
         if (this.is_edit_mode) {
            this.updatePage(this.form)
         } else {
            this.addPage(this.form)
         }
      },
      getData() {
         this.$store.commit('SET_LOADING', true)
         this.getSinglePage(this.$route.params.id).then((response) => {
            if (response.status == 200) {
               let responseData = response.data.data
               this.form.id = responseData.id
               this.form.title = responseData.title
               this.form.slug = responseData.slug
               this.form.category = responseData.category || ''
               this.form.status = responseData.status || 'draft'
               this.form.theme = responseData.theme || 'light'
               this.form.meta_description = responseData.meta_description || ''
               this.form.content = responseData.content || ''
               this.form.pixel_id = responseData.pixel_id || ''
               this.form.pixel_access_token = responseData.pixel_access_token || ''
               this.form.pixel_test_event_code = responseData.pixel_test_event_code || ''
               this.existingAssetSrc = responseData.asset?.src || ''
            }
         }).finally(() => {
            this.$store.commit('SET_LOADING', false)
         })
      },
      selectNewImage() {
         this.$refs.image.click()
      },
      updateImagePreview() {
         const file = this.$refs.image.files[0]
         if (!file) return
         this.form.featured_image = file
         this.form.remove_featured_image = false
      },
      removeImage() {
         this.form.featured_image = null
         this.form.remove_featured_image = true
         this.existingAssetSrc = ''
         if (this.$refs.image) {
            this.$refs.image.value = ''
         }
      },
   },
   mounted() {
      this.$canAccess('view-content')
   }
}
</script>

