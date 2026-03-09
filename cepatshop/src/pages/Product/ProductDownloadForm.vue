<template>
   <q-page padding class="bg-grey-2">
      <AppHeader :title="formTitle" goBack></AppHeader>
      <q-form @submit.prevent="submit">
         <q-card flat>
            <q-card-section>
               <div class="">
                  <div class="q-gutter-y-md">
                     <q-input required type="text" v-model="form.title" label="Title Produk"></q-input>

                     <money-formatter required v-model="form.price" prefix="Rp" stack-label />
                     <CategoryBlock v-model="form.category_id" />



                     <div class="q-mt-sm q-mb-sm">
                        <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
                        <ContentEditor v-model="form.description" />
                     </div>

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
                           <q-select filled type="number" v-model="form.aff_is_percentage" label="Tipe Komisi" :options="[
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
         <q-card flat class="q-mt-md" id="files">
            <q-card-section class="q-py-lg" style="min-height: 200px">
               <div class="q-mb-md">
                  <div class="text-lg flex justify-between items-center q-mb-xs">
                     <div>File Download</div>
                     <q-btn label="Tambah File" color="primary" size="12px" @click="handleAddFile"></q-btn>
                  </div>
                  <!-- <div class="text-sm text-grey-7">
                     MAX UPLOAD FILE {{ uploadFIleTotal }}MB/{{
                        current_config.max_upload_perpost
                     }}MB
                  </div> -->
               </div>
               <div class="q-pb-md text-red text-sm" v-if="error_files">
                  Files required*
               </div>
               <input type="file" class="hidden" ref="fileinput" @change="changeFileInput" />
               <q-list separator>
                  <q-expansion-item v-for="(file, index) in form.digital_downloads" :label="`Files #${index + 1}`"
                     :key="index" default-opened>
                     <q-card class="q-mb-md bg-grey-1" bordered flat>
                        <q-card-section>
                           <q-item dense class="q-mb-sm" v-if="form.digital_downloads[index].download_type == 'file'">
                              <q-item-section avatar>
                                 <q-icon name="attach_file"></q-icon>
                              </q-item-section>
                              <q-item-section>
                                 {{ form.digital_downloads[index].filename }}
                              </q-item-section>
                           </q-item>

                           <q-item dense v-if="form.digital_downloads[index].download_type == 'url'" class="q-mb-sm">
                              <q-item-section>
                                 <q-input required filled v-model="form.digital_downloads[index].filepath"
                                    label="Download Url" outlined stack-label></q-input>
                              </q-item-section>
                           </q-item>

                           <q-item dense>
                              <q-item-section>
                                 <q-input required filled v-model="form.digital_downloads[index].filename"
                                    label="Filename" outlined stack-label class="q-mb-md"></q-input>
                                 <q-input filled type="textarea" label="Deskripsi (optional)"
                                    v-model="form.digital_downloads[index].caption" rows="3"></q-input>
                              </q-item-section>
                           </q-item>
                           <div class="flex justify-end q-mt-sm q-px-md">
                              <q-btn :label="`remove file #${index + 1}`" color="red" @click="removeFile(index, file)"
                                 outline size="12px"></q-btn>
                           </div>
                        </q-card-section>
                     </q-card>
                  </q-expansion-item>
               </q-list>
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
      <q-dialog v-model="addFileModal">
         <q-card class="card-md">
            <q-card-section>
               <div class="block-title flex justify-between items-center">
                  <h5>Pilih Tipe File</h5>
                  <q-btn flat round icon="close" v-close-popup></q-btn>
               </div>
               <q-list>
                  <q-item clickable @click="handleFileUpload">
                     <q-item-section avatar>
                        <q-icon name="upload"></q-icon>
                     </q-item-section>
                     <q-item-section>File Upload</q-item-section>
                  </q-item>
                  <q-item clickable @click="handleAddExternalUrl">
                     <q-item-section avatar>
                        <q-icon name="link"></q-icon>
                     </q-item-section>
                     <q-item-section>External Url</q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { BaseApi } from "src/boot/axios";
import ContentEditor from 'components/ContentEditor.vue'
import MediaBlock from './MediaBlock.vue'
import CategoryBlock from './CategoryBlock.vue'
import { mapActions } from 'vuex'
export default {
   components: { ContentEditor, MediaBlock, CategoryBlock },
   data() {
      return {
         addFileModal: false,
         subscription_options: [],
         error_files: false,
         form: {
            id: "",
            title: "",
            price: 0,
            category_id: "",
            description: "",
            assets: [],
            digital_downloads: [],
            product_type: 'Digital Download',
            aff_is_active: false,
            aff_is_percentage: false,
            aff_amount: 0,
         },
         imagePreview: [],
         is_clone_product: false,
         is_edit_product: false,
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
      current_config() {
         return this.$store.state.config
      }
      // uploadFIleTotal() {
      //    let size = 0;
      //    let items = this.form.digital_downloads.filter((el) => el.download_type == "file");
      //    if (items.length) {
      //       let curSize = items.reduce(
      //          (acc, obj) => parseFloat(obj.filesize) + acc,
      //          0
      //       );
      //       size = curSize / 1024;
      //    }
      //    return size.toFixed(2);
      // },
      // hasMaxUpload() {
      //    return this.uploadFIleTotal >= this.current_config.max_upload_perpost;
      // },
   },
   methods: {
      ...mapActions('product', ['productStore', 'productUpdate']),
      selectNewImage() {
         this.$refs.image.click();
      },
      getSubscriptionOptions() {
         BaseApi.get("product-subscriptions-options")
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
         this.oldImages = this.oldImages.filter(function (el) {
            return el.id !== img.id;
         });
         this.form.del_images.push(img.id);
      },
      submit() {
         if (!this.form.digital_downloads.length) {
            this.error_files = true;

            this.$q.notify({
               type: "negative",
               message: "File is required",
            });

            this.jumpTo("files");

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
      handleAddFile() {
         this.addFileModal = true;
      },
      handleAddExternalUrl() {
         this.form.digital_downloads.push({
            filepath: "",
            filename: "",
            caption: "",
            download_type: "url",
         });
         this.addFileModal = false;
      },
      handleFileUpload() {
         this.addFileModal = false;
         this.$refs.fileinput.value = "";
         this.$refs.fileinput.click();
      },
      changeFileInput(e) {
         let file = e.target.files[0];

         if (!file) return;

         this.uploadFile(file);
      },
      removeFile(index, file) {
         if (file.id) {
            this.$q
               .dialog({
                  title: "Konfirmasi",
                  message: "Yakin akan menghapus file?",
                  cancel: true,
               })
               .onOk(() => {
                  this.form.digital_downloads.splice(index, 1);
                  this.deleteFile(file);
               });
         } else {
            this.form.digital_downloads.splice(index, 1);
         }
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
      uploadFile(file) {
         this.$q.loading.show();
         let formData = new FormData();
         formData.append("file", file);
         BaseApi
            .post("digital-download/upload", formData, {
               headers: {
                  "content-Type": "multipart/formData",
               },
            })
            .then((res) => {
               if (res.status == 200) {
                  this.form.digital_downloads.push({
                     filename: res.data.data.filename,
                     filepath: res.data.data.filepath,
                     filesize: res.data.data.filesize,
                     caption: "",
                     download_type: "file",
                  });
               }
            })
            .finally(() => this.$q.loading.hide());
      },
      deleteFile(file) {
         BaseApi.post("digital-download/delete/" + file.id);
      },
      setData(data) {
         this.form.id = data.id;
         this.form.title = data.title;
         this.form.price = data.price;
         this.form.description = data.description;
         this.form.category_id = data.category_id;
         this.form.aff_is_active = data.aff_is_active
         this.form.aff_is_percentage = data.aff_is_percentage
         this.form.aff_amount = data.aff_amount

         this.form.assets = data.assets;
         if (this.is_edit_product) {
            this.form.digital_downloads = data.digital_downloads;

            this.form.assets = data.assets;
         }

      },
   },
   created() {
      this.$store.dispatch('getAffiliateConfig')
   },
   mounted() {

      if (this.$route.name == "ProductDownloadEdit") {
         this.is_edit_product = true;
         this.getProduct();
      }
      if (this.$route.name == "ProductDownloadClone") {
         this.is_clone_product = true;
         this.getProduct();
      }

      this.getSubscriptionOptions();
   },
};
</script>
