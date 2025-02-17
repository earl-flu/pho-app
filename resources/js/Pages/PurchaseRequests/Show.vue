<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { debounce } from "lodash";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

const props = defineProps({
  purchaseRequest: {
    type: Object,
    required: true,
  },
  items: {
    type: Array,
    required: true,
  },
  totalPrice: {
    type: String,
    required: true,
  },
  filters: {
    type: Object,
  },
});

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
</script>

<template>
  <Head title="All Purchase Requests" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">Purchase Request</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <Link :href="route('purchase-requests.index')">All</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Show</li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ purchaseRequest.name }}
        </li>
      </ol>
    </nav>
    <!--end breadcrumb-->

    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <p>Total Price</p>
            <h4>₱ {{ numberWithComma(totalPrice) }}</h4>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <p>Budget</p>
            <h4>{{ purchaseRequest.name }}</h4>
          </div>
        </div>
      </div>
      <div class="d-flex align-items-stretch">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div class="">
                <!-- <h5 class="mb-0">Show Purchase Request</h5> -->
              </div>
            </div>

            <!-- Add paper -->
            <div class="d-flex align-items-start mb-4" style="gap:10px;">
              <div style="flex: 1;"></div>
              <Link
                class="btn btn-primary px-4"
                :href="route('purchase-requests.create')"
              >
                <i class="bi bi-plus-lg me-2"></i>Add New PR
              </Link>
              <a
                class="btn btn-primary px-4"
                target="_blank"
                :href="route('print.purchase-request', purchaseRequest)"
              >
                <i class="bi bi-printer me-2"></i>Print
              </a>
            </div>
            <!-- End of add paper -->

            <!-- Table -->
            <div class="table-responsive">
              <table class="table align-middle table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Item Name</th>
                    <th>Details</th>
                    <th>Markup Price</th>
                    <th>Quantity</th>
                    <th>UOM</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in items" :key="item.id">
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ item.item_name }}</p>
                      </div>
                    </td>
                    <td>
                      <p class="mb-0">
                        {{ item.item_detail.description }}
                      </p>
                    </td>
                    <td>
                      <p class="mb-0">
                        ₱ {{ numberWithComma(item.markup_price) }}
                      </p>
                    </td>
                    <td>
                      <p class="mb-0">{{ item.quantity }}</p>
                    </td>
                    <td>
                      <p class="mb-0">{{ item.uom }}</p>
                    </td>
                    <td>
                      <p class="mb-0">₱ {{ numberWithComma(item.subtotal) }}</p>
                    </td>
                  </tr>
                  <tr class="table-dark">
                    <td>
                      <p class="mb-0 fw-bold">TOTAL</p>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="">
                      <p class="mb-0 fw-bold">
                        ₱ {{ numberWithComma(totalPrice) }}
                      </p>
                    </td>
                  </tr>
                </tbody>
                <!-- 
Status = status "approved"
Is Received = is_received, 1 or 2
Received date = date_received
Approved date = date_approved
PR name = name
Budget name = budget_name
Remaining budget = budget_remaining
Created Date = created_at
 -->
              </table>
            </div>
            <!-- End of table -->
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
