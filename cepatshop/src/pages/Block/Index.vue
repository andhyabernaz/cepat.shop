<template>
   <q-page padding>
      <AppHeader title="Block Banner">
         <q-btn v-if="$can('create-content')" color="white" text-color="dark" @click="handleAddBlock" icon="add" label="Block" />
      </AppHeader>
      <q-card class="section shadow">
         <q-card-section>
            <div class="table-responsive">
               <table class="table aligned bordered">
                  <thead>
                     <tr>
                        <th v-for="h in ['#', 'Title', 'Position', 'Action']" :key="h">
                           {{ h }}
                        </th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="block in blocks.data" :key="block.id">
                        <td>
                           <q-img v-if="block.image" :src="block.image_url"
                              class="bg-white rounded-corners img-thumbnail img-avatar" />
                        </td>
                        <td>
                           <q-item-label class="text-subtitle2">{{
                              block.label
                           }}</q-item-label>
                           <q-item-label v-if="block.description" caption>
                              <div v-html="block.description"></div>
                           </q-item-label>
                        </td>

                        <!-- <td>
                           <q-badge :color="block.is_show_title ? 'green' : 'grey-7'">{{ block.is_show_title ? 'YA' :
                              'Tidak' }}</q-badge>
                        </td> -->

                        <td>
                           <q-chip size="sm" color="teal" text-color="white">
                              {{ block.position }}
                           </q-chip>
                        </td>

                        <!-- <td>
                           <q-item-label lines="2" caption>{{
                              block.post ? block.post.title : "-"
                           }}</q-item-label>
                        </td> -->

                        <td class="flex no-wrap q-gutter-xs">
                           <q-btn v-if="$can('delete-content')" @click="handleRemoveBlock(block.id)" size="11px" round icon="delete" color="red">
                              <q-tooltip>Delete</q-tooltip>
                           </q-btn>
                           <q-btn v-if="$can('update-content')" @click="handleEditBlock(block)" size="11px" round color="blue" icon="edit">
                              <q-tooltip>Edit</q-tooltip>
                           </q-btn>
                           <q-btn v-if="$can('update-content')" @click="handleEditPostLink(block)" size="11px" round icon="link" color="teal">
                              <q-tooltip>Link to post</q-tooltip>
                           </q-btn>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <template v-if="!blocks.available">
               <div class="text-center q-py-md">Tidak ada data</div>
            </template>
         </q-card-section>
      </q-card>
      <q-dialog v-model="blockModal">
         <q-card style="width: 100%; max-width: 450px" class="bg-white">
            <div class="card-heading text-md">
               {{ formType == "add" ? "Tambah" : "Edit" }} Data
            </div>
            <form @submit.prevent="submitBlock" class="full-width">
               <q-card-section class="q-gutter-y-md">
                  <q-select filled v-model="form.position" :options="positionOptions" label="Pilih Posisi" />
                  <q-input filled required label="Title" v-model="form.label" />
                  <q-input filled label="Deskripsi (opsional)" v-model="form.description" autogrow />
                  <!-- <q-checkbox v-model="form.is_show_title" label="Tampilkan judul dan deskripsi"></q-checkbox> -->
               </q-card-section>
               <q-card-section>
                  <q-btn @click.prevent="handleUploadImage" color="primary" size="sm" label="Upload Gambar"></q-btn>
                  <div class="q-pt-sm text-yellow-10">Untuk hasil terbaik gunakan gambar dengan rasio 1:1</div>
                  <input type="file" class="hidden" ref="image" @change="handleImagePreview" />
               </q-card-section>
               <q-card-section v-if="imagePreview">
                  <div class="row items-center justify-between q-gutter-x-sm">
                     <img :src="imagePreview" style="max-height: 80px; width: auto; object-fit: contain" />
                  </div>
               </q-card-section>
               <q-card-actions class="justify-end q-pa-md sticky-bottom bg-grey-2">
                  <q-btn @click.prevent="closeModal" type="button" color="primary" outline label="Batal"></q-btn>
                  <q-btn :loading="loading" type="submit" color="primary" label="Simpan Data"></q-btn>
               </q-card-actions>
            </form>
         </q-card>
      </q-dialog>

      <q-dialog v-model="linkModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-title flex justify-between items-center q-mb-md">
                  <div>Link to post</div>
                  <q-btn round dense icon="close" v-close-popup flat></q-btn>
               </div>

               <q-list separator bordered style="height: 50vh; overflow-y: auto">
                  <q-item v-for="post in posts.data" :key="post.id" clickable @click="selectPost(post)">
                     <q-item-section avatar>
                        <q-icon :color="formPostLink.post_id == post.id ? 'green-7' : 'grey-7'
                           " text-color="white" :name="formPostLink.post_id == post.id
                              ? 'check_box'
                              : 'check_box_outline_blank'
                              " />
                     </q-item-section>
                     <q-item-section>{{ post.title }}</q-item-section>
                  </q-item>
               </q-list>

               <div class="card-action">
                  <q-btn label="Submit" color="primary" unelavated @click="handleSubmitPostLink"></q-btn>
                  <q-btn label="Cancel" color="primary" outline v-close-popup></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { mapState, mapActions } from "vuex";
export default {
   name: "BlockIndex",
   data() {
      return {
         formType: "",
         blockModal: false,
         form: {
            id: "",
            label: "",
            description: "",
            weight: "",
            position: "Top",
            image: "",
            del_image: false,
            post_id: "",
            is_show_title: false
         },
         imagePreview: "",
         positionOptions: ["Top", "Bottom"],
         linkModal: false,
         formPostLink: {
            post_id: "",
            block_id: "",
         },
      };
   },
   computed: {
      ...mapState({
         blocks: (state) => state.block.admin_data,
         posts: (state) => state.post.posts,
      }),
      loading() {
         return this.$store.state.loading
      }
   },
   mounted() {
      if (!this.blocks.data.length) {
      }
      this.getAdminBlocks();
      if (!this.posts.data.length) {
         this.$store.dispatch("post/getAllPost");
      }
   },
   methods: {
      ...mapActions("block", [
         "addBlock",
         "updateBlock",
         "getAdminBlocks",
         "getBlockById",
         "deleteBlock",
         "setPostLink",
      ]),
      selectPostData(post) {
         this.form.post_id = post.id;
      },
      selectPost(post) {
         if (this.formPostLink.post_id == post.id) {
            this.formPostLink.post_id = "";
         } else {
            this.formPostLink.post_id = post.id;
         }
      },

      handleEditPostLink(block) {
         this.formPostLink.block_id = block.id;
         this.formPostLink.post_id = block.post_id ?? "";
         this.linkModal = true;
      },
      handleSubmitPostLink() {
         this.setPostLink(this.formPostLink).finally(() => {
            this.linkModal = false;
            this.getAdminBlocks();
         });
      },
      submitBlock() {

         if (this.formType == "add") {
            this.addBlock(this.form).then((response) => {
               this.closeModal();
               this.getAdminBlocks();
            });
         }
         if (this.formType == "edit") {
            this.updateBlock(this.form).then(() => {
               this.closeModal();
               this.getAdminBlocks();
            });
         }
      },
      handleEditBlock(block) {
         this.clearForm();

         this.formType = "edit";
         this.form.id = block.id;
         this.form.post_id = block.post_id;
         this.form.label = block.label;
         this.form.description = block.description;
         this.form.weight = block.weight;
         this.form.position = block.position;
         this.form.is_show_title = block.is_show_title;
         this.imagePreview = block.image_url;
         this.blockModal = true;
      },
      handleRemoveBlock(id) {
         this.$q
            .dialog({
               title: "Konfirmasi Penghapusan Item",
               message: "Yakin akan menghapus data?",
               ok: { label: "Hapus", flat: true, "no-caps": true },
               cancel: { label: "Batal", flat: true, "no-caps": true },
            })
            .onOk(() => {
               this.deleteBlock(id);
            });
      },
      handleAddBlock() {
         this.clearForm();
         this.blockModal = true;
         this.formType = "add";
         this.form.weight = this.blocks.data.length
            ? this.blocks.data.length + 1
            : 1;
      },
      clearForm() {
         this.form.id = "";
         this.form.label = "";
         this.form.description = "";
         this.form.post_id = "";
         this.form.image = "";
         this.form.weight = "";
         this.form.del_image = false;
         this.form.is_show_title = false;
         this.imagePreview = "";
      },
      handleUploadImage() {
         this.$refs.image.click();
      },
      handleRemoveImage() {
         this.imagePreview = "";
         this.form.image = "";
         if (this.formType == "edit") {
            this.form.del_image = true;
         }
      },
      closeModal() {
         (this.blockModal = false), this.clearForm();
      },
      handleImagePreview() {
         let img = this.$refs.image.files[0];
         if (!img) return;

         this.form.image = img;

         const reader = new FileReader();

         reader.onload = (e) => {
            this.imagePreview = e.target.result;
         };

         reader.readAsDataURL(img);
      },
   },
};
</script>
