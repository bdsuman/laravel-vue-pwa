<template>
  <div>
    <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Posts</h3>
        <p class="text-3xl font-bold">{{ stats.posts }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Categories</h3>
        <p class="text-3xl font-bold">{{ stats.categories }}</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Published Posts</h3>
        <p class="text-3xl font-bold">{{ stats.published }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const stats = ref({ posts: 0, categories: 0, published: 0 })

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/posts')
    stats.value.posts = data.meta?.total || 0
  } catch (error) {
    console.error('Failed to fetch stats', error)
  }
})
</script>
