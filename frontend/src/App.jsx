import { useEffect, useState } from 'react';
import TaskList from './components/TaskList';
import TaskForm from './components/TaskForm';
import Login from './components/Login';
import Register from './components/Register';
import './App.css';

function App() {

  const [user, setUser] = useState(null);
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [selectedTask, setSelectedTask] = useState(null);
  const [showRegister, setShowRegister] = useState(false);

  const [page, setPage] = useState(1);
  const [limit] = useState(5);
  const [status, setStatus] = useState('');

  const apiUrl = import.meta.env.VITE_API_URL;

  const checkAuth = async () => {

    try {

      const response = await fetch(
        `${apiUrl}/auth/me.php`,
        {
          credentials: 'include'
        }
      );

      if (!response.ok) {
        setUser(null);
        setTasks([]);
        setLoading(false);
        return;
      }

      const loggedInUser = await response.json();

      setUser(loggedInUser);

    } catch (error) {

      console.error(error);

      setUser(null);
      setTasks([]);
      setLoading(false);
    }
  };

  useEffect(() => {
    checkAuth();
  }, []);

  const getTasks = async () => {

    try {

      setLoading(true);
      setError('');

      let url =
        `${apiUrl}/tasks/index.php?page=${page}&limit=${limit}`;

      if (status) {
        url += `&status=${status}`;
      }

      const response = await fetch(url, {
        credentials: 'include'
      });

      if (response.status === 401) {

        setUser(null);
        setTasks([]);
        setSelectedTask(null);

        return;
      }

      const result = await response.json();

      if (!response.ok) {
        throw new Error(
          result.message || 'Failed to fetch tasks'
        );
      }

      setTasks(result.data);

    } catch (error) {

      setError(error.message);

    } finally {

      setLoading(false);
    }
  };

  useEffect(() => {

    if (user) {
      getTasks();
    }

  }, [user, page, status]);

  const handleEdit = (task) => {
    setSelectedTask(task);
  };

  const handleTaskSaved = () => {

    setSelectedTask(null);

    getTasks();
  };

  const handleDelete = async (id) => {

    const confirmDelete = window.confirm(
      'Are you sure you want to delete this task?'
    );

    if (!confirmDelete) {
      return;
    }

    try {

      setError('');

      const response = await fetch(
        `${apiUrl}/tasks/delete.php?id=${id}`,
        {
          method: 'DELETE',
          credentials: 'include'
        }
      );

      if (response.status === 401) {

        setUser(null);
        setTasks([]);
        setSelectedTask(null);

        return;
      }

      const result = await response.json();

      if (!response.ok) {
        throw new Error(
          result.message || 'Failed to delete task'
        );
      }

      if (
        selectedTask &&
        selectedTask.id === id
      ) {
        setSelectedTask(null);
      }

      getTasks();

    } catch (error) {

      setError(error.message);
    }
  };

  const handleStatusChange = (event) => {

    setStatus(event.target.value);
    setPage(1);
  };

  const handleLogout = async () => {

    try {

      await fetch(
        `${apiUrl}/auth/logout.php`,
        {
          method: 'POST',
          credentials: 'include'
        }
      );

    } catch (error) {

      console.error(error);

    } finally {

      setUser(null);
      setTasks([]);
      setSelectedTask(null);
      setPage(1);
      setStatus('');
    }
  };

  const handleSessionExpired = () => {

    setUser(null);
    setTasks([]);
    setSelectedTask(null);
    setError('');
  };

  if (!user) {

    if (showRegister) {
      return (
        <Register
          onRegister={() => setShowRegister(false)}
        />
      );
    }

    return (
      <Login
        onLogin={(loggedInUser) => {

          setUser(loggedInUser);
          setPage(1);
          setStatus('');
          setError('');
        }}
        onRegister={() => setShowRegister(true)}
      />
    );
  }

  return (
    <div className="container">

      <div className="header">

        <div>

          <h1>Task Manager</h1>

          <p>
            Welcome, {user.name}
          </p>

          <p>
            Role: {user.role}
          </p>

        </div>

        <button onClick={handleLogout}>
          Logout
        </button>

      </div>

      <div className="task-layout">

        <div className="form-section">

          <TaskForm
            task={selectedTask}
            onTaskSaved={handleTaskSaved}
            onSessionExpired={handleSessionExpired}
          />

        </div>

        <div className="list-section">

          <div className="task-list-header">

            <h2>Tasks</h2>

            <select
              value={status}
              onChange={handleStatusChange}
            >
              <option value="">
                All
              </option>

              <option value="todo">
                Todo
              </option>

              <option value="in-progress">
                In Progress
              </option>

              <option value="done">
                Done
              </option>
            </select>

          </div>

          {loading && (
            <p>Loading tasks...</p>
          )}

          {error && (
            <p className="error">
              {error}
            </p>
          )}

          {!loading && !error && (
            <TaskList
              tasks={tasks}
              onEdit={handleEdit}
              onDelete={handleDelete}
            />
          )}

          <div className="pagination">

            <button
              onClick={() => setPage(page - 1)}
              disabled={page === 1}
            >
              Previous
            </button>

            <span>
              Page {page}
            </span>

            <button
              onClick={() => setPage(page + 1)}
              disabled={tasks.length < limit}
            >
              Next
            </button>

          </div>

        </div>

      </div>

    </div>
  );
}

export default App;