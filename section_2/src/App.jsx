import './App.css'
import TaskList from './components/TaskList'

function App() {
  return (
    <div className="app-shell">
      <div className="container py-5">
        <div className="row justify-content-center">
          <div className="col-12 col-md-8 col-lg-6">
            <TaskList />
          </div>
        </div>
      </div>
    </div>
  )
}

export default App
