import DefaultLayout from '../layouts/DefaultLayout'
import Company from './company'
import Contact from './contact'

export default [
  // Dashboard
  {
    path: '/',
    name: 'Dashboard',
    component: DefaultLayout,
    meta: { auth: true, title: 'Escritorio' },
    children: [
      {
        path: 'companies',
        component: { template: '<router-view />' },
        children: Company
      },
      {
        path: 'contacts',
        component: { template: '<router-view />' },
        children: Contact
      }
    ]
  }
]
