<script setup>
defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, required: true },
  emptyMessage: { type: String, default: 'Tiada rekod ditemui' },
})
</script>

<template>
  <div class="overflow-x-auto rounded-xl border border-slate-100">
    <table class="table-auto w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            :class="['px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide', col.class || '']"
          >
            {{ col.label }}
          </th>
          <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Tindakan
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="rows.length === 0">
          <td :colspan="columns.length + 1" class="px-4 py-10 text-center text-slate-400">
            {{ emptyMessage }}
          </td>
        </tr>
        <tr
          v-for="(row, index) in rows"
          :key="row.id ?? index"
          class="border-b border-slate-50 hover:bg-slate-50 transition-colors"
          :class="{ 'bg-slate-50/50': index % 2 === 1 }"
        >
          <td
            v-for="col in columns"
            :key="col.key"
            :class="['px-4 py-3 text-slate-700 align-middle', col.class || '']"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
              {{ row[col.key] }}
            </slot>
          </td>
          <td class="px-4 py-3 text-right align-middle">
            <slot name="row-actions" :row="row" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
