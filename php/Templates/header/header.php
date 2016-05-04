  <div class="header">
  
    <!-- Login stuff. -->
  
    <div class="right"> 
      <?
      
        if(Template::Load("header")->ShowLoginInfo())
        // If the login data (either the user's info or the links to login/register) should be
        // shown, then display them here.
        {
          if(isset($_SESSION["username"]))
          // If the user has an active PHP session, display the relevant information.
          {
            echo "<div class=\"element\">Logged in as  <b>" . $_SESSION["username"] . "</b></div>\n";
            echo "<div class=\"element\"><a href=\"" . rooturl() . "logout/?redirect=" . urlencode($_SERVER["REQUEST_URI"]) . "\">Logout</a></div>\n";
          }
          
          else
          // Oherwise, display the Login and Register selections.
          {
            echo "<div class=\"element\"><a href=\"https:" . rooturl() . "login/?redirect=" . urlencode($_SERVER["REQUEST_URI"]) . "\">Login</a></div>\n";
            echo "<div class=\"element\"><a href=\"https:" . rooturl() . "register/?redirect=" . urlencode($_SERVER["REQUEST_URI"]) . "\">Register</a></div>\n";
          }
        }
        else
        // Otherwise, just have a comment saying they aren't being shown.
        {
          echo "      <!-- Display nothing on this page. -->";
        }
      
      ?>
    </div>
    
    <!-- Navigation. -->
    
    <div class="left">
      <div class="element"><a href="<? echo rooturl(); ?>">Home</a></div>
      <div class="element">
        <form action="" method="get">
          <input type="text" placeholder="Search..." />
        </form>
      </div>
    </div>
    
  </div>
