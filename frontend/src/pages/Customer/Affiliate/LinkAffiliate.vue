<template>
	<div>
		<div v-if="!loading">
			<q-card flat bordered>
				<q-card-section>
					<div class="block-title text-weight-bold text-md flex justify-between">
						<div>Detail Affiliasi</div>
						<q-btn color="primary" @click="openCouponModal">Edit Kupon</q-btn>
					</div>
					<q-list separator>
						<q-item class="item-header">
							<q-item-section>Kode Affiliasi</q-item-section>
							<q-item-section>Kode Kupon</q-item-section>
							<q-item-section>Status</q-item-section>
							<q-item-section>Terdaftar</q-item-section>
						</q-item>
						<q-item>
							<q-item-section>{{ affiliate.code }}</q-item-section>
							<q-item-section>{{ affiliate.coupon_code ? affiliate.coupon_code : '-' }}</q-item-section>
							<q-item-section>
								<span>
									<q-badge>{{ affiliate.status_label }}</q-badge>
								</span>
							</q-item-section>
							<q-item-section>{{ affiliate.created }}</q-item-section>
						</q-item>
					</q-list>
				</q-card-section>
			</q-card>
			<ProductAffiliate :affiliate="affiliate" />
		</div>
		<q-inner-loading :showing="loading">
			<q-spinner-facebook size="50px" color="primary" />
		</q-inner-loading>
		<q-dialog v-model="couponModal" persistent>
			<q-card class="card-lg">
				<div class="card-heading text-md">Generate Kupon</div>
				<q-card-section>

					<q-form @submit.prevent="submitData">
						<div class="label q-py-xs">Kode Kupon</div>
						<q-input outlined v-model="form.coupon_code" hint="Minimal 6 Karakter" @update:modelValue="handleCheckAvailable"
							stack-label :error="is_exist"
							error-message="Kode kupon sudah terdaftar silahkan ganti dengan yang lain"
							:loading="is_checked_loading"></q-input>
						<!-- <div class="q-pa-xs text-xs" v-if="is_message" :class="is_exist ? 'text-red' : 'text-green-9'">{{ is_exist ? 'Kode Kupon sudah terdafar' : 'Kode Kupon tersedia' }}</div> -->
						<div class="q-mt-lg q-gutter-x-sm">
							<q-btn label="Submit" type="submit" color="primary" :disable="!is_ready"></q-btn>
							<q-btn label="Batal" outline color="primary" v-close-popup></q-btn>
						</div>
					</q-form>
					<div class="q-pa-sm bg-yellow-2 q-mt-md" v-if="affiliate.coupon_code">
						<div class="text-weight-bold text-yellow-10">WARNING!</div> Kode kupon anda sudah aktif, Jika anda
						megubah kode kupon, maka kode sebelumnya sudah tidak berlaku.
					</div>
				</q-card-section>
			</q-card>
		</q-dialog>
	</div>
</template>
  
<script>
import { Api } from "boot/axios";
import ProductAffiliate from './ProductAffiliate.vue'
export default {
	name: "AffiliateIndex",
	components: { ProductAffiliate },
	data() {
		return {
			tab1: 'Link',
			couponModal: false,
			loading: false,
			is_affiliate_active: false,
			is_checked_loading: false,
			affiliate: null,
			is_exist: false,
			is_ready: false,
			is_message: false,
			form: {
				coupon_code: ""
			},
			timeout: null
		};
	},
	watch: {
		'form.coupon_code'(val) {
			if (val) {
				this.form.coupon_code = val.replace(/[^a-zA-Z0-9]/g, '');
			}
		}
	},
	created() {
		this.getAffiliate();
	},
	mounted() {
		setTimeout(() => {
			if (this.affiliate_config && !this.affiliate_config.is_active) {
				this.$router.back()
			}
		}, 1000)
	},
	computed: {
		affiliate_config() {
			return this.$store.state.affiliate_config
		},
		site_has_affiliate() {
			if (this.affiliate_config && !this.affiliate_config.is_active) {
				return false
			}
			return true
		}
	},
	methods: {
		getAffiliate() {
			this.loading = true
			Api().get("affiliate").then(res => {
				if (res.data.data.active == false) {
					return this.$router.push({ name: 'CustomerAffiliateRegister' })
				}
				this.affiliate = res.data.data.affiliate
				this.is_affiliate_active = res.data.data.active;
				this.form.coupon_code = this.affiliate.coupon_code ?? ''
			}).finally(() => this.loading = false)
		},
		submitData() {
			Api().post("affiliate/update-coupon/" + this.affiliate.id, this.form).then(res => {
				this.affiliate = res.data.data;
				this.is_affiliate_active = true;
				this.couponModal = false
			});
		},
		openCouponModal() {
			this.is_exist = false
			this.form.coupon_code = this.affiliate.coupon_code ?? ''
			this.couponModal = true
		},
		handleCheckAvailable() {
			this.is_message = false;
			this.is_ready = false;

			if (this.form.coupon_code.length <= 5) {
				return;
			}

			clearTimeout(this.timeout)

			this.timeout = setTimeout(() => {

				this.is_checked_loading = true

				Api().get("affiliate/check-coupon/" + this.form.coupon_code).then(res => {
					this.is_exist = res.data.is_exist;
					this.is_message = true;
					if (this.is_exist == false) {
						this.is_ready = true;
					}
				}).finally(() => this.is_checked_loading = false)
			}, 1000)
		},
	},
}
</script>
  