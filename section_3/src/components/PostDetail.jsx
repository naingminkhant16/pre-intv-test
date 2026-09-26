function PostDetail({ post }) {
    if (!post) {
        return (
            <div className="card h-100 border-0 bg-light">
                <div className="card-body d-flex align-items-center justify-content-center text-muted">
                    Select a post to read its body.
                </div>
            </div>
        )
    }

    return (
        <div className="card h-100 border-0 shadow-sm">
            <div className="card-body">
                <h5 className="card-title mb-3">{post.title}</h5>
                <p className="card-text mb-0">{post.body}</p>
            </div>
        </div>
    )
}

export default PostDetail
