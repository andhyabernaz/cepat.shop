<template>
   <q-page padding class="bg-grey-1" :class="{'flex flex-center' : !posts.available}">
      <MobileHeader title="Artikel" goBack></MobileHeader>

      <div class="main-content">
         <!-- <div class="post-breadcrumb">

            <q-breadcrumbs class="text-grey-7 text-md" active-color="grey-7">
               <q-breadcrumbs-el label="Home" to="/" />
               <q-breadcrumbs-el label="Artikel" :to="{ name: 'FrontPostIndex' }" />
            </q-breadcrumbs>
         </div> -->
         <h1 class="post-title" v-if="posts.available">Artikel</h1>
         <div class="q-pa-sm q-mb-sm" v-if="tags.length">
            <div class="q-gutter-sm">
               <q-btn unelevated @click="getAllData" class="btn-tags" :color="selected == 'all' ? 'primary' : 'white'"
                  :text-color="selected == 'all' ? 'white' : 'dark'" no-caps rounded>Semua</q-btn>
               <q-btn unelevated :color="selected == tag ? 'primary' : 'white'"
                  :text-color="selected == tag ? 'white' : 'dark'" @click="handleGetData(tag)" class="btn-tags" no-caps
                  rounded v-for="(tag, i) in tags" :key="i">{{ tag }}</q-btn>
            </div>

         </div>
         <template v-if="posts.ready">
            <div v-if="posts.available" class="post-card-container">
               <PostCard v-for="(post, index) in posts.data" :key="index" v-bind="post" />
               <SimplePagination v-bind="posts" @loadUrl="loadUrl" auto-hide> </SimplePagination>
            </div>
            <div v-if="!posts.available">
               <EmptyData title="Kehalaman Artikel" routeName="FrontPostIndex"></EmptyData>
            </div>
         </template>
         <div v-else class="post-card-container">
            <div class="column full-height relative bg-white" v-for="i in 6" :key="i">
               <q-skeleton height="200px" square />
               <div class="relative col column justify-between q-pa-sm">
                  <q-skeleton type="text" width="80%" />
                  <q-skeleton type="text" width="100%" />
                  <q-skeleton class="q-mt-sm" height="20px" width="90%" />
               </div>
            </div>
         </div>
      </div>
   </q-page>
</template>

<script>
import { createMetaMixin } from 'quasar'
import PostCard from 'components/PostCard.vue'
import { mapState } from 'vuex'
import { Api } from 'src/boot/axios'
import EmptyData from 'src/components/EmptyData.vue'
import post from 'src/store/post'
export default {
   components: { PostCard, EmptyData },
   mixins: [
      createMetaMixin(function () {
         return {
            title: 'Artikel',
            meta: {
               ogTitle: { property: 'og:title', content: 'Artikel' },
               ogImage: { property: 'og:image', content: this.posts.data.length ? this.posts.data[0].asset.src : '' },
            }
         }
      })
   ],
   data() {
      return {
         selected: this.$route.query.tags || 'all'
      }
   },
   computed: {
      ...mapState({
         posts: state => state.front.posts
      }),
      tags: {
         get() {
            return this.$store.state.front.post_tags
         },
         set(val) {
            this.$store.commit('front/SET_POST_TAGS', val)
         }
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
   },
   created() {
      this.getData()
      if (!this.tags.length) {
         this.gettags()
      }
   },
   methods: {
      gettags() {
         Api.get('post-tags').then(res => {
            this.tags = res.data.data
         })
      },
      getAllData() {
         this.selected = 'all'
         let url = `getPosts?q=listing`
         this.$router.replace({ name: 'FrontPostIndex' })
         this.getData(url)
      },
      handleGetData(tag) {
         this.selected = tag
         this.$router.replace({ name: 'FrontPostIndex', query: { tags: tag } })
         this.getData()
      },
      getData(url = null) {
         if (!url) {
            url = `getPosts?q=listing&tags=${this.selected}`
         }
         this.$store.dispatch('front/getPosts', url)
      },
      loadUrl(url) {
         this.getData(url)
      }
   },
}
</script>