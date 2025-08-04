import { defineStore } from 'pinia'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL

export const useClassSessionStore = defineStore('classSession', {
    state: () => ({
        loading: false,
        error: null,
        classSessions: [], // Array to hold student data
    }),
    
    actions: {
        async fetchClassSessions() {
            try {
                this.loading = true;
                
                console.log('Fetching class sessions from:', `${API_URL}/class-sessions`)
                const response = await axios.get(`${API_URL}/class-sessions`)
                return response.data
            } catch (error) {
                throw error.response?.data?.message || 'Failed to fetch class sessions'
            } finally {
                this.loading = false;
            }
        },

        formatDate(selectedDate) {
            const date = new Date(selectedDate)

            if (isNaN(date.getTime())) return 'Data inválida'

            return date.toLocaleDateString('pt-BR', {
                timeZone: 'UTC',
            })
        }
    }
})