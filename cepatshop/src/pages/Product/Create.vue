<template>
   <q-page padding>

      <AppHeader title="Tambah Produk" goBack></AppHeader>
      <form @submit.prevent="submit">
         <q-card class="section shadow">
            <q-card-section>
               <div class="q-gutter-y-md">
                  <q-input type="text" v-model="form.title" label="Title Produk" required />

                  <!-- <CategoryBlock v-model="form.category_id" /> -->

                  <div class="q-mt-md q-mb-sm">
                     <label for="description" class="text-grey-7 q-pb-sm block">Deskripsi</label>
                     <ContentEditor v-model="form.description" />
                     <div class="text-xs text-red" v-if="errors.description"> {{ errors.description[0] }}</div>
                  </div>
               </div>

            </q-card-section>
         </q-card>
         <q-card square flat class="section shadow q-mt-sm">
            <q-card-section>
               <MediaBlock v-model="form.images"></MediaBlock>
            </q-card-section>
         </q-card>
         <q-card square flat class="section shadow q-mt-sm">
            <q-card-section>
               <div class="text-weight-medium text-md q-mb-sm text-grey-9">Gambar Produk</div>
               <div class="q-py-md">
                  <div class="">
                     <div class="row q-gutter-md">

                        <div class="box-image bordered cursor-pointer flex justify-center items-center"
                           @click="selectNewImage">
                           <q-icon name="add_a_photo" size="lg" color="grey"></q-icon>
                        </div>
                        <div class="box-image relative cursor-pointer"
                           :class="{ 'feature-image-selected': form.featured_index == index }"
                           v-for="(image, index) in imagePreview" :key="index">
                           <img :src="image" class="bg-white" @click="changeFeaturedImage(index)" />
                           <div class="absolute-top-right">
                              <q-btn dense @click.prevent="removeImage(index)" size="10px" unelevated icon="close"
                                 color="red" padding="1px" />
                           </div>
                        </div>
                     </div>
                     <div class="text-xs text-grey q-mt-md" v-if="imagePreview.length">Untuk memilih featured
                        image klik
                        pada gambar</div>
                  </div>
               </div>
               <input type="file" class="hidden" ref="image" @change="updateImagePreview" multiple>
            </q-card-section>
         </q-card>

         <q-card class="shadow q-mt-sm" square flat style="min-height:300px;">
            <q-card-section>
               <q-radio class="text-weight-medium text-md" v-model="form.simple_product" :val="true"
                  label="Simple Produk"></q-radio>
               <q-radio class="text-weight-medium text-md" v-model="form.simple_product" :val="false"
                  label="Produk Dengan Varian"></q-radio>
            </q-card-section>
            <q-separator></q-separator>
            <q-card-section v-if="form.simple_product" class="q-pb-xl q-px-lg" style="min-height:200px;">
               <h5 class="q-py-md">Simple Produk</h5>
               <div class="row items-center q-gutter-x-sm">
                  <div class="col">
                     <money-formatter required outlined v-model="form.price" prefix="Rp" />
                  </div>
                  <div class="col">
                     <money-formatter required outlined v-model="form.stock" label="Stok" />
                  </div>
                  <div class="col">
                     <money-formatter required outlined v-model="form.weight" label="Berat" suffix="Gram" />
                  </div>
               </div>
               <!-- <div class="text-xs text-grey-7 q-pa-xs">Input harga terendah jika menggunakan varian</div> -->
            </q-card-section>

            <q-card-section v-if="!form.simple_product" class="">
               <div id="variants" style="min-height:200px;">
                  <div class="row items-center q-gutter-sm q-py-md q-mb-md">
                     <h5 class="q-mb-none text-grey-9">Produk Varian</h5>
                     <q-btn v-if="canAddVarian" outline icon="add" no-caps label="Tambah Varian"
                        @click="varianModal = true" color="primary" unelevated size="12px"></q-btn>
                  </div>
                  <div v-if="form.varians.length">
                     <div v-if="form.varians[0].has_subvarian">

                        <q-card v-for="(varian, varIndex) in form.varians" :key="varIndex" class="q-mb-lg bg-grey-1"
                           flat bordered>
                           <q-card-section class="q-px-sm">
                              <div class="row items-center justify-between q-mb-sm q-px-sm">
                                 <div class="text-weight-bold text-md">{{ form.varians[varIndex].label }} {{
         form.varians[varIndex].value }}</div>
                                 <q-btn-dropdown flat dropdown-icon="more_vert" unelevated auto-close fab-mini
                                    no-icon-animation padding="xs">
                                    <q-list separator bordered>
                                       <q-item clickable v-close-popup @click="handleEditLabel(varIndex)">
                                          <q-item-section side>
                                             <q-icon name="eva-edit" color="blue"></q-icon>
                                          </q-item-section>
                                          <q-item-section>
                                             <q-item-label>Edit Label</q-item-label>
                                          </q-item-section>
                                       </q-item>
                                       <q-item clickable v-close-popup @click="pushSubVarian(varIndex)">
                                          <q-item-section side>
                                             <q-icon name="eva-plus-circle" color="teal"></q-icon>
                                          </q-item-section>
                                          <q-item-section>
                                             <q-item-label>Tambah Item</q-item-label>
                                          </q-item-section>
                                       </q-item>
                                       <q-item clickable v-close-popup
                                          @click="duplicateVarian(varian, varIndex, 'main-var-')">
                                          <q-item-section side>
                                             <q-icon name="eva-copy" color="purple"></q-icon>
                                          </q-item-section>
                                          <q-item-section>
                                             <q-item-label>Duplikat Varian</q-item-label>
                                          </q-item-section>
                                       </q-item>
                                       <q-item clickable v-close-popup @click="deleteVarian(varIndex)">
                                          <q-item-section side>
                                             <q-icon name="eva-close" color="red"></q-icon>
                                          </q-item-section>
                                          <q-item-section>
                                             <q-item-label>Hapus Varian</q-item-label>
                                          </q-item-section>
                                       </q-item>
                                    </q-list>

                                 </q-btn-dropdown>
                              </div>
                              <q-list class="" v-if="form.varians[varIndex].subvarian.length">
                                 <q-item class="bg-white q-mb-sm box-shadow"
                                    v-for="(subvarian, subIndex) in form.varians[varIndex].subvarian" :key="subIndex">

                                    <q-item-section>
                                       <q-item-label class="q-mb-xs">
                                          <q-input class="multi-varian" :class="'main-var-' + varIndex" stack-label
                                             required v-model="form.varians[varIndex].subvarian[subIndex].value"
                                             :label="form.varians[varIndex].subvarian[subIndex].label"></q-input>
                                       </q-item-label>
                                       <q-item-label>
                                          <money-formatter stack-label required
                                             v-model="form.varians[varIndex].subvarian[subIndex].price" prefix="Rp"
                                             label="Harga Jual" />
                                       </q-item-label>
                                    </q-item-section>

                                    <q-item-section>
                                       <q-item-label class="q-mb-xs">
                                          <money-formatter stack-label required
                                             v-model="form.varians[varIndex].subvarian[subIndex].weight" label="Berat"
                                             suffix="Gram" />
                                       </q-item-label>
                                       <q-item-label>
                                          <money-formatter stack-label required
                                             v-model="form.varians[varIndex].subvarian[subIndex].stock" label="Stok" />

                                       </q-item-label>
                                    </q-item-section>
                                    <q-item-section side>
                                       <q-btn round unelevated padding="2px" icon="eva-close" size="13px" color="red"
                                          @click="deleteSubvarian(varIndex, subIndex)"></q-btn>
                                    </q-item-section>
                                 </q-item>
                              </q-list>
                           </q-card-section>
                        </q-card>

                     </div>
                     <q-card v-else class="q-mb-lg bg-grey-1" flat>
                        <q-card-section class="q-px-sm">
                           <div class="row items-center q-gutter-sm q-mb-md q-px-sm">
                              <div class="text-weight-bold text-lg">{{ form.varians[0].label }} </div>
                              <div>
                                 <q-btn unelevated size="10px" color="teal" @click="pushVarian">Tambah
                                    Item</q-btn>
                              </div>
                           </div>
                           <q-list>
                              <q-item class="bg-white q-mb-sm box-shadow" v-for="(varian, vIndex) in form.varians"
                                 :key="vIndex">

                                 <q-item-section>
                                    <q-item-label class="q-mb-xs">
                                       <q-input class="single-varian" :class="'input-var-' + vIndex" stack-label square
                                          required v-model="form.varians[vIndex].value"
                                          :label="form.varians[vIndex].label"></q-input>
                                    </q-item-label>
                                    <q-item-label>
                                       <money-formatter stack-label required v-model="form.varians[vIndex].price"
                                          prefix="Rp" label="Harga Jual" />
                                    </q-item-label>
                                 </q-item-section>
                                 <q-item-section>
                                    <q-item-label class="q-mb-xs">
                                       <money-formatter stack-label required v-model="form.varians[vIndex].weight"
                                          label="Berat" suffix="Gram" />
                                    </q-item-label>
                                    <q-item-label>
                                       <money-formatter stack-label required v-model="form.varians[vIndex].stock"
                                          label="Stok" />
                                    </q-item-label>
                                 </q-item-section>

                                 <q-item-section side>
                                    <q-btn unelevated padding="2px" icon="eva-close" size="13px" color="red"
                                       @click="deleteVarian(vIndex)"></q-btn>
                                    <q-btn unelevated padding="2px" icon="eva-copy" size="13px" color="blue"
                                       class="q-mt-sm" @click="duplicateVarian(varian, vIndex, `input-var-`)"></q-btn>
                                 </q-item-section>

                              </q-item>
                           </q-list>
                        </q-card-section>
                     </q-card>
                  </div>
               </div>
            </q-card-section>
         </q-card>
         <div class="card-action">
            <q-btn color="primary" type="submit" :loading="loading" class="full-width" label="Simpan Data">
               <q-tooltip class="bg-accent">Simpan Data</q-tooltip>
            </q-btn>
         </div>
      </form>

      <q-dialog v-model="varianModal">
         <q-dialog v-model="varianModal" persistent>
            <VarianModal :mustHaveSubvarian="mustHaveSubvarian" :canToggleSubvarian="canToggleSubvarian"
               :has_subvarian="form.has_subvarian" @changeSubvarian="(val) => form.has_subvarian = val"
               @addVarian="addVarianProduk"></VarianModal>
         </q-dialog>
      </q-dialog>
      <q-dialog v-model="editLabelModal" persistent>
         <q-card class="card-md" v-if="form.varians.length">
            <form @submit.prevent="submitEditLabel">
               <q-card-section>
                  <q-input required v-model="form.varians[editLabelIndex].label" label="Varian Label"></q-input>
                  <q-input required v-model="form.varians[editLabelIndex].value" label="Label"></q-input>
               </q-card-section>
               <q-card-actions class="q-pa-md">
                  <q-btn color="primary" unelevated label="Simpan" type="submit"></q-btn>
               </q-card-actions>
            </form>
         </q-card>
      </q-dialog>

   </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import VarianModal from './VarianFormModal.vue'
import ContentEditor from 'components/ContentEditor.vue'
import CategoryBlock from './CategoryBlock.vue'
import MediaBlock from './MediaBlock.vue'
export default {
   components: { VarianModal, ContentEditor, CategoryBlock, MediaBlock },
   name: 'ProductFormCreate',
   data() {
      return {
         varianModal: false,
         editLabelModal: false,
         editLabelIndex: 0,
         form: {
            title: '',
            price: 0,
            weight: 0,
            stock: 0,
            description: '',
            category_id: '',
            varians: [],
            images: [],
            has_subvarian: false,
            featured_index: 0,
            simple_product: true,
         },
         imagePreview: [],
      }
   },
   computed: {
      errors: function () {
         return this.$store.state.errors
      },
      loading: function () {
         return this.$store.state.loading
      },
      categories() {
         return this.$store.getters['category/getValueOptions']
      },
      mustHaveSubvarian() {
         if (this.form.varians.length) {
            if (this.form.varians[0].has_subvarian) {
               return true
            }
         }
         if (this.form.has_subvarian) {
            return true
         }
         return false
      },
      canAddVarian() {
         if (this.form.varians.length) {
            if (!this.form.varians[0].has_subvarian) {
               return false
            }
         }
         return true
      },
      canToggleSubvarian() {
         if (this.form.varians.length) {
            if (this.form.varians[0].has_subvarian) {
               return false
            }
         }
         return true
      },

   },
   methods: {
      ...mapActions('product', ['productStore']),
      ...mapActions('customerService', ['getCustomerServices']),
      handleEditLabel(index) {
         this.editLabelIndex = index
         this.editLabelModal = true
      },
      onUpdateCategory(catId) {
         this.form.category_id = catId
      },
      submitEditLabel() {
         this.editLabelModal = false
      },
      changeFeaturedImage(index) {
         this.form.featured_index = index
      },
      deleteVarian(varIndex) {
         this.$q.dialog({
            title: 'Konfirmasi',
            message: 'Yakin akan menghapus varian',
            cancel: true
         }).onOk(() => {
            this.form.varians.splice(varIndex, 1)
         })
      },
      deleteSubvarian(varIndex, subIndex) {

         this.form.varians[varIndex].subvarian.splice(subIndex, 1)

         if (!this.form.varians[varIndex].subvarian.length) {
            this.form.varians.splice(varIndex, 1)
         }
      },
      pushSubVarian(varIndex) {
         let varian = this.form.varians[varIndex]

         let tpl = { label: varian.subvarian[0].label, value: '', stock: 0, price: varian.price ?? 0, weight: varian.weight ?? 0 }

         this.form.varians[varIndex].subvarian.push(tpl)

         setTimeout(() => {
            let cls = `.main-var-${varIndex}`
            let col = document.querySelectorAll(cls)
            let nodes = [...col]

            let label = nodes[nodes.length - 1]
            this.jumpToInputClass(label)

         }, 500)
      },
      pushVarian() {
         this.form.varians.push({ has_subvarian: false, label: this.form.varians[0].label, value: '', stock: 0, price: this.form.price ?? 0, weight: this.form.weight ?? 0 })

         setTimeout(() => {
            let col = document.querySelectorAll('.single-varian')
            let nodes = [...col]

            let label = nodes[nodes.length - 1]
            this.jumpToInputClass(label)

         }, 500)

      },
      addVarianProduk(data) {
         let defaultPrice = this.form.price ?? 0;
         let weight = this.form.weight ?? 0;
         let stock = this.form.stock ?? 0;

         if (this.form.has_subvarian) {

            data.tempVarian.value.forEach(v => {
               let varian = { has_subvarian: true, label: data.tempVarian.label, value: v, subvarian: [] }

               data.tempSubvarian.value.forEach(el => {
                  let sub = { label: data.tempSubvarian.label, value: el, stock: stock, price: defaultPrice, weight: weight }
                  varian.subvarian.push(sub)
               })

               this.form.varians.push(varian)

            })
         } else {

            data.tempVarian.value.forEach(val => {
               this.form.varians.push({
                  has_subvarian: false, label: data.tempVarian.label, value: val, stock: stock, price: defaultPrice, weight: weight
               })

            })
         }

         this.varianModal = false

      },
      duplicateVarian(varian, varIndex, key) {
         let newTpl = null

         if (varian.has_subvarian) {

            newTpl = {
               has_subvarian: true,
               label: varian.label,
               value: varian.value,
               subvarian: []
            }

            varian.subvarian.forEach(el => {
               let sub = {
                  label: el.label,
                  value: '',
                  stock: el.stock,
                  price: el.price,
                  weight: el.weight
               }
               newTpl.subvarian.push(sub)
            })

         } else {
            newTpl = {
               has_subvarian: false,
               label: varian.label,
               value: '',
               stock: varian.stock,
               price: varian.price,
               weight: varian.weight
            }
         }

         this.form.varians.splice(varIndex + 1, 0, newTpl)

         let cls = `.${key}${varIndex + 1}`;
         setTimeout(() => {
            let label = document.querySelector(cls);
            this.jumpToInputClass(label)
         }, 500)

         if (key.startsWith('main')) {
            setTimeout(() => {
               this.handleEditLabel(varIndex + 1)

            }, 1000)
         }
      },
      jumpToInputClass(node) {
         node.querySelector('INPUT').focus()
         node.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
         })
      },
      // Submit Product
      submit() {

         if (!this.form.description) {

            this.$q.notify({
               type: 'negative',
               message: 'Deskripsi tidak boleh kosong'
            })
            return
         }

         if (!this.form.simple_product && !this.form.varians.length) {

            this.$q.notify({
               type: 'negative',
               message: 'produk varian tidak boleh kosong'
            })
            return
         }

         // let formData = new FormData();

         // formData.append('simple_product', this.form.simple_product)
         // formData.append('title', this.form.title)
         // formData.append('price', this.form.price)
         // formData.append('weight', this.form.weight)
         // formData.append('has_subvarian', this.form.has_subvarian)
         // formData.append('stock', this.form.stock)
         // formData.append('description', this.form.description)
         // formData.append('featured_index', this.form.featured_index)

         // if (this.form.category_id) {
         //    formData.append('category_id', this.form.category_id)
         // }

         // if (this.form.varians.length > 0) {
         //    formData.append('varians', JSON.stringify(this.form.varians))
         // }

         // for (var j = 0; j < this.form.images.length; j++) {
         //    let file = this.form.images[j];

         //    formData.append('images[' + j + ']', file);
         // }

         this.productStore(this.form)
      },
      selectNewImage() {
         this.$refs.image.click();
      },

      updateImagePreview() {

         let imgs = this.$refs.image.files

         for (let i = 0; i < imgs.length; i++) {

            this.form.images.push(imgs[i])

            const reader = new FileReader();

            reader.onload = (e) => {
               this.imagePreview.push(e.target.result);
            };

            reader.readAsDataURL(imgs[i]);
         }
      },

      removeImage(index) {
         if (this.form.featured_index == index) {
            if (index != 0) {
               this.form.featured_index--
            } else {
               this.form.featured_index = 0
            }
         }

         this.imagePreview.splice(index, 1)
         this.form.images.splice(index, 1)

      },

   }
}
</script>