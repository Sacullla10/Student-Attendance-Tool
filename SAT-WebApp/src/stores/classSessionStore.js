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
                const response = await axios.get(`${API_URL}/class-sessions`)
                return response.data
            } catch (error) {
                throw error.response?.data?.message || 'Failed to fetch class sessions'
            } finally {
                this.loading = false;
            }
        },

        async createNewClassSession(request) {
            try {
                if (!request?.date) {
                    throw new Error('A data da aula é obrigatória.')
                }

                const formattedDate = request.date instanceof Date
                    ? request.date.toISOString().split('T')[0]
                    : request.date
                
                const response = await axios.post(`${API_URL}/class-sessions`, {
                    session_date: formattedDate
                })
                return response.data
            } catch (error) {
                const message =
                    error.response?.data?.message ||
                    error.message ||
                    'Erro ao criar nova aula.'

                console.error('Erro ao criar aula:', message)
                throw new Error(message)
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