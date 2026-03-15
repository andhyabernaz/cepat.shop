import { AddressbarColor, setCssVar } from 'quasar'

export default {
   SET_ERRORS: (state, payload) => {
      state.errors = payload
   },
   PUSH_ERRORS: (state, payload) => {
      state.errors[payload.key] = payload.value
   },
   CLEAR_ERRORS: (state) => {
      state.errors = {}
   },
   SET_FORGOT_PASSWORD: (state, payload) => {
      state.forgot_password[payload.key] = payload.value
   },
   SET_LOADING: (state, payload) => {
      state.loading = payload
   },
   SET_AFFILIATE_CONFIG: (state, payload) => {
      state.affiliate_config = payload
      if (payload.is_active == true) {
         for (let i = 0; i < (state.customer_menus ? state.customer_menus.length : 0); i++) {
            if (state.customer_menus[i].group == 'affiliate') {
               state.customer_menus[i].active = true
            }
         }
      }
   },
   CAN_INSTALL: (state, data) => {
      state.can_install = data
   },
   SET_SHOP: (state, shop) => {
      state.shop = shop
   },
   TOGGLE_DRAWER: (state) => {
      state.drawer = !state.drawer
   },
   SET_ASSETS: (state, payload) => {
      state.assets = { ...payload }
   },
   SET_DRAWER: (state, val) => {

      if (state.window_width >= 1024) {
         state.drawer = true
         state.is_mini = !state.is_mini
      } else {
         state.is_mini = false
         state.drawer = val
      }

   },
   SET_BLOCK: (state, payload) => {
      state.blocks = payload
   },
   SET_META_TITLE: (state, payload) => {
      state.meta.title = payload
   },
   SET_HAS_VALIDATION_TOKEN: (state) => {
      state.has_validation_token = true
   },
   SET_CONFIG: (state, payload) => {

      state.config = payload || {}

      const themeColor = state.config.theme_color || state.config.primary_color || '#1976D2'
      const primaryColor = state.config.primary_color || themeColor
      const secondaryColor = state.config.secondary_color || '#26A69A'
      const accentColor = state.config.accent_color || '#9C27B0'

      setCssVar('brand', themeColor)
      setCssVar('primary', primaryColor)
      setCssVar('secondary', secondaryColor)
      setCssVar('accent', accentColor)

      AddressbarColor.set(themeColor)

   },
   SET_CURRENT_THEME: (state) => {
      const themeColor = state.config?.theme_color || state.config?.primary_color || '#1976D2'
      const primaryColor = state.config?.primary_color || themeColor
      const secondaryColor = state.config?.secondary_color || '#26A69A'
      const accentColor = state.config?.accent_color || '#9C27B0'

      setCssVar('brand', themeColor)
      setCssVar('primary', primaryColor)
      setCssVar('secondary', secondaryColor)
      setCssVar('accent', accentColor)

      AddressbarColor.set(themeColor)
   },
   SET_HOME_VIEW_MODE: (state, payload) => {
      state.config.home_view_mode = payload
   },
   SET_PRODUCT_VIEW_MODE: (state, payload) => {
      state.config.product_view_mode = payload
   },
   SET_THEME: (state, payload) => {
      state.config.theme = payload
   },
   SET_THEME_COLOR: (state, clr) => {
      const color = clr || state.config.theme_color || '#1976D2'
      state.config.theme_color = color
      document.body.style.setProperty('--q-primary', color)
      setCssVar('brand', color)
      AddressbarColor.set(color)
   },
   SET_PRIMARY_COLOR: (state, clr) => {
      state.config.primary_color = clr
      setCssVar('primary', state.config.primary_color)
   },
   SET_SECONDARY_COLOR: (state, clr) => {
      state.config.secondary_color = clr
      setCssVar('secondary', state.config.secondary_color)
   },
   SET_ACCENT_COLOR: (state, clr) => {
      state.config.accent_color = clr
      setCssVar('accent', state.config.accent_color)
   },
   SET_INSTALL_APP: (state, payload) => {
      state.deferredPrompt = payload
   },
   REMOVE_INSTALL_APP: (state) => {
      state.deferredPrompt = null
   },

   SET_GUEST_CHECKOUT: (state, status) => {
      state.config.is_guest_checkout = status
   },
   SET_WHATSAPP_CHECKOUT: (state, status) => {
      state.config.is_whatsapp_checkout = status
   },
   SET_SESSION_ID: (state, payload) => {
      if (!state.session_id) {
         state.session_id = payload
      }
   },
   SET_MENU_CATEGORY: (state, status) => {
      state.isMenuCategory = status
   },
   SET_PAGE_WIDTH: (state, width) => {
      state.page_width = width
   },
   SET_WINDOW_WIDTH: (state, width) => {
      state.window_width = width
   },
   SET_INITIAL_DATA: (state, status) => {
      state.initial_data = status
   }
}
