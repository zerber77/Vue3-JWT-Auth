<template>
    <div class="modern-filter-container q-mb-md">
        <q-card-section class="text-h7 text-weight-bold">Filter by</q-card-section> 
        <q-select
        v-model="localFilterField"
        :options="fieldOptions"
        label="Поле для фильтрации"
        outlined
        class="filter-select"
        options-dense
        emit-value
        map-options
        transition-show="scale"
        transition-hide="scale"
        >
        <template v-slot:prepend>
            <q-icon name="filter_list" size="22px" color="primary" />
        </template>
        </q-select>

        <!-- Поле ввода для фильтрации -->
        <q-input
        v-model="localFilterValue"
        label="Введите значение для фильтрации..."
        outlined
        class="filter-input"
        clearable
        >
        <template v-slot:prepend>
            <q-icon name="search" size="22px" color="primary" />
        </template>
        </q-input>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    columns: {
        type: Array,
        required: true
    },
    modelValue: {
        type: String,
        default: ''
    },
    filterField: {
        type: String,
        default: 'login'
    }
})

const emit = defineEmits(['update:modelValue', 'update:filterField'])

const fieldOptions = computed(() => {
    return props.columns
        .filter(col => col.name !== 'actions')
        .map(col => ({
        label: col.label,
        value: col.name
        }))
})

const localFilterField = computed({
    get: () => props.filterField,
    set: (val) => emit('update:filterField', val)
})

const localFilterValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
})
</script>

<style scoped>
.modern-filter-container {
    display: flex;
    gap: 20px;
    align-items: stretch;
    flex-wrap: wrap;
    padding: 20px 28px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
    border-radius: 16px;
    border: 1px solid rgba(102, 126, 234, 0.15);
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.08);
    transition: all 0.3s ease;
}

.modern-filter-container:hover {
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.12);
    border-color: rgba(102, 126, 234, 0.25);
}

.filter-select {
  width: 260px;
  min-width: 240px;
}

.filter-input {
  flex: 1;
  min-width: 280px;
}

/* === Увеличение шрифтов и размеров полей === */
:deep(.q-field__control) {
  min-height: 56px !important;
  border-radius: 12px !important;
  transition: all 0.25s ease !important;
}

:deep(.q-field__label) {
  font-size: 15px !important;
  font-weight: 500 !important;
  color: #6b7280 !important;
  top: 18px !important;
}

:deep(.q-field__native) {
  font-size: 16px !important;
  font-weight: 500 !important;
  color: #1f2937 !important;
  padding-top: 4px !important;
}

/* Эффект при фокусе */
:deep(.q-field--outlined.q-field--highlighted .q-field__control) {
  border-width: 2px !important;
  border-color: #667eea !important;
  box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
}

:deep(.q-field--outlined.q-field--highlighted .q-field__label) {
  color: #667eea !important;
  font-weight: 600 !important;
}

/* Кнопка очистки (крестик) */
:deep(.q-field__append .q-icon) {
  font-size: 20px !important;
  color: #9ca3af !important;
  transition: color 0.2s ease;
}
:deep(.q-field__append .q-icon:hover) {
  color: #ef4444 !important;
}

/* === Темная тема === */
body.body--dark .modern-filter-container {
  background: linear-gradient(135deg, #1e1e24 0%, #252530 100%);
  border-color: rgba(102, 126, 234, 0.3);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

body.body--dark .modern-filter-container:hover {
  border-color: rgba(102, 126, 234, 0.5);
  box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
}

body.body--dark :deep(.q-field__control) {
  background: rgba(255, 255, 255, 0.03);
}

body.body--dark :deep(.q-field__label) {
  color: #9ca3af !important;
}

body.body--dark :deep(.q-field__native) {
  color: #f3f4f6 !important;
}

body.body--dark :deep(.q-field--outlined.q-field--highlighted .q-field__control) {
  border-color: #818cf8 !important;
  box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.15) !important;
}

body.body--dark :deep(.q-field--outlined.q-field--highlighted .q-field__label) {
  color: #818cf8 !important;
}

/* === mobile === */
@media (max-width: 768px) {
    .modern-filter-container {
        flex-direction: column;
        gap: 16px;
        padding: 16px 20px;
    }
    
    .filter-select,
    .filter-input {
        width: 100%;
        min-width: 100%;
  }
}
</style>