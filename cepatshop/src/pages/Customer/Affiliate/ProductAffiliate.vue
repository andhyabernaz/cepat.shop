<template>
	 <div class="box-column flat">
		<div>
			<div class="card-subtitle">Produk Affiliasi</div>
			<div class="table-responsive">
				<table class="table bordered aligned">
					<thead>
						<tr>
							<th>#</th>
							<th colspan="2">Produk</th>
							<th>Komisi</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(product, idx) in products.data" :key="product.id">
							    <td>{{ products.from + idx}}</td>
							 <td >
                           <q-img :src="product.assets[0].src" class="bg-white img-product-admin img-thumbnail"
                              ratio="1" width="55px" />
                        </td>
							<td>
								<div>
									<q-item-label>{{ product.title }}</q-item-label>
									<q-item-label caption v-if="affiliate">{{ getRoutePath(product.id) }}</q-item-label>
									<div>
									  <template v-if="product.total_count > 0">
										  <q-item-label caption>
											  {{
												  renderVarianPrice(product)
											  }}
										  </q-item-label>
									  </template>
									  <template v-else>
										  <q-item-label caption>{{
											  moneyIdr(product.price)
											  }}</q-item-label>
									  </template>
								</div>
							</div>
							
						</td>
						<td>
					{{ product.affiliate_detail }}
						</td>
							<td>
								<div class="flex no-wrap q-gutter-x-sm" v-if="affiliate">
									<q-btn icon="content_copy" size="sm" padding="sm" unelevated outline
										@click="copyString(getRoutePath(product.id))"></q-btn>
									<q-btn icon="visibility" size="sm" padding="sm" unelevated outline
										:to="{ name: 'ProductShow', params: { slug: product.slug }, query: { _rdr: $route.name } }"></q-btn>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div v-if="!products.available" class="text-center q-py-lg">Tidak ada data</div>

			<SimplePagination v-bind="products" @loadUrl="getdata"></SimplePagination>
		</div>
	</div>
</template>

<script>
import { copyString } from 'src/utils';

export default {
	mounted() {
		this.getdata()
	},
	computed: {
		products() {
			return this.$store.state.affiliate.all_products
		},
		affiliate() {
			return this.$store.state.affiliate.affiliate
		}
	},
	methods: {
		getdata(url = null) {
			this.$store.dispatch('affiliate/getAllProducts', url)
		},
		getRoutePath(id) {
			let props = this.$router.resolve({
				name: "AffiliateGet",
				params: { product_id: id, affiliate_code: this.affiliate.code },
			});
			return location.origin + props.href;
		},
		 renderVarianPrice(product) {
         if (product.minPrice && product.maxPrice) {

            let minPrice = parseInt(product.minPrice)
            let maxPrice = parseInt(product.maxPrice)
            if (minPrice < maxPrice) {
               return `${this.moneyIdr(minPrice)} - ${this.moneyIdr(maxPrice)}`;
            }

            return `${this.moneyIdr(minPrice)}`;
         }

         return "";
      },
	}
}

</script>
