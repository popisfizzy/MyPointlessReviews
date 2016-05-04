/* Variables */

// This is the reCAPTCHA public key.
var reCAPTCHA_public_key = "6Le0qNUSAAAAAMHAM4hauBXjztonbBLqEqpiBZbP";

// This is how many times the user has entered the username and password incorrectly, respetively.
var username_fails = 1;
var password_fails = 1;

/* Functions */

function ProcessLogin()
{
  // Grab the username and password from the form.
  var username = $("#username").val();
  var password = $("#password").val();
  
  // The PHP script.
  var script = "local/login.php";
  
  // The XML HTTP Request object.
  var Request;
  if(window.XMLHttpRequest)
    // Code for non-IE 5 and 6 browsers.
    Request = new XMLHttpRequest;
  else
    // Code for IE 5 and 6.
    Request = new ActiveXObject("Microsoft.XMLHTTP");
  
  // The status box.
  var status = $("#ajaxstatus");
  
  // Blank the error message.
  $("#errormessage").html("&nbsp;");
  
  // This is used for the bottom loading display.
  var timeout;
  
  // Setup the callback function for when the readyState changes.
  Request.onreadystatechange = function ()
  {
    if(Request.readyState == 4)
    {
      clearTimeout(timeout);
      status.html("&nbsp;");
      
      /* setTimeout( function () {
        $("#errormessage").html(Request.responseText);
      }, 300); */
      
      /* alert(Request.responseText); */
      
      var response;
      
      if(Request.status == 200)
        response = Request.responseText;
      else if(Request.status == 404)
        response = "<login><error>\"" + script + "\" not found.</error><class>Server</class></login>";

      // The XML object.
      var XML;
      
      if(window.DOMParser)
      // Non-IE browsers.
      {
        XML = new window.DOMParser();
        XML = XML.parseFromString(response, "text/xml");
      }
      else
      // IE.
      {
        XML = new ActiveXObject("Microsoft.XMLDOM");
        XML.async = false;
        XML.loadXML(response);
      }
      
      // Now process the response.
      ProcessResponse(XML);
    }
  }
  
  // Send the request, via POST.
  Request.open("POST", script, true);
  Request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  
  if(!Recaptcha.get_challenge())
    // If there is no verification, send only this info.
    Request.send("username=" + username + "&password=" + password);
  else
    // Otherwise, we need to send some more.
    Request.send("username=" + username + "&password=" + password +
                 "&challenge=" + Recaptcha.get_challenge() + "&response=" + Recaptcha.get_response());
  
  // Start up the timer display.
  
  // The current position.
  var i = 0;
  timeout = setInterval( function ()
    {
      var off = "rgb(255, 255, 255);";
      var on = "rgb(233, 84, 84);";
      
      // Set the font-weight for this to bold.
      status.css("font-weight", "bold");
    
      // Generate the string.
      var string = "<span style=\"color: " + off + "\">";
      
      for(var j = 0; j < 5; j ++)
      {
        if(i == j)
          string = string + "<span style=\"color: " + on + "\">.</span>";
        else
          string = string + ".</span>";
      }
      string = string + "</span>";
      
      // Print the string.
      status.html(string);
      
      // Increment.
      i = (i + 1) % 5;
    }, 150);
}

function ProcessResponse(XML)
{
  if(XML.documentElement.getElementsByTagName("success").length != 0)
  // If the <success> element was found, the user is logged in.
  {
    // Hide the registration, forgotpassword, and forgotusername messages.
    $("#forgotusername").slideUp(100);
    $("#forgotpassword").slideUp(100);
    $("#registration").slideUp(100);
  
    // On success, hide the loginbox container.
    $("#loginbox").slideUp(700, function () {
      // Then display the successbox container.
      $("#successbox").slideDown(700);
    });
  }
  else
  {
    // Find the <error> tag so we can process what went wrong.
    var data = XML.documentElement.getElementsByTagName("error")[0];
    
    // This is the actual error. Store it.
    var error = "Error: " + data.childNodes[0].nodeValue;
    
    // The error number helps us decide what exactly to do, and what fields to clear.
    var error_number = 0;
    
    if(data.hasAttribute("no"))
      // Grab the string version of the number, and turn it into a number.
      error_number = Number(data.getAttribute("no"));
    
    if(data.hasAttribute("mysqlno"))
      // This is used for MySQL errors.
      error = error + " (error no. " + data.getAttribute("mysqlno") + ")";
    
    // Here we decide how to repond to the error, mostly by blanking certain fields.
    switch(error_number)
    {
      case 1.1: // No username posted.
      case 1.2: // No password posted.
      {
        // No fields need to be blanked, because this is a (likely) Javascript error.
        $("#button").focus(); // Send focus back to the button. It should be there already, though.
        break;
      }
      
      case 2.1: // No username provided.
      {
        // No fields need to be blanked, because they already are thanks to the user.
        $("#username").focus() // Focus on the username box.
        break;
      }
      case 2.2: // No password provided.
      {
        // Nothing needs to be blanked.
        $("#password").focus() // Focus on the password box.
        break;
      }
      case 2.3: // The user either failed to enter the verification, or they need to because
                // of a lockout of the username they entered.
      {
        if(!Recaptcha.get_challenge())
        // If it's not visible, display the reCAPTCHA box.
        {
          // Display the box, nicely.
          Recaptcha.create(reCAPTCHA_public_key, "recaptcha",
            {
              // Use the clean theme.
              theme: "blackglass",
              callback: function ()
              {
                $("#recaptcha").slideDown(250, Recaptcha.focus_response_field);
              }
            }
          );
        }
        else
        {
          // Otherwise, display a new message.
          Recaptcha.reload();
        }
        
        break;
      }
      
      case 3: // MySQL connection error.
      {
        // No need to blank the fields. The problem was with connecting to the database.
        $("#button").focus(); // Send focus to the button.
        break;
      }
      
      case 4.1: // Username not found.
      {    
        // The user's username was not found. Blank the box for them to retry.
        $("#username").val("");
        $("#username").focus(); // Send focus to the username box.
        
        if((username_fails ++) == 3)
          // If they have failed three times, shake the forgot-username box.
          $("#forgotusername").effect("shake", { times: 3, distance: 8}, 200);
          
        break;
      }
      case 4.2: // Password is invalid.
      {  
        if(Recaptcha.get_challenge())
          // If the reCAPTCHA is visible, reload it.
          Recaptcha.reload();
        
        // Blank the password box.
        $("#password").val("");
        
        setTimeout( function() {
          $("#password").focus(); // Send focus to the password box.
        }, 150); // The timeout is because reload() will draw focus away from #password if
                 // not given enough time.
        
        if((password_fails ++) == 3)
          // If they have failed three times, shake the forgot-password suggestion box.
          $("#forgotpassword").effect("shake", { times: 3, distance: 8 }, 200);
        
        break;
      }
      
      case 5.1: // Too many login attempts. The user must now enter verification.
      {
        if(!Recaptcha.get_challenge())
        // If it's not visible, display the reCAPTCHA box.
        {
          // Display the box, nicely.
          Recaptcha.create(reCAPTCHA_public_key, "recaptcha",
            {
              // Use the clean theme.
              theme: "blackglass",
              callback: function ()
              {
                $("#recaptcha").slideDown(250);
              }
            }
          );
        }
        else
        {
          // Otherwise, display a new message.
          Recaptcha.reload();
        }
        
        $("#password").val("");
        setTimeout( function() {
          $("#password").focus(); // Send focus to the password box.
        }, 150); // The timeout is because reload() will draw focus away from #password if
                 // not given enough time.
        
        break;
      }
      case 5.2: // Too many login attempts. The user is now locked out.
      case 5.3:
      {
        // Disable the username, password, and button.
        $("#username").attr("disabled", "disabled");
        $("#password").attr("disabled", "disabled");
        $("#button").attr("disabled", "disabled");
        
        // Hide and destroy the reCAPTCHA, if it is present.
        $("#recaptcha").slideUp(250, Recaptcha.destroy);
        
        break;
      }
    
      case 6.1: // Verification not entered.
      {
        // Generate a new CAPTCHA and focus the user input on the field.
        Recaptcha.reload();
        Recaptcha.focus_response_field();
        break;
      }
    }
    
    // Now display the error.
    $("#errormessage").html(error);
    
  }
}