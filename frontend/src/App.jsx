import { useEffect, useState } from 'react';
import TaskList from './components/TaskList';
import './App.css';

function App() {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const getTasks = async () => {
    try {
      setLoading(true);
      setError('');

      const response = await fetch(
        'http://localhost/task_management_php_react_mysql/server/api/tasks/index.php'
      );

      console.log(response);

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || 'Failed to fetch tasks');
      }

      setTasks(result.data);
    } catch (error) {
      setError(error.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    getTasks();
  }, []);

  const handleEdit = (task) => {
    console.log('Edit task:', task);
  };

  const handleDelete = async (id) => {
    console.log('Delete task:', id);
  };

  if (loading) {
    return <p>Loading tasks...</p>;
  }

  if (error) {
    return <p>{error}</p>;
  }

  return (
    <div className="container">
      <h1>Task Manager</h1>

      <TaskList
        tasks={tasks}
        onEdit={handleEdit}
        onDelete={handleDelete}
      />
    </div>
  );
}

export default App;