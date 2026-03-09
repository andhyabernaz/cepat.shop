<template>
   <q-page padding>
      <AppHeader title="Artikel">
         <q-btn v-if="$can('create-content')" color="white" text-color="dark" icon="add" :to="{ name: 'PostCreate' }" label="Artikel" />
      </AppHeader>
      <q-card class="section shadow">
         <q-card-section>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['#', 'Title', 'Action']" :key="h">{{ h }}</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="post in posts.data" :key="post.id">

                        <td>
                           <q-img v-if="post.asset" :src="post.asset.src" class="bg-white img-thumbnail img-avatar"
                              ratio="1" />
                        </td>

                        <td>
                           <q-item-label>{{ post.title }}</q-item-label>
                           <div class="flex no-wrap q-mt-xs q-gutter-xs">
                              <q-badge dense v-if="post.is_listing" color="blue"
                                 text-color="white">Listing</q-badge>
                              <q-badge dense v-if="post.is_promote" color="teal"
                                 text-color="white">Promote</q-badge>
                              <q-badge dense v-if="post.tags" color="grey-7"
                                 text-color="white">#{{ post.tags }}</q-badge>
                           </div>
                        </td>

                        <td>
                           <div class="flex no-wrap q-gutter-xs">
                              <q-btn v-if="$can('delete-content')" @click="remove(post.id)" size="11px" round icon="delete" color="red" />
                              <q-btn v-if="$can('update-content')" :to="{ name: 'PostEdit', params: { id: post.id } }" size="11px" round color="blue"
                                 icon="edit" />
                              <q-btn
                                 :to="{ name: 'FrontPostShow', params: { slug: post.slug }, query: { _rdr: $route.path } }"
                                 size="11px" round color="teal" icon="open_in_new" />
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>

            <div>
               <div class="text-center q-py-md" v-if="!posts.available">Tidak ada data</div>
            </div>

         </q-card-section>
      </q-card>
      <div class="q-my-md">

         <SimplePagination autoHide v-bind="posts" @loadUrl="loadmore"></SimplePagination>
      </div>
   </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import SimplePagination from 'src/components/SimplePagination.vue'
export default {
   name: 'PostAdminIndex',
   components: { SimplePagination },
   data() {
      return {
         isSelect: '',
         queryParams: {
            search: '',
            per_page: 10,
         }
      }
   },
   computed: {
      ...mapState({
         posts: state => state.post.posts,
         loading: state => state.loading
      }),
   },
   methods: {
      ...mapActions('post', ['getAllPost', 'deletePost']),
      handleSelectFilter(str) {
         this.isSelect = str
         this.$store.commit('post/FILTERBY', str)
      },
      loadmore(url) {
         this.getData(url)
      },
      getData(url = null) {
         this.$store.commit('SET_LOADING', true)
         if (!url) {
            url = `posts?${new URLSearchParams(this.queryParams).toString()}`
         }
         this.getAllPost(url)
      },
      remove(id) {
         this.$q.dialog({
            title: 'Konfirmasi Penghapusan Item',
            message: 'Yakin akan menghapus data?',
            ok: { label: 'Hapus', flat: true, 'no-caps': true },
            cancel: { label: 'Batal', flat: true, 'no-caps': true },
         }).onOk(() => {
            this.deletePost(id)
         })
      },
   },
   created() {
      if (!this.posts.total) this.getAllPost()
   }
}
</script>
