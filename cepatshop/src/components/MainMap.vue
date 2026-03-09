<template>
   <div>
      <div class="q-mb-md">
         <q-input :loading="searchLoading" dense placeholder="cari desa atau kecamatan" v-model="query"
            @update:modelValue="searchQuery" debounce="1000" outlined></q-input>
      </div>
      <div class="relative">
         <div class="list-box-content" v-show="showList">
            <q-list separator>
               <q-item v-for="(list, i) in lists" :key="i" clickable @click="selectItemList(list)">
                  <q-item-section>{{ list.label }}</q-item-section>
               </q-item>
               <q-item v-if="notFound">
                  <q-item-section class="text-center">Data tidak ditemukan</q-item-section>
               </q-item>
            </q-list>
         </div>
         <div class="relative">
            <div id="mainMapContainer">
            </div>
            <q-btn flat icon="ion-locate" :loading="loading" class="bg-white text-grey-7 btn-floating" round dense
               padding="sm" @click="getCurrentPosition"></q-btn>
         </div>
      </div>
   </div>
</template>

<script>
import "leaflet/dist/leaflet.css";
import 'leaflet-geosearch/dist/geosearch.css';
import L from 'leaflet'
import { OpenStreetMapProvider } from 'leaflet-geosearch';

export default {
   props: ["config", "coordinate", "is_client", 'radius'],
   data() {
      return {
         map: null,
         zoom: 11,
         searchControl: null,
         center: [-7.969945177009877, 110.6048905849457],
         marker: null,
         originMarkerOption: {
            clickable: true,
            draggable: true,
            autoPan: true,
            icon: null
         },
         query: '',
         provider: null,
         lists: [],
         listSelected: null,
         searchLoading: false,
         notFound: false,
         container: null,
         loading: false,
         icon_admin: "/static/warehouse.png",
         icon_client: "/static/delivery.png",
         circle: null
      }
   },
   mounted() {
      this.initialProvider()

      this.$nextTick(() => {
         setTimeout(() => {
            this.initialMap()
         }, 1000)
      })
   },
   beforeUnmount() {
      this.map.remove()
   },

   watch: {
      radius(val) {

         if (this.map) {
            this.updateCircle(this.coordinate, val)
         }
      },
   },
   computed: {
      showList() {
         return this.lists.length || this.notFound
      },
   },
   methods: {
      searchQuery() {
         this.notFound = false
         this.searchLoading = true
         this.provider.search({ query: this.query }).then(response => {
            this.lists = response

         }).finally(() => {
            this.searchLoading = false
            if (!this.lists.length) {
               this.notFound = true

               setTimeout(() => {
                  this.notFound = false

               }, 5000)
            }
         })

      },
      updateCircle(center, radius) {

         if (this.circle) {

            if (!radius) {
               this.map.removeLayer(this.circle)
               this.circle = null
 
               return;
            }

             this.circle.setRadius(radius)

         }else {
             setTimeout(() => {
               this.circle = L.circle(center, radius)
               this.circle.addTo(this.map);
            }, 600)
         }

      },
      inputQuery() {
         this.notFound = false
      },
      selectItemList(list) {

         this.listSelected = list
         this.emitData([list.y, list.x]);
         this.lists = []

         this.setMapView()

      },
      emitData(center) {
         this.$emit('onEmitMap', center)
      },
      initialProvider() {
         this.provider = new OpenStreetMapProvider({
            params: {
               countrycodes: 'id'
            }
         });
      },
      initialMap() {
         this.originMarkerOption.icon = L.icon({
            iconUrl: this.is_client ? this.icon_client : this.icon_admin,
            iconSize: [40, 40],
            iconAnchor: [20, 40]
         })

         if (!this.coordinate || !this.coordinate.length) {

            this.map = L.map('mainMapContainer').setView(this.center, this.zoom);

         } else {
            this.map = L.map('mainMapContainer').setView(this.coordinate, this.zoom);

         }

         if (this.config && this.config.mapbox_access_token) {

            L.tileLayer(`https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=${this.config.mapbox_access_token}`, {
               attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
               maxZoom: 20,
               id: 'mapbox/streets-v11',
               tileSize: 512,
               zoomOffset: -1,
               accessToken: this.config.mapbox_access_token
            }).addTo(this.map);

         } else {

            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

         }


         this.updateCircle(this.coordinate, this.radius)


         this.map.on('click', this.handleMapClicked);
         this.map.on('zoomend', this.setOriginMarker);

         this.setOriginMarker()

      },
      setOriginMarker() {

         if (this.originMarker) {
            this.map.removeLayer(this.originMarker)
         }
         if (this.coordinate && this.coordinate.length) {

            setTimeout(() => {


               this.originMarker = L.marker(this.coordinate, this.originMarkerOption)

               this.originMarker.addTo(this.map)

               this.originMarker.on('dragend', this.handleMArkerDragend)

            }, 200)
         }

      },
      handleMArkerDragend(e) {

         let latlng = e.target._latlng

         this.emitData([latlng.lat, latlng.lng])

         this.setMapView()

      },
      handleMapClicked(e) {

         let container = L.DomUtil.create('div')
         let btn = L.DomUtil.create('button', '', container);
         btn.setAttribute('type', 'button');
         btn.innerHTML = 'Pilih lokasi ini'

         L.popup()
            .setContent(container)
            .setLatLng(e.latlng)
            .openOn(this.map);
         L.DomEvent.on(btn, 'click', () => {

            this.emitData([e.latlng.lat, e.latlng.lng])

            this.setMapView()

         });
      },
      setMapView() {
         setTimeout(() => {
            this.map.setView(this.coordinate)
            this.map.closePopup();
            this.loading = false

            this.setOriginMarker()

            this.updateCircle(this.coordinate, this.radius)
         }, 200)

      },
      getAutoPosition() {
         this.getCurrentPosition()

         setTimeout(() => {

            this.initialMap()

         }, 2000)
      },
      getCurrentPosition() {
         this.loading = true
         // const options = {
         //   enableHighAccuracy: true,
         //   timeout: 5000,
         // };
         if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.geoLocationSuccess, this.geoLocationError);

         } else {
            this.$q.notify({
               type: 'warning',
               message: 'Geolocation is not supported by this browser.'
            })
         }
      },
      geoLocationError() {
         this.loading = false
         this.$q.notify({
            type: 'warning',
            message: 'Lokasi tidak ditemukan, coba ulangi sekali lagi.'
         })
      },
      geoLocationSuccess(position) {
         this.emitData([position.coords.latitude, position.coords.longitude])
         this.loading = false
         setTimeout(() => {
            this.setMapView()
         }, 2000)
      }
   },
   beforeUnmount() {
      this.map = null
   }
}
</script>
<style scoped>
#mainMapContainer {
   height: 380px;
   width: 100%;
   background-color: rgb(202, 202, 202);
}

.list-box {
   position: relative;
   z-index: 1006;
}

.list-box-content {
   position: absolute;
   width: 100%;
   overflow-y: auto;
   background-color: #ffffff;
   z-index: 1006;
   max-height: 270px;
   height: auto;
}
</style>
