import { createStore } from 'vuex';
import router from './router';

const backendUrl = process.env.VUE_APP_BACKEND_URL || '';

const store = createStore({
  state: {
    user: null,
    token: localStorage.getItem('token') || null,
    loggedIn: !!localStorage.getItem('token'),
    loginError: false,
    preLoading: false,
    dataPreLoading: false,
    rating: [],
    page: { currentPage: 1, perPage: 5 },
    displayName: localStorage.getItem('displayName') || null,
    // ===== ДОБАВЛЕНО ДЛЯ 5.3 =====
    createRatingDialogVisible: false,
    validation: {},
    // Запись, открытая на редактирование (null = режим создания)
    editingRating: null,
  },
  mutations: {
    setToken(state, token) {
      state.token = token;
      localStorage.setItem('token', token);
    },
    setUser(state, user) {
      state.user = user;
    },
    setDisplayName(state, name) {
      state.displayName = name;
      localStorage.setItem('displayName', name);
    },
    setLoggedIn(state, status) {
      state.loggedIn = status;
    },
    setLoginError(state, error) {
      state.loginError = error;
    },
    setPreloading(state, isLoading) {
      state.preLoading = isLoading;
    },
    setDataPreloading(state, isLoading) {
      state.dataPreLoading = isLoading;
    },
    setRating(state, rating) {
      state.rating = rating;
    },
    setPage(state, page) {
      state.page = page;
    },
    // Меняем только номер текущей страницы, не затирая объект пагинации
    setCurrentPage(state, page) {
      state.page.currentPage = page;
    },
    setPerPage(state, perPage) {
      state.page.perPage = perPage;
    },
    // ===== ДОБАВЛЕНО ДЛЯ 5.3 =====
    setCreateRatingDialogVisible(state, visible) {
      state.createRatingDialogVisible = visible;
    },
    setValidation(state, validation) {
      state.validation = validation;
    },
    setEditingRating(state, item) {
      state.editingRating = item;
    },
    logout(state) {
      state.user = null;
      state.token = null;
      state.loggedIn = false;
      state.rating = [];
      state.displayName = null;
      localStorage.removeItem('token');
      localStorage.removeItem('displayName');
      router.push('/');
    },
  },
  actions: {
    async auth({ commit }, { login, password }) {
      commit('setPreloading', true);
      commit('setLoginError', false);

      try {
        const response = await fetch(`${backendUrl}/oauth/token`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            grant_type: 'password',
            client_id: 'TestClient',
            client_secret: 'test_secret',
            username: login,
            password,
          }),
        });
        const data = await response.json();

        if (response.ok && data.access_token) {
          commit('setToken', data.access_token);
          commit('setLoggedIn', true);
          const displayName = login.split('@')[0] || login;
          commit('setDisplayName', displayName);
          commit('setUser', { username: displayName, email: login });
          router.push('/');
        } else {
          commit('setLoginError', true);
        }
      } catch (error) {
        console.error('Auth error:', error);
        commit('setLoginError', true);
      } finally {
        commit('setPreloading', false);
      }
    },

    async getUser({ commit, state }) {
      if (!state.token) return;
      try {
        const response = await fetch(`${backendUrl}/api/user`, {
          headers: { Authorization: 'Bearer ' + state.token },
        });
        if (response.ok) {
          const user = await response.json();
          commit('setUser', user);
          if (user.username || user.email) {
            commit('setDisplayName', user.username || user.email);
          }
        } else if (response.status === 401) {
          // Токен протух/невалиден — разлогиниваем, чтобы выкинуть на страницу входа
          commit('logout');
        }
      } catch (error) {
        console.error('GetUser error:', error);
      }
    },

    // ✅ ИСПРАВЛЕН ЭКШЕН getRating – теперь использует GET
    async getRating({ commit, state }) {
      if (!state.token) {
        console.warn('Нет токена для запроса рейтингов');
        return;
      }
      commit('setDataPreloading', true);
      try {
        const params = new URLSearchParams({
          per_page: state.page.perPage,
          page: state.page.currentPage,
        });
        const url = `${backendUrl}/RatingApi/rating?${params}`;
        console.log('📤 Запрос:', url);
        const response = await fetch(url, {
          method: 'GET', // ✅ изменено на GET
          headers: {
            Authorization: 'Bearer ' + state.token,
          },
        });
        console.log('📥 Статус ответа:', response.status);
        if (response.ok) {
          const data = await response.json();
          console.log('📦 Данные:', data);
          const ratings = data.ratings || [];
          commit('setRating', ratings);
          commit('setPage', data.page || {});
        } else if (response.status === 401) {
          commit('logout');
        } else {
          console.error('❌ Ошибка загрузки:', response.status);
        }
      } catch (error) {
        console.error('❌ getRating error:', error);
      } finally {
        commit('setDataPreloading', false);
      }
    },

    // ===== ДОБАВЛЕНО ДЛЯ 5.3 =====
    // Создание рейтинга через авторизованный API RatingApi::store
    async createRating({ commit, state, dispatch }, { name, description, birthday, gender }) {
      commit('setDataPreloading', true);
      commit('setValidation', {});

      try {
        const response = await fetch(`${backendUrl}/RatingApi/store`, {
          method: 'POST',
          headers: {
            Authorization: 'Bearer ' + state.token,
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            name,
            description: description || '',
            birthday: birthday || '',
            gender: gender ?? '',
          }),
        });

        const data = await response.json();

        if (response.status === 201) {
          console.log('Рейтинг создан');
          commit('setCreateRatingDialogVisible', false);
          commit('setValidation', {});
          await dispatch('getRating');
          router.push('/ratings');
        } else {
          commit('setValidation', data.errors || data);
        }
      } catch (error) {
        console.error('Ошибка создания:', error);
        commit('setValidation', { _error: 'Ошибка сети' });
      } finally {
        commit('setDataPreloading', false);
      }
    },

    // Обновление рейтинга через авторизованный API RatingApi::update
    async updateRating({ commit, state, dispatch }, { id, name, description, birthday, gender }) {
      commit('setDataPreloading', true);
      commit('setValidation', {});

      try {
        const response = await fetch(`${backendUrl}/RatingApi/update/${id}`, {
          method: 'POST',
          headers: {
            Authorization: 'Bearer ' + state.token,
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            name,
            description: description || '',
            birthday: birthday || '',
            gender: gender ?? '',
          }),
        });

        const data = await response.json();

        if (response.status === 200) {
          console.log('Рейтинг обновлён');
          commit('setCreateRatingDialogVisible', false);
          commit('setEditingRating', null);
          commit('setValidation', {});
          await dispatch('getRating');
        } else {
          commit('setValidation', data.errors || data);
        }
      } catch (error) {
        console.error('Ошибка обновления:', error);
        commit('setValidation', { _error: 'Ошибка сети' });
      } finally {
        commit('setDataPreloading', false);
      }
    },

    // Удаление рейтинга через авторизованный API RatingApi::delete
    async deleteRating({ commit, state, dispatch }, { id }) {
      commit('setDataPreloading', true);
      try {
        const response = await fetch(`${backendUrl}/RatingApi/delete/${id}`, {
          method: 'DELETE',
          headers: {
            Authorization: 'Bearer ' + state.token,
          },
        });

        if (response.status === 200) {
          console.log('Рейтинг удалён');
          await dispatch('getRating');
        } else {
          const data = await response.json();
          console.error('Ошибка удаления:', data);
        }
      } catch (error) {
        console.error('Ошибка удаления:', error);
      } finally {
        commit('setDataPreloading', false);
      }
    },
  },
});

export default store;