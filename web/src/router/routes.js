import Login from 'pages/Login'
import SignUp from 'pages/SignUp'
import MainLayout from 'layouts/MainLayout'

const routes = [
  {
    path: '/login',
    component: Login
  },
  {
    path: '/signup',
    component: SignUp
  },
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: () => import('pages/Index.vue') },
      { path: '/dashboard', component: () => import('pages/Dashboard.vue') },
      { path: 'types', component: () => import('pages/types/Index.vue') },
      { path: 'types/new', component: () => import('pages/types/New.vue') },
      { path: 'types/:id', component: () => import('pages/types/Edit.vue') },
      { path: 'payment-methods', component: () => import('pages/paymentMethods/Index.vue') },
      { path: 'payment-methods/new', component: () => import('pages/paymentMethods/New.vue') },
      { path: 'payment-methods/:id', component: () => import('pages/paymentMethods/Edit.vue') },
      { path: 'categories', component: () => import('pages/categories/Index.vue') },
      { path: 'categories/new', component: () => import('pages/categories/New.vue') },
      { path: 'categories/:id', component: () => import('pages/categories/Edit.vue') },
      { path: 'expenses', component: () => import('pages/expenses/Index.vue') },
      { path: 'expenses/new', component: () => import('pages/expenses/New.vue') },
      { path: 'expenses/:id', component: () => import('pages/expenses/Edit.vue') }
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]


export default routes
