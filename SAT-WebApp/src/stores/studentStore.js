import { defineStore } from 'pinia'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL

export const useStudentStore = defineStore('student', {
    state: () => ({
        loading: true,
        error: null,
        students: [], // Array to hold student data
    }),
    
    actions: {
        async fetchStudents() {
            try {
                this.loading = true;
                
                console.log('Fetching students from:', `${API_URL}/student/list`)
                const response = await axios.get(`${API_URL}/student/list`)
                return response.data
            } catch (error) {
                throw error.response?.data?.message || 'Failed to fetch students'
            } finally {
                this.loading = false;
            }
        },
    }
})