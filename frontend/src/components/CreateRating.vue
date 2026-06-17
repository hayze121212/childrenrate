<template>
  <Dialog
    v-model:visible="dialogVisible"
    modal
    :style="{ width: '450px' }"
    :header="isEdit ? 'Редактирование рейтинга' : 'Создание рейтинга'"
    @update:visible="onDialogClose"
  >
    <div class="p-fluid">
      <div class="field">
        <h5>Имя ребёнка</h5>
        <span class="p-input-icon-left">
          <i class="pi pi-user" />
          <InputText
            id="name"
            v-model="form.name"
            placeholder="Имя"
            :class="{ 'p-invalid': validation.name }"
          />
        </span>
        <small v-if="validation.name" class="p-error">{{ validation.name }}</small>
      </div>

      <div class="field">
        <h5>Описание</h5>
        <span class="p-input-icon-left">
          <i class="pi pi-comment" />
          <InputText
            id="description"
            v-model="form.description"
            placeholder="Комментарий"
            :class="{ 'p-invalid': validation.description }"
          />
        </span>
        <small v-if="validation.description" class="p-error">{{ validation.description }}</small>
      </div>

      <div class="field">
        <h5>Дата рождения</h5>
        <input
          id="birthday"
          type="date"
          v-model="form.birthday"
          class="p-inputtext p-component"
          :class="{ 'p-invalid': validation.birthday }"
          style="width: 100%;"
        />
        <small v-if="validation.birthday" class="p-error">{{ validation.birthday }}</small>
      </div>

      <div class="field">
        <h5>Пол</h5>
        <div class="p-field-radiobutton" style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
          <RadioButton inputId="g1" name="gender" :value="1" v-model="form.gender" />
          <label for="g1">Мужской</label>
        </div>
        <div class="p-field-radiobutton" style="display: flex; align-items: center; gap: 8px;">
          <RadioButton inputId="g0" name="gender" :value="0" v-model="form.gender" />
          <label for="g0">Женский</label>
        </div>
        <small v-if="validation.gender" class="p-error">{{ validation.gender }}</small>
      </div>

      <div v-if="generalError" class="field">
        <small class="p-error">{{ generalError }}</small>
      </div>
    </div>

    <template #footer>
      <Button label="Отмена" icon="pi pi-times" class="p-button-text" @click="close" :disabled="loading" />
      <Button :label="isEdit ? 'Сохранить' : 'Создать'" icon="pi pi-check" autofocus @click="submit" :loading="loading" />
    </template>
  </Dialog>
</template>

<script>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';

export default {
  name: 'CreateRating',
  components: { Dialog, Button, InputText, RadioButton },
  data() {
    return {
      form: {
        name: '',
        description: '',
        birthday: '',
        gender: null,
      },
      loading: false,
      generalError: '',
    };
  },
  computed: {
    dialogVisible: {
      get() {
        return this.$store.state.createRatingDialogVisible;
      },
      set(val) {
        this.$store.commit('setCreateRatingDialogVisible', val);
      },
    },
    validation() {
      return this.$store.state.validation || {};
    },
    editing() {
      return this.$store.state.editingRating;
    },
    isEdit() {
      return !!this.editing;
    },
  },
  watch: {
    // При открытии диалога: заполняем форму (редактирование) или сбрасываем (создание)
    dialogVisible(newVal) {
      if (newVal) {
        this.$store.commit('setValidation', {});
        this.generalError = '';
        if (this.editing) {
          this.form = {
            name: this.editing.name || '',
            description: this.editing.description || '',
            birthday: this.editing.birthday || '',
            gender: this.editing.gender !== undefined && this.editing.gender !== null
              ? Number(this.editing.gender)
              : null,
          };
        } else {
          this.resetForm();
        }
      }
    },
  },
  methods: {
    async submit() {
      this.loading = true;
      this.generalError = '';
      this.$store.commit('setValidation', {});

      try {
        if (this.isEdit) {
          await this.$store.dispatch('updateRating', {
            id: this.editing.id,
            name: this.form.name,
            description: this.form.description,
            birthday: this.form.birthday,
            gender: this.form.gender,
          });
        } else {
          await this.$store.dispatch('createRating', {
            name: this.form.name,
            description: this.form.description,
            birthday: this.form.birthday,
            gender: this.form.gender,
          });
        }

        if (Object.keys(this.$store.state.validation).length === 0) {
          this.close();
        }
      } catch (error) {
        this.generalError = 'Ошибка сети или сервера';
        console.error('Ошибка сохранения:', error);
      } finally {
        this.loading = false;
      }
    },
    close() {
      this.$store.commit('setCreateRatingDialogVisible', false);
      this.$store.commit('setEditingRating', null);
      this.resetForm();
      this.$store.commit('setValidation', {});
      this.generalError = '';
    },
    onDialogClose(val) {
      if (!val) {
        this.close();
      }
    },
    resetForm() {
      this.form = { name: '', description: '', birthday: '', gender: null };
    },
  },
};
</script>

<style scoped>
.field {
  margin-bottom: 1.5rem;
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
  font-size: 0.875rem;
}
.p-invalid {
  border-color: #f44336 !important;
}
</style>
