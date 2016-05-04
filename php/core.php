<?

/*
 * Core PHP file for MyPointlessReviews. This primarily stores some basic functions and includes
 * important files.
 */

/* Returns the 'root' URL for the review site. */

function rooturl()
{
  return "//mypointlessramblings.com/reviews/";
}

/* Returns the 'root' directory for the review site. */

function rootdir()
{
  return "/home/fizzy/enorisian-empire.com/main/reviews/";
}

/* Returns the 'root' domains for the website. */

function rootdomain()
{
  // Note the lack of www.
  return "mypointlessramblings.com/";
}

/* Included files. */

set_include_path(rootdir() . "php/");

// Template manager.
include_once("classes/template.php");

// Manages database connections.
include_once("classes/database.php");

/* This file is used to set up defaults across the site. */

include_once("settings.php");

/* This file is used to define a number of functions that don't belong in another file. */

include_once("functions.php");

/* It's stupid, but session_start() has to be included in any page that uses session, which will
   be most of them. */

session_start();

?>