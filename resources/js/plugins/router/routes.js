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

      // ✅ Módulo Diagnósticos con prefijo
      {
        path: 'diagnostics',
        beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'diagnostics-index', component: () => import('@/pages/diagnostic.vue') },
          { path: 'show/:uuid', name: 'diagnostics-show', component: () => import('@pages/diagnostics/show.vue') },
          { path: 'create', name: 'diagnostics-create', component: () => import('@pages/diagnostics/create.vue') },
          { path: 'edit/:uuid', name: 'diagnostics-edit', component: () => import('@pages/diagnostics/edit.vue') },
        ],
      },
      // ✅ Módulo Consentimientos con prefijo
      {
        path: 'consents',
        beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'consents-index', component: () => import('@/pages/consent.vue') },
          { path: 'show/:uuid', name: 'consents-show', component: () => import('@pages/consents/show.vue') },
          { path: 'create', name: 'consents-create', component: () => import('@pages/consents/create.vue') },
          { path: 'edit/:uuid', name: 'consents-edit', component: () => import('@pages/consents/edit.vue') },
        ],
      },
      // ✅ Módulo Condiciones con prefijo
      {
        path: 'conditions',
        beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'index', name: 'conditions-index', component: () => import('@/pages/condition.vue') },
          { path: 'show/:id', name: 'conditions-show', component: () => import('@pages/conditions/show.vue') },
          { path: 'create', name: 'conditions-create', component: () => import('@pages/conditions/create.vue') },
          { path: 'edit/:id', name: 'conditions-edit', component: () => import('@pages/conditions/edit.vue') },
        ],
      },
      // ✅ Módulo Components Ralimit
      {
        path: 'components',
        beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
        children: [
          { path: 'filter', name: 'components', component: () => import('@/pages/com_rali.vue') },
          { path: 'index', name: 'components-index', component: () => import('@/pages/ralimit/list.vue') },
          { path: 'show/:id', name: 'components-show', component: () => import('@/pages/ralimit/show.vue') },
          { path: 'create', name: 'components-create', component: () => import('@/pages/ralimit/add.vue') },
          { path: 'edit/:id', name: 'components-edit', component: () => import('@/pages/ralimit/edit.vue') },
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
