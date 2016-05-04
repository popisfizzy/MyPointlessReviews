        <div class="box">
          <div class="title"><a href="?page=1">Recent Posts</a></div>
          
          <!-- The Recent Posts box selects the three most recent posts and provides truncated
               links to them. -->

<?

// Connect to the database.

Database::Connect();

// And grab the query.

$query = Database::Query("
  SELECT `title`, `id`
  FROM `news_posts`
  ORDER BY `postdate` DESC
  LIMIT 0, 3
");

if($query->num_rows == 0)
// If there have been no rows selected, then the database has no news posts.
{
  echo "          <div class=\"data\">There are no posts.</div>\n";
}

else
// Otherwise, print the titles, truncated at 25 characters.
{
  while($row = $query->fetch_array(MYSQLI_ASSOC))
  {
    $trunc = tokenTruncate($row["title"], 25);
    if($trunc != $row["title"])
      $trunc .= "...";
      
    // Gets the urlified version of the title.
    $url = urlify($row["title"]);
    
    echo "          <div class=\"data\"><a href=\"news/?id=" . $row["id"] .
         "&title=" . $url . "\">$trunc</a></div>\n";
  }
}

?>

        </div>
      
        <div class="box">
          <div class="title">Random Posts</div>
          
          <!-- The just grabs five random posts from the database. -->
          
<?

// First, we get the maximum ID value of the tables.

$max_id = strval(Database::Query("
  SELECT MAX(`id`) AS `max_id`
  FROM `news_posts`
  ORDER BY `id`
")->fetch_array(MYSQL_ASSOC)["max_id"]);

// Now that we have this, we keep going until we have three posts.

$total = 0;
$shown = array(); // The IDs of posts already shown, to prevent duplicates.
do
{
  // The post we'll be getting.
  $post = mt_rand(1, $max_id);
  
  if(in_array($post, $shown))
    // If the post has already been displayed, just start the next iteration of the loop.
    continue;
  
  // If it hasn't been displayed, push it into the array.
  array_push($shown, $post);
  
  // Query the database for this post.
  $query = Database::Query("
    SELECT `id`, `title`
    FROM `news_posts`
    WHERE `id` = $post
    ORDER BY `id` ASC
    LIMIT 1
  ");
  
  if($query->num_rows > 0)
  // Got something, so print it. We have to do some manipulation like we did with the most recent
  // posts (in the above section), though.
  {
    $query = $query->fetch_array(MYSQL_ASSOC);
  
    // Truncate the title.
    $trunc = tokenTruncate($query["title"], 25);
    if($trunc != $query["title"])
      $trunc .= "...";
    
    // URL-ify it.
    $url = urlify($query["title"]);
    
    // And print.
    echo "          <div class=\"data\"><a href=\"news/?id=" . $query["id"] .
         "&title=" . $url . "\">$trunc</a></div>\n";
    
    // Lastly, increment the total posted.
    $total ++;
  }

} while($total < ( (3 > $max_id) ? $max_id : 3) );

?>

        </div>