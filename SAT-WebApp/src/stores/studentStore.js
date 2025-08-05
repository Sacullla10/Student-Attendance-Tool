import { defineStore } from 'pinia'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL

export const useStudentStore = defineStore('student', {
    state: () => ({
        loading: true,
        aux_loading: false,
        error: null,
        students: [], // Array to hold student data
    }),
    
    actions: {
        async fetchStudents() {
            try {
                this.loading = true;
                
                const response = await axios.get(`${API_URL}/students`)
                return response.data
            } catch (error) {
                throw error.response?.data?.message || 'Failed to fetch students'
            } finally {
                this.loading = false;
            }
        },

        async fetchStudentAttendanceSummary(student_id) {
            try {
                this.aux_loading = true;
                
                const response = await axios.get(`${API_URL}/students/${student_id}/attendance-summary`)
                return response.data
            } catch (error) {
                throw error.response?.data?.message || 'Failed to fetch student attendance summary'
            } finally {
                this.aux_loading = false;
            }
        }
    }
})