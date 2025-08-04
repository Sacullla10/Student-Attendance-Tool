<template>
    <new-class-session
        :visible="showNewSessionDialog"
        @close="showNewSessionDialog = false"
        @new-session-created="reloadClassSessions"
    />

    <v-snackbar v-model="snackbar.show" :timeout="3000" color="success">
        {{ snackbar.message }}
    </v-snackbar>

    <span>
        <h1 class="mb-4">Lista de Aulas</h1>
        <p class="mb-4">
            Gerencie a lista de aulas, adicione novos registros e a presença de alunos.
        </p>
    </span>
    <v-container>
            <v-row>
                <template v-if="classSessionStore.loading" >
                    <v-row class="justify-center">
                        <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                size="64"
                                width="6"
                            ></v-progress-circular>
                            <span class="ml-4">Carregando aulas...</span>
                        </v-col>
                    </v-row>
                </template>
                <template v-else-if="classSessions.length > 0">
                    <v-row>
                        <v-col cols="5">
                            <v-card>
                                <v-card-title>
                                    <v-row class="align-center justify-space-between ma-1">
                                        Lista de Aulas
                                        <v-btn 
                                            color="primary"
                                            icon="mdi-plus"
                                            variant="outlined"
                                            size="small"
                                            density="compact"
                                            @click="showNewSessionDialog = true"
                                        />
                                    </v-row>
                                </v-card-title>
                                <v-card-text>
                                    <v-table>
                                        <thead>
                                            <tr>
                                                <th class="text-left" style="width: 40%;">
                                                    Data da Aula
                                                </th>
                                                <th class="text-center" style="width: 60%;">
                                                    Ações
                                                </th>
                                            </tr>
                                        </thead>
                                    </v-table>
                                    <div class="scrollable-table-body">
                                        <v-table>
                                            <tbody>
                                                <tr v-for="session in classSessions" :key="session.id">
                                                    <td class="text-left" style="width: 40%;">
                                                        {{ classSessionStore.formatDate(session.date) }}
                                                    </td>
                                                    <td class="text-center" style="width: 60%;">
                                                        <v-btn color="primary" @click="selectedSession = session; showAttendanceDialog = true">
                                                            Presença
                                                        </v-btn>
                                                    </td>

                                                </tr>
                                            </tbody>
                                        </v-table>
                                    </div>
                                </v-card-text>
                                <v-card-actions class="justify-end">
                                    <v-btn color="primary" @click="showNewSessionDialog = true">
                                        Adicionar Aula
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                        <v-col cols="7">
                            <v-card>
                                <v-card-title>
                                    <v-row class="ma-1">
                                        Detalhes da Aula : {{ selectedSession ? classSessionStore.formatDate(selectedSession.date) : 'Nenhuma Aula Selecionada' }}
                                    </v-row>
                                </v-card-title>
                                <v-card-text>
                                    <v-table>
                                        <thead>
                                            <tr>
                                                <th class="text-left" style="width: 70%;">
                                                    Nome do Aluno
                                                </th>
                                                <th class="text-center" style="width: 30%;">
                                                    Presença
                                                </th>
                                            </tr>
                                        </thead>
                                    </v-table>
                                    <template v-if="selectedSession">
                                        <template v-if="attendanceStore.loading">
                                            <v-row class="justify-center">
                                                <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                                                    <v-progress-circular
                                                        indeterminate
                                                        color="primary"
                                                        size="32"
                                                        width="6"
                                                    ></v-progress-circular>
                                                    <span class="ml-4">Carregando alunos...</span>
                                                </v-col>
                                            </v-row>
                                        </template>
                                        <template v-else-if="studentList.length > 0">
                                            <div class="scrollable-table-body">
                                                <v-table>
                                                    <tbody>
                                                        <tr v-for="student in studentList" :key="student.id">
                                                            <td class="text-left" style="width: 70%;">
                                                                {{ student.name }}
                                                            </td>
                                                            <td class="text-center" style="width: 30%;">
                                                                <div class="d-flex justify-center">
                                                                    <v-switch
                                                                        v-model="student.is_present"
                                                                        color="success"
                                                                        hide-details="true"
                                                                    />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </v-table>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <v-row class="justify-center">
                                                <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                                                    <p>Nenhum aluno encontrado para esta aula.</p>
                                                </v-col>
                                            </v-row>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <v-row class="justify-center">
                                                <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                                                    <p>Selecione uma aula para ver os detalhes.</p>
                                                </v-col>
                                            </v-row>
                                    </template>    
                                </v-card-text>
                                <v-card-actions class="justify-end">
                                    <v-btn color="primary" @click="showAttendanceDialog = true">
                                        Salvar Presença
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </template>
                <template v-else>
                    <v-row class="justify-center">
                        <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                            <p>Nenhuma aula encontrada.</p>
                        </v-col>
                    </v-row>
                </template>
            </v-row>
    </v-container>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useClassSessionStore } from '@/stores/classSessionStore'
import { useAttendancesStore } from '@/stores/attendancesStore'
import newClassSession from '@/components/classSession/newClassSession.vue'

const classSessionStore = useClassSessionStore()
const attendanceStore = useAttendancesStore()

const classSessions = ref([])
const studentList = ref([])
const selectedSession = ref(null)
const showNewSessionDialog = ref (false)
const snackbar = ref({
  show: false,
  message: ''
})

const fetchAttendancesBySessionId = async (session_id) => {
  try {
    const response = await attendanceStore.fetchAttendancesBySessionId(session_id)
        studentList.value = response.attendances
  } catch (error) {
    console.error('Error fetching students:', error)
  }
}

const fetchClassSessions = async () => {
  try {
    const response = await classSessionStore.fetchClassSessions()
    classSessions.value = response.classSessionsList
  } catch (error) {
    console.error('Error fetching class sessions:', error)
  }
}

const reloadClassSessions = async () => {
  await fetchClassSessions()
  showSnackbar('Aula cadastrada com sucesso!')
}

const showSnackbar = (msg) => {
  snackbar.value.message = msg
  snackbar.value.show = true
}

onMounted(() => {
  fetchClassSessions()
})

watch(
  selectedSession,
  (newVal) => {
    console.log('Selected session changed:', newVal)
    if (newVal && newVal?.id > 1) {
      fetchAttendancesBySessionId(newVal.id)
    } else {
      console.log('Nenhuma aula selecionada válida!')
    }
  },
  { deep: true }
)
</script>