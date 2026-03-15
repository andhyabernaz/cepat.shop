import { route } from 'quasar/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import routes from './routes'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default route(function ({ store }) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

  const Router = createRouter({
     scrollBehavior: (to, from, savedPosition) => {
        return new Promise((resolve, reject) => {
           if (savedPosition) {
              resolve(savedPosition)
           } else {
              resolve({ left: 0, top: 0 })
           }
        })
     },
   //  scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE)
  })

  Router.beforeEach((to, from, next) => {
    const token = store?.state?.user?.token
    const user = store?.state?.user?.user

    const requiresAdmin = to.matched.some((r) => r.meta?.requiresAdmin)
    const requiresCustomer = to.matched.some((r) => r.meta?.requiresCustomer)

    if (!requiresAdmin && !requiresCustomer) return next()

    if (!token) {
      return next({ name: 'Login', query: { redirect: to.fullPath } })
    }

    if (!user) return next()

    if (requiresAdmin && !user.is_admin) {
      return next({ name: 'Home' })
    }

    if (requiresCustomer && user.is_admin) {
      return next({ name: 'AdminDashboard' })
    }

    return next()
  })

  return Router
})
