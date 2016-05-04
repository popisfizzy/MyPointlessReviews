<?

/*

  This class is a sort-of class manager which is used to load up template classes. Templates are
  stored in the templates/ folder. A specific template Foo is stored under templates/Foo, and the
  Foo manager class will be stored in templates/Foo.class.php. This class should implement the
  Template::Manager interface shown below, and be under the Template\ namespace.
  
  If the manager class does not exist, one will be created using the Template::GenericManager class
  below, which implements Template::Manager. It is expected a HTML-oriented php file of the form
  templates/Foo.php will exist.

*/

// Include the TemplateManager class.
include_once("classes/templatemanager.php");

class Template
{
  /* Variables */
  
  // Already-loaded templates.
  private static $loaded = array();

  /* Functions */
  
  public static function Load($template)
  // This loads the template class. If there is a $template.class.php class, a new instance
  // of that class will be used. Otherwise, a new instance of the TemplateManager class will
  // be used.
  {
    if(array_key_exists($template, self::$loaded))
    {
      // If the template has been loaded, return it.
      return self::$loaded[$template];
    }
  
    if(is_dir(rootdir() . "php/templates/$template/"))
    // Make sure the template actually exists.
    {
      if(file_exists(rootdir() . "php/templates/$template/$template.class.php"))
      // If the class exists, create a new instance and return it.
      {
        include_once("templates/$template/$template.class.php");
        
        // PHP wonkiness fun.
        $class = "Template\\" . $template;
        self::$loaded[$template] = new $class;
        return self::$loaded[$template];
      }
      
      // Otherwise, we'll use the TemplateManager class. This requires that a file in the form
      // of templates/$template.php or templates/$template.html exists.
      self::$loaded[$template] = new TemplateManager($template);
      return self::$loaded[$template];
    }
    
    // If the directory does not exist, return false. We can'd do anything from here.
    return FALSE;
  }
}