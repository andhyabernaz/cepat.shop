<script>
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         order: null,
         form: {
            order_id: '',
            items: [],
         },
         selectedIndex: null
      }
   },
   mounted() {
      this.getOrder()
   },
   methods: {
      submitReviews() {
         BaseApi.post('customer/submitReviews', this.form).then((res) => {
            this.$router.push({ name: 'CustomerReviews' })
         })
      },
      resizeImage(img, maxWidth, maxHeight) {
         const canvas = document.createElement('canvas');
         const ctx = canvas.getContext('2d');

         let width = img.width;
         let height = img.height;

         // Calculate new dimensions while maintaining aspect ratio
         if (width > height) {
            if (width > maxWidth) {
               height *= maxWidth / width;
               width = maxWidth;
            }
         } else {
            if (height > maxHeight) {
               width *= maxHeight / height;
               height = maxHeight;
            }
         }

         canvas.width = width;
         canvas.height = height;

         // Draw the image with new dimensions
         ctx.drawImage(img, 0, 0, width, height);

         // Get the resized image data as a data URL (or use canvas.toBlob() for a file object)
         const resizedDataUrl = canvas.toDataURL('image/jpeg', 0.8); // Adjust quality as needed

         return resizedDataUrl;
      },
      handleUploadImage(idx) {
         this.selectedIndex = idx

         const inputFile = document.getElementById('inputFile')
         inputFile.value = ''
         inputFile.click()

      },
      handleProcessImage(e) {
         const files = e.target.files
         for (let i = 0; i < files.length; i++) {
            var img = new Image();
            img.onload = () => {
               const resized = this.resizeImage(img, 800, 600);
               this.form.items[this.selectedIndex].review_images.push(resized)

               console.log(this.form);
               
            };

             img.src = URL.createObjectURL(files[i])
         }

      },
      getOrder() {
         BaseApi.get('customer/invoice/' + this.$route.params.invoice_ref).then(res => {
            this.order = res.data.data

            if (!this.order || !this.order.can_review) {
               this.$router.back()
            }

            if (this.order) {
               this.form.order_id = this.order.id
               this.order.items.forEach(el => {
                  let item = {
                     product_name: el.name,
                     product_varian: el.note,
                     product_id: el.product_id,
                     comment: '',
                     rating: 0,
                     review_images: []

                  }
                  this.form.items.push(item)
               });
            }

         })
      }
   }
}
</script>

<template>
   <q-page padding>
      <AppHeader title="Berikan Ulasan" size="20px" routerBack></AppHeader>
      <div>
         <form @submit.prevent="submitReviews">
            <q-card v-for="(item, idx) in form.items" :key="idx" class="q-mb-md" flat bordered>
               <q-card-section>

                  <div class="row-responsive justify-between">

                     <div class="text-md q-mb-sm">
                        <q-item-label>{{ form.items[idx].product_name }}</q-item-label>
                        <q-item-label caption v-if="form.items[idx].product_varian">Varian
                           {{ form.items[idx].product_varian }}</q-item-label>
                     </div>
                     <div>
                        <q-rating data-nosnippet="true" required v-model="form.items[idx].rating" color="accent"
                           icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half" size="1.6rem" />
                     </div>
                  </div>
                  <q-input outlined stack-label class="q-mt-sm" rows="2" label="Ulasan Anda" type="textarea" required
                     v-model="form.items[idx].comment"></q-input>

                  <div class="q-mt-sm reviews-image-container">
                     <q-icon class="img-thumbnail" name="add_a_photo" size="65px" color="grey-5"  @click="handleUploadImage(idx)"></q-icon>
                    <div class="relative" v-for="(img, i) in form.items[idx].review_images" :key="i" :ratio="1" >
                      <q-img class="img-thumbnail" :src="img" :ratio="1"></q-img>
                      <div class="absolute-top-right">review
                         <q-btn round dense icon="close" size="xs" color="red" @click="form.items[idx].review_images.splice(i, 1)"></q-btn>
                      </div>
                    </div>
                  </div>
               </q-card-section>
            </q-card>
            <div class="q-py-md">
               <q-btn label="Kirim Ulasan" type="submit" color="primary" class="full-width"></q-btn>
            </div>
         </form>
      </div>
      <input type="file" accept="image/*" class="hidden" id="inputFile" @change="handleProcessImage"  multiple/>
   </q-page>

</template>