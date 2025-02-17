<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
  budgets: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
  },
});

let name = ref(props.filters.name);

const debouncedFetch = debounce((name) => {
  router.get(
    route("budgets.index"),
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
  <Head title="All Tags" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
      <div class="breadcrumb-title pe-3">Budgets</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item">
              <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">All</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
      <div class="d-flex align-items-stretch">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div class="">
                <h5 class="mb-0">All Budget</h5>
              </div>
            </div>

            <!-- Search inputs -->
            <form class="row g-3 mb-5">
              <div class="col-md-4">
                <label for="name" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  placeholder="Budget name"
                  v-model="name"
                  autocomplete="off"
                />
              </div>
            </form>
            <!-- End of search inputs -->

            <!-- Add paper -->
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div></div>
              <Link
                class="btn btn-primary px-4"
                :href="route('budgets.create')"
              >
                <i class="bi bi-plus-lg me-2"></i>Add New Budget
              </Link>
            </div>
            <!-- End of add paper -->

            <!-- Table -->
            <div class="table-responsive">
              <table class="table align-middle table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Budget Name</th>
                    <th>Amount</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="budget in budgets.data" :key="budget.id">
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ budget.name }}</p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ budget.amount }}</p>
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
                            <Link :href="route('budgets.editAmount', budget)">
                              <a class="dropdown-item" href="javascript:;"
                                ><i class="bi bi-pencil-square me-2"></i>Update Amount</a
                              >
                            </Link>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <Pagination :links="budgets.links" />
            </div>
            <!-- End of table -->
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
