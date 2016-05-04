<?

/* This is the login page for the site. It uses the login template. */

// Include the standard core file.
include_once("../php/core.php");

// Don't allow the user to view the login page if they're already logged in.

if(isset($_SESSION["username"]))
{
  // Do a little modification to the redirect url, to get rid of the top-level, usually reviews/.
  $suburl = bottom_url($_GET["redirect"]);
  
  // Now pass the header and redirect.
  header("Location: " . rooturl() . $suburl);
  exit();
}

// Make sure the user is using a secure connection.
ForceHTTPS();

// Now load the page template stuff.

Template::Load("standard")->SetPageName("Login");
Template::Load("header")->DisableShowLoginInfo();
Template::Load("standard")->SetContentTemplate("login");
Template::Load("head")->Stylesheet(rooturl() . "login/local/login.css"); // Login CSS style.
Template::Load("head")->Javascript(rooturl() . "login/local/LoginAJAX.js"); // Login AJAX script.
Template::Load("head")->Javascript("//www.google.com/recaptcha/api/js/recaptcha_ajax.js"); // reCAPTCHA script. */
Template::Load("standard")->Display();