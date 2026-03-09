<template>
   <q-page padding class="bg-grey-2">
      <AppHeader :title="formTitle" goBack></AppHeader>
      <q-form @submit.prevent="submit">
          <q-card flat>
            <q-card-section>

               <div class="q-gutter-y-md">
                  <q-input required type="text" v-model="form.title" label="Title Produk"></q-input>
                  <money-formatter required v-model="form.price" prefix="Rp" stack-label />
                  <CategoryBlock v-model="form.category_id" />
      
                  <div class="q-mt-lg">
                     <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
                     <ContentEditor v-model="form.description" />
                  </div>
               </div>
            </q-card-section>
            </q-card>
         <q-card square flat class="section shadow q-mt-md">
            <q-card-section>
               <MediaBlock v-model="form.assets"></MediaBlock>
            </q-card-section>
         </q-card>

          <q-card class="q-mt-md shadow" v-if="affiliateConfig.is_active">
               <q-list bordered class="q-py-md">
                  <q-item>
                     <q-item-section avatar>
                        <q-checkbox v-model="form.aff_is_active"></q-checkbox>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Sistem Affiliasi?</q-item-label>
                        <q-item-label caption>Mengaktifkan sistem affiliasi</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item v-if="form.aff_is_active == true">
                     <q-item-section>
                        <div class="row">
                           <div class="col q-pa-xs">
                              <q-select filled type="number" v-model="form.aff_is_percentage" label="Tipe Komisi"
                                 :options="[
                                    { label: 'Nominal', value: false },
                                    { label: 'Persentase', value: true },
                                 ]" emit-value map-options />
                           </div>
                           <div class="col q-pa-xs">
                              <q-input filled type="number" v-model="form.aff_amount" label="Nilai Komisi" required
                                 min="1" />
                           </div>
                        </div>
                     </q-item-section>
                  </q-item>
               </q-list>
            </q-card>

          <q-card flat class="q-mt-md">
            <q-card-section>

               <section id="video">
                  <div class="text-lg q-mb-md flex justify-between items-center">
                     <div>Videos</div>
                     <q-btn label="Tambah Video" color="primary" size="12px" @click="handleAdVideo"></q-btn>
                  </div>
                   <div class="q-pb-md text-red text-sm" v-if="error_files">
                  Video required*
               </div>
                  <q-card v-for="(video, index) in form.digital_videos" :key="index" class="q-mb-md" bordered flat>
                     <q-card-section class="bg-grey-1">
                        <div class="flex justify-between q-mb-md items-center">
                           <div class="text-md2 text-weight-medium q-mb-sm">
                              Video {{ index + 1 }}
                           </div>
      
                           <q-btn label="remove" :color="form.digital_videos.length == 1 ? 'grey-3' : 'red'"
                              @click="removeVideo(index)" :disable="form.digital_videos.length == 1" outline
                              size="12px"></q-btn>
                        </div>
                        <div class="q-gutter-y-md">
                           <q-select required class="bg-white" outlined label="Video Ratio"
                              :options="ratio_options" emit-value map-options
                              v-model="form.digital_videos[index].video_ratio"></q-select>
                           <q-input required outlined class="bg-white" type="textarea" label="Video Embed"
                              hint="Harus menggunakan: <iframe></iframe>" v-model="form.digital_videos[index].video_embed"></q-input>
      
                           <q-input required class="bg-white" outlined label="Video Title"
                              v-model="form.digital_videos[index].video_title"></q-input>
      
      
                           <q-input required class="bg-white" outlined label="Video Durasi"
                              placeholder="eg: 20 menit" v-model="form.digital_videos[index].video_duration"></q-input>
      
                           <div>
                              <div class="label text-grey-7">Video Description</div>
                              <q-editor v-model="form.digital_videos[index].video_description"></q-editor>
                           </div>
                        </div>
                     </q-card-section>
                  </q-card>
               </section>
            </q-card-section>
            </q-card>

         <!-- <textarea v-model="text" style="white-space:pre-wrap"/> -->
         <div class="q-pa-md bg-white q-mt-md">
            <q-btn size="md" type="submit" :loading="loading" color="primary" class="full-width" unelevated
               label="Simpan Data">
               <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
            </q-btn>
         </div>
      </q-form>
   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
import ContentEditor from 'components/ContentEditor.vue'
import MediaBlock from './MediaBlock.vue'
import CategoryBlock from './CategoryBlock.vue'
import { mapActions } from 'vuex'
export default {
   components: { ContentEditor, MediaBlock, CategoryBlock },
   data() {
      return {
         subscription_options: [],
         ratio_options: [
            { label: "Ratio 21:9", value: "ratio-21by9" },
            { label: "Ratio 16:9", value: "ratio-16by9" },
            { label: "Ratio 4:3", value: "ratio-4by3" },
            { label: "Ratio 1:1", value: "ratio-1by1" },
         ],
           error_files: false,
         form: {
            id: "",
            title: "",
            price: 0,
            category_id: "",
            description: "",
            assets: [],
            product_type: 'Digital Video',
            aff_is_active: false,
            aff_is_percentage: false,
            aff_amount: 0,
            digital_videos: [
               {
                  video_title: "",
                  video_embed: "",
                  video_duration: "",
                  video_description: "",
                  video_ratio: "ratio-16by9",
               },
            ],
         },
         imagePreview: [],
         is_edit_product: false,
         is_clone_product: false,
         oldImages: [],
      };
   },
   computed: {
      affiliateConfig() {
         return this.$store.state.affiliate_config
      },
      loading() {
         return this.$store.state.loading;
      },
      formTitle() {
         if (this.is_edit_product) {
            return 'Edit Produk'
         }
         if (this.is_clone_product) {
            return 'Duplikat Produk'
         }
         return 'Tambah Produk'
      },
   },
   methods: {
       ...mapActions('product', ['productStore', 'productUpdate']),
      selectNewImage() {
         this.$refs.image.click();
      },
      getSubscriptionOptions() {
         BaseApi
            .get("product-subscriptions-options")
            .then((res) => {
               if (res.status == 200) {
                  this.subscription_options = res.data.data;
               }
            });
      },
      updateImagePreview() {
         let img = this.$refs.image.files;

         for (let i = 0; i < img.length; i++) {
            this.form.images.push(img[i]);

            const reader = new FileReader();

            reader.onload = (e) => {
               this.imagePreview.push(e.target.result);
            };

            reader.readAsDataURL(img[i]);
         }
      },
      removeImage(index) {
         this.imagePreview = this.imagePreview.filter(function (el, i) {
            return i !== index;
         });
         this.form.images = this.form.images.filter(function (el, i) {
            return i !== index;
         });
      },
      removeOldImage(img) {
         this.form.del_images.push(img.id);
         this.oldImages = this.oldImages.filter(function (el) {
            return el.id !== img.id;
         });
      },
      submit() {
         if (!this.form.digital_videos.length) {
            this.error_files = true;

            this.$q.notify({
               type: "negative",
               message: "Video is required",
            });

            this.jumpTo("video");

            return;
         }

         if (this.is_edit_product) {
            this.form._method = "PUT";
            this.productUpdate(this.form)
         } else {
            this.form._method = "POST";
            this.productStore(this.form)
         }
      },
      handleAdVideo() {
         let tpl = {
            video_title: "",
            video_duration: "",
            video_embed: "",
            video_description: "",
            video_ratio: "ratio-16by9"
         };

         this.form.digital_videos.push(tpl);
      },
      removeVideo(index) {
         this.form.digital_videos.splice(index, 1);
      },
      getProduct() {
         this.$store
            .dispatch("product/getProductById", this.$route.params.id)
            .then((response) => {
               if (response.status == 200) {
                  this.setData(response.data.data);
               }
            });
      },
      setData(data) {
         this.form.id = data.id;
         this.form._method = "PUT";
         this.form.title = data.title;
         this.form.price = data.price;
         this.form.description = data.description;
         this.form.category_id = data.category_id;
         this.form.aff_is_active = data.aff_is_active
         this.form.aff_is_percentage = data.aff_is_percentage
         this.form.aff_amount = data.aff_amount

         if (this.is_edit_product) {
            this.form.digital_videos = data.digital_videos.map((el) => ({
               video_title: el.video_title,
               video_duration: el.video_duration,
               video_embed: el.video_embed,
               video_description: el.video_description,
               video_ratio: el.video_ratio,
            }));
            this.form.assets = data.assets;
         }


      },
   },

   created() {
      this.$store.dispatch('getAffiliateConfig')
   },
   mounted() {

      if (this.$route.name == "ProductVideoEdit") {
         this.is_edit_product = true;
         this.getProduct();
      }
      if (this.$route.name == "ProductVideoClone") {
         this.is_clone_product = true;
         this.getProduct();
      }

      this.getSubscriptionOptions();
   },
};
</script>
