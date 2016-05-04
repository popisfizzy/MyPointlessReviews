<?

/* Used as the interface for template manager classes. */
  
interface Manager
{
  // This is used to print out the content for the templates. Most templates will include an
  // HTML-oriented PHP file, or a plain HTML file, for display. Others will simply generate
  // the page on the fly.
  public function Display();
  
  // Used to set template variables.
  public function Set($variable, $value);
}