# 📃 Library App

This is a Symfony-based Library Management web application.

---

## ✨ Features

- ✅ User registration & login with password hashing
- ✅ Book CRUD (Create, Read, Update, Delete)
- ✅ Role-based access control (only logged-in users can manage books)
- ✅ Remember-me login option
- ✅ Functional tests with PHPUnit
- ✅ Dockerized environment (PHP + MySQL)
- ✅ AJAX-powered dynamic search with loading spinner and no-results message
- ✅ Pagination for book listing
- ✅ AJAX form submission with loading spinner, success message, and inline validation errors
- ✅ OpenAPI 3 / Swagger API documentation

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

The app will be available at: [http://localhost:8000](http://localhost:8000)

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
- Use AJAX-powered live search
- Enjoy pagination

---

## 🔍 AJAX Search

- Real-time search on the book list page
- Uses native JavaScript `fetch()` to request `/book/search?q=...`
- Loading spinner displayed while searching
- "No results found" message shown if no matches
- Results dynamically updated without page reload

---

## 📩 AJAX Form Submission with Validation

Book form submission (creation & edit) uses jQuery + AJAX:

- While submitting, a loading spinner is shown inside the Save button
- On successful save, a Bootstrap success alert is displayed and form is reset
- On validation errors, messages are shown near each field (styled with Bootstrap's `is-invalid`)
- Implemented in `assets/book-form.js`
- Rendered form uses `novalidate` and `#book-form` for precise JS control

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

Test classes are located under `tests/`:

- `Functional/` – covers form rendering, CRUD actions
- `Controller/Api/` – covers API endpoints and responses

Tests include:
- Form validation errors
- Successful AJAX submissions
- Search functionality tests

---

## ✅ Test Status

All functional tests are passing:

- **28 tests**  
- **100+ assertions**

Run tests with:

```bash
docker compose exec php php bin/phpunit
```

---

## 🚪 Access Control

| Route        | Access         |
|--------------|----------------|
| `/register`  | Public          |
| `/login`     | Public          |
| `/book/*`    | ROLE_USER only  |

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
- [x] AJAX book search with spinner and no-results
- [x] Pagination support
- [x] AJAX form submit with inline validation and success message

---

## 📘️ API Documentation (OpenAPI / Swagger)

This project includes full API documentation using **NelmioApiDocBundle** and the **OpenAPI 3** specification.

- Swagger UI: [http://localhost:8000/api/doc](http://localhost:8000/api/doc)
- Raw JSON: [http://localhost:8000/api/doc.json](http://localhost:8000/api/doc.json)

### Available Endpoints

| Method   | Endpoint             | Description                              |
|----------|----------------------|------------------------------------------|
| `GET`    | `/book`               | Retrieve a paginated list of books       |
| `POST`   | `/book/new`           | Create a new book                        |
| `GET`    | `/book/search?q=...`  | Search for books by title or author      |
| `GET`    | `/book/{id}`          | Get a single book by ID                  |
| `PUT`    | `/book/{id}/edit`     | Update an existing book                  |
| `DELETE` | `/book/{id}`          | Delete a book                            |

### 📡 Example API Usage

#### Create a Book (AJAX-style)

```http
POST /book/new
Content-Type: application/x-www-form-urlencoded

book[title]=Ajax+Book&book[author]=Me&book[isbn]=9780306406157
```

- ✅ Response: `204 No Content` if valid
- ❌ Response: `400 Bad Request` with form errors (JSON)

#### Search Books (AJAX-style)

```http
GET /book/search?q=history
```

Response: partial HTML rendered with matching books

---

## 📊 Architectural decisions

This project follows **Symfony best practices**:

- **MVC pattern** for clear separation
- **Repository pattern** for data queries
- **Service layer** for logic
- **Doctrine ORM** for persistence
- **AJAX** interaction without full reloads
- **Bootstrap styling** for frontend

---

## 🚀 Deployment instructions

To deploy using Docker:

```bash
docker build -t library-app .
docker run -d -p 8000:8000 -e APP_ENV=prod library-app
docker exec -it <container_id> php bin/console doctrine:migrations:migrate --no-interaction
docker exec -it <container_id> php bin/console cache:warmup
```

---

> Created with ❤️ using Symfony 6 and Docker.

