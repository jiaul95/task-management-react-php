# Task Management System

A simple full-stack Task Management application built using PHP, MySQL and React.

The application allows users to register and log in, create and manage their
own tasks, while administrators can manage tasks created by all users.

This project was developed as part of a PHP / MySQL / React practical
assessment.

---

## Tech Stack

### Backend

- PHP 8.2
- MySQL
- MySQLi
- REST APIs
- PHP Sessions

### Frontend

- React
- JavaScript
- Vite
- CSS

### Development Environment

- XAMPP / Apache
- MySQL
- Node.js
- npm

---

## Main Features

### Task Management

- Create tasks
- View tasks
- Edit tasks
- Delete tasks
- Update task status
- Set task priority
- Set due date
- Filter tasks by status
- Basic pagination
- Display task owner

### Authentication

- User registration
- User login
- Secure password hashing
- Password verification
- Session-based authentication
- Logout
- Session check

### Authorization

The application supports two roles:

- Admin
- User

A normal user can only view and manage their own tasks.

An admin can view and manage tasks created by all users.

The API returns:

- `401 Unauthorized` when authentication is required
- `403 Forbidden` when the logged-in user does not have permission

---

# Project Structure

```text
task_management_php_react_mysql/
│
├── frontend/
│   ├── public/
│   │
│   ├── src/
│   │   ├── components/
│   │   │   ├── Login.jsx
│   │   │   ├── Register.jsx
│   │   │   ├── TaskForm.jsx
│   │   │   └── TaskList.jsx
│   │   │
│   │   ├── App.jsx
│   │   ├── App.css
│   │   └── main.jsx
│   │
│   ├── .env
│   ├── .env.example
│   ├── .gitignore
│   ├── package.json
│   └── vite.config.js
│
├── server/
│   │
│   ├── api/
│   │   ├── auth/
│   │   │   ├── register.php
│   │   │   ├── login.php
│   │   │   ├── logout.php
│   │   │   └── me.php
│   │   │
│   │   └── tasks/
│   │       ├── index.php
│   │       ├── create.php
│   │       ├── update.php
│   │       └── delete.php
│   │
│   ├── config/
│   │   ├── config.php
│   │   └── cors.php
│   │
│   ├── includes/
│   │   ├── db.php
│   │   └── auth.php
│   │
│   └── test_connection.php
│
├── .gitignore
└── README.md