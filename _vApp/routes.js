import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

//Components for routes

import LoginScreen from './pages/login'
import Pages from './pages/page/list'
import PageAdd from './pages/page/add'

import PostCategoryAdd from './pages/postCategory/add.vue'
import PostCategoryEdit from './pages/postCategory/add.vue'
import PostCategoryList from './pages/postCategory/list.vue'

import PostAdd from './pages/post/add.vue'
import PostEdit from './pages/post/add.vue'
import PostList from './pages/post/index.vue'

const routes = [

    {
        path: '/login',
        component: LoginScreen
    },
    {
        path: '/page/add',
        component: PageAdd
    },
    {
        path: '/page/edit/:id',
        component: PageAdd
    },
    {
        path: '/pages',
        component: Pages
    },
    {
        path: '/post/edit/:id',
        component: PostEdit
    },
    {
        path: '/posts',
        component: PostList
    },
    {
        path: '/post/add',
        component: PostAdd
    },
    {
        path: '/blog/category/edit/:id',
        component: PostCategoryEdit
    },
    {
        path: '/blog/category',
        component: PostCategoryList
    },
    {
        path: '/blog/category/add',
        component: PostCategoryAdd
    }

];

const router = new VueRouter({
    mode: 'history',
    routes: routes,
    base: ADMIN_APP_ROUTE
})

export default router;