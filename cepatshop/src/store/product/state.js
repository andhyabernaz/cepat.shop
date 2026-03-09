export default function () {
  return {
    products: {
      data: [],
      ready: false,
      available: true,
      links: null,
      meta: null
    },
    admin_products: {
      data: [],
      links: null,
      last_page_url: null,
      last_page_url: null,
      ready: false,
      available: true,
      per_page: 0
    },
    initial_products: {
      data: [],
      ready: false,
      available: true
    },
    favorites: [],
    productSearch: {
      data: [],
      ready: false,
      available: true
    },
    searchKey: '',
    productsByCategory: {
      data: [],
      ready: false,
      available: true,
      total: 0,
    },
    productFavorites: {
      data: [],
      ready: false,
      available: true
    },
    product_promo: [],
    category_products:[]
  }
}
