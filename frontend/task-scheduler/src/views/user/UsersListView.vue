<template>
  <div>
    <div class="user_list_page">
      <h2>User list page</h2>
      <table>
        <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Availability</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td :class="`available available_${user.available}`">
            <span :class="user.available ? 'available_true' : 'available_false'">
            {{ user.available ? 'Yes' : 'No' }}
          </span>
          </td>
          <td><button @click="onToggle(user)">Toggle Availability</button></td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import {fetchUsers,toggleUserAvailability} from "@/api/user.ts";
import {ref,onMounted} from "vue";
import type {User} from "@/api/types";

const users = ref<User[]>([]);

onMounted(async () => {
  users.value = await fetchUsers();
});

async function onToggle(user: User) {
  const res = await toggleUserAvailability(user.id);
  const u = users.value.find((u: User) => u.id === res.id);
  if (u) u.available = res.available;
}

</script>
<style scoped>
table {
  width: 80%;
  margin: auto;
  border-collapse: collapse;
  border: 1px solid #ccc;
  tr:hover {
    background-color: rgba(121, 119, 119, 0.42);
  }
  th {
    background: #f4f4f4;
    text-align: left;
    color: #1c6b48;
  }

  td, th {
    padding: 8px;
  }
}
h2{
  text-align: center;
}

.available_true{
  color: green;
}
.available_false{
  color: red;
}

</style>