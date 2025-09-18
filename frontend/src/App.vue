<template>
  <div id="app">
    <header class="app-header">
      <div class="header-container">
        <h1>📚 Biblioteca de Libros</h1>
        <p class="header-subtitle">Gestiona tu colección personal</p>
      </div>
    </header>

    <main class="container">
      <div class="main-actions">
        <button class="create-btn" @click="openForm">
          <span class="btn-icon">📖</span>
          Crear libro
        </button>
      </div>

      <div class="layout">
        <section class="main-content">
          <BookList
            ref="bookListRef"
            @view-reviews="openReviews"
            @edited="openFormWithBook"
            @saved="refreshList"
          />
        </section>
      </div>

      <!-- Modal Crear/Editar -->
      <div v-if="showFormModal" class="modal-backdrop" @click.self="closeForm">
        <div class="modal">
          <BookForm
            :bookToEdit="bookToEdit"
            @saved="onSavedAndClose"
            @closed="closeForm"
          />
        </div>
      </div>

      <!-- Modal Reseñas -->
      <div v-if="selectedBook" class="modal-backdrop" @click.self="closeReviews">
        <div class="modal modal-reviews">
          <ReviewList
            :bookId="selectedBook.id"
            :bookTitle="selectedBook.title"
            @close="closeReviews"
            @updated="refreshList"
          />
        </div>
      </div>
    </main>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import BookList from './components/BookList.vue';
import BookForm from './components/BookForm.vue';
import ReviewList from './components/ReviewList.vue';
import type { Book } from './types';

export default defineComponent({
  name: 'App',
  components: { BookList, BookForm, ReviewList },
  setup() {
    const selectedBook = ref<Book | null>(null);
    const showFormModal = ref(false);
    const bookToEdit = ref<Book | null>(null);
    const bookListRef = ref<InstanceType<typeof BookList> | null>(null);

    const openReviews = (book: Book) => {
      selectedBook.value = book;
    };

    const closeReviews = () => {
      selectedBook.value = null;
    };

    const openForm = () => {
      console.log('Abriendo formulario para crear libro');
      bookToEdit.value = null;
      showFormModal.value = true;
    };

    const openFormWithBook = (book: Book) => {
      console.log('Abriendo formulario para editar libro:', book);
      bookToEdit.value = book;
      showFormModal.value = true;
    };

    const closeForm = () => {
      console.log('Cerrando formulario');
      showFormModal.value = false;
      bookToEdit.value = null;
    };

    const onSavedAndClose = () => {
      console.log('Libro guardado, cerrando formulario y refrescando lista');
      closeForm();
      refreshList();
    };

    const refreshList = () => {
      console.log('Refrescando lista de libros');
      if (bookListRef.value) {
        bookListRef.value.fetch();
      }
    };

    return {
      selectedBook,
      showFormModal,
      bookToEdit,
      bookListRef,
      openForm,
      openFormWithBook,
      closeForm,
      openReviews,
      closeReviews,
      onSavedAndClose,
      refreshList,
    };
  },
});
</script>

<style>
/* Variables CSS para consistencia */
:root {
  --primary: #007bff;
  --secondary: #6c757d;
  --success: #28a745;
  --danger: #dc3545;
  --warning: #ffc107;
  --info: #17a2b8;
  --accent: #6f42c1;
  --muted: #6c757d;
  --light: #f8f9fa;
  --dark: #343a40;
  
  --border-color: #e9ecef;
  --border-radius: 8px;
  --shadow: 0 2px 8px rgba(0,0,0,0.1);
  --shadow-hover: 0 4px 16px rgba(0,0,0,0.15);
  
  --header-height: 120px;
}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', sans-serif;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  color: var(--dark);
  line-height: 1.6;
  min-height: 100vh;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header mejorado */
.app-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 2rem 0;
  box-shadow: var(--shadow);
  position: relative;
  overflow: hidden;
}

.app-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="4"/></g></svg>') repeat;
  pointer-events: none;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  text-align: center;
  position: relative;
  z-index: 1;
}

.app-header h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.header-subtitle {
  margin: 0;
  font-size: 1.1rem;
  opacity: 0.9;
  font-weight: 300;
}

/* Contenedor principal */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  flex: 1;
}

.main-actions {
  margin-bottom: 2rem;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.create-btn {
  background: linear-gradient(135deg, var(--accent) 0%, #9c88ff 100%);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: var(--border-radius);
  cursor: pointer;
  font-weight: 600;
  font-size: 1rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: var(--shadow);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.create-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
  background: linear-gradient(135deg, #9c88ff 0%, var(--accent) 100%);
}

.create-btn:active {
  transform: translateY(0);
}

.btn-icon {
  font-size: 1.2rem;
}

.layout {
  display: flex;
  gap: 2rem;
  
}

.main-content {
  flex: 1;
}

/* Modales */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem;
  z-index: 1000;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.modal {
  width: min(500px, 95%);
  max-height: 90vh;
  overflow: hidden;
  border-radius: var(--border-radius);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from { 
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to { 
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-reviews {
  width: min(1000px, 95%);
  height: 85vh;
  display: flex;
  flex-direction: column;
}

/* Responsive */
@media (max-width: 768px) {
  .app-header h1 {
    font-size: 2rem;
  }
  
  .header-subtitle {
    font-size: 1rem;
  }
  
  .container {
    padding: 1rem;
  }
  
  .create-btn {
    width: 100%;
    justify-content: center;
  }
  
  .modal-reviews {
    width: 95%;
    height: 90vh;
  }
}

@media (max-width: 480px) {
  .app-header {
    padding: 1.5rem 0;
  }
  
  .app-header h1 {
    font-size: 1.75rem;
  }
  
  .create-btn {
    padding: 0.875rem 1.5rem;
    font-size: 0.9rem;
  }
}
</style>