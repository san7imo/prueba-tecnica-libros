<template>
  <div id="app">
    <header>
      <h1>Biblioteca de Libros</h1>
    </header>

    <main>
      <div class="container">
        <div class="actions">
          <button @click="fetchBooks" :disabled="loading">
            {{ loading ? 'Cargando...' : 'Refrescar Lista' }}
          </button>
        </div>

        <div v-if="error" class="error">
          {{ error }}
        </div>

        <div class="books-grid" v-if="books.length > 0">
          <div v-for="book in books" :key="book.title" class="book-card">
            <h3>{{ book.title }}</h3>
            <p class="author">por {{ book.author }}</p>
            <p class="year">{{ book.published_year }}</p>
            <div class="rating">
              <span class="rating-label">Promedio:</span>
              <span class="rating-value">
                {{ book.average_rating ? book.average_rating.toFixed(1) + '/5' : 'Sin calificaciones' }}
              </span>
            </div>
          </div>
        </div>

        <div v-else-if="!loading" class="no-books">
          No se encontraron libros.
        </div>
      </div>
    </main>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import axios from 'axios'

// Definimos el tipo de Book
interface Book {
  title: string
  author: string
  published_year: number
  average_rating: number | null
}

export default defineComponent({
  name: 'App',
  setup() {
    const books = ref<Book[]>([])
    const loading = ref(false)
    const error = ref<string | null>(null)

    const fetchBooks = async () => {
      loading.value = true
      error.value = null
      try {
        const response = await axios.get<Book[]>('http://localhost:8000/api/books')
        books.value = response.data
      } catch (err) {
        console.error('Error fetching books:', err)
        error.value = 'Error al cargar los libros. Asegúrate de que el servidor esté ejecutándose.'
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      fetchBooks()
    })

    return {
      books,
      loading,
      error,
      fetchBooks
    }
  }
})
</script>

<style>
/* tus estilos se mantienen igual */
</style>
