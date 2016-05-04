<?

/* This is a script-inclusion management class for CSS and Javascript files. */

class Includes
{

  /* Variable definitions */

  private static $CSS_includes = [];
  private static $Javascript_includes = [];
  
  /* Functions */
  
  public function __construct()
  /* Constructor */
  {
    return null;
  }
  
  public static function GetCSSIncludes()
  {
    return self::$CSS_includes;
  }
  
  public static function CSSAdd($url)
  {
    array_push(self::$CSS_includes, $url);
  }
  
  public static function GetJavascriptIncludes()
  {
    return self::$Javascript_includes;
  }
  
  public static function JavascriptAdd($url)
  {
    return array_push(self::$Javascript_includes, $url);
  }
  
  public static function Defaults()
  /* Automatically includes the default scripts. */
  {
    self::JavascriptAdd("//ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js");
    self::JavascriptAdD("//ajax.googleapis.com/ajax/libs/jqueryui/1.8.0/jquery-ui.min.js");

    self::CSSAdd("//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css");
    self::CSSAdd("//ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css");
    self::CSSAdd("//fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic");
    self::CSSAdd("//fonts.googleapis.com/css?family=Adamina");
    self::CSSAdd(rooturl() . "/css/style.css");
  }

}