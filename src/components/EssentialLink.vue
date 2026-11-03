<template>
  <q-item
    :to="link"
    exact
    clickable
    v-ripple
    class="sidebar-item"
    active-class="sidebar-item-active"
  >
    <q-item-section
      v-if="icon"
      avatar
      class="sidebar-icon-wrapper"
    >
      <div class="sidebar-icon">
        <q-icon :name="icon" size="20px" color="white" />
      </div>
    </q-item-section>

    <q-item-section class="sidebar-text-section">
      <q-item-label class="sidebar-item-label">
        {{ title }}
      </q-item-label>
    </q-item-section>
  </q-item>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  link: {
    type: String,
    default: '#'
  },
  icon: {
    type: String,
  },
})
</script>

<style scoped>
/* Основные стили пункта меню */
.sidebar-item {
  margin: 6px 12px;
  padding: 12px 16px !important;
  border-radius: 14px !important;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  background: transparent !important;
  position: relative;
  overflow: hidden;
}

.sidebar-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
  opacity: 0;
  transition: opacity 0.25s ease;
  border-radius: 14px;
}

.sidebar-item:hover::before {
  opacity: 1;
}

.sidebar-item:hover {
  transform: translateX(4px);
}

/* Иконка */
.sidebar-icon-wrapper {
  min-width: 40px !important;
}

.sidebar-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-item:hover .sidebar-icon {
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.35);
}

/* Текст */
.sidebar-text-section {
  padding-left: 8px !important;
}

.sidebar-item-label {
  font-size: 15px !important;
  font-weight: 500 !important;
  color: #495057 !important;
  transition: color 0.25s ease;
  text-align: left !important;
}

.sidebar-item:hover .sidebar-item-label {
  color: #212529 !important;
}

/* Активное состояние */
.sidebar-item-active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%) !important;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
}

.sidebar-item-active::before {
  opacity: 0;
}

.sidebar-item-active .sidebar-icon {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  box-shadow: 0 6px 20px rgba(118, 75, 162, 0.4);
}

.sidebar-item-active .sidebar-item-label {
  color: #667eea !important;
  font-weight: 600 !important;
}

/* === Темная тема === */
body.body--dark .sidebar-item-label {
  color: #a0a0b0 !important;
}

body.body--dark .sidebar-item:hover .sidebar-item-label {
  color: #ffffff !important;
}

body.body--dark .sidebar-item-active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%) !important;
}

body.body--dark .sidebar-item-active .sidebar-item-label {
  color: #8b9cf7 !important;
}

body.body--dark .sidebar-item::before {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%);
}
</style>