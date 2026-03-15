<template>
   <q-page padding>
      <AppHeader :title="title" goBack>
      </AppHeader>
      <form @submit.prevent="submitPost">
         <q-card class="section shadow">
            <q-card-section>
               <div class="q-gutter-y-md">
                  <q-input outlined v-model="form.title" label="Title" required></q-input>
                  <q-select :options="tags" use-input new-value-mode="add" clearable hide-dropdown-icon outlined v-model="form.tags" label="Kategori"></q-select>
                  <q-list>
                     <q-item class="q-px-xs">
                        <q-item-section side>
                           <q-item-label>
                              <q-toggle v-model="form.is_promote">Tampil Diberanda</q-toggle>
                           </q-item-label>
                        </q-item-section>
                        <q-item-section>
                           <q-item-label>
                              <q-toggle v-model="form.is_listing">Tampil Dihalaman</q-toggle>
                           </q-item-label>
                        </q-item-section>
                     </q-item>
                  </q-list>

                  <div>
                     <div class="label-text">
                        Konten
                     </div>
                     <ContentEditor v-model="form.body" />
                  </div>
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
      <input type="file" class="hidden" ref="image" @change="updateImagePreview" />
   </q-page>
</template>

<script>
import { BaseApi } from 'src/boot/axios'
import { mapActions } from 'vuex'
import ContentEditor from 'components/ContentEditor.vue'
export default {
   name: 'PostCreate',
   components: { ContentEditor },
   data() {
      return {
          tags: [],
         form: {
            id: '',
            _method: 'POST',
            title: '',
            tags: '',
            body: '',
            image: '',
            is_listing: true,
            is_promote: true
         },
         imagePreview: '',
         is_edit_mode: false
      }
   },
   created() {
      this.getTags()
      if(this.$route.name == 'PostEdit') {
         this.is_edit_mode = true
         this.getData()
      }
   },
   computed: {
      title() {
         return this.is_edit_mode ?  "Edit Post" : 'Tambah Post'
      },
      loading() {
         return this.$store.state.loading
      }
   },
   methods: {
      ...mapActions('post', ['addPost', "updatePost", "getSinglePost"]),
      submitPost() {
         this.$store.commit('SET_LOADING', true)
         if(this.is_edit_mode) {
            this.updatePost(this.form)
         }else {
            this.addPost(this.form)

         }
      },
       getTags() {
         BaseApi.get('post-tags').then(res => {
            this.tags = res.data.data
         })
      },
       getData() {
         this.$store.commit("SET_LOADING", true);
         this.getSinglePost(this.$route.params.id).then((response) => {
            if (response.status == 200) {
               let responseData = response.data.data;
               this.form.id = responseData.id;
               this.form.title = responseData.title;
               this.form.category = responseData.category
                  ? responseData.category
                  : "";
               this.form.tags = responseData.tags ?? null;
               this.form.is_promote = responseData.is_promote;
               this.form.is_listing = responseData.is_listing;
               this.form.body = responseData.body;
               this.imagePreview = responseData.asset ? responseData.asset.src : null;
            }
         });
      },
      updateImagePreview() {
         this.form.image = this.$refs.image.files[0]
         if (!this.form.image) return

         const reader = new FileReader();

         reader.onload = (e) => {
            this.imagePreview = e.target.result;
         };

         reader.readAsDataURL(this.$refs.image.files[0]);
      },
      selectNewImage() {
         this.$refs.image.click()
      },
      removeImage() {
         this.imagePreview = ''
         this.form.image = ''
      }
   }
}
</script>
