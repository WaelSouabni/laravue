import Layout from '@/layout';

const packageRoutes = {
  path: '/package',
  component: Layout,
  redirect: '/table/complex-table',
  name: 'اعدادت الرحلات',
  meta: {
    title: 'اعدادت الرحلات',
    icon: 'cog',
    permissions: ['view menu package'],
  },
  children: [
    {
      path: 'complex-table',
      component: () => import('@/views/package/listePackages'),
      name: 'ComplexTable',
      meta: { title: 'قائمة الرحلات' },
    },
    {
      path: 'drag-table',
      component: () => import('@/views/package/DragTable'),
      name: 'DragTable',
      meta: { title: 'زيادة رحلة' },
    },
  ],
};
export default packageRoutes;
