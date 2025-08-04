<template>
    <v-dialog v-model="showDialog" max-width="300px" persistent @click:outside="emit('close')">
        <v-card>
            <v-card-title class="text-center">
                <h3 class="mb-1">Nova Aula</h3>
                <p class="text-subtitle-1">Preencha o formulário abaixo:</p>
            </v-card-title>
            <v-card-text>
                <v-form ref="form" v-model="isValid" @submit.prevent="submitForm">
                  <v-text-field
                    label="Data da Aula"
                    v-model="date"
                    type="date"
                    :rules="[rules.required]"
                    required
                    clearable
                  />
                </v-form>
            </v-card-text>
            <v-card-actions class="justify-space-between">
              <v-btn color="primary" @click="$emit('close')">Fechar</v-btn>
              <v-btn color="primary" type="submit" :disabled="!isValid" @click="submitForm">Cadastrar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed} from 'vue'
import { useClassSessionStore } from '@/stores/classSessionStore'

const classSessionStore = useClassSessionStore()

const props = defineProps({
  visible: Boolean
})

const isValid = ref(false)
const date = ref('')
const form = ref(null)

const emit = defineEmits(['update:visible', 'close', 'new-session-created'])

const rules = {
  required: (value) => !!value || 'Campo obrigatório',
}

const submitForm = async () => {
  const formEl = form.value

  if (!formEl) return
  
  const isFormValid = await formEl.validate()

  if (isFormValid && date.value){
    try {
      await classSessionStore.createNewClassSession({
        date: new Date(date.value)
      })

      date.value = ''
      emit('new-session-created')
      emit('close')
    } catch (error) {
      console.error('Erro ao criar aula:', error)
    }
  }


  if (form.value.validate()) {
    console.log('Data enviada:', date.value)
    // Aqui você pode chamar o backend com fetch/axios ou store (ex: store.createSession(date.value))
  }
}

const showDialog = computed({
  get: () => props.visible,
  set: value => emit('update:visible', value)
})
</script>
