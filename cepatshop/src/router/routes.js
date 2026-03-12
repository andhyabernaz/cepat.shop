const routes = [
   {
      path: '/',
      component: () => import('layouts/FrontLayout.vue'),
      children: [
         { path: '', name: 'Home', component: () => import('pages/Front/PageIndex.vue') },
         { path: 'favorite', name: 'ProductFavorite', component: () => import('pages/Front/ProductFavorite.vue') },
         { path: 'products', name: 'ProductIndex', component: () => import('pages/Front/ProductIndex.vue') },
         { path: 'products/category/:id', name: 'ProductCategory', component: () => import('pages/Front/ProductsByCategory.vue') },
         { path: 'products/:id', name: 'ProductByCategory', component: () => import('pages/Front/ProductsByCategory.vue') },
         { path: 'search-product', name: 'ProductSearch', component: () => import('src/pages/Front/ProductSearch.vue') },
         { path: 'search-order', name: 'OrderSearch', component: () => import('src/pages/Front/OrderSearch.vue') },
         { path: 'posts', name: 'FrontPostIndex', component: () => import('src/pages/Front/PostIndex.vue') },
         { path: 'post/:slug', name: 'FrontPostShow', component: () => import('src/pages/Front/PostDetaIl.vue') },
         { path: 'cart', name: 'Cart', component: () => import('src/pages/Shop/Cart.vue') },
         {
            path: 'product/:slug',
            name: 'ProductShow',
            component: () => import('pages/Front/ProductDetail.vue')
         },
      ]
   },
   { path: '/products/item/:product_id/:affiliate_code', name: 'AffiliateGet', component: () => import('src/pages/Customer/Affiliate/GetRedirect.vue') },
   { path: '/print-order/:order_ref', name: 'OrderPrint', component: () => import('pages/Order/Print.vue') },
   { path: '/print-label/:order_ref', name: 'OrderPrintLabel', component: () => import('pages/Order/PrintLabel.vue') },
   {
      path: '/p',
      component: () => import('src/layouts/BlankLayout.vue'),
      children: [
         { path: 'checkout', name: 'Checkout', component: () => import('src/pages/Checkout/Index.vue') },
         { path: 'direct-checkout', name: 'DirectCheckout', component: () => import('src/pages/Checkout/Index.vue') },
         { path: 'invoice/:order_ref', name: 'UserInvoice', component: () => import('src/pages/Invoice/Index.vue') },
      ]
   },
   {
      path: '/auth',
      component: () => import('src/layouts/AuthLayout.vue'),
      children: [
         { path: 'login', name: 'Login', component: () => import('src/pages/Auth/Login.vue') },
         { path: 'register', name: 'Register', component: () => import('src/pages/Auth/Register.vue') },
         { path: 'forgot-password', name: 'ForgotPassword', component: () => import('src/pages/Auth/ForgotPassword.vue') },
         { path: 'reset-password', name: 'ResetPassword', component: () => import('src/pages/Auth/ResetPassword.vue') },
      ]
   },
   {
      path: '/me',
      component: () => import('layouts/CustomerLayout.vue'),
      meta: { requiresCustomer: true },
      children: [
         { path: '', name: 'CustomerDashboard', component: () => import('src/pages/Customer/CustomerDashboard.vue') },
         { path: 'edit', name: 'CustomerAccountEdit', component: () => import('src/pages/Customer/CustomerAccountEdit.vue') },
         { path: 'order', name: 'CustomerOrder', component: () => import('src/pages/Customer/CustomerOrder.vue') },
         { path: 'address', name: 'CustomerAddress', component: () => import('src/pages/Customer/CustomerAddress.vue') },
         { path: 'review/:invoice_ref', name: 'OrderProductReview', component: () => import('src/pages/Customer/OrderProductReview.vue') },
         { path: 'reviews', name: 'CustomerReviews', component: () => import('src/pages/Customer/CustomerReviews.vue') },
         { path: 'MutasiSaldo', name: 'CustomerMutasiSaldo', component: () => import('pages/Customer/CustomerMutasiSaldo.vue') },
         { path: 'licenses', name: 'CustomerLicense', component: () => import('src/pages/Customer/CustomerLicense.vue') },
         { path: 'license/:license_id', name: 'CustomerLicenseShow', component: () => import('src/pages/Customer/CustomerLicenseShow.vue') },
         { path: 'affiliasi', name: 'CustomerAffiliate', component: () => import('src/pages/Customer/Affiliate/CustomerAffiliate.vue') },
         { path: 'affiliasi/products', name: 'CustomerAffiliateProduct', component: () => import('src/pages/Customer/Affiliate/CustomerAffiliateProduct.vue') },
         { path: 'affiliasi/page-visited', name: 'CustomerPageVisited', component: () => import('src/pages/Customer/Affiliate/CustomerPageVisited.vue') },
         { path: 'affiliasi/leads', name: 'CustomerLeads', component: () => import('src/pages/Customer/Affiliate/CustomerLeads.vue') },
         { path: 'affiliasi/leaderboard', name: 'CustomerLeaderboard', component: () => import('src/pages/Customer/Affiliate/CustomerLeaderboard.vue') },
         { path: 'affiliasi/register', name: 'CustomerAffiliateRegister', component: () => import('src/pages/Customer/Affiliate/CustomerAffiliateRegister.vue') },
         { path: 'withdrawal', name: 'CustomerWithdrawal', component: () => import('src/pages/Customer/CustomerWithdrawal.vue') },
      ]
   },
   {
      path: '/admin',
      component: () => import('layouts/AdminLayout.vue'),
      meta: { requiresAdmin: true },
      children: [
         { path: 'dashboard', name: 'AdminDashboard', component: () => import('src/pages/Dashboard/AdminDashboard.vue') },
         { path: 'account', name: 'Account', component: () => import('src/pages/Account/Index.vue') },
         { path: 'settings', name: 'Settings', component: () => import('src/pages/Dashboard/Settings.vue') },
         { path: 'slider', name: 'Slider', component: () => import('src/pages/Slider/Index.vue') },
         { path: 'store', name: 'Shop', component: () => import('src/pages/Shop/Index.vue') },
         { path: 'categories', name: 'CategoryIndex', component: () => import('src/pages/Category/CategoryIndex.vue') },
         { path: 'products', name: 'AdminProductIndex', component: () => import('pages/Product/Index.vue') },
         { path: 'posts', name: 'AdminPostIndex', component: () => import('src/pages/Post/Index.vue') },
         { path: 'blocks', name: 'AdminBlockIndex', component: () => import('pages/Block/Index.vue') },
         { path: 'orders', name: 'OrderIndex', component: () => import('pages/Order/Index.vue') },
         { path: 'orders/:id', name: 'OrderDetail', component: () => import('pages/Order/Detail.vue') },
         { path: 'banks', name: 'BankIndex', component: () => import('pages/Shop/BankAccount.vue') },

         { path: 'promo', name: 'PromoIndex', component: () => import('pages/Promo/Index.vue') },
         { path: 'promo/:id', name: 'PromoDetail', component: () => import('pages/Promo/Detail.vue') },
         { path: 'reviews', name: 'ReviewsIndex', component: () => import('pages/Reviews/Index.vue') },
         { path: 'vouchers', name: 'VoucherIndex', component: () => import('pages/Vouchers/VoucherIndex.vue') },
         { path: 'vouchers/create', name: 'VoucherCreate', component: () => import('pages/Vouchers/VoucherForm.vue') },
         { path: 'vouchers/edit/:voucher_id', name: 'VoucherEdit', component: () => import('pages/Vouchers/VoucherForm.vue') },

         { path: 'product/digital-download', name: 'ProductDownloadForm', component: () => import('pages/Product/ProductDownloadForm.vue') },
         { path: 'product/digital-download/:id/edit', name: 'ProductDownloadEdit', component: () => import('pages/Product/ProductDownloadForm.vue') },
         { path: 'product/digital-download/:id/clone', name: 'ProductDownloadClone', component: () => import('pages/Product/ProductDownloadForm.vue') },

         { path: 'product/videos', name: 'ProductVideoForm', component: () => import('pages/Product/ProductVideoForm.vue') },
         { path: 'product/videos/:id/edit', name: 'ProductVideoEdit', component: () => import('pages/Product/ProductVideoForm.vue') },
         { path: 'product/videos/:id/clone', name: 'ProductVideoClone', component: () => import('pages/Product/ProductVideoForm.vue') },

         { path: 'product/create', name: 'ProductCreate', component: () => import('pages/Product/ProductForm.vue') },
         { path: 'product/edit/:id', name: 'ProductEdit', component: () => import('pages/Product/ProductForm.vue') },
         { path: 'product/clone/:id', name: 'ProductClone', component: () => import('pages/Product/ProductForm.vue') },
         { path: 'category/form', name: 'CategoryForm', component: () => import('src/pages/Category/CategoryForm.vue') },
         { path: 'category/form/:category_id', name: 'CategoryFormEdit', component: () => import('src/pages/Category/CategoryForm.vue') },
         { path: 'post/create', name: 'PostCreate', component: () => import('src/pages/Post/PostForm.vue') },
         { path: 'post/edit/:id', name: 'PostEdit', component: () => import('pages/Post/PostForm.vue') },
         { path: 'config', name: 'Config', component: () => import('pages/Config/Index.vue') },
         { path: 'notifications', name: 'NotificationAdmin', component: () => import('pages/Notification/NotificationAdmin.vue') },
         { path: 'notification-messages', name: 'NotificationMessage', component: () => import('pages/Notification/NotificationMessage.vue') },
         { path: 'notification-templates', name: 'NotificationTemplate', component: () => import('pages/Notification/NotificationTemplate.vue') },
         { path: 'notification-templates/create', name: 'NotificationTemplateCreate', component: () => import('pages/Notification/NotificationTemplateForm.vue') },
         { path: 'media', name: 'MediaIndex', component: () => import('pages/Media/MediaIndex.vue') },
         { path: 'MutasiSaldo', name: 'MutasiSaldo', component: () => import('pages/MutasiSaldo/MutasiSaldo.vue') },
         { path: 'withdrawal', name: 'WithdrawalIndex', component: () => import('pages/Shop/WithdrawalIndex.vue') },
         { path: 'affiliates', name: 'AffiliatesIndex', component: () => import('src/pages/Affiliate/AffiliatesIndex.vue') },
         { path: 'affiliate-users', name: 'AffiliateUser', component: () => import('src/pages/Affiliate/AffiliateUser.vue') },

         { path: 'users', name: 'UserList', component: () => import('src/pages/User/UserList.vue') },
         { path: 'roles', name: 'RoleIndex', component: () => import('src/pages/User/RoleIndex.vue') },
         { path: 'roole-permissions', name: 'RolePermissions', component: () => import('src/pages/User/RolePermissions.vue') },

      ]
   },
   { path: '/install', name: 'InstallApp', component: () => import('src/pages/Install/InstallWelcome.vue') },
   // Always leave this as last one,
   // but you can also remove it
   {
      path: '/:catchAll(.*)*',
      component: () => import('pages/ErrorNotFound.vue')
   }
]

export default routes
