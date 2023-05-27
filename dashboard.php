<!DOCTYPE html>
<html>
<head>
    <title>Social Media App - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
        }
        .card img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Social Media App</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                          <span>Profile</span>
                            <i class="fas fa-user"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php">
                          <span>Messages</span>
                            <i class="fas fa-envelope"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="notifications.php">
                            <i class="fas fa-bell"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="friend_requests.php">
                            <i class="fas fa-user-friends"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Welcome, <?php echo $username; ?></h2>
        <h4>Friend Requests</h4>
        <?php if (!empty($friendRequests)): ?>
            <div class="card">
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($friendRequests as $request): ?>
                            <li class="list-group-item">
                                <?php echo $request['username']; ?>
                                <div class="float-right">
                                    <form action="accept_friend_request.php" method="POST" class="d-inline">
                                        <input type="hidden" name="sender_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Accept</button>
                                    </form>
                                    <form action="reject_friend_request.php" method="POST" class="d-inline">
                                        <input type="hidden" name="sender_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <p>No friend requests at the moment.</p>
        <?php endif; ?>

        <h4>Create Post</h4>
        <form method="POST" action="create_post.php" enctype="multipart/form-data">
            <textarea name="content" placeholder="Enter your post content" class="form-control"></textarea>
            <br>
            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" name="image" id="image">
            </div>
            <br>
            <input type="submit" value="Create" class="btn btn-primary">
        </form>

        <h4>My Posts</h4>
        <?php foreach ($posts as $post): ?>
            <div class="card">
                <div class="card-header">
                    <?php echo $post['username']; ?>
                </div>
                <div class="card-body">
                    <?php echo $post['content']; ?>
                </div>
                <?php if (!empty($post['image'])): ?>
                    <img src="<?php echo $post['image']; ?>" class="card-img-top" alt="Post Image">
                <?php endif; ?>
                <div class="card-footer">
                    <form action="like_post.php" method="POST" class="d-inline">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Like <span class="badge badge-light"><?php echo $post['likes']; ?></span>
                        </button>
                    </form>
                    <a href="#" class="btn btn-info btn-sm">
                        Comments <span class="badge badge-light"><?php echo count($post['comments']); ?></span>
                    </a>
                </div>
                <div class="card-footer">
                    <form action="add_comment.php" method="POST">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <div class="input-group">
                            <input type="text" name="comment" class="form-control" placeholder="Add a comment">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Post</button>
                            </div>
                        </div>
                    </form>
                    <?php if (!empty($post['comments'])): ?>
                        <ul class="list-group mt-2">
                            <?php foreach ($post['comments'] as $comment): ?>
                                <li class="list-group-item">
                                    <strong><?php echo $comment['username']; ?>:</strong> <?php echo $comment['comment']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
