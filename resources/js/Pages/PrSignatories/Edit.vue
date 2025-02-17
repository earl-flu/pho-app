<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import { useToast } from "vue-toastification";

const props = defineProps({
  prSignatory: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  requested_by: props.prSignatory.requested_by,
  cash_availability: props.prSignatory.cash_availability,
  approved_by: props.prSignatory.approved_by,
  requested_by_position: props.prSignatory.requested_by_position,
  cash_availability_position: props.prSignatory.cash_availability_position,
  approved_by_position: props.prSignatory.approved_by_position,
});

const submit = () => {
  form.put(route("pr-signatories.update", props.prSignatory), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("Pr Signatory Updated Successfully", {
        timeout: 3000,
      });
    },
  });
};

</script>

<template>
  <Head title="Add Signatory" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Signatories</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
              <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
      <div class="col-12 col-xl-6">
        <div class="card">
          <div class="card-body p-4">
            <h5 class="mb-4">Signatory Form</h5>
            <form class="row g-3" @submit.prevent="submit">
              <div class="col-md-6">
                <label for="requested_by" class="form-label"
                  >Requested By</label
                >
                <input
                  type="text"
                  class="form-control"
                  v-model="form.requested_by"
                  autofocus
                  id="requested_by"
                />
                <div class="invalid-feedback d-block">
                  {{ form.errors.requested_by }}
                </div>
              </div>
              <div class="col-md-6">
                <label for="requested_by_position" class="form-label"
                  >Requested By - Position</label
                >
                <input
                  type="text"
                  class="form-control"
                  v-model="form.requested_by_position"
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
                  type="text"
                  class="form-control"
                  v-model="form.cash_availability"
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
                  type="text"
                  class="form-control"
                  v-model="form.cash_availability_position"
                  autofocus
                  id="cash_availability_position"
                />
                <div class="invalid-feedback d-block">
                  {{ form.errors.cash_availability_position }}
                </div>
              </div>
              <div class="col-md-6">
                <label for="approved_by" class="form-label">Approved By</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.approved_by"
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
                  type="text"
                  class="form-control"
                  v-model="form.approved_by_position"
                  autofocus
                  id="approved_by_position"
                />
                <div class="invalid-feedback d-block">
                  {{ form.errors.approved_by_position }}
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
