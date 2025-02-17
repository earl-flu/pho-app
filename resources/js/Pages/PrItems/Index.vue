<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Components/Pagination.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
  },
  items: {
    type: Object,
    required: true,
  },
});

const form = useForm({
  name: "",
});

let name = ref(props.filters.name);
const addCategoryModal = ref(null);

const submit = () => {
  form.post(route("categories.items.store", props.category.id), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("PR Category Item Saved Successfully", {
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
    route("categories.items", { categoryId: props.category.id }),
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
  <Head :title="'PR - ' + category.name" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">PR</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <Link :href="route('categories.index')">Categories</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ category.name }}
        </li>
      </ol>
    </nav>
    <!--end breadcrumb-->

    <div class="row">
      <div class="d-flex align-items-stretch">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <!-- Add new item -->
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div>
                <h5 class="mb-0">{{ category.name }}</h5>
              </div>

              <div>
                <!-- Button trigger modal -->
                <button
                  type="button"
                  class="btn btn-primary px-4"
                  data-bs-toggle="modal"
                  data-bs-target="#addCategoryModal"
                >
                  <i class="bi bi-plus-lg me-2"></i>
                  Add New Item
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
                          <h5 class="modal-title">Add Item</h5>
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
            <!-- End of add new item -->

            <!-- Search inputs -->
            <form class="row g-3 mb-5">
              <div class="col-md-4">
                <label for="name" class="form-label">Search</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  placeholder="Item name"
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
                    <th>Item Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items.data" :key="item.id">
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ item.id }}</p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ item.name }}</p>
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
                            <Link
                              :href="
                                route('items.details.index', {
                                  categoryId: category.id,
                                  itemId: item.id,
                                })
                              "
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
              <Pagination :links="items.links" />
            </div>
          </div>
          <!-- End of table -->
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
