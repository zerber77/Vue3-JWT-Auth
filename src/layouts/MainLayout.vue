<template>
  <ErrorMessageComponent
    v-model="error"
    :errorMessage="errorMessage"
  />
  <HelpMessageComponent
    v-model="help"
    :helpMessage="helpMessage"
  />
  
  <q-layout view="lHh Lpr lFf">
    <!-- Хедер -->
    <q-header elevated class="modern-header">
      <q-toolbar class="q-px-md">
        <!-- Кнопка меню -->
        <q-btn
          v-if="isAuthenticated"
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          class="menu-btn"
        />

        <!-- Логотип / Название -->
        <q-toolbar-title class="header-title">
          <q-icon name="rocket_launch" size="24px" class="q-mr-sm" color="purple-4" />
          MySite
        </q-toolbar-title>

        <!-- Авторизованный пользователь -->
        <template v-if="isAuthenticated">
          <div class="user-profile-wrapper">
            <q-avatar size="32px" class="user-avatar">
              <q-icon name="person" color="white" size="18px" />
            </q-avatar>
            <span class="user-name q-mx-sm">{{ userData?.login }}</span>
            <q-btn 
              flat 
              round 
              dense 
              icon="logout" 
              @click.prevent="logOut" 
              class="logout-btn"
            >
              <q-tooltip>Выйти</q-tooltip>
            </q-btn>
          </div>
        </template>

        <!-- Неавторизованный пользователь -->
        <template v-else>
          <q-btn
            v-if="router.currentRoute.value.name !== 'SignUpPage'"
            to="/SignUpPage/" 
            unelevated 
            class="login-btn"
            label="Войти" 
          />
        </template>
      </q-toolbar>
    </q-header>

    <!-- Боковая панель -->
    <q-drawer
      v-if="isAuthenticated"
      v-model="leftDrawerOpen"
      show-if-above
      class="modern-drawer"
    >
      <!-- Заголовок панели -->
      <div class="drawer-header">
        <q-icon name="apps" size="18px" class="q-mr-sm" />
        <span>Tools</span>
      </div>
      <q-separator class="drawer-separator" />

      <!-- Список ссылок -->
      <q-list class="q-pa-sm">
        <EssentialLink
          v-for="link in visibleLinks"
          :key="link.id"
          :title="link.title"
          :icon="link.icon"
          :link="link.link"
        />
      </q-list>
    </q-drawer>

    <!-- Контейнер страниц -->
    <q-page-container class="modern-page-container">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import EssentialLink from 'components/EssentialLink.vue'
import { useRouter } from 'vue-router';
import { isTokenValid } from "components/modules/auth/isTokenValid";
import { useAuthStore } from 'stores/auth'
import useMessageVars from "components/modules/messages/getMessageVars";
import ErrorMessageComponent from "components/Modals/ErrorMessageComponent.vue";
import HelpMessageComponent from "components/Modals/HelpMessageComponent.vue";

const linksList = [
  {
    id: 0,
    title: 'Cabinet',
    caption: '',
    icon: 'person',
    link: '/CategoriesPage/',
    roles: ['user']
  },
  {
    id: 1,
    title: 'Users',
    caption: '',
    icon: 'group',
    link: '/UsersPage/',
    roles: ['admin']
  },
  {
    id: 2,
    title: 'About Project',
    caption: '',
    icon: 'star',
    link: '/',
    roles: ['user', 'admin']
  },
]

const leftDrawerOpen = ref(false)
const toggleLeftDrawer = () => leftDrawerOpen.value = !leftDrawerOpen.value

const { help, error, helpMessage, errorMessage, setHelpMessage, setErrorMessage } = useMessageVars()
const authStore = useAuthStore()
const router = useRouter()

const isAuthenticated = computed(() => authStore.isAuthenticated())
const userData = computed(() => authStore.getTokenData())

const visibleLinks = computed(() => {
  if (!isAuthenticated.value || !userData.value?.role) return []
  return linksList.filter(link => 
    link.roles.includes(userData.value.role)
  )
})

onMounted(async () => {
  // check token
  if (authStore.isAuthenticated()) {
    try {
      const { response } = await isTokenValid()
      if (response.value.error) {
        setErrorMessage(response.value.error)
      }
    } catch (err) {
      setErrorMessage(err)
      authStore.logout()
    }
  }
  /// token expired
  else {
    setErrorMessage('Token expired. Authorize again')
    authStore.logout() // clear token
    setTimeout(() => { router.push({ name: 'SignUpPage' }) }, 2000) 
  }
})

// LogOut button
const logOut = () => {
  authStore.logout()
  router.push({ name: 'SignUpPage' })
}
</script>

<style scoped>
/* =========================================
   ХЕДЕР
   ========================================= */
.modern-header {
  background: linear-gradient(135deg, #1e1e24 0%, #2d2b55 100%) !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.menu-btn {
  transition: all 0.25s ease !important;
}

.menu-btn:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  transform: scale(1.1);
}

.header-title {
  font-size: 20px !important;
  font-weight: 700 !important;
  letter-spacing: 0.5px;
  color: #ffffff !important;
  display: flex;
  align-items: center;
}

/* Профиль пользователя в хедере */
.user-profile-wrapper {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 4px 12px 4px 4px;
  transition: all 0.25s ease;
}

.user-profile-wrapper:hover {
  background: rgba(255, 255, 255, 0.12);
  border-color: rgba(255, 255, 255, 0.2);
}

.user-avatar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
}

.user-name {
  font-size: 14px;
  font-weight: 500;
  color: #ffffff;
}

.logout-btn {
  transition: all 0.25s ease !important;
  color: rgba(255, 255, 255, 0.7) !important;
}

.logout-btn:hover {
  color: #ff5252 !important;
  background: rgba(255, 82, 82, 0.15) !important;
  transform: scale(1.1);
}

/* Кнопка входа для неавторизованных */
.login-btn {
  border-radius: 10px !important;
  padding: 8px 20px !important;
  font-weight: 600 !important;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
  transition: all 0.25s ease !important;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.45) !important;
}

/* =========================================
   БОКОВАЯ ПАНЕЛЬ (DRAWER)
   ========================================= */
.modern-drawer {
  background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%) !important;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04) !important;
  border-right: none !important; /* Убираем стандартный bordered */
}

.drawer-header {
  display: flex;
  align-items: center;
  padding: 20px 24px 12px 24px;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #6c757d;
}

.drawer-separator {
  margin: 0 20px 12px 20px !important;
  background: rgba(0, 0, 0, 0.06) !important;
}

/* =========================================
   КОНТЕЙНЕР СТРАНИЦ
   ========================================= */
.modern-page-container {
  background: #f4f6f8; /* Мягкий фон для контраста с белыми карточками */
}

/* =========================================
   ТЕМНАЯ ТЕМА (DARK MODE)
   ========================================= */
body.body--dark .modern-header {
  background: linear-gradient(135deg, #0f0f13 0%, #1a1a2e 100%) !important;
  border-bottom-color: rgba(255, 255, 255, 0.05);
}

body.body--dark .user-profile-wrapper {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.08);
}

body.body--dark .modern-drawer {
  background: linear-gradient(180deg, #1e1e24 0%, #16161c 100%) !important;
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3) !important;
}

body.body--dark .drawer-header {
  color: #a0a0b0;
}

body.body--dark .drawer-separator {
  background: rgba(255, 255, 255, 0.06) !important;
}

body.body--dark .modern-page-container {
  background: #121218;
}
</style>