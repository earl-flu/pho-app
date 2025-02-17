<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();

const deleteRole = (roleId) => {
  if (confirm("Are you sure you want to delete this role?")) {
    // Call the delete route
    axios.delete(route('roles.destroy', roleId))
      .then(() => {
        // Optionally, you can refresh the page or remove the role from the list
        window.location.reload();
      })
      .catch(error => {
        console.error("There was an error deleting the role!", error);
      });
  }
};
</script>

<template>
  <Head title="Roles" />

  <AuthenticatedLayout>
    <div class="container">
      <h1 class="mb-4">Roles</h1>
      <table class="table">
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Assigned Permissions</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="role in props.roles" :key="role.id">
            <td>{{ role.name }}</td>
            <td>
              <ul>
                <li v-for="permission in role.permissions" :key="permission.id">
                  {{ permission.name }}
                </li>
              </ul>
            </td>
            <td>
              <a :href="route('roles.edit', role.id)" class="btn btn-primary btn-sm">Edit</a>
              <button @click="deleteRole(role.id)" class="btn btn-danger btn-sm">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      <a href="javascript:;" class="btn btn-success">Create New Role</a>
    </div>
  </AuthenticatedLayout>
</template>