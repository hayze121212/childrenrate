import { createApp } from 'vue';
import axios from 'axios';
import router from './router';
import PrimeVue from 'primevue/config';
import App from './App.vue';
import store from './state';
import ConfirmationService from 'primevue/confirmationservice';
import Tooltip from 'primevue/tooltip';

import 'primevue/resources/themes/bootstrap4-light-blue/theme.css';
import 'primeicons/primeicons.css';
import 'primevue/resources/primevue.min.css';
import 'primeflex/primeflex.css';

const myApp = createApp(App);
window.axios = axios;
myApp.use(store);
myApp.use(router);
myApp.use(PrimeVue);
myApp.use(ConfirmationService);
myApp.directive('tooltip', Tooltip);
myApp.mount('#app');
