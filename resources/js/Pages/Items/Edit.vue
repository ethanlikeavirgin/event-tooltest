<template>
  <AppLayout title="Edit">
    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center py-6 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8 bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-extrabold text-gray-900 text-center">Edit Item</h1>
        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label for="parent" class="block text-sm font-medium text-gray-700">Parent</label>
            <select v-model="form.parent" id="parent" class="mt-1 mb-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
            <div v-if="form.file_path">
              <img :src="form.file_path.startsWith('blob') ? form.file_path : `/storage/${form.file_path}`" class="w-12 h-12 rounded-md mt-2">
            </div>
            <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</div>
          </div>

          <div>
            <button 
              type="submit" 
              :disabled="form.processing" 
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
  },
  props: {
    items: Array,
    item: Object,
  },
  setup(props) {
    const form =  reactive({
      parent: props.item.item_id,
      name: props.item.name,
      price: props.item.price,
      description: props.item.description,
      date: props.item.date,
      max: props.item.max,
      file: null,
      file_path: props.item.file_path,
      errors: {},
      processing: false,
    });

    const handleFileUpload = (event) => {
      form.file = event.target.files[0];
    };

    const submit = () => {
      form.processing = true;

      const formData = new FormData();
      formData.append('item_id', form.parent);
      formData.append('name', form.name);
      formData.append('price', form.price);
      formData.append('description', form.description);
      formData.append('date', form.date);
      formData.append('max', form.max);
      if (form.file) {
        formData.append('file', form.file);
      }
      formData.append('_method', 'put');

      router.post(route('items.update', props.item.id), formData, {
        onFinish: () => {
          console.log(form.parent);
          form.processing = false;
        },
        onError: (errors) => {
          form.errors = errors;
        },
      });
    };

    return { form, handleFileUpload, submit };
  },
};
</script>