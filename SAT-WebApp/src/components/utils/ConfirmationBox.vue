<template>
    <v-dialog v-model="showDialog" max-width="250px" persistent @click:outside="emit('close')">
        <v-card>
            <v-card-title class="text-center">
              <h3 class="mb-1">Tem certeza?</h3>
            </v-card-title>
            <v-card-subtitle class="text-center">
              <p class="text-wrap"> Ao confirmar, esta ação não poderá ser desfeita</p>
            </v-card-subtitle>
            <v-card-actions class="justify-space-between">
              <v-btn color="primary" @click="$emit('close')">Cancelar</v-btn>
              <v-btn color="primary" @click="confirmAction">Confirmar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import {computed} from 'vue'


const props = defineProps({
  visible: Boolean
})

const emit = defineEmits(['update:visible', 'close', 'confirm'])

const showDialog = computed({
  get: () => props.visible,
  set: value => emit('update:visible', value)
})

const confirmAction = () => {
  emit('confirm')
  emit('close')
}
</script>
