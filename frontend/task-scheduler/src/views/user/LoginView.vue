<template>
  <div id="login_content">
    <h2>Login</h2>
    <form @submit.prevent="onLogin">
      <input type="email" v-model="email" placeholder="Email" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>
<script setup lang="ts">
import {ref} from "vue";
import router from "@/router";
import {login} from "@/api/auth";

const email = ref('');
const password = ref('');
const error = ref('');
async function onLogin() {
  try {
    error.value = '';
    const credentials = await login(email.value, password.value);
    localStorage.setItem('token', credentials.token);
    await router.push('/tasks-list');
  } catch (e: any) {
    error.value = e.message || 'Login failed';
  }
}
</script>
<style scoped>
#login_content {
  width: 30%;
  display: flex;
  flex-direction: column;
  margin: auto;
  height: 300px;
  position: relative;
  form {
    height: 300px;
    width: 300px;
    margin: auto;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
  }
  input{
    margin: 5px;
  }
  h2{
    text-align: center;
    margin-bottom: 20px;
  }
  button{
    width: 100px;
    text-align: center;
  }
}
</style>