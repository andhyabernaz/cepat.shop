<template>
  <q-page padding>
    <AppHeader title="Kategori">
      <q-btn
        color="white"
        text-color="dark"
        label="Kategori"
        icon="add"
        v-if="$can('create-category')"
        :to="{ name: 'CategoryForm' }"
      />
    </AppHeader>
    <q-card class="section shadow">
      <q-card-section>
        <q-list separator>
          <q-item class="item-header" dense>
            <q-item-section style="width: 65px" side>Image</q-item-section>
            <q-item-section> Label </q-item-section>
            <q-item-section side> Aksi </q-item-section>
          </q-item>
          <q-expansion-item
            v-for="cat in categories.data"
            :key="cat.id"
            expand-separator
            group="menu-category"
            class="q-px-none"
            header-class="q-px-xs"
          >
            <template v-slot:header>
              <q-item-section avatar>
                <q-img
                  :src="cat.src"
                  ratio="1"
                  class="img-thumbnail img-avatar"
                />
              </q-item-section>
              <q-item-section>
                <q-item-label> {{ cat.title }}</q-item-label>
                <q-item-label>
                  <q-badge
                    v-if="cat.is_front"
                    size="sm"
                    color="teal"
                    text-color="white"
                  >
                    listing
                  </q-badge>
                </q-item-label>
              </q-item-section>

              <q-item-section side>
                <div class="row items-center q-gutter-x-sm">
                  <q-btn
                    dense
                    text-color="red"
                    @click="remove(cat.id)"
                    unelevated
                    icon="delete"
                    v-if="$can('delete-category')"
                  />
                  <q-btn
                    dense
                    text-color="blue"
                    v-if="$can('update-category')"
                    :to="{
                      name: 'CategoryFormEdit',
                      params: { category_id: cat.id },
                    }"
                    unelevated
                    icon="edit"
                  />
                  <!-- <q-btn dense size="13px" text-color="teal" icon="add" unelevated
                              :to="{ name: 'CategoryForm', query: { parent_id: cat.id } }" /> -->
                </div>
              </q-item-section>
            </template>
            <q-list separator class="bg-grey-1">
              <q-item v-for="item in cat.childs" :key="item.id">
                <q-item-section avatar>
                  <q-icon
                    name="radio_button_unchecked"
                    class="self-center"
                    size="16px"
                  />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ item.title }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <div class="text-grey-8 q-gutter-x-sm">
                    <q-btn
                      unelevated
                      round
                      text-color="red"
                      dense
                      @click="remove(item.id)"
                      v-if="$can('delete-category')"
                      icon="delete"
                    />
                    <q-btn
                      unelevated
                      round
                      text-color="blue"
                      v-if="$can('update-category')"
                      dense
                      :to="{
                        name: 'CategoryFormEdit',
                        params: { category_id: item.id },
                      }"
                      icon="edit"
                    />
                  </div>
                </q-item-section>
              </q-item>
              <div v-if="!cat.childs.length" class="q-py-lg text-center">
                Tidak ada subkategori
              </div>
            </q-list>
          </q-expansion-item>
        </q-list>
        <div v-if="!categories.available" class="q-py-md text-center">
          Tidak ada data
        </div>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
  data() {
    return {
      modal: false,
      removeId: null,
    };
  },
  computed: {
    ...mapState({
      categories: (state) => state.category.category_with_childs,
    }),
  },
  methods: {
    ...mapActions("category", ["getCategoriesWithChilds", "categoryDelete"]),
    getData() {
      this.getCategoriesWithChilds()
    },
    remove(id) {
      this.removeId = id;
      this.$q
        .dialog({
          title: "Konfirmasi Penghapusan Item",
          message: "Yakin akan menghapus data?",
          ok: { label: "Hapus", flat: true, "no-caps": true },
          cancel: { label: "Batal", flat: true, "no-caps": true },
        })
        .onOk(() => {
          this.categoryDelete(this.removeId);
        });
    },
  },
  created() {
    if (!this.categories.data.length) {
      this.getData()
    };
  },
  mounted() {
     this.$canAccess('view-category')
  }
};
</script>
