<template>
   <div>
      <!-- <div class="q-mb-md">
         <q-input :loading="searchLoading" dense placeholder="cari desa atau kecamatan" v-model="query"
            @update:modelValue="searchQuery" debounce="1000" outlined></q-input>
      </div> -->
      <div class="relative clint-map-container">
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
            <div id="clientMapContiner">
            </div>
            <q-btn flat icon="ion-locate" :loading="loading" class="bg-white text-grey-7 btn-floating" round dense
               padding="sm" @click="getCurrentLocation"></q-btn>
         </div>
      </div>
   </div>
</template>

<script>
import "leaflet/dist/leaflet.css";

import L from 'leaflet'
import { OpenStreetMapProvider } from 'leaflet-geosearch';
import 'leaflet-control-geocoder';
import 'leaflet-routing-machine'

import 'leaflet-geosearch/dist/geosearch.css';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet-control-geocoder/dist/Control.Geocoder.css';

export default {
   props: ['coordinate', 'config'],
   data() {
      return {
         map: null,
         zoom: 11,
         routeControl: null,
         searchControl: null,
         originMarker: null,
         destinationMarker: null,
         originMarkerOption: {
            clickable: false,
            draggable: false,
            icon: null
         },
         destinationMarkerOption: {
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
         geocoder: null,
         loading: false,
         warehouse_coordinate: [],
         polyline: null,
      }
   },
   mounted() {
      this.$nextTick(() => {
         setTimeout(() => {
            this.initialMap()
         }, 700)
      })
   },
   beforeUnmount() {
      this.map.remove()
   },
   watch: {
      coordinate: function (val) {
         if (val && val.length) {
            if (this.map) {
               this.setDestinationMarker(val)
            }
         }
      }
   },
   computed: {
      showList() {
         return this.lists.length || this.notFound
      },
      shop() {
         return this.$store.state.shop
      }

   },
   methods: {
      autoSearch(key) {
         this.provider.search({ query: key }).then(response => {
            if (response && response.length) {

               this.selectItemList(response[0])
            }
         })
      },
      searchQuery() {
         if (this.query.length < 4) return
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
      inputQuery() {
         this.notFound = false
      },
      selectItemList(list) {
         let center = [list.y, list.x]
         this.listSelected = list
         this.lists = []
         if(this.coordinate && this.coordinate.length) {
            let distance = this.map.distance(center, this.coordinate)
            
            if(distance > 500) {
                   this.emitData(center)
            }
            
         }else {
            this.emitData(center)

         }

      },
      initialMap() {
         // this.warehouse_coordinate = this.config.warehouse_coordinate
         this.provider = new OpenStreetMapProvider({
            params: {
               countrycodes: 'id'
            }
         });

         this.destinationMarkerOption.icon = L.icon({
            iconUrl: "/static/location.png",
            iconSize: [60, 60],
            iconAnchor: [30, 60],
         })

         if (this.config.warehouse_coordinate && this.config.warehouse_coordinate.length) {
            this.center = this.config.warehouse_coordinate
         }

         this.map = L.map('clientMapContiner', {
            center: this.coordinate.length ? this.coordinate : this.center,
            zoom: this.zoom
         });


         if (this.config && this.config.mapbox_access_token) {

            L.tileLayer(`https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=${this.config.mapbox_access_token}`, {
               attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
               maxZoom: 20,
               id: 'mapbox/streets-v11',
               tileSize: 512,
               zoomOffset: -1,
            }).addTo(this.map);

         } else {

            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
               attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

         }
         if (this.coordinate && this.coordinate.length) {

            this.setDestinationMarker(this.coordinate)
         }

         this.map.on('click', this.handleMapClicked);

      },
      setGeocoder() {

         L.Control.geocoder({
            defaultMarkGeocode: false,
            geocoder: L.Control.Geocoder.nominatim(),
            style: 'bar',

         }).on('markgeocode', (result) => {
            this.emitData(result.geocode.center)

         }).addTo(this.map);
      },
      handleMapClicked(e) {

         let container = L.DomUtil.create('div')

         let btn = L.DomUtil.create('button', '', container);
         btn.setAttribute('type', 'button');
         btn.innerHTML = 'Pilih lokasi disini';

         L.popup()
            .setContent(container)
            .setLatLng(e.latlng)
            .openOn(this.map);
         L.DomEvent.on(btn, 'click', () => {
            let coords = [e.latlng.lat, e.latlng.lng]
            this.emitData(coords)

         });
      },
      handleMArkerDragend(e) {

         let latlng = e.target._latlng
         let center = [latlng.lat, latlng.lng]

         this.emitData(center)

         this.map.closePopup();

         this.map.setView(center)

      },
      setDestinationMarker(center) {

         if (this.polyline) {
            this.map.removeLayer(this.polyline);
            this.polyline = null;
         }

         if (this.destinationMarker != undefined) {

            this.map.removeLayer(this.destinationMarker);
         };

         this.destinationMarker = L.marker(center, this.destinationMarkerOption);

         this.destinationMarker.addTo(this.map)

         this.map.closePopup();

         this.map.setView(center)

         this.loading = false

         this.destinationMarker.on('dragend', (e) => {
            this.handleMArkerDragend(e)
         })

         //draw a line between two points
         // this.polyline = L.polyline([this.warehouse_coordinate, center], {
         //    color: 'red'
         // });

         //add the line to the map
         // this.polyline.addTo(this.map);


      },
      emitData(center) {
         this.$emit('onEmitMap', center)
      },
      getCurrentLocation() {

         const successCallback = (position) => {

            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            let center = [lat, lng]
            this.emitData(center)

         }
         const errorCallback = (error) => {

            switch (error.code) {
               case error.PERMISSION_DENIED:
                  console.log("User denied the request for geolocation");
                  break;
               case error.POSITION_UNAVAILABLE:
                  console.log("Location information unavailable");
                  break;
               case error.TIMEOUT:
                  console.log("The request for get location timeout");
                  break;
               case error.UNKNOWN_ERROR:
                  console.log("An unknown error occurred");
                  break;


               default:
                  console.log('Unknown Error Geolocation');

                  break;
            }
         }

         const options = {
            enableHighAccuracy: true,
            timeout: 5000,

         }
         if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(successCallback, errorCallback, options)
         } else {
            console.log('Geolocation not supported by this browser');
         }

      },
   },
}
</script>

<style scoped>
#clientMapContiner {
   height: 350px;
   width: 100%;
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

/* .leaflet-top.leaflet-right {
   display: none;
} */
</style>