<!-- Add jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- JavaScript code for handling like/unlike -->
<script>
$(document).ready(function() {
  // Function to handle like/unlike
  function toggleLike(postId) {
    $.ajax({
      type: "POST",
      url: "like_post.php",
      data: {
        post_id: postId
      },
      success: function(response) {
        // Refresh the like count and button
        refreshLikeInfo(postId);
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  }

  // Function to refresh the like count and button
  function refreshLikeInfo(postId) {
    $.ajax({
      type: "GET",
      url: "get_like_info.php",
      data: {
        post_id: postId
      },
      success: function(response) {
        // Update the like count
        $("#like-count-" + postId).text(response.likeCount);

        // Update the like/unlike button
        if (response.liked) {
          $("#like-button-" + postId).text("Unlike");
        } else {
          $("#like-button-" + postId).text("Like");
        }
      },
      error: function(xhr, status, error) {
        console.log(xhr.responseText);
      }
    });
  }

  // Event listener for like/unlike button click
  $(".like-button").click(function() {
    var postId = $(this).data("post-id");
    toggleLike(postId);
  });
});
</script>

<?php
// ...

// Display like count and like/unlike button
// Check if the $post_id variable is set
if (isset($post_id)) {
    // Display like count and like/unlike button
    echo "<h2>Likes: <span id='like-count-$post_id'>$like_count</span></h2>";
  
    if ($liked) {
      echo "<button class='like-button' data-post-id='$post_id'>Unlike</button>";
    } else {
      echo "<button class='like-button' data-post-id='$post_id'>Like</button>";
    }
  }
  
// ...
?>
