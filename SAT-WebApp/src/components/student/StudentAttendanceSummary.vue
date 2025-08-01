<template>
    <v-dialog v-model="showDialog" max-width="350px" persistent @click:outside="emit('close')">
        <v-card>
            <v-card-title class="text-center">
                <h3 class="mb-1">Resumo de Presença</h3>
                <p class="text-subtitle-1">{{student?.name}}</p>
            </v-card-title>
            <v-card-text>
                <v-text-field
                    label="Dias presente:"
                    v-model="attendanceSummary.presentDays"
                    readonly
                />
                <v-text-field
                    label="Dias ausente:"
                    v-model="attendanceSummary.absentDays"
                    readonly
                />
            </v-card-text>
            <v-card-actions class="justify-center">
                <v-btn color="primary" @click="$emit('close')">Fechar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed} from 'vue'

const props = defineProps({
  student: Object,
  visible: Boolean
})

const emit = defineEmits(['update:visible', 'close'])

const attendanceSummary = ref({
  presentDays: 0,
  absentDays: 0
})

const showDialog = computed({
  get: () => props.visible,
  set: value => emit('update:visible', value)
})
</script>
