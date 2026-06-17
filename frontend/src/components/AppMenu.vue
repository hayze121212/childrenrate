<template>
  <Menubar :model="items" style="position: fixed; top: 0; left: 0; width: 100%; z-index: 2000">
    <template #start>
      <a href="/">
        <img alt="Logo" src="../assets/logo.png" width="30" />
      </a>
    </template>
  </Menubar>
</template>

<script>
import Menubar from 'primevue/menubar';
import router from '../router';

export default {
  name: 'AppMenu',
  components: { Menubar },
  computed: {
    isLoggedIn() {
      return this.$store.state.loggedIn;
    },
    // Имя из загруженного пользователя, иначе из localStorage, иначе подпись по умолчанию
    displayName() {
      return (
        this.$store.state.user?.username ||
        this.$store.state.displayName ||
        'Пользователь'
      );
    },
    // Меню строится реактивно из состояния store — корректно при входе,
    // выходе и перезагрузке страницы (не зависит от watch на изменение).
    items() {
      return [
        // ===== ПУНКТ "РЕЙТИНГ" (5.6) — виден только авторизованным =====
        {
          label: 'Рейтинг',
          icon: 'pi pi-fw pi-chart-line',
          visible: this.isLoggedIn,
          items: [
            {
              label: 'Все рейтинги',
              icon: 'pi pi-fw pi-list',
              command: () => {
                router.push('/ratings');
              },
            },
            {
              label: 'Создать рейтинг',
              icon: 'pi pi-fw pi-plus',
              command: () => {
                this.$store.commit('setEditingRating', null);
                this.$store.commit('setCreateRatingDialogVisible', true);
              },
            },
          ],
        },
        // ===== ПУНКТ "ПОЛЬЗОВАТЕЛЬ" =====
        {
          label: this.displayName,
          icon: 'pi pi-fw pi-user',
          items: [
            {
              label: 'Выход',
              icon: 'pi pi-fw pi-sign-out',
              visible: this.isLoggedIn,
              command: () => {
                this.logout();
              },
            },
            {
              label: 'Вход',
              icon: 'pi pi-fw pi-sign-in',
              visible: !this.isLoggedIn,
              command: () => {
                router.push('/login');
              },
            },
          ],
        },
      ];
    },
  },
  methods: {
    logout() {
      this.$store.commit('logout');
      router.push('/login');
    },
  },
};
</script>