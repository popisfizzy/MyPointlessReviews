    <!-- Unlock message box. -->
    
    <div class="unlock">
    
      <div class="message">
        <?
          /* Here, processing will be done for unlocking the account. */
        
          // Include the Database class, because we'll be doing some querying of the database.
          include_once("classes/database.php");
          
          // Connect to the database.
          Database::Connect();
          
          if(Database::Error())
          // Error in connectig.
          {
            echo "Error: " . Database::Connection()->error . " (" . Database::Connection()->errorno . ").";
            return;
          }
          
          // Sanitize our inputs.
          $id = Database::Escape($_GET["id"]);
          $verification = Database::Escape($_GET["verification"]);
          
          // And now attempt to verify the input.
          $query = Database::Query("
            SELECT `verification`, `username`
            FROM `users`
            WHERE `id` = \"$id\"
            LIMIT 1
          ")->fetch_assoc();
          
          $username = $query["username"];
          
          if($query["verification"] == $verification)
          // If the verification in the database matches the one in the GET data for the same user,
          // then unlock the account and reset the associated data. Also log the user in.
          {
            Database::Query("
              UPDATE `users`
              SET `login_attempt` = NULL,
                  `failed_attempts` = 0,
                  `verification` = NULL
              WHERE `id` = \"$id\"
            ");
            
            // Set up the session cookie.
            session_set_cookie_params(time() + (60 * 60 * 24), "/", ".mypointlessramblings.com/reviews/");
            session_start();
            
            // Set the session variables.
            $_SESSION["username"] = $username;
            
            // And output the status.
            echo "$username unlocked and logged in. Please <a href=\"" . rooturl() . "\">return to the main page.</a>";
          }
          else
          {
            echo "Invalid verification: Verification string \"$verification\" does not match verification for user
                  \"$username\" (at id = $id) in database. If you are having login problems, please contact an
                  administrator. Please <a href=\"" . rooturl() . "\">return to the main page.</a>";
          }
        ?>
      </div>
    
    </div>