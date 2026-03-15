<template>
	<q-page padding>
		<AppHeader title="Lisensi Saya">
		</AppHeader>
		<LicenseTable :licenses="licenses"></LicenseTable>
		<SimplePagination v-bind="licenses" @loadUrl="getData"></SimplePagination>
	</q-page>
</template>

<script>
import LicenseTable from './LicenseTable.vue'
export default {
	components: { LicenseTable },
	data() {
		return {
			isSearch: true,
			options: ['UNPAID', 'PAID', 'PACKING', 'SHIPPING', 'CANCELED'],
			isFilter: false,
			inputResiModal: false,
			orderSelected: '',
			followUpModal: false,
			currentOrder: null,
			orderFiltered: [],
			orderBySearch: [],
			search: '',
			form: {
				order_id: '',
				resi: '',
				status: ''
			},
		}
	},
	computed: {
		licenses() {
			return this.$store.state.user.customer_licenses
		},
	},
	mounted() {
		this.getData()
	},
	methods: {
		getData(url = null) {
			this.$store.commit("SET_LOADING", true);
			this.$store.dispatch('user/getCustomerLicense', url)
		},
	},
}
</script>
