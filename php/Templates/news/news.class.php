<?

/* This is the template manager for the news.php class. It's main purpose is to manage the infopane
   elements. */

// Set this as the Template namespace.
namespace Template;

class news implements \Manager
{

  /* Variables */
  
  // The infopane templates that will be used.
  private $infopanes = array();
  
  /* Methods */
  
  public function Set($variable, $value)
  // This does nothing in this class.
  {
    return NULL;
  }
  
  public function PushPane($template)
  // This adds a new infopane to the $infopanes class.
  {
    $class = \Template::Load($template);
    if($class != NULL)
      return $this->infopanes[$template] = $class;
    
    return FALSE;
  }
  
  public function GetPanes()
  {
    return $this->infopanes;
  }
  
  public function Display()
  // Actually displays this template. The displaying of panes is handled inside the template file.
  {
    include_once("news.php");
  }

}