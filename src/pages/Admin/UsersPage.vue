<template>
  <ErrorMessageComponent
    v-model = "error"
    :errorMessage = "errorMessage"
  />
  <HelpMessageComponent
    v-model = "help"
    :helpMessage = "helpMessage"
  />
    <div>
        <div 
            v-if="isAdmin"
            class="q-ma-md"
        >
            <q-table
                    class="my-sticky-header-column-table"
                    flat
                    bordered
                    title="USERS"
                    :rows="rows"
                    :columns="columns"
                    row-key="id"
            >
            <!--  actions -->
                <template v-slot:body-cell-actions="props">
                    <q-td :props="props">
                        <!-- Если пользователь админ - показываем кнопку "Сделать пользователем" -->
                        <q-btn 
                            v-if="props.row.role === ROLES.ADMIN"
                            flat 
                            round 
                            dense 
                            color="warning" 
                            icon="person" 
                            @click="changeUserRole(props.row, ROLES.USER)"
                        >
                            <q-tooltip>Сделать пользователем</q-tooltip>
                        </q-btn>
                        
                        <!-- Если пользователь не админ - показываем кнопку "Сделать админом" -->
                        <q-btn 
                            v-else
                            flat 
                            round 
                            dense 
                            color="primary" 
                            icon="admin_panel_settings" 
                            @click="changeUserRole(props.row, ROLES.ADMIN)"
                        >
                            <q-tooltip>Сделать админом</q-tooltip>
                        </q-btn>
                        
                        <q-btn 
                            flat 
                            round 
                            dense 
                            color="negative" 
                            icon="delete" 
                            @click="deleteUser(props.row)"
                        >
                            <q-tooltip>Удалить</q-tooltip>
                        </q-btn>
                    </q-td>
                </template>
            </q-table>
        </div>
        <div v-else>
            {{ `What are you doing here?`  }}
        </div>
    </div>
    <!-- <pre>{{ authorised }}</pre> -->
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import { useAuthStore } from 'stores/auth'
import useMessageVars from "components/modules/messages/getMessageVars";
import ErrorMessageComponent from "components/Modals/ErrorMessageComponent.vue";
import HelpMessageComponent from "components/Modals/HelpMessageComponent.vue";

import getUsers from "src/components/modules/admin/getUsers";
import { patchUser } from "src/components/modules/admin/patchUser";
import { deleteUserById } from "src/components/modules/admin/deleteUserById";

const ROLES = {
    ADMIN: 'admin',
    USER: 'user',
    // MODERATOR: 'moderator',
}

const authStore = useAuthStore()  
const authorised = computed(()=> authStore.getTokenData())
const {help, error, helpMessage, errorMessage, setHelpMessage, setErrorMessage, clearMessages} = useMessageVars()

const isAdmin = computed(() => authorised.value?.role === ROLES.ADMIN)

const columns = [
{name: 'id',required: true,label: 'ID',align: 'center',field: 'id',sortable: true},
{name: 'login',align: 'center',label: 'login',field: 'login',sortable: true},
{name: 'name', label: 'name', field: 'name', sortable: true },
{name: 'email', label: 'email', field: 'email', sortable: true },
{name: 'signed', label: 'signed', field: 'signed', sortable: true },
{name: 'last_visit', label: 'last visit', field: 'last_visit',sortable: true },
{name: 'role', label: 'role', field: 'role', sortable: true },
{name: 'actions', label: 'Actions', field: 'actions', align: 'center'}
]

let rows = ref([])
async function  fetchUsers() {
  try{
    const {response} = await getUsers()
    rows.value = response?.value
  }catch (e) {
    console.error('Failed to get users:', e)
    setErrorMessage('Failed to get users')
  }
  
}
onMounted( ()=>{
  fetchUsers()
})

const changeUserRole = async (row, newRole) => {
  try{
    const response = await patchUser( {id:row.id, role: newRole })
    const err = response.data.value?.error;
    if (err) {
      setErrorMessage(err.message || err || 'Server error, try again later');
      return;
    }

    if(response.data.value.message){
      setHelpMessage(response.data.value.message)
      row.role = newRole
    }
  }catch (e) {
        console.error('Failed to update user:', e)
        setErrorMessage('Failed to update user')
    }
  
}

const deleteUser = async(row) => {
        try{
          const response = await deleteUserById( {id:row.id})
          const err = response.data.value?.error;
          if (err) {
            setErrorMessage(err.message || err || 'Server error, try again later');
            return;
          }
          if(response.data.value.message){
            setHelpMessage(response.data.value.message)
            fetchUsers()
            ///another faster way
            ///rows.value = rows.value.filter(user => user.id !== row.id)
          }
        }catch (e) {
              console.error('Failed to delete user:', e)
              setErrorMessage('Failed to delete user')
          }  
}
    

</script>

<style lang="sass">
.my-sticky-header-column-table
  /* height or max-height is important */
  height: 500px

  /* specifying max-width so the example can
    highlight the sticky column on any browser window */
  max-width: 1800px


  td:first-child
    /* bg color is important for td; just specify one */
    background-color: #d8dcdc

  tr th
    position: sticky
    /* higher than z-index for td below */
    z-index: 2
    /* bg color is important; just specify one */
    background: #d8dcdc
    font-size: 16px

  /* this will be the loading indicator */
  thead tr:last-child th
    /* height of all previous header rows */
    top: 48px
    /* highest z-index */
    z-index: 3
  thead tr:first-child th
    top: 0
    z-index: 1
  tr:first-child th:first-child
    /* highest z-index */
    z-index: 3

  td:first-child
    z-index: 1

  td:first-child, th:first-child
    position: sticky
    left: 0

  /* prevent scrolling behind sticky top row on focus */
  tbody
    /* height of all previous header rows */
    scroll-margin-top: 48px
</style>