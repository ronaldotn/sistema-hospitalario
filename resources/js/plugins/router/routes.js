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
      // ✅ ejmplo ralimit Módulo Patients 
    
   
  
          {
            path: 'patients-index', 
            component: () => import('@/pages/ralimit/list.vue'),
                 beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
          },
          {
            path: 'patients-show/:id', 
            component: () => import('@/pages/ralimit/show.vue'),
                 beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
          },
          {
            path: 'patients-create',
            component: () => import('@/pages/ralimit/add.vue'),
                 beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
          },
          {
            path: 'patients-edit/:id',
            component: () => import('@/pages/ralimit/edit.vue'),
                 beforeEnter: routeAuthGuard,  // <-- guard aplicado al módulo completo
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
