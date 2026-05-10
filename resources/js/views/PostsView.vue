<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Posts</h2>
      <button @click="showModal = true" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
        Add Post
      </button>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="post in posts" :key="post.id">
            <td class="px-6 py-4">{{ post.title }}</td>
            <td class="px-6 py-4">{{ post.category?.name }}</td>
            <td class="px-6 py-4">
              <span :class="post.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 rounded-full text-xs">
                {{ post.is_published ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td class="px-6 py-4 space-x-2">
              <button @click="editPost(post)" class="text-blue-600 hover:text-blue-800">Edit</button>
              <button @click="deletePost(post.id)" class="text-red-600 hover:text-red-800">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const posts = ref([])
const showModal = ref(false)

const fetchPosts = async () => {
  const { data } = await axios.get('/api/posts')
  posts.value = data.data
}

const deletePost = async (id) => {
  if (confirm('Delete this post?')) {
    await axios.delete(`/api/posts/${id}`)
    fetchPosts()
  }
}

const editPost = (post) => {
  // Handle edit logic
}

onMounted(fetchPosts)
</script>
