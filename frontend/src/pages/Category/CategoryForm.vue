<template>
   <q-page padding>
      <AppHeader title="Form Kategori" goBack></AppHeader>
      <form @submit.prevent="submit">
         <q-card class="section shadow">
            <q-card-section>
               <div class="q-gutter-y-md">
                  <q-select :options="parentCategoryOptionsFiltered" map-options emit-value required filled
                     label="Parent" v-model="form.category_id" />
                  <q-input required filled label="Title" v-model="form.title" />
                  <q-input required filled mask="####" label="Urutan" v-model="form.weight" />
                  <q-input filled type="textarea" rows="3" label="Meta Deskripsi" v-model="form.description" />
                  <div class="border rounded q-pa-sm">
                     <div>
                        <q-toggle v-model="form.is_front" label="Tampilkan produk di beranda"></q-toggle>
                        <div class="text-xs text-grey-6 q-pa-xs">
                           Pilihan apakah produk dibawah kategori ini akan di listing di
                           halaman beranda
                        </div>
                     </div>
                  </div>

                  <input type="file" class="hidden" ref="image" @change="updateImagePreview" />
                  <input type="file" class="hidden" ref="banner" @change="updateBannerPreview" />



               </div>
            </q-card-section>
         </q-card>
         <q-card class="section q-mt-md shadow">
            <q-card-section>
               <div class="card-subtitle">Kategori Ikon / Image</div>
               <section id="image-banner q-mt-md" v-if="!form.category_id">
                  <q-btn label="Upload Icon / Image" color="teal" icon="eva-upload" size="11px" type="button"
                     @click.prevent="selectNewImage" />

                  <div class="text-xs text-red q-my-md" v-if="errors.images">
                     {{ errors.images[0] }}
                  </div>
                  <div>

                     <div class="q-mt-md" v-if="imagePreview">
                        <div class="relative">
                           <img :src="imagePreview" class="thumbnail"
                              style="width: 120px; height: auto; object-fit: contain" />
                           <div class="absolute-top">
                              <q-btn @click="removeImage" color="red" dense icon="delete" />

                           </div>
                        </div>
                     </div>
                  </div>
               </section>
            </q-card-section>

         </q-card>
         <q-card class="section q-mt-md shadow">
            <q-card-section>
               <div class="card-subtitle">Kategori Banner</div>
               <section id="image-banner q-mt-md" v-if="!form.category_id">
                  <div class="row items-center justify-between">
                     <q-btn label="Upload Banner" size="11px" color="teal" icon="eva-upload" type="button"
                        @click.prevent="selectNewBanner" />
                  </div>
                  <div class="q-mt-md" v-if="bannerPreview">
                     <div class="relative">
                        <img :src="bannerPreview" class="thumbnail"
                           style="height: 100px; width: auto; object-fit: contain" />
                        <div class="absolute-top">
                           <q-btn @click="removeBanner" color="red" dense icon="delete" />

                        </div>
                     </div>

                     <div class="q-mt-sm">
                        <q-checkbox v-model="form.is_background_banner"
                           label="Tampilan Sebagai Background"></q-checkbox>
                     </div>
                  </div>
               </section>
            </q-card-section>
         </q-card>
         <div class="card-action">
            <q-btn :loading="loading" class="full-width" type="submit" label="Simpan Data" color="primary">
            </q-btn>
         </div>
      </form>
   </q-page>
</template>

<script>
import { mapActions, mapState } from "vuex";
export default {
   data() {
      return {
         category: this.$route.query.edit
            ? this.$store.getters["category/getById"](
               parseInt(this.$route.query.edit)
            )
            : "",
         formType: "Add",
         form: {
            id: "",
            category_id: "",
            images: "",
            title: "",
            description: "",
            is_front: true,
            weight: 1,
            banner: "",
            remove_banner: false,
            remove_image: false,
            is_background_banner: false,
         },
         modal: false,
         imagePreview: "",
         bannerPreview: "",
      };
   },
   watch: {
      "form.category_id"(val) {
         if (val) {
            this.unsetDataCategory();
         }
      },
   },
   computed: {
      ...mapState({
         loading: (state) => state.loading,
         errors: (state) => state.errors,
      }),
      parentCategoryOptions() {
         return this.$store.getters["category/getParentCategoryOptions"];
      },
      parentCategoryOptionsFiltered() {
         if (this.category) {
            return this.parentCategoryOptions.filter(
               (el) => el.value != this.category.id
            );
         }
         return [];
      },
   },

   methods: {
      ...mapActions("category", [
         "categoryStore",
         "getCategories",
         "getCategory",
         "categoryUpdate",
      ]),
      submit() {
         let formData = new FormData();
         formData.append("title", this.form.title);
         formData.append("category_id", this.form.category_id);
         formData.append("description", this.form.description);
         formData.append("images", this.form.images);
         formData.append("banner", this.form.banner);
         formData.append("weight", this.form.weight);
         formData.append("is_front", this.form.is_front);
         formData.append("is_background_banner", this.form.is_background_banner);

         if (this.formType == "Edit") {
            formData.append("remove_banner", this.form.remove_banner);
         }
         if (this.formType == "Edit") {
            formData.append("remove_image", this.form.remove_image);
         }

         if (this.formType == "Add") {
            formData.append("_method", "POST");
            this.categoryStore(formData);
         }
         if (this.formType == "Edit") {
            formData.append("_method", "PUT");
            this.categoryUpdate({ id: this.form.id, data: formData });
         }
      },
      selectNewImage() {
         this.$refs.image.click();
      },
      selectNewBanner() {
         this.$refs.banner.click();
      },
      updateImagePreview() {
         let file = this.$refs.image.files[0];
         if (!file) return;

         this.form.images = file;

         const reader = new FileReader();

         reader.onload = (e) => {
            this.imagePreview = e.target.result;
         };

         reader.readAsDataURL(file);
      },
      updateBannerPreview() {
         let file = this.$refs.banner.files[0];
         if (!file) return;

         this.form.banner = file;

         const reader = new FileReader();

         reader.onload = (e) => {
            this.bannerPreview = e.target.result;
         };

         reader.readAsDataURL(file);
      },
      setData() {
         this.form.id = this.category.id;
         this.form.title = this.category.title;
         this.form.description = this.category.description
            ? this.category.description
            : "";
         this.form.weight = this.category.weight;
         this.form.is_background_banner = this.category.is_background_banner;
         this.form.category_id = this.category.category_id ?? "";
         this.form.is_front = this.category.is_front;
         this.form.is_special = this.category.is_special;
         this.imagePreview = this.category.src ? this.category.src : "";
         this.bannerPreview = this.category.banner_src
            ? this.category.banner_src
            : "";
      },
      clearForm() {
         this.form.id = "";
         this.form.title = "";
         this.form.description = "";
         this.form.weight = 1;
         this.form.is_front = true;
         this.form.is_background_banner = false;
         this.form.is_special = "";
         this.form.images = ""
         this.form.banner = ""
         this.form.remove_banner = false;

         this.imagePreview = "";
         this.bannerPreview = "";
      },
      removeImage() {
         this.imagePreview = "";
         this.form.images = "";
         this.form.remove_image = true;
      },
      removeBanner() {
         this.bannerPreview = "";
         this.form.banner = "";
         this.form.remove_banner = true;
      },
      unsetDataCategory() {
         if (this.form.banner) {
            this.removeBanner();
         }
         if (this.form.images) {
            this.removeImage();
         }
      },
   },
   mounted() {
      if (this.$route.query.parent_id) {
         this.form.category_id = this.$route.query.parent_id;
      }
   },
   created() {
      this.clearForm();
      if (this.$route.name == "CategoryFormEdit") {
         this.formType = "Edit";

         if (!this.category) {
            this.getCategory(this.$route.params.category_id).then((response) => {
               this.category = response.data.data;
               this.setData();
            });
         } else {
            this.setData();
         }
      } else {
         this.formType = "Add";
         this.category = {};
         this.clearForm();
      }

      if (this.parentCategoryOptions.length <= 1) {
         return this.$store.dispatch("category/getCategoriesWithChilds");
      }
   },
};
</script>
