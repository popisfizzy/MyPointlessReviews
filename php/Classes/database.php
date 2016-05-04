<?

/* A wrapper for the mysqli class. */

class Database
{
  /* Variables */
  
  // Login information.
  private static $_MYSQL_HOST     = "localhost";
  private static $_MYSQL_USERNAME = "MYUSERNAME";
  private static $_MYSQL_PASSWORD = "MYPASSWORD";
  private static $_MYSQL_DATABASE = "MYUSERNAME_MYDATABASE";
  
  // mysqli connection. This should be an object.
  private static $connection = NULL;
  
  // Stores the results of the last query response.
  private static $last_query;
  
  /* Functions */
  
  public static function Connect()
  // Attempt to connect to the database. If a connection is already present, this will return true.
  {
    if(self::Connection())
      return true;
  
    self::$connection = new mysqli(self::$_MYSQL_HOST, self::$_MYSQL_USERNAME, self::$_MYSQL_PASSWORD, self::$_MYSQL_DATABASE);
    
    if(self::$connection->errno)
      return false;
    else
      return true;
  }
  
  public static function Error()
  // If there was a connection error.
  {
    if(self::Connect() && self::Connection()->errorno)
      return true;
    else
      return false;
  }
  
  public static function Query($query = NULL)
  {
    if(!self::Connection())
      return false;
    
    if($query === NULL)
      return self::$last_query;
  
    else
      return self::$last_query = self::Connection()->query($query);
  }
  
  public static function Close()
  // Closes the connection.
  {
    if(self::Connection())
    {
      self::Connection()->close();
      self::$connection = NULL;
    }
  }
  
  public static function Refresh()
  // This closes the connection and attempts to open a new one.
  {
    if(self::Connection())
      self::Close();
    
    self::Connection();
  }
  
  public static function Connection()
  // Returns the mysqli connection object.
  {
    return self::$connection;
  }
  
  public static function Escape($string)
  // This is used for sanitizing user input.
  {
    if(self::Connection())
      return self::Connection()->real_escape_string($string);
  }
}