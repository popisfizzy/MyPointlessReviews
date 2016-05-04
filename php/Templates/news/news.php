    <!-- Infopanes. -->
    
    <div class="info"> <?

      // This processes the display of infopanes, which are small templates that display some
      // sort of information. For example, recent reviews or blog posts or etc.
      
      foreach(Template::Load("news")->GetPanes() as $template => $class)
      {
        // Open up the pane, with a comment indicating which it is.
        echo "\n\n      <!-- $template pane template. -->\n\n" .
             "      <div class=\"infopane\">\n\n";
             
        // Now include the pane.
        $class->Display();
        
        // And close it.
        echo "\n      </div>";
      }
      
      echo "\n";
?>

      <!-- Articles. --> <?

  /* This pulls the news posts from the database, formats them, and then prints them. */
  
  // Include the Database class.
  include_once("classes/database.php");
  
  // Try and connect.
  Database::Connect();
  
  $page = 1; // The current page. The base is used to calculate which news posts we will pull
             // from the database. These start at ((5 * $page) - 5) and show 5.
  
  if(isset($_GET["page"]))
  // If it's in the $_GET data, then pull it and sanitize it.
  {
    $page = Database::Escape($_GET["page"]);
    $page = strval($page);
  }
  
  if(!is_numeric($page))
    // If it's not a number, default to 1.
    $page = 1;
  
  // Must be an integer.
  $page = floor($page);

  if($page < 1)
    // Has to be at least equal to one.
    $page = 1;
  
  // This selects 5 queries.
  $query = Database::Query("
    SELECT `news_posts`.`id` AS `news_id`,
           `news_posts`.`title` AS `title`,
           `news_posts`.`content` AS `content`,
            UNIX_TIMESTAMP(`news_posts`.`postdate`) AS `postdate`,
            UNIX_TIMESTAMP(`news_posts`.`editdate`) AS `editdate`,
           `users`.`id` AS `user_id`,
           `users`.`username` AS `username`,
            COUNT(`news_comments`.`id`) AS `comments`
    FROM `news_posts`
         LEFT JOIN `users`
           ON `news_posts`.`user_id` = `users`.`id`
         LEFT JOIN `news_comments`
           ON `news_posts`.`id` = `news_comments`.`news_id`
           GROUP BY `news_posts`.`id`
    ORDER BY `news_posts`.`postdate` DESC
    LIMIT " . ((5 * $page) - 5) . ", 5
  ");
  
  if($query->num_rows == 0)
  // If there were no rows, make a second query. This makes the assumption that the page number was
  // too large for us to grab anything (the limit was, basically, out of bounds, and thus we'll
  // grab the last 5 rows.
  {
    // This first grabs the total number of news posts (rows in the news_posts table), as we'll
    // need it below in two places.
    $total = Database::Query("
      SELECT COUNT(`id`) AS `total`
      FROM `news_posts`
    ");
    
    // Stores the above as an int.
    $total = intval(Database::Query()->fetch_assoc()["total"]);
  
    // This grabs the last 1 to 5 rows by selecting in ascending order, and then converting to
    // descending order. The number of rows is equal to $total mod 5, making this equivalent to
    // looking at the last valid page.
    $query = Database::Query("
      SELECT *
      FROM (
        SELECT `news_posts`.`id` AS `news_id`,
               `news_posts`.`title` AS `title`,
               `news_posts`.`content` AS `content`,
                UNIX_TIMESTAMP(`news_posts`.`postdate`) AS `postdate`,
                UNIX_TIMESTAMP(`news_posts`.`editdate`) AS `editdate`,
               `users`.`id` AS `user_id`,
               `users`.`username` AS `username`,
                COUNT(`news_comments`.`id`) AS `comments`
        FROM `news_posts`
          LEFT JOIN `users`
            ON `news_posts`.`user_id` = `users`.`id`
          LEFT JOIN `news_comments`
            ON `news_posts`.`id` = `news_comments`.`news_id`
            GROUP BY `news_posts`.`id`
        ORDER BY `news_posts`.`postdate` ASC
        LIMIT 0, " . ($total % 5) . "
      ) `temporary`
      ORDER BY `temporary`.`postdate` DESC
    ");
    
    // And this sets $page to the number that would be the last valid page. This is so
    // that on the '<- Prev' button the user is not taken back to an invalid page.
    $page = ceil($total / 5);
    
    if($query->num_rows == 0)
    // If there's still no rows, then there must just be no posts in the database. We display the
    // following in that case.
    {
      echo "      <div class=\"article\" id=\"top\">\n" .
           "\n<div class=\"title\">We're sorry...</div>\n".
           "<div class=\"words\">\n" .
           "<p>It appears that there are presently no posts in the database to display. Please check\n" .
           "back at a later date to see if any posts have been made.</p>\n" .
           "\n<p>Thank you.</p>\n" .
           "</div>\n" .
           "\n      </div>";
    
      return;
    }
  }
  
  $number = 0;
  while($row = $query->fetch_array(MYSQLI_ASSOC))
  {
    // This string stores the contents of the post. It will be echo'd after the script generates
    // the post. 
    $post = "";
    
    // Just include this comment to quickly identify the position of the post on the page.
    $post = "\n\n      <!-- Post #" . ($number + 1) . " -->\n\n";
    
    // Because of the formatting of the page, the first post gets a special tag in the title. Thus,
    // we must check whether this is the first post or not.
    
    if( ($number ++) == 0)
      $post .= "      <div class=\"article\" id=\"top\">\n";
    else
      $post .= "      <div class=\"article\">\n";
      
    // This is just a comment to more easily identify some stuff about the post, mostly for
    // debugging post.
    $post .= "\n<!--\n" .
             "  title: " . $row["title"] . "\n" .
             "  news_id: " . $row["news_id"] . "\n" .
             "  postdate: " . $row["postdate"] . "\n" .
             "  editdate: " . $row["editdate"] . "\n" .
             "-->\n";
    
    // This uses the urlify() function to produced a 'urlified' version of the title. It's mainly
    // so that the title can be roughly-identified in URLs, rather than for anything the system
    // really needs. This strips out everything but [A-z0-9 -], converts everyting to lowercase,
    // replaces the spaces with hyphens, and cuts it to 50 characters. It's stored as a variable
    // because it's used in two places.
    $url_title = urlify($row["title"]);
    
    // Now the title is printed. In this page, it links to the full-content post, located at the
    // post/ directory, along with the id and title of the post. At a later date, this may be
    // changed to the date of the post and an ID, but we'll see.
    $post .= "\n<div class=\"title\">\n" .
             "  <a href=\"news/?id=" . $row["news_id"] . "&title=" . $url_title . "\">\n" .
             "    " .$row["title"] . "\n" .
             "  </a>\n" .
             "</div>\n";
    
    // This is the actual content of the post, along with the div it is in.
    $post .= "\n<div class=\"words\">\n" . $row["content"] . "\n</div>\n";
    
    /* The following is the comments bar. In addition to linking the comments, it also includes the
       date posted, last time the post was edited, and in the future may include share links and
       the like. */
    
    // Beginning of the div.
    $post .= "\n<div class=\"commentsbar\">\n";
    
    // This is the comments count and link.
    $post .= "  <div class=\"data\"><a href=\"news/?id=" . $row["news_id"] . "&title=" . $url_title .
             "#comments\">";
    
    // A few slight variations, depending on the number of comments.
    if($row["comments"] == 0)
      // No comments.
      $post .= "No comments";
    else if($row["comments"] == 1)
      // A single comment.
      $post .= "One comment";
    else
    // Multiple comments.
      $post .= ucfirst(NumberToEnglish($row["comments"])) . " comments";
    
    // End the comments div.
    $post .= "</a></div>\n";
    
    // This generates div for the post and edit date. If the edit date and post date are within
    // five minutes of eachother, then it is not considered edited.
    
    $row["postdate"] = intval($row["postdate"]);
    $row["editdate"] = intval($row["editdate"]);
    
    // Start the div.
    $post .= "  <div class=\"data\">Posted by " . $row["username"] . " " . EnglishTimeDifference($row["postdate"]);
    
    // Check to see whether the post was considered edited.
    if(abs($row["editdate"] - $row["postdate"]) > (5 * 60))
      $post .= " (edited " . EnglishTimeDifference($row["editdate"]) . ")";
    
    // End the date div, and the commentsbar div.
    $post .= ".</div>\n" .
             "</div>\n";
    
    // Lastly, end the article div.
    $post .= "\n      </div>";
    
    // And output the post.
    echo $post;
  }
?>

    </div><?
  
  /*
   * This is used for navigation stuff. Basically, whether a "<- Prev" or "Next ->" link should be
   * displayed for moving around pages.
   */
  
  // This grabs the next set of data, to see if there's anything there.
  $count = Database::Query("
    SELECT NULL
    FROM `news_posts`
    ORDER BY `postdate` DESC
    LIMIT " . (5 * $page) . ", 5
  ")->num_rows;
  
  if( ($page != 1) || ($count != 0) )
  // If we are not on the first page OR there is stuff on the next page, then the navigation box
  // will be displayed.
  {
    // Display the nav box, with a comment.
    echo "\n\n    <!-- The navigation box. -->\n\n" . 
         "    <div class=\"navigation\">\n";
    
    if($count != 0)
      // If there is stuff on the next page, we can go on.
      echo "      <div class=\"next\"><a href=\"?page=" . ($page + 1) . 
           "\">Next Page &#x2192;</a></div>\n";
    
    if($page != 1)
      // If we aren't on the first page, we can go back. $page has been checked abovev, so we don't
      // need to do it again.
      echo "      <div class=\"prev\"><a href=\"?page=" . ($page - 1) .
           "\">&#x2190; Previous Page</a></div>\n";
    
    // Close the navigation box.
    echo "    </div>";
  }
  
?>
