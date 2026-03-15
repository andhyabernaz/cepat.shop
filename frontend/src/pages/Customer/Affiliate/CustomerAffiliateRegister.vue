<template>
  <q-page padding>
        <AppHeader title="Daftar Affiliasi">
      </AppHeader>

    <div v-if="!loading">
      <q-card flat bordered>
        <q-card-section class="q-pa-lg">
          <div class="card-subtitle">
            <div>Daftar Affiliasi</div>
          </div>
          <div class="q-mb-md" v-if="affiliate_config" v-html="affiliate_config.description"> </div>
          <q-form @submit.prevent="submitData">
            <q-input label="Kode Affiliasi" v-model="form.code" required @update:modelValue="handleCheckAvailable" outlined
              placeholder="minimal 6 karakter" stack-label>
              <template v-slot:append>
                <q-btn label="Auto Generate Code" color="blue" unelevated @click="handleAutoGenarate"></q-btn>
              </template>
            </q-input>
            <div class="text-xs q-pa-xs" v-if="is_message" :class="is_exist ? 'text-red' : 'text-green-9'">{{ is_exist ?
              'Code sudah terdafar' : 'Code tersedia' }}</div>
            <div class="q-mt-lg">
              <q-btn label="Daftar Sekarang" type="submit" color="primary" :disable="!is_ready"></q-btn>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
    <q-inner-loading :showing="loading">
      <q-spinner-facebook size="50px" color="primary" />
    </q-inner-loading>
  </q-page>
</template>

<script>
import { BaseApi } from "boot/axios";
export default {
  name: "AffiliateIndex",
  data() {
    return {
      loading: false,
      is_affiliate_active: false,
      affiliate: null,
      is_exist: false,
      is_ready: false,
      is_message: false,
      form: {
        code: ""
      },
      timeout: null
    };
  },
  watch: {
    'form.code'(val) {
      if (val) {
        this.form.code = val.replace(/[^a-zA-Z0-9]/g, '');
      }
    }
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
    }
  },
  methods: {
    handleAutoGenarate() {
      BaseApi.get("affiliate/generate").then(res => {
        this.form.code = res.data.data;
        this.is_ready = true;
      });
    },
    submitData() {
      BaseApi.post("affiliate", this.form).then(res => {
        this.affiliate = res.data.data;
        this.$router.push({ name: 'CustomerAffiliate' })
      });
    },
    handleCheckAvailable(str) {
      this.is_message = false;
      this.is_ready = false;

      if (str.length < 6) {
        return;
      }

      clearTimeout(this.timeout)

      this.timeout = setTimeout(() => {
        BaseApi.get("affiliate/check/" + str).then(res => {
          this.is_exist = res.data.is_exist;
          this.is_message = true;
          if (this.is_exist == false) {
            this.is_ready = true;
          }
        });
      }, 1000)
    },
  },
}
</script>
