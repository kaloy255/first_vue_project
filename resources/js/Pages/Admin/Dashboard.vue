<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
import Pagination from "@/Components/Pagination.vue";
import { usePage } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
const page = usePage();

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },

    usersTasks: Object,
});

//create modal
const showModal = ref(false);
const openModal = () => (showModal.value = true);
const closeModal = () => {
    showModal.value = false;
    form.reset();
    selectedUser.value = null;
};

//get the current page
const currentPage = ref(page.url.split("page=")[1] || 1);
//form
const form = useForm({
    name: "",
    description: "",
    selectedUser: null,
    page: currentPage.value,
});

//assign to
const selectedUser = ref(null);
watch(selectedUser, (newUser) => {
    form.selectedUser = newUser?.id || null;
});

const onSearchUser = (searchTerm) => {
    console.log("Searching for:", searchTerm);
};

let submit = () => {
    form.page = currentPage.value; // update before submit
    form.post(route("admin.store"), {
        onSuccess: () => {
            form.reset();
            selectedUser.value = null;
            closeModal();
        },
    });
};

//done button
function doneTask(taskId) {
    router.patch(route("tasks.done", taskId));
}
//failed button
function failedTask(taskId) {
    router.patch(route("tasks.failed", taskId));
}

//asc/desc toggle fieds(name,desription,assignto)
const filters = page.props.filters;
console.log(filters);

function toggleSort(field) {
    const currentSort = filters.sort;
    const currentDirection = filters.direction;
    console.log(currentSort);
    console.log(currentDirection);
    const newDirection =
        currentSort === field && currentDirection === "asc" ? "desc" : "asc";
    console.log(newDirection);
    router.get(
        route("admin.dashboard"),
        {
            sort: field,
            direction: newDirection,
        },
        {
            preserveScroll: true,
            replace: true,
        }
    );
}
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div>
                    <button
                        @click="openModal"
                        class="px-4 py-2 bg-indigo-600 text-white rounded"
                    >
                        Create Tasks
                    </button>

                    <Modal :show="showModal" @close="closeModal" max-width="lg">
                        <template #default>
                            <div class="p-6">
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <h2 class="text-lg font-semibold">
                                        Create Task
                                    </h2>
                                    <button
                                        @click="closeModal"
                                        class="text-gray-400 hover:text-gray-700"
                                        aria-label="Close"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <form @submit.prevent="submit">
                                    <!-- name task field -->
                                    <div class="mb-4">
                                        <label
                                            class="block text-gray-700 mb-2"
                                            for="name"
                                            >Name</label
                                        >
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="w-full px-3 py-2 border rounded"
                                            placeholder="Enter your name"
                                        />
                                        <span v-text="form.errors.name"></span>
                                    </div>
                                    <!-- description field -->
                                    <div class="mb-4">
                                        <label
                                            class="block text-gray-700 mb-2"
                                            for="description"
                                            >Description</label
                                        >
                                        <input
                                            id="description"
                                            v-model="form.description"
                                            type="text"
                                            class="w-full px-3 py-2 border rounded"
                                            placeholder="Enter your description"
                                        />
                                    </div>

                                    <!-- assign to field -->
                                    <div class="mb-4">
                                        <label for="assigned_to"
                                            >Assign To</label
                                        >
                                        <multiselect
                                            v-model="selectedUser"
                                            :options="users"
                                            label="name"
                                            track-by="id"
                                            placeholder="Search user"
                                            @search-change="onSearchUser"
                                        >
                                            <template #noResult>
                                                <div>No user found</div>
                                            </template></multiselect
                                        >
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white rounded"
                                        >
                                            Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </template>
                    </Modal>
                </div>
            </div>

            <table class="min-w-full bg-white border border-gray-200 mt-8">
                <thead>
                    <tr>
                        <th
                            class="px-4 py-2 border-b cursor-pointer"
                            @click="toggleSort('name')"
                        >
                            Name
                            <span v-if="filters.sort === 'name'">
                                {{ filters.direction === "asc" ? "↑" : "↓" }}
                            </span>
                        </th>
                        <th
                            class="px-4 py-2 border-b cursor-pointer"
                            @click="toggleSort('description')"
                        >
                            Description
                            <span v-if="filters.sort === 'description'">
                                {{ filters.direction === "asc" ? "↑" : "↓" }}
                            </span>
                        </th>
                        <th
                            class="px-4 py-2 border-b cursor-pointer"
                            @click="toggleSort('assign_to')"
                        >
                            Assign To
                            <span v-if="filters.sort === 'assign_to'">
                                {{ filters.direction === "asc" ? "↑" : "↓" }}
                            </span>
                        </th>

                        <th class="px-4 py-2 border-b">Created By</th>

                        <th
                            class="px-4 py-2 border-b cursor-pointer"
                            @click="toggleSort('status')"
                        >
                            Status
                            <span v-if="filters.sort === 'status'">
                                {{ filters.direction === "asc" ? "↑" : "↓" }}
                            </span>
                        </th>
                        <th class="px-4 py-2 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="task in usersTasks.data"
                        :key="task.id"
                        class="text-center"
                    >
                        <td class="px-4 py-2 border-b">{{ task.name }}</td>
                        <td class="px-4 py-2 border-b">
                            {{ task.description }}
                        </td>
                        <td class="px-4 py-2 border-b">
                            {{ task.assign_to?.name || "N/A" }}<br />
                            <span class="text-gray-500 text-sm">{{
                                task.assign_to?.email || ""
                            }}</span>
                        </td>
                        <td class="px-4 py-2 border-b">
                            {{ task.creator?.name || "N/A" }}
                        </td>
                        <td class="px-4 py-2 border-b">
                            <span
                                :class="{
                                    ' bg-green-600 p-2 rounded-full text-sm text-white':
                                        task.status === 'completed',
                                    'bg-yellow-600 p-2 rounded-full text-sm text-white':
                                        task.status === 'pending',
                                    'bg-blue-600 p-2 rounded-full text-sm text-white':
                                        task.status === 'inprogress',
                                    'bg-gray-600 p-2 rounded-full text-sm text-white':
                                        task.status === 'checking',
                                    'bg-red-600 p-2 rounded-full text-sm text-white':
                                        task.status === 'failed',
                                }"
                            >
                                {{
                                    task.status === "checking"
                                        ? "Checking..."
                                        : task.status.charAt(0).toUpperCase() +
                                          task.status.slice(1)
                                }}
                            </span>
                        </td>

                        <td class="px-4 py-2 border-b">
                            <button
                                v-if="task.status === 'checking'"
                                @click="doneTask(task.id)"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                            >
                                Done
                            </button>

                            <span
                                v-else-if="task.status === 'completed'"
                            ></span>

                            <button
                                v-else-if="task.status !== 'failed'"
                                @click="failedTask(task.id)"
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                            >
                                Failed
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!usersTasks.data || usersTasks.data.length === 0">
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No tasks found.
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination
                :links="usersTasks.links"
                class="mt-4"
                preserve-scroll
            />
        </div>
    </AdminLayout>
</template>
