<?

/*
 * The crypt class, used for handling registration and logins.
 */

// This uses the database class to access the MySQL database on the server.

include_once("database.php");

class Crypt
{

  /* Variables */
  
  // bcrypt uses a base-64 charset for the salt.
  private $_CHARSET = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  
  private $_SALT = NULL; // A randomly-generated salt.
  
  private $_SCHEME = "$2y"; // Use the 2y algorithm.
  private $_PASSES = "$14"; // 2^14 passes.
  
  public $error; // An error message, for determining if something failed.
  
  /* Functions */
  
  public function GenerateKey()
  // Generates a 22 character salt.
  {
    $this->_SALT = "$";
    for($i = 0; $i < 22; $i ++)
      $this->_SALT = $this->_SALT . $this->_CHARSET[mt_rand(0, 63)];
    return $this->_SALT;
  }
  
  public function Argument()
  // Returns the argument of the bcrypt function. This is $_SCHEME . $_PASSES . $_SALT. This is
  // used for registration only. Otherwise, use FromDatabase() to get the hash.
  {
    $this->GenerateKey();
    return $this->_SCHEME . $this->_PASSES . $this->_SALT;
  }
  
  public function FromDatabase($username)
  // This gets an already-existant hash from the database.
  {
    if(Database::Connect())
    {
      // Store the connection.
      $connection = Database::Connection();
      
      // Attempt to find the hashed password for the associated username.
      $data = $connection->query("SELECT `password` FROM `users` WHERE `username` = \"" . $username . "\"");
      if($data === FALSE)
        $this->error = "Query for user failed.";
      else if($data->num_rows === 0)
        $this->error = "\"" . $username . "\" not found in database.";
      else
        return $data->fetch_all(MYSQLI_NUM)[0][0];
    }
    else
    {
      $this->$error = Database::Connection()->$error;
    }
    
    return false;
  }
  
  public function Hash($plaintext, $salt)
  
  {
    return crypt($plaintext, $salt);
  }
  
  public function Verify($username, $plaintext)
  // Boolean function. True if the $plaintext is valid, and false if it's either invalid, or if
  // FromDatabase() returns a false value. See that function for why it would.
  {
    $ciphertext = $this->FromDatabase($username);
    
    if($ciphertext === FALSE)
      // FromDatabase() has failed, somehow.
      return FALSE;
    
    return $this->Hash($plaintext, $ciphertext) == $ciphertext;
  }

}