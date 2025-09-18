// src/services/api.ts
import axios from 'axios';
import type { Book, Review } from '../types';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 5000,
});

// Books
export const getBooks = () => api.get<Book[]>('/books');
export const getBook = (id: number) => api.get<Book>(`/books/${id}`);
export const createBook = (payload: { title: string; author: string; published_year: number }) =>
  api.post('/books', payload);
export const updateBook = (id: number, payload: Partial<{ title: string; author: string; published_year: number }>) =>
  api.put(`/books/${id}`, payload);
export const deleteBook = (id: number) => api.delete(`/books/${id}`);

// Reviews
export const getReviewsByBook = (bookId: number) => api.get<{ reviews: Review[] }>(`/books/${bookId}`);

export const createReview = (payload: { book_id: number; rating: number; comment?: string }) =>
  api.post('/reviews', payload);
export const updateReview = (id: number, payload: Partial<{ rating: number; comment: string }>) =>
  api.put(`/reviews/${id}`, payload);
export const deleteReview = (id: number) => api.delete(`/reviews/${id}`);

export default api;
