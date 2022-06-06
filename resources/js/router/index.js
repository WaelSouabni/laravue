import Vue from 'vue';
import Router from 'vue-router';

/**
 * Layzloading will create many files and slow on compiling, so best not to use lazyloading on devlopment.
 * The syntax is lazyloading, but we convert to proper require() with babel-plugin-syntax-dynamic-import
 * @see https://doc.laravue.dev/guide/advanced/lazy-loading.html
 */

Vue.use(Router);

/* Layout */
import Layout from '@/layout';

/* Router for modules */

/**
 * Sub-menu only appear when children.length>=1
 * @see https://doc.laravue.dev/guide/essentials/router-and-nav.html
 **/

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    roles: ['admin', 'editor']   Visible for these roles only
    permissions: ['view menu zip', 'manage user'] Visible for these permissions only
    title: 'title'               the name show in sub-menu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    noCache: true                if true, the page will no be cached(default is false)
    breadcrumb: false            if false, the item will hidden in breadcrumb (default is true)
    affix: true                  if true, the tag will affix in the tags-view
  }
**/

export const constantRoutes = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect/index'),
      },
    ],
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true,
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/AuthRedirect'),
    hidden: true,
  },
  {
    path: '/404',
    redirect: { name: 'Page404' },
    component: () => import('@/views/error-page/404'),
    hidden: true,
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401'),
    hidden: true,
  },
  // dynamic segments start with a colon
  {
    path: '/packages/:id',
    component: Layout,
    hidden: true,
    redirect: '/packages/detail',
    children: [
      {
        path: '/packages/:id',
        component: () => import('@/views/package/packageDetail'),
        name: ' ',
        meta: { title: ' ', icon: 'flight-plane', permissions: ['manage'] },
      },
    ],
  },
  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: 'dashboard', icon: 'dashboard', noCache: false },
      },
    ],
  },
  {
    path: '/packages',
    component: Layout,
    redirect: '/packages/index',
    children: [
      {
        path: '/packages',
        component: () => import('@/views/package/listePackages'),
        name: 'قائمة الرحلات ',
        meta: { title: 'قائمة الرحلات ', icon: 'flight-plane', permissions: ['manage'] },
      },
    ],
  },
  {
    path: '/users',
    component: Layout,
    redirect: '/users/index',
    children: [
      {
        path: 'users',
        component: () => import('@/views/users/List'),
        name: 'قائمة الحرفاء',
        meta: { title: 'قائمة الحرفاء', icon: 'user', permissions: ['manage'] },
      },
    ],
  },
  {
    path: '/accompgnateurs',
    component: Layout,
    redirect: '/accompgnateurs/index',
    name: 'اعدادات المرافقين',
    alwaysShow: true,
    meta: {
      title: 'اعدادات المرافقين',
      icon: 'users',
      permissions: ['view menu administrator'],
    },
    children: [
      {
        path: 'accompgnateur',
        component: () => import('@/views/accompgnateur/List'),
        name: 'قائمة المرافقين',
        meta: { title: 'قائمة المرافقين', icon: 'user', permissions: ['manage'] },
      },
      {
        path: 'accompagnateurPackages',
        component: () => import('@/views/accompgnateurPackage/List'),
        name: ' قائمة علاقة المرافقين بالرحلات',
        meta: { title: ' قائمة علاقة المرافقين بالرحلات', icon: 'user', permissions: ['manage'] },
      },
    ],
  },
  {
    path: '/pelerins',
    component: Layout,
    redirect: '/pelerins/index',
    children: [
      {
        path: 'pelerins',
        component: () => import('@/views/pelerins/List'),
        name: 'قائمة المعتمرين',
        meta: { title: 'قائمة المعتمرين', icon: 'user', permissions: ['manage'] },
      },
    ],
  },
  {
    path: '/paiement',
    component: Layout,
    redirect: '/paiement/index',
    children: [
      {
        path: 'paiement',
        component: () => import('@/views/paiement/List'),
        name: 'قائمة الخلاص',
        meta: { title: 'قائمة الخلاص', icon: 'dollar', permissions: ['manage'] },
      },
    ],
  },
  {
    path: '/messages',
    component: Layout,
    redirect: '/messages/index',
    children: [
      {
        path: 'messages',
        component: () => import('@/views/message/List'),
        name: 'قائمة الرسائل',
        meta: { title: 'قائمة الرسائل', icon: 'envolope', permissions: ['manage'] },
      },
    ],
  },
];

export const asyncRoutes = [
//  { path: '*', redirect: '/404', hidden: true },
];

const createRouter = () => new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  base: process.env.MIX_LARAVUE_PATH,
  routes: constantRoutes,
});

const router = createRouter();

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter();
  router.matcher = newRouter.matcher; // reset router
}

export default router;
