// resources/js/Stores/useItemsStore.js
import { defineStore } from 'pinia';

export const useItemsStore = defineStore('items', {
  state: () => ({
    items: []
  }),
  actions: {
    addItem(item) {
      const exists = this.items.some((existingItem) => existingItem.item_detail_id === item.id);
      if (exists) return;
      this.items.push({
        item_detail_id: item.id,
        itemName: item.itemName,
        description: item.description,
        markup_price: parseFloat(item.markup_price) || 0,
        uom: item.uom,
        quantity: 1,
        subtotal: parseFloat(item.markup_price) || 0,
      });
    },

    removeItem(index) {
      this.items.splice(index, 1);
    },

    updateQuantity(index, quantity) {
      if (index >= 0 && index < this.items.length) {
        this.items[index].quantity = parseInt(quantity) || 0;
        this.updateTotalPrice(index);
      }
    },

    updateMarkupPrice(index, price) {
      if (index >= 0 && index < this.items.length) {
        this.items[index].markup_price = parseFloat(price) || 0;
        this.updateTotalPrice(index);
      }
    },

    updateTotalPrice(index) {
      if (index >= 0 && index < this.items.length) {
        const item = this.items[index];
        item.subtotal = (item.markup_price * item.quantity) || 0;
      }
    },

    setItems(newItems) {
      this.items = newItems.map(item => ({
        item_detail_id: item.item_detail_id,
        itemName: item.item_name,
        description: item.item_detail.description,
        markup_price: parseFloat(item.markup_price),
        uom: item.uom,
        quantity: parseInt(item.quantity),
        subtotal: parseFloat(item.markup_price) * parseInt(item.quantity)
      }));
    },

    clearItems() {
      this.items = [];
    },
  },
  getters: {
    totalItems: (state) => state.items.length,
    totalPrice: (state) => state.items.reduce((sum, item) => sum + (item.subtotal || 0), 0),
  }
});