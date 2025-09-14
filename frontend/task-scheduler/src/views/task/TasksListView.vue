<template>
  <div>
    <div class="task_list_page">
      <div class="toolbar">
        <RouterLink class="create_task_button" to="/tasks/new">+ Create Task</RouterLink>
        <div class="filters">
          <div>
            <label>Search By Name or Description</label>
            <input
                v-model.trim="search"
                type="text"
                placeholder="Search name or description…"
            />
          </div>
          <div>
            <label>Filter By Status</label>
            <select v-model="status">
              <option :value="undefined">All statuses</option>
              <option value="todo">To Do</option>
              <option value="in_progress">In Progress</option>
              <option value="done">Done</option>
              <option value="blocked">Blocked</option>
            </select>
          </div>
          <div>
            <label>Filter By Assignee</label>
            <select v-model="assignedUserId">
              <option :value="null">All assignees</option>
              <option :value="-1">Unassigned</option>
              <option v-for="u in users" :key="u.id" :value="u.id">
                {{ u.name }}
              </option>
            </select>
          </div>
          <div>
            <button class="btn" @click="apply">Apply</button>
            <button class="btn" @click="reset">Reset</button>
          </div>
        </div>
      </div>
      <h2>Tasks List</h2>
      <table>
        <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Status</th>
          <th>Due date</th>
          <th>Assignee</th>
          <th>Actuons</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="task in tasks" :key="task.id">
          <td>{{ task.id }}</td>
          <td>{{ task.name }}</td>
          <td>{{ task.description }}</td>
          <td>{{ task.status_info?.display_name || '—' }}</td>
          <td>{{ dayjs(task.due_date).format('DD.MM.YYYY HH:mm') }}</td>
          <td>{{ task.assignee?.name || 'Unassigned' }}</td>
          <td>
            <button @click="showTask(task.id)">Show</button>
            <button @click="editTask(task.id)">Edit</button>
            <button @click="removeTask(task.id)">Delete</button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
import {fetchTasks, deleteTask} from "@/api/task";
import {ref, onMounted, onActivated, watch} from "vue";
import {TASKS_TYPE_LIST} from "@/api/types";
import type {Task} from "@/api/types";
import router from "@/router";
import dayjs from "dayjs";
import {fetchUsers} from '@/api/user';
import type { TaskStatus } from '@/api/types';

const tasks = ref<Task[]>([]);
const users = ref<{ id: number; name: string }[]>([]);
const search = ref<string>('');
const status = ref<TaskStatus | undefined>(undefined);
const assignedUserId = ref<number | null>(null);
async function load() {
  const assignedParam =
      assignedUserId.value === undefined ? undefined : assignedUserId.value;
  tasks.value = await fetchTasks(
      TASKS_TYPE_LIST,
      search.value || undefined,
      assignedUserId.value ?? undefined,
      status.value
  );
}

async function removeTask(id: number) {
  try {
    await deleteTask(id);
  } catch (e) {
    console.error('Failed to delete task', e);
  }
}

function editTask(id: number) {
  router.push(`/tasks/${id}/edit`);
}

function showTask(id: number) {
  router.push(`/tasks/${id}/show`);
}

onActivated(load)
onMounted(async () => {
  try {
    users.value = await fetchUsers();
  } catch {
  }
  await load();
});

let t: number | undefined
watch([search, status, assignedUserId], () => {
  clearTimeout(t)
  t = window.setTimeout(() => load(), 250)
});

function apply() {
  load();
}

function reset() {
  search.value = '';
  status.value = undefined;
  assignedUserId.value = null;
  load();
}
</script>
<style scoped>
table {
  width: 80%;
  margin: auto;
  border-collapse: collapse;
  border: 1px solid #42b983;

  tr:hover {
    background-color: #1c6b48;
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

h2 {
  text-align: center;
}

.create_task_button {
  background: #2d3748;
  padding: 10px;
  margin: 10px;
}
.filters{
  margin-top: 10px;
  width: 100%;
  border: 1px solid #4a5568;
  display: flex;
  justify-content: space-around;
  div{
    display: flex;
    flex-direction: column;
    label{
      color: #1c6b48;
    }
  }
  input,select{
    width: 200px;
    padding: 5px;
    margin: 5px;
  }
}
</style>