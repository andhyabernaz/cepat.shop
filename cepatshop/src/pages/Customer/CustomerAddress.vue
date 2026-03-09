<template>
   <q-page padding>
      <AppHeader title="Daftar Alamat">
         <q-btn color="primary" size="13px" @click="handleAddAddress" icon="add" label="Alamat" />
      </AppHeader>
      <div class="box-column flat">
         <div class="table-responsive">
            <table class="table aligned bordered">
               <thead>

                  <tr>
                     <th>Alamat</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>

                  <tr v-for="(item, index) in user_address" :key="index">
                     <td>
                        <q-item-label class="text-sm text-weight-medium q-mb-sm">
                           {{ item.title }}
                           <q-badge v-if="item.is_primary" class="q-ml-xs" color="green">Utama</q-badge></q-item-label>
                        <div class="text-grey-8">
                           <q-item-label>{{ item.receiver_name }}</q-item-label>
                           <q-item-label>{{ item.receiver_phone }}</q-item-label>
                           <q-item-label v-if="item.full_address">
                              {{ item.full_address }}
                           </q-item-label>
                           <q-item-label v-if="!item.is_complete">
                              <q-badge color="red-4" outline>incomplete</q-badge></q-item-label>

                        </div>
                     </td>
                     <td class="q-gutter-x-sm">
                        <q-btn @click="handleDeleteAddress(item.id)" icon="delete" color="red" round size="11px"
                           padding="6px"></q-btn>
                        <q-btn @click="handleEditAddress(item)" icon="edit" color="blue" round size="11px"
                           padding="6px"></q-btn>
                     </td>
                  </tr>
               </tbody>
            </table>

         </div>
         <div class="text-center q-py-lg" v-if="!user_address.length">
            Tidak ada data
         </div>
      </div>
      <UserAddressForm ref="userAddressForm" />
   </q-page>
</template>

<script>
import UserAddressForm from "src/components/UserAddressForm.vue";
export default {
   components: { UserAddressForm },

   computed: {
      user() {
         return this.$store.state.user.user;
      },
      user_address() {
         return this.$store.state.user.address;
      },
   },
   mounted() {
      this.getData()
   },
   methods: {
      getData() {
         this.$store.commit("SET_LOADING", true);
         this.$store.dispatch("user/getUserAddress").then(res => {
            this.$store.commit('user/SET_USER_ADDRESS', res.data.data)
         })
      },
      getUser() {
         this.$store.dispatch("user/getUser");
      },
      handleAddAddress() {
         this.$refs.userAddressForm.handleAddAddress();
      },
      handleEditAddress(item) {
         this.$refs.userAddressForm.handleEditAddress(item);
      },
      handleDeleteAddress(id) {
         this.$refs.userAddressForm.handleDeleteAddress(id);
      },
   },
};
</script>
