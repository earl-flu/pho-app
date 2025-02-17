<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Components/Pagination.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
  prCategories: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
  },
});

const form = useForm({
  name: "",
});

let name = ref(props.filters.name);
const addCategoryModal = ref(null);

const submit = () => {
  form.post(route("categories.store"), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("PR Category Saved Successfully", {
        timeout: 3000,
      });
      form.reset();

      // Hide the modal - not working
      //   addCategoryModal.hide();
    },
  });
};

const debouncedFetch = debounce((name) => {
  router.get(
    route("categories.index"),
    { name },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
}, 300);

watch([name], (values) => {
  const [name] = values;
  debouncedFetch(name);
});
</script>

<template>
  <Head title="PR Categories" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">PR</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Categories</li>
      </ol>
    </nav>
    <!--end breadcrumb-->

    <div class="row">
      <div class="d-flex align-items-stretch">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <!-- Add new category -->
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div><h5 class="mb-0">Categories</h5></div>

              <div>
                <!-- Button trigger modal -->
                <button
                  type="button"
                  class="btn btn-primary px-4"
                  data-bs-toggle="modal"
                  data-bs-target="#addCategoryModal"
                >
                  <i class="bi bi-plus-lg me-2"></i>
                  Add New Category
                </button>
                <!-- Modal -->
                <div
                  class="modal fade"
                  id="addCategoryModal"
                  ref="addCategoryModal"
                  aria-hidden="true"
                  style="display: none"
                >
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form class="" @submit.prevent="submit">
                        <div class="modal-header border-bottom-0 py-2">
                          <h5 class="modal-title">Add Category</h5>
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
                            <label for="name" class="form-label">Name</label>
                            <input
                              type="text"
                              class="form-control"
                              v-model="form.name"
                              autocomplete="off"
                              autofocus
                              id="name"
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
                            Save
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
              </div>
            </div>
            <!-- End of add new category -->

            <!-- Search inputs -->
            <form class="row g-3 mb-5">
              <div class="col-md-4">
                <label for="name" class="form-label">Search</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  placeholder="Category Name"
                  v-model="name"
                  autocomplete="off"
                />
              </div>
            </form>
            <!-- End of search inputs -->

            <!-- Table -->
            <div class="table-responsive">
              <table class="table align-middle table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="category in prCategories.data" :key="category.id">
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ category.id }}</p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ category.name }}</p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-1">
                        <div class="dropdown">
                          <button
                            class="btn btn-sm dropdown-toggle dropdown-toggle-nocaret"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                          >
                            <i class="bi bi-three-dots"></i>
                          </button>
                          <ul class="dropdown-menu" style="">
                            <!-- <Link :href="route('tags.edit', tag)">
                              <a class="dropdown-item" href="javascript:;"
                                ><i class="bi bi-pencil-square me-2"></i>Edit</a
                              >
                            </Link> -->
                            <Link
                              :href="route('categories.items', category.id)"
                            >
                              <a class="dropdown-item" href="javascript:;">
                                <i class="bi bi-arrow-right-circle me-2"></i
                                >Next Level
                              </a>
                            </Link>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <Pagination :links="prCategories.links" />
            </div>
          </div>
          <!-- End of table -->
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
