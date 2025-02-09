<script setup lang="ts">
import { ref } from 'vue';
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    getFilteredRowModel,
} from '@tanstack/vue-table';

import { ArrowBigRight, ArrowBigLeft, ArrowBigRightDash, ArrowBigLeftDash } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    tableName: {
        type: String,
        required: true,
    },
});

const data = ref(props.data);
import type { ColumnDef, SortingState } from '@tanstack/vue-table';

const sorting = ref<SortingState>([]);
const filter = ref('');

const { t } = useI18n();

const table = useVueTable({
    data: data,
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    columns: props.columns as ColumnDef<unknown, any>[],
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    state: {
        get sorting() {
            return sorting.value;
        },
        get globalFilter() {
            return filter.value;
        },
    },
    onSortingChange: (updaterOrValue) => {
        sorting.value = typeof updaterOrValue === 'function' ? updaterOrValue(sorting.value) : updaterOrValue;
    },
    initialState: {
        pagination: {
            pageSize: 10,
        },
    },
});
</script>

<template>
    <div class="space-y-4">
        <!-- Table Card -->
        <div class="bg-gray-900/50 border border-gray-800 rounded-lg p-4">
            <div class="relative overflow-x-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-200">{{ tableName }}</h2>
                    <div class="relative w-1/3">
                        <input
                            type="text"
                            class="w-full bg-gray-800/50 border border-gray-700/50 rounded-lg pl-4 pr-10 py-2 text-sm text-gray-100 placeholder-gray-500 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 focus:outline-none"
                            :placeholder="t('components.table.search')"
                            v-model="filter"
                        />
                        <button class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M14.293 15.707a1 1 0 01-1.414 0l-3.586-3.586a6 6 0 111.414-1.414l3.586 3.586a1 1 0 010 1.414z"
                                    clip-rule="evenodd"
                                />
                                <path fill-rule="evenodd" d="M9 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <br />
                <table class="w-full text-left">
                    <thead>
                        <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <th
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                scope="col"
                                class="px-4 py-3 text-sm font-medium text-gray-400 border-b border-gray-800"
                                :class="[header.column.getCanSort() ? 'cursor-pointer hover:text-gray-300' : '']"
                                @click="header.column.getToggleSortingHandler()?.($event)"
                            >
                                <div class="flex items-center gap-2">
                                    <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                                    <span v-if="header.column.getIsSorted()" class="text-purple-400">
                                        {{
                                            header.column.getIsSorted()
                                                ? { asc: '↑', desc: '↓' }[header.column.getIsSorted() as 'asc' | 'desc']
                                                : ''
                                        }}
                                    </span>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-800">
                        <tr v-for="row in table.getRowModel().rows" :key="row.id" class="hover:bg-gray-800/50">
                            <td
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                class="px-4 py-3 text-sm text-gray-300"
                            >
                                <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            <div class="flex justify-between items-center text-sm text-gray-400 mt-4">
                <div>
                    {{ t('components.table.page') }} {{ table.getState().pagination.pageIndex + 1 }}
                    {{ t('components.table.of') }} {{ table.getPageCount() }} -
                    {{ table.getFilteredRowModel().rows.length }} {{ t('components.table.results') }}
                </div>
                <div class="flex items-center gap-2">
                    <label for="pageSize" class="mr-2">{{ t('components.table.pageSize') }}:</label>
                    <div class="relative">
                        <select
                            id="pageSize"
                            v-model="table.getState().pagination.pageSize"
                            class="appearance-none px-4 py-2 text-sm font-medium bg-gray-800/50 text-gray-400 rounded-lg transition-colors focus:outline-none focus:ring-1 focus:ring-purple-500/50"
                        >
                            <option
                                v-for="size in [5, 10, 15, 20, 25, 50, 100]"
                                :key="size"
                                :value="size"
                                class="bg-gray-900 text-gray-300"
                            >
                                {{ size }}
                            </option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Controls -->
            <div class="flex justify-center gap-2 mt-4">
                <button
                    class="flex items-center px-4 py-2 text-sm font-medium bg-gray-800/50 text-gray-400 rounded-lg hover:bg-gray-700/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    @click="table.setPageIndex(0)"
                    :disabled="!table.getCanPreviousPage()"
                >
                    <ArrowBigLeftDash /> {{ t('components.table.first') }}
                </button>
                <button
                    class="flex items-center px-4 py-2 text-sm font-medium bg-gray-800/50 text-gray-400 rounded-lg hover:bg-gray-700/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    :disabled="!table.getCanPreviousPage()"
                    @click="table.previousPage()"
                >
                    <ArrowBigLeft /> {{ t('components.table.previous') }}
                </button>
                <button
                    class="flex items-center px-4 py-2 text-sm font-medium bg-gray-800/50 text-gray-400 rounded-lg hover:bg-gray-700/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    :disabled="!table.getCanNextPage()"
                    @click="table.nextPage()"
                >
                    {{ t('components.table.next') }}
                    <ArrowBigRight />
                </button>
                <button
                    class="flex items-center px-4 py-2 text-sm font-medium bg-gray-800/50 text-gray-400 rounded-lg hover:bg-gray-700/50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    @click="table.setPageIndex(table.getPageCount() - 1)"
                    :disabled="!table.getCanNextPage()"
                >
                    {{ t('components.table.last') }}
                    <ArrowBigRightDash />
                </button>
            </div>
        </div>
    </div>
</template>
