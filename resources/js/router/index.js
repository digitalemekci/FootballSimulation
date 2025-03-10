import { createRouter, createWebHistory } from 'vue-router';
import Teams from '../components/Teams.vue';
import Groups from '../components/Groups.vue';
import Fixtures from '../components/Fixtures.vue';
import GroupStandings from '../components/GroupStandings.vue';

const routes = [
    { path: '/', component: Teams },
    { path: '/groups', component: Groups },
    { path: '/fixtures', component: Fixtures },
    { path: '/group-standings', component: GroupStandings },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
