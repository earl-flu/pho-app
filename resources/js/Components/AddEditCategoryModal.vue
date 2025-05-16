<script setup>
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { watch } from "vue";

const props = defineProps({
  mode: {
    type: String,
    default: "add",
  },
  category: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  name: "",
  id: null,
});

// Initialize form when category prop changes
watch(
  () => props.category,
  (newCategory) => {
    if (newCategory?.id) {
      form.name = newCategory.name;
      form.id = newCategory.id;
    }
  },
  { immediate: true }
);

const submit = () => {
  if (props.mode === "add") {
    form.post(route("categories.store"), {
      onSuccess: () => {
        handleSuccess("PR Category Saved Successfully");
      },
    });
  } else {
    form.put(route("categories.update", form.id), {
      onSuccess: () => {
        handleSuccess("PR Category Updated Successfully");
      },
    });
  }
};

const handleSuccess = (message) => {
  const toast = useToast();
  toast.success(message, {
    timeout: 3000,
  });
  form.reset();
  closeModal();
};

const closeModal = () => {
  const modal = document.getElementById(
    props.mode === "add" ? "addCategoryModal" : "editCategoryModal"
  );
  const bsModal = bootstrap.Modal.getInstance(modal);
  bsModal.hide();
};
</script>

<template>
  <div
    class="modal fade"
    :id="props.mode === 'add' ? 'addCategoryModal' : 'editCategoryModal'"
    aria-hidden="true"
    style="display: none"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="" @submit.prevent="submit">
          <div class="modal-header border-bottom-0 py-2">
            <h5 class="modal-title">
              {{ props.mode === "add" ? "Add" : "Edit" }} Category
            </h5>
            <a
              href="javascript:;"
              class="primaery-menu-close"
              data-bs-dismiss="modal"
            >
              <i class="material-icons-outlined">close</i>
            </a>
          </div>
          <div class="modal-body">
            <div class="col-md-12">
              <label :for="`${props.mode}-name`" class="form-label">Name</label>
              <input
                type="text"
                class="form-control"
                v-model="form.name"
                autocomplete="off"
                autofocus
                :id="`${props.mode}-name`"
              />
              <div class="invalid-feedback d-block">
                {{ form.errors.name }}
              </div>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button
              class="btn btn-grd-info"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              {{ props.mode === "add" ? "Save" : "Update" }}
            </button>
            <button
              type="button"
              class="btn btn-grd-danger"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template> 