<template>
    <q-dialog 
        v-model="confirm" 
        transition-show="scale" 
        transition-hide="scale"
        persistent
    >
        <q-card class="custom-confirm-card">
        
        <!-- Блок с иконкой -->
        <q-card-section class="text-center q-pt-xl q-pb-none">
            <div class="icon-wrapper">
            <q-icon name="delete_forever" size="36px" color="white" />
            </div>
        </q-card-section>

        <!-- Блок с текстом -->
        <q-card-section class="text-center q-pt-md q-px-xl">
            <div class="text-h6 text-weight-bold text-grey-9 dialog-title">
            Подтвердите удаление
            </div>
            <p class="text-body2 text-grey-6 q-mt-sm q-mb-none dialog-text">
            {{ props.confirmMessage || 'Вы уверены, что хотите удалить этот элемент? Это действие нельзя отменить.' }}
            </p>
        </q-card-section>

        <!-- btns -->
        <q-card-actions align="center" class="q-pb-xl q-px-xl q-pt-md">
            <q-btn
            flat
            label="Отмена"
            color="grey-7"
            class="btn-custom btn-cancel"
            v-close-popup
            no-caps
            />
            <q-btn
            unelevated
            label="Удалить"
            color="red-6"
            icon="delete_outline"
            class="btn-custom btn-delete"
            v-close-popup
            @click="$emit('delete')"
            no-caps
            />
        </q-card-actions>

        </q-card>
    </q-dialog>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    confirmMessage: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'delete'])

//  v-model 
const confirm = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})
</script>

<style scoped>
/* Стили для самой карточки */
.custom-confirm-card {
  border-radius: 24px !important;
  min-width: 420px;
  max-width: 480px;
  box-shadow: 0 15px 45px rgba(0, 0, 0, 0.12) !important;
  overflow: hidden;
}

/* Красивая иконка с градиентом и свечением */
.icon-wrapper {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #ff5252 0%, #d50000 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  box-shadow: 0 10px 25px rgba(213, 0, 0, 0.3);
}

/* Стили для кнопок */
.btn-custom {
  border-radius: 14px !important;
  padding: 12px 28px !important;
  font-weight: 600 !important;
  font-size: 15px !important;
  min-width: 130px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.btn-cancel {
  background-color: rgba(0, 0, 0, 0.04);
}

.btn-cancel:hover {
  background-color: rgba(0, 0, 0, 0.08) !important;
}

.btn-delete {
  box-shadow: 0 6px 16px rgba(213, 0, 0, 0.35);
}

.btn-delete:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 22px rgba(213, 0, 0, 0.45) !important;
}

.btn-delete:active {
  transform: translateY(0);
}

/* === Адаптация для Темной темы (Dark Mode) === */
body.body--dark .custom-confirm-card {
  background: #1e1e24;
  box-shadow: 0 15px 45px rgba(0, 0, 0, 0.5) !important;
}

body.body--dark .dialog-title {
  color: #ffffff !important;
}

body.body--dark .dialog-text {
  color: #a0a0b0 !important;
}

body.body--dark .btn-cancel {
  background-color: rgba(255, 255, 255, 0.08);
  color: #e0e0e0 !important;
}

body.body--dark .btn-cancel:hover {
  background-color: rgba(255, 255, 255, 0.12) !important;
}

/* Мобильная адаптация */
@media (max-width: 500px) {
  .custom-confirm-card {
    min-width: 90vw;
    border-radius: 20px !important;
  }
  
  .btn-custom {
    width: 100%;
  }
  
  /* На мобильных делаем кнопки друг под другом */
  .q-card__actions {
    flex-direction: column-reverse;
    gap: 12px;
  }
}
</style>