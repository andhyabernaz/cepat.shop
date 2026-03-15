<template>

  <div class="">
    <q-form @submit.prevent="updateDate">
         <q-card flat class="section">
            <q-card-section>

            <div>
              <div class="card-subtitle">Pengaturan Deposit</div>
              <q-input filled required v-model="form.min_deposit_amount" label="Minimum Deposit" type="number"
                min="1000" />

              <div class="q-pt-md">
                <div class="text-label">Deskripsi / Panduan Deposit</div>
                <q-editor :toolbar="[
                  [
                    'bold',
                    'italic',
                    'strike',
                    'underline',
                    'subscript',
                    'superscript',
                  ],
                  ['quote', 'unordered', 'ordered', 'outdent', 'indent'],
                  [
                    {
                      label: $q.lang.editor.formatting,
                      icon: $q.iconSet.editor.formatting,
                      list: 'no-icons',
                      options: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'code'],
                    },
                  ],
                ]" v-model="form.deposit_description" />
              </div>
            </div>
          </q-card-section>
        </q-card>
          <q-card flat class="section q-mt-md">
            <q-card-section>
              <div class="card-subtitle">
                Pengaturan Withdrawal
              </div>
      
            <div>
              <q-input filled required v-model="form.min_withdraw_amount" label="Minimum Penarikan" type="number"
                min="1000" class="q-mb-md" />
              <q-input filled required type="number" v-model="form.max_withdraw_amount" label="Maksimum Penarikan"
                hint="Input `0` untuk tanpa batasan penarikan" class="q-mb-md" />
              <q-input filled required type="number" v-model="form.withdraw_fee" label="Biaya Penarikan"
                class="q-mb-md" />
              <q-select use-input use-chips multiple hide-dropdown-icon input-debounce="0" new-value-mode="add" filled
                v-model="form.withdraw_channels" :options="withdraw_channels_default"
                label="Pengaturan Channel Penarikan" class="q-mt-md"
                hint="Silahkan tambahkan jika options tidak tersedia" />
              <div class="q-pt-lg">
                <div class="text-label">Deskripsi / Panduan Penarikan</div>
                <q-editor :toolbar="[
                  [
                    'bold',
                    'italic',
                    'strike',
                    'underline',
                    'subscript',
                    'superscript',
                  ],
                  ['quote', 'unordered', 'ordered', 'outdent', 'indent'],
                  [
                    {
                      label: $q.lang.editor.formatting,
                      icon: $q.iconSet.editor.formatting,
                      list: 'no-icons',
                      options: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'code'],
                    },
                  ],
                ]" v-model="form.withdraw_description" />
              </div>
            </div>
             </q-card-section>
      </q-card>
    
        <div class="q-mt-lg">
          <q-btn class="full-width" unelevated type="submit" label="Simpan Perubahan" color="primary"></q-btn>
        </div>
        </q-form>
  </div>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
  data() {
    return {
      withdraw_channels_default: [
        "OVO",
        "DANA",
        "GOPAY",
        "SHOPPEPAY",
        "BANK BRI",
        "BANK BCA",
        "BANK BNI",
        "BANK BSI",
      ],
      form: {
        min_deposit_amount: "",
        deposit_description: "",
        min_withdraw_amount: "",
        max_withdraw_amount: "",
        withdraw_fee: "",
        withdraw_description: "",
        withdraw_channels: [],
      },
    };
  },
  mounted() {
    this.getConfig();
  },
  methods: {
    setConfig(data) {
      this.form.min_deposit_amount = data.min_deposit_amount;
      this.form.min_withdraw_amount = data.min_withdraw_amount;
      this.form.withdraw_fee = data.withdraw_fee;
      this.form.max_withdraw_amount = data.max_withdraw_amount ?? 0;
      this.form.deposit_description = data.deposit_description ?? "";
      this.form.withdraw_description = data.withdraw_description ?? "";
      this.form.withdraw_channels = data.withdraw_channels ?? [];

      if (this.form.withdraw_channels.length) {
        this.form.withdraw_channels.forEach((el) => {
          if (!this.withdraw_channels_default.includes(el)) {
            this.withdraw_channels_default.push(el);
          }
        });
      }
    },
    getConfig() {
      BaseApi
        .get("saldo-config")
        .then((res) => {
          this.setConfig(res.data.data);
        });
    },
    updateDate() {
      if (!this.form.withdraw_channels.length) {
        this.$q.notify({
          type: "negative",
          message: "Channel penarikan tidak boleh kosong",
        });
        return;
      }
      BaseApi
        .post("saldo-config", this.form)
        .then(() => {
          this.getConfig()
          this.$q.notify({
            type: "positive",
            message: "Berhasil update data",
          });
        });
    },
  },
};
</script>