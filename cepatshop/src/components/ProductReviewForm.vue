<script>

export default {
   props: { product },
   data() {
      return {
         reviewModal: false,
         loading: false,
         form: {
            product_id: '',
            product_slug: '',
            name: '',
            comment: '',
            rating: 0
         },
      }
   },
   methods: {
      submitReview() {
         this.form.product_id = this.product.id
         this.form.product_slug = this.product.slug
         if (this.form.name && this.form.comment && this.form.rating) {
            this.loading = true
            this.reviewModal = false
            this.addProductReview(this.form).then((res) => {
               let dataReview = res.data.data
               if (res.status == 200) {
                  this.$q.notify({
                     type: 'positive',
                     message: res.data.message
                  })
               }
               if (!dataReview.is_approved) {
                  localStorage.setItem('unapproved_review', JSON.stringify(dataReview))
                  this.unapproved_review = dataReview

                  setTimeout(() => {
                     localStorage.removeItem('unapproved_review');
                  }, 30000)
               }
            })
            this.loading = false
         } else {
            this.$q.notify({
               type: 'warning',
               message: 'Semua field harus di isi'
            })
         }
      },
   }
}

</script>

<template>
   <div>

      <q-dialog v-model="reviewModal" persistent>
         <div class="q-card" style="width:100%;max-width:400px;">
            <q-card-section>
               <form @submit.prevent="submitReview">
                  <div>
                     <div class="text-subtitle2 q-mb-sm">Berikan Ulasan Anda</div>
                     <q-rating data-nosnippet="true" v-model="form.rating" color="accent" icon="ion-star-outline"
                        icon-selected="ion-star" icon-half="ion-star-half" size="sm" />
                     <div class="q-my-md q-gutter-y-xs">
                        <q-input dense label="Nama Anda" v-model="form.name"
                           :rules="[val => val && val != '' || 'Wajib disisi']"></q-input>
                        <q-input dense label="Ulasan Anda" type="textarea" v-model="form.comment" rows="3"
                           :rules="[val => val && val != '' || 'Wajib disisi']"></q-input>
                     </div>
                     <div class="q-gutter-y-sm q-my-md items-center text-grey">
                        <div class="text-grey text-xs">Jawab tantangan berikut, hanya untuk memastikan anda bukan robot
                        </div>
                        <div class="row q-gutter-x-sm items-center">
                           <div class="text-weight-bold bg-dark text-white q-px-sm q-py-xs rounded">
                              {{ number2 }} + {{ number1 }}
                           </div>
                           <div class="text-weight-bold"> = </div>
                           <input class="rounded text-grey-9" type="text" ref="jawaban" v-model="jawaban"
                              style="width:60px;padding:3px 6px;border:1px solid grey">
                        </div>
                     </div>
                     <div class="row justify-end q-gutter-x-sm">
                        <q-btn unelevated type="button" @click.prevent="closeReviewModal" label="Batal" color="primary"
                           outline></q-btn>
                        <q-btn unelevated :disable="chalengeTesting" type="submit" :loading="loading"
                           label="Kirim Ulasan" color="primary"></q-btn>
                     </div>
                  </div>
               </form>
            </q-card-section>
         </div>
      </q-dialog>
   </div>
</template>