import { store } from 'quasar/wrappers'
import { createStore } from 'vuex'

import createPersistedState from "vuex-persistedstate";

import SecureLS from "secure-ls";
var ls = new SecureLS({ isCompression: false });

const stateData = createPersistedState({
   key: '_cepatshop__state',
   paths: ['user.user', 'user.token', 'user.permissions' ,'cart', 'directCheckout', 'shop', 'config', 'session_id', 'product.favorites', 'forgot_password', 'affiliate_config'],
   storage: {
      getItem: (key) => ls.get(key),
      setItem: (key, value) => ls.set(key, value, { expires: 1 }),
      removeItem: (key) => ls.remove(key),
   }
})

import mutations from './mutations'
import actions from './actions'
import * as getters from './getters'


import user from './user'
import product from './product'
import category from './categories'
import slider from './slider'
import post from './post'
import block from './block'
import order from './order'
import bank from './bank'
import cart from './cart'
import directCheckout from './directCheckout'
import promo from './promo'
import front from './front';
import affiliate from './affiliate';

// import example from './module-example'

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */


export default store(function (/* { ssrContext } */) {
   const Store = createStore({
      state: {
         themes: [
            { label: 'Default', value: 'default' },
            { label: 'Simple', value: 'romance' },
            { label: 'Elegan', value: 'elegant' },
         ],
         errors: {},
         loading: false,
         shop: {},
         config: {},
         deferredPrompt: null,
         isMenuCategory: false,
         session_id: null,
         page_width: window.innerWidth,
         window_width: window.innerWidth,
         initial_data: false,
         has_validation_token: false,
         drawer: true,
         is_mini: false,
         can_install: false,
         affiliate_config: null,
         assets: {
            data: [],
            total: 0,
         },
         forgot_password: {
            token: '',
            email: '',
            hide_email: 'anda'
         },
         meta: {
            title: '',
            description: 'Simple Web Commerce checkout whatsapp'
         },
         customer_menus: [
            {
               label: "Dashboard",
               caption: "Dashboard Admin",
               path: "CustomerDashboard",
               icon: "dashboard",
               color: "green",
               active: true,
               group: ''
            },
            {
               label: "Pesanan",
               caption: "Pesanan",
               path: "CustomerOrder",
               icon: "eva-shopping-bag",
               color: "green",
               active: true,
               group: ''
            },

            {
               label: "Lisensi Saya",
               caption: "Lisensi Produk",
               path: "CustomerLicense",
               icon: "bookmark",
               color: "green",
               group: 'product_digital',
               active: true
            },
            {
               label: "Mutasi Saldo",
               caption: "Riwayat Mutasi Saldo",
               path: "CustomerMutasiSaldo",
               icon: "assignment",
               color: "green",
               group: 'saldo_balance',
               active: true
            },
            {
               label: "Affiliasi",
               caption: "Program Affiliasi",
               path: "CustomerAffiliate",
               icon: "repeat",
               color: "green",
               group: 'affiliate',
               active: false
            },
            {
               label: "Withdrawal",
               caption: "Riwayat Penarikan Dana",
               path: "CustomerWithdrawal",
               icon: "add_card",
               color: "green",
               group: 'affiliate',
               active: false
            },

            {
               label: "Alamat",
               caption: "Alamat Pengiriman",
               path: "CustomerAddress",
               icon: "contact_mail",
               color: "green",
               group: '',
               active: true
            },
            {
               label: "Ulasan",
               caption: "Ulasan Produk",
               path: "CustomerReviews",
               icon: "message",
               color: "green",
               group: '',
               active: true
            },
            {
               label: "Akun",
               caption: "Detil Akun",
               path: "CustomerAccountEdit",
               icon: "manage_accounts",
               color: "green",
               group: '',
               active: true
            },
         ],
         admin_menus: [
            {
               label: "Dashboard",
               caption: "Dashboard Admin",
               path: "AdminDashboard",
               icon: "dashboard",
               color: "green",
               active: true,
               ability: 'all'
            },
            // { label: 'Akun', caption: 'Pengaturan Akun', path: 'Account', icon: 'person', color: 'green' },
            {
               label: "Pesanan",
               caption: "Kelola pesanan",
               path: "OrderIndex",
               icon: "receipt",
               color: "green",
               active: true,
               ability: 'view-order'
            },
            {
               label: "Produk",
               caption: "Tambah, edit dan hapus produk",
               path: "AdminProductIndex",
               icon: "inventory_2",
               color: "deep-orange",
               active: true,
               ability: 'view-product'
            },
            {
               label: "Promo",
               caption: "Kelola produk promo",
               path: "PromoIndex",
               icon: "local_offer",
               color: "blue-7",
               active: true,
               ability: 'view-promo'
            },
            {
               label: "Voucher",
               caption: "Kelola voucher belanja",
               path: "VoucherIndex",
               icon: "loyalty",
               color: "blue-7",
               active: true,
               ability: 'view-voucher'
            },
            {
               label: "Kategori",
               caption: "Kelola kategori produk",
               path: "CategoryIndex",
               icon: "category",
               color: "amber-7",
               active: true,
               ability: 'view-category'
            },
            {
               label: "Mutasi Saldo",
               caption: "Kelola Mutasi Saldo",
               path: "MutasiSaldo",
               icon: "payments",
               color: "amber-7",
               active: true,
               ability: 'view-mutasi-saldo'
            },
            {
               label: "Withdrawal",
               caption: "Kelola Penarikan Dana",
               path: "WithdrawalIndex",
               icon: "add_card",
               color: "amber-7",
               active: true,
               ability: 'view-withdrawal'
            },
            {
               label: "Affiliate",
               caption: "Kelola Affiliasi",
               path: "AffiliatesIndex",
               icon: "diversity_3",
               color: "amber-7",
               active: true,
               ability: 'view-affiliate'
            },
            {
               label: "Toko",
               caption: "Pengaturan toko dan aplikasi",
               path: "Shop",
               icon: "store",
               color: "blue",
               active: true,
               ability: 'view-store'
            },
            {
               label: "Slider",
               caption: "Kelola slideshow",
               path: "Slider",
               icon: "view_carousel",
               color: "teal",
               active: true,
               ability: 'view-content'
            },
            {
               label: "Block Banner",
               caption: "Kelola banner",
               path: "AdminBlockIndex",
               icon: "space_dashboard",
               color: "amber-7",
               active: true,
               ability: 'view-content'
            },
            {
               label: "Artikel",
               caption: "Kelola Artikel / blog",
               path: "AdminPostIndex",
               icon: "article",
               color: "deep-orange",
               active: true,
               ability: 'view-content'
            },
            {
               label: "Akun Pembayaran",
               caption: "Kelola Akun Pembayaran",
               path: "BankIndex",
               icon: "account_balance",
               color: "purple",
               active: true,
               ability: 'view-payment-account'
            },
            {
               label: "Users & Permissions",
               caption: "List Pengguna & Akses",
               path: "UserList",
               icon: "group",
               color: "teal",
               active: true,
               ability: 'view-user'
            },
            {
               label: "Ulasan Produk",
               caption: "Moderasi Ulasan Produk",
               path: "ReviewsIndex",
               icon: "chat",
               color: "purple",
               active: true,
               ability: 'view-review'
            },
            {
               label: "Notifikasi",
               caption: "Konfigurasi Notifikasi",
               path: "NotificationTemplate",
               icon: "notifications",
               color: "blue",
               active: true,
               ability: 'view-notification'
            },
            {
               label: "File Media",
               caption: "Image Library",
               path: "MediaIndex",
               icon: "photo_library",
               color: "blue",
               active: true,
               ability: 'view-media'
            },
            {
               label: "Pengaturan",
               caption: "Pengaturan Website dan pengiriman",
               path: "Config",
               icon: "settings",
               color: "blue",
               active: true,
               ability: 'view-config'
            },
         ],
      },
      actions,
      mutations,
      getters,
      modules: {
         user,
         product,
         category,
         slider,
         post,
         block,
         order,
         bank,
         cart,
         directCheckout,
         promo,
         front,
         affiliate
      },
      plugins: [stateData],

      // enable strict mode (adds overhead!)
      // for dev mode and --debug builds only
      strict: process.env.DEBUGGING
   })

   return Store
})
