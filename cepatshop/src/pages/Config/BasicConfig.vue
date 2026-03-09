<template>
   <div>
      <q-card class="section shadow">
         <q-card-section>
            <div class="card-subtitle">
               Tema dan basis warna
            </div>
            <q-list>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Mobile Theme</q-item-label>
                     <q-item-label caption>Konfigurasi default tema untuk tampilan mobile</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <q-select style="min-width: 110px" outlined dense v-model="form.theme" :options="themes" emit-value
                        map-options></q-select>
                  </q-item-section>
               </q-item>

               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Brand Color</q-item-label>
                     <q-item-label caption>Konfigurasi warna Address bar, pwa theme dan
                        heading</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <div class="cursor-pointer img-thumbnail"
                        :style="`width:110px;height:30px;background-color:${form.theme_color}`">
                        <q-popup-proxy transition-show="scale" transition-hide="scale">
                           <q-color no-header v-model="form.theme_color" />
                        </q-popup-proxy>
                     </div>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Primary Color</q-item-label>
                     <q-item-label caption>Terdapat pada title, button</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <div class="cursor-pointer img-thumbnail"
                        :style="`width:110px;height:30px;background-color:${form.primary_color}`">
                        <q-popup-proxy transition-show="scale" transition-hide="scale">
                           <q-color v-model="form.primary_color" />
                        </q-popup-proxy>
                     </div>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Secondary Color</q-item-label>
                     <q-item-label caption>Terdapat pada harga produk, badge, diskon</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <div class="cursor-pointer img-thumbnail"
                        :style="`width:110px;height:30px;background-color:${form.secondary_color}`">
                        <q-popup-proxy transition-show="scale" transition-hide="scale">
                           <q-color v-model="form.secondary_color" />
                        </q-popup-proxy>
                     </div>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Accent Color</q-item-label>
                     <q-item-label caption>Terdapat pada rating, varian, produk review</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <div class="cursor-pointer img-thumbnail"
                        :style="`width:110px;height:30px;background-color:${form.accent_color}`">
                        <q-popup-proxy transition-show="scale" transition-hide="scale">
                           <q-color v-model="form.accent_color" />
                        </q-popup-proxy>
                     </div>
                  </q-item-section>
               </q-item>

            </q-list>

         </q-card-section>
      </q-card>

      <q-card class="section q-mt-md shadow">
         <q-card-section>
            <div class="card-subtitle">Konfigurasi Situs</div>
            <q-list separator>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Mode Desktop (beta)</q-item-label>
                     <q-item-label caption>Tampilan mode desktop untuk layar lebar</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.mode_desktop" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Tampilkan Nama Situs</q-item-label>
                     <q-item-label caption>Menampilkan nama situs di halaman depan</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.display_sitename" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Moderasi Ulasan Otomatis</q-item-label>
                     <q-item-label caption>Ulasan akan otomatis di moderasi</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.review_auto_approved" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Kode Unik Dipesanan</q-item-label>
                     <q-item-label caption>Hanya berlaku untuk manual transfer</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.is_unique_code" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Ulasan Produk Guest</q-item-label>
                     <q-item-label caption>Semua orang dapat memberi ulasan produk</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.public_review" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Tampilkan Produk Terjual</q-item-label>
                     <q-item-label caption>Menampilkan jumlah produk terjual di produk</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.display_product_sold" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Featured Slider</q-item-label>
                     <q-item-label caption>Fitur spasi slider pada tampilan desktop</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.is_featured_slider" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Slider Autoplay</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.slider_autoplay" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Slider Interval</q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-input filled type="number" max="99" min="1" v-model="form.slider_interval" dense
                        suffix="Detik"></q-input>
                  </q-item-section>
               </q-item>
               <q-item class="q-px-xs">
                  <q-item-section>
                     <q-item-label class="text-weight-medium">Social Proof</q-item-label>
                     <q-item-label caption>
                        Menampilkan popup semua produk yang pernah dibeli user.
                     </q-item-label>
                  </q-item-section>
                  <q-item-section side top>
                     <q-toggle v-model="form.is_notifypro" left-label color="teal"></q-toggle>
                  </q-item-section>
               </q-item>
               <q-item v-if="form.is_notifypro" class="q-px-none">
                  <q-item-section>
                     <q-input filled label="Jeda Waktu Tayang" type="number" min="1" :hint="'Popup akan tayang setiap ' + form.notifypro_interval + ' Detik'
                        " v-model="form.notifypro_interval"></q-input>
                  </q-item-section>
                  <q-item-section>
                     <q-input filled label="Durasi Penayangan" type="number" min="1" :hint="'Popup akan tayang selama ' + form.notifypro_timeout + ' Detik'
                        " v-model="form.notifypro_timeout"></q-input>
                  </q-item-section>
               </q-item>
            </q-list>
         </q-card-section>
      </q-card>
      <q-card class="section shadow q-mt-md">
         <q-card-section>
            <div class="card-subtitle">Tampilan Produk Beranda</div>
            <q-list>
               <q-item class="q-px-none q-mt-xs">
                  <q-item-section>
                     <q-item-label>
                        <q-input type="number" min="4" filled v-model="form.home_product_limit"
                           label="Showing"></q-input>
                     </q-item-label>
                  </q-item-section>
                  <q-item-section>
                     <q-item-label>
                        <q-select filled v-model="form.home_product_sort" label="Sort"
                           :options="[...sorts, { value: 'RANDOM', label: 'Random' }]" emit-value
                           map-options></q-select>
                     </q-item-label>
                  </q-item-section>
                  <q-item-section>
                     <q-item-label>
                        <q-select filled v-model="form.home_view_mode" label="View Mode" :options="[
                           { value: 'grid', label: 'Grid' },
                           { value: 'list', label: 'List' },
                        ]" emit-value map-options></q-select>
                     </q-item-label>
                  </q-item-section>
               </q-item>
            </q-list>
         </q-card-section>
      </q-card>

      <q-card class="section shadow q-mt-md">
         <q-card-section>
            <div>
               <div class="card-subtitle">Tampilan Produk Katalog</div>
               <q-list>

                  <q-item class="q-px-none q-mt-xs">
                     <q-item-section>
                        <q-item-label>
                           <q-input filled v-model="form.catalog_product_limit" label="Showing" type="number"
                              min="4"></q-input>
                        </q-item-label>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>
                           <q-select filled v-model="form.catalog_product_sort" label="Sort" :options="sorts" emit-value
                              map-options></q-select>
                        </q-item-label>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>
                           <q-select filled v-model="form.product_view_mode" label="View Mode" :options="[
                              { value: 'grid', label: 'Grid' },
                              { value: 'list', label: 'List' },
                           ]" emit-value map-options></q-select>
                        </q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
            </div>
         </q-card-section>
      </q-card>

      <q-card class="section shadow q-mt-md">
         <q-card-section>
            <div>
               <div class="card-subtitle">Image Aspect Ratio</div>
               <q-list dense separator>
                  <q-item>
                     <q-item-section>
                        Card Image Ratio
                     </q-item-section>
                     <q-item-section side>
                        <q-select borderless emit-value map-options :options="image_ratios"
                           v-model="form.card_img_ratio"></q-select>

                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>
                        Card Image Fit
                     </q-item-section>
                     <q-item-section side>
                        <q-select borderless emit-value map-options :options="image_fits"
                           v-model="form.card_img_fit"></q-select>
                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>
                        Product Image Ratio
                     </q-item-section>
                     <q-item-section side>
                        <q-select borderless emit-value map-options :options="image_ratios"
                           v-model="form.product_img_ratio"></q-select>
                     </q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>
                        Product Image Fit
                     </q-item-section>
                     <q-item-section side>

                        <q-select borderless emit-value map-options :options="image_fits"
                           v-model="form.product_img_fit"></q-select>
                     </q-item-section>
                  </q-item>
               </q-list>
            </div>
         </q-card-section>
      </q-card>

      <div class="q-pt-lg" v-if="$can('basic-config')">
         <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary" @click="updateData"></q-btn>
      </div>

   </div>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   data() {
      return {
         image_fits: [
            { label: 'Cover', value: 'cover' },
            { label: 'Contain', value: 'contain' },
         ],
         image_ratios: [
            { label: '1:1', value: '1' },
            { label: '3:4', value: '3/4' },
            { label: '4:3', value: '4/3' },
            { label: '16:9', value: '16/9' },
         ],
         sorts: [
            { value: "DESC", label: "Terbaru" },
            { value: "ASC", label: "Terlama" },
         ],
         form: {
            theme: "",
            theme_color: "",
            primary_color: "",
            secondary_color: "",
            accent_color: "",
            home_view_mode: "",
            product_view_mode: "",
            is_notifypro: false,
            notifypro_interval: 20,
            notifypro_timeout: 4,
            review_auto_approved: false,
            catalog_product_limit: 10,
            catalog_product_sort: "DESC",
            home_product_limit: 10,
            home_product_sort: "DESC",
            review_auto_approved: false,
            public_review: false,
            display_sitename: false,
            is_unique_code: false,
            display_product_sold: false,
            mode_desktop: false,
            is_featured_slider: false,
            card_img_ratio: '1',
            card_img_fit: 'cover',
            product_img_fit: '1',
            product_img_ratio: 'cover',
            slider_interval: 4,
            slider_autoplay: true
         },
      };
   },
   computed: {
      config: function () {
         return this.$store.state.config;
      },
      themes() {
         return this.$store.state.themes;
      },
   },
   watch: {
      "form.theme_color": function (val) {
         this.$store.commit("SET_THEME_COLOR", val);
      },
      "form.primary_color": function (val) {
         this.$store.commit("SET_PRIMARY_COLOR", val);
      },
      "form.secondary_color": function (val) {
         this.$store.commit("SET_SECONDARY_COLOR", val);
      },
      "form.accent_color": function (val) {
         this.$store.commit("SET_ACCENT_COLOR", val);
      },
   },
   mounted() {
      this.setConfig()
   },
   methods: {
      changeHomeViewMode(str) {
         this.form.home_view_mode = str;
      },
      changeProductViewMode(str) {
         this.form.product_view_mode = str;
      },
      handleChangeColor() {
         this.$refs.color.click();
      },
      updateData() {
         localStorage.removeItem("__cepatshop_theme");

         BaseApi.post("config", this.form)
            .then((response) => {
               if (response.status == 200) {
                  this.$store.dispatch("getAdminConfig");
               }
               this.$q.notify({
                  type: "positive",
                  message: "Berhasil memperbarui data",
               });
               // location.reload()
            })
            .catch(() => {
               this.$q.notify({
                  type: "negative",
                  message: "Gagal memperbarui data",
               });
            });
      },
      setConfig() {
         this.form.product_view_mode = this.config.product_view_mode;
         this.form.home_view_mode = this.config.home_view_mode;
         this.form.is_notifypro = this.config.is_notifypro;
         this.form.notifypro_interval = this.config.notifypro_interval;
         this.form.notifypro_timeout = this.config.notifypro_timeout;
         this.form.theme = this.config.theme;
         this.form.theme_color = this.config.theme_color;
         this.form.primary_color = this.config.primary_color;
         this.form.secondary_color = this.config.secondary_color;
         this.form.accent_color = this.config.accent_color;
         this.form.review_auto_approved = this.config.review_auto_approved;
         this.form.public_review = this.config.public_review;
         this.form.catalog_product_limit = this.config.catalog_product_limit;
         this.form.catalog_product_sort = this.config.catalog_product_sort;
         this.form.home_product_limit = this.config.home_product_limit;
         this.form.home_product_sort = this.config.home_product_sort;
         this.form.display_sitename = this.config.display_sitename;
         this.form.is_unique_code = this.config.is_unique_code;
         this.form.display_product_sold = this.config.display_product_sold;
         this.form.mode_desktop = this.config.mode_desktop;
         this.form.is_featured_slider = this.config.is_featured_slider;
         this.form.card_img_ratio = this.config.card_img_ratio
         this.form.card_img_fit = this.config.card_img_fit
         this.form.product_img_fit = this.config.product_img_fit
         this.form.product_img_ratio = this.config.product_img_ratio
         this.form.slider_interval = this.config.slider_interval ?? 5
      }
   },
};
</script>
