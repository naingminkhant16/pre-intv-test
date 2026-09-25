import { useState } from 'react'

export default function TaskInput({ onAddTask }) {
    const [title, setTitle] = useState('')
    const [error, setError] = useState('')

    const handleSubmit = (event) => {
        event.preventDefault()

        const trimmedTitle = title.trim()

        if (!trimmedTitle) {
            setError('Task title is required.')
            return
        }

        if (trimmedTitle.length < 3) {
            setError('Task title must be at least 3 characters long.')
            return
        }

        onAddTask(trimmedTitle)
        setTitle('')
        setError('')
    }

    return (
        <form onSubmit={handleSubmit} className="mb-4">
            <div className="input-group">
                <input
                    type="text"
                    className="form-control"
                    placeholder="Enter a task"
                    value={title}
                    onChange={(event) => {
                        setTitle(event.target.value)
                        if (error) setError('')
                    }}
                />
                <button className="btn btn-primary" type="submit">
                    Add Task
                </button>
            </div>
            {error && <div className="text-danger small mt-2">{error}</div>}
        </form>
    )
}