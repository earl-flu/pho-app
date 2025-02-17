import { defineStore } from 'pinia'

export const usePurchaseRequestFormStore = defineStore('form', {
  state: () => ({
    budget_id: '',
    status: 'pending',
    remarks: '',
    name: '',
    number: '',
    date_approved: null,
    is_received: false,
    date_received: null,
    requested_by: '',
    requested_by_position: '',
    cash_availability: '',
    cash_availability_position: '',
    approved_by: '',
    approved_by_position: '',
  }),

  actions: {
    updateField(field, value) {
      this[field] = value
    },
    resetForm() {
      this.budget_id = ''
      this.status = 'pending'
      this.remarks = ''
      this.name = ''
      this.number = ''
      this.date_approved = null
      this.is_received = false
      this.date_received = null
    },

    setSignatory(data) {
      this.requested_by = data.requested_by || '';
      this.requested_by_position = data.requested_by_position || '';
      this.cash_availability = data.cash_availability || '';
      this.cash_availability_position = data.cash_availability_position || '';
      this.approved_by = data.approved_by || '';
      this.approved_by_position = data.approved_by_position || '';
    },

    initializeForm(data) {
      this.budget_id = data.budget_id || '';
      this.status = data.status || 'pending';
      this.remarks = data.remarks || '';
      this.name = data.name || '';
      this.number = data.number || '';
      this.date_approved = data.date_approved || null;
      this.is_received = data.is_received || false;
      this.date_received = data.date_received || null;
      this.requested_by = data.requested_by || '';
      this.requested_by_position = data.requested_by_position || '';
      this.cash_availability = data.cash_availability || '';
      this.cash_availability_position = data.cash_availability_position || '';
      this.approved_by = data.approved_by || '';
      this.approved_by_position = data.approved_by_position || '';
    },
  }
})