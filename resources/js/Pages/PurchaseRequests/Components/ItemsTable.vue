<!-- ItemsTable.vue -->
<script setup>
import { defineProps, defineEmits } from "vue";
import Pagination from "@/Components/Pagination.vue";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

const props = defineProps({
  items: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["addItem"]);

const { numberWithComma } = useNumberWithComma();

const addItem = (item) => {
  emit("addItem", item);
};
</script>

<template>
  <div class="table-responsive">
    <table class="table align-middle table-hover">
      <thead class="table-dark">
        <tr>
          <th>Item Name</th>
          <th>Description</th>
          <th>UOM</th>
          <th>Markup Price</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items.data" :key="item.id" @click="addItem(item)" class="cursor-pointer">
          <td>
            <div class="d-flex align-items-center gap-3">
              <p class="mb-0 fw-bold">{{ item.itemName }}</p>
            </div>
          </td>
          <td v-html="item.description"></td>
          <td>
            <p class="mb-0">{{ item.uom }}</p>
          </td>
          <td>
            <p class="mb-0">{{ numberWithComma(item.markup_price) }}</p>
          </td>
        </tr>
      </tbody>
    </table>
    <Pagination :links="items.links" />
  </div>
</template>