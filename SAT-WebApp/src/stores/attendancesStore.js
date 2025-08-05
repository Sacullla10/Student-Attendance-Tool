import { defineStore } from 'pinia'
import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL

export const useAttendancesStore = defineStore('attendances', {
    state: () => ({
        loading: false,
        error: null,
        attendances: [], 
    }),
    
    actions: {
        async fetchAttendancesBySessionId(session_id) {
            try {
                this.loading = true;
        
                const response = await axios.get(`${API_URL}/students/by-session/${session_id}`)
                return response.data
            } catch (error) {
                throw error.response?.data?.error || 'Failed to fetch students'
            } finally {
                this.loading = false;
            }
        },

        async deleteAttendanceBySessionId(sessionId){
            try {
                const response = await axios.delete(`${API_URL}/class-sessions/${sessionId}/attendances`)
                return response.data

            } catch (error) {
                throw error.response?.data?.error || 'Failed to delete attendances'
            }
        },

        async saveAttendacesForSession(sessionId, attendances){
            try {
                this.loading = true
                const response = await axios.put(`${API_URL}/attendances/bulk`, {
                    session_id: sessionId,
                    attendances: attendances,
                })
                return response.data

            } catch (error) {
                throw error.response?.data?.error || 'Failed to update attendances'
            } finally {
                this.loading = false
            }
        }
    }
})