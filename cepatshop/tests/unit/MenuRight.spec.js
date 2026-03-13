import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import { createStore } from 'vuex'
import MenuRight from 'components/MenuRight.vue'

const QStub = {
   template: '<div><slot /></div>',
}

const QBtnStub = {
   template: '<button @click="$emit(\'click\')"><slot /></button>',
}

const QDialogStub = {
   props: ['modelValue'],
   emits: ['update:modelValue'],
   template: '<div><slot /></div>',
}

describe('MenuRight', () => {
   it('opens auth dialog in login mode from header login button', async () => {
      const store = createStore({
         state: {
            page_width: 1200,
         },
         getters: {
            isModeDesktop: () => true,
         },
         modules: {
            product: {
               namespaced: true,
               getters: { favoriteCount: () => 0 },
            },
            cart: {
               namespaced: true,
               getters: { cartCount: () => 0 },
            },
            user: {
               namespaced: true,
               state: () => ({ user: null, token: null }),
               actions: { logout: vi.fn() },
            },
         },
      })

      const wrapper = mount(MenuRight, {
         global: {
            plugins: [store],
            mocks: {
               $route: { query: {} },
               $router: { push: vi.fn() },
            },
            directives: {
               'close-popup': () => {},
            },
            stubs: {
               ShareButton: QStub,
               LoginBlock: QStub,
               'q-btn': QBtnStub,
               'q-badge': QStub,
               'q-menu': QStub,
               'q-list': QStub,
               'q-item': QStub,
               'q-item-section': QStub,
               'q-dialog': QDialogStub,
            },
         },
      })

      expect(wrapper.vm.authDialog).toBe(false)
      await wrapper.get('[data-test="nav-login"]').trigger('click')
      expect(wrapper.vm.authDialog).toBe(true)
      expect(wrapper.vm.authFormType).toBe('login')
   })
})
