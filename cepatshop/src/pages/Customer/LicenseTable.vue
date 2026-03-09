<template>
   <div>
      <div class="box-column flat" v-if="licenses">
         <div class="flex justify-between q-pa-sm items-center">
            <div class="text-weight-bold text-md">Lisensi Produk</div>
            <q-btn v-if="next && licenses.available" color="primary" label="Selengkapnya" flat no-caps padding="xs"
               :to="{ name: 'CustomerLicense' }"></q-btn>
         </div>
         <div class="table-responsive">
            <table class="table aligned bordered">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Image</th>
                     <th>Produk</th>
                     <th>Masa Aktif</th>
                     <th>Tanggal</th>
                     <th>Akses Produk</th>
                  </tr>
               </thead>
               <tbody>
                  <tr v-for="(license, index) in licenses.data" :key="index">
                     <td>{{ index + 1 }}</td>
                     <td>
                        <q-img width="60px" v-if="license.product" :src="license.product.assets[0].src" class="img-thumbnail" />
                     </td>
   
                     
                     <td>
                        <div v-if="license.product" style="min-width: 180px;">
                           {{ license.product.title }}
                        </div>
                        <q-badge :color="license.product.is_digital_video ? 'purple' : 'teal'">{{ license.product.product_type }}</q-badge>
                     </td>
                     <td>
                        <div>
                           <!-- <q-badge :color="license.is_active ? 'green' : 'grey-7'">{{ license.is_active ? 'Active' :
                              'Inactive'
                           }}</q-badge> -->
                           {{ license.active_label }}
                        </div>
                     </td>
                     <td>{{ dateFormat(license.created_at) }}</td>
                     <td>
                        <q-btn :loading="is_loading" v-if="license.is_active" dense icon-right="login" flat
                           label="Akses Produk" no-caps :to="{
                              name: 'CustomerLicenseShow',
                              params: { license_id: license.id },
                           }"></q-btn>
                        <q-btn :loading="is_loading" v-else dense icon-right="login" label="Perpanjang" no-caps
                           color="orange-7" @click="handleUpdateLicense(license)"></q-btn>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div v-if="!licenses.total" class="text-center q-pa-sm">
            Tidak ada Lisensi
         </div>
  </div>
   </div>
</template>

<script>
import { Api } from "boot/axios";
import { dateFormat } from "src/utils";
export default {
   props: {
      licenses: Object,
      next: {
         type: Boolean,
         default: false,
      },
   },
   data() {
      return {
         is_loading: false,
      };
   },
   computed: {
      session_id() {
         return this.$store.state.session_id;
      },
   },
   methods: {
      handleUpdateLicense(license) {
         this.is_loading = true;
         this.license = license;
         Api()
            .get("productById/" + license.product_id)
            .then((res) => {
               if (res.status == 200) {
                  let product = res.data.data;
                  this.addToCart(license, product);
               }
            });
      },
      addToCart(license, product) {
         if (!this.session_id) this.makeSessionId();
         this.$store.dispatch("cart/clearCart", this.session_id);

         let note = "";

         if (product.subscription_label) {
            note = `Perpanjang ${product.subscription_label}`;
         }

         let item = {
            session_id: this.session_id,
            product_id: product.id,
            product_stock: 999,
            sku: product.sku,
            name: product.title,
            price: product.price,
            quantity: 1,
            note: note,
            product_url: this.getRoutePath(product.slug),
            image_url: product.assets[0].src,
            weight: 1,
            product_type: product.product_type,
            is_single_checkout: true,
            affiliate_code: "",
            license_id: license.id,
         };

         this.$store.dispatch("cart/addToCart", item);

         setTimeout(() => {
            this.$router.push({ name: "SingleCheckout" });
         }, 300);
         setTimeout(() => {
            this.is_loading = false;
         }, 3000);
      },
      getRoutePath(slug) {
         let props = this.$router.resolve({
            name: "ProductShow",
            params: { slug: slug },
         });

         return location.origin + props.href;
      },
   },
};
</script>