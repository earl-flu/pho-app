<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, watch, computed, onMounted, onUnmounted } from "vue";
import { Head, Link, useForm, router, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { debounce } from "lodash";
import ItemsTable from "./Components/ItemsTable.vue";
import PurchaseRequestForm from "./Components/PurchaseRequestForm.vue";
import { useItemsStore } from "@/Stores/useItemsStore";
import { usePurchaseRequestFormStore } from "@/Stores/usePurchaseRequestFormStore";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

const props = defineProps({
  purchaseRequest: {
    type: Object,
    required: true,
  },
  items: {
    type: Object,
    required: true,
  },
  budgets: {
    type: Array,
    required: true,
  },
  filters: {
    type: Object,
  },
});

const itemsStore = useItemsStore();
const $page = usePage();
const formStore = usePurchaseRequestFormStore();
const { numberWithComma } = useNumberWithComma();

const error = computed(() => $page.props.errors);

// Initialize stores with existing data
onMounted(() => {
  itemsStore.setItems(props.purchaseRequest.items);
  formStore.initializeForm({
    budget_id: props.purchaseRequest.budget_id,
    status: props.purchaseRequest.status,
    remarks: props.purchaseRequest.remarks,
    name: props.purchaseRequest.name,
    number: props.purchaseRequest.number,
    date_approved: props.purchaseRequest.date_approved,
    is_received: props.purchaseRequest.is_received,
    date_received: props.purchaseRequest.date_received,
    requested_by: props.purchaseRequest.requested_by,
    requested_by_position: props.purchaseRequest.requested_by_position,
    cash_availability: props.purchaseRequest.cash_availability,
    cash_availability_position: props.purchaseRequest.cash_availability_position,
    approved_by: props.purchaseRequest.approved_by,
    approved_by_position: props.purchaseRequest.approved_by_position,
  });
});

onUnmounted(() => {
  itemsStore.clearItems();
  formStore.resetForm();
});

const form = useForm({
  items: computed(() => itemsStore.items),
  budget_id: computed(() => formStore.budget_id),
  status: computed(() => formStore.status),
  remarks: computed(() => formStore.remarks),
  name: computed(() => formStore.name),
  number: computed(() => formStore.number),
  date_approved: computed(() => formStore.date_approved),
  is_received: computed(() => formStore.is_received),
  date_received: computed(() => formStore.date_received),
  requested_by: computed(() => formStore.requested_by),
  requested_by_position: computed(() => formStore.requested_by_position),
  cash_availability: computed(() => formStore.cash_availability),
  cash_availability_position: computed(
    () => formStore.cash_availability_position
  ),
  approved_by: computed(() => formStore.approved_by),
  approved_by_position: computed(() => formStore.approved_by_position),
});

const searchItemName = ref(props.filters.searchItemName || "");

const selectedBudgetAmount = computed(() => {
  const oldBudgetId = props.purchaseRequest.budget_id;
  const oldTotalAmount = props.purchaseRequest.items.reduce(
    (sum, item) => sum + (parseFloat(item.subtotal) || 0),
    0
  );
  const oldStatus = props.purchaseRequest.status;
  const id = formStore.budget_id;

  const selectedBudget = props.budgets.find((budg) => budg.id == id);
  // Ensure selectedBudget is defined before accessing its amount property
  if (!selectedBudget) {
    console.error("Selected budget not found.");
    return 0; // Return a default value or handle the error as needed
  }
  console.log(oldStatus);
  if (oldBudgetId == form.budget_id && oldStatus == "approved") {
    return parseFloat(oldTotalAmount) + parseFloat(selectedBudget.amount);
  }

  return selectedBudget.amount;
});

const debouncedFetch = debounce((searchItemName) => {
  router.get(
    route("purchase-requests.edit", props.purchaseRequest.id),
    { searchItemName },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
}, 300);

watch([searchItemName], (values) => {
  const [searchItemName] = values;
  debouncedFetch(searchItemName);
});

const submitForm = () => {
  form.put(route("purchase-requests.update", props.purchaseRequest.id), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      useToast().success("Purchase Request Updated Successfully", {
        timeout: 3000,
      });
    },
  });
};

const formatNumber = (value) => {
  const num = parseFloat(value);
  return isNaN(num) ? "0.00" : num.toFixed(2);
};
</script>

<template>
  <Head title="Edit Purchase Request" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">Puchase Request ({{ purchaseRequest.name }})</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <Link :href="route('purchase-requests.index')">All</Link>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
        <li class="breadcrumb-item active" aria-current="page">
          {{ purchaseRequest.name }}
        </li>
      </ol>
    </nav>
    <!--end breadcrumb-->

    <!-- Table -->
    <div class="row g-4">
      <div class="col-md-5">
        <div class="card">
          <div class="card-body">
            <!-- Search inputs -->
            <form class="row g-3 mb-4">
              <div class="col-6">
                <label for="searchItemName" class="form-label">Search</label>
                <input
                  type="text"
                  class="form-control"
                  id="searchItemName"
                  placeholder="Search item name..."
                  v-model="searchItemName"
                  autocomplete="off"
                />
              </div>
            </form>
            <!-- End of search inputs -->
            <ItemsTable :items="items" @addItem="itemsStore.addItem" />
          </div>
        </div>
      </div>
      <!-- End of table -->

      <!-- PR Items -->
      <div class="col-md-7">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <p>Total Price</p>
                <h4>
                  ₱ {{ numberWithComma(formatNumber(itemsStore.totalPrice)) }}
                </h4>
                <p class="text-danger">{{ error.budget }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <p>Budget</p>
                <h4>₱ {{ numberWithComma(selectedBudgetAmount) }}</h4>
              </div>
            </div>
          </div>
        </div>
        <PurchaseRequestForm
          :form="form"
          :formStore="formStore"
          :itemsStore="itemsStore"
          :budgets="budgets"
          @submit="submitForm"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>