import { useEffect, useState } from 'react';

function TaskForm({ task, onTaskSaved, onSessionExpired }) {

    const [form, setForm] = useState({
        title: '',
        description: '',
        status: 'todo',
        priority: 'medium',
        due_date: ''
    });

    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const apiUrl = import.meta.env.VITE_API_URL;

    useEffect(() => {

        if (task) {
            setForm({
                title: task.title || '',
                description: task.description || '',
                status: task.status || 'todo',
                priority: task.priority || 'medium',
                due_date: task.due_date || ''
            });
        } else {
            setForm({
                title: '',
                description: '',
                status: 'todo',
                priority: 'medium',
                due_date: '',
                user_id: 1
            });
        }

        setError('');

    }, [task]);

    const handleChange = (event) => {

        const { name, value } = event.target;

        setForm({
            ...form,
            [name]: value
        });
    };

    const handleSubmit = async (event) => {

        event.preventDefault();

        if (!form.title.trim()) {
            setError('Title is required');
            return;
        }

        try {

            setLoading(true);
            setError('');

            let url =
                `${apiUrl}/tasks/create.php`;

            let method = 'POST';

            if (task) {
                url =
                    `${apiUrl}/tasks/update.php?id=${task.id}`;

                method = 'PUT';
            }

            const response = await fetch(url, {
                method: method,
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(form)
            });

            if (response.status === 401) {

                setUser(null);

                return;
            }

            const result = await response.json();

            if (response.status === 401) {
                onSessionExpired();
                return;
            }

            if (!response.ok) {
                throw new Error(
                    result.message || 'Failed to save task'
                );
            }

            setForm({
                title: '',
                description: '',
                status: 'todo',
                priority: 'medium',
                due_date: ''
            });

            onTaskSaved();

        } catch (error) {

            setError(error.message);

        } finally {

            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="task-form">

            <h2>
                {task ? 'Edit Task' : 'Add Task'}
            </h2>

            {error && <p>{error}</p>}

            <div>
                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    value={form.title}
                    onChange={handleChange}
                />
            </div>

            <div>
                <label>Description</label>

                <textarea
                    name="description"
                    value={form.description}
                    onChange={handleChange}
                />
            </div>

            <div>
                <label>Status</label>

                <select
                    name="status"
                    value={form.status}
                    onChange={handleChange}
                >
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

            <div>
                <label>Priority</label>

                <select
                    name="priority"
                    value={form.priority}
                    onChange={handleChange}
                >
                    <option value="low">
                        Low
                    </option>

                    <option value="medium">
                        Medium
                    </option>

                    <option value="high">
                        High
                    </option>
                </select>
            </div>

            <div>
                <label>Due Date</label>

                <input
                    type="date"
                    name="due_date"
                    value={form.due_date}
                    onChange={handleChange}
                />
            </div>

            <button
                type="submit"
                disabled={loading}
            >
                {loading
                    ? 'Saving...'
                    : task
                        ? 'Update Task'
                        : 'Add Task'
                }
            </button>

        </form>
    );
}

export default TaskForm;