<template>
        <q-dialog v-model="confirm">
        <q-card>
            <q-card-section class="row items-center">
            <q-avatar icon="warning" color="red" text-color="white" />
            <span class="q-ml-sm">
                {{props.confirmMessage}}
            </span>
            </q-card-section>

            <q-card-actions align="right">
            <q-btn flat label="Cancel" color="primary" v-close-popup />
            <q-btn flat label="Delete" color="red" v-close-popup @click="$emit('delete')"/>
            </q-card-actions>
        </q-card>
        </q-dialog>

</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    confirmMessage:String,
})

const confirm = ref(false)
watch(()=>confirm.value,(newVal)=>{
debugger

})

</script>