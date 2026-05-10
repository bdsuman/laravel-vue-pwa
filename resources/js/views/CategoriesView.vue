<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Categories</h2>
      <button @click="showModal = true" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
        Add Category
      </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div v-for="category in categories" :key="category.id" class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="font-bold">{{ category.name }}</h3>
            <p class="text-gray-500 text-sm">{{ category.slug }}</p>
          </div>
          <span :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 rounded-full text-xs">
            {{ category.is_active ? 'Active' : 'Inactive' }}
          </span>
        </div>
        <div class="mt-4 flex space-x-2">
          <button @click="toggleCategory(category)" class="text-sm text-blue-600 hover:text-blue-800">Toggle</button>
          <button @click="deleteCategory(category.id)" class="text-sm text-red-600 hover:text-red-800">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const categories = ref([])
const showModal = ref(false)

const fetchCategories = async () => {
  const { data } = await axios.get('/api/categories')
  categories.value = data.data
}

const toggleCategory = async (category) => {
  await axios.post(`/api/categories/${category.id}/toggle`)
  fetchCategories()
}

const deleteCategory = async (id) => {
  if (confirm('Delete this category?')) {
    await axios.delete(`/api/categories/${id}`)
    fetchCategories()
  }
}

onMounted(fetchCategories)
</script>
