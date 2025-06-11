<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Pagination from "@/Components/Pagination.vue";
const page = usePage();
const props = defineProps({
    myTasks: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");

watch(search, (value) => {
    router.get(
        route("dashboard"),
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

function startTask(taskId) {
    router.patch(route("tasks.start", taskId));
}

function checkTask(taskId) {
    router.patch(route("tasks.check", taskId));
}
const filters = page.props.filters;
function toggleSort(column) {
    const currentSort = filters.sort;
    const currentDirection = filters.direction;
    const newDirection =
        currentSort === column && currentDirection === "asc" ? "desc" : "asc";

    router.get(
        route("dashboard"),
        {
            sort: column,
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

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="p-12">
            <input
                v-model="search"
                type="text"
                placeholder="search..."
                class="mb-4 p-2 border rounded w-full sm:w-1/3 lg:w-1/4"
            />
            <table class="min-w-full bg-white border border-gray-200 mt-8">
                <thead>
                    <tr>
                        <th
                            @click="toggleSort('name')"
                            class="cursor-pointer px-4 py-2 border-b"
                        >
                            Name
                            <span>
                                <span v-if="filters.sort === 'name'">
                                    {{
                                        filters.direction === "asc" ? "↑" : "↓"
                                    }}
                                </span></span
                            >
                        </th>

                        <th
                            @click="toggleSort('description')"
                            class="cursor-pointer px-4 py-2 border-b"
                        >
                            Description
                            <span>
                                <span v-if="filters.sort === 'description'">
                                    {{
                                        filters.direction === "asc" ? "↑" : "↓"
                                    }}
                                </span></span
                            >
                        </th>
                        <th class="px-4 py-2 border-b">Created By</th>
                        <th
                            @click="toggleSort('status')"
                            class="cursor-pointer px-4 py-2 border-b"
                        >
                            Status
                            <span>
                                <span v-if="filters.sort === 'status'">
                                    {{
                                        filters.direction === "asc" ? "↑" : "↓"
                                    }}
                                </span></span
                            >
                        </th>
                        <th class="px-4 py-2 border-b">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="myTasks.length === 0">
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            No tasks available.
                        </td>
                    </tr>
                    <tr
                        v-for="task in myTasks.data"
                        :key="task.id"
                        class="text-center"
                    >
                        <td class="px-4 py-2 border-b">{{ task.name }}</td>
                        <td class="px-4 py-2 border-b">
                            {{ task.description }}
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
                                v-if="task.status === 'pending'"
                                @click="startTask(task.id)"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                            >
                                Start
                            </button>
                            <button
                                v-else-if="task.status === 'inprogress'"
                                @click="checkTask(task.id)"
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                            >
                                Check
                            </button>
                            <span
                                v-else-if="task.status === 'checking'"
                                class="text-gray-600"
                                >checking</span
                            >
                            <span v-else="task.status === 'completed'"></span>
                        </td>
                    </tr>
                </tbody>
                <div><Pagination :links="myTasks.links" class="mt-4" /></div>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
