import Company from './company'
import Contact from './contact'

export default [
  {
    path: '/',
    component: {name: 'origin', template: '<router-view />'  },
    children: [
      {
        path: 'companies',
        component: {name: 'companies', template: '<router-view />'  },
        children: Company
      },
      {
        path: 'contacts',
        component: {name: 'contacts', template: '<router-view />' },
        children: Contact
      }
    ],
  }
]
