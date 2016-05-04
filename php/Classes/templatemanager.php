<?

// Include the Manager interface.
include_once("classes/manager.interface.php");

/* Used for templates that do not have a class. */
  
class TemplateManager implements Manager
{
  /* Variables */
  
  // The template file. Either an HTML file or an HTML-oriented PHP file, presumably.
  public $template; 
  
  /* Functions */
  
  function __construct($template)
  {
    if(file_exists(rootdir() . "php/templates/$template/$template.php"))
      $this->template = "templates/$template/$template.php";
    else if(file_exists(rootdir() . "php/templates/$template/$template.html"))
      $this->template = "templates/$template/$template.html";
  }
  
  public function Display()
  {
    // Includes the file for display.
    include($this->template);
  }
  
  public function Set($variable, $value)
  // Does nothing in this class.
  {
    return NULL;
  }
}