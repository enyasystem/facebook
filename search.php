<?php
// Include the necessary configuration and database connection
require_once "config.php";

// Define an empty array to store the search results
$searchResults = [];

// Define a variable to track if the search form is submitted
$searchSubmitted = false;

// Check if the search form is submitted
if (isset($_GET['query'])) {
    // Retrieve the search query from the form submission
    $query = $_GET['query'];

    // Set the searchSubmitted flag to true
    $searchSubmitted = true;

    // Perform search queries on relevant tables (e.g., posts, users, etc.)
    // Adjust the queries based on your database structure and search requirements

    // Check if the search query is not empty
    if (!empty($query)) {
        // Search posts table
        $sqlPosts = "SELECT * FROM posts WHERE content LIKE '%$query%'";
        $resultPosts = $conn->query($sqlPosts);
        if ($resultPosts->num_rows > 0) {
            while ($row = $resultPosts->fetch_assoc()) {
                $row['type'] = 'post'; // Add the type to identify the search result
                $searchResults[] = $row;
            }
        }

        // Search users table
        $sqlUsers = "SELECT * FROM users WHERE username LIKE '%$query%' OR email LIKE '%$query%'";
        $resultUsers = $conn->query($sqlUsers);
        if ($resultUsers->num_rows > 0) {
            while ($row = $resultUsers->fetch_assoc()) {
                $row['type'] = 'user'; // Add the type to identify the search result
                $searchResults[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Search</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Add your custom CSS styles here -->

</head>

<body>
    <!-- Navigation bar -->
    <?php include "navbar.php"; ?>

    <div class="container mt-5">
        <h2>Search</h2>

        <!-- Search form -->
        <form method="GET" action="search.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="query" class="form-control" placeholder="Enter your search query">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <!-- Display search results -->
        <?php if ($searchSubmitted) : ?>
            <?php if (empty($query)) : ?>
                <div class="alert alert-danger" role="alert">
                    Please enter a search query.
                </div>
            <?php elseif (!empty($searchResults)) : ?>
                <h3>Search Results</h3>
                <ul class="list-group">
                    <?php foreach ($searchResults as $result) : ?>
                        <li class="list-group-item">
                            <?php if ($result['type'] === 'post') : ?>
                                <strong>Post:</strong> <a href="post.php?id=<?php echo $result['id']; ?>"><?php echo $result['content']; ?></a>
                            <?php elseif ($result['type'] === 'user') : ?>
                                <strong>User:</strong> <a href="profile.php?user_id=<?php echo $result['id']; ?>"><?php echo $result['username']; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No results found for "<?php echo $query; ?>"</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Add your custom scripts here -->

</body>

</html>
