<?

/* This is used to verify the user's login information. The script itself is queried via a POST-
   oriented AJAX script. */

// Include the core script.
include_once("../../php/core.php");

// And also the Crypt and Database classes.

include_once("classes/database.php");
include_once("classes/crypt.php");

/*
 * These are the errors present in the script.
 */

$_ERRORS = array(

  "1.1" => array(
    "Username not found in POST data.",
    "POST"
    ),
  "1.2" => array(
    "Password not found in POST data.",
    "POST"
    ),
  
  "2.1" => array(
    "No username submitted.",
    "user"
  ),
  "2.2" => array(
    "No password submitted.",
    "user"
  ),
  "2.3" => array(
    "Please enter verification.",
    "user"
  ),
  
  "3" => array(
    "MySQL Error: ",
    "MySQL"
  ),
  
  "4.1" => array(
    "Username not found in database.",
    "user"
  ),
  "4.2" => array(
    "Incorrect password.",
    "user"
  ),
  
  "5.1" => array(
    "Too many login attempts. Please enter verification to continue.",
    "user"
  ),
  "5.2" => array(
    "Too many login attempts. Wait twelve hours or reset via email.",
    "user"
  ),
  "5.3" => array(
    "Invalid password. This username has been locked out. Please wait twelve hours or reset via email.",
    "user"
  ),
  
  "6.1" => array(
    "Validation incorrect. Please re-attempt.",
    "user"
  ),

);

/* This is used to generate the response message. */

function Response($float, $mysql = NULL)
// $mysql is only used for error 3.
{
  global $_ERRORS;

  if($float == 0)
    // This is the only success value.
    return "<login>\n" . 
           "  <success />\n" .
           "</login>";
  
  else if($float == 3)
  // When there is a MySQL connection error.
  {
    $float = strval($float);
    return "<login>\n" .
           "  <error no=\"$float\" mysqlno=\"" . $_ERRORS[$float][0] . $mysql->errorno . "\">" . $mysql->error . "</error>\n" .
           "  <class>" . $_ERRORS[$float][1] . "</class>\n" .
           "</login>";
  }
  
  else
  // All other errors.
  {
    $float = strval($float);
    return "<login>\n" .
           "  <error no=\"$float\">" . $_ERRORS[$float][0] . "</error>\n" .
           "  <class>" . $_ERRORS[$float][1] . "</class>\n" .
           "</login>";
  }
}

/*
 * Now begin input processing.
 */

// Firstly, make sure that username and password were provided in the POST data.

if(!isset($_POST["username"]))
{
  echo Response(1.1);
  return;
}

if(!isset($_POST["password"]))
{
  echo Response(1.2);
  return;
}

// And next make sure that the username and password are of non-zero length, i.e. that the user
// actually entered something for each.

if(strlen($_POST["username"]) == 0)
{
  echo Response(2.1);
  return;
}

if(strlen($_POST["password"]) == 0)
{
  echo Response(2.2);
  return;
}
// Attempt to connect to the database.

Database::Connect();

if(Database::Error())
// If there was a database error, indicate this in the response.
{
  echo Response(3, Database::Connection());
  return;
}

// Store the username and password in global variables, to simplify accessing them. At the same
// time, sanitize them both.

$username = Database::Escape($_POST["username"]);
$password = Database::Escape($_POST["password"]);

/*
 * Now begin database query stuff.
 */

// First, make sure the user is present in the database.

Database::Query("
  SELECT `username`
  FROM `users`
  WHERE `username` = \"$username\"
  LIMIT 1
");

if(Database::Query()->num_rows == 0)
// If no rows were found, then indicate the username is not valid.
{
  echo Response(4.1);
  return;
}
else
// If it was found, the username is present. Store the canonical form of the username in the
// $username variable. This is because the user may not enter their username in the canonical form.
{
  $username = Database::Query()->fetch_assoc()["username"];
}

// In the case that the user has been locked out, this query will check whether twelve hours have
// passed. If so, the user is unlocked so that they may try and login again. 

Database::Query("
  UPDATE `users`
  SET `login_attempt` = NULL,
      `failed_attempts` = 0,
      `verification` = NULL
  WHERE `username` = \"$username\"
        AND DATE_SUB(NOW(), INTERVAL 12 HOUR) > `login_attempt`
");

// Now that the user's lock-out has possibly been reset, we'll check on the number of login
// attempts. If there are eleven or more, the user is completely locked out. If there are five or
// more, the user must enter a reCAPTCHA verification.

$attempts = Database::Query("
  SELECT `failed_attempts`
  FROM `users`
  WHERE `username` = \"$username\"
  LIMIT 1
")->fetch_assoc()["failed_attempts"];

if($attempts > 11)
// Eleven attempts mean the user must either wait or respond to the lock-out email. Send them an
// error informing them of this.
{
  echo Response(5.2);
  return;
}
else if($attempts > 5)
// The user must enter verification to continue.
{
  if(!isset($_POST["response"]) || !$_POST["response"])
  // If the response is not present, inform the user that is must be submitted.
  {
    echo Response(2.3);
    return;
  }
  
  // If it is present, verify that the response is valid. This makes use of the reCAPTCHA static
  // class, so it must be included.
  include_once("classes/recaptcha.php");
  
  if(reCAPTCHA::Query($_POST["challenge"], $_POST["response"]) == FALSE)
  // If this is false, then we have an error of some sort.
  {
    switch(reCAPTCHA::Error())
    // See the errors.
    {
      case "incorrect-captcha-sol":
      // The user provided the incorrect response.
      {
        echo Response(6.1);
        return;
      }
      default:
      {
        // Handle these later. They shouldn't happen.
        return;
      }
    }
  }
}

// We've gotten far enough that we can attempt to verify the password. This uses the Creypt class,
// so produce and instance of it.

$crypt = new Crypt;

// Now see if the user entered the correct password.

if($crypt->Verify($username, $password))
// The user's password has been verified. Log them in.
{
  // Set up the session cookie.
  session_set_cookie_params(time() + (60 * 60 * 24), "/", ".mypointlessramblings.com/reviews/");
  session_start();
  
  // And set the session variable.
  $_SESSION["username"] = $username;
  
  // Since a login has occurred, the user's failed attempts and initial failed attempt are nulled
  // out.
  Database::Query("
    UPDATE `users`
    SET `login_attempts` = NULL,
        `failed_attempts = 0
    WHERE `username` = \"$username\"
  ");
  
  // And send a successful XML message.
  echo Response(0);
  return;
}
else
// The user entered an invalid password. This causes a number of things to happen.
{
  // If login_attempt is null, then set it to the current date. Until the user logs successfully
  // logs in, this indicates the twelve-hour count period.
  Database::Query("
    UPDATE `users`
    SET `login_attempt` = CASE
      WHEN `login_attempt` IS NULL
        THEN NOW()
      ELSE
        `login_attempt`
      END,
      `failed_attempts` = `failed_attempts` + 1
    WHERE `username` = \"$username\"
  ");
  
  if($attempts >= 11)
  // The user has reached the lock-out number. Start the process of locking them out.
  {
    // First, we start the process of emailing the user. To do this, we generate a 60-character,
    // base-64 verification number.
    
    // Base-64 character string.
    $_BASE = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxzy+/";

    // The verification code.
    $verification = "";
    for($i = 0; $i < 60; $i ++)
      $verification .= $_BASE[mt_rand(0, 63)];
   
    // Insert this value into the database, and also set the lockout time to teh current time.
    Database::Query("
      UPDATE `users`
      SET `verification` = \"$verification\",
          `login_attempt` = NOW()
      WHERE `username` = \"$username\"
    ");
    
    // Now that it's been stored in the database, encode the verification.
    $verification = urlencode($verification);
    
    // And get the user's ID.
    $id = Database::Query("
      SELECT `id`
      FROM `users`
      WHERE `username` = \"$username\"
      LIMIT 1
    ")->fetch_assoc()["id"];
    
    // Now we will start on the email. This is done, in part, by the email static class, so include
    // the file it's in.
    include_once("classes/email.php");
    
    email::Sender("login-bot"); // From the login-bot.
    email::Reciever($username);
    email::Subject("\"$username\" has been locked out.");
    email::Message("
<html>
  <head>
    <style type=\"text/css\">
      p {
        margin-above: 15px;
        font-size: normal;
      }
      
      p.text {
        text-indent: 20px;
      }
      
      p.postscript {
        font-size: small;
      }
    </style>
  </head>
  <body>
    <p>Dear MyPointlessReviews User:</p><br />
  
    <p class=\"text\">The username \"admin\" registered to this email on <a href=\"http://mypointlessramblings.com/reviews/\">
    MyPointlessReviews</a> has been locked out due to too many login attempts. You may either wait twelve hours,
    and the system will automatically unlock the username by itself, or you may
    <a href=\"http://mypointlessramblings.com/reviews/login/unlock/?id=$id&verification=$verification\">click here</a> to
    unlock it yourself.</p><br />

    </p>Thank you,<br />
    Login-Bot</p><br /><br />
    
    <p class=\"postscript\">P.S.: This email account is unmonitored, and messages recieved will not be read.</p>
  </body>
</html>
");

    // Now send the email and reset the data.
    email::Send();
    email::Reset();
    
    // Lastly, return the error.
    echo Response(5.3);
    return;
  }
  else if( ($attempts >= 5) && !isset($_POST["challenge"]))
  // The user must now enter reCAPTCHA verification.
  {
    echo Response(5.1);
    return;
  }
  
  // Otherwise, this is just a simple invalid password error.
  echo Response(4.2);
  return;
}