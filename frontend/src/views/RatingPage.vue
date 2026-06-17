<template>
  <div style="padding: 20px;">
    <!-- Заголовок с кнопкой создания -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h1 class="p-text-center" style="margin: 0;">Рейтинги</h1>
      <Button label="Создать рейтинг" icon="pi pi-plus" @click="openCreateDialog" />
    </div>

    <!-- Спиннер загрузки -->
    <div v-if="$store.state.dataPreLoading" style="text-align: center; padding: 50px;">
      <ProgressSpinner strokeWidth="4" style="width:50px;height:50px;" />
      <p>Загрузка данных...</p>
    </div>

    <!-- Данные -->
    <div v-else>
      <DataView :value="ratings" layout="grid">
        <template #grid="slotProps">
          <div style="display: flex; flex-wrap: wrap;">
            <div
              v-for="item in slotProps.items"
              :key="item.id"
              class="p-col-12 p-md-3"
              style="padding: 0.5em; box-sizing: border-box; width: 25%;"
            >
              <div class="product-grid-item card">
                <Panel>
                  <template #header>
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                      <strong>{{ item.name }}</strong>
                      <div>
                        <Button
                          icon="pi pi-cog"
                          class="p-button-primary p-button-sm"
                          style="margin-right: 5px;"
                          @click="editRating(item)"
                        />
                        <Button
                          icon="pi pi-trash"
                          class="p-button-danger p-button-sm"
                          @click="showDeleteDialog(item.id)"
                          :loading="deletingId === item.id"
                        />
                      </div>
                    </div>
                  </template>
                  <template #default>
                    <div style="text-align: center;">
                      <img
                        :src="avatar(item.gender)"
                        :alt="item.gender == 1 ? 'Мальчик' : 'Девочка'"
                        width="96"
                        height="96"
                      />
                    </div>
                    <div style="margin-top: 8px; font-size: 0.9rem; color: #555;">
                      {{ item.description || 'Без описания' }}
                    </div>
                    <div style="margin-top: 8px; font-weight: 600;">
                      {{ ageText(item.birthday) }}
                    </div>
                  </template>
                </Panel>
              </div>
            </div>
          </div>
        </template>
      </DataView>

      <Paginator
        :rows="perPage"
        :totalRecords="total"
        :first="first"
        @page="onPage"
      />
    </div>

    <!-- Диалог подтверждения удаления -->
    <Dialog
      v-model:visible="confirmDialog.visible"
      modal
      header="Удаление рейтинга"
      :style="{ width: '450px' }"
    >
      <p>Вы действительно хотите удалить этот рейтинг (данное действие необратимо)?</p>
      <template #footer>
        <Button label="Нет" class="p-button-text" @click="confirmDialog.visible = false" />
        <Button label="Да" class="p-button-danger" @click="executeDelete" :loading="deletingId !== null" />
      </template>
    </Dialog>

    <CreateRating />
  </div>
</template>

<script>
import DataView from 'primevue/dataview';
import Panel from 'primevue/panel';
import ProgressSpinner from 'primevue/progressspinner';
import Paginator from 'primevue/paginator';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import CreateRating from '../components/CreateRating.vue';
import boyAvatar from '../assets/boy.svg';
import girlAvatar from '../assets/girl.svg';

export default {
  name: 'RatingPage',
  components: { DataView, Panel, ProgressSpinner, Paginator, Button, Dialog, CreateRating },
  data() {
    return {
      deletingId: null,
      confirmDialog: {
        visible: false,
        id: null,
      },
    };
  },
  computed: {
    ratings() {
      return this.$store.state.rating || [];
    },
    total() {
      return this.$store.state.page?.total || 0;
    },
    perPage() {
      return this.$store.state.page?.perPage || 5;
    },
    currentPage() {
      return this.$store.state.page?.currentPage || 1;
    },
    // Индекс первой записи текущей страницы — делает Paginator управляемым
    first() {
      return (this.currentPage - 1) * this.perPage;
    },
  },
  mounted() {
    if (this.$store.state.loggedIn) {
      this.$store.dispatch('getRating');
    } else {
      this.$router.push('/');
    }
  },
  methods: {
    avatar(gender) {
      return Number(gender) === 1 ? boyAvatar : girlAvatar;
    },
    // Возраст в формате "X лет Y мес." из даты рождения
    ageText(birthday) {
      if (!birthday) return '';
      const b = new Date(birthday);
      const now = new Date();
      let months = (now.getFullYear() - b.getFullYear()) * 12 + (now.getMonth() - b.getMonth());
      if (now.getDate() < b.getDate()) months -= 1;
      if (months < 0) months = 0;
      const years = Math.floor(months / 12);
      const rest = months % 12;
      return `Возраст: ${years} лет ${rest} мес.`;
    },
    onPage(event) {
      this.$store.commit('setPerPage', event.rows);
      this.$store.commit('setCurrentPage', event.page + 1);
      this.$store.dispatch('getRating');
    },
    openCreateDialog() {
      this.$store.commit('setEditingRating', null);
      this.$store.commit('setCreateRatingDialogVisible', true);
    },
    editRating(item) {
      this.$store.commit('setEditingRating', { ...item });
      this.$store.commit('setCreateRatingDialogVisible', true);
    },
    showDeleteDialog(id) {
      this.confirmDialog.id = id;
      this.confirmDialog.visible = true;
    },
    async executeDelete() {
      const id = this.confirmDialog.id;
      if (!id) return;
      this.deletingId = id;
      try {
        await this.$store.dispatch('deleteRating', { id });
        this.confirmDialog.visible = false;
        this.confirmDialog.id = null;
      } catch (error) {
        console.error('Ошибка удаления:', error);
      } finally {
        this.deletingId = null;
      }
    },
  },
};
</script>

<style scoped>
.product-grid-item {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #fff;
  transition: 0.2s;
}
.product-grid-item:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.p-text-center {
  text-align: center;
}
</style>
