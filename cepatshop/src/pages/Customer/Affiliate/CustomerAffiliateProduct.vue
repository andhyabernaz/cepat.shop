<template>
	<q-page padding>
		<AppHeader title="Produk Affiliasi"></AppHeader>

		<AffiliateMenu />

		<ProductAffiliate />

	</q-page>
</template>
  
<script>
import AffiliateMenu from "./AffiliateMenu.vue";
import ProductAffiliate from './ProductAffiliate.vue'
export default {
	components: { AffiliateMenu, ProductAffiliate },
}
</script>
  