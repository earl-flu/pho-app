<script setup>
import { defineProps, defineEmits } from "vue";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

const props = defineProps({
  prSignatory: {
    type: Object,
  },
  form: {
    type: Object,
    required: true,
  },
  formStore: {
    type: Object,
    required: true,
  },
  itemsStore: {
    type: Object,
    required: true,
  },
  budgets: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(["submit"]);

const { numberWithComma } = useNumberWithComma();

const formatNumber = (value) => {
  const num = parseFloat(value);
  return isNaN(num) ? "0.00" : num.toFixed(2);
};
</script>

<template>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle table-hover">
          <thead class="table-dark">
            <tr>
              <th>Item Name</th>
              <th>Description</th>
              <th>Markup Price</th>
              <th>Qty</th>
              <th>UOM</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in itemsStore.items"
              :key="item.item_detail_id"
            >
              <td>
                <div class="d-flex align-items-center gap-3">
                  <p class="mb-0 fw-bold">{{ item.itemName }}</p>
                </div>
              </td>
              <td class="tiptap" v-html="item.description"></td>
              <td>
                <input
                  type="number"
                  class="form-control"
                  v-model="item.markup_price"
                  @input="
                    itemsStore.updateMarkupPrice(index, item.markup_price)
                  "
                  step="0.01"
                  min="0"
                />
              </td>
              <td>
                <input
                  type="number"
                  class="form-control"
                  v-model="item.quantity"
                  @input="itemsStore.updateQuantity(index, item.quantity)"
                  min="1"
                />
              </td>
              <td>{{ item.uom }}</td>
              <td>{{ numberWithComma(formatNumber(item.subtotal)) }}</td>
              <td>
                <button
                  @click="itemsStore.removeItem(index)"
                  class="btn btn-danger btn-sm"
                >
                  Remove
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="invalid-feedback d-block">
          {{ form.errors.items }}
        </div>
      </div>

      <form @submit.prevent="$emit('submit')" class="mt-5 row g-3">
        <div class="col-md-12">
          <h6>PR DETAILS</h6>
        </div>
        <div class="col-md-6">
          <label for="name" class="form-label">PR Name</label>
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            :value="formStore.name"
            @input="formStore.updateField('name', $event.target.value)"
            autofocus
            id="name"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.name }}
          </div>
        </div>
        <div class="col-md-6">
          <label for="budget" class="form-label">Select Budget</label>
          <select
            :value="formStore.budget_id"
            @change="formStore.updateField('budget_id', $event.target.value)"
            id="budget"
            class="form-select"
          >
            <option
              v-for="budget in budgets"
              :key="budget.id"
              :value="budget.id"
            >
              {{ budget.name }}
            </option>
          </select>
          <div class="invalid-feedback d-block">
            {{ form.errors.budget_id }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="number" class="form-label">PR Number</label>
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.number"
            autofocus
            id="number"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.number }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="status" class="form-label">Select Status</label>
          <select v-model="formStore.status" id="status" class="form-select">
            <option value="approved">Approved</option>
            <option value="pending">Pending</option>
            <option value="rejected">Rejected</option>
          </select>
          <div class="invalid-feedback d-block">
            {{ form.errors.status }}
          </div>
        </div>

        <div class="col-md-6" v-show="formStore.status === 'approved'">
          <label for="date_approved" class="form-label">Date Approved</label>
          <input
            autocomplete="off"
            type="datetime-local"
            step="any"
            class="form-control"
            v-model="formStore.date_approved"
            autofocus
            id="date_approved"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.date_approved }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="is_received" class="form-label">Is Received</label>
          <input
            class="form-control form-check-input"
            type="checkbox"
            id="is_received"
            :checked="formStore.is_received"
            v-model="formStore.is_received"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.is_received }}
          </div>
        </div>

        <div class="col-md-6" v-show="formStore.is_received">
          <label for="date_received" class="form-label">Date Received</label>
          <input
            autocomplete="off"
            type="datetime-local"
            step="any"
            class="form-control"
            v-model="formStore.date_received"
            autofocus
            id="date_received"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.date_received }}
          </div>
        </div>

        <div class="col-md-12">
          <label for="remarks" class="form-label">Remarks</label>
          <textarea
            class="form-control"
            name="remarks"
            id="remarks"
            cols="30"
            rows="3"
            v-model="formStore.remarks"
          ></textarea>
          <div class="invalid-feedback d-block">
            {{ form.errors.remarks }}
          </div>
        </div>

        <div class="col-md-12 mt-5">
          <h6>PR SIGNATORIES</h6>
        </div>
        <div class="col-md-6">
          <label for="requested_by" class="form-label">Requested by</label>
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.requested_by"
            autofocus
            id="requested_by"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.requested_by }}
          </div>
        </div>
        <div class="col-md-6">
          <label for="requested_by_position" class="form-label"
            >Requested by - Position</label
          >
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.requested_by_position"
            autofocus
            id="requested_by_position"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.requested_by_position }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="cash_availability" class="form-label"
            >Cash Availability</label
          >
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.cash_availability"
            autofocus
            id="cash_availability"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.cash_availability }}
          </div>
        </div>
        <div class="col-md-6">
          <label for="cash_availability_position" class="form-label"
            >Cash Availability - Position</label
          >
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.cash_availability_position"
            autofocus
            id="cash_availability_position"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.cash_availability_position }}
          </div>
        </div>

        <div class="col-md-6">
          <label for="approved_by" class="form-label">Approved by</label>
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.approved_by"
            autofocus
            id="approved_by"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.approved_by }}
          </div>
        </div>
        <div class="col-md-6">
          <label for="approved_by_position" class="form-label"
            >Approved by - Position</label
          >
          <input
            autocomplete="off"
            type="text"
            class="form-control"
            v-model="formStore.approved_by_position"
            autofocus
            id="approved_by_position"
          />
          <div class="invalid-feedback d-block">
            {{ form.errors.approved_by_position }}
          </div>
        </div>

        <button
          type="submit"
          class="btn btn-primary mt-4"
          :disabled="form.processing"
        >
          Save Purchase Request
        </button>
      </form>
    </div>
  </div>
</template>