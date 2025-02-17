<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { debounce } from "lodash";

const props = defineProps({
  prSignatory: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <Head title="Pr Signatories" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex mb-4">
      <div class="breadcrumb-title pe-3">Pr Signatories</div>
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
                <!-- <h5 class="mb-0">All Signatories</h5> -->
              </div>
            </div>

            <!-- Add signatory -->
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div></div>
              <Link
                v-show="!prSignatory"
                class="btn btn-primary px-4"
                :href="route('pr-signatories.create')"
              >
                <i class="bi bi-plus-lg me-2"></i>Add New Signatory
              </Link>
            </div>
            <!-- End of add signatory -->

            <!-- Table -->
            <div class="table-responsive">
              <table class="table align-middle table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Requested By</th>
                    <th>Cash Availability</th>
                    <th>Approved By</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="d-flex flex-column">
                        <p class="mb-0 fw-bold mt-2">
                          {{ prSignatory.requested_by }}
                        </p>
                        <p class="mb-2">
                          {{ prSignatory.requested_by_position }}
                        </p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column">
                        <p class="mb-0 fw-bold mt-2">
                          {{ prSignatory.cash_availability }}
                        </p>
                        <p class="mb-2">
                          {{ prSignatory.cash_availability_position }}
                        </p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column">
                        <p class="mb-0 fw-bold mt-2">
                          {{ prSignatory.approved_by }}
                        </p>
                        <p class="mb-2">
                          {{ prSignatory.approved_by_position }}
                        </p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column gap-1">
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
                              :href="route('pr-signatories.edit', prSignatory)"
                            >
                              <a class="dropdown-item" href="javascript:;"
                                ><i class="bi bi-pencil-square me-2"></i>Edit</a
                              >
                            </Link>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- End of table -->
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
