<template>
  <div class="reviews-panel">
    <!-- Header fijo -->
    <div class="reviews-header">
      <h3>Reseñas - {{ bookTitle }}</h3>
      <button class="close-btn" @click="$emit('close')">
        <span>&times;</span>
      </button>
    </div>

    <!-- Layout de dos columnas -->
    <div class="reviews-body">
      <!-- Columna izquierda: Lista de reseñas -->
      <div class="reviews-left">
        <div class="reviews-content">
          <div v-if="loading" class="loading">Cargando reseñas...</div>
          <div v-if="error" class="error">{{ error }}</div>

          <div v-if="reviews.length && !loading" class="reviews-list">
            <h4 class="section-title">
              Reseñas existentes ({{ reviews.length }})
            </h4>
            <div v-for="rev in reviews" :key="rev.id" class="review-item">
              <div class="review-meta">
                <div class="rating">
                  <span class="stars">{{ '★'.repeat(rev.rating) }}{{ '☆'.repeat(5 - rev.rating) }}</span>
                  <strong>{{ rev.rating }}/5</strong>
                </div>
                <span class="date">{{ formatDate(rev.created_at) }}</span>
              </div>
              <p class="comment" v-if="rev.comment">{{ rev.comment }}</p>
              <div class="review-actions">
                <button class="delete-btn" @click="onDelete(rev.id)">
                  Eliminar
                </button>
              </div>
            </div>
          </div>

          <div v-else-if="!reviews.length && !loading" class="no-reviews">
            <div class="no-reviews-icon">📚</div>
            <h4>Sin reseñas aún</h4>
            <p>Sé el primero en reseñar este libro</p>
          </div>
        </div>
      </div>

      <!-- Columna derecha: Formulario -->
      <div class="reviews-right">
        <div class="form-container">
          <ReviewForm :bookId="bookId" @saved="fetchReviews" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, watch } from 'vue';
import ReviewForm from './ReviewForm.vue';
import { getReviewsByBook, deleteReview } from '../services/api';
import type { Review } from '../types';

export default defineComponent({
  name: 'ReviewList',
  components: { ReviewForm },
  props: {
    bookId: { type: Number, required: true },
    bookTitle: { type: String, default: '' }
  },
  emits: ['close', 'updated'],
  setup(props, { emit }) {
    const reviews = ref<Review[]>([]);
    const loading = ref(false);
    const error = ref<string | null>(null);

    const fetchReviews = async () => {
      loading.value = true;
      error.value = null;
      try {
        const res = await getReviewsByBook(props.bookId);
        const data = res.data as { reviews: Review[] };
        reviews.value = data.reviews ?? [];
        emit('updated');
      } catch (err) {
        console.error(err);
        error.value = 'Error cargando reseñas';
      } finally {
        loading.value = false;
      }
    };

    const onDelete = async (id?: number) => {
      if (!id) return;
      if (!confirm('¿Está seguro de eliminar esta reseña?')) return;
      try {
        await deleteReview(id);
        await fetchReviews();
        emit('updated');
      } catch (err) {
        console.error(err);
        error.value = 'Error eliminando reseña';
      }
    };

    const formatDate = (dateString?: string) => {
      if (!dateString) return '';
      try {
        return new Date(dateString).toLocaleDateString('es-ES', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });
      } catch {
        return dateString;
      }
    };

    watch(() => props.bookId, () => fetchReviews(), { immediate: true });

    return { reviews, loading, error, fetchReviews, onDelete, formatDate };
  },
});
</script>

<style scoped>
.reviews-panel {
  background: white;
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
}

.reviews-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  flex-shrink: 0;
}

.reviews-header h3 {
  margin: 0;
  color: #333;
  font-size: 1.25rem;
}

.close-btn {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1.2rem;
  line-height: 1;
  color: #6c757d;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #e9ecef;
  color: #495057;
}

.close-btn span {
  font-size: 1.5rem;
  font-weight: bold;
}

.reviews-body {
  flex: 1;
  display: flex;
  min-height: 0;
}

.reviews-left {
  flex: 1;
  border-right: 1px solid #e9ecef;
  display: flex;
  flex-direction: column;
}

.reviews-right {
  width: 350px;
  background: #f8f9fa;
  display: flex;
  flex-direction: column;
}

.reviews-content {
  flex: 1;
  overflow-y: auto;
  padding: 1rem;
  min-height: 0;
}

.form-container {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.section-title {
  margin: 0 0 1rem 0;
  color: #495057;
  font-size: 1rem;
  font-weight: 600;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e9ecef;
}

.loading {
  padding: 2rem;
  text-align: center;
  color: #6c757d;
}

.error {
  color: #dc3545;
  background: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 1rem;
  border-radius: 6px;
  margin: 1rem 0;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.review-item {
  padding: 1rem;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}

.review-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.review-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.stars {
  color: #ffc107;
  font-size: 1.1rem;
}

.rating strong {
  color: #495057;
  font-size: 0.9rem;
}

.date {
  color: #6c757d;
  font-size: 0.85rem;
}

.comment {
  margin: 0 0 0.75rem 0;
  line-height: 1.5;
  color: #495057;
}

.review-actions {
  display: flex;
  justify-content: flex-end;
}

.delete-btn {
  background: #dc3545;
  color: white;
  border: none;
  padding: 0.4rem 0.8rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
  transition: background-color 0.2s;
}

.delete-btn:hover {
  background: #c82333;
}

.no-reviews {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  color: #6c757d;
  height: 100%;
}

.no-reviews-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.no-reviews h4 {
  margin: 0 0 0.5rem 0;
  color: #495057;
}

.no-reviews p {
  margin: 0;
  font-style: italic;
}

/* Scrollbar personalizado */
.reviews-content::-webkit-scrollbar {
  width: 6px;
}

.reviews-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.reviews-content::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.reviews-content::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Responsive */
@media (max-width: 768px) {
  .reviews-body {
    flex-direction: column;
  }
  
  .reviews-left {
    border-right: none;
    border-bottom: 1px solid #e9ecef;
  }
  
  .reviews-right {
    width: 100%;
    max-height: 300px;
  }
}
</style>