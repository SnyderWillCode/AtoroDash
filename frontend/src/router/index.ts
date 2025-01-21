import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/auth/login',
        name: 'Login',
        component: () => import('@/views/client/auth/Login.vue'),
    },
    {
        path: '/auth/register',
        name: 'Register',
        component: () => import('@/views/client/auth/Register.vue'),
    },
    {
        path: '/auth/forgot-password',
        name: 'Forgot Password',
        component: () => import('@/views/client/auth/ForgotPassword.vue'),
    },
    {
        path: '/auth/reset-password',
        name: 'Reset Password',
        component: () => import('@/views/client/auth/ResetPassword.vue'),
    },
    {
        path: '/auth/2fa/setup',
        name: 'Two Factor Setup',
        component: () => import('@/views/client/auth/TwoFactorSetup.vue'),
    },
    {
        path: '/errors/403',
        name: 'Forbidden',
        component: () => import('@/views/client/errors/Forbidden.vue'),
    },
    {
        path: '/errors/500',
        name: 'ServerError',
        component: () => import('@/views/client/errors/ServerError.vue'),
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('@/views/client/Home.vue'),
    },
    {
        path: '/account',
        name: 'Account',
        component: () => import('@/views/client/Account.vue'),
    },
    {
        path: '/ticket',
        name: 'Ticket',
        component: () => import('@/views/client/ticket/List.vue'),
    },
    {
        path: '/ticket/create',
        name: 'Create Ticket',
        component: () => import('@/views/client/ticket/Create.vue'),
    },
    {
        path: '/ticket/:id',
        name: 'Ticket Detail',
        component: () => import('@/views/client/ticket/[id].vue'),
    },
    {
        path: '/auth/sso',
        name: 'SSO',
        component: () => import('@/views/client/auth/sso.vue'),
    },
    {
        path: '/auth/2fa/setup/disband',
        redirect: () => {
            window.location.href = '/api/auth/2fa/setup/kill';
            return '/api/auth/2fa/setup/kill';
        },
    },
    {
        path: '/auth/logout',
        redirect: () => {
            window.location.href = '/api/user/auth/logout';
            return '/api/user/auth/logout';
        },
    },
    {
        path: '/auth/2fa/verify',
        name: 'Two Factor Verify',
        component: () => import('@/views/client/auth/TwoFactorVerify.vue'),
    },
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/mc-admin',
        name: 'Admin Home',
        component: () => import('@/views/admin/Home.vue'),
    },
];

routes.push({
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/client/errors/NotFound.vue'),
});

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
