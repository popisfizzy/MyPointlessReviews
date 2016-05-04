<?

/* This is the maintanance class for the head template. It's only use is to name the page. */

namespace Template;

class head implements \Manager
{
  /* Variables */
  
  // The name of the page.
  private $page_name = NULL;
  
  // Includes.
  private $stylesheets = array();
  private $javascripts = array();
  
  /* Functions */
  
  function __construct()
  // Upon construction, include the defaults.
  {
    $this->Stylesheet("//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css");
    $this->Stylesheet("//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css");
    $this->Stylesheet("//fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic");
    $this->Stylesheet("//fonts.googleapis.com/css?family=Adamina");
    $this->Stylesheet(rooturl() . "css/style.css");
    
    $this->Javascript("//ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js");
    $this->Javascript("//ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js");
  }
  
  public function Set($variable, $value)
  // Implements the Set() method from the Manager interface.
  {
    if($variable == "page_name")
      return $this->vars[$variable] = $value;
  }
  
  public function SetPageName($value)
  {
    return $this->page_name = $value;
  }
  
  public function GetPageName()
  {
    return $this->page_name;
  }
  
  public function Stylesheet($url)
  // Used to add stylesheets.
  {
    array_push($this->stylesheets, $url);
  }
  
  public function Javascript($url)
  // Used to add Javascripts.
  {
    array_push($this->javascripts, $url);
  }
  
  public function Display()
  {
    /*
      Displayed as:
      > head.former.html
      >> head template (names page, stylesheets includes, javascript includes).
    */
    
    // Include the first part.
    include("templates/head/head.former.html");
    
    // Echo the title.
    echo ($this->GetPageName() ? ( " - " . $this->GetPageName() ) : "") . "</title>\n";
    
    // Now display the includes stuff, starting off with javascripts.
    
    echo "\n  <!--\n      Javascripts\n    -->\n\n";
    
    for($i = 0; $i < count($this->javascripts); $i ++)
      echo "  <script type=\"text/javascript\" src=\"" . $this->javascripts[$i] . "\"></script>\n";
      
    if(count($this->javascripts) == 0)
      echo "  <!-- None? -->\n";
    
    echo "\n  <!--\n      Stylesheets\n    -->\n\n";
    
    for($i = 0; $i < count($this->stylesheets); $i ++)
      echo "  <link href=\"" . $this->stylesheets[$i] . "\" type=\"text/css\" rel=\"stylesheet\" />\n";
      
    if(count($this->stylesheets) == 0)
      echo "  <!-- None? -->\n";
    
  }
}