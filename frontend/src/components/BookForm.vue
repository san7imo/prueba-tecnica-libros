<template>
  <form class="modal-form" @submit.prevent="onSubmit">
    <div class="modal-header">
      <h3>{{ bookToEdit ? 'Editar libro' : 'Agregar libro' }}</h3>
    </div>

    <div class="modal-body">
      <label class="field">
        <span>Título</span>
        <input v-model="title" required maxlength="255" />
      </label>

      <label class="field">
        <span>Autor</span>
        <input v-model="author" required maxlength="255" />
      </label>

      <label class="field">
        <span>Año</span>
        <input v-model.number="published_year" type="number" min="1000" :max="currentYear" required />
      </label>

      <div v-if="error" class="error">{{ error }}</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="primary" :disabled="loading">
        {{ loading ? 'Guardando...' : (bookToEdit ? 'Actualizar libro' : 'Guardar libro') }}
      </button>
      <button type="button" class="secondary" @click="reset" :disabled="loading">Cancelar</button>
    </div>
  </form>
</template>

<script lang="ts">
import { defineComponent, ref, computed, watch } from 'vue';
import { createBook, updateBook } from '../services/api';
import type { Book } from '../types';

export default defineComponent({
  name: 'BookForm',
  props: {
    bookToEdit: { 
      type: Object as () => Book | null, 
      default: null,
      required: false 
    }
  },
  emits: {
    saved: null,
    closed: null
  },
  setup(props, { emit }) {
    const title = ref('');
    const author = ref('');
    const published_year = ref<number | null>(new Date().getFullYear());
    const loading = ref(false);
    const error = ref<string | null>(null);

    const currentYear = computed(() => new Date().getFullYear());

    // Definir reset ANTES del watch
    const reset = () => {
      title.value = '';
      author.value = '';
      published_year.value = currentYear.value;
      error.value = null;
      emit('closed');
    };

    // Función para limpiar campos sin emitir 'closed'
    const clearFields = () => {
      title.value = '';
      author.value = '';
      published_year.value = currentYear.value;
      error.value = null;
    };

    // Llenar campos cuando cambie bookToEdit
    watch(() => props.bookToEdit, (book) => {
      if (book) {
        title.value = book.title;
        author.value = book.author;
        published_year.value = book.published_year;
        error.value = null;
      } else {
        // Solo limpiar campos, no cerrar el modal
        clearFields();
      }
    }, { immediate: true });

    const onSubmit = async () => {
      error.value = null;
      if (!title.value || !author.value || !published_year.value) {
        error.value = 'Todos los campos son requeridos';
        return;
      }

      loading.value = true;
      try {
        if (props.bookToEdit) {
          // Actualizar libro existente
          await updateBook(props.bookToEdit.id, {
            title: title.value,
            author: author.value,
            published_year: published_year.value,
          });
        } else {
          // Crear nuevo libro
          await createBook({
            title: title.value,
            author: author.value,
            published_year: published_year.value,
          });
        }
        
        emit('saved');
        clearFields();
      } catch (err: unknown) {
        console.error(err);
        error.value = 'Error al guardar el libro';
      } finally {
        loading.value = false;
      }
    };

    return { title, author, published_year, currentYear, loading, error, onSubmit, reset };
  },
});
</script>

<style scoped>
.modal-form {
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.modal-header {
  padding: 1rem;
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.modal-header h3 {
  margin: 0;
  color: #333;
}

.modal-body {
  padding: 1rem;
}

.field {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}

.field span {
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #333;
}

.field input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
}

.field input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.modal-footer {
  padding: 1rem;
  background: #f8f9fa;
  border-top: 1px solid #dee2e6;
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
}

.primary {
  background: #007bff;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
}

.primary:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

.secondary {
  background: #6c757d;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
}

.secondary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error {
  color: #dc3545;
  margin-top: 0.5rem;
  padding: 0.5rem;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
}
</style>