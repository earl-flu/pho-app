<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { debounce } from "lodash";
import Pagination from "@/Components/Pagination.vue";
import TipTapEditor from "@/Components/TipTapEditor.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
  },
  itemDetails: {
    type: Object,
    required: true,
  },
  categoryId: {
    type: String,
    required: true,
  },
});

const form = useForm({
  description: "",
  uom: "",
  website_link: "",
  original_price: null,
  markup_price: null,
});

const editForm = useForm({
  id: null,
  description: "",
  uom: "",
  website_link: "",
  original_price: null,
  markup_price: null,
});

let description = ref(props.filters.description);

const editModal = ref(null);
let editModalInstance = null;

const addModal = ref(null);
let addModalInstance = null;

let isThirtyPercentChecked = ref(false);

onMounted(() => {
  if (editModal.value) {
    editModalInstance = new bootstrap.Modal(editModal.value);
  }

  if (addModal.value) {
    addModalInstance = new bootstrap.Modal(addModal.value);
  }
});

function formatToNumber(strNum) {
  strNum = strNum || "0";
  if (typeof strNum === "string") {
    strNum = strNum.replace(/,/g, "");
  }

  return Number(strNum);
}

function formatPriceToString(value) {
  // Remove all non-digit characters except for decimal points
  value = value.replace(/[^\d.]/g, "");
  // Split the integer and decimal parts
  let parts = value.split(".");
  // Format the integer part with commas
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  // Join the integer and decimal parts back together
  if (parts.length > 1) {
    value = parts[0] + "." + parts[1].substring(0, 2); // Limit to 2 decimal places
  } else {
    value = parts[0];
  }

  return value;
}

function updateMarkupPrice() {
  if (isThirtyPercentChecked.value) {
    const originalPrice = parseFloat(formatToNumber(form.original_price)) || 0;
    const markupPrice = originalPrice + originalPrice * 0.3;
    form.markup_price = markupPrice.toFixed(2); // Rounds to 2 decimal places
    formatMarkUpPrice();
  } else {
    form.markup_price = 0;
  }
}

function formatOriginalPrice() {
  console.log("formatOriginalPrice()");
  form.original_price = formatPriceToString(form.original_price);
}

function formatOriginalPriceEditForm() {
  editForm.original_price = formatPriceToString(editForm.original_price);
}

function formatMarkUpPrice() {
  form.markup_price = formatPriceToString(form.markup_price);
}

function formatMarkUpPriceEditForm() {
  editForm.markup_price = formatPriceToString(editForm.markup_price);
}

function submit() {
  form.original_price = formatToNumber(form.original_price);
  form.markup_price = formatToNumber(form.markup_price);
  form.post(
    route("items.details.store", {
      categoryId: props.categoryId,
      itemId: props.item.id,
    }),
    {
      onSuccess: () => {
        const toast = useToast();
        toast.success("PR Item Detail Saved Successfully", {
          timeout: 3000,
        });
        form.reset();
        isThirtyPercentChecked.value = false;

        // Hide add modal
        if (addModalInstance) {
          addModalInstance.hide();
        } else {
          console.error("Modal instance is not initialized.");
        }
      },
    }
  );
}

function submitEdit() {
  editForm.original_price = formatToNumber(editForm.original_price);
  editForm.markup_price = formatToNumber(editForm.markup_price);
  editForm.put(route("items.details.update", editForm.id), {
    onSuccess: () => {
      const toast = useToast();
      toast.success("PR Item Detail Updated Successfully", {
        timeout: 3000,
      });
      editForm.reset();
      isThirtyPercentChecked.value = false;

      // Hide the modal
      if (editModalInstance) {
        editModalInstance.hide();
      } else {
        console.error("Modal instance is not initialized.");
      }
    },
  });
}

const debouncedFetch = debounce((description) => {
  router.get(
    route("items.details.index", {
      categoryId: props.categoryId,
      itemId: props.item.id,
    }),
    { description },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
}, 300);

// Watch search input
watch([description], (values) => {
  const [description] = values;
  debouncedFetch(description);
});

// Watch checkbox and input form.original_price
watch(
  [isThirtyPercentChecked, () => form.original_price],
  ([isChecked, price]) => {
    if (isThirtyPercentChecked.value) {
      const originalPrice = parseFloat(formatToNumber(price)) || 0;
      const markupPrice = originalPrice + originalPrice * 0.3;
      form.markup_price = markupPrice.toFixed(2); // Rounds to 2 decimal places
      formatMarkUpPrice();
    }
  }
);

watch(
  [isThirtyPercentChecked, () => editForm.original_price],
  ([isChecked, price]) => {
    if (isThirtyPercentChecked.value) {
      const originalPrice = parseFloat(formatToNumber(price)) || 0;
      const markupPrice = originalPrice + originalPrice * 0.3;
      editForm.markup_price = markupPrice.toFixed(2); // Rounds to 2 decimal places
      formatMarkUpPriceEditForm();
    }
  }
);

const openEditModal = (itemDetail) => {
  editForm.id = itemDetail.id;
  editForm.description = itemDetail.description;
  editForm.uom = itemDetail.uom;
  editForm.website_link = itemDetail.website_link;
  editForm.original_price = itemDetail.original_price;
  editForm.markup_price = itemDetail.markup_price;

  // Show edit modal
  if (editModalInstance) {
    editModalInstance.show();
  }
};
</script>

<template>
  <Head :title="'PR Library - ' + item.name" />

  <AuthenticatedLayout>
    <!--breadcrumb-->
    <h3 class="mb-4">PR</h3>
    <nav aria-label="breadcrumb page-breadcrumb" style="margin-bottom: 30px">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <Link :href="route('categories.index')">Categories</Link>
        </li>
        <Link
          :href="route('categories.items', item.category.id)"
          class="breadcrumb-item"
        >
          <a href="#">{{ item.category.name }}</a>
        </Link>
        <li class="breadcrumb-item active" aria-current="page">
          {{ item.name }}
        </li>
        <li class="breadcrumb-item active" aria-current="page">Details</li>
      </ol>
    </nav>
    <!--end breadcrumb-->

    <div class="row">
      <div class="d-flex align-items-stretch">
        <div class="card w-100 rounded-4">
          <div class="card-body">
            <!-- Add item detail -->
            <div class="d-flex align-items-start justify-content-between mb-4">
              <div>
                <h5 class="mb-0">{{ item.name }}</h5>
              </div>

              <div>
                <!-- Button trigger modal -->
                <button
                  type="button"
                  class="btn btn-primary px-4"
                  data-bs-toggle="modal"
                  data-bs-target="#addModal"
                >
                  <i class="bi bi-plus-lg me-2"></i>
                  Add New Detail
                </button>
                <!-- Add Modal -->
                <div
                  class="modal fade"
                  id="addModal"
                  ref="addModal"
                  aria-hidden="true"
                  style="display: none"
                >
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form @submit.prevent="submit">
                        <div class="modal-header border-bottom-0 py-2">
                          <h5 class="modal-title">Add Detail</h5>
                          <a
                            href="javascript:;"
                            class="primaery-menu-close"
                            data-bs-dismiss="modal"
                          >
                            <i class="material-icons-outlined">close</i>
                          </a>
                        </div>
                        <div class="modal-body row">
                          <div class="col-md-12">
                            <label for="description" class="form-label"
                              >Description</label
                            >
                            <TipTapEditor v-model="form.description" />
                            <div class="invalid-feedback d-block">
                              {{ form.errors.description }}
                            </div>
                          </div>
                          <div class="col-md-12 mt-3">
                            <label for="uom" class="form-label"
                              >Unit of Measure</label
                            >
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="form.uom"
                              autofocus
                              id="uom"
                            />
                            <div class="invalid-feedback d-block">
                              {{ form.errors.uom }}
                            </div>
                          </div>

                          <div class="col-md-12 mt-3">
                            <label for="website_link" class="form-label"
                              >Website Link</label
                            >
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="form.website_link"
                              autofocus
                              id="website_link"
                            />
                            <div class="invalid-feedback d-block">
                              {{ form.errors.website_link }}
                            </div>
                          </div>

                          <div class="col-md-12 mt-3">
                            <label for="original_price" class="form-label"
                              >Original Price</label
                            >
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="form.original_price"
                              autofocus
                              id="original_price"
                              @input="formatOriginalPrice()"
                            />
                            <div class="invalid-feedback d-block">
                              {{ form.errors.original_price }}
                            </div>
                          </div>

                          <div class="col-md-12 mt-3">
                            <label for="markup_price" class="form-label"
                              >Markup Price</label
                            >
                            <div class="form-check form-check-success">
                              <input
                                class="form-check-input"
                                type="checkbox"
                                value=""
                                id="isThirtyPercent"
                                v-model="isThirtyPercentChecked"
                              />
                              <!-- @change="updateMarkupPrice" -->
                              <label
                                class="form-check-label"
                                for="isThirtyPercent"
                              >
                                Original Price + 30%
                              </label>
                            </div>
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="form.markup_price"
                              autofocus
                              id="markup_price"
                              :disabled="isThirtyPercentChecked"
                            />
                            <div class="invalid-feedback d-block">
                              {{ form.errors.markup_price }}
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
                <!-- End Add Modal -->

                <!-- Edit Modal -->
                <div
                  class="modal fade"
                  id="editModal"
                  ref="editModal"
                  aria-hidden="true"
                  style="display: none"
                >
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <form @submit.prevent="submitEdit">
                        <div class="modal-header border-bottom-0 py-2">
                          <h5 class="modal-title">Edit Detail</h5>
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
                            <label for="description" class="form-label"
                              >Description</label
                            >
                            <TipTapEditor v-model="editForm.description" />
                            <div class="invalid-feedback d-block">
                              {{ editForm.errors.description }}
                            </div>
                          </div>
                          <div class="col-md-12 mt-3">
                            <label for="uom" class="form-label"
                              >Unit of Measure</label
                            >
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="editForm.uom"
                              autofocus
                              id="uom"
                            />
                            <div class="invalid-feedback d-block">
                              {{ editForm.errors.uom }}
                            </div>
                          </div>

                          <div class="col-md-12 mt-3">
                            <label for="website_link" class="form-label"
                              >Website Link</label
                            >
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="editForm.website_link"
                              autofocus
                              id="website_link"
                            />
                            <div class="invalid-feedback d-block">
                              {{ editForm.errors.website_link }}
                            </div>
                          </div>

                          <div class="col-md-12 mt-3">
                            <label for="original_price" class="form-label"
                              >Original Price</label
                            >
                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="editForm.original_price"
                              autofocus
                              id="original_price"
                              @input="formatOriginalPriceEditForm()"
                            />
                            <div class="invalid-feedback d-block">
                              {{ editForm.errors.original_price }}
                            </div>
                          </div>

                          <div class="col-md-12 mt-3">
                            <label for="markup_price" class="form-label"
                              >Markup Price
                            </label>
                            <div class="form-check form-check-success">
                              <input
                                class="form-check-input"
                                type="checkbox"
                                value=""
                                id="isThirtyPercent"
                                v-model="isThirtyPercentChecked"
                              />
                              <label
                                class="form-check-label"
                                for="isThirtyPercent"
                              >
                                Original Price + 30%
                              </label>
                            </div>

                            <input
                              autocomplete="off"
                              type="text"
                              class="form-control"
                              v-model="editForm.markup_price"
                              autofocus
                              id="markup_price"
                              :disabled="isThirtyPercentChecked"
                            />
                            <div class="invalid-feedback d-block">
                              {{ editForm.errors.markup_price }}
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer border-top-0">
                          <button
                            class="btn btn-grd-info"
                            :class="{ 'opacity-25': editForm.processing }"
                            :disabled="editForm.processing"
                          >
                            Update
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
                <!-- End Edit Modal -->
              </div>
            </div>
            <!-- End of add item detail -->
            <!-- Search inputs -->
            <form class="row g-3 mb-5">
              <div class="col-md-4">
                <label for="description" class="form-label">Search</label>
                <input
                  type="text"
                  class="form-control"
                  id="description"
                  placeholder="Description"
                  v-model="description"
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
                    <th>Item Description</th>
                    <th>Original Price</th>
                    <th>Markup Price</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="itemDetail in itemDetails.data"
                    :key="itemDetail.id"
                  >
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">{{ itemDetail.id }}</p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">
                          <span
                            class="tiptap"
                            v-html="itemDetail.description"
                          ></span>
                        </p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">
                          {{ itemDetail.original_price }}
                        </p>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <p class="mb-0 fw-bold">
                          {{ itemDetail.markup_price }}
                        </p>
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
                            <li v-show="itemDetail.website_link">
                              <a
                                class="dropdown-item"
                                href="#"
                                @click="openEditModal(itemDetail)"
                              >
                                <i class="bi bi-pencil-square me-2"></i>
                                Edit
                              </a>
                            </li>
                            <li v-show="itemDetail.website_link">
                              <a
                                class="dropdown-item"
                                :href="itemDetail.website_link"
                                target="_blank"
                              >
                                <i class="bi bi-link-45deg me-2"></i>Website
                                Link
                              </a>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              <Pagination :links="itemDetails.links" />
            </div>
          </div>
          <!-- End of table -->
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
