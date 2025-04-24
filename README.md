# 📚 Library App

This is a Symfony-based Library Management web application.

---

## ✨ Features

- ✅ User registration & login with password hashing
- ✅ Book CRUD (Create, Read, Update, Delete)
- ✅ Role-based access control (only logged-in users can manage books)
- ✅ Remember-me login option
- ✅ Functional tests with PHPUnit
- ✅ Dockerized environment (PHP + MySQL)
- ✅ AJAX-powered dynamic search
- ✅ Pagination for book listing
- ✅ AJAX form submission with validation errors shown inline

---

## 🚀 Getting Started

### 1. Clone the repository

```bash
git clone https://github.com/wwiktorass1/library-app.git
cd library-app
```

### 2. Start containers with Docker

```bash
docker-compose up -d --build
```

App will be available at: [http://localhost:8000](http://localhost:8000)

### 3. Set up the database

```bash
docker exec -it library_app-php-1 bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

## 👨‍💼 Authentication

### Register

- Visit [http://localhost:8000/register](http://localhost:8000/register)
- Create a new account (email + password)

### Login

- Go to [http://localhost:8000/login](http://localhost:8000/login)
- Use your credentials to log in

---

## 📖 Book Management

After logging in, navigate to:

```
/book
```

You can:
- Create a new book
- View, edit, or delete existing books
- Use AJAX-powered search
- Enjoy pagination

---

## 🔍 AJAX Search

- Real-time search on the book list page
- Uses native JavaScript `fetch()` to request `/book/search?q=...`
- Results are dynamically updated without page reload

---

## 📩 AJAX Form Submission with Validation

Book form submission (creation & edit) uses jQuery + AJAX:

- Valid data: redirects to book list
- Invalid data: errors shown near each field (styled with Bootstrap's `is-invalid`)
- Implemented in `assets/book-form.js`
- Rendered form uses `novalidate` and `#book-form` for precise JS control

You can customize validation styling in `book/_form.html.twig`.

---

## 📄 Pagination

- The book list at `/book` is paginated
- Powered by `KnpPaginatorBundle`
- Displays 10 books per page

---

## 🔧 Testing

Run all functional and controller tests:

```bash
docker exec -it library_app-php-1 php bin/phpunit
```

---

## 🧪 Functional Tests

This project includes tests for:

- ✅ Creating books with valid & invalid data
- ✅ AJAX book search results
- ✅ Book listing with pagination
- ✅ Form validation errors

Test structure:

- Uses `WebTestCase` and `KernelBrowser`
- `book` and `user` tables are truncated before each test
- Test user: `naujokas@example.com` / `test1234`

---

## 🚪 Access Control

| Route        | Access         |
|--------------|----------------|
| `/register`  | Public         |
| `/login`     | Public         |
| `/book/*`    | ROLE_USER only |

---

## 🛠️ Technologies Used

- Symfony 6.x
- Doctrine ORM
- Twig templates
- PHPUnit
- Webpack Encore
- Bootstrap 5
- jQuery
- Docker + MySQL 8

---

## 🧑‍💻 Developer Info

### Admin user for local testing:

```sql
INSERT INTO user (email, roles, password) VALUES (
  'admin@example.com',
  '["ROLE_USER"]',
  '$2y$13$mhBY6T9lfXSevU3yevtkzuptPaKdSQKmUdKMtcIn80vfiJCIYwJ9i'
);
```

Password: `test1234`

---

## ✅ Feature Checklist

- [x] Form-based authentication
- [x] Registration with role `ROLE_USER`
- [x] CRUD functionality for Book entity
- [x] Form validation with Symfony Validator
- [x] CSRF protection
- [x] Docker-based development
- [x] Functional and controller tests
- [x] AJAX book search
- [x] Pagination support
- [x] AJAX form submit + inline validation

---

## 🎯 Final Notes

This app is great as a base for learning Symfony, testing, and frontend/backend integration.  
Feel free to fork, customize, and extend it as needed!

---

> Created with ❤️ using Symfony 6 and Docker.

---

## 📘 API Documentation (OpenAPI / Swagger)

This project includes full API documentation using **NelmioApiDocBundle** and the **OpenAPI 3** specification.

- Swagger UI: [http://localhost:8000/api/doc](http://localhost:8000/api/doc)
- Raw JSON: [http://localhost:8000/api/doc.json](http://localhost:8000/api/doc.json)

### Available Endpoints

| Method   | Endpoint             | Description                              |
|----------|----------------------|------------------------------------------|
| `GET`    | `/book`              | Retrieve a paginated list of books       |
| `POST`   | `/book/new`          | Create a new book                        |
| `GET`    | `/book/search?q=...` | Search for books by title or author      |
| `GET`    | `/book/{id}`         | Get a single book by ID                  |
| `PUT`    | `/book/{id}/edit`    | Update an existing book                  |
| `DELETE` | `/book/{id}`         | Delete a book                            |

### How to Access

Make sure your Docker environment is running and the application is accessible at `http://localhost:8000`. Then navigate to `/api/doc` in your browser to view the Swagger interface.

---

## 📡 AJAX Form Submission

This project supports asynchronous (AJAX) form submission for book creation:

- When submitted via JavaScript (`XMLHttpRequest`), the `/book/new` endpoint:
  - Returns `204 No Content` on successful creation.
  - Returns `400 Bad Request` and renders form partial with validation errors if input is invalid.

Example error response (partial HTML):

```html
<div class="form-error-message text-danger title-error">
    This value should not be blank
</div>
```

---

## 📐 Architectural decisions

This project follows **Symfony best practices** with clear separation of concerns:

- **MVC pattern** is used to isolate business logic (`Controller`, `Entity`, `Twig`).
- **Service layer** handles logic beyond controllers (e.g., data processing, validation).
- **Repository pattern** is used for custom queries and search logic.
- **FormType classes** help encapsulate form and validation logic.
- **Doctrine ORM** handles all data persistence with proper constraints.
- **AJAX interactions** are handled via `XMLHttpRequest` and Symfony responses.
- **Validation is layered**:
  - Entity-level constraints (e.g., `@Assert`)
  - Form-level error rendering
  - AJAX-safe validation feedback

---

## 💡 Challenges & Solutions

| Challenge | Solution |
|----------|----------|
| Preventing circular references in entity serialization | Used groups in API responses and disabled circular reference handler |
| Supporting both AJAX and regular form submissions | Detected `isXmlHttpRequest()` and rendered partial form template on failure |
| Making validation testable in both form and API | Reused the same `BookType` with constraints in all controllers and ensured test coverage |
| Avoiding route conflicts (e.g. `/book/search` vs `/book/{id}`) | Ordered routes properly in controller (specific before dynamic) |

---

## 🚀 Deployment instructions

This application can be deployed using Docker on any cloud platform:

### 1. Build and run the Docker container

```bash
# Build the Docker image
docker build -t library-app .

# Run the container in production mode
docker run -d -p 8000:8000 -e APP_ENV=prod library-app
```

### 2. After the container is running:

```bash
# Run database migrations
docker exec -it <container_id> php bin/console doctrine:migrations:migrate --no-interaction

# Warm up the cache
docker exec -it <container_id> php bin/console cache:warmup
```

> Replace `<container_id>` with the actual container ID from `docker ps`.