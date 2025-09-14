<!-- App.vue -->
<template>
  <Header v-if="showHeader" />
  <RouterView />
</template>

<script setup lang="ts">
import { RouterView, useRoute } from 'vue-router';
import { computed } from 'vue';
import Header from '@/components/Header.vue';

const route = useRoute();
const showHeader = computed(() => Boolean(route.meta.requiresAuth));
</script>
