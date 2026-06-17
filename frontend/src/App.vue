<template>
  <div id="app" style="padding-top: 60px">
    <AppMenu />
    <router-view :key="$route.fullPath" />
    
    <!-- Глобальный диалог создания рейтинга -->
    <CreateRating />

    <!-- Глобальный спиннер загрузки -->
    <div v-if="$store.state.dataPreLoading || $store.state.preLoading" class="global-loader">
      <i class="pi pi-spin pi-spinner" style="font-size: 3rem; color: #4caf50;"></i>
      <p style="margin-top: 1rem; font-size: 1.2rem;">Загрузка...</p>
    </div>
  </div>
</template>

<script>
import store from './state';

import AppMenu from './components/AppMenu.vue';
import CreateRating from './components/CreateRating.vue';

export default {
  name: 'App',
  components: { AppMenu, CreateRating },
  data() {
    return {};
  },

  mounted() {
    const token = localStorage.getItem('token');
    if (token) {
      store.commit('setToken', token);
    }
    this.$store.dispatch('getUser');
  },
};
</script>

<style scoped>
.global-loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.85);
  z-index: 9999;
}
</style>