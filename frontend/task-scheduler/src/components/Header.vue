<template>
  <header class="header">
    <nav class="nav" >
      <RouterLink to="/tasks-list">Tasks List</RouterLink>
      <RouterLink to="/kanban-board">Kanban Board</RouterLink>
      <RouterLink to="/users">Users</RouterLink>
      <a href="#" @click.prevent="handleLogout">Logout</a>
    </nav>
  </header>
</template>

<script setup lang="ts">
import router from "@/router";
import { RouterLink } from 'vue-router';
import { logout } from "@/api/auth";

async function handleLogout(): Promise<void> {
  localStorage.removeItem('token');
  try {
    await logout();
  } catch (e) {
    console.error("Logout error:", e);
  }
  await router.push('/login');
}
</script>
<style>
.header{
  width: 100%;
  .nav{
    padding: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    a{
      margin: 5px;
      text-decoration: none;
      border-radius: 5px;
      border: 1px solid #ccc;
      display: block;
      padding: 5px 15px;
    }
    a.router-link-active {
      color: #000;
      background: #42b983;
    }
  }
}
</style>