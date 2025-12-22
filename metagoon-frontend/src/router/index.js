import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Vakances from '../views/Vakances.vue';
import Favoriti from '../views/Favoriti.vue';
import Login from '../views/Login.vue';
import Regis from '../views/Regis.vue';
import Profile from '../views/Profile.vue';
import VacancyDetailed from '../views/VacancyDetailed.vue';
import Applications from '../views/Applications.vue';
import AdminPanel from "../views/AdminPanel.vue";

const routes = [
  {
    path: "/admin",
    component: AdminPanel,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  { path: '/', name: 'home', component: Home },
  { path: '/vakances', name: 'vakances', component: Vakances },
  { path: '/favoriti', name: 'favoriti', component: Favoriti },
  { path: '/login', name: 'login', component: Login, meta: { hideHeader: true } },
  { path: '/registracija', name: 'registracija', component: Regis, meta: { hideHeader: true } },
  { path: '/profile', name: 'profile', component: Profile },
  { path: '/vacancy/:id', component: VacancyDetailed, props: true },
  { path: '/applications', name: 'applications', component: Applications }, // employer
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  const role = localStorage.getItem("role");

  if (to.meta.requiresAuth && !token) {
    return next("/login");
  }

  if (to.meta.requiresAdmin && role !== "admin") {
    return next("/");
  }

  next();
});


export default router;
