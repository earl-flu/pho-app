<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const props = defineProps({
  permissions: Array,
});

const form = useForm({
  name: "",
  permissions: [],
});

const submit = () => {
  form.post(route("roles.store"), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("Role Saved Successfully", {
        timeout: 3000,
      });
    },
  });
};
</script>

<template>
  <Head title="Create Role" />

  <AuthenticatedLayout>
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Roles</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
              <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Create Role</li>
          </ol>
        </nav>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-xl-6">
        <div class="card">
          <div class="card-body p-4">
            <h5 class="mb-4">Create Role Form</h5>
            <form class="row g-3" @submit.prevent="submit">
              <div class="col-md-12">
                <label for="name" class="form-label">Role Name</label>
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
                <label class="form-label">Permissions</label>
                <div v-for="permission in props.permissions" :key="permission.id" class="form-check">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    :value="permission.id"
                    v-model="form.permissions"
                    :id="'permission-' + permission.id"
                  />
                  <label :for="'permission-' + permission.id" class="form-check-label">
                    {{ permission.name }}
                  </label>
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
