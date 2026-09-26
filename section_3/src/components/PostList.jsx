function PostList({ posts, selectedPostId, onTitleClick }) {
    if (!posts.length) {
        return (
            <div className="alert alert-info mb-0" role="alert">
                No matching posts found.
            </div>
        )
    }

    return (
        <div className="list-group">
            {posts.map((post) => {
                const isLongTitle = post.title.length > 30
                const isSelected = selectedPostId === post.id

                return (
                    <button
                        key={post.id}
                        type="button"
                        className={[
                            'list-group-item',
                            'list-group-item-action',
                            isSelected ? 'active' : '',
                            isLongTitle ? 'list-group-item-warning' : '',
                        ].join(' ')}
                        onClick={() => onTitleClick(post)}
                    >
                        <div className="d-flex justify-content-between align-items-start gap-2">
                            <span>{post.title}</span>
                            {isLongTitle && (
                                <span className="badge text-bg-warning rounded-pill">Long</span>
                            )}
                        </div>
                    </button>
                )
            })}
        </div>
    )
}

export default PostList
