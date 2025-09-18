# Prueba Técnica - Sistema de Gestión de Libros y Reseñas

Este es un sistema web para la gestión de una biblioteca de libros y sus reseñas, desarrollado con **Symfony 6** para el backend (API REST) y **Vue 3** para el frontend. 

---

## Evolución del Proyecto

El desarrollo se realizó en fases, reflejadas en los siguientes commits:

- **Primer Commit (Requerimientos Mínimos):**  
  Se completaron los requisitos iniciales de la prueba.  
  Se implementaron los modelos (`Book` y `Review`), las migraciones, las fixtures y los endpoints `GET /api/books` y `POST /api/reviews`.  
  Se incluyó una vista básica en Vue 3 que consume la API.

- **Commits Adicionales (Mejoras y Expansión):**  
  Se extendió la funcionalidad para implementar un **CRUD completo** (Crear, Leer, Actualizar, Borrar) para libros y reseñas.  
  Se realizaron mejoras significativas en la interfaz de usuario del frontend para ofrecer una experiencia más completa e interactiva.

---

## Características Principales

- **Backend Robusto:** API RESTful construida con Symfony 6, Doctrine ORM y Doctrine Migrations.
- **Frontend Dinámico:** Interfaz de usuario intuitiva en Vue 3 (Composition API) que consume la API con Axios.
- **CRUD Completo:** Permite crear, leer, actualizar y eliminar libros y reseñas.
- **Validación de Datos:** Reglas de validación en el backend para garantizar la integridad de los datos, incluyendo:
  - Rating debe estar entre 1 y 5.
  - `book_id` debe existir.
  - `comment` no puede estar vacío.
- **Rendimiento:** El promedio de calificación (`average_rating`) se calcula de forma eficiente usando una consulta DQL con `AVG()`.

---

## Requisitos del Sistema

- PHP 8.1+ y Composer  
- MySQL 8.0+ (o equivalente como MariaDB)  
- Node.js 16+ y npm  
- Symfony CLI (opcional pero recomendado)  

---

## Instalación y Configuración

### 1. Clonar el Repositorio
```bash
git clone https://github.com/san7imo/prueba-tecnica-libros.git
cd prueba-tecnica-libros
```

### 2. Configuración del Backend (Symfony)
```bash
# Copia el archivo de configuración de entorno
cp .env.example .env

# Edita el archivo .env para configurar tu base de datos
# Ejemplo: DATABASE_URL="mysql://root:password@127.0.0.1:3306/prueba_libros?serverVersion=8.0.32&charset=utf8mb4"

# Instala las dependencias de Composer
composer install

# Crea la base de datos
php bin/console doctrine:database:create

# Ejecuta las migraciones de Doctrine para crear las tablas
php bin/console doctrine:migrations:migrate

# Carga los datos de prueba iniciales (fixtures)
php bin/console doctrine:fixtures:load

# Inicia el servidor de desarrollo de Symfony
symfony server:start
# (Alternativa: php -S localhost:8000 -t public/)
```

El backend de la API estará disponible en: [http://localhost:8000](http://localhost:8000)

---

### 3. Configuración del Frontend (Vue 3)
```bash
# Navega al directorio del frontend
cd frontend

# Instala las dependencias de Node.js
npm install

# Inicia el servidor de desarrollo de Vite
npm run dev
```

El frontend estará disponible en: [http://localhost:5173](http://localhost:5173)  
*(Corrección: Vite usa el puerto 5173 por defecto)*

---

## Endpoints de la API

### Libros
| Método | Endpoint             | Descripción                     |
|--------|----------------------|---------------------------------|
| GET    | `/api/books`         | Lista todos los libros con su calificación promedio. |
| POST   | `/api/books`         | Crea un nuevo libro.            |
| PUT    | `/api/books/{id}`    | Actualiza un libro existente.   |
| DELETE | `/api/books/{id}`    | Elimina un libro.               |

### Reseñas
| Método | Endpoint                     | Descripción                               |
|--------|------------------------------|-------------------------------------------|
| GET    | `/api/books/{bookId}/reviews`| Obtiene todas las reseñas de un libro específico. |
| POST   | `/api/reviews`               | Crea una nueva reseña para un libro.      |
| DELETE | `/api/reviews/{id}`          | Elimina una reseña.                       |

---

## Ejemplos de Peticiones y Respuestas

### GET /api/books 
**Respuesta exitosa:**
```json
[
  {
    "title": "El Arte de Programar",
    "author": "Donald Knuth",
    "published_year": 1968,
    "average_rating": 4.7
  }
]
```

### POST /api/reviews 
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
---

## Estructura: 

```bash
prueba-tecnica-libros/
├── bin/                      # Scripts ejecutables de Symfony
├── composer.json             # Dependencias PHP
├── composer.lock
├── config/                   # Configuración de Symfony (doctrine, bundles, routes, etc.)
├── migrations/               # Migraciones generadas por Doctrine
├── public/                   # Carpeta pública (entrypoint: index.php)
├── src/                      # Código fuente del backend Symfony
│   ├── Controller/           # Controladores API REST
│   │   ├── BookController.php
│   │   └── ReviewController.php
│   ├── DataFixtures/         # Datos de prueba para la BD
│   │   ├── AppFixtures.php
│   │   └── BookFixtures.php
│   ├── Entity/               # Entidades Doctrine (mapeo de tablas)
│   │   ├── Book.php
│   │   └── Review.php
│   ├── Repository/           # Repositorios para consultas personalizadas
│   │   ├── BookRepository.php
│   │   └── ReviewRepository.php
│   └── Kernel.php            # Kernel principal de Symfony
├── symfony.lock
├── var/                      # Cachés y logs de Symfony
├── vendor/                   # Dependencias PHP instaladas con Composer
├── README.md                 # Documentación principal
└── frontend/                 # Aplicación frontend Vue 3 + Vite
    ├── env.d.ts              # Tipos de entorno
    ├── eslint.config.ts      # Configuración de ESLint
    ├── index.html            # HTML base de Vite
    ├── node_modules/         # Dependencias de Node.js
    ├── package.json          # Dependencias y scripts frontend
    ├── package-lock.json
    ├── public/               # Archivos públicos frontend
    ├── README.md             # Documentación del frontend
    ├── src/                  # Código fuente del frontend
    │   ├── App.vue           # Componente raíz Vue
    │   ├── components/       # Componentes UI
    │   │   ├── BookForm.vue
    │   │   ├── BookList.vue
    │   │   ├── ReviewForm.vue
    │   │   └── ReviewList.vue
    │   ├── main.ts           # Punto de entrada Vue
    │   ├── services/         # Servicios API (axios)
    │   │   └── api.ts
    │   ├── shims-vue.d.ts    # Definiciones TS para Vue
    │   ├── style.css         # Estilos globales
    │   └── types.ts          # Tipos TS (Book, Review, etc.)
    ├── tsconfig.json         # Configuración TypeScript
    ├── tsconfig.app.json
    ├── tsconfig.node.json
    └── vite.config.ts        # Configuración de Vite
```

---

## Escalabilidad

Para escalar la aplicación, consideraría las siguientes mejoras:

1. **Base de Datos:** Implementar índices en campos de búsqueda (title, author). Para grandes volúmenes de datos, considerar sharding horizontal.
2. **Mecanismos de Caché:** Redis o Memcached para cachear datos como promedios de calificación.
3. **Optimización de API:** Implementar paginación y rate limiting.
4. **Microservicios:** Separar en servicios independientes (books-service, reviews-service).
5. **Procesamiento Asíncrono:** Usar colas de jobs (RabbitMQ, Redis Queue) para cálculos de promedios.

---

## Información del Branch y Commit

- **Branch a evaluar:** main  
- **Commit final:** 159880dcb079e6a19c569b644078390302712335