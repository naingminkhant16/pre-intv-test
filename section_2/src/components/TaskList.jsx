import { useEffect, useState } from 'react'

import TaskInput from './TaskInput'
import TaskItem from './TaskItem'

const defaultTasks = [
    { id: 1, title: 'Reading Book', completed: false },
    { id: 2, title: 'Go to the gym', completed: true },
    { id: 3, title: 'Watch a movie', completed: false },
]
const defaultFilter = 'all'
const STORAGE_KEY = 'task-manager-items'

export default function TaskList() {
    const [tasks, setTasks] = useState(defaultTasks)
    const [filter, setFilter] = useState(defaultFilter)

    // set initial tasks
    useEffect(() => {
        const savedTasks = localStorage.getItem(STORAGE_KEY)

        if (!savedTasks) return

        const parsedTasks = JSON.parse(savedTasks)
        if (Array.isArray(parsedTasks) && parsedTasks.length > 0) {
            setTasks(parsedTasks)
        }
    }, [])

    useEffect(() => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(tasks))
    }, [tasks])

    // filter tasks
    const filteredTasks = tasks.filter((task) => {
        if (filter === 'active') return !task.completed
        if (filter === 'completed') return task.completed
        return true
    })

    // add new task
    const handleAddTask = (title) => {
        setTasks((currentTasks) => [
            { id: Date.now(), title, completed: false },
            ...currentTasks,
        ])
    }

    // set task completed or not
    const toggleTask = (taskId) => {
        setTasks((currentTasks) =>
            currentTasks.map((task) =>
                task.id === taskId ? { ...task, completed: !task.completed } : task,
            ))
    }

    // delete task
    const deleteTask = (taskId) => {
        setTasks((currentTasks) => currentTasks.filter((task) => task.id !== taskId))
    }

    return (
        <div className="card task-manager-card shadow-lg border-0">
            <div className="card-body p-4 p-md-5">
                <h2 className="card-title text-center mb-4 text-primary fw-bold">Task Manager</h2>

                <TaskInput onAddTask={handleAddTask} />

                <div className="btn-group w-100 mb-4" role="group" aria-label="Task filters">
                    {['all', 'active', 'completed'].map((option) => (
                        <button
                            key={option}
                            type="button"
                            className={`btn btn-sm ${filter === option ? 'btn-primary' : 'btn-outline-primary'}`}
                            onClick={() => setFilter(option)}
                        >
                            {option.toUpperCase()}
                        </button>
                    ))}
                </div>

                <ul className="list-group list-group-flush">
                    {filteredTasks.length > 0 ? (
                        filteredTasks.map((task) => (
                            <TaskItem
                                key={task.id}
                                task={task}
                                onToggle={toggleTask}
                                onDelete={deleteTask}
                            />
                        ))
                    ) : (
                        <li className="list-group-item text-center text-muted py-4">
                            No tasks in this view.
                        </li>
                    )}
                </ul>
            </div>
        </div>
    )
}