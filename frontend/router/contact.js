import ContactIndex from '../views/Contact/Index';
import ContactEdit from '../views/Contact/Edit';
import ContactCreate from '../views/Contact/Create';
export default [{
    path: '/contacts',
    meta: { title: 'Contacts' },
    component: { template: '<router-view />' },
    children: [{
            path: 'v2',
            name: 'Contact',
            meta: { title: 'Contacts' },
            component:ContactIndex
        },
        {
            path: 'v2/create',
            name: 'ContactCreate',
            meta: {
                title: 'Create Contact'
            },
            component:ContactCreate
        },
        {
            path: 'v2/:id/edit',
            name: 'ContactyEdit',
            meta: {
                title: 'Edit Contact'
            },
            component:ContactEdit
        }
    ]
}]