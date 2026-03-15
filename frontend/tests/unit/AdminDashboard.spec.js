import { mount } from '@vue/test-utils'
import { describe, it, expect, vi } from 'vitest'
import AdminDashboard from 'pages/Dashboard/AdminDashboard.vue'

vi.mock('boot/axios', () => {
   return {
      BaseApi: {
         get: vi.fn(),
      },
   }
})

const QStub = {
   template: '<div><slot /></div>',
}

const QBtnStub = {
   template: '<button @click="$emit(\'click\')"><slot /></button>',
}

const flushPromises = () => new Promise((resolve) => setTimeout(resolve, 0))

describe('AdminDashboard', () => {
   it('fetches dashboard data exactly once and renders critical widgets', async () => {
      const { BaseApi } = await import('boot/axios')
      BaseApi.get.mockReset()
      BaseApi.get
        .mockResolvedValueOnce({
         data: {
            data: {
               today_orders_total: 3,
               weekly_sales_total: 150000,
               weekly_sales_labels: ['01 Jan', '02 Jan', '03 Jan', '04 Jan', '05 Jan', '06 Jan', '07 Jan'],
               weekly_sales_series: [0, 20000, 30000, 0, 50000, 0, 50000],
            },
         },
        })
        .mockResolvedValueOnce({
          data: {
            data: {
              data: [
                { id: 1, order_ref: 'INV-001', order_status: 'PENDING', customer_name: 'A', order_total: 10000, payment_fee: 0, created_at: '2025-01-01T10:00:00Z' },
                { id: 2, order_ref: 'INV-002', order_status: 'COMPLETE', customer_name: 'B', order_total: 20000, payment_fee: 0, created_at: '2025-01-02T10:00:00Z' },
              ],
              from: 1,
              current_page: 1,
              path: '/api/orders',
            },
          },
        })

      const wrapper = mount(AdminDashboard, {
         global: {
            mocks: {
               $store: { commit: vi.fn() },
            },
            stubs: {
               'q-page': QStub,
               'q-linear-progress': QStub,
               'q-banner': QStub,
               'q-card': QStub,
               'q-card-section': QStub,
               'q-skeleton': QStub,
               'q-separator': QStub,
               'q-badge': QStub,
               'q-btn': QBtnStub,
               'q-btn-toggle': QStub,
            },
         },
      })

      wrapper.vm.observeWidgets()
      wrapper.vm.observeOrders()
      await flushPromises()

      expect(BaseApi.get).toHaveBeenCalledTimes(2)
      expect(BaseApi.get).toHaveBeenNthCalledWith(1, 'adminReports?period=weekly&mode=widgets', { silent: false })
      expect(BaseApi.get).toHaveBeenNthCalledWith(2, 'orders?per_page=5', { silent: true })

      expect(wrapper.get('[data-test="today-orders"]').text()).toContain('3')
      expect(wrapper.get('[data-test="weekly-sales"]').text()).toContain('Rp 150.000')

      expect(wrapper.text()).toContain('INV-001')
      expect(wrapper.text()).toContain('INV-002')
   })
})
