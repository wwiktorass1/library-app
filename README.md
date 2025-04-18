# Library App

This is a Symfony-based Library Management web application.

## ✨ Features

- User registration & login with password hashing
- Book CRUD (Create, Read, Update, Delete)
- Role-based access control (only logged-in users can manage books)
- Remember-me login option
- Functional tests with PHPUnit
- Dockerized environment (PHP + MySQL)

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

---

## 🔧 Testing

### Run all tests:
```bash
docker exec -it library_app-php-1 bash
php bin/phpunit
```

---

## 🏑 Technologies Used
- Symfony 6.x
- Doctrine ORM
- Twig templates
- PHPUnit for testing
- Docker / docker-compose
- MySQL 8

---

## 🚪 Access Control

| Route | Access |
|-------|--------|
| `/register` | Public |
| `/login`    | Public |
| `/book/*`   | ROLE_USER only |

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



