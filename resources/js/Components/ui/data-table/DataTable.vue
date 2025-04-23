<script setup>
import {
  flexRender,
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ref, computed, watch } from 'vue'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table'

import { Button } from '@/Components/ui/button'

const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    required: true,
  },
  pagination: {
    type: Boolean,
    default: true,
  },
})

const sorting = ref([])

const table = useVueTable({
  get data() {
    return props.data
  },
  get columns() {
    return props.columns
  },
  state: {
    get sorting() {
      return sorting.value
    },
  },
  onSortingChange: (updater) => {
    sorting.value = updater(sorting.value)
  },
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
})

const currentPage = computed(() => table.getState().pagination.pageIndex + 1)
const totalPages = computed(() => table.getPageCount())
</script>

<template>
  <div>
    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead v-for="header in headerGroup.headers" :key="header.id">
              <div
                v-if="header.column.getCanSort()"
                class="flex items-center cursor-pointer select-none"
                @click="header.column.toggleSorting()"
              >
                {{ header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext()) }}
                <span class="ml-2">
                  <template v-if="header.column.getIsSorted() === 'asc'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>
                  </template>
                  <template v-else-if="header.column.getIsSorted() === 'desc'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                  </template>
                  <template v-else>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-down opacity-30"><path d="m21 16-4 4-4-4"/><path d="M17 20V4"/><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/></svg>
                  </template>
                </span>
              </div>
              <template v-else>
                {{ header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext()) }}
              </template>
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <TableRow
              v-for="row in table.getRowModel().rows"
              :key="row.id"
              :data-state="row.getIsSelected() && 'selected'"
            >
              <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                {{ flexRender(cell.column.columnDef.cell, cell.getContext()) }}
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow>
              <TableCell :colSpan="columns.length" class="h-24 text-center">
                No results.
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
    <div v-if="pagination && table.getPageCount() > 1" class="flex items-center justify-end space-x-2 py-4">
      <div class="flex-1 text-sm text-muted-foreground">
        Page {{ currentPage }} of {{ totalPages }}
      </div>
      <div class="space-x-2">
        <Button
          variant="outline"
          size="sm"
          @click="table.previousPage()"
          :disabled="!table.getCanPreviousPage()"
        >
          Previous
        </Button>
        <Button
          variant="outline"
          size="sm"
          @click="table.nextPage()"
          :disabled="!table.getCanNextPage()"
        >
          Next
        </Button>
      </div>
    </div>
  </div>
</template>
