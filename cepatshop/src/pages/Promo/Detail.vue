<template>
   <q-page padding>
      <AppHeader title="Promo Detail" goBack></AppHeader>
      <div v-if="promo">
         <q-card class="section shadow">
            <q-card-section>
               <q-list separator>
                  <q-item>
                     <q-item-section>Label</q-item-section>
                     <q-item-section>{{ promo.label }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Start</q-item-section>
                     <q-item-section>{{
                        dateFormat(promo.start_date)
                        }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Selesai</q-item-section>
                     <q-item-section>{{ dateFormat(promo.end_date) }}</q-item-section>
                  </q-item>
                  <q-item>
                     <q-item-section>Status</q-item-section>
                     <q-item-section>
                        <q-item-label>
                           <q-badge class="q-px-md q-py-xs rounded-borders text-white"
                              :class="promo.is_active ? 'bg-green' : 'bg-grey-7'">{{ promo.is_active ? "Active" :
                              "Inactive" }}</q-badge>
                        </q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>
         <q-separator></q-separator>
      </div>
      <q-card bordered flat class="q-mt-lg">
         <q-card-section>
            <div v-if="promo">
               <div class="flex justify-between items-center q-mb-md">
                  <div class="text-md text-weight-bold">
                     Total Produk {{ products.length }}
                  </div>
                  <q-btn color="white" text-color="grey-8" size="13px" @click="handleAddProductPromo" outline
                     icon="add_circle" label="Produk">
                  </q-btn>
               </div>
               <div class="">
                  <q-list separator>
                     <q-item class="item-header" dense>
                        <q-item-section side>#</q-item-section>
                        <q-item-section>Nama Produk</q-item-section>
                        <q-item-section>Diskon</q-item-section>
                        <q-item-section side>Actions</q-item-section>
                     </q-item>
                     <q-item v-for="(product, index) in products" :key="index">
                        <q-item-section side>{{ index + 1 }}</q-item-section>
                        <q-item-section>{{ product.title }}</q-item-section>
                        <q-item-section>
                           {{
                              product.pivot.discount_type == "PERCENT"
                                 ? product.pivot.discount_amount + " % "
                                 : moneyIdr(product.pivot.discount_amount)
                           }}
                        </q-item-section>
                        <q-item-section side>
                           <div class="q-gutter-sm flex">
                              <q-btn icon="eva-trash-2" size="11px" round unelevated color="red" :disable="syncLoading"
                                 @click="handleRemoveProductPromo(product.id)">
                              </q-btn>
                           </div>
                        </q-item-section>
                     </q-item>
                  </q-list>
                  <div class="text-center q-py-md" v-if="!products.length">
                     Tidak ada data
                  </div>
               </div>
            </div>
         </q-card-section>
      </q-card>
      <q-dialog v-model="productPromoModal" persistent position="bottom">
         <div class="max-width">
            <q-form @submit.prevent="submitProductPromo">
               <q-card flat>
                  <q-card-section class="sticky-top bg-white q-pb-xs">
                     <div class="card-subtitle flex justify-between items-center">
                        <div>Tambah Produk</div>
                        <q-btn icon="close" v-close-popup round dense flat></q-btn>
                     </div>
                     <q-input outlined :disable="!rendered_products.length" dense placeholder="Filter by name"
                        v-model="search" type="search" clearable @clear="() => (search = '')"></q-input>
                     <div class="flex items-center q-mt-sm full-width">
                        <q-select required filled square label="Diskon Tipe" class="col q-mr-sm"
                           v-model="form.discount_type" :options="discountTypeOptions" emit-value map-options
                           :rules="[(val) => !!val || 'Field is required']"></q-select>
                        <q-input class="col" required filled square label="Diskon Nominal"
                           v-model="form.discount_amount" :rules="[(val) => !!val || 'Field is required']"></q-input>
                     </div>
                  </q-card-section>
                  <q-card-section class="q-gutter-y-sm q-pt-xs" style="height: 50vh; overflow-y: auto">
                     <div v-if="rendered_products.length">
                        <q-list separator>
                           <q-item v-for="product in rendered_products" :key="product.id">
                              <q-item-section side>
                                 <q-checkbox v-model="form.products" :val="product.id"></q-checkbox>
                              </q-item-section>
                              <q-item-section>{{ product.title }}</q-item-section>
                           </q-item>
                        </q-list>
                     </div>
                     <div v-if="!rendered_products.length" class="text-center q-pt-lg">
                        Tidak ada produk
                     </div>
                  </q-card-section>
                  <div class="bg-white max-width q-pa-md sticky-bottom">
                     <q-btn label="Submit" class="full-width" color="primary" type="submit"
                        :disable="!form.products.length"></q-btn>
                  </div>
               </q-card>
            </q-form>
         </div>
      </q-dialog>
   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
   name: "ProductPromo",
   data() {
      return {
         productPromoModal: false,
         productSelected: null,
         product_ids: [],
         promo: null,
         syncLoading: false,
         formFilter: {
            search: "",
            category_id: "",
         },
         search: "",
         productSearch: [],
         categories: [],
         products: [],
         all_products: [],
         form: {
            promo_id: null,
            discount_amount: 0,
            discount_type: "PERCENT",
            products: [],
         },
         discountTypeOptions: [
            { value: "PERCENT", label: "Persen" },
            { value: "AMOUNT", label: "Nominal" },
         ],
         removeId: null,
         notFound: false,
      };
   },
   computed: {
      rendered_products() {
         if (this.search) {
            let needle = this.search.toLowerCase();
            return this.all_products.filter(
               (v) => v.title.toLowerCase().indexOf(needle) > -1
            );
         }
         return this.all_products;
      },
   },
   methods: {
      getProductWithoutPromo() {
         BaseApi.get("product-promo/product-without-promo").then((res) => {
            this.all_products = res.data.data;
         });
      },
      getPromoDetail() {
         BaseApi.get("promo/detail/" + this.$route.params.id).then((response) => {
            if (response.status == 200 && response.data.success) {
               this.products = response.data.data.products;
               this.promo = response.data.data.promo;
            }
         });
      },
      handleAddProductPromo() {
         this.productPromoModal = true;
      },
      submitProductPromo() {
         this.form.promo_id = this.promo.id;
         if (this.form.discount_type == "PERCENT") {
            if (this.form.discount_amount == 0 || this.form.discount_amount >= 99) {
               this.$q.notify({
                  type: "negative",
                  message: "Nilai nominal diskon belum benar",
               });

               return;
            }
         }
         BaseApi.post("product-promo", this.form).then((res) => {
            this.products = res.data.data;
            this.productPromoModal = false;
            this.getProductWithoutPromo();
         });
      },
      handleRemoveProductPromo(id) {
         this.removeId = id;
         this.$q
            .dialog({
               title: "Konnfirmasi",
               message: "Yakin akan menghapus data?",
               cancel: true,
            })
            .onOk(() => {
               BaseApi.post("product-promo/remove", {
                  promo_id: this.promo.id,
                  product_id: this.removeId,
               }).then((res) => {
                  this.products = res.data.data;
                  this.getProductWithoutPromo();
               });
            });
      },
   },
   mounted() {
      this.getPromoDetail();
      this.getProductWithoutPromo();
   },
};
</script>
