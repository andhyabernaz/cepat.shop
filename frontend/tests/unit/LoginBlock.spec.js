import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import LoginBlock from 'components/LoginBlock.vue'

const QStub = {
   template: '<div><slot /></div>',
}

const QBtnStub = {
   template: '<button @click="$emit(\'click\')"><slot /></button>',
}

const QInputStub = {
   props: ['modelValue'],
   emits: ['update:modelValue'],
   template: '<input :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />',
}

const factory = ({ initialFormType = 'login', storeOverrides = {} } = {}) => {
   const store = {
      state: { loading: false, errors: {} },
      dispatch: vi.fn(() => Promise.resolve()),
      commit: vi.fn(),
      ...storeOverrides,
   }

   const wrapper = mount(LoginBlock, {
      props: { initialFormType },
      global: {
         mocks: { $store: store },
         stubs: {
            'q-card': QStub,
            'q-card-section': QStub,
            'q-separator': QStub,
            'q-banner': QStub,
            'q-icon': QStub,
            'q-btn': QBtnStub,
            'q-input': QInputStub,
         },
      },
   })

   return { wrapper, store }
}

describe('LoginBlock', () => {
   it('blocks submit with client-side validation errors (login)', async () => {
      const { wrapper, store } = factory({ initialFormType: 'login' })

      await wrapper.vm.submit()

      expect(store.dispatch).not.toHaveBeenCalled()
      expect(wrapper.vm.clientErrors.length).toBeGreaterThan(0)
   })

   it('dispatches user/login when form is valid (login)', async () => {
      const { wrapper, store } = factory({ initialFormType: 'login' })

      wrapper.vm.form.email = 'user@example.com'
      wrapper.vm.form.password = '123456'

      await wrapper.vm.submit()

      expect(store.dispatch).toHaveBeenCalledWith(
         'user/login',
         expect.objectContaining({
            email: 'user@example.com',
            password: '123456',
         })
      )
      expect(wrapper.emitted('onResponse')).toBeTruthy()
   })

   it('blocks submit when password confirmation mismatch (register)', async () => {
      const { wrapper, store } = factory({ initialFormType: 'register' })

      wrapper.vm.form.name = 'Nama'
      wrapper.vm.form.email = 'user@example.com'
      wrapper.vm.form.phone = '081234567890'
      wrapper.vm.form.password = '123456'
      wrapper.vm.form.password_confirmation = '654321'

      await wrapper.vm.submit()

      expect(store.dispatch).not.toHaveBeenCalled()
      expect(wrapper.vm.clientErrors.join(' ')).toContain('Konfirmasi password')
   })
})
