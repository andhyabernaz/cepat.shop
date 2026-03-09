export default function () {
   return {
      posts: {
         is_done: false,
         is_available: true,
         data: []
      },
      post_tags: [],
      blocks: {
         banner: [],
         partner: [],
         featured: [],
         ready: false,
         available: true
      },
      product_promo: [],
      categories: {
         ready: false,
         available: true,
         data: []
      },
      category_menu: [],
      products: [],
      sliders: {
         count: 0,
         data: [],
         ready: false,
         available: true
      },
      is_loaded: false,
      posts: {
         data: [],
         from: 1,
         total: 0,
         ready: false
      },
      promote_posts: {
         data: [],
         count: 0,
         ready: false
      },
      product_list: {
         data: [],
         links: null,
         meta: null,
         ready: false,
         available: true
      },
      all_categories: []
   }
}
