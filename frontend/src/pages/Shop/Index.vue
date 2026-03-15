<template>
   <q-page padding>
      <AppHeader title="Pengaturan Toko"></AppHeader>
      <div class="bg-white q-mb-md">
         <q-tabs v-model="tab" align="left">
            <q-tab name="shop" label="Konfigurasi Toko"></q-tab>
            <q-tab name="pwa" label="Konfigurasi PWA"></q-tab>
         </q-tabs>
      </div>
      <q-tab-panels v-model="tab" animated>
         <q-tab-panel name="shop" class="q-pa-none">
            <q-form @submit.prevent="submitData">
               <q-card class="section shadow">
                  <q-card-section>
                     <div class="q-gutter-y-sm">
                        <!-- <div class="text-md">Shop Config</div> -->
                        <q-input required filled label="Nama Toko" v-model="form.name"></q-input>
                        <q-input class="q-pt-sm" required filled label="Nomor Whatsapp" v-model="form.phone" lazy-rules
                           :rules="[requiredRules, validPhoneRules]" placeholder="0812*******"></q-input>
                        <q-input required type="email" filled label="Email Toko" v-model="form.email"></q-input>
                        <q-input class="q-pt-sm" filled label="Slogan" v-model="form.slogan"></q-input>
                        <div class="q-my-xs text-red text-sm" v-if="errors.phone">
                           Nomor Whatsapp harus berupa angka
                        </div>
                        <q-input class="q-pt-sm" filled type="textarea" rows="3" label="Deskripsi Toko"
                           v-model="form.description"></q-input>
                        <div class="q-my-md">
                           <div for="" class="text-grey-8 q-mb-sm">Alamat Toko</div>
                           <q-editor v-model="form.address"
                              :toolbar="[['bold', 'italic', 'underline', 'link', 'unordered', 'removeFormat', 'viewsource']]"></q-editor>
                        </div>
                     </div>

                     <div class="q-py-md">
                        <input accept="image/*" filled type="file" class="hidden" ref="image"
                           @change="handleChangeImage" />
                        <q-btn v-if="$can('update-store')" label="Upload Logo" size="sm" color="primary" icon="eva-upload" class="mt-2 mr-2"
                           type="button" @click.prevent="handleBtnUpload"></q-btn>
                        <div class="text-xs text-red q-my-md" v-if="errors.logo">
                           {{ errors.logo[0] }}
                        </div>
                     </div>
                     <q-list v-if="imagePreview">
                        <q-item>
                           <q-item-section top>
                              <img :src="imagePreview" class="bg-grey-1 img-thumbnail"
                                 style="width: 100px; height: 100px; object-fit: contain" />
                           </q-item-section>
                           <q-space />
                           <q-item-section side>
                              <div class="text-grey-8 q-gutter-xs">
                                 <q-btn @click="removeLogo" size="12px" round icon="eva-trash-2" color="red" />
                              </div>
                           </q-item-section>
                        </q-item>
                     </q-list>

                     <div class="q-mt-md" v-if="$can('update-store')">
                        <q-btn :loading="isLoading" class="full-width" type="submit" label="Simpan Data"
                           color="primary">
                        </q-btn>
                     </div>
                  </q-card-section>
               </q-card>

            </q-form>
         </q-tab-panel>
         <q-tab-panel name="pwa" class="q-pa-none">
            <q-form @submit="submitPwa">
               <q-card class="section shadow">
                  <q-card-section class="q-gutter-y-md">
                     <q-input filled label="App Name" v-model="formPwa.app_name"></q-input>
                     <q-input filled label="App Download Url" v-model="formPwa.download_url"
                        hint="Kosongkan untuk default PWA"></q-input>
                     <q-input filled label="Download Desription" v-model="formPwa.download_desc"></q-input>

                     <div class="q-py-md" style="min-height:100px">
                        <input accept="image/*" filled type="file" class="hidden" ref="icon"
                           @change="handleChangeIcon" />
                        <q-btn v-if="$can('update-store')" label="Upload Icon" size="sm" color="primary" icon="eva-upload" class="mt-2 mr-2"
                           type="button" @click.prevent="handleUploadIcon"></q-btn>

                        <div v-if="iconPreview" class="q-mt-md">
                           <img :src="iconPreview" class="bg-grey-1 img-thumbnail"
                              style="width: 100px; height: 100px; object-fit: contain" />
                        </div>
                     </div>
                     <div class="q-mt-md" v-if="$can('update-store')">
                        <q-btn :loading="isLoading" class="full-width" type="submit" label="Simpan Data"
                           color="primary">
                        </q-btn>
                     </div>
                  </q-card-section>
               </q-card>
            </q-form>
         </q-tab-panel>
      </q-tab-panels>



   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   name: 'ShopIndex',
   data() {
      return {
         tab: 'shop',
         provinceOptions: [],
         subdistrictOptions: [],
         cityOptions: [],
         isLoading: false,
         form: {
            name: "",
            phone: "",
            email: "",
            address: "",
            description: "",
            slogan: "",
            logo: null,
            is_remove_logo: false,
         },
         formPwa: {
            icon: "",
            download_desc: "",
            app_name: "",
            download_url: "",
         },
         shop: null,
      };
   },
   computed: {
      config() {
         return this.$store.state.config;
      },
      errors() {
         return this.$store.state.errors;
      },
      imagePreview() {
         if (this.form.logo) {
            return URL.createObjectURL(this.form.logo)
         } else if (this.shop && this.shop.logo) {
            return this.shop.logo
         }
         return null
      },
      iconPreview() {
         if (this.formPwa.icon) {
            return URL.createObjectURL(this.formPwa.icon)
         } else if (this.shop && this.shop.icon) {
            return this.shop.icon
         }
         return null
      },
   },
   methods: {
      submitData() {
         this.isLoading = true;
         let self = this;
         let formData = new FormData();

         for (const i in this.form) {
            if (this.form[i] && this.form[i] != null && this.form[i] != "null") {
               formData.append(i, this.form[i]);
            }
         }

         BaseApi.post("shop", formData, {
            headers: { "Content-Type": "multipart/form-data" },
         })
            .then((response) => {
               self.isLoading = false;
               if (response.status == 200) {
                  self.$store.commit("SET_SHOP", response.data.data);
                  // localStorage.setItem('_washop', JSON.stringify(response.data.data))
                  this.$q.notify({
                     type: "positive",
                     message: "Berhasil menyimpan data",
                  });
               }
            })
            .catch((err) => {
               self.isLoading = false;
               this.$q.notify({
                  type: "negative",
                  message: "Gagal menyimpan data, Coba refresh halaman",
               });
            });
      },
      submitPwa() {
         this.isLoading = true;
         let self = this;
         let formData = new FormData();

         for (const i in this.formPwa) {
            if (this.formPwa[i] && this.formPwa[i] != null && this.formPwa[i] != "null") {
               formData.append(i, this.formPwa[i]);
            }
         }

         BaseApi.post("update-pwa", formData, {
            headers: { "Content-Type": "multipart/form-data" },
         })
            .then((response) => {
               self.isLoading = false;
               if (response.status == 200) {
                  self.$store.commit("SET_SHOP", response.data.data);
                  // localStorage.setItem('_washop', JSON.stringify(response.data.data))
                  this.$q.notify({
                     type: "positive",
                     message: "Berhasil menyimpan data",
                  });
               }
            })
            .catch((err) => {
               self.isLoading = false;
               this.$q.notify({
                  type: "negative",
                  message: "Gagal menyimpan data, Coba refresh halaman",
               });
            });
      },
      handleBtnUpload() {
         this.$refs.image.click();
      },
      handleUploadIcon() {
         this.$refs.icon.click();
      },
      handleChangeImage() {
         this.form.logo = this.$refs.image.files[0];
      },
      handleChangeIcon() {
         this.formPwa.icon = this.$refs.icon.files[0];
      },
      removeLogo() {
         if (this.toko.logo_path) {
            this.form.is_remove_logo = true;
         }
         this.form.logo = null;
      },
      setData() {
         this.form.name = this.shop.name ?? "";
         this.form.phone = this.shop.phone ?? "";
         this.form.email = this.shop.email ?? "";
         this.form.slogan = this.shop.slogan ?? "";
         this.form.address = this.shop.address ?? "";
         this.form.description = this.shop.description ?? "";

         this.formPwa.app_name = this.shop.app_name ?? "";
         this.formPwa.download_url = this.shop.download_url ?? "";
         this.formPwa.download_desc = this.shop.download_desc ?? "";
      },
      getShop() {
         this.$store.commit("SET_LOADING", true);
         BaseApi.get("shop")
            .then((response) => {
               if (response.status == 200) {
                  this.shop = response.data.data.shop;
                  this.setData();
                  this.$store.commit("SET_SHOP", response.data.data.shop);
               }
            })
            .finally(() => this.$store.commit("SET_LOADING", false));
      },
   },
   created() {
      this.getShop();
   },
    mounted() {
       this.$canAccess('view-store')
   }
};
</script>
