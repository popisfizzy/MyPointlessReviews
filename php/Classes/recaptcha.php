<?

/* This class is used for managing reCAPTCHA verification queries via AJAX. */

class reCAPTCHA
{
  /* Variables */
  
  // The private and public keys.
  const public_key = "reCAPTCHA_PUBLICKEY";
  const private_key = "reCAPTCHA_PRIVATEKEY";
  
  // The query URL.
  const url = "https://www.google.com/recaptcha/api/verify";
  
  // The error.
  private static $error = NULL;
  
  /* Functions */
  
  public static function Query($challenge, $response)
  // This queries Google's remote server to verify the user's reCAPTCHA input. If it
  // returns true, then the user's entry was validated. If it returns false, then
  // something went wrong (likely the user entered something wrong) and Error() should
  // be examined for further information.
  {
    // The user's IP.
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // The remote query.
    $query = "privatekey=" . self::private_key .
             "&remoteip=$ip" .
             "&challenge=$challenge" .
             "&response=$response";
    
    // The cURL object.
    $curl = curl_init();
    
    // Set the cURL object's settings.
    curl_setopt($curl, CURLOPT_URL, self::url);
    curl_setopt($curl, CURLOPT_POST, 4); // Four fields.
    curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    
    // Now execute the session and grab the data.
    $reply = curl_exec($curl);
    
    // And close the session.
    curl_close($curl);
    
    // Next, process the reply.
    $reply = explode("\n", $reply);
    
    if($reply[0] == "true")
      return true;
    else
    {
      self::$error = $reply[1];
      return false;
    }
  }
  
  public static function Error()
  // The error from the remote server, if there was any.
  {
    return self::$error;
  }
}