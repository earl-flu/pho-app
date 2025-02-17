<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, watch, computed, onMounted } from "vue";
import { Head, useForm, router, usePage } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { debounce } from "lodash";
import ItemsTable from "./Components/ItemsTable.vue";
import PurchaseRequestForm from "./Components/PurchaseRequestForm.vue";
import { useItemsStore } from "@/Stores/useItemsStore";
import { usePurchaseRequestFormStore } from "@/Stores/usePurchaseRequestFormStore";
import { useNumberWithComma } from "@/Composables/useNumberWithComma";

// TEXT AREA FOR REMARKS
const props = defineProps({
  prSignatory: {
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

// Set signatory value
onMounted(() => {
  formStore.setSignatory(props.prSignatory);
});

const error = computed(() => $page.props.errors);

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
  const id = formStore.budget_id;
  const budget = props.budgets.find((budg) => budg.id == id);
  return budget ? budget.amount : "-";
});

const debouncedFetch = debounce((searchItemName) => {
  router.get(
    route("purchase-requests.create"),
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
  form.post(route("purchase-requests.store"), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      itemsStore.clearItems();
      formStore.resetForm();
      useToast().success("PR Saved Successfully", {
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
  <Head title="Create Purchase Request" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">Puchase Request</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Create</li>
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