import { createRouter, createWebHistory } from 'vue-router';
import HomePage from '../views/HomePage.vue';
import LoginDialog from '../components/LoginDialog.vue';
import RatingPage from '../views/RatingPage.vue';

const routes = [
  {
    path: '/',
    component: HomePage,
  },
  {
    path: '/login',
    component: LoginDialog,
    props: { visible: true },
  },
  {
    path: '/ratings',
    component: RatingPage,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;