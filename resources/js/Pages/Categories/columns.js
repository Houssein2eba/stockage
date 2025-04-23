import { h } from 'vue'

export const columns = [
  {
    accessorKey: 'name',
    header: 'Name',
    cell: ({ row }) => {
      return h('span', { class: 'font-medium text-gray-900' }, row.getValue('name'))
    }
  },
  {
    accessorKey: 'description',
    header: 'Description',
    cell: ({ row }) => {
      const description = row.getValue('description') || '---'
      return h('div', { class: 'text-gray-600 truncate max-w-xs' }, description)
    }
  },
  {
    accessorKey: 'products_count',
    header: 'Products',
    cell: ({ row }) => {
      return h('span', { 
        class: 'px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium'
      }, row.getValue('products_count'))
    }
  },
  {
    id: 'actions',
    header: 'Actions',
    cell: ({ row }) => {
      const category = row.original
      
      return h('div', { class: 'flex items-center justify-end gap-2 sm:gap-3' }, [
        // Edit button
        h('button', {
          class: 'text-blue-600 hover:text-blue-900 transition-colors flex items-center gap-1 whitespace-nowrap',
          onClick: () => window.openEditModal(category)
        }, [
          h('svg', {
            xmlns: 'http://www.w3.org/2000/svg',
            class: 'h-4 w-4',
            fill: 'none',
            viewBox: '0 0 24 24',
            stroke: 'currentColor'
          }, [
            h('path', {
              'stroke-linecap': 'round',
              'stroke-linejoin': 'round',
              'stroke-width': '2',
              d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
            })
          ]),
          h('span', { class: 'hidden sm:inline' }, 'Edit')
        ]),
        
        // Delete button
        h('button', {
          class: 'text-red-600 hover:text-red-900 transition-colors flex items-center gap-1 whitespace-nowrap',
          onClick: () => window.confirmDelete(category.id)
        }, [
          h('svg', {
            xmlns: 'http://www.w3.org/2000/svg',
            class: 'h-4 w-4',
            fill: 'none',
            viewBox: '0 0 24 24',
            stroke: 'currentColor'
          }, [
            h('path', {
              'stroke-linecap': 'round',
              'stroke-linejoin': 'round',
              'stroke-width': '2',
              d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'
            })
          ]),
          h('span', { class: 'hidden sm:inline' }, 'Delete')
        ])
      ])
    }
  }
]
