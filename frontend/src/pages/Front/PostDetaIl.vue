<template>
   <q-page class="bg-grey-1">
      <MobileHeader title="Artikel" goBack></MobileHeader>
      <div class="bg-white q-layout-padding">
         <div class="main-content" v-if="ready">
            <div v-if="post">
               <div class="post-breadcrumb">
   
                  <q-breadcrumbs class="text-grey-7" active-color="grey-7"  gutter="xs">
                     <q-breadcrumbs-el label="Home" to="/" />
                     <q-breadcrumbs-el label="Artikel" :to="{ name: 'FrontPostIndex' }" />
                     <!-- <q-breadcrumbs-el :label="post.title"  /> -->
                     <q-breadcrumbs-el v-if="post.tags" :label="post.tags"
                        :to="{ name: 'FrontPostIndex', query: { tags: post.tags } }" />
                  </q-breadcrumbs>
               </div>
   
               <h1 class="post-title">{{ post.title }}</h1>
               <div class="post-submited">
   
                  <q-list>
                     <q-item class="q-px-none">
                        <q-item-section side class="q-pr-sm">
                           <q-icon color="primary" size="40px" name="account_circle" text-color="white"></q-icon>
                        </q-item-section>
                        <q-item-section>
                           <div class="post-submited-author">
                              <q-item-label class="author-name">
                                 {{ post.user.name }}
                              </q-item-label>
                              <q-icon class="author-verified" color="green" name="verified"></q-icon>
   
                           </div>
                           <q-item-label class="post-submited-date">{{ dateFormat(post.created_at) }}</q-item-label>
                        </q-item-section>
                     </q-item>
                  </q-list>
               </div>
               <q-img v-if="post.asset" :src="post.asset.src" class="bg-white box-shadow post-image"></q-img>
               <div class="post-content">
                  <div v-html="post.body"></div>
               </div>
   
              
            </div>
            <div v-else class="text-center q-py-xl">
               <h1 class="post-title">404!</h1>
               <div class="text-grey-8">Halaman tidak ditemukan</div>
               <q-btn class="q-mt-md" label="Lihat Artikel Lainnya" color="primary"
                  :to="{ name: 'FrontPostIndex' }"></q-btn>
            </div>
         </div>

      </div>
      <div class="bg-grey-1 q-layout-padding q-my-md" v-if="related_posts.length">

         <div class="main-content">
            <div class="block-title featured q-mb-md">
               <h2>Related Post</h2>
            </div>
            <!-- <div class="q-gutter-y-xs">
               <PostList with_teaser @onClick="loadData" v-for="(post, i) in related_posts" :key="i" v-bind="post">
               </PostList>

            </div> -->
            <div class="post-list-container">
                <PostList with_teaser @onClick="loadData" v-for="(post, i) in related_posts" :key="i" v-bind="post">
               </PostList>
            </div>
         </div>
      </div>
   </q-page>
</template>

<script>
import { Api } from 'boot/axios'
import { createMetaMixin } from 'quasar'
import PostList from 'src/components/PostList.vue'
export default {
   components: { PostList },
   mixins: [
      createMetaMixin(function () {
         return {
            title: this.post?.title,
            meta: {
               ogTitle: { property: 'og:title', content: this.post?.title },
               ogImage: { property: 'og:image', content: this.post?.image_url },
            }
         }
      })
   ],
   data() {
      return {
         ready: false,
         post: null,
         related_posts: [],
         is_loading: false
      }
   },
   computed: {
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
   },
   methods: {
      async getPost(slug) {
         this.is_loading = true
         let { data } = await Api.get(`getPost/${slug}`)
         this.post = data.data
         this.ready = true
         this.is_loading = false
         this.getRelatedPost(this.post)
      },
      getRelatedPost(post) {
         Api.get(`getRelatedPost/${post.id}`).then(res => {
            this.related_posts = res.data.data
         })
      },
      loadData(slug) {
         if (this.is_loading) return;
         this.getPost(slug)
      },
      goBack() {
         if (this.$route.query._rdr) {
            this.$router.push(this.$route.query._rdr)
         } else {
            if (window.history.length > 2) {
               this.$router.back()
            } else {
               this.$router.push({ name: 'FrontPostIndex' })
            }
         }
      }
   },
   created() {
      if (!this.post || this.post.slug != this.$route.params.slug) {
         this.getPost(this.$route.params.slug)
      } else {
         this.ready = true
      }
   },
}
</script>
