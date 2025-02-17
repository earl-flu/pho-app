<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const form = useForm({
  name: "",
  amount: 0,
  remarks: "",
});

const submit = () => {
  form.post(route("budgets.store"), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("Budget Saved Successfully", {
        timeout: 3000,
      });
    },
  });
};
</script>

<template>
  <Head title="Add Tag" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Budget</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
              <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Add</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
      <div class="col-12 col-xl-6">
        <div class="card">
          <div class="card-body p-4">
            <h5 class="mb-4">Budget Form</h5>
            <form class="row g-3" @submit.prevent="submit">
              <div class="col-md-12">
                <label for="name" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.name"
                  autofocus
                  id="name"
                />
                <div class="invalid-feedback d-block">
                  {{ form.errors.name }}
                </div>
              </div>

              <div class="col-md-12">
                <label for="amount" class="form-label">Amount</label>
                <input
                  type="number"
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
                <label for="remarks" class="form-label">Remarks</label>
                <textarea
                  name="remarks"
                  id="remarks"
                  v-model="form.remarks"
                  cols="30"
                  rows="5"
                  class="form-control"
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
