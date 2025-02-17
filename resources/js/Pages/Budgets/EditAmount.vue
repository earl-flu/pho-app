<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { computed } from "vue";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

const { numberWithComma } = useNumberWithComma();

const props = defineProps({
  budget: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  operation: "subtract",
  amount: 0,
  remarks: "",
});

const newAmount = computed(() => {
  const operation = form.operation;
  const currentBudgetAmount = parseFloat(props.budget.amount) || 0;
  const amount = parseFloat(form.amount) || 0;

  if (operation === "add") {
    return currentBudgetAmount + amount;
  }

  if (operation === "subtract") {
    return currentBudgetAmount - amount;
  }

  return currentBudgetAmount;
});

const submit = () => {
  console.log('hey')
  form.put(route("budgets.updateAmount", props.budget), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("Budget Amount Updated Successfully", {
        timeout: 3000,
      });
    },
  });
};
</script>

<template>
  <Head title="Edit Budget" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">Budgets</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <Link :href="route('budgets.index')">All</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ budget.name }}
          <span class="text-secondary">({{ budget.id }})</span>
        </li>
      </ol>
    </nav>
    <!--end breadcrumb-->

    <div class="row mt-5">
      <div class="col-12 col-xl-6">
        <div class="card">
          <div class="card-body p-4">
            <h5 class="mb-4">Edit Tag Form</h5>
            <form class="row g-3" @submit.prevent="submit">
              <div class="col-md-12">
                <label for="name" class="form-label">Budget Name</label>
                <p>{{ budget.name }}</p>
              </div>

              <div class="col-md-12">
                <label for="name" class="form-label">Current Amount</label>
                <p>₱ {{ numberWithComma(budget.amount) }}</p>
              </div>

              <div class="col-md-12">
                <label for="operation" class="form-label">Operation</label>
                <select
                  class="form-control"
                  name="operation"
                  id="operation"
                  v-model="form.operation"
                >
                  <option value="add">Add</option>
                  <option value="subtract">Subtract</option>
                </select>
                <div class="invalid-feedback d-block">
                  {{ form.errors.operation }}
                </div>
              </div>

              <div class="col-md-12">
                <label for="amount" class="form-label">Amount</label>
                <input
                  autocomplete="off"
                  type="text"
                  class="form-control"
                  v-model="form.amount"
                  autofocus
                  id="amount"
                />
                <div class="invalid-feedback d-block">
                  {{ form.errors.amount }}
                </div>
              </div>

              <div class="col-md-12">
                <label for="name" class="form-label">New Amount</label>
                <p>₱ {{ numberWithComma(newAmount) }}</p>
              </div>

              <div class="col-md-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea
                  class="form-control"
                  v-model="form.remarks"
                  name="remarks"
                  id="remarks"
                  cols="10"
                  rows="3"
                ></textarea>

                <div class="invalid-feedback d-block">
                  {{ form.errors.remarks }}
                </div>
              </div>

              <div class="col-md-12 mt-4">
                <div class="d-md-flex d-grid align-items-center gap-3">
                  <button
                    class="btn btn-grd btn-grd-primary px-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                  >
                    Save
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
