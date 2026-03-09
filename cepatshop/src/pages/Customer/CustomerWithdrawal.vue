<template>
	<q-page padding>
		<AppHeader title="Withdrawal"></AppHeader>
		<div class="box-column flat">
			<div>
				<div class="card-subtitle">
					<div>Riwayat Penarikan</div>
				</div>
				<div class="table-responsive">
					<table class="table aligned bordered">
						<thead>
							<tr>
								<th>#</th>
								<th>Tanggal</th>
								<th>Reference</th>
								<th>Nominal</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in withdrawal.data" :key="index">
								<td>{{ withdrawal.from + index }}</td>
								<td>
									<q-item-label>{{ dateFormat(item.created_at) }}</q-item-label>
								</td>
								<td>{{ item.ref_code }}</td>
								<td>
									<q-item-label>{{ moneyIdr(item.amount) }}</q-item-label>
								</td>
								<td>
									<div>
										<q-badge :color="getColorBadge(item.status)">{{ item.status }}</q-badge>

									</div>
								</td>
								<td>
									<q-btn size="13px" padding="3px 8px" unelevated class="q-ma-xs" color="blue"
										@click="handleDetail(item)" no-caps>
										<span>Detail</span>
									</q-btn>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div v-if="!withdrawal.total" class="text-center q-pa-sm">Belum ada Data</div>
				<SimplePagination v-bind="withdrawal" @loadUrl="getData"></SimplePagination>
			</div>
		</div>
		<q-dialog v-model="evidenceModal">
			<img :src="evidence" v-if="evidence" style="max-height:100%" />
		</q-dialog>
		<q-dialog v-model="detailModal">
			<q-card class="card-md">
				<q-card-section>
					<div class="card-title flex justify-between">
						<div>Detail</div>
						<q-btn round flat icon="close" v-close-popup dense></q-btn>
					</div>
					<div v-if="temp_data">
						<q-list separator>
							<q-item class="q-px-none">
								<q-item-section>Reference</q-item-section>
								<q-item-section >{{ temp_data.ref_code }}</q-item-section>
							</q-item>
							<q-item class="q-px-none">
								<q-item-section>Amount</q-item-section>
								<q-item-section class="text-weight-bold">{{ moneyIdr(temp_data.amount) }}</q-item-section>
							</q-item>
							<q-item class="q-px-none">
								<q-item-section>Vendor</q-item-section>
								<q-item-section>{{ temp_data.target_vendor }}</q-item-section>
							</q-item>
							<q-item class="q-px-none">
								<q-item-section>Account Number</q-item-section>
								<q-item-section>{{ temp_data.target_number }}</q-item-section>
							</q-item>
							<q-item class="q-px-none">
								<q-item-section>Account Name</q-item-section>
								<q-item-section>{{ temp_data.target_account }}</q-item-section>
							</q-item>
							<q-item class="q-px-none">
								<q-item-section>Status</q-item-section>
								<q-item-section class="text-weight-bold">{{ temp_data.status }}</q-item-section>
							</q-item>
							<q-item v-if="temp_data.reason" class="q-px-none">
								<q-item-section top>Reason</q-item-section>
								<q-item-section>
									<q-item-label>{{ temp_data.reason }}</q-item-label>
								</q-item-section>
							</q-item>
							<q-item v-if="temp_data.evidence" class="q-px-none">
								<q-item-section top>Bukti Pembayaran</q-item-section>
								<q-item-section>
									<img v-if="temp_data.evidence" :src="temp_data.evidence.src" style="width:50px;height:auto"
										class="thumbnail cursor-pointer" @click="openEvidence(temp_data.evidence.src)" />
								</q-item-section>
							</q-item>
						</q-list>
					</div>
				</q-card-section>
			</q-card>
		</q-dialog>
	</q-page>
</template>

<script>
import { dateFormat } from 'src/utils';
export default {
	data() {
		return {
			evidenceModal: false,
			detailModal: false,
			evidence: '',
			temp_data: ''
		}
	},
	computed: {
		withdrawal() {
			return this.$store.state.user.customer_withdrawal
		}
	},
	created() {
		this.getData()
	},
	methods: {
		getData(url = null) {
			 this.$store.commit("SET_LOADING", true);
			if (!url) {
				url = 'customer/withdrawals'
			}
			this.$store.dispatch('user/getCustomerWithdrawals', url)
		},
		handleDetail(data) {
			this.temp_data = data
			this.detailModal = true
		},
		openDialog(title, msg) {
			this.$q.dialog({
				title: title,
				message: msg
			})
		},
		openEvidence(src) {
			this.evidence = src
			this.evidenceModal = true
		}
	},
}
</script>
