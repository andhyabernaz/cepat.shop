<template>
   <div class="inline-block">
      <q-btn aria-label="Share Page" padding="5px" @click="shareTheWeb" color="grey-1" unelevated dense round
         :size="isDesktop ? '16px' : '15px'" icon="eva-share" text-color="grey-9">
      </q-btn>
      <q-dialog v-model="shareModal" position="bottom">
         <q-card class="section max-width-mobile">
            <q-card-section class="q-gutter-md" style="min-height:200px">
               <div class="card-title">Share</div>
               <q-btn color="blue" flat dense @click="shareFb">
                  <div class="text-center share-btn">
                     <q-icon size="lg" name="ion-logo-facebook"></q-icon>
                     <div>Facebook</div>
                  </div>
               </q-btn>
               <q-btn color="teal" flat dense @click="shareTwitter">
                  <div class="text-center share-btn">
                     <q-icon size="lg" name="ion-logo-twitter"></q-icon>
                     <div>Twitter</div>
                  </div>
               </q-btn>
               <q-btn color="green" flat dense as="a" :href="`whatsapp://send??text=${location}`"
                  data-action="share/whatsapp/share">
                  <div class=" text-center share-btn">
                     <q-icon size="lg" name="ion-logo-whatsapp"></q-icon>
                     <div>Whatsapp</div>
                  </div>
               </q-btn>
               <q-btn color="grey-7" flat dense @click="copyUrl">
                  <div class="text-center share-btn">
                     <q-icon size="lg" name="ion-copy"></q-icon>
                     <div>Salin URL</div>
                  </div>
               </q-btn>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
export default {
   props: ['image_url'],
   data() {
      return {
         shareModal: false,
         title: '',
         location: ''
      }
   },
   computed: {
      webShareApiSupported() {
         return navigator.share
      },
      isDesktop() {
         return this.$q.platform.is.desktop ? true : false
      },
   },
   mounted() {
      setTimeout(() => {
         this.title = document.title;
         this.location = document.location.href;
      }, 500)
   },
   methods: {
      shareTheWeb() {
         if (this.webShareApiSupported) {

            const title = document.title;
            navigator.share({
               title: title,
               text: this.image_url,
               url: document.location.href,
            })
         } else {
            this.shareModal = true
         }

      },
      shareFb() {
         let url = `https://www.facebook.com/sharer/sharer.php?u=${document.location.href}&t=${document.title}`
         window.open(url, 'new')
         this.shareModal = false
      },
      shareTwitter() {
         let url = `https://twitter.com/share?url=${document.location.href}&text=${document.title}`
         window.open(url, 'new')
         this.shareModal = false
      },
      copyUrl() {
         let link = document.location.href
         this.copyString(link)
         this.shareModal = false
      }
   }
}
</script>
