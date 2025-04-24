
# Library App

This is a Symfony-based Library Management web application.

## ✨ Features

- User registration & login with password hashing
- Book CRUD (Create, Read, Update, Delete)
- Role-based access control (only logged-in users can manage books)
- Remember-me login option
- Functional tests with PHPUnit
- Dockerized environment (PHP + MySQL)
- AJAX-powered dynamic search
- Pagination for book listing

---

## 🚀 Getting Started

### 1. Clone repository
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
- Go to [http://localhost:8000/register](http://localhost:8000/register)
- Create a new account (email + password)

### Login
- Visit [http://localhost:8000/login](http://localhost:8000/login)
- Use your credentials to log in

---

## 📖 Book Management

After logging in, navigate to:
```
/book
```
You can:
- Create a new book
- View the list
- Edit or delete existing books
- Use AJAX-powered search

---

## 🔍 AJAX Search

- Real-time search is available on the book list page
- Uses native JavaScript and `fetch()` to query `/book/search?q=...`
- Results are dynamically updated without page reload

---

## 📄 Pagination

- The book list at `/book` is paginated
- Powered by `KnpPaginatorBundle`
- Displays up to 10 books per page

---

## 🔧 Testing

### Run all tests:
```bash
docker exec -it library_app-php-1 php bin/phpunit
```

## 🧪 Functional Tests

This project includes functional tests that cover:
- ✅ Book creation with valid and invalid data
- ✅ Book listing pagination on `/book`
- ✅ AJAX search via `/book/search`

Each test:
- Truncates `book` and `user` tables before execution
- Ensures a test user `naujokas@example.com` with password `test1234` exists
- Uses Symfony’s `WebTestCase` with the `KernelBrowser` client

---

## 🚪 Access Control

| Route        | Access         |
|--------------|----------------|
| `/register`  | Public         |
| `/login`     | Public         |
| `/book/*`    | ROLE_USER only |

---

## 🚪 Admin user (for local use)

You can manually insert a user with hashed password:

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

- [x] User registration and login with form-based authentication
- [x] CRUD operations for Book entity
- [x] Form validation using Symfony Validator (e.g., ISBN, Date, Required fields)
- [x] Functional tests with PHPUnit
- [x] Dockerized environment with MySQL
- [x] CSRF protection in forms
- [x] AJAX-powered book search
- [x] Pagination support for book list
