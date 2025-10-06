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
        beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'patients-index', component: () => import('@/pages/patient.vue') },
          { path: 'show/:uuid', name: 'patients-show', component: () => import('@/views/pages/patients/show.vue') },
          { path: 'create', name: 'patients-create', component: () => import('@/views/pages/patients/create.vue') },
          { path: 'edit/:uuid', name: 'patients-edit', component: () => import('@/views/pages/patients/edit.vue') },
        ],
      },
      // ✅ Módulo Components Ralimit
      {
        path: 'components',
        beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'filter', name: 'components', component: () => import('@/pages/com_rali.vue') },
          ],
      },


      // ✅ Módulo Practitioner con prefijo
      {
        path: 'practitioners',
         beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'practitioners-index', component: () => import('@/pages/practitioner.vue') },
          { path: 'show/:uuid', name: 'practitioners-show', component: () => import('@pages/practitioners/show.vue') },
          { path: 'create', name: 'practitioners-create', component: () => import('@pages/practitioners/create.vue') },
          { path: 'edit/:uuid', name: 'practitioners-edit', component: () => import('@pages/practitioners/edit.vue') },
        ],
      },
      // ✅ Módulo Organizations con prefijo
      {
        path: 'organizations',
         beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'organization-index', component: () => import('@/pages/organizations.vue') },
          { path: 'show/:uuid', name: 'organization-show', component: () => import('@pages/organizations/show.vue') },
          { path: 'create', name: 'organization-create', component: () => import('@pages/organizations/create.vue') },
          { path: 'edit/:uuid', name: 'organization-edit', component: () => import('@pages/organizations/edit.vue') },
        ],
      },
      // ✅ Módulo Encounters con prefijo
      {
        path: 'encounters',
         beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'encounter-index', component: () => import('@/pages/encounter.vue') },
          { path: 'show/:id', name: 'encounter-show', component: () => import('@pages/encounters/show.vue') },
          { path: 'create', name: 'encounter-create', component: () => import('@pages/encounters/create.vue') },
          { path: 'edit/:id', name: 'encounter-edit', component: () => import('@pages/encounters/edit.vue') },
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
