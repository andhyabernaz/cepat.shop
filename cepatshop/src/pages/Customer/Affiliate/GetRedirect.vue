<template>
  <div>
    <q-inner-loading :showing="true"></q-inner-loading>
  </div>
</template>

<script>
import { Api } from "boot/axios";
import { Cookies } from "quasar";
export default {
  mounted() {
    if (!this.affiliateConfig) {
      this.getAffiliateConfig();
    } else {
      this.setAffiliate();
    }
  },
  computed: {
    affiliateConfig() {
      return this.$store.state.affiliate_config;
    },
  },
  methods: {
    getAffiliateConfig() {
      Api
        .get("getAffiliateConfig")
        .then((res) => {
          if (res.status == 200) {
            this.$store.commit("SET_AFFILIATE_CONFIG", res.data.data);
            setTimeout(() => {
              this.setAffiliate();
            }, 1000);
          }
        });
    },
    storeLead(form) {
      Api.post("pushLeads", form);
    },
    setAffiliate() {
      let key = "__aff_" + this.$route.params.product_id;
      let val =
        this.$route.params.product_id + "_" + this.$route.params.affiliate_code;

      Cookies.set(key, val, {
        expires: this.affiliateConfig.ttl,
        sameSite: "Lax",
        path: "/",
      });

      this.storeLead({
        product_id: this.$route.params.product_id,
        affiliate_code: this.$route.params.affiliate_code,
      });

      this.$router.replace({
        name: "ProductShow",
        params: { slug: this.$route.params.product_id },
      });
    },
  },
};
</script>