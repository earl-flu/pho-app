<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Components/Pagination.vue";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

const props = defineProps({
  purchaseRequests: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
  },
});

let name = ref(props.filters.name);
let status = ref(props.filters.status || "all");
let receivedOnly = ref(props.filters.receivedOnly || 0);

const { numberWithComma } = useNumberWithComma();

function formattedDate(createdAt) {
  if (!createdAt) return "-";
  const date = new Date(createdAt);
  return new Intl.DateTimeFormat("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  }).format(date);
}

const debouncedFetch = debounce((name, status, receivedOnly) => {
  router.get(
    route("purchase-requests.index"),
    { name, status, receivedOnly },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
}, 300);

watch([name, status, receivedOnly], (values) => {
  const [name, status, receivedOnly] = values;
  debouncedFetch(name, status, receivedOnly);
});
</script>

<template>
  <Head title="All Purchase Requests" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-4">
      <div class="breadcrumb-title pe-3">Purchase Requests</div>
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
                <h5 class="mb-0">All Purchase Requests</h5>
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
                  placeholder="Name"
                  v-model="name"
                  autocomplete="off"
                />
              </div>

              <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select id="status" class="form-select" v-model="status">
                  <option value="all">All</option>
                  <option value="approved">Approved</option>
                  <option value="pending">Pending</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="receivedOnly" class="form-label"
                  >Received Only</label
                >
                <input
                  class="form-check-input form-control"
                  type="checkbox"
                  value=""
                  id="receivedOnly"
                  v-model="receivedOnly"
                />
              </div>
            </form>
            <!-- End of search inputs -->

            <!-- Add paper -->
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div></div>
              <Link
                class="btn btn-primary px-4"
                :href="route('purchase-requests.create')"
              >
                <i class="bi bi-plus-lg me-2"></i>Add New PR
              </Link>
            </div>
            <!-- End of add paper -->

            <!-- Table -->
            <div class="table-responsive">
              <table class="table align-middle table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>PR Name</th>
                    <th>Total Amount</th>
                    <th>Budget name</th>
                    <th>Status</th>
                    <th>Is Received</th>
                    <th>Received Date</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pr in purchaseRequests.data" :key="pr.id">
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ pr.name }}</p>
                      </div>
                    </td>
                    <td>
                      <p class="mb-0 fw-bold">
                        ₱ {{ numberWithComma(pr.totalAmount) }}
                      </p>
                    </td>
                    <td>
                      <p class="mb-0 fw-bold">{{ pr.budgetName }}</p>
                    </td>
                    <td>
                      <p class="mb-0 fw-bold">{{ pr.status }}</p>
                    </td>
                    <td>
                      <p class="mb-0 fw-bold">
                        {{ pr.is_received ? "Yes" : "No" }}
                      </p>
                    </td>
                    <td>
                      <p class="mb-0 fw-bold">
                        {{ formattedDate(pr.date_received) }}
                      </p>
                    </td>
                    <td>
                      <p class="mb-0 fw-bold">
                        {{ formattedDate(pr.created_at) }}
                      </p>
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
                            <Link :href="route('purchase-requests.edit', pr)">
                              <a class="dropdown-item" href="javascript:;"
                                ><i class="bi bi-pencil-square me-2"></i>Edit</a
                              >
                            </Link>
                            <Link :href="route('purchase-requests.show', pr)">
                              <a class="dropdown-item" href="javascript:;"
                                ><i class="bi bi-eye me-2"></i>View</a
                              >
                            </Link>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <Pagination :links="purchaseRequests.links" />
            </div>
            <!-- End of table -->
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
