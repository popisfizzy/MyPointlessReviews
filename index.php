<?

/* The main page, at http://mypointlessramblings.com/review/ To use this page, we load up the
   default template with the news content template. */

// Include the standard core file.
include_once("php/core.php");

// And setup the template.

Template::Load("standard")->SetPageName("Home");
Template::Load("standard")->SetContentTemplate("news");

// Add the panes.
Template::Load("news")->PushPane("reviewpane");
Template::Load("news")->PushPane("newspane");

// And display the page.
Template::Load("standard")->Display();