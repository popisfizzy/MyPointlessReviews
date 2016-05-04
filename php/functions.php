<?

/*
 * Used for functions that don't really belong in any other file.
 */

/* Forces a user to connect to the page via HTTPS instead of HTTP. */

function ForceHTTPS()
{
  if($_SERVER["HTTPS"] != "on")
  {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
  }
}

/* This returns the bottom part of a URL. For example, bottom_url("/foo/bar/a/b/") will return
   "/bar/a/b/". */

function bottom_url($url)
{
  return preg_replace("/(\/[A-z0-9_\-\.~\:\\?#\[\]@\!\$&'\(\)\*\+\,;\=]+)(\/)/", "", $url, 1);
}

/* This takes a UNIX timestamp and calculates how long ago it was posted (approximately). For
   example, it might post things like "ten minutes ago", "twelve hours ago", "four days ago",
   "four weeks ago", "nine months ago, etc. */

function EnglishTimeDifference($when, $now = NULL)
{
  // Generates two DateTime objects.
  $when = new DateTime("@$when") ;
  $now = new DateTime($now);
  
  // The interval between them.
  $diff = $when->diff($now);
  
  // Just a quick anonymous inner function for appending 's'.
  $s = function ($t) { return ($t == 1 ? "" : "s"); };
  
  if($diff->y != 0)
  // First, the years.
  {
    return NumberToEnglish($diff->y) . " year" . $s($diff->y) . " ago";
  }
  
  if($diff->m != 0)
  // Months.
  {
    return NumberToEnglish($diff->m) . " month" . $s($diff->m) . " ago";
  }
  
  if($diff->d != 0)
  // Days. This also handles weeks.
  {
    if($diff->d >= 7)
    // If it has been greater than or equal to seven days, then it has been at least one week
    // since the timestamp.
    {
      $weeks = floor($diff->d / 7);
      return NumberToEnglish($weeks) . " week" . $s($weeks) . " ago";
    }
  
    else
      // If not, just in days.
      return NumberToEnglish($diff->d) . " day" . $s($diff->d) . " ago";
  }
  
  if($diff->h != 0)
  // Hours.
  {
    return NumberToEnglish($diff->h) . " hour" . $s($diff->s) . " ago";
  }
  
  if($diff->i != 0)
  // Minutes.
  {
    return NumberToEnglish($diff->i) . " minute" . $s($diff->i) . " ago";
  }
  
  if($diff->s != 0)
  // Two variations.
  {
    if($diff->s < 15)
    // If less than fifteen seconds.
    {
      return " just now";
    }
    
    else
    {
      return NumberToEnglish($diff->s) . " second" . $s($diff->s) . " ago";
    }
  }
  
  // If nothing else, then something weird happened.
  return " an unknown amount of time ago";
}

/* This function is used to convert a number into its English representation as words. Taken from
   http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/ and written by Karl Rixon.
   It takes an integer and converts it into its English form. */

function NumberToEnglish($number) {
     
     $hyphen      = '-';
     $conjunction = ' and ';
     $separator   = ', ';
     $negative    = 'negative ';
     $decimal     = ' point ';
     $dictionary  = array(
         0                   => 'zero',
         1                   => 'one',
         2                   => 'two',
         3                   => 'three',
         4                   => 'four',
         5                   => 'five',
         6                   => 'six',
         7                   => 'seven',
         8                   => 'eight',
         9                   => 'nine',
         10                  => 'ten',
         11                  => 'eleven',
         12                  => 'twelve',
         13                  => 'thirteen',
         14                  => 'fourteen',
         15                  => 'fifteen',
         16                  => 'sixteen',
         17                  => 'seventeen',
         18                  => 'eighteen',
         19                  => 'nineteen',
         20                  => 'twenty',
         30                  => 'thirty',
         40                  => 'fourty',
         50                  => 'fifty',
         60                  => 'sixty',
         70                  => 'seventy',
         80                  => 'eighty',
         90                  => 'ninety',
         100                 => 'hundred',
         1000                => 'thousand',
         1000000             => 'million',
         1000000000          => 'billion',
         1000000000000       => 'trillion',
         1000000000000000    => 'quadrillion',
         1000000000000000000 => 'quintillion'
     );
     
     if (!is_numeric($number)) {
         return false;
     }
     
     if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
         // overflow
         trigger_error(
             'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
             E_USER_WARNING
         );
         return false;
     }

     if ($number < 0) {
         return $negative . convert_number_to_words(abs($number));
     }
     
     $string = $fraction = null;
     
     if (strpos($number, '.') !== false) {
         list($number, $fraction) = explode('.', $number);
     }
     
     switch (true) {
         case $number < 21:
             $string = $dictionary[$number];
             break;
         case $number < 100:
             $tens   = ((int) ($number / 10)) * 10;
             $units  = $number % 10;
             $string = $dictionary[$tens];
             if ($units) {
                 $string .= $hyphen . $dictionary[$units];
             }
             break;
         case $number < 1000:
             $hundreds  = $number / 100;
             $remainder = $number % 100;
             $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
             if ($remainder) {
                 $string .= $conjunction . convert_number_to_words($remainder);
             }
             break;
         default:
             $baseUnit = pow(1000, floor(log($number, 1000)));
             $numBaseUnits = (int) ($number / $baseUnit);
             $remainder = $number % $baseUnit;
             $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
             if ($remainder) {
                 $string .= $remainder < 100 ? $conjunction : $separator;
                 $string .= convert_number_to_words($remainder);
             }
             break;
     }
     
     if (null !== $fraction && is_numeric($fraction)) {
         $string .= $decimal;
         $words = array();
         foreach (str_split((string) $fraction) as $number) {
             $words[] = $dictionary[$number];
         }
         $string .= implode(' ', $words);
     }
     
     return $string;
}

/* This reformats a string into a urlified form. This removes any characters except numbers,
   letters, hyphens, and spaces, makes the string lowercase, replaces spaces with hyphens, and
   cuts it off at 50 characters. */
 
function urlify($string, $cut = 50)
{
  $string = preg_replace("/[^A-z0-9 \\-]/", "", $string);
  $string = strtolower($string);
  $string = preg_replace("/ /", "-", $string);
  
  return substr($string, 0, $cut);
}

/*
  This truncates a string at the word closest to the width. Credit goes to the user Cd-MaN on
  stackoverflow.com. The relevant post can be found at the following link:
    http://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
*/

function tokenTruncate($string, $your_desired_width) {
  $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
  $parts_count = count($parts);

  $length = 0;
  $last_part = 0;
  for (; $last_part < $parts_count; ++$last_part) {
    $length += strlen($parts[$last_part]);
    if ($length > $your_desired_width) { break; }
  }

  return implode(array_slice($parts, 0, $last_part));
}