# Prueba Técnica - Sistema de Libros y Reseñas

Sistema web para gestionar libros y sus reseñas, desarrollado con Symfony 6 + Vue 3.

## Requisitos del Sistema

- PHP 8.1 o superior
- Composer
- MySQL 8.0 o superior (o MariaDB 10.3+)
- Node.js 16+ y npm
- Symfony CLI (opcional pero recomendado)

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/prueba-tecnica-libros.git
cd prueba-tecnica-libros
```

### 2. Configurar el Backend (Symfony)

```bash
# Copiar archivo de configuración
cp .env.example .env

# Editar .env y configurar la conexión a la base de datos
# DATABASE_URL="mysql://root:password@127.0.0.1:3306/prueba_libros?serverVersion=8.0.32&charset=utf8mb4"

# Instalar dependencias
composer install

# Crear la base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate

# Cargar datos de prueba (fixtures)
php bin/console doctrine:fixtures:load

# Iniciar servidor de desarrollo
symfony server:start
# O alternativamente: php -S localhost:8000 -t public/
```

### 3. Configurar el Frontend (Vue 3)

```bash
# Navegar al directorio frontend
cd frontend

# Instalar dependencias
npm install

# Iniciar servidor de desarrollo
npm run dev
```

El frontend estará disponible en: http://localhost:3000
El backend API estará disponible en: http://localhost:8000

## Endpoints de la API

### GET /api/books
Obtiene la lista de libros con su calificación promedio.

**Respuesta de ejemplo:**
```json
[
  {
    "title": "El Arte de Programar",
    "author": "Donald Knuth",
    "published_year": 1968,
    "average_rating": 4.7
  },
  {
    "title": "Clean Code",
    "author": "Robert C. Martin",
    "published_year": 2008,
    "average_rating": 3.5
  },
  {
    "title": "Refactoring",
    "author": "Martin Fowler",
    "published_year": 1999,
    "average_rating": 5.0
  }
]
```

### POST /api/reviews
Crea una nueva reseña para un libro.

**Request body:**
```json
{
  "book_id": 1,
  "rating": 5,
  "comment": "Excelente libro"
}
```

**Respuesta exitosa (201 Created):**
```json
{
  "id": 7,
  "book_id": 1,
  "rating": 5,
  "comment": "Excelente libro",
  "created_at": "2024-01-15 10:30:45"
}
```

**Respuesta de error (400 Bad Request):**
```json
{
  "errors": {
    "rating": "El rating debe estar entre 1 y 5",
    "comment": "El comentario no puede estar vacío"
  }
}
```

## Ejemplos de uso con cURL

### Obtener libros:
```bash
curl -X GET http://localhost:8000/api/books
```

### Crear una reseña:
```bash
curl -X POST http://localhost:8000/api/reviews \
  -H "Content-Type: application/json" \
  -d '{
    "book_id": 1,
    "rating": 4,
    "comment": "Muy buen libro, lo recomiendo"
  }'
```

### Ejemplo de error de validación:
```bash
curl -X POST http://localhost:8000/api/reviews \
  -H "Content-Type: application/json" \
  -d '{
    "book_id": 999,
    "rating": 6,
    "comment": ""
  }'
```

## Datos de Prueba

El sistema incluye los siguientes datos iniciales:

**Libros:**
1. "El Arte de Programar" - Donald Knuth (1968)
2. "Clean Code" - Robert C. Martin (2008)  
3. "Refactoring" - Martin Fowler (1999)

**Reseñas:** 6 reseñas distribuidas entre los libros con ratings variados (1-5).

## Validaciones Implementadas

- **Rating:** Debe ser un entero entre 1 y 5 (inclusive)
- **Book ID:** Debe existir en la base de datos
- **Comment:** No puede estar vacío
- **Cálculo de promedio:** Se calcula usando AVG() en la consulta SQL para eficiencia

## Escalabilidad

**¿Qué cambiarías para escalar esta app a cientos de miles de libros y usuarios?**

Para escalar la aplicación consideraría:

1. **Base de datos:** Implementar índices en campos de búsqueda frecuente, considerar sharding horizontal y usar réplicas de lectura.

2. **Cache:** Redis/Memcached para cachear promedios de ratings y listas de libros más consultados.

3. **API:** Implementar paginación en endpoints, rate limiting, y versionado de API.

4. **Arquitectura:** Separar en microservicios (books-service, reviews-service), implementar CQRS para separar lecturas de escrituras.

5. **Frontend:** Lazy loading, virtual scrolling para listas grandes, y CDN para assets estáticos.

6. **Infraestructura:** Load balancers, containers con Kubernetes, monitoreo con Prometheus/Grafana.

7. **Procesamiento:** Cola de jobs asíncronos (RabbitMQ/Redis Queue) para recálculo de promedios.

## Estructura del Proyecto

```
prueba-tecnica-libros/
├── src/
│   ├── Controller/
│   │   ├── BookController.php
│   │   └── ReviewController.php
│   ├── Entity/
│   │   ├── Book.php
│   │   └── Review.php
│   ├── Repository/
│   │   ├── BookRepository.php
│   │   └── ReviewRepository.php
│   └── DataFixtures/
│       └── BookFixtures.php
├── frontend/
│   ├── src/
│   │   ├── App.vue
│   │   ├── main.js
│   │   └── style.css
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
├── migrations/
├── config/
├── .env.example
├── composer.json
└── README.md
```

## Información del Branch

- **Branch evaluado:** main
- **Commit final:** [Insertar hash del último commit]

## Tecnologías Utilizadas

- **Backend:** Symfony 6, Doctrine ORM, MySQL
- **Frontend:** Vue 3 (Composition API), Axios, Vite
- **Validaciones:** Symfony Validator
- **CORS:** NelmioCorsBundle