<template>
  <form class="review-form" @submit.prevent="onSubmit">
    <div class="form-header">
      <h4>Añadir nueva reseña</h4>
    </div>
    
    <div class="form-body">
      <label class="field">
        <span class="field-label">Calificación</span>
        <div class="rating-input">
          <select v-model.number="rating" required>
            <option value="5">★★★★★ (5) - Excelente</option>
            <option value="4">★★★★☆ (4) - Muy bueno</option>
            <option value="3">★★★☆☆ (3) - Bueno</option>
            <option value="2">★★☆☆☆ (2) - Regular</option>
            <option value="1">★☆☆☆☆ (1) - Malo</option>
          </select>
        </div>
      </label>

      <label class="field">
        <span class="field-label">Comentario</span>
        <textarea 
          v-model="comment" 
          rows="6" 
          maxlength="1000"
          placeholder="Comparte tu opinión sobre el libro..."
        ></textarea>
        <small class="char-count">{{ comment.length }}/1000</small>
      </label>

      <div v-if="error" class="error">{{ error }}</div>
    </div>

    <div class="form-actions">
      <button type="submit" class="submit-btn" :disabled="loading">
        <span v-if="loading">⏳</span>
        <span v-else>📝</span>
        {{ loading ? 'Guardando...' : 'Publicar reseña' }}
      </button>
    </div>
  </form>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { createReview } from '../services/api';

export default defineComponent({
  name: 'ReviewForm',
  props: { bookId: { type: Number, required: true } },
  emits: ['saved'],
  setup(props, { emit }) {
    const rating = ref<number>(5);
    const comment = ref<string>('');
    const loading = ref(false);
    const error = ref<string | null>(null);

    const onSubmit = async () => {
      loading.value = true;
      error.value = null;
      try {
        await createReview({ 
          book_id: props.bookId, 
          rating: rating.value, 
          comment: comment.value.trim() 
        });
        
        // Reset form
        comment.value = '';
        rating.value = 5;
        
        emit('saved');
      } catch (err: unknown) {
        console.error(err);
        interface ApiError {
          response?: {
            data?: {
              errors?: unknown;
            };
          };
        }

        if (err instanceof Error && (err as ApiError)?.response?.data?.errors) {
          error.value = JSON.stringify((err as ApiError)?.response?.data?.errors || 'Error desconocido');
        } else {
          error.value = 'Error al crear reseña';
        }
      } finally {
        loading.value = false;
      }
    };

    return { rating, comment, loading, error, onSubmit };
  },
});
</script>

<style scoped>
.review-form {
  background: transparent;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.form-header {
  padding: 1.5rem;
  background: white;
  border-bottom: 1px solid #e9ecef;
}

.form-header h4 {
  margin: 0;
  color: #495057;
  font-size: 1.1rem;
  font-weight: 600;
}

.form-body {
  padding: 1.5rem;
  flex: 1;
  overflow-y: auto;
}

.field {
  display: flex;
  flex-direction: column;
  margin-bottom: 1.5rem;
}

.field-label {
  font-weight: 500;
  margin-bottom: 0.75rem;
  color: #495057;
  font-size: 0.9rem;
}

.rating-input select {
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 0.9rem;
  background: white;
  cursor: pointer;
  width: 100%;
}

.rating-input select:focus {
  outline: none;
  border-color: #80bdff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.field textarea {
  padding: 0.75rem;
  border: 1px solid #ced4da;
  border-radius: 6px;
  font-size: 0.9rem;
  font-family: inherit;
  resize: vertical;
  min-height: 120px;
  background: white;
  line-height: 1.5;
}

.field textarea:focus {
  outline: none;
  border-color: #80bdff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.char-count {
  color: #6c757d;
  font-size: 0.8rem;
  text-align: right;
  margin-top: 0.5rem;
}

.form-actions {
  padding: 1rem 1.5rem;
  background: white;
  border-top: 1px solid #e9ecef;
}

.submit-btn {
  background: #28a745;
  color: white;
  border: none;
  padding: 0.875rem 1.5rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  width: 100%;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.submit-btn:hover:not(:disabled) {
  background: #218838;
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.submit-btn:disabled {
  background: #6c757d;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.error {
  color: #dc3545;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 0.75rem;
  border-radius: 6px;
  margin-top: 1rem;
  font-size: 0.9rem;
}
</style>