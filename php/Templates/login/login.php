    <!-- Login box -->
    
    <div class="login">
    
      <!-- The container class is used for positioning. -->
      
      <div class="container" id="loginbox">
        <div class="message" id="errormessage">&nbsp;</div>
      
        <input type="text" placeholder="Username..." class="input" id="username" />
        <input type="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;" class="input" id="password" />
        <div class="recaptcha" id="recaptcha"></div>
        <input type="submit" value="Submit" class="button" onclick="ProcessLogin()" id="button" />
        
        <div class="message" id="ajaxstatus">&nbsp;</div>
      </div>
      
      <div class="container" id="successbox">
        <div class="message" id="loggedin">
          You have successfully logged in.<br />
          <a href="<?
            echo rooturl() . bottom_url($_GET["redirect"]);;
          ?>">Click here</a> to go back.
        </div>
      </div>
      
      <div class="message" id="forgotusername">Did you forget your username? <a href="<? echo rooturl() . "login/username/"; ?>">Click here.</a></div>
      <div class="message" id="forgotpassword">Did you forget your password? <a href="<? echo rooturl() . "login/password/"; ?>">Click here.</a></div>
      <div class="message" id="registration">Not registered? <a href="<? echo rooturl() . "register/"; ?>">Click here.</a></div>
      
    </div>