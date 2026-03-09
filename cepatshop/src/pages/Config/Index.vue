<template>
   <q-page padding>
      <AppHeader title="Pengaturan Aplikasi"></AppHeader>
      <div class="bg-white box-shadow text-dark q-mb-sm">
         <q-tabs v-model="tab" outside-arrows mobile-arrows active-color="primary" align="left">
          <template v-for="item in tabs" :key="item.value">
              <q-tab v-if="$can(item.ability)" :name="item.value" :label="item.label" no-caps></q-tab>
          </template>
         </q-tabs>
      </div>

      <q-tab-panels v-model="tab" animated class="bg-transparent">
         <q-tab-panel class="q-pa-none" name="BasicConfig">
            <BasicConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="Order">
            <OrderConfig />
         </q-tab-panel>

         <q-tab-panel class="q-pa-none" name="ShippingConfig">
            <ShippingConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="Local">
            <LocalShipping />
         </q-tab-panel>

         <q-tab-panel class="q-pa-none" name="Notifikasi">
            <notification />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="Wagateway">
            <WagatewayConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="PaymentConfig">
            <PaymentConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="MetaConfig">
            <MetaConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="AffiliateConfig">
            <AffiliateConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="SaldoConfig">
            <SaldoConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="MarketplaceConfig">
            <MarketplaceConfig />
         </q-tab-panel>
         <q-tab-panel class="q-pa-none" name="System">
            <system-update />
         </q-tab-panel>
      </q-tab-panels>
   </q-page>
</template>

<script>
import ShippingConfig from "./ShippingConfig.vue";
import BasicConfig from "./BasicConfig.vue";
import Notification from "./NotificationConfig.vue";
import OrderConfig from "./OrderConfig.vue";
import SystemUpdate from "./SystemUpdate.vue";
import LocalShipping from "./LocalShippingConfig.vue";
import WagatewayConfig from "./WagatewayConfig.vue";
import PaymentConfig from "./PaymentConfig.vue";
import MetaConfig from "./MetaConfig.vue";
import MarketplaceConfig from "./MarketplaceConfig.vue";
import AffiliateConfig from "./AffiliateConfig.vue";
import SaldoConfig from "./SaldoConfig.vue";
export default {
   name: "AppConfigIndex",
   components: {
      ShippingConfig,
      BasicConfig,
      Notification,
      OrderConfig,
      SystemUpdate,
      LocalShipping,
      WagatewayConfig,
      PaymentConfig,
      MarketplaceConfig,
      MetaConfig,
      AffiliateConfig,
      SaldoConfig
   },
   data() {
      return {
         tab: "BasicConfig",
         tabs: [
            { ability: 'view-config',value: "BasicConfig", label: "Basic" },
            { ability: 'order-config',value: "Order", label: "Order Config" },
            { ability: 'shipping-config',value: "ShippingConfig", label: "Ekspedisi" },
            { ability: 'shipping-config',value: "Local", label: "Pickup dan Kurir toko" },
            { ability: 'smtp-config',value: "Notifikasi", label: "SMTP / Telegram" },
            { ability: 'whatsapp-gateway-config',value: "Wagateway", label: "Whatsapp Gateway" },
            { ability: 'payment-gateway-config',value: "PaymentConfig", label: "Payment Gateway" },
            { ability: 'meta-config',value: "MetaConfig", label: "Meta Config" },
            { ability: 'affiliate-config',value: "AffiliateConfig", label: "Affiliate Config" },
            { ability: 'saldo-config',value: "SaldoConfig", label: "Saldo Config" },
            { ability: 'marketplace-config',value: "MarketplaceConfig", label: "Marketplace Config" },
            { ability: 'system-config',value: "System", label: "System & Update" },
         ],
      };
   },
   computed: {
      config() {
         return this.$store.state.config;
      },
   },
   created() {
      this.$store.dispatch("getAdminConfig");
   },
};
</script>
