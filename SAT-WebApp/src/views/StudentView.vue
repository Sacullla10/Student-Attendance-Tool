<template>
    <StudentAttendanceSummary
        :visible="showAttendanceDialog"
        :student="selectedStudent"
        @close="showAttendanceDialog = false"
    />

    <span>
        <h1 class="mb-4">Lista de Alunos</h1>
        <p class="mb-4">
            Gerencie a lista de alunos, adicione novos registros e visualize detalhes.
        </p>
    </span>
    <v-container>
        <v-row>
            <template v-if="studentStore.loading" >
                <v-row class="justify-center">
                    <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="64"
                            width="6"
                        ></v-progress-circular>
                        <span class="ml-4">Carregando alunos...</span>
                    </v-col>
                </v-row>
            </template>
            <template v-else-if="students.length > 0">
                <v-col
                    v-for="(col, index) in columns"
                    :key="index"
                    cols="12"
                    md="4"
                >
                    <v-card
                        v-for="student in col"
                        :key="student.id"
                        class="mb-4"
                    >
                        <v-card-title>{{ student.name }}</v-card-title>
                        <v-card-actions>
                            <v-btn color="primary" @click="selectedStudent = student; showAttendanceDialog = true">
                                Detalhes de Presença
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </template>
            <template>
                <v-row class="justify-center">
                    <v-col cols="12" class="d-flex flex-column justify-center align-center" style="height: 200px;">
                        <span class="ml-4">Nenhum aluno encontrado.</span>
                    </v-col>
                </v-row>
            </template>
        </v-row>
        <v-pagination
            v-model="currentPage"
            :length="pageCount"
            class="mt-4"
            color="primary"
            circle
        />
    </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStudentStore } from '@/stores/studentStore'
import StudentAttendanceSummary from '@/components/student/StudentAttendanceSummary.vue'

const studentStore = useStudentStore()

const students = ref([])
const selectedStudent = ref(null)

const showAttendanceDialog = ref(false)

const itemsPerPage = 12
const numberOfColumns = 3
const currentPage = ref(1)
const pageCount = computed(() => {
  return Math.ceil(students.value.length / itemsPerPage) || 1
})

const fetchStudents = async () => {
  try {
    const response = await studentStore.fetchStudents()
    students.value = response.studentsList
  } catch (error) {
    console.error('Error fetching students:', error)
  }
}

const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return students.value.slice(start, start + itemsPerPage)
})

const columns = computed(() => {
    const colHeight = Math.ceil(paginatedStudents.value.length / numberOfColumns)
    const cols = Array.from({ length: numberOfColumns }, () => [])

    paginatedStudents.value.forEach((student, index) => {
        const colIndex = Math.floor(index / colHeight)
        cols[colIndex].push(student)
    })
    return cols
})


onMounted(() => {
  fetchStudents()

})
</script>