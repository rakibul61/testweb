<?php
  include("includes/header.php");
  if(isset($_POST['post'])) {
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $error_message = "";
    if($imageName != "") {
      $targetDir = "assets/images/posts/";
      $imageName = $targetDir . uniqid() . basename($imageName);
      $imageFiletype = pathinfo($imageName, PATHINFO_EXTENSION);
      if($_FILES['fileToUpload']['size'] > 10000000) {
        $error_message = "Sorry, your file is too large!";
        $uploadOk = 0;
      }
      if((strtolower($imageFiletype) != "jpeg") && (strtolower($imageFiletype) != "png") && (strtolower($imageFiletype) != "jpg")) {
        $error_message = "Sorry, only JPEG, JPG and PNG files are allowed";
        $uploadOk = 0;
      }
      if($uploadOk) {
        if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
        }
        else {
          $uploadOk = 0;
        }
      }
    }
    if($uploadOk) {
      $post = new Post($con, $userLoggedIn);
      $post->submitPost($_POST['post_text'], 'none', $imageName);
    }
    else {
      echo "<div style='text-align: center;' class='alert alert-danger'>
              $error_message
            </div>";
    }
  }
?>
    <div class="user_details column">
      <a href="<?php echo $userLoggedIn ?>"><img src="<?php echo $user['profile_pic']; ?>" alt="Profile Picture"></a>
      <div class="user_details_left_right">
        <a href="<?php echo $userLoggedIn ?>">
        <?php
          echo $user['first_name']." ".$user['last_name'];
        ?>
        </a>
        <br>
        <?php
          echo "Posts: "." ".$user['num_posts']."<br>";
          echo "Likes: "." ".$user['num_likes'];
        ?>
      </div>
    </div>

    <div class="main_column column">
      <form action="index.php" method="POST" class="post_form" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" id="fileToUpload">
        <textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
        <input type="submit" name="post" id="post" value="Post">
        <hr>
      </form>

      <div class="posts_area"></div>
      <img id="loading" src="assets/images/icons/loading.gif">
    </div>

    <div class="user_details column">
      <h4>Popular</h4>
      <div class="trends">
        <?php
          $query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");
          foreach($query as $row) {
            $word = $row['title'];
            $word_dot = strlen($word) >= 14 ? "..." : "";
            $trimmed_word = str_split($word, 14);
            $trimmed_word = $trimmed_word[0];
            echo "<div style='padding: 1px'>";
            echo $trimmed_word . $word_dot;
            echo "<br><br></div>";
          }
        ?>
      </div>
    </div>

    <script>
      var userLoggedIn = '<?php echo $userLoggedIn; ?>';
      $(document).ready(function() {
        $('#loading').show();

        //Original ajax request for loading first posts
        $.ajax({
          url: "includes/handlers/ajax_load_posts.php",
          type: "POST",
          data: "page=1&userLoggedIn=" + userLoggedIn,
          cache: false,

          success: function(data) {
            $('#loading').hide();
            $('.posts_area').html(data);
          }
        });
        $(window).scroll(function() {
          var height = $('.posts_area').height();
          var scroll_top = $(this).scrollTop();
          var page = $('.posts_area').find('.nextPage');
          var noMorePosts = $('.posts_area').find('.noMorePosts').val();

          if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
            $('#loading').show();
            var ajaxReq = $.ajax({
              url: "includes/handlers/ajax_load_posts.php",
              type: "POST",
              data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
              cache: false,

              success: function(respone) {
                $('.posts_area').find('.nextPage').remove();
                $('.posts_area').find('.noMorePosts').remove();
                $('#loading').hide();
                $('.posts_area').append(response);
              }
            });
          }
          return false;
        });
      });
    </script>
  </div>
</body>
</html>