<template>
   <q-page padding>

      <AppHeader :title="title" routeName="CustomerLicense" ></AppHeader>
      
      <div v-if="data">
         <div class="row" v-if="data.is_digital_video">
            <div class="col-12 col-md-8 q-pa-xs">
               <q-card class="q-mb-md box-shadow video-box">
                  <q-card-section v-if="playing">
                     <!-- <iframe width="560" height="315" :src="`${video.video_embed}?controls=0&showinfo=0&rel=0`" frameborder="0" allowfullscreen="true" class="no-pointer"></iframe> -->
                     <div :class="`embed-responsive ${playing.video_ratio}`" v-html="playing.video_embed"></div>
                     <div class="q-pa-sm">
                        <div class="q-mt-sm text-lg">{{ playing.video_title }}</div>
                        <q-item-label caption>
                           Durasi {{ playing.video_duration }}
                        </q-item-label>
                        <div>
                           <div class="text-subtitle q-mt-md q-mb-sm text-grey-8">Deskripsi Video</div>
                           <div class="q-pa-md bg-grey-1" v-html="playing.video_description" ></div>
                        </div>
                     </div>
                  </q-card-section>
               </q-card>
            </div>
            <div class="col q-pa-xs" style="min-width: 200px">
               <q-card class="box-shadow">
                  <div class="row justify-between items-center q-px-md q-py-sm bg-grey-7 text-white">
                     <div class="text-md">Playlist</div>
                     <div class="">{{ data.digital_videos.length }} Video</div>
                  </div>
                  <div class="">
                     <q-list>
                        <q-item clickable @click="playing = video" class="q-pa-sm"
                           :class="{ 'bg-green-1': playing.id == video.id }" v-for="video in data.digital_videos" :key="video.id">
                           <q-item-section side>
                              <q-icon :name="playing.id == video.id ? 'play_circle_filled' : 'pause'
                                 "></q-icon>
                           </q-item-section>
                           <q-item-section>
                              <div class="q-mt-sm text-weight-semibold">
                                 {{ video.video_title }}
                              </div>
                              <q-item-label caption>
                                 Durasi {{ video.video_duration }}
                              </q-item-label>
                           </q-item-section>
                        </q-item>
                     </q-list>
                  </div>
               </q-card>
            </div>
         </div>
         <div v-if="data.is_digital_download">
            <q-card flat bordered>
               <q-card-section>
                  <table class="table aligned">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Detail</th>
                           <th>Download</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr v-for="(item, idx) in data.digital_downloads" :key="idx">
                           <td>{{ idx + 1 }}</td>
                           <td>
                              <q-item-label class="text-md">{{ item.filename }}</q-item-label>
                              <q-item-label class="text-grey-8" v-if="item.filesize">
                                 size {{ item.filesize }} KB</q-item-label>
                              <q-item-label v-if="item.caption" class="q-pt-xs">
                                 {{ item.caption }}
                              </q-item-label>
                           </td>
                           <td>
                              <q-btn :loading="downloading" unelevated color="primary" class="btn-action" icon="download" label="Download"
                                 @click="handleDownloadLicense(item)"></q-btn>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </q-card-section>
            </q-card>
         </div>
      </div>

   </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";

export default {
   data() {
      return {
         data: null,
         playing: null,
         downloading: false
      };
   },
   computed: {
      loading() {
         return this.$store.state.loading;
      },
      title() {
         if(this.data) {
            return `Lisensi #${this.data.title}`
         }
         return 'Lisensi Produk'
      }
   },
   created() {
      this.getLicenseById();
      // document.addEventListener('contextmenu', event => event.preventDefault());
   },
   methods: {
      getLicenseById() {
         this.$store.commit("SET_LOADING", true);
        BaseApi
            .get("license/" + this.$route.params.license_id)
            .then((response) => {
               if (response.status == 200) {
                  this.data = response.data.data;
                  if (this.data.is_digital_video) {
                     this.playing = this.data.digital_videos[0];
                  }
               }
            })
            .catch((err) => {
               this.$router.push({ name: "CustomerLicense" });
            });
      },
      handleGetFilepath(item) {
         BaseApi
            .get("getDownloadUrl/" + item.id)
            .then((res) => {
               if (res.status == 200) {
                  let url = res.data.data;
                  if (!url.startsWith("http")) {
                     url = "http://" + url;
                  }
                  window.open(url, "new");
               }
            });
      },
      handleDownloadLicense(item) {
         if (item.download_type == "url") {
            this.handleGetFilepath(item);
         } else {
            this.downloading = true
            let params = {
               license_id: this.$route.params.license_id,
               item_id: item.id,
            };
            BaseApi
               .get("download?" + new URLSearchParams(params).toString(), {
                  responseType: "blob",
               })
               .then((response) => {
                  let filename = `file-${item.filename}`;

                  const href = window.URL.createObjectURL(response.data);

                  // create "a" HTML element with href to file & click
                  const link = document.createElement("a");
                  link.href = href;
                  link.setAttribute("download", filename); //or any other extension
                  document.body.appendChild(link);
                  link.click();

                  // clean up "a" element & remove ObjectURL
                  document.body.removeChild(link);
                  window.URL.revokeObjectURL(href);
               }).finally(() => {
                  this.downloading = false
               })
         }
      },
   },
};
</script>
