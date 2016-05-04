<?

/* This is the template manager for the header template. It mainly exists just for a few
   settings. */

// As with all template managers, this is in the Template namespace.
namespace Template;

class header implements \Manager
{
  /* Variables */
  
  private $show_login_info = TRUE;
  
  /* Functions */
  
  public function Set($variable, $value)
  // Implement this function, as the interface requires.
  {
    if($variable == "show_login_info")
      return $this->vars[$variable] = $value;
  }
  
  public function SetShowLoginInfo($setting)
  {
    return $this->show_login_info = $setting;
  }
  
  public function ShowLoginInfo()
  {
    return $this->show_login_info;
  }
  
  public function EnableShowLoginInfo()
  {
    return $this->SetShowLoginInfo(TRUE);
  }
  
  public function DisableShowLoginInfo()
  {
    return $this->SetShowLoginInfo(FALSE);
  }
  
  public function Display()
  // The Display method from the interface.
  {
    // Just include the template PHP file.
    include("templates/header/header.php");
  }
}