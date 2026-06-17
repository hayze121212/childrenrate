<template>
  <Dialog
    :visible="visible"
    modal
    :style="{ width: '450px' }"
    header="Вход в систему"
    @update:visible="(val) => $emit('update:visible', val)"
  >
    <div class="p-fluid">
      <div class="field">
        <h5>Логин</h5>
        <span class="p-input-icon-left">
          <i class="pi pi-user" />
          <InputText
            v-model="login"
            placeholder="Введите логин"
            @keyup.enter="signIn"
          />
        </span>
      </div>

      <div class="field">
        <h5>Пароль</h5>
        <span class="p-input-icon-left">
          <i class="pi pi-lock" />
          <InputText
            v-model="password"
            type="password"
            placeholder="Введите пароль"
            @keyup.enter="signIn"
          />
        </span>
      </div>

      <div v-if="loginError" class="field">
        <small class="p-error">Неверный логин или пароль</small>
      </div>
    </div>

    <template #footer>
      <Button
        label="Войти"
        icon="pi pi-check"
        autofocus
        @click="signIn"
        :loading="loading"
      />
      <Button
        label="Отмена"
        icon="pi pi-times"
        class="p-button-text"
        @click="close"
      />
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';

export default {
  name: 'LoginDialog',
  components: { Dialog, Button, InputText },
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:visible', 'login-success'],
  data() {
    return {
      login: '',
      password: '',
      loading: false,
      loginError: false,
    };
  },
  methods: {
    async signIn() {
      if (!this.login || !this.password) {
        this.loginError = true;
        return;
      }

      this.loading = true;
      this.loginError = false;

      try {
        // ✅ ВЫЗЫВАЕМ ЭКШЕН ИЗ ХРАНИЛИЩА
        await this.$store.dispatch('auth', {
          login: this.login,
          password: this.password
        });

        // Если ошибка входа – хранилище установит loginError = true
        if (this.$store.state.loginError) {
          this.loginError = true;
        } else {
          this.$emit('login-success');
          this.close();
        }
      } catch (error) {
        console.error('Ошибка входа:', error);
        this.loginError = true;
      } finally {
        this.loading = false;
      }
    },
    close() {
      this.login = '';
      this.password = '';
      this.loginError = false;
      this.$emit('update:visible', false);
      if (this.$route.path === '/login') {
        this.$router.push('/');
      }
    },
  },
};
</script>

<style scoped>
.field {
  margin-bottom: 1rem;
}
.p-input-icon-left {
  position: relative;
  display: block;
  width: 100%;
}
.p-input-icon-left i {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 1;
}
.p-input-icon-left .p-inputtext {
  padding-left: 2.5rem;
  width: 100%;
}
.p-error {
  color: #f44336;
}
</style>