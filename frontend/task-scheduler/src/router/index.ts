import {createRouter, createWebHistory} from 'vue-router'
import LoginView from '@/views/user/LoginView.vue'
import UsersListView from '@/views/user/UsersListView.vue'
import TasksListView from '@/views/task/TasksListView.vue'
import TaskCreateView from '@/views/task/TaskCreateView.vue'
import TaskEditView from '@/views/task/TaskEditView.vue'
import TaskShowView from '@/views/task/TaskShowView.vue'
// @ts-ignore
import TasksKanbanView from '@/views/task/TasksKanbanView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/', redirect: '/tasks-list',
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/tasks-list', component: TasksListView,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/kanban-board', component: TasksKanbanView,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/tasks/new', component: TaskCreateView,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/tasks/:id/edit', component: TaskEditView,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/tasks/:id/show', component: TaskShowView,
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/users', component: UsersListView,
            meta: {
                requiresAuth: true
            }
        },
        {path: '/login', component: LoginView},
    ],
})
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')

    if (to.meta.requiresAuth && !token) return next('/login')
    if (to.path === '/login' && token)  return next('/tasks-list')

    next()
})

export default router
