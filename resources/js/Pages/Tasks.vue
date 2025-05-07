<template>
    <AppLayout>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <!-- Filters -->
                <div class="border-top pt-3">
                    <h5>Filters</h5>

                    <div class="row">
                        <div class="col-xs-6 col-md-12 mb-3">
                            <label class="form-label">Status</label>
                            <select
                                class="form-select"
                                v-model="filters.status"
                                @change="applyFilters"
                            >
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="done">Done</option>
                            </select>
                        </div>

                        <div class="col-xs-6 col-md-12 mb-3">
                            <label class="form-label">Search</label>
                            <input
                                type="text"
                                v-model="filters.search"
                                class="form-control"
                                @input="debouncedSearch"
                            />
                        </div>

                        <div class="col-xs-6 col-md-12 mb-3">
                            <label class="form-label">Sort by</label>
                            <select
                                class="form-select"
                                v-model="filters.sort"
                                @change="applyFilters"
                            >
                                <option value="created_desc">Date ↓</option>
                                <option value="created_asc">Date ↑</option>
                                <option value="title_asc">Title A-Z</option>
                                <option value="title_desc">Title Z-A</option>
                            </select>
                        </div>

                        <div class="col-xs-6 col-md-12 mb-3">
                            <label class="form-label">Items per page</label>
                            <select
                                class="form-select"
                                v-model="filters.per_page"
                                @change="applyFilters"
                            >
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                            </select>
                        </div>
                    </div>
                </div>

                <br />
                <hr />

                <form @submit.prevent="handleSubmit" class="mb-4">
                    <h5>{{ isEditing ? "Edit Task" : "Add New Task" }}</h5>

                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title</label>
                        <input
                            type="text"
                            id="title"
                            v-model="form.title"
                            class="form-control"
                            required
                        />
                    </div>

                    <div class="mb-3">
                        <label for="desc" class="form-label">Description</label>
                        <textarea
                            id="desc"
                            v-model="form.description"
                            class="form-control"
                            rows="3"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        :disabled="loading"
                    >
                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm me-2"
                        ></span>
                        {{ isEditing ? "Update Task" : "Add Task" }}
                    </button>
                </form>
            </div>

            <!-- Main content -->
            <div class="col-md-9">
                <h3 class="d-flex align-items-center gap-2">
                    Task List
                    <div
                        v-if="store.loading"
                        class="spinner-border spinner-border-sm text-secondary"
                        role="status"
                    >
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </h3>

                <ul class="list-group mb-3">
                    <li
                        v-if="!store.loading && store.tasks.length === 0"
                        class="list-group-item text-center text-muted"
                    >
                        No tasks found.
                    </li>
                    <li
                        v-for="task in store.tasks"
                        :key="task.id"
                        class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row"
                    >
                        <div class="flex-grow-1">
                            <strong
                                :class="{
                                    'text-decoration-line-through':
                                        task.is_done,
                                }"
                            >
                                {{ task.title }}
                            </strong>
                            <small
                                class="ms-2"
                                :class="
                                    task.is_done
                                        ? 'text-success'
                                        : 'text-warning'
                                "
                            >
                                - {{ task.is_done ? "Done" : "Pending" }}
                            </small>
                            <p class="mb-1 small text-muted">
                                {{ task.description }}
                            </p>
                        </div>
                        <div
                            class="mt-2 mt-md-0 d-flex flex-column align-items-end gap-1"
                        >
                            <div
                                class="col-12 col-md-4 d-flex flex-wrap flex-md-nowrap justify-content-end align-items-start gap-1 mt-2 mt-md-0"
                            >
                                <!-- Done / Undo -->
                                <button
                                    class="btn btn-sm btn-outline-success"
                                    @click="toggleDone(task)"
                                    :disabled="loading.value"
                                    :title="
                                        task.is_done
                                            ? 'Mark as pending'
                                            : 'Mark as done'
                                    "
                                >
                                    <span
                                        v-if="
                                            processingId === task.id &&
                                            loading.value
                                        "
                                        class="spinner-border spinner-border-sm me-1"
                                    ></span>
                                    {{ task.is_done ? "Undo" : "Done" }}
                                </button>

                                <!-- Edit -->
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="startEdit(task)"
                                    :disabled="loading.value"
                                    title="Edit task"
                                >
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                <!-- Delete -->
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    @click="openDeleteModal(task.id)"
                                    :disabled="loading.value"
                                    title="Delete task"
                                >
                                    <span
                                        v-if="
                                            processingId === task.id &&
                                            loading.value
                                        "
                                        class="spinner-border spinner-border-sm me-1"
                                    ></span>
                                    Delete
                                </button>
                            </div>
                            <small class="text-secondary"
                                >Created:
                                {{ formatDate(task.created_at) }}</small
                            >
                        </div>
                    </li>
                </ul>
                <!-- Pagination -->
                <nav
                    v-if="
                        store.pagination.links &&
                        store.pagination.links.length > 1
                    "
                    aria-label="Task pagination"
                >
                    <ul class="pagination justify-content-center">
                        <li
                            v-for="(link, index) in store.pagination.links"
                            :key="index"
                            :class="[
                                'page-item',
                                { active: link.active, disabled: !link.url },
                            ]"
                        >
                            <a
                                v-if="link.url"
                                class="page-link"
                                href="#"
                                v-html="formatLabel(link.label)"
                                @click.prevent="goToPage(link.url)"
                            ></a>

                            <span
                                v-else
                                class="page-link"
                                v-html="formatLabel(link.label)"
                            ></span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <ConfirmModal
            ref="confirmModal"
            title="Confirm Delete"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useTaskStore } from "@/Stores/taskStore";
import { reactive, ref, onMounted } from "vue";
import toastr from "toastr";
import debounce from "lodash.debounce";
import ConfirmModal from "@/Components/ConfirmModal.vue";
const confirmModal = ref(null);
const taskToDelete = ref(null);

const store = useTaskStore();
const loading = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const processingId = ref(null);

const form = reactive({
    title: "",
    description: "",
});

const filters = reactive({
    status: "",
    search: "",
    sort: "created_desc",
    page: 1,
    per_page: 10,
});

const applyFilters = () => {
    const payload = {
        ...filters,
        is_done:
            filters.status === "pending"
                ? false
                : filters.status === "done"
                ? true
                : undefined,
    };

    delete payload.status; // não envia o campo original

    store.fetchTasks(payload);
};

const debouncedSearch = debounce(() => {
    applyFilters();
}, 500);

const handleSubmit = async () => {
    loading.value = true;
    try {
        if (isEditing.value && editingId.value) {
            await store.updateTask(editingId.value, { ...form });
            toastr.success("Task updated");
        } else {
            await store.addTask({ ...form });
            toastr.success("Task created");
        }

        resetForm();
        applyFilters();
    } catch (e) {
        toastr.error("Error saving task");
    } finally {
        loading.value = false;
    }
};

const startEdit = (task) => {
    form.title = task.title;
    form.description = task.description;
    editingId.value = task.id;
    isEditing.value = true;
};

const resetForm = () => {
    form.title = "";
    form.description = "";
    isEditing.value = false;
    editingId.value = null;
};

const openDeleteModal = (taskId) => {
    taskToDelete.value = taskId;
    confirmModal.value.open();
};

const confirmDelete = async () => {
    processingId.value = taskToDelete.value;
    loading.value = true;
    try {
        await store.deleteTask(taskToDelete.value);
        toastr.info("Task deleted");
        applyFilters();
    } catch (e) {
        toastr.error("Failed to delete");
    } finally {
        loading.value = false;
        processingId.value = null;
        taskToDelete.value = null;
    }
};

const toggleDone = async (task) => {
    loading.value = true;
    processingId.value = task.id;
    try {
        await store.toggleDone(task.id);
        toastr[task.is_done ? "warning" : "success"](
            `Task marked as ${task.is_done ? "pending" : "done"}`
        );
    } catch (e) {
        toastr.error("Error updating task status");
    } finally {
        loading.value = false;
        processingId.value = null;
    }
};

const goToPage = (url) => {
    const urlObj = new URL(url);
    filters.page = urlObj.searchParams.get("page");
    applyFilters(); // Isso faz o fetchTasks rodar com a nova página
};

const formatLabel = (label) => {
    if (label === "&laquo; Previous") return "«";
    if (label === "Next &raquo;") return "»";
    return label;
};
onMounted(() => {
    store.fetchTasks({ ...filters });
});

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString(undefined, {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>
