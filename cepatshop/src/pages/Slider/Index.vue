<template>
   <q-page padding>
      <AppHeader title="Slider Banner">
         <q-btn v-if="$can('create-content')" color="white" text-color="dark" icon="add" label="Slider" @click="handleBtnUpload" />
      </AppHeader>

      <q-card class="section shadow">
         <q-card-section>
            <div class="q-mb-sm q-pa-sm text-xs text-grey-8 bg-yellow-2">
               <div>Note</div>
               <div>
                  Untuk hasil terbaik, Gunakan gambar dengan rasio yang sama.
               </div>
            </div>

            <div>
               <div class="table-responsive">
                  <table class="table aligned bordered">
                     <thead>
                        <tr>
                           <th v-for="h in ['Image', 'Sort', 'Link Post', 'Action']" :key="h">
                              {{ h }}
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr v-for="slider in sliders.data" :key="slider.id">
                           <td>
                              <q-img :src="slider.src" class="bg-white rounded-corners img-thumbnail img-avatar"
                                 ratio="1" />
                           </td>
                           <td>
                              <div class="flex no-wrap q-gutter-sm items-center">
                                 <q-btn icon="remove" size="11px" unelevated dense outline @click="decre(slider)" />
                                 <div class="text-md">{{ slider.weight }}</div>
                                 <q-btn icon="add" size="11px" dense unelevated outline @click="incre(slider)" />
                              </div>
                           </td>
                           <td>
                              <q-item-label lines="2" caption>{{
                                 slider.post ? slider.post.title : "-"
                                 }}</q-item-label>
                           </td>
                           <td class="flex no-wrap q-gutter-xs">
                              <q-btn v-if="$can('delete-content')" @click="remove(slider.id)" size="11px" round icon="delete" color="red">
                                 <q-tooltip>Delete</q-tooltip>
                              </q-btn>
                              <q-btn v-if="$can('update-content')" @click="handleEditPostLink(slider)" size="11px" round icon="link" color="teal">
                                 <q-tooltip>Link to post</q-tooltip>
                              </q-btn>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
            <template v-if="!sliders.available">
               <div class="text-center q-py-md">Tidak ada data</div>
            </template>
         </q-card-section>
      </q-card>
      <input type="file" class="hidden" ref="image" @change="handleFileUpload" />
      <q-dialog v-model="linkModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-title flex justify-between items-center q-mb-md">
                  <div>Link to post</div>
                  <q-btn round dense icon="close" v-close-popup flat></q-btn>
               </div>

               <q-list separator bordered style="height: 380px; overflow-y: auto">
                  <q-item v-for="post in posts.data" :key="post.id" clickable @click="selectPost(post)">
                     <q-item-section avatar>
                        <q-icon :color="form.post_id == post.id ? 'green-7' : 'grey-7'" text-color="white" :name="form.post_id == post.id
                           ? 'check_box'
                           : 'check_box_outline_blank'
                           " />
                     </q-item-section>
                     <q-item-section>{{ post.title }}</q-item-section>
                  </q-item>
               </q-list>

               <div class="card-action">
                  <q-btn label="Submit" color="primary" unelavated @click="handleSubmitData"></q-btn>
                  <q-btn label="Cancel" color="primary" outline v-close-popup></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
import { mapState, mapActions } from "vuex";
export default {
   name: 'SliderIndex',
   data() {
      return {
         timeout: null,
         uploadPercentage: 0,
         linkModal: false,
         form: {
            post_id: "",
            slider_id: "",
         },
      };
   },
   computed: {
      ...mapState({
         sliders: (state) => state.slider.sliders,
         posts: (state) => state.post.posts,
      }),
   },
   mounted() {
      if (!this.sliders.data.length) {
         this.getData();
      }

      if (!this.posts.data.length) {
         this.$store.dispatch("post/getAllPost");
      }
   },
   methods: {
      ...mapActions("slider", [
         "removeSlider",
         "getSliders",
         "updateSliderWeight",
         "setPostLink",
      ]),

      getData() {
          this.$store.commit('SET_LOADING', true)
          this.getSliders()
      },

      changeWeight(weight, id) {
         this.updateSliderWeight({ value: weight, id: id });
      },
      selectPost(post) {
         if (this.form.post_id == post.id) {
            this.form.post_id = "";
         } else {
            this.form.post_id = post.id;
         }
      },

      handleEditPostLink(slider) {
         this.form.slider_id = slider.id;
         this.form.post_id = slider.post_id ?? "";
         this.linkModal = true;
      },

      handleSubmitData() {
         this.setPostLink(this.form).finally(() => {
            this.linkModal = false;
            this.getData();
         });
      },

      incre(slider) {
         this.changeWeight(slider.weight + 1, slider.id);
         // if (this.timeout) clearTimeout(this.timeout);
         // this.timeout = setTimeout(() => {
         //   }, 1000); // delay
      },
      decre(slider) {
         if (slider.weight <= 1) return;
         this.changeWeight(slider.weight - 1, slider.id);

         // if (this.timeout) clearTimeout(this.timeout);
         // this.timeout = setTimeout(() => {
         //   }, 100); // delay
      },

      remove(id) {
         this.$q
            .dialog({
               title: "Konfirmasi Penghapusan Item",
               message: "Yakin akan menghapus data ini?",
               ok: { label: "Hapus", flat: true, "no-caps": true },
               cancel: { label: "Batal", flat: true, "no-caps": true },
            })
            .onOk(() => {
               this.removeSlider(id);
            });
      },
      handleBtnUpload() {
         this.$refs.image.click();
      },
      handleFileUpload() {
         let self = this;
         self.$store.commit("slider/SET_DATA_STATUS", false);
         let formData = new FormData();
         formData.append("image", this.$refs.image.files[0]);

         BaseApi.post("sliders", formData, {
            headers: { "Content-Type": "multipart/form-data" },
            onUploadProgress: (progressEvent) => {
               this.uploadPercentage = parseInt(
                  Math.round((progressEvent.loaded / progressEvent.total) * 100)
               );
            },
         })
            .then(function (res) {
               self.getData();
            })
            .catch(function (err) {
               self.$store.commit("slider/SET_DATA_STATUS", true);
            });
      },
   },
};
</script>
