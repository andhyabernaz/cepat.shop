<template>
   <q-page padding class="relative" :class="{ 'flex flex-center': !ready }">
      <div class="main-content col relative overflow-x-hidden" v-if="ready && product">
         <MobileHeader :title="title" goBack></MobileHeader>
         <div class="product-container" :class="{ 'grid-1': !is_mode_desktop, 'grid-2': is_mode_desktop }">
            <div>

               <div class="relative bg-white q-pa-sm" v-if="product">
                  <div class="absolute-top-left z-10" v-if="isMobileWidth" style="left:15px;top:15px">
                     <q-btn icon="eva-arrow-back" dense unelevated @click="goBack" color="white" text-color="grey-7"></q-btn>

                  </div>
                  <Transition name="slide" tag="div" class="relative text-center">
                     <q-img :ratio="getImageRatio" :src="currentImage" :fit="getImageFit" class="cursor-pointer"
                        @click="clickImage">

                     </q-img>
                  </Transition>

                  <div class="product-image-footer row items-center justify-between">
                     <div v-if="product" class="asset-badge q-mr-sm">{{ slide }}/{{ product.assets.length }}</div>
                     <q-btn aria-label="Fullscreen" dense color="white" class="bordered" text-color="grey-8"
                        icon="ion-expand" unelevated size="13px" padding="8px" round
                        @click="fullscreen = !fullscreen" />
                  </div>

               </div>
            </div>

            <div>

               <q-card flat>
                  <q-card-section>
                     <div>
                        <h1 class="text-h6 text-weight-semibold" v-if="product">{{ product.title }}</h1>
                        <div class="flex items-center">
                           <div class="flex items-center text-secondary">
                              <span class="text-md">Rp</span>
                              <span class="text-lg text-weight-medium">
                                 {{ moneyFormat(parseInt(getCurrentPrice)) }}
                              </span>
                           </div>
                           <div class="flex items-center text-strike text-xs q-ml-xs" v-if="renderDiscount">
                              <span class="text-sm">Rp</span>
                              <span class="text-md">{{ moneyFormat(getDefaultPrice) }} </span>
                           </div>

                           <template v-if="renderMaxPrice">
                              <div class="q-px-sm text-md text-weight-bold">-</div>
                              <div class="flex items-center text-secondary">
                                 <span class="text-md">Rp</span>
                                 <span class="text-lg text-weight-medium">{{ moneyFormat(getMaxPrice) }} </span>
                              </div>
                           </template>

                        </div>

                        <div class="items-center justify-between row gutter-sm">
                           <div class="row items-center q-gutter-x-sm">
                              <q-rating data-nosnippet="true" v-model="productRating" readonly color="accent"
                                 icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half"
                                 size="1.3rem" />
                              <div class="text-weight-medium text-sm">
                                 {{ product.reviews_count > 0 ? product.reviews_count + ' ulasan' : '' }}</div>
                           </div>

                           <q-item-label class="text-nowrap text-grey-7"
                              v-if="config.display_product_sold && product.sold != 0">{{
                                 product.sold }}</q-item-label>

                        </div>

                     </div>

                  </q-card-section>
               </q-card>
               <q-card flat class="q-mt-sm">
                  <q-card-section class="bg-white" v-if="product && product_varian_item_render.length">
                     <div class="q-pb-sm">
                        <div class="flex justify-between items-center text-md text-weight-semibold">
                           <!-- <div class="">Pilih Varian</div> -->
                        </div>
                        <div class="q-mt-sm" v-if="product.varian_attributes.length">
                           <div class="q-mb-xs">{{ product.varian_attributes[0].label }}</div>
                           <div class="q-gutter-sm">
                              <q-btn aria-label="Product Attribute" class="product-varian--btn" outline
                                 v-for="attr in product.varian_attributes" :key="attr.id" :label="attr.value"
                                 :color="product_attribute_selected && product_attribute_selected.id == attr.id ? 'accent' : 'grey-9'"
                                 @click="selectVarianAttribute(attr)">
                                 <badge-tick
                                    v-if="product_attribute_selected && product_attribute_selected.id == attr.id" />
                              </q-btn>
                           </div>
                        </div>
                        <div class="q-mt-sm">
                           <div class="q-mb-xs">{{ product_varian_item_render[0].label }}</div>
                           <div class="q-gutter-sm">
                              <q-btn aria-label="Product Varian" class="product-varian--btn" outline
                                 v-for="item in product_varian_item_render" :key="item.id"
                                 :label="`${item.value} ${item.stock > 0 ? '' : 'Habis'}`"
                                 :color="product_varian_selected && product_varian_selected.id == item.id ? 'accent' : 'grey-9'"
                                 @click="selectVarianItem(item)" :disable="item.stock < 1">
                                 <badge-tick v-if="product_varian_selected && product_varian_selected.id == item.id" />
                              </q-btn>
                           </div>
                        </div>
                     </div>
                  </q-card-section>
               </q-card>
               <q-card flat v-if="!isMobileWidth">
                  <q-card-section class="q-gutter-y-sm q-mt-sm">
                     <q-btn class="full-width col btn-cart" icon="eva-shopping-bag" unelevated @click="buyNow" label="Beli Sekarang" color="primary"></q-btn>
                     <div class="flex q-gutter-x-sm">
                        <q-btn class="col btn-cart" unelevated outline @click="addNewItem"
                           icon="eva-shopping-cart-outline" label="Masukan Keranjang" color="primary"></q-btn>
                        <q-btn class="col btn-chat" unelevated outline @click="chatModal = true"
                           icon="eva-message-circle-outline" label="Chat Admin" color="primary"></q-btn>

                     </div>
                  </q-card-section>
               </q-card>
               <q-card class="box-shadow q-mt-sm bg-white q-pb-sm" v-if="product_links.length">
                  <q-card-section>
                     <div class="text-subtitle q-mb-sm">Beli Via Marketplace</div>
                     <div class="row q-gutter-sm items-center">
                        <a v-for="item in product_links" :href="item.product_url" :key="item.id" target="new"
                           class="mp_btn">
                           <img :src="item.icon" :alt="`Beli via ${item.provider}`">
                        </a>
                     </div>
                  </q-card-section>
               </q-card>
            </div>
         </div>


         <q-card class="box-shadow q-mt-md bg-white q-pb-xl">
            <q-tabs v-model="tab" align="left" indicator-color="accent">
               <q-tab name="Description" label="Deskripsi Produk"></q-tab>
               <q-tab name="Review" label="Ulasan Produk"></q-tab>
            </q-tabs>
            <q-separator />
            <q-tab-panels v-model="tab">
               <q-tab-panel name="Description">
                  <div id="description" class="q-mt-md" style="min-height:180px;">
                     <div class="" v-html="product.description"></div>
                  </div>
               </q-tab-panel>
               <q-tab-panel name="Review">
                  <div id="ulasan" class="q-mt-lg">
                     <div class="flex column justify-center items-center">
                        <div class="text-center" v-if="productRating > 0">
                           <div class="text-3xl text-weight-bold">{{ product.rating }}</div>
                           <q-rating data-nosnippet="true" readonly v-model="productRating" color="accent"
                              icon="ion-star-outline" icon-selected="ion-star" icon-half="ion-star-half" size="30px" />
                        </div>
                        <div class="text-weight-medium text-md q-my-sm">
                           {{ product.reviews_count > 0
                              ? 'Total ' + product.reviews_count + ' ulasan'
                              : 'Belum ada ulasan' }}
                        </div>
                        <q-btn v-if="config.public_review" outline color="accent" @click="handleReviewModal"
                           label="Berikan ulasan" class="q-my-xs"></q-btn>
                     </div>
                     <div class="q-pt-xl">
                        <ReviewBlock :reviews="AllProductReviews" />
                     </div>
                     <div class="q-my-md row justify-center">
                        <q-btn outline no-caps color="primary" :loading="loadMoreLoading"
                           v-if="AllProductReviews.length < product.reviews_count" label="loadmore..."
                           @click="loadReview">
                        </q-btn>
                     </div>
                  </div>
               </q-tab-panel>
            </q-tab-panels>
         </q-card>
         <RelatedProduct v-if="product_related.length" :products="product_related"></RelatedProduct>
      </div>
      <q-footer class="flex nowrap box-shadow-top" v-if="isMobileWidth">
         <div class="flex nowrap bg-white">
            <q-btn unelevated flat @click="chatModal = true" icon="eva-message-circle-outline" 
            :size="window_width > 500 ? '19px' : '16px'" 
            class="btn-chat"
               color="grey-7" outline></q-btn>
               <q-separator vertical inset color="grey-7"></q-separator>
            <q-btn unelevated flat @click="addNewItem" icon="eva-shopping-cart-outline" 
            :size="window_width > 500 ? '19px' : '16px'" 
            class="btn-cart"
               color="grey-7"></q-btn>
         </div>
            <q-btn unelevated square @click="buyNow" label="Beli Sekarang" color="primary" padding="14px 4px" no-caps
               class="col btn-checkout-now"></q-btn>
         </q-footer>
      <q-inner-loading :showing="!ready">

      </q-inner-loading>
      <q-dialog v-model="reviewModal" persistent>
         <div class="q-card" style="width:100%;max-width:360px;">
            <q-card-section>
               <form @submit.prevent="submitReview">
                  <div>
                     <div class="text-subtitle2 q-mb-sm">Berikan Ulasan Anda</div>
                     <q-rating data-nosnippet="true" v-model="form.rating" color="accent" icon="ion-star-outline"
                        icon-selected="ion-star" icon-half="ion-star-half" size="sm" />
                     <div class="q-my-md q-gutter-y-xs">
                        <q-input dense label="Nama Anda" v-model="form.name"
                           :rules="[val => val && val != '' || 'Wajib disisi']"></q-input>
                        <q-input dense label="Ulasan Anda" type="textarea" v-model="form.comment" rows="3"
                           :rules="[val => val && val != '' || 'Wajib disisi']"></q-input>
                     </div>
                     <div class="q-gutter-y-sm q-my-md items-center text-grey">
                        <div class="text-grey text-xs">Jawab tantangan berikut, hanya untuk memastikan anda bukan robot
                        </div>
                        <div class="row q-gutter-x-sm items-center">
                           <div class="text-weight-bold bg-dark text-white q-px-sm q-py-xs rounded">
                              {{ number2 }} + {{ number1 }}
                           </div>
                           <div class="text-weight-bold"> = </div>
                           <input class="rounded text-grey-9" type="text" ref="jawaban" v-model="jawaban"
                              style="width:60px;padding:3px 6px;border:1px solid grey">
                        </div>
                     </div>
                     <div class="row justify-end q-gutter-x-sm">
                        <q-btn unelevated type="button" @click.prevent="closeReviewModal" label="Batal" color="primary"
                           outline></q-btn>
                        <q-btn unelevated :disable="chalengeTesting" type="submit" :loading="loading"
                           label="Kirim Ulasan" color="primary"></q-btn>
                     </div>
                  </div>
               </form>
            </q-card-section>
         </div>
      </q-dialog>
      <q-dialog v-model="chatModal">
         <q-card style="max-width:450px;width:100%;" class="text-grey-9">
            <div class="text-weight-bold q-py-sm q-px-md bg-primary text-white">Kirim pesan / tanya ke penjual</div>
            <q-card-section class="transition-height">
               <div class="q-mb-sm text-subtitle2" v-if="this.product"># {{ product.title }}</div>
               <q-input outlined autogrow autofocus v-model="chatText"
                  placeholder="contoh: Halo Admin, Apakah ini masih ada?"></q-input>
               <div class="q-pt-sm">
                  <div class="q-pa-xs cursor-pointer" v-for="chat in defaultChat" :key="chat"
                     @click="changeChatText(chat)">
                     <span>{{ chat }}</span>
                  </div>
               </div>
            </q-card-section>
            <q-card-actions class="justify-end q-pa-md">
               <q-btn @click="closeChatModal" outline color="primary" label="Batal"></q-btn>
               <q-btn @click="submitChat" :disable="!chatText" color="primary" label="Kirim"></q-btn>
            </q-card-actions>
         </q-card>
      </q-dialog>
      <q-dialog v-model="cartModal" transition-show="slide-up" transition-hide="slide-down" 
      backdrop-filter="blur(4px)">
         <q-card flat class="bg-white cart-modal" v-if="product">
            <!-- <q-linear-progress size="10px" :value="100" /> -->
            <img :src="product.assets[0].src" />
            <q-card-section >
               <div class="text-md text-center text-weight-medium">
               Produk berhasil ditambahkan di keranjang belanja.
               </div>

               <div class="q-pt-sm q-gutter-y-sm">
                  <q-btn class="full-width" no-caps :to="{ name: 'Cart' }" icon="eva-shopping-bag" label="Lanjut Checkout"
                     color="primary"></q-btn>
                  <q-btn class="full-width" no-caps outline color="primary" @click="cartModal = false"   label="Berbelanja Lagi"></q-btn>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
      <q-dialog v-model="alreadyItemModal" persistent>
         <q-card class="card-lg">
            <q-linear-progress size="10px" :value="100" />
            <q-card-section v-if="product">
               <div class="q-mb-sm text-weight-medium text-lg">Produk sudah dikeranjang!</div>
               <div><span class="text-weight-medium text-md text-capitalize">{{ product.title }} </span>
                  {{ getVarianTextNote() }}
                  <div v-if="product.is_default_type">Sudah ada dikeranjang, Apakah ingin tetap menambahkan?, Jika
                     ditambahkan, keranjang akan
                     diperbarui kuantitasnya </div>
                  <div v-else>Sudah ada dikeranjang</div>
               </div>
            </q-card-section>
            <q-card-actions class="justify-end q-gutter-x-sm q-pa-md">
               <q-btn outline no-caps @click="alreadyItemModal = false" label="Tutup" color="primary"></q-btn>
               <q-btn v-if="product.is_default_type" unelevated no-caps @click="updateNewItem" label="Tambahkan"
                  color="primary"></q-btn>
            </q-card-actions>
         </q-card>
      </q-dialog>
      <q-dialog v-model="formCartModal" position="bottom" transition-show="slide-up" transition-hide="slide-down">
         <q-card class="dialog-container-bottom z-max" flat v-if="product">
            <q-card-section class="q-py-sm relative">
               <div class="absolute-top-right q-pa-xs">
                  <q-btn flat icon="close" dense round v-close-popup></q-btn>
               </div>
               <div class="q-pb-sm" style="min-height:100px;">
                  <q-list>
                     <q-item class="q-px-xs">
                        <q-item-section avatar top>
                           <q-img :src="product.assets[0].src" width="80px" class="img-thumbnail" ratio="1"></q-img>
                        </q-item-section>
                        <q-item-section top class=q-pr-lg>
                           <div class="text-md text-weight-meduim q-mb-sm">{{ product.title }}</div>
                           <div class="flex items-center">
                              <div class="flex items-center text-secondary">
                                 <span class="text-md">Rp</span>
                                 <span class="text-md text-weight-medium">
                                    {{ moneyFormat(parseInt(getCurrentPrice)) }}
                                 </span>
                              </div>
                              <div class="flex items-center text-strike text-xs q-ml-xs" v-if="renderDiscount">
                                 <span class="text-sm">Rp</span>
                                 <span class="text-md">{{ moneyFormat(getDefaultPrice) }} </span>
                              </div>

                              <template v-if="renderMaxPrice">
                                 <div class="q-px-sm text-md text-weight-bold">-</div>
                                 <div class="flex items-center text-secondary">
                                    <span class="text-md">Rp</span>
                                    <span class="text-md text-weight-medium">{{ moneyFormat(getMaxPrice) }} </span>
                                 </div>
                              </template>

                           </div>
                        </q-item-section>
                     </q-item>
                  </q-list>
                  <div v-if="has_varian" class="q-mt-sm">
                     <!-- <div class="text-md">Pilih Varian</div> -->
                     <div class="q-mt-sm" v-if="product.varian_attributes.length">
                        <div class="q-mb-xs">{{ product.varian_attributes[0].label }}</div>
                        <div class="q-gutter-sm">
                           <q-btn aria-label="Product Attribute" class="product-varian--btn" outline
                              v-for="attr in product.varian_attributes" :key="attr.id" :label="attr.value"
                              :color="product_attribute_selected && product_attribute_selected.id == attr.id ? 'accent' : 'grey-9'"
                              @click="selectVarianAttribute(attr)">
                              <badge-tick
                                 v-if="product_attribute_selected && product_attribute_selected.id == attr.id" />
                           </q-btn>
                        </div>
                     </div>
                     <div class="q-mt-sm">
                        <div class="q-mb-xs">{{ product_varian_item_render[0].label }}</div>
                        <div class="q-gutter-sm">

                           <q-btn aria-label="Product Varian" class="product-varian--btn" outline
                              v-for="item in product_varian_item_render" :key="item.id"
                              :color="product_varian_selected && product_varian_selected.id == item.id ? 'accent' : 'grey-9'"
                              @click="selectVarianItem(item)" :label="`${item.value} ${item.stock > 0 ? '' : 'Habis'}`"
                              :disable="item.stock < 1">
                              <badge-tick v-if="product_varian_selected && product_varian_selected.id == item.id" />
                           </q-btn>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="flex justify-between items-center q-mt-md" v-if="product.is_default_type">
                  <div class="text-md">Quantity</div>
                  <div class="flex no-wrap q-gutter-x-sm">
                     <q-btn :disable="!checkVarianIsReady()" aria-label="decrement" unelevated icon="eva-minus-outline"
                        dense color="grey-3" text-color="dark" @click="decrementQty" style="cursor:pointer;"></q-btn>
                     <input :disabled="!checkVarianIsReady()" type="number" class="text-center" outlined dense
                        style="width:70px;" :value="quantity" @input="e => setQty(e)" />
                     <q-btn :disable="!checkVarianIsReady()" aria-label="increment" unelevated icon="eva-plus-outline"
                        dense color="grey-3" text-color="dark" @click="incrementQty" style="cursor:pointer;"></q-btn>
                  </div>
               </div>

               <div style="min-height:21px" class="text-weight-medium">

                  <div v-if="checkVarianIsReady()" :class="stockAvailabilityClass">{{ stockAvailabilityText }}</div>
               </div>

            </q-card-section>
            <q-card-section class="sticky-bottom bg-white q-pb-lg">
               <q-btn v-if="nextToCart" @click="buyNow" name="eva-shopping-cart" label="Beli Sekarang" color="primary"
                  class="full-width"></q-btn>
               <q-btn v-else @click="addNewItem" name="eva-shopping-cart" label="Tambahkan Keranjang" color="primary"
                  class="full-width"></q-btn>
            </q-card-section>
         </q-card>
      </q-dialog>
      <q-dialog v-model="fullscreen" persistent maximized>
         <div class="relative" v-if="product" style="background:rgb(120 120 120 / 96%);">
            <div class="text-center q-py-md absolute" style="top:5px;width:100%;z-index:99;">
               <div class="flex justify-center" v-show="helpTextScreen">
                  <div class="q-px-md" style="background:rgb(240 240 240 / 90%);">Scroll mouse atau cubit layar untuk
                     zoom
                  </div>
               </div>
            </div>
            <Transition name="slide" tag="div" class="preview-image relative bg-grey-5 text-center">
               <PinchScrollZoom centered ref="zoomer" :width="zoomerWidth" :height="zoomerHeight"
                  :contentWidth="zoomerWidth" :contentHeight="zoomerHeight" :scale="scale">
                  <img ref="zoomerimage" :src="currentImage" />
               </PinchScrollZoom>
            </Transition>
            <div class="absolute row items-center q-py-md q-px-lg justify-between fuul-width"
               style="bottom:0;left:0;right:0">
               <div class="asset-badge">{{ slide }}/{{ product.assets.length }}</div>
               <div class="row items-center">

                  <template v-if="product.assets.length > 1">
                     <q-btn :disable="slide == 1" dense size="16px" color="white" text-color="dark" round unelevated
                        icon="eva-arrow-ios-back" @click="slide--" class="q-mr-sm bordered" />
                     <q-btn :disable="product.assets.length == slide" dense size="16px" color="white" text-color="dark"
                        round unelevated icon="eva-arrow-ios-forward" class="q-mr-sm bordered" @click="slide++" />
                  </template>

                  <q-btn dense size="16px" color="white" text-color="dark" round unelevated icon="ion-refresh"
                     @click="resetZoom" class="q-mr-sm bordered" />
                  <q-btn dense size="16px" color="white" text-color="dark" class="bordered" round unelevated
                     icon="eva-close" @click="fullscreen = !fullscreen" />
               </div>
            </div>
         </div>
      </q-dialog>
   </q-page>
</template>

<script>
import { mapActions } from 'vuex'
import BadgeTick from 'components/BadgeTick.vue'
import PinchScrollZoom from "@coddicat/vue-pinch-scroll-zoom";
import ReviewBlock from './ReviewBlock.vue';
import { Api } from 'src/boot/axios';
import RelatedProduct from 'src/components/RelatedProduct.vue';
import { Cookies } from "quasar";
export default {
   name: 'ProductShow',
   components: {
      BadgeTick,
      PinchScrollZoom,
      ReviewBlock,
      RelatedProduct
   },
   data() {
      return {
         scale: 1,
         tab: 'Description',
         defaultChat: ['Apakah ini masih ada?', 'Apakah bisa grosir?'],
         chatText: '',
         chatModal: false,
         reviewModal: false,
         timeInterval: null,
         number1: 0,
         number2: 0,
         jawaban: '',
         loading: false,
         slide: 1,
         quantity: 1,
         discount: 0,
         fullscreen: false,
         helpTextScreen: true,
         shop: this.$store.state.shop,
         ready: false,
         loadMoreLoading: false,
         form: {
            product_id: null,
            product_name: '',
            name: '',
            comment: '',
            rating: 0
         },
         cartModal: false,
         alreadyItemModal: false,
         formCartModal: false,
         product: null,
         productReviews: [],
         imagePreviewIndex: 0,
         unapproved_review: JSON.parse(localStorage.getItem('unapproved_review')) || null,
         has_varian: false,
         product_attribute_selected: null,
         product_varian_selected: null,
         product_links: [],
         product_related: [],
         autoplayImage: true,
         autoplayCounter: null,
         nextToCart: false
      }
   },
   watch: {
      tab: function (val, oldval) {
         if (val != oldval
            && val == 'Review'
            && !this.productReviews.length
            && this.product.reviews_count > 0) {
            this.getReview()
         }
      },
      fullscreen(val) {
         if (val == true) {
            if (localStorage.getItem('helpTextScreen')) {
               this.helpTextScreen = false
            } else {
               this.helpTextScreen = true
               setTimeout(() => {
                  this.helpTextScreen = false
                  localStorage.setItem('helpTextScreen', 1)
               }, 5000)
            }

         }
      },
      '$route.params.slug': {
         immediate: true,
         handler(val) {
            this.ready = false
            this.getProduct()
         }
      },
   },
   computed: {
      product_varian_item_render() {
         if (this.product_attribute_selected) {
            return this.product.varian_items.filter(v => v.varian_id == this.product_attribute_selected.id)
         }
         return this.product.varian_items
      },
      zoomImageStyle() {
         return `width:100%;height:100%;object-fit:contain;padding:4%;max-width:800px;max-height:${this.zoomerHeight}`
      },
      currentImage() {
         return this.product.assets[this.slide - 1].src
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
      window_width() {
         return this.$store.state.window_width
      },
      isMobileWidth() {
         return this.$store.getters['isMobileWidth']
      },
      config() {
         return this.$store.state.config
      },
      getImageRatio() {
         if (this.config && this.config.product_img_ratio) {
            return this.getRatio(this.config.product_img_ratio)
         }
         return '1'
      },
      getImageFit() {
         if (this.config && this.config.product_img_fit) {
            return this.config.product_img_fit
         }
         return 'cover'
      },
      title() {
         if (this.product) {
            return this.product.title
         }
         return 'Produk'
      },
      zoomerHeight() {
         return window.innerHeight
      },
      page_width() {
         return this.$store.state.page_width
      },
      zoomerWidth() {
         return window.innerWidth
         // return this.page_width
         if (this.page_width > 768) {
            return 768;
         }
         return this.page_width
      },
      renderMaxPrice() {
         if (this.has_varian && !this.product_varian_selected) {
            if (parseInt(this.getCurrentPrice) < parseInt(this.getMaxPrice)) {
               return true
            }
         }
         return false
      },
      renderDiscount() {
         if (this.product.pricing.is_discount) {
            if (!this.renderMaxPrice) {
               return true
            }
         }
         return false
      },
      AllProductReviews() {
         if (this.unapproved_review) {
            return [this.unapproved_review, ...this.productReviews]
         }
         return this.productReviews
      },
      session_id() {
         return this.$store.state.session_id
      },
      chalengeTesting() {
         return this.number1 + this.number2 != this.jawaban
      },
      productRating() {
         return parseFloat(this.product.rating)
      },
      carts() {
         return this.$store.state.cart.carts
      },
      carouselWidth() {
         if (this.$refs.carousel) {
            return this.$refs.carousel.innerHeight
         }
         return this.page_width
      },
      cStyle() {
         if (!this.fullscreen && this.$q.screen.width < 560 && this.$q.screen.width > 200) {
            return 'height:' + this.carouselWidth + 'px'
         }
         return ''
      },
      height() {
         return this.$q.screen.width + 'px'
      },
      productStock() {
         if (this.has_varian && this.product_varian_selected) {
            return parseInt(this.product_varian_selected.stock)
         }

         if (this.product.is_unlimited_stock) {
            return null
         }

         return parseInt(this.product.stock)
      },
      isCurrentStockUnlimited() {
         return !this.has_varian && this.product.is_unlimited_stock
      },
      currentStock() {
         if (this.isCurrentStockUnlimited) {
            return 9999999
         }
         let stock = parseInt(this.productStock)

         let hasCart = this.carts.find(el => el.sku == this.currentProductSku)

         if (this.has_varian && this.product_varian_selected) {

            stock = parseInt(this.product_varian_selected.stock)

         }

         return hasCart != undefined ? stock - parseInt(hasCart.quantity) : stock

      },
      stockAvailabilityClass() {
         return {
            'text-green': this.isCurrentStockUnlimited || this.currentStock >= 5,
            'text-amber-10': !this.isCurrentStockUnlimited && this.currentStock < 5 && this.currentStock > 0,
            'text-red': !this.isCurrentStockUnlimited && this.currentStock < 1
         }
      },
      stockAvailabilityText() {
         if (this.isCurrentStockUnlimited) {
            return 'Stok Unlimited'
         }

         return this.currentStock > 0 ? `Tersedia ${this.currentStock} item` : 'Stok Habis'
      },
      currentProductSku() {
         if (this.has_varian && this.product_varian_selected) {
            return this.product_varian_selected.sku
         }
         return this.product.sku ? this.product.sku : this.product.id
      },
      getDiscountAmount() {
         if (this.product.pricing.is_discount) {
            if (this.product.pricing.discount_type == 'PERCENT') {
               return (parseInt(this.getDefaultPrice) * parseInt(this.product.pricing.discount_amount)) / 100
            } else {
               return parseInt(this.product.pricing.discount_amount)
            }
         }
         return 0
      },
      getMaxPrice() {
         if (this.has_varian) {

            let maxPrice = parseInt(this.product.pricing.max_price);

            if (this.product_attribute_selected) {

               maxPrice = parseInt(this.product_varian_item_render[this.product_varian_item_render.length - 1].price)

            }

            let discount = this.getcurrentDiscount(maxPrice);

            return maxPrice - discount
         }
         return 0
      },
      getDefaultPrice() {
         if (this.product_varian_selected) {
            return parseInt(this.product_varian_selected.price)
         }

         if (this.product_attribute_selected) {
            return parseInt(this.product_varian_item_render[0].price)
         }

         return parseInt(this.product.pricing.default_price)
      },
      getCurrentPrice() {
         return this.getDefaultPrice - this.getDiscountAmount
      },
      getCurrentWeight() {
         if (this.product_varian_selected) {
            return parseInt(this.product_varian_selected.weight)
         }

         return parseInt(this.product.weight)
      },

   },
   methods: {
      ...mapActions('product', ['productDetail', 'loadProductReview', 'addProductReview']),
      resetZoom() {
         this.$refs.zoomer.setData({
            scale: 1,
            originX: 0,
            originY: 0,
            translateX: 0,
            translateY: 0
         });
      },
      goBack() {
         if(window.history.length > 2) {
            this.$router.back()
         }else {
            this.$router.replace({name: 'ProductIndex'})
         }
      },
      getAffiliateCode() {
         let key = "__aff_" + this.product.id;

         if (Cookies.has(key)) {
            let code = Cookies.get(key);
            return code.split("_")[1];
         }
         return "";
      },
      clickImage() {
         if (this.slide < this.product.assets.length) {
            this.slide++
         } else {
            this.slide = 1
         }
      },
      getcurrentDiscount(price) {
         if (this.product.pricing.is_discount) {
            if (this.product.pricing.discount_type == 'PERCENT') {
               return (parseInt(price) * parseInt(this.product.pricing.discount_amount)) / 100
            } else {
               return parseInt(this.product.pricing.discount_amount)
            }
         }
         return 0;
      },
      selectVarianAttribute(item) {
         this.product_attribute_selected = item
         this.product_varian_selected = null
         this.quantity = 1
      },
      selectVarianItem(item) {
         this.product_varian_selected = item
         this.quantity = 1
      },
      backButton() {
         if (window.history.length > 2) {
            this.$router.back()
         } else {
            this.$router.replace({ name: 'ProductIndex' })
         }
      },
      addToCart() {
         this.formCartModal = false

         let cartItem = {
            session_id: this.session_id,
            product_id: this.product.id,
            product_stock: this.currentStock,
            sku: this.currentProductSku,
            name: this.product.title,
            price: this.getCurrentPrice,
            quantity: this.quantity,
            note: this.getVarianTextNote(),
            product_url: this.getRoutePath(),
            image_url: this.product.assets[0].src,
            weight: this.getCurrentWeight,
            product_type: this.product.product_type,
            affiliate_code: this.getAffiliateCode(),
         }

         this.$store.dispatch('cart/addToCart', cartItem)

         this.quantity = 1
      },
      showNotifyHasSelectVarian() {
         if (this.formCartModal) {
            this.$q.notify({
               type: 'info',
               message: 'Silahkan pilih produk varian terlebih dahulu',
            })
         } else {
            this.formCartModal = true
         }
      },
      buyNow() {
         this.quantity = 1
         this.nextToCart = true
         
         this.processItem()
         
         
      },
      addNewItem() {
         this.nextToCart = false
         this.processItem()
      },
      processItem() {
         if (!this.formCartModal) {
            this.formCartModal = true
            return
         }
         if (this.has_varian && !this.product_varian_selected) {
            this.$q.notify({
               type: 'info',
               message: 'Silahkan pilih produk varian terlebih dahulu',
            })
            return
         }

         if (this.currentStock <= 0) {
            let item = this.has_varian ? 'varian' : 'produk'
            this.$q.dialog({
               title: 'Stok habis',
               message: `Maaf, stok untuk ${item} ini habis, silahkan pilih ${item} lainnya.`
            })

            return
         }

         this.addToCart()

         if(this.nextToCart) {
            this.$router.push({ name: 'Cart' })
         }else {

            this.cartModal = true
         }
      },
      updateNewItem() {
         this.alreadyItemModal = false
         this.addToCart()
         this.cartModal = true
      },
      checkCart() {
         return new Promise((resolve, reject) => {
            let cartAlready;

            cartAlready = this.carts.find(el => el.sku == this.currentProductSku)

            if (cartAlready != undefined) {

               reject('Product sudah dikeranjang')

            } else {
               resolve('add new Item')
            }

         })
      },
      getVarianTextNote() {
         let str = ''
         if (this.product_varian_selected) {
            if (this.product_attribute_selected) {
               str += `${this.product_attribute_selected.label} ${this.product_attribute_selected.value}, `
            }

            str += `${this.product_varian_selected.label} ${this.product_varian_selected.value}`
         }
         return str
      },
      getRoutePath() {
         let props = this.$router.resolve({
            name: 'ProductShow',
            params: { slug: this.product.slug },
         });

         return location.origin + props.href;
      },
      checkVarianIsReady() {
         if (this.has_varian && !this.product_varian_selected) {
            return false
         }
         return true
      },
      incrementQty() {
         if (!this.checkVarianIsReady()) {
            this.$q.dialog({
               title: 'Pilih Varian!',
               message: 'Silahkan pilih varian untuk melanjutkan.'
            })
            return
         }

         if (this.quantity < this.currentStock) {
            this.quantity += 1
         } else {
            this.$q.dialog({
               title: 'Warning!',
               message: 'Stok tidak cukup, stok tersisa ' + this.currentStock + ' item.'
            })
         }

      },
      decrementQty() {
         if (!this.checkVarianIsReady) {
            this.$q.dialog({
               title: 'Pilih Varian!',
               message: 'Silahkan pilih varian untuk melanjutkan.'
            })
            return
         }

         if (this.quantity > 1) {
            this.quantity -= 1
         }
      },
      setQty(el) {
         let val = el.target.value
         if (!val) {
            this.quantity = 1
            return
         }
         let qty = parseInt(val);

         if (qty < 1) {
            this.quantity = 1
            return
         }

         if (!this.checkVarianIsReady()) {
            this.$q.dialog({
               title: 'Pilih Varian!',
               message: 'Silahkan pilih varian untuk melanjutkan.'
            })
            return
         }

         if (qty > this.currentStock) {
            this.quantity = this.currentStock
            this.$q.dialog({
               title: 'Warning!',
               message: 'Stok tidak cukup, stok tersisa ' + this.currentStock + ' item.'
            })
         }
      },
      getTeaser(html) {
         if (html) {
            let strippedString = html.replace(/(<([^>]+)>)/gi, "");
            return strippedString.substr(0, 80)
         } else {
            return ''
         }
      },
      getProductRelated() {
         this.product_related = []
         Api.get(`product-related/${this.product.id}`).then(res => {
            this.product_related = res.data.data
         })
      },
      closeReviewModal() {
         clearInterval(this.timeInterval)
         this.reviewModal = false
      },
      handleReviewModal() {
         this.getRandomNumber()

         this.timeInterval = setInterval(() => {
            if (document.activeElement !== this.$refs.jawaban) {
               this.getRandomNumber()
            }
         }, 10000)
         this.reviewModal = true
      },
      submitReview() {
         if (this.number1 + this.number2 != this.jawaban) {
            this.$q.notify({
               type: 'negative',
               message: 'Jawaban Salah, silahkan jawab dengan benar.'
            })
            this.getRandomNumber()
            return
         }
         this.jawaban = ''
         this.getRandomNumber()
         this.form.product_id = this.product.id
         this.form.product_slug = this.product.slug
         this.form.product_name = this.product.title
         if (this.form.name && this.form.comment && this.form.rating) {
            this.loading = true
            this.reviewModal = false
            this.addProductReview(this.form).then((res) => {
               let dataReview = res.data.data
               if (res.status == 200) {
                  this.$q.notify({
                     type: 'positive',
                     message: res.data.message
                  })
               }
               if (!dataReview.is_approved) {
                  localStorage.setItem('unapproved_review', JSON.stringify(dataReview))
                  this.unapproved_review = dataReview

                  setTimeout(() => {
                     localStorage.removeItem('unapproved_review');
                  }, 30000)
               }
               this.productDetail(this.$route.params.slug).then(response => {
                  this.product = response.data.data
                  this.getReview()
                  this.ready = true

                  this.has_varian = resultData.varian_items.length > 0

                  if (this.has_varian && resultData.varian_attributes.length) {
                     this.product_attribute_selected = resultData.varian_attributes[0];
                  }
               })
            })
            this.resetForm()
            this.loading = false
         } else {
            this.$q.notify({
               type: 'warning',
               message: 'Semua field harus di isi'
            })
         }
      },
      resetForm() {
         this.form.name = ''
         this.form.comment = ''
      },
      getProductLinks(productId) {
         Api.get(`productLinks/${productId}`).then(res => {
            this.product_links = res.data.data
         })
      },
      getReview() {
         this.loadMoreLoading = true
         this.loadProductReview({ product_id: this.product.id }).then(response => {
            if (response.status == 200) {
               this.loadMoreLoading = false
               this.productReviews = response.data.data
            }
         }).catch(err => {
            this.loadMoreLoading = false
         })
      },
      loadReview() {
         this.loadMoreLoading = true
         this.loadProductReview({ product_id: this.product.id, skip: this.productReviews.length }).then(response => {
            if (response.status == 200) {
               this.loadMoreLoading = false
               this.productReviews = [... this.productReviews, ...response.data.data]
            }
         }).catch(err => {
            this.loadMoreLoading = false
         })
      },
      getProduct() {
         this.product = null
         this.productDetail(this.$route.params.slug).then(response => {
            if (response.status == 200) {
               let resultData = response.data.data
               this.product = resultData
               this.ready = true

               this.has_varian = resultData.varian_items.length > 0

               if (this.has_varian && resultData.varian_attributes.length) {
                  this.product_attribute_selected = resultData.varian_attributes[0];
               }
               this.getProductLinks(this.product.id)
               this.getProductRelated()
               // this.autoplayImageCarousel()

            } else {
               this.$router.push({ name: 'ProductIndex' })
            }
         }).catch(() => {
            this.$router.push({ name: 'ProductIndex' })
         })
      },
      autoplayImageCarousel() {
         this.autoplayCounter = setInterval(() => {
            this.clickImage()
         }, 5000)
      },
      getRandomNumber() {
         let number = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
         this.number1 = Math.floor((Math.random() * number.length));
         this.number2 = Math.ceil((Math.random() * number.length));
      },
      formatPhoneNumber(number) {
         let formatted = number.replace(/\D/g, '')

         if (formatted.startsWith('0')) {
            formatted = '62' + formatted.substr(1)
         }

         return formatted;
      },
      closeChatModal() {
         this.chatText = ''
         this.chatModal = false
      },
      changeChatText(chat) {
         this.chatText = chat
      },
      submitChat() {
         let shopPhone = this.shop.phone
         if (!shopPhone) {
            this.$q.dialog({
               title: 'Maaf, Sedang masalah!',
               message: 'Silahkan coba kembali beberapa saat lagi, jika masih berlanjut silahkan hubungi kami.',
            })
            return
         }

         let str = `${this.chatText}\nProduk:  ${this.product.title}\n${location.href}`

         let link = `${this.currentWhatsappUrl}/send?phone=${this.formatPhoneNumber(shopPhone)}&text=${encodeURI(str)}`;
         window.open(link, '_blank');

         setTimeout(() => {
            this.closeChatModal()
         }, 2000)

      },

   },
   mounted() {
      this.$store.dispatch('generateSessionId');
   },
   created() {
      this.getRandomNumber()
   }
}
</script>

<style lang="scss">
.header-top {
   z-index: 50;
   position: fixed;
   top: 0;
   left: 0;
   right: 0;
   width: 100%;
   background: transparent;
   background: linear-gradient(0deg, rgba(2, 0, 36, 0) 0%, rgba(0, 0, 5, 0.678) 100%);
   color: #fff;
}

.q-body--fullscreen-mixin .img-carousel::after {
   height: 0;
}

.product-detail::before {
   position: absolute;
   width: 100%;
   background-color: white;
   content: "";
   height: 20px;
   border-radius: 80px 80px 0 0;
   top: -20px;
   left: 0;

   .q-carousel__control.absolute.absolute-bottom-right {
      transform: translateY(-20px)
   }

   .q-carousel__navigation--bottom {
      transform: translateY(-20px)
   }
}

.slide-enter-active,
.slide-leave-active {
   transition: all 0.5s ease;
}

.slide-enter-to,
.slide-leave-from,
.slide-enter-from,
.slide-leave-to {
   opacity: 0;
}

.preview-image {
   position: relative;
   overflow: hidden;
   display: grid;
   // flex-direction: column;
   // justify-content: center;
   align-items: center;

   img {
      width: 100%;
      max-width: 800px;
      padding: 4%;
      height: auto;
      max-height: 100vh;
      object-fit: contain;
      margin: auto;
   }
}
</style>
