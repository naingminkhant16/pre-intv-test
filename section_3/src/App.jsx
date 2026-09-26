import { useEffect, useMemo, useState } from 'react'
import './App.css'
import PostDetail from './components/PostDetail'
import PostList from './components/PostList'

const API_URL = 'https://jsonplaceholder.typicode.com/posts'

function App() {
  const [posts, setPosts] = useState([])
  const [selectedPostId, setSelectedPostId] = useState(null)
  const [searchTerm, setSearchTerm] = useState('')
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  // const [sp, setSp] = useState(null)

  useEffect(() => {
    const fetchPosts = async () => {
      try {
        const response = await fetch(API_URL)

        if (!response.ok) {
          throw new Error(`Request failed with status ${response.status}`)
        }

        const data = await response.json()
        const firstTenPosts = Array.isArray(data) ? data.slice(0, 10) : []

        setPosts(firstTenPosts)
        setSelectedPostId(firstTenPosts[0]?.id ?? null)
      } catch (err) {
        setError(err.message || 'Unable to load posts. Please try again.')
      } finally {
        setLoading(false)
      }
    }

    fetchPosts()
  }, [])

  const filteredPosts = useMemo(() => {
    const term = searchTerm.trim().toLowerCase()

    if (!term) {
      return posts
    }

    return posts.filter((post) => post.title.toLowerCase().includes(term))
  }, [posts, searchTerm])


  // useEffect(() => {
  //   const fetchPostDetail = async () => {
  //     if (!selectedPostId) {
  //       setSp(null)
  //       return
  //     }
  //     try {
  //       const response = await fetch(API_URL + '/' + selectedPostId)
  //       if (!response.ok) {
  //         throw new Error(`Request failed with status ${response.status}`)
  //       }
  //       const data = await response.json()
  //       setSp(data)
  //     } catch (err) {
  //       setError(err.message || 'Unable to load post details. Please try again.')
  //     }
  //   }

  //   fetchPostDetail()
  // }, [selectedPostId])

  const selectedPost = posts.find((post) => post.id === selectedPostId) ?? filteredPosts[0] ?? null

  return (
    <div className="container py-4">
      <div className="row justify-content-center">
        <div className="col-lg-8">
          <div className="card shadow-sm border-0">
            <div className="card-header bg-primary text-white">
              <h1 className="h3 mb-0">Posts</h1>
            </div>

            <div className="card-body">
              <div className="mb-3">
                <input
                  type="text"
                  className="form-control"
                  placeholder="Search posts..."
                  value={searchTerm}
                  onChange={(event) => setSearchTerm(event.target.value)}
                />
              </div>

              {loading && (
                <div className="text-center py-4" aria-live="polite">
                  <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                  </div>
                </div>
              )}

              {!loading && error && (
                <div className="alert alert-danger" role="alert">
                  {error}
                </div>
              )}

              {!loading && !error && (
                <>
                  <p className="text-muted small mb-3">
                    Showing {filteredPosts.length} of {posts.length} posts
                  </p>

                  <div className="row g-3">
                    <div className="col-md-6">
                      <PostList
                        posts={filteredPosts}
                        selectedPostId={selectedPostId}
                        onTitleClick={(post) => setSelectedPostId(post.id)}
                      />
                    </div>

                    <div className="col-md-6">
                      <PostDetail post={selectedPost} />
                    </div>
                    {/* <div className="col-md-6">
                      <PostDetail post={sp} />
                    </div> */}
                  </div>
                </>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default App
