<?

/* This is used to streamline emailing. It's a simple static class. */

include_once("classes/database.php");

class email
{
  /* Variables */

  private static $sender;
  private static $reciever;
  private static $subject;
  private static $body;
  
  /* Methods */
  
  public static function Sender($local)
  // This should only be the local part (the part preceding @foo.com).
  {
    self::$sender = $local . "@" . substr(rootdomain(), 0, -1) . "(MyPointlessReviews)";
  }
  
  public static function Reciever($username)
  // This will use the Database class to look up the user's email, so the email address itself
  // should not be passed.
  {
    $email = Database::Query("
      SELECT `email`
      FROM `users`
      WHERE `username` = \"$username\"
      LIMIT 1
    ")->fetch_assoc()["email"];
    
    self::$reciever = "$username <$email>";
  }
  
  public static function Subject($subject)
  {
    self::$subject = $subject;
  }
  
  public static function Message($message)
  // The message. It is automatically wrapped at 70 characters.
  {
    self::$body = wordwrap($message, 70, "\n");
  }
  
  private static function Header()
  // Generates the email's header.
  {
    return "From: " . self::$sender . "\r\n" .
           "MIME-Version: 1.0\r\n" .
           "Content-type: text/html; charset=iso-8859-1\r\n" .
           "X-Mailer: PHP/" . phpversion();
  }
  
  public static function Send()
  // Sends the email.
  {
    mail(self::$reciever, self::$subject, self::$body, self::Header());
  }
  
  public static function Reset()
  // Resets the class's static properties.
  {
    self::$reciever = NULL;
    self::$sender = NULL;
    self::$subject = NULL;
    self::$body = NULL;
  }
}