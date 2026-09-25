export default function TaskItem({ task, onToggle, onDelete }) {
    return (
        <li className={`list-group-item rounded-3 mb-2 border ${task.completed ? 'bg-light text-decoration-line-through text-muted' : 'bg-white'}`}>
            <div className="d-flex align-items-center justify-content-between gap-3 py-2">
                <div className="d-flex align-items-center gap-3 flex-grow-1">
                    <input
                        type="checkbox"
                        className="form-check-input"
                        checked={task.completed}
                        onChange={() => onToggle(task.id)}
                    />
                    <span className="flex-grow-1">{task.title}</span>
                </div>

                <button
                    type="button"
                    className="btn btn-sm btn-outline-danger"
                    onClick={() => onDelete(task.id)}
                >
                    Delete
                </button>
            </div>
        </li>
    )
}