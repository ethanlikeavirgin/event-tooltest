<template>
  <AppLayout title="Index">
    <div class="min-h-screen bg-[#0d0d19] text-white flex flex-col items-center py-12 px-4 font-sans">
      <h1 class="text-4xl font-bold text-white mb-6 flex items-center gap-2">
        🎟 <span>Your Tickets</span>
      </h1>

      <div class="mb-6">
        <Link href="/items/create" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-xl shadow-lg hover:opacity-90 transition font-medium">+ Create Ticket</Link>
      </div>

      <div class="bg-[#141427] w-full max-w-3xl rounded-3xl shadow-lg overflow-hidden">
        <table class="w-full text-left">
          <thead>
            <tr class="text-gray-400 text-sm uppercase tracking-wide bg-[#1b1b2f]">
              <th class="px-6 py-4">Event</th>
              <th class="px-6 py-4">File</th>
              <th class="px-6 py-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id" class="border-t border-[#2a2a3d] hover:bg-[#1a1a2e] transition">
              <td class="px-6 py-4 font-semibold text-white">{{ item.name }}</td>
              <td class="px-6 py-4">
                <a v-if="item.file_path" :href="`/storage/${item.file_path}`" target="_blank" class="text-blue-400 hover:underline">
                  View File
                </a>
              </td>
              <td class="px-6 py-4 flex gap-4 items-center">
                <Link :href="`/items/${item.id}/source`" class="text-white hover:underline flex items-center gap-1">
                  📈 Sources
                </Link>
                <Link :href="`/items/${item.id}/edit`" class="text-green-400 hover:underline flex items-center gap-1">
                  ✏️ Edit
                </Link>
                <button @click="destroy(item.id)" class="text-red-400 hover:underline flex items-center gap-1">
                  🗑️ Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  props: {
    items: Array,
  },
  setup() {
    const destroy = (id) => {
      router.delete(route('items.destroy', id), {
        onBefore: () => confirm('Are you sure you want to delete this item?'),
        onSuccess: () => {
          console.log(`Item with ID ${id} deleted successfully`);
        },
        onError: (errors) => {
          console.error('Error deleting item:', errors);
        },
      });
    };

    return { destroy };
  },
  components: {
    Link, AppLayout,
  },
};
</script>