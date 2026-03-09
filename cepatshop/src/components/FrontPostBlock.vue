<template>
   <BlockObserver @onObserve="handleObserve">
      <div id="post" class="block-container" v-if="posts.count">
         <div class="block-header row items-end justify-between auto-padding">
            <div class="block-title featured">
               <h2>Post Terbaru</h2>
            </div>
            <q-btn flat no-caps color="primary" padding="4px" :to="{ name: 'FrontPostIndex' }">
               <span>Lihat Semua</span>
               <q-icon name="eva-arrow-forward" size="16px"></q-icon>
            </q-btn>
         </div>
         <template v-if="posts.ready">
            <div class="block-content auto-padding-side post-list-container"  :class="{'is-mobile': !isModeDesktop}">
               <PostList v-for="(post, index) in posts.data" :key="index" v-bind="post" />
            </div>
         </template>
         <template v-else>
            <div class="block-content auto-padding-side post-list-container"  :class="{'is-mobile': !isModeDesktop}">
               <PostListSkeleton v-for="a in 2" :key="a" />
            </div>
         </template>
      </div>
   </BlockObserver>
</template>

<script>
import BlockObserver from 'components/BlockObserver';
import PostList from 'components/PostList.vue'
import PostListSkeleton from 'components/PostListSkeleton.vue'
export default {
   components: { PostList, BlockObserver, PostListSkeleton },
   computed: {
      posts() {
         return this.$store.state.front.promote_posts
      },
      isModeDesktop() {
         return this.$store.getters['isModeDesktop']
      }

   },
   methods: {
      handleObserve() {
         if (!this.posts.length) {
            this.$store.dispatch('front/getPromotePosts')
         }
      }
   }
}
</script>
