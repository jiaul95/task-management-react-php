function TaskList({ tasks, onEdit, onDelete }) {

    return (
        <div>

            {tasks.length === 0 && (
                <p>No tasks found.</p>
            )}

            {tasks.map((task) => (
                <div key={task.id} className="task-item">

                    <h3>{task.title}</h3>

                    <p>{task.description}</p>

                    <p>
                        Status: {task.status}
                    </p>

                    <p>
                        Priority: {task.priority}
                    </p>

                    <p>
                        Due Date: {task.due_date || 'No due date'}
                    </p>

                    <button onClick={() => onEdit(task)}>
                        Edit
                    </button>

                    <button onClick={() => onDelete(task.id)}>
                        Delete
                    </button>

                </div>
            ))}

        </div>
    );
}

export default TaskList;