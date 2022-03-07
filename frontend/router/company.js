import CompanyIndex from '../views/Company/Index';
import CompanyEdit from '../views/Company/Edit';
import CompanyCreate from '../views/Company/Create';
export default [{
    path: '/companies',
    meta: { title: 'Companies' },
    component: { template: '<router-view />' },
    children: [{
            path: 'v2',
            name: 'Company',
            meta: { title: 'Companies' },
            component:CompanyIndex
        },
        {
            path: 'v2/create',
            name: 'CompanyCreate',
            meta: {
                title: 'Create Company'
            },
            component:CompanyCreate
        },
        {
            path: 'v2/:id/edit',
            name: 'CompanyEdit',
            meta: {
                title: 'Edit Company'
            },
            component:CompanyEdit
        }
    ]
}]