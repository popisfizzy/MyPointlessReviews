<?

/* This is the maintanence class for the standard template. Its primary use is for the page name
   and the body content template. */

// All template managers should be using the Template namespace.
namespace Template;

class standard implements \Manager
// The class manager for the standard template.
{
  /* Variables */
  
  // The name of the page.
  private $page_name = NULL; // The name of the page.
  
  // The templates that will be included in the page. Several of these will populated as soon
  // as the manager is construcated. When iterating through the array, the array is firsted
  // iterated from 1 to +infinity, and then from -infinity to 1. For example, an array of the
  // form <-3, -2, -1, 1, 2, 3, 4> will be iterated as <1, 2, 3, 4, -3, -2, -1>.
  // private $includes = array();
  
  private $content_template = NULL;
  
  /* Functions */
  
  function __construct()
  // When the class is instantiated, generate the first, basic classes..
  {
    \Template::Load("head");
    \Template::Load("header");
    \Template::Load("footer");
  }
  
  public function Set($variable, $value)
  // Implements the Set() method from the Manager interface.
  {
    if($variable == "page_name")
      return $this->vars[$variable] = $value;
    
    return FALSE;
  }
  
  public function SetPageName($value)
  // A more direct way that Set("page_name", $value);
  {
    return $this->page_name = $value;
  }
  
  public function GetPageName()
  {
    return $this->page_name;
  }
  
  public function SetContentTemplate($template)
  // Used to set the body content template.
  {
    return $this->content_template = \Template::Load($template);
  }
  
  public function GetContentTemplate()
  // Returns the object, not the name.
  {
    return $this->content_template;
  }
  
  /* private function Order()
  // Generates the iteration verseion of the $includes array.
  {
    // The ordered version of the array.
    $ordered = array();
    
    // Get the first set.
    for($i = 1; array_key_exists($this->includes[$i]); $i ++)
      array_push($ordered, $this->includes[$i]);
      
    $j = -1; // This is the 'last' number. The largest negative number.
    while(array_key_exists($this->includes[$j]))
      $j --;
    
    // And now get the last ones.
    for($j; array_key_exists($this->includes[$j]); $j ++)
      array_push($ordered, $this->includes[$j]);
    
    return $ordered;
  } */
  
  public function Display()
  // Implements the Display() method from the Manager interface. This is used to actually 'Print'
  // the template.
  {
    /*
      The page is displayed as follows:
        > standard.prehead.html
        >> head template.
        > standard.preheader.html
        >> header template.
        > standard.precontent.html
        >> Body content template (user-defined)
        > standard.prefooter.html
        >> footer template.
        > standard.postfooter.html
    */
    
    // Include the pre-head HTML.
    include("templates/standard/standard.prehead.html");
    
    // Get the head template, set its name, and Display it.
    $head = \Template::Load("head");
    $head->SetPageName($this->GetPageName());
    $head->Display();
    
    // Include the pre-header HTML.
    include("templates/standard/standard.preheader.html");
    
    // Display the header template.
    \Template::Load("header")->Display();
    
    // Include the pre-content HTML.
    include("templates/standard/standard.precontent.html");
    
    // Display the body content.
    if($this->GetContentTemplate())
      $this->GetContentTemplate()->Display();
    
    // Include the pre-footer HTML.
    include("templates/standard/standard.prefooter.html");
    
    // Display the footer template.
    \Template::Load("footer")->Display();
    
    // And, lastly, include the post-footer HTML.
    include("templates/standard/standard.postfooter.html");
  }
}