# Task Management System

A simple full-stack Task Management application built using PHP, MySQL and React.

The application allows users to register and log in, create and manage their
own tasks, while administrators can manage tasks created by all users.

## Tech Stack

### Backend
- PHP 8.2
- MySQL
- REST APIs
- MySQLi
- PHP Sessions

### Frontend
- React
- JavaScript
- Vite
- CSS

## Main Features

### Task Management

- Create a task
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
- Password hashing
- Session-based authentication
- Logout
- Session check

### Authorization

The application has two roles:

- Admin
- User

A normal user can only view and manage their own tasks.

An admin can view and manage tasks created by all users.

The API returns `401 Unauthorized` when authentication is required and
`403 Forbidden` when the logged-in user does not have permission to perform
an operation.

## Project Structure

```text
task_management_php_react_mysql/
│
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Login.jsx
│   │   │   ├── Register.jsx
│   │   │   ├── TaskForm.jsx
│   │   │   └── TaskList.jsx
│   │   ├── App.jsx
│   │   ├── App.css
│   │   └── main.jsx
│   ├── .env
│   ├── .env.example
│   ├── .gitignore
│   ├── package.json
│   └── vite.config.js
│
├── server/
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
└── README.md