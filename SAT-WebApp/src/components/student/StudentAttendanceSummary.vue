<template>
    <v-dialog v-model="showDialog" max-width="350px" persistent @click:outside="emit('close')">
        <v-card>
            <v-card-title class="text-center">
                <h3 class="mb-1">Resumo de Presença</h3>
                <p class="text-subtitle-1">{{student?.name}}</p>
            </v-card-title>
            <v-card-text>
                <template v-if="studentStore.aux_loading">
                    <v-row class="justify-center">
                        <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                size="16"
                                width="2"
                            ></v-progress-circular>
                            <span class="ml-4">Carregando resumo de presença...</span>
                        </v-col>
                    </v-row>
                </template>
                <template v-else>
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
                    </template>
            </v-card-text>
            <v-card-actions class="justify-center">
                <v-btn color="primary" @click="$emit('close')">Fechar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, computed, watch} from 'vue'
import { useStudentStore } from '@/stores/studentStore'

const studentStore = useStudentStore()

const props = defineProps({
  student: Object,
  visible: Boolean
})

const emit = defineEmits(['update:visible', 'close'])

const attendanceSummary = ref({
    totalDays: 0,
    presentDays: 0,
    absentDays: 0
})

const lastStudentId = ref(null)

const showDialog = computed({
  get: () => props.visible,
  set: value => emit('update:visible', value)
})

const fetchStudentSummary = async (student_id) => {
  try {
    const response = await studentStore.fetchStudentAttendanceSummary(student_id)
    attendanceSummary.value = response.attendance_summary
  } catch (error) {
    console.error('Error fetching students:', error)
  }
}

watch(
  () => props.visible,
  (visible) => {
    if (visible && props.student?.id !== lastStudentId.value) {
      lastStudentId.value = props.student?.id
      fetchStudentSummary(props.student.id)
    }
  }
)
</script>
