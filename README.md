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