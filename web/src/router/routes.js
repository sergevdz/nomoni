import Login from 'pages/Login'
import MyLayout from 'layouts/MyLayout'

const routes = [
  {
    path: '/login',
    component: Login
  },
  {
    path: '/',
    component: MyLayout,
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
      { path: 'spends', component: () => import('pages/spends/Index.vue') },
      { path: 'spends/new', component: () => import('pages/spends/New.vue') },
      { path: 'spends/:id', component: () => import('pages/spends/Edit.vue') }
    ]
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
