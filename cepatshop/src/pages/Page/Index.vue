<template>
   <q-page padding>
      <AppHeader title="Halaman">
         <q-btn v-if="$can('create-content')" color="white" text-color="dark" icon="add" :to="{ name: 'PageCreate' }"
            label="Halaman" />
      </AppHeader>
      <q-card class="section shadow">
         <q-card-section>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['#', 'Title', 'Status', 'Action']" :key="h">{{ h }}</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="page in pages.data" :key="page.id">
                        <td>
                           <q-img v-if="page.asset" :src="page.asset.src" class="bg-white img-thumbnail img-avatar"
                              ratio="1" />
                        </td>
                        <td>
                           <q-item-label>{{ page.title }}</q-item-label>
                           <q-item-label caption>/page/{{ page.slug }}</q-item-label>
                        </td>
                        <td>
                           <q-badge v-if="page.status === 'published'" color="green" text-color="white">Published</q-badge>
                           <q-badge v-else color="grey-7" text-color="white">Draft</q-badge>
                        </td>
                        <td>
                           <div class="flex no-wrap q-gutter-xs">
                              <q-btn v-if="$can('delete-content')" @click="remove(page.id)" size="11px" round icon="delete"
                                 color="red" />
                              <q-btn v-if="$can('update-content')" :to="{ name: 'PageEdit', params: { id: page.id } }"
                                 size="11px" round color="blue" icon="edit" />
                              <q-btn :to="{ name: 'FrontPageShow', params: { slug: page.slug }, query: { _rdr: $route.path } }"
                                 size="11px" round color="teal" icon="open_in_new" />
                           </div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>

            <div>
               <div class="text-center q-py-md" v-if="!pages.available">Tidak ada data</div>
            </div>
         </q-card-section>
      </q-card>
      <div class="q-my-md">
         <SimplePagination autoHide v-bind="pages" @loadUrl="loadmore"></SimplePagination>
      </div>
   </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import SimplePagination from 'src/components/SimplePagination.vue'
export default {
   name: 'PageAdminIndex',
   components: { SimplePagination },
   data() {
      return {
         queryParams: {
            search: '',
            per_page: 10,
         }
      }
   },
   computed: {
      ...mapState({
         pages: (state) => state.page.pages,
         loading: (state) => state.loading
      }),
   },
   methods: {
      ...mapActions('page', ['getAllPage', 'deletePage']),
      loadmore(url) {
         this.getData(url)
      },
      getData(url = null) {
         this.$store.commit('SET_LOADING', true)
         if (!url) {
            url = `pages?${new URLSearchParams(this.queryParams).toString()}`
         }
         this.getAllPage(url)
      },
      remove(id) {
         this.$q.dialog({
            title: 'Konfirmasi Penghapusan Item',
            message: 'Yakin akan menghapus data?',
            ok: { label: 'Hapus', flat: true, 'no-caps': true },
            cancel: { label: 'Batal', flat: true, 'no-caps': true },
         }).onOk(() => {
            this.deletePage(id)
         })
      },
   },
   created() {
      if (!this.pages.total) this.getAllPage()
   },
   mounted() {
      this.$canAccess('view-content')
   }
}
</script>

