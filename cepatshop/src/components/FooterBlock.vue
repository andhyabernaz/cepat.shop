<template>

  <div class="footer q-px-sm q-py-lg">
    <div class="main-content grid-2-responsive">

      <div class="q-pa-md">
        <!-- <div class="text-md text-weight-bold text-white q-mb-md">
          Tentang Kami
        </div> -->
        <q-list v-if="shop">
          <q-item dense class="q-px-none" v-if="shop.name">
            <q-item-section side class="footer-icon">
              <q-icon name="eva-home-outline" />
            </q-item-section>
            <q-item-section>
              <q-item-section>
                <q-item-label class="text-weight-bold text-md">{{ shop.name }}</q-item-label>
              </q-item-section>
            </q-item-section>
          </q-item>
          <q-item dense class="q-px-none" v-if="shop.phone">
            <q-item-section side class="footer-icon">
              <q-icon name="eva-phone-call-outline" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-weight-medium">{{ shop.phone }}</q-item-label>
            </q-item-section>
          </q-item>
          <q-item dense class="q-px-none" v-if="shop.address">
            <q-item-section side top class="footer-icon">
              <q-icon name="eva-pin-outline" />
            </q-item-section>
            <q-item-section>
              <q-item-section>
                <div class="" v-html="shop.address"></div>
              </q-item-section>
            </q-item-section>
          </q-item>
        </q-list>
      </div>
      <MarketplaceBlock></MarketplaceBlock>
    </div>
  </div>
</template>

<script>
import MarketplaceBlock from './MarketplaceBlock.vue'
export default {
  components: {
    MarketplaceBlock
  },
  computed: {
    shop() {
      return this.$store.state.shop
    }
  }
}
</script>
