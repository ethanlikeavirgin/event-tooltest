<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center py-6 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-extrabold text-gray-900 text-center">Create Item</h1>
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label for="parent" class="block text-sm font-medium text-gray-700">Parent</label>
            <select v-model="form.item_id" id="parent" class="mt-1 mb-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
              <option :key="item.id" :value="item.id" v-for="item in items">{{ item.name }}</option>
            </select>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input 
              v-model="form.name" 
              id="name" 
              type="text" 
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
          </div>

          <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input 
              v-model="form.price" 
              id="price" 
              type="number" 
              step="0.01" 
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div v-if="form.errors.price" class="text-red-500 text-sm mt-1">{{ form.errors.price }}</div>
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <input 
              v-model="form.description" 
              id="description" 
              type="text" 
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
          </div>

          <div>
            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
            <input 
              v-model="form.date" 
              id="date" 
              type="date" 
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div v-if="form.errors.date" class="text-red-500 text-sm mt-1">{{ form.errors.date }}</div>
          </div>

          <div>
            <label for="max" class="block text-sm font-medium text-gray-700">Max</label>
            <input 
              v-model="form.max" 
              id="max" 
              type="number" 
              step="1" 
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div v-if="form.errors.max" class="text-red-500 text-sm mt-1">{{ form.errors.max }}</div>
          </div>

          <div>
            <label for="file" class="block text-sm font-medium text-gray-700">File</label>
            <input 
              type="file" 
              @change="handleFileUpload" 
              class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            />
            <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</div>
          </div>

          <div>
            <button 
              type="submit" 
              :disabled="form.processing" 
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              Create
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
  },
  props: {
    items: Array,
  },
  setup() {
    const form = useForm({
      item_id: '',
      name: '',
      price: '',
      description: '',
      date: '',
      max: '',
      file: null,
    });

    const handleFileUpload = (event) => {
      form.file = event.target.files[0];
    };

    const submit = () => {
      form.post(route('items.store'), {
        forceFormData: true,
      });
    };

    return { form, handleFileUpload, submit };
  },
};
</script>