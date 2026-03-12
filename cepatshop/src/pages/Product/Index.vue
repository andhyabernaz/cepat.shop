<template>
   <q-page padding>
      <AppHeader title="Produk List">
         <q-btn v-if="$can('create-product')" color="white" text-color="dark" icon="add" @click="productTypeModal = true" label="Produk" />
      </AppHeader>
      <div class="bg-white row justify-between items-center q-px-sm">
         <div class="col">
            <q-tabs v-model="queryParams.product_type" align="left">
               <q-tab v-for="item in product_types" :key="item.label" :name="item.value">{{ item.label }}</q-tab>
            </q-tabs>
         </div>
      </div>
      <q-card class="section shadow q-mb-md q-mt-md">
         <q-card-section>
            <div class="col q-mb-sm">
               <q-input :loading="loading" ref="input" filled dense color="grey-2" v-model="queryParams.search"
                  @update:model-value="searchProduct" debounce="700" placeholder="Cari produk" clearable @clear="reset">
               </q-input>
            </div>
            <div class="col q-mb-md">
               <q-select filled dense emit-value map-options label="Kategori" :options="categoryOptions"
                  v-model="queryParams.category"></q-select>
            </div>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['#', 'Image', 'Produk', 'Total Stok', 'Affiliasi', 'Action']" :key="h"
                           class="text-nowrap">
                           {{ h }}
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(product, idx) in products.data" :key="product.id" class="q-px-xs">
                        <td>
                           {{ products.from + idx }}
                        </td>
                        <td class="q-pr-md" top>
                           <q-img :src="product.assets[0].src" class="bg-white img-product-admin img-thumbnail"
                              ratio="1" width="55px" />
                        </td>

                        <td>
                           <div>
                              <q-item-label lines="2" class="text-15 text-weight-medium text-grey-9">
                                 {{ product.title }}
                              </q-item-label>
                              <template v-if="product.total_count > 0">
                                 <q-item-label caption class="text-nowrap">
                                    {{
                                       renderVarianPrice(product)
                                    }}
                                 </q-item-label>
                              </template>
                              <template v-else>
                                 <q-item-label caption class="text-nowrap">{{
                                    moneyIdr(product.price)
                                 }}</q-item-label>
                              </template>

                              <q-badge :color="product.is_default_type ? 'teal' : 'purple'">{{ product.product_type }}</q-badge>
                           </div>
                        </td>
                        <td>
                           <div v-if="product.total_count > 0">
                              <q-item-label> {{ numberFormat(product.total_stock) }}</q-item-label>
                              <q-item-label caption>{{ product.total_count }}
                                 varian</q-item-label>
                           </div>
                           <div v-else>
                              {{ product.stock < 0 ? 'Unlimited' : numberFormat(product.stock) }} </div>
                        </td>

                        <td>{{ product.affiliate_detail }}</td>

                        <td>
                           <div class="flex no-wrap q-gutter-xs justify-end">
                              <q-btn size="11px" v-if="product.total_count > 0" @click="getDetailVarian(product)" round
                                 icon="eva-pantone" color="accent">
                                 <q-tooltip content-class="bg-dark">Detil Varian</q-tooltip>
                              </q-btn>
                              <q-btn v-if="$can('delete-product')" size="11px" @click="deleteProduct(product.id)" round icon="delete" color="red">
                                 <q-tooltip content-class="bg-dark">Hapus</q-tooltip>
                              </q-btn>
                              <q-btn v-if="$can('update-product')" size="11px" @click="handleEdit(product)" round color="blue" icon="edit">
                                 <q-tooltip content-class="bg-dark">Edit</q-tooltip>
                              </q-btn>
                              <q-btn v-if="$can('create-product')" size="11px" @click="handleDuplicate(product)" round color="purple"
                                 icon="folder_copy">
                                 <q-tooltip content-class="bg-dark">Duplicate</q-tooltip>
                              </q-btn>
                              <q-btn size="11px" :to="{
                                 name: 'ProductShow',
                                 params: { slug: product.slug },
                              }" round color="teal" icon="open_in_new">
                                 <q-tooltip content-class="bg-dark">Lihat</q-tooltip>
                              </q-btn>
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div></div>
            <div v-if="!products.total" class="text-center q-py-md">
               Tdak ada data
            </div>
         </q-card-section>
      </q-card>
      <div class="q-my-md">
         <SimplePagination autoHide v-bind="products" @loadUrl="paginate"></SimplePagination>
      </div>

      <q-dialog v-model="varianViewModal" persistent position="bottom">
         <q-card v-if="productSelected" class="max-width-mobile q-pb-lg" style="min-height: 400px">
            <q-card-section>
               <div class="card-title flex justify-between items-center q-mb-md">
                  <div>Detail Varian</div>
                  <q-btn icon="eva-close" flat padding="xs" round v-close-popup></q-btn>
               </div>
               <div class="table-responsive">
                  <table class="table aligned bordered">
                     <thead>
                        <tr>
                           <th v-for="h in ['Varian', 'Stok', 'Harga']" :key="h">{{ h }}</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr v-for="varian in varian_items" :key="varian.id">
                           <td>
                              <q-item-label class="q-gutter-x-xs">
                                 {{ varian.product_name }}
                              </q-item-label>
                           </td>
                           <td>{{ varian.stock }}</td>
                           <td>{{ moneyIdr(parseInt(varian.price)) }}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <q-inner-loading :showing="is_loading_varian"></q-inner-loading>

            </q-card-section>
         </q-card>
      </q-dialog>

      <q-dialog v-model="productTypeModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-title q-mb-md">Pilih Tipe Produk</div>
               <q-list separator>
                  <q-item :to="{ name: 'ProductDownloadForm', query: { type: 'Digital' } }">
                     <q-item-section side>
                        <q-icon name="chevron_right"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Digital Downloads</q-item-label>
                        <q-item-label caption>Produk digital download seperti ebook, file, template dll</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item :to="{ name: 'ProductVideoForm' }">
                     <q-item-section side>
                        <q-icon name="chevron_right"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Digital Video</q-item-label>
                        <q-item-label caption>Produk digital video seperti kursus dll</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item :to="{ name: 'ProductCreate', query: { type: 'Digital' } }">
                     <q-item-section side>
                        <q-icon name="chevron_right"></q-icon>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label>Produk Digital Lainnya</q-item-label>
                        <q-item-label caption>Produk digital yang di proses manual seperti jual akun dll</q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { mapState, mapActions } from "vuex";
import { BaseApi } from "boot/axios";
import SimplePagination from "components/SimplePagination.vue";
export default {
   name: "AdminProductList",
   components: { SimplePagination },
   data() {
      return {
         module: 'Product',
         productTypeModal: false,
         productSelected: null,
         varianViewModal: false,
         pageNumber: 1,
         showListId: null,
         is_loading_varian: false,
         varian_items: [],
         queryParams: {
            search: "",
            per_page: 10,
            product_type: this.$route.query.product_type || 'ALL',
            category: this.$route.query.category || 'ALL'
         },
         product_types: [
            { label: 'Semua', value: 'ALL' },
            { label: 'Produk Digital', value: 'Digital' },
         ],
      };
   },
   watch: {
      "queryParams.product_type"(val) {
         this.$router.replace({ name: 'AdminProductIndex', query: this.queryParams })
         this.getData()
      },
      "queryParams.category"(val) {
         this.$router.replace({ name: 'AdminProductIndex', query: this.queryParams })
         this.getData()
      },
   },
   computed: {
      ...mapState({
         products: (state) => state.product.admin_products,
         loading: (state) => state.loading,
      }),
      isDesktop() {
         return window.innerWidth > 600;
      },
      categories() {
         return this.$store.state.category.categories
      },
      categoryOptions() {
         let cats = [
            {
               label: 'Semua',
               value: 'ALL'
            },
            {
               label: 'Uncategories',
               value: 'Uncategory'
            },
         ]

         if (this.categories.data.length) {
            this.categories.data.forEach(el => {

               if (el.category_id == null) {
                  cats.push({ label: el.title, value: `${el.id}` })
               }
            })
         }

         return cats
      }

   },
   methods: {
      ...mapActions("product", ["getAdminProducts", "productDelete"]),
      selectVarian(product) {
         this.varianViewModal = true;
         this.productSelected = product;
      },
      reset() {
         this.queryParams.search = "";
         this.getData();
      },
      renderVarianPrice(product) {
         if (product.minPrice && product.maxPrice) {

            let minPrice = parseInt(product.minPrice)
            let maxPrice = parseInt(product.maxPrice)
            if (minPrice < maxPrice) {
               return `${this.moneyIdr(minPrice)} - ${this.moneyIdr(maxPrice)}`;
            }

            return `@ ${this.moneyIdr(minPrice)}`;
         }

         return "";
      },
      searchProduct() {
         if (
            this.queryParams.search.length > 0 &&
            this.queryParams.search.length < 4
         ) {
            return;
         }
         this.getData();
      },
      showList(id) {
         if (this.showListId == id) {
            this.showListId = null;
         } else {
            this.showListId = id;
         }
      },
      handleDuplicate(item) {
         if (['Digital Download'].includes(item.product_type)) {
            this.$router.push({ name: 'ProductDownloadClone', params: { id: item.id } })
         } else if (['Digital Video'].includes(item.product_type)) {
            this.$router.push({ name: 'ProductVideoClone', params: { id: item.id } })
         } else {
            this.$router.push({ name: 'ProductClone', params: { id: item.id } })
         }
      },
      handleEdit(item) {
         if (['Digital Download'].includes(item.product_type)) {
            this.$router.push({ name: 'ProductDownloadEdit', params: { id: item.id } })
         } else if (['Digital Video'].includes(item.product_type)) {
            this.$router.push({ name: 'ProductVideoEdit', params: { id: item.id } })
         } else {
            this.$router.push({ name: 'ProductEdit', params: { id: item.id } })
         }
      },
      deleteProduct(id) {
         this.$q
            .dialog({
               title: "Konfirmasi Penghapusan Item",
               message: "Yakin akan menghapus data?",
               ok: { label: "Hapus", flat: true, "no-caps": true },
               cancel: { label: "Batal", flat: true, "no-caps": true },
            })
            .onOk(() => {
               this.productDelete(id).then(() => {
                  this.getData()
               })
            });
      },
      getMargin(product) {
         if (!product.buy_price || !product.price) {
            return 0;
         } else {
            return product.price - product.buy_price;
         }
      },
      getData(url = null) {
         this.$store.commit('SET_LOADING', true)
         if (!url) {
            url = `products?${new URLSearchParams(this.queryParams).toString()}`;
         }
         this.getAdminProducts(url);
      },
      paginate(url) {
         this.getData(url);
      },
      getDetailVarian(product) {
         this.varianViewModal = true;
         if (this.productSelected && this.productSelected.id == product.id) return;
         this.varian_items = [];
         this.is_loading_varian = true;
         this.productSelected = product;
         BaseApi.get(`products/${product.id}/varians`)
            .then((res) => {
               if (res.status == 200) {
                  this.varian_items = res.data.data;
               }
            })
            .finally(() => (this.is_loading_varian = false));
      },
   },
   created() {
      if (!this.products.total) {
         this.getData();
      }

      if (!this.categories.data.length) {
         this.$store.dispatch('category/getCategories')
      }
   },
   mounted() {
       this.$canAccess('view-product')
   }
};
</script>
