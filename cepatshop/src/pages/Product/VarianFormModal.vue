<template>
   <q-card bordered class="card-lg">
      <div class="card-heading text-md">Tambah Produk Varian</div>
      <form @submit.prevent="addVarianProduk">
         <q-card-section>
            <div>
               <q-input :readonly="varianLabel ? true : false" required label="Varian Label"
                  v-model="form.tempVarian.label" placeholder="contoh: Ukuran"></q-input>
               <q-select ref="inputVarian" class="q-mt-sm" v-model="form.tempVarian.value" use-input use-chips multiple
                  hide-dropdown-icon new-value-mode="add-unique" label="Varian Item (multiple)" :error="varianError"
                  @input-value="handleInputVarian" hint='Gunakan koma (",") untuk menambahkan item'>
                  <template v-slot:error>Item tidak boleh kosong</template>
               </q-select>

            </div>

            <q-toggle class="q-mt-lg" v-if="canToggleSubvarian" v-model="subvarian_toggle"
               label="Dengan Subvarian?"></q-toggle>

            <div class="q-mt-lg" v-if="mustHaveSubvarian">
               <q-input :readonly="subvarianLabel ? true : false" required label="Subvarian Label"
                  v-model="form.tempSubvarian.label" placeholder="contoh: Warna"></q-input>
               <q-select ref="inputsubvarian" class="q-mt-sm" v-model="form.tempSubvarian.value" use-input use-chips
                  multiple hide-dropdown-icon new-value-mode="add-unique" label="Subvarian Item (multiple)"
                  :error="subvarianError" @input-value="handleInputSubvarian"
                  hint='Gunakan koma (",") untuk menambahkan item'>
                  <template v-slot:error>Item tidak boleh kosong</template>
               </q-select>

            </div>
            <div class="flex q-mt-md q-gutter-sm">
               <q-btn unelevated label="Tambah" type="submit" color="primary"></q-btn>
               <q-btn label="Tutup" v-close-popup outline color="primary"></q-btn>
            </div>
         </q-card-section>
      </form>
   </q-card>
</template>

<script>
export default {
   props: ['has_subvarian', 'mustHaveSubvarian', 'canToggleSubvarian', 'varianLabel', 'subvarianLabel'],
   data() {
      return {
         varianError: false,
         subvarianError: false,
         form: {
            tempVarian: {
               label: '',
               value: [],
            },
            tempSubvarian: {
               label: '',
               value: [],
            },
         }
      }
   },
   watch: {
      'tempVarian.value': function (val) {
         if (val.length) {
            this.varianError = false
         }
      },
      'tempSubvarian.value': function (val) {
         if (val.length) {
            this.subvarianError = false
         }
      }
   },
   computed: {
      subvarian_toggle: {
         get() {
            return this.has_subvarian
         },
         set(val) {
            this.$emit('changeSubvarian', val)
         }
      }
   },
   mounted() {
      this.form.tempVarian.value = []
      this.form.tempSubvarian.value = []
      this.varianError = false
      this.subvarianError = false

      if (this.varianLabel) {
         this.form.tempVarian.label = this.varianLabel
      }
      if (this.subvarianLabel) {
         this.form.tempSubvarian.label = this.subvarianLabel
      }
   },
   methods: {
      createValue(val, done) {
         done(val)
      },
      handleInputVarian(val) {
         this.varianError = false
         if (val.length) {
            let lastChar = val.substr(val.length - 1)
            if (lastChar == ',') {
               let newVal = val.slice(0, -1)
               if (!this.form.tempVarian.value.includes(newVal)) {
                  this.form.tempVarian.value.push(newVal)
               }
               this.$refs.inputVarian.updateInputValue('')
            }
         }
      },
      handleInputSubvarian(val) {
         this.subvarianError = false
         if (val.length) {
            let lastChar = val.substr(val.length - 1)
            if (lastChar == ',') {
               let newVal = val.slice(0, -1)
               if (!this.form.tempSubvarian.value.includes(newVal)) {
                  this.form.tempSubvarian.value.push(newVal)
               }
               this.$refs.inputsubvarian.updateInputValue('')
            }
         }
      },
      addVarianProduk() {

         if (!this.form.tempVarian.value.length) {
            this.varianError = true
            return
         }
         if (this.subvarian_toggle && !this.form.tempSubvarian.value.length) {
            this.subvarianError = true
            return
         }

         this.$emit('addVarian', this.form)
      }
   }
}
</script>
