<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
</script>

<template>
  <Head title="Purchase Request" />

  <div class="header">
    <h2>
      Republic of Philippines<br />Department of Health<br />Juan M. Alberto
      Mem. District Hospital
    </h2>
    <h3>SUMMARY OF ISSUED DRUGS AND MEDICINES</h3>
    <p>FROM: 11/11/2024 TO: 12/12/2024</p>
  </div>

  <table>
    <thead>
      <tr>
        <th>Order Date</th>
        <th>Issue Date</th>
        <th>Charge Slip</th>
        <th>Patient Name</th>
        <th>Drug Description</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Sub Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
        <td>Lorem Ipsum</td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
body {
  font-family: Arial, sans-serif;
  font-size: 12px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
th,
td {
  border: 1px solid #000;
  padding: 8px;
  text-align: left;
}
th {
  background-color: #f2f2f2;
}
.header {
  text-align: center;
  margin-bottom: 20px;
}
</style>
