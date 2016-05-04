<?

/* This page is used for a user to unlock their account after it has been locked due to too many
   login attempts. */

include_once("../../php/core.php");

// If the user is already logged in, or the $_GET["id"] and $_GET["verification"] values are not
// set, redirect the user either to where they came from, or the main page.

if(isset($_SESSION["username"]) || (!isset($_GET["id"]) || !isset($_GET["verification"])))
{
  // By default, they go back to reviews/.
  $redirect = "";
  
  if(isset($_SERVER["HTTP_REFERER"]) && (preg_match("/^(http(s)?\:\/\/)?(www.)?mypointlessramblings.com\/reviews(\/)?/i", $_SERVER["HTTP_REFERER"]) != FALSE) )
    // If HTTP_REFERER is set, and points to another page in reviews/, then go to it instead.
    $redirect = preg_replace("/(^(http(s)?\:\/\/)?(www.)?mypointlessramblings.com\/reviews(\/)?)/i", "", $_SERVER["HTTP_REFERER"]);
  
  // Now pass the header and redirect.
  header("Location: " . rooturl() . $redirect);
  exit();
}

// If all is well and good, then display the page.

Template::Load("standard")->SetPageName("Unlock Account");
Template::Load("header")->DisableShowLoginInfo();
Template::Load("standard")->SetContentTemplate("unlockaccount");
Template::Load("head")->Stylesheet("../../login/unlock/local/style.css");
Template::Load("standard")->Display();