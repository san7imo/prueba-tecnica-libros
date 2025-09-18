//src/components/BookList.vue
<template>
  <div>
    <div class="list-controls">
      <input v-model="q" placeholder="Buscar por título o autor" @input="filter" />
      <select v-model="sort" @change="filter">
        <option value="title">Ordenar: Título</option>
        <option value="published_year">Ordenar: Año</option>
        <option value="rating">Ordenar: Promedio</option>
      </select>
      <button @click="fetch" :disabled="loading">{{ loading ? 'Cargando...' : 'Refrescar' }}</button>
    </div>

    <div v-if="error" class="error">{{ error }}</div>

    <div class="books-grid" v-if="filtered.length">
      <div v-for="book in filtered" :key="book.id" class="book-card card">
        <div class="card-body">
          <h3 class="book-title">{{ book.title }}</h3>
          <p class="author">por {{ book.author }}</p>
          <p class="meta">{{ book.published_year }} •
            <span class="rating-badge">
              {{ book.average_rating !== null && book.average_rating !== undefined ? (book.average_rating.toFixed(1) + '/5') : 'Sin calif.' }}
            </span>
          </p>
        </div>

        <div class="card-actions">
          <button @click="$emit('view-reviews', book)">Reseñas</button>
          <button @click="onEdit(book)">Editar</button>
          <button class="danger" @click="onDelete(book)" :disabled="deletingId === book.id">
            {{ deletingId === book.id ? 'Eliminando...' : 'Eliminar' }}
          </button>
        </div>
      </div>
    </div>

    <div v-else-if="!loading" class="no-books">No se encontraron libros.</div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { getBooks, deleteBook } from '../services/api';
import type { Book } from '../types';

export default defineComponent({
  name: 'BookList',
  emits: ['view-reviews', 'edited', 'saved'],
  setup(_, { emit }) {
    const books = ref<Book[]>([]);
    const filtered = ref<Book[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);
    const q = ref('');
    const sort = ref<'title' | 'published_year' | 'rating'>('title');
    const deletingId = ref<number | null>(null);

    const fetch = async () => {
      loading.value = true;
      error.value = null;
      try {
        const res = await getBooks();
        console.log('Libros recibidos:', res.data);
        books.value = res.data;
        applyFilterSort();
      } catch (err: unknown) {
        console.error(err);
        error.value = 'Error cargando libros';
      } finally {
        loading.value = false;
      }
    };

    const applyFilterSort = () => {
      let list = [...books.value];
      if (q.value) {
        const term = q.value.toLowerCase();
        list = list.filter(b => b.title.toLowerCase().includes(term) || b.author.toLowerCase().includes(term));
      }
      if (sort.value === 'title') list.sort((a, b) => a.title.localeCompare(b.title));
      if (sort.value === 'published_year') list.sort((a, b) => (b.published_year || 0) - (a.published_year || 0));
      if (sort.value === 'rating') list.sort((a, b) => (b.average_rating || 0) - (a.average_rating || 0));
      filtered.value = list;
    };

    const filter = () => applyFilterSort();

    const onDelete = async (book: Book) => {
      if (!confirm(`Eliminar "${book.title}"?`)) return;
      deletingId.value = book.id ?? null;
      try {
        await deleteBook(book.id as number);
        await fetch();
        emit('saved');
      } catch (err) {
        console.error(err);
        error.value = 'Error eliminando libro';
      } finally {
        deletingId.value = null;
      }
    };

    const onEdit = (book: Book) => {
      emit('edited', book); // ✅ Emite el evento `edited` con el objeto del libro
    };

    // inicial
    fetch();

    return { books, filtered, loading, error, q, sort, fetch, filter, onDelete, onEdit, deletingId };
  },
});
</script>

<style scoped>
.list-controls { display:flex; gap:0.5rem; align-items:center; margin-bottom:1rem; }
.list-controls input { padding:0.5rem; border-radius:6px; border:1px solid #ccc; flex:1; }
.list-controls select, .list-controls button { padding:0.5rem; border-radius:6px; }
.book-card { display:flex; justify-content:space-between; align-items:center; padding:1rem; gap:1rem; }
.card-body { flex:1; }
.book-title { margin:0; }
.card-actions { display:flex; gap:0.5rem; }
button.danger { background:var(--danger); color:white; border:none; padding:0.4rem 0.6rem; border-radius:6px; }
.no-books { color:var(--muted); }
.rating-badge { background: rgba(255,255,255,0.06); padding: 0.15rem 0.4rem; border-radius:4px; }
</style>