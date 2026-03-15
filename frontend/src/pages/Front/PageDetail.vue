<template>
   <q-page class="bg-grey-1">
      <MobileHeader :title="page?.title || 'Halaman'" goBack></MobileHeader>
      <div class="bg-white q-layout-padding">
         <div class="main-content" v-if="ready">
            <div v-if="page">
               <div class="post-breadcrumb">
                  <q-breadcrumbs class="text-grey-7" active-color="grey-7" gutter="xs">
                     <q-breadcrumbs-el label="Home" to="/" />
                     <q-breadcrumbs-el :label="page.title" />
                  </q-breadcrumbs>
               </div>

               <h1 class="page-title">{{ page.title }}</h1>
               <q-img v-if="page.asset" :src="page.asset.src" class="bg-white box-shadow post-image"></q-img>
               <div class="post-content">
                  <div v-html="page.content"></div>
               </div>
            </div>
            <div v-else class="text-center q-py-xl">
               <h1 class="post-title">404!</h1>
               <div class="text-grey-8">Halaman tidak ditemukan</div>
               <q-btn class="q-mt-md" label="Kembali ke Beranda" color="primary" to="/" />
            </div>
         </div>
      </div>
   </q-page>
</template>

<script>
import { Api } from 'boot/axios'
import { createMetaMixin } from 'quasar'
export default {
   mixins: [
      createMetaMixin(function () {
         return {
            title: this.page?.title,
            meta: {
               description: { name: 'description', content: this.page?.meta_description || '' },
               ogTitle: { property: 'og:title', content: this.page?.title },
               ogImage: { property: 'og:image', content: this.page?.asset?.src || '' },
            }
         }
      })
   ],
   data() {
      return {
         ready: false,
         page: null,
         is_loading: false
      }
   },
   methods: {
      async getPage(slug) {
         this.is_loading = true
         try {
            let { data } = await Api.get(`getPage/${slug}`)
            this.page = data.data
         } catch (e) {
            this.page = null
         } finally {
            this.ready = true
            this.is_loading = false
         }
      },
      goBack() {
         if (this.$route.query._rdr) {
            this.$router.push(this.$route.query._rdr)
         } else {
            if (window.history.length > 2) {
               this.$router.back()
            } else {
               this.$router.push({ name: 'Home' })
            }
         }
      }
   },
   created() {
      this.getPage(this.$route.params.slug)
   },
   watch: {
      '$route.params.slug': function (slug) {
         if (!slug) return
         this.ready = false
         this.getPage(slug)
      }
   }
}
</script>

