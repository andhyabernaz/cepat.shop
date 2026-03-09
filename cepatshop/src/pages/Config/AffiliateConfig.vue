<template>
  <q-card class="section shadow">
    <q-card-section>
      <div class="card-title">
        Affiliate Config
      </div>
      <q-form @submit.prevent="updateDate">
        <div>
          <q-list separator padding>
            <q-item class="q-px-xs">
              <q-item-section>
                <q-item-label>Status Affiliasi</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-toggle v-model="form.is_active" left-label
                  color="teal" />
              </q-item-section>
            </q-item>
            <q-item class="q-px-xs">
              <q-item-section>
                <q-item-label>User Auto Active</q-item-label>
                <q-item-label caption>Status langsung aktif setelah pendaftaran</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-toggle v-model="form.is_auto_active" left-label
                  color="teal" />
              </q-item-section>
            </q-item>
            <q-item class="q-px-xs">
              <q-item-section>
                <q-input v-model="form.ttl" type="number" min="1" label="Masa aktif cookie (days)" suffix="DAYS" />

              </q-item-section>
            </q-item>
          </q-list>
          <div class="q-gutter-y-lg q-pt-sm">
            <div>
              <div class="card-subtitle">
                <q-item-label>Deskripsi</q-item-label>
                <q-item-label caption>Pesan akan ditampikan di halaman pendaftaran</q-item-label>
              </div>
              <ContentEditor v-model="form.description" />
            </div>
            <div>
               <div class="card-subtitle">
                <q-item-label>Welcome Message</q-item-label>
                <q-item-label caption>Pesan akan ditampikan di halaman akun belum aktif</q-item-label>
              </div>
              <ContentEditor v-model="form.welcome_message" />
            </div>
            <div>
              <div class="card-subtitle">
                <q-item-label>Suspended Message</q-item-label>
                <q-item-label caption>Pesan akan ditampikan di halaman akun suspended</q-item-label>
              </div>
              
              <ContentEditor v-model="form.suspend_message" />
            </div>
          </div>
        </div>
        <div class="q-mt-md">
          <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
        </div>
      </q-form>
    </q-card-section>
  </q-card>
</template>

<script>
import { BaseApi } from 'boot/axios'
import ContentEditor from 'src/components/ContentEditor.vue'
export default {
  components: { ContentEditor },
  data() {
    return {
      form: {
        description: '',
        ttl: '',
        is_active: false,
        is_auto_active: false,
        welcome_message: '',
        suspend_message: ''
      },
    }
  },
  mounted() {
    this.getConfig()
  },
  methods: {
    setConfig(data) {
      this.form = { ...data }
    },
    getConfig() {
      BaseApi.get('affiliate-config').then(res => {
        this.setConfig(res.data.data)
      })
    },
    updateDate() {
      BaseApi.post('affiliate-config', this.form).then(() => {
        this.$q.notify({
          type: 'positive',
          message: 'Berhasil update data'
        })
      })
    },
  }
}
</script>