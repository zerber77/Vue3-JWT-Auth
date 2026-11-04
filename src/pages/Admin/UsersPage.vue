<template>
  <ErrorMessageComponent
    v-model="error"
    :errorMessage="errorMessage"
  />
  <HelpMessageComponent
    v-model="help"
    :helpMessage="helpMessage"
  />
  <ConfirmMessageComponent
    v-model="confirm"
    :confirmMessage="confirmMessage"
    @delete="onDeleteUser"
  />
  
  <div>
    <div v-if="isAdmin" class="q-ma-md">
      <q-table
        class="modern-users-table my-sticky-column-table"
        flat
        title="Пользователи"
        :rows="filteredRows" 
        :columns="columns"
        row-key="id"
        :rows-per-page-options="[10, 20, 50]"
        :pagination="{ rowsPerPage: 10 }"
      >
        <!-- Кастомный заголовок таблицы с интегрированным фильтром -->
        <template v-slot:top>
          <div class="table-header">
            <div class="table-title-wrapper">
              <div class="table-icon">
                <q-icon name="group" size="24px" color="white" />
              </div>
              <div class="table-title-content">
                <div class="text-h6 text-weight-bold">Пользователи</div>
                <div class="text-caption text-grey-6">Управление пользователями системы</div>
              </div>
            </div>
          </div>
          
          <!-- ВСТАВКА КОМПОНЕНТА ФИЛЬТРА -->
          <FiltersComponent 
            v-model="filterValue" 
            v-model:filterField="filterField" 
            :columns="columns" 
          />
        </template>

        <!-- Кастомные ячейки действий -->
        <template v-slot:body-cell-actions="props">
          <q-td :props="props" class="actions-cell">
            <div class="actions-wrapper">
              <q-btn
                v-if="props.row.role === ROLES.ADMIN"
                unelevated
                round
                dense
                color="warning"
                icon="person"
                class="action-btn action-btn-warning"
                @click="changeUserRole(props.row, ROLES.USER)"
              >
                <q-tooltip>Сделать пользователем</q-tooltip>
              </q-btn>
              
              <q-btn
                v-else
                unelevated
                round
                dense
                color="primary"
                icon="admin_panel_settings"
                class="action-btn action-btn-primary"
                @click="changeUserRole(props.row, ROLES.ADMIN)"
              >
                <q-tooltip>Сделать админом</q-tooltip>
              </q-btn>
              
              <q-btn
                unelevated
                round
                dense
                color="red-6"
                icon="delete"
                class="action-btn action-btn-delete"
                @click="deleteUser(props.row)"
              >
                <q-tooltip>Удалить</q-tooltip>
              </q-btn>
            </div>
          </q-td>
        </template>
      </q-table>
    </div>
    <div v-else>
      {{ `What are you doing here?` }}
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useAuthStore } from 'stores/auth'
import useMessageVars from "components/modules/messages/getMessageVars";
import ErrorMessageComponent from "components/Modals/ErrorMessageComponent.vue";
import HelpMessageComponent from "components/Modals/HelpMessageComponent.vue";
import ConfirmMessageComponent from "components/Modals/ConfirmMessageComponent.vue";

import FiltersComponent from "src/components/admin/FiltersComponent.vue";

import getUsers from "src/components/modules/admin/getUsers";
import { patchUser } from "src/components/modules/admin/patchUser";
import { deleteUserById } from "src/components/modules/admin/deleteUserById";

const ROLES = {
  ADMIN: 'admin',
  USER: 'user',
}

const authStore = useAuthStore()  
const authorised = computed(() => authStore.getTokenData())
const { help, error, helpMessage, errorMessage, setHelpMessage, setErrorMessage } = useMessageVars()
const confirm = ref(false)
const confirmMessage = ref("")

const isAdmin = computed(() => authorised.value?.role === ROLES.ADMIN)

const columns = [
  { name: 'id', required: true, label: 'ID', align: 'center', field: 'id', sortable: true },
  { name: 'login', align: 'center', label: 'login', field: 'login', sortable: true },
  { name: 'name', label: 'name', field: 'name', sortable: true },
  { name: 'email', label: 'email', field: 'email', sortable: true },
  { name: 'signed', label: 'signed', field: 'signed', sortable: true },
  { name: 'last_visit', label: 'last visit', field: 'last_visit', sortable: true },
  { name: 'role', label: 'role', field: 'role', sortable: true },
  { name: 'actions', label: 'Actions', field: 'actions', align: 'center' }
]

let rows = ref([])

// === НОВОЕ: Переменные для фильтрации ===
const filterField = ref('login') // Поле по умолчанию для поиска
const filterValue = ref('')

// === НОВОЕ: Вычисляемое свойство для мгновенной фильтрации ===
const filteredRows = computed(() => {
  if (!filterValue.value.trim()) {
    return rows.value
  }
  
  const searchLower = filterValue.value.toLowerCase()
  
  return rows.value.filter(row => {
    const cellValue = row[filterField.value]
    if (cellValue === undefined || cellValue === null) return false
    return String(cellValue).toLowerCase().includes(searchLower)
  })
})

async function fetchUsers() {
  try {
    const { response } = await getUsers()
    rows.value = response?.value || []
  } catch (e) {
    console.error('Failed to get users:', e)
    setErrorMessage('Failed to get users')
  }
}

onMounted(() => {
  fetchUsers()
})

const changeUserRole = async (row, newRole) => {
  try {
    const response = await patchUser({ id: row.id, role: newRole })
    const err = response.data.value?.error;
    if (err) {
      setErrorMessage(err.message || err || 'Server error, try again later');
      return;
    }

    if (response.data.value.message) {
      setHelpMessage(response.data.value.message)
      row.role = newRole
    }
  } catch (e) {
    console.error('Failed to update user:', e)
    setErrorMessage('Failed to update user')
  }
}

let selectedRowId = null

const deleteUser = (row) => {
  selectedRowId = row.id
  confirmMessage.value = "Are you sure want do remove " + row.name + "?"
  confirm.value = true    
}

const onDeleteUser = async () => {
  if (!selectedRowId) return
  try {
    const response = await deleteUserById({ id: selectedRowId })
    const err = response.data.value?.error;
    if (err) {
      setErrorMessage(err.message || err || 'Server error, try again later');
      return;
    }
    if (response.data.value.message) {
      setHelpMessage(response.data.value.message)
      fetchUsers()
    }
  } catch (e) {
    console.error('Failed to delete user:', e)
    setErrorMessage('Failed to delete user')
  }  
}
</script>

<style scoped>
.modern-users-table {
  border-radius: 20px !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08) !important;
  overflow: hidden;
  background: white;
}

.table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 20px 24px;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.table-title-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
}

.table-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.3);
}

.table-title-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

:deep(.q-table th) {
  background: #f8f9fa !important;
  font-weight: 600 !important;
  text-transform: uppercase;
  font-size: 12px !important;
  letter-spacing: 0.5px;
  color: #6c757d !important;
  padding: 16px 12px !important;
  border-bottom: 2px solid rgba(0, 0, 0, 0.06) !important;
}

:deep(.q-table tbody tr) {
  transition: background 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

:deep(.q-table tbody tr:hover) {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.06) 0%, rgba(118, 75, 162, 0.06) 100%) !important;
  /* Можно добавить легкую тень вместо увеличения масштаба, если хочется "приподнять" строку: */
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

:deep(.q-table th),
:deep(.q-table td) {
  box-sizing: border-box !important;
}

:deep(.q-table tbody tr.selected) {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%) !important;
}

:deep(.q-table tbody tr:hover td) {
  background: transparent !important;
}

:deep(.q-table td) {
  padding: 16px 12px !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
  font-size: 14px;
  color: #495057;
}

.actions-cell {
  text-align: center;
}

.actions-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.action-btn {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.action-btn:hover {
  transform: translateY(-3px) scale(1.1);
}
.action-btn:active {
  transform: translateY(0) scale(0.95);
}

.action-btn-primary {
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
}

.action-btn-primary:hover {
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.45) !important;
}

.action-btn-warning {
  box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3) !important;
}

.action-btn-warning:hover {
  box-shadow: 0 6px 20px rgba(255, 193, 7, 0.45) !important;
}

.action-btn-delete {
  box-shadow: 0 4px 12px rgba(213, 0, 0, 0.3) !important;
}

.action-btn-delete:hover {
  box-shadow: 0 6px 20px rgba(213, 0, 0, 0.45) !important;
}

:deep(.q-table__bottom) {
  border-top: 1px solid rgba(0, 0, 0, 0.06) !important;
  padding: 16px 24px !important;
  background: #f8f9fa !important;
}

:deep(.q-table__control select) {
  border-radius: 8px !important;
  border: 1px solid rgba(0, 0, 0, 0.1) !important;
  padding: 6px 12px !important;
  font-size: 13px !important;
}

body.body--dark .modern-users-table {
  background: #1e1e24;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5) !important;
}

body.body--dark .table-header {
  background: linear-gradient(135deg, #2a2a32 0%, #1e1e24 100%);
  border-bottom-color: rgba(255, 255, 255, 0.06);
}

body.body--dark .table-title-content .text-caption {
  color: #a0a0b0 !important;
}

body.body--dark :deep(.q-table th) {
  background: #2a2a32 !important;
  color: #a0a0b0 !important;
  border-bottom-color: rgba(255, 255, 255, 0.06) !important;
}

body.body--dark :deep(.q-table tbody tr:hover) {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%) !important;
}

body.body--dark :deep(.q-table td) {
  border-bottom-color: rgba(255, 255, 255, 0.04) !important;
  color: #e0e0e0;
}

body.body--dark :deep(.q-table__bottom) {
  background: #2a2a32 !important;
  border-top-color: rgba(255, 255, 255, 0.06) !important;
}

/* block first column/////////////////////////////////////*/
/* :deep(.my-sticky-column-table) {
  max-width: 1600px;
} */

:deep(.my-sticky-column-table thead tr:first-child th:first-child) {
  background-color: #c7c9ca;
}

:deep(.my-sticky-column-table tbody:not(.q-virtual-scroll__padding) td:first-child) {
  background-color: #c7c9ca;
}

:deep(.my-sticky-column-table th:first-child),
:deep(.my-sticky-column-table tbody:not(.q-virtual-scroll__padding) td:first-child) {
  position: sticky;
  left: 0;
  z-index: 1000;
  box-shadow: 0 -1px 0 #606263;
}

@media (max-width: 768px) {
  .table-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  
  .actions-wrapper {
    gap: 6px;
  }
  
  .action-btn {
    width: 32px;
    height: 32px;
  }
}
</style>