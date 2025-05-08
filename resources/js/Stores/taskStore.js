import { defineStore } from "pinia";
import axios from "axios";

export const useTaskStore = defineStore("tasks", {
    state: () => ({
        tasks: [],
        loading: false,
        error: null,
        pagination: {},
    }),

    actions: {
        async fetchTasks(filters = {}) {
            this.loading = true;
            try {
                const response = await axios.get("/api/tasks", {
                    params: filters,
                });

                this.tasks = response.data.data;

                this.pagination = {
                    ...response.data.meta,
                    links: response.data.meta.links,
                };
            } catch (e) {
                console.error("Failed to fetch tasks:", e);
                this.error = "Failed to fetch tasks.";
            } finally {
                this.loading = false;
            }
        },

        async addTask(data) {
            try {
                const response = await axios.post("/api/tasks", data);
                this.tasks.unshift(response.data.data);
            } catch (error) {
                this.error = "Failed to create task.";
            }
        },

        async updateTask(id, data) {
            try {
                const response = await axios.put(`/api/tasks/${id}`, data);
                const index = this.tasks.findIndex((task) => task.id === id);
                if (index !== -1) {
                    this.tasks[index] = response.data.data;
                }
            } catch (error) {
                this.error = "Failed to update task.";
            }
        },

        async toggleDone(id) {
            try {
                const response = await axios.patch(
                    `/api/tasks/${id}/toggle-done`
                );
                const index = this.tasks.findIndex((t) => t.id === id);
                if (index !== -1) {
                    this.tasks[index] = response.data.data;
                }
            } catch (e) {
                this.error = "Failed to toggle task status.";
            }
        },

        async deleteTask(id) {
            try {
                await axios.delete(`/api/tasks/${id}`);
                this.tasks = this.tasks.filter((task) => task.id !== id);
            } catch (error) {
                this.error = "Failed to delete task.";
            }
        },
        async generateDummyTasks() {
            this.loading = true;
            try {
                await axios.post("/api/tasks/generate");
            } catch (e) {
                this.error = "Failed to generate dummy tasks.";
            } finally {
                this.loading = false;
            }
        },
    },
});
