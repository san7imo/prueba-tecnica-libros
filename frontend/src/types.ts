// src/types.ts
export interface Book {
  id: number;
  title: string;
  author: string;
  published_year: number;
  average_rating?: number | null;
  reviews?: Review[];
}

export interface Review {
  id: number;
  book_id: number;
  rating: number;
  comment?: string;
  created_at?: string;
}
