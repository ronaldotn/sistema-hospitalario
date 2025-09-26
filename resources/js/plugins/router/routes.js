import { useAuthStore } from '@/stores/auth'
import { hasToken } from '@/composables/Token'

const routeAuthGuard = (to, from, next) => {
  const isLoggedIn = useAuthStore().userInformation.isLoggedIn && hasToken()
  if (isLoggedIn) {
    next()
  } else {
    next('/login')
  }
}

const redirectIfAlredyLoggedIn = (to, from, next) => {
  const isLoggedIn = useAuthStore().userInformation.isLoggedIn && hasToken()
  if (isLoggedIn) {
    next('/dashboard')
  } else {
    next()
  }
}

export const routes = [
  { path: '/', redirect: '/dashboard' },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
        beforeEnter: routeAuthGuard,
      },
      {
        path: 'account-settings',
        component: () => import('@/pages/account-settings.vue'),
        beforeEnter: routeAuthGuard,
      },
      {
        path: 'typography',
        component: () => import('@/pages/typography.vue'),
        beforeEnter: routeAuthGuard,
      },
      {
        path: 'icons',
        component: () => import('@/pages/icons.vue'),
        beforeEnter: routeAuthGuard,
      },
      {
        path: 'cards',
        component: () => import('@/pages/cards.vue'),
        beforeEnter: routeAuthGuard,
      },
      {
        path: 'tables',
        component: () => import('@/pages/tables.vue'),
        beforeEnter: routeAuthGuard,
      },
      {
        path: 'form-layouts',
        component: () => import('@/pages/form-layouts.vue'),
        beforeEnter: routeAuthGuard,
      },
       // ✅ Módulo Patients con prefijo
      {
        path: 'patients',
        children: [
          {
            path: 'show',
            name: 'patients-show',
            component: () => import('@/pages/patients/Show.vue'),
          },
          {
            path: 'index',
            name: 'patients-index',
            component: () => import('@/pages/patients/Index.vue'),
          },
          {
            path: 'create',
            name: 'patients-create',
            component: () => import('@/pages/patients/Create.vue'),
          },
          {
            path: 'edit',
            name: 'patients-edit',
            component: () => import('@/pages/patients/Edit.vue'),
          },
        ],
      },
       // ✅ Módulo Practitioner con prefijo
      {
        path: 'practitioners',
        children: [
          {
            path: 'show',
            name: 'practitioner-show',
            component: () => import('@/pages/practitioners/Show.vue'),
          },
          {
            path: 'index',
            name: 'practitioner-index',
            component: () => import('@/pages/practitioners/Index.vue'),
          },
          {
            path: 'create',
            name: 'practitioner-create',
            component: () => import('@/pages/practitioners/Create.vue'),
          },
          {
            path: 'edit',
            name: 'practitioner-edit',
            component: () => import('@/pages/practitioners/Edit.vue'),
          },
        ],
      },
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('@/pages/login.vue'),
        beforeEnter: redirectIfAlredyLoggedIn,
      },
      {
        path: 'register',
        name: 'register',
        component: () => import('@/pages/register.vue'),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
