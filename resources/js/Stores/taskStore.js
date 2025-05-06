import { defineStore } from "pinia";
import axios from "axios";

export const useTaskStore = defineStore("tasks", {
    state: () => ({
        tasks: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchTasks() {
            this.loading = true;
            try {
                const response = await axios.get("/api/tasks");
                this.tasks = response.data.data;
            } catch (e) {
                this.error = "Failed to fetch tasks";
            } finally {
                this.loading = false;
            }
        },

        async addTask(task) {
            const response = await axios.post("/api/tasks", task);
            this.tasks.unshift(response.data.data);
        },

        async updateTask(id, updatedData) {
            const response = await axios.put(`/api/tasks/${id}`, updatedData);
            const index = this.tasks.findIndex((t) => t.id === id);
            if (index !== -1) this.tasks[index] = response.data.data;
        },

        async deleteTask(id) {
            await axios.delete(`/api/tasks/${id}`);
            this.tasks = this.tasks.filter((t) => t.id !== id);
        },
    },
});
