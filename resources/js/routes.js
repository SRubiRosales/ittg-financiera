import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '@/js/stores';

import Home from '@/js/components/Home';
import About from '@/js/components/About';
import Clients from '@/js/components/Clients';
import Loans from '@/js/components/Loans';
import Payments from '@/js/components/Payments';
import Loan from '@/js/components/Loan';
import Login from '@/js/pages/LoginPage';
import View from '@/js/views/View';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '',
            component: View,
            children: [
                {
                    path: '/',
                    name: 'home',
                    component: Home
                },
                {
                    path: '/about',
                    name: 'about',
                    component: About
                },
                {
                    path: '/clients',
                    name: 'clients',
                    component: Clients
                },
                {
                    path: '/loans',
                    name: 'loans',
                    component: Loans
                },
                {
                    path: '/payments',
                    name: 'payments',
                    component: Payments
                },
                {
                    path: '/loans/:id',
                    name: 'loan',
                    component: Loan,
                    props: true
                },
            ]
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        }
    ]
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = store.state.isAuthenticated;
    if (to.name !== 'login' && !isAuthenticated) next({ name: 'login'});
    else if (to.name === 'login' && isAuthenticated) next({ name: 'home' });
    else next();
});

export default router;
