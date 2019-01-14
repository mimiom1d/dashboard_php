<?php
//**core configuration
include_once "config/includes.php";
 
//**set page title
$page_title = "Register";
 
//**include login checker
include_once "login_checker.php";
 
//**include classes
include_once 'config/database.php';
include_once 'objects/user.php';
include_once "libs/php/utils.php";
include_once "libs/php/functions.php";
 
//**include page header HTML
include_once "templates/layout_header.php";
 
echo "<div class='col-md-12'>";
 
//**if form was posted
if($_POST){
 
    //**get database connection
    $database = new Database();
    $db = $database->getConnection();
 
    //**initialize objects
    $user = new User($db);
    $utils = new Utils();
 
    //**set user email to detect if it already exists
    $email_pre = safeinputs($_POST['email']);
 
    //**check if email already exists
    if($user->emailExists($email_pre)){
        echo "<div class='alert alert-danger'>";
            echo "The email you specified is already registered. Please try again or <a href='{$home_url}login'>login.</a>";
        echo "</div>";
    }
    //**  check all the validations and then try create()
    else{
        $firstname_pre = safeinputs($_POST['firstname']);
        $lastname_pre = safeinputs($_POST['lastname']);
        // $email_pre = safeinputs($_POST['email']); // ** Its already defined above
        $phone_pre = safeinputs($_POST['contact_number']);
        $address_pre = safeinputs($_POST['address']);
        $password_pre = safeinputs($_POST['password']);

        if(!$user->validName($firstname_pre) || !$user->validName($lastname_pre)){
            echo "<div class='alert alert-danger'>";
                echo "Please enter valid names using only Alphabets";
            echo "</div>";
        }else if(!filter_var($email_pre, FILTER_VALIDATE_EMAIL)){
            echo "<div class='alert alert-danger'>";
                echo "Please enter valid email address.";
            echo "</div>";
        }else if(!$user->validPhone($phone_pre)){
            echo "<div class='alert alert-danger'>";
                echo "Please enter valid phone Number. You cannot include alphabets";
            echo "</div>";
        }else if(strlen($password_pre) <8){
            echo "<div class='alert alert-danger'>";
                echo "Please enter more than 8 characters for password.";
            echo "</div>";
        }else{

            //**set values to object properties
            $user->firstname = $firstname_pre;
            $user->lastname = $lastname_pre;
            $user->email = $email_pre;
            $user->contact_number = $phone_pre;
            $user->address = $address_pre;
            $user->password = $password_pre;
            $user->access_level='Customer';
            $user->status=1;
            //**access code for email verification
            // $access_code=$utils->getToken();
            // $user->access_code=$access_code;
            echo $user->address;
            //** create user for testing; comment this after done with testing */
            //** create the user
            if($user->create()){
            
                echo "<div class='alert alert-info'>";
                    echo "Successfully registered. <a href='{$home_url}login'>Please login</a>.";
                echo "</div>";
            
                // empty posted values
                $_POST=array();
            
            }else{
                echo "<div class='alert alert-danger' role='alert'>Unable to register. Please try again.</div>";
            }
            //**create the user with email verification
            // if($user->create()){
            
            //     //**send confimation email
            //     $send_to_email=$_POST['email'];
            //     $body="Hi {$send_to_email}.<br /><br />";
            //     $body.="Please click the following link to verify your email and login: {$home_url}verify/?access_code={$access_code}";
            //     $subject="Verification Email";
            
            //     if($utils->sendEmailViaPhpMail($send_to_email, $subject, $body)){
            //         echo "<div class='alert alert-success'>
            //             Verification link was sent to your email. Click that link to login.
            //         </div>";
            //     }
            
            //     else{
            //         echo "<div class='alert alert-danger'>
            //             User was created but unable to send verification email. Please contact admin.
            //         </div>";
            //     }
            
            //     //**empty posted values
            //     $_POST=array();
            
            // }else{
            //     echo "<div class='alert alert-danger' role='alert'>Unable to register. Please try again.</div>";
            // }
        }
    }
}
?>
<form action='register.php' method='post' id='register'>
 
    <table class='table table-responsive'>
 
        <tr>
            <td class='width-30-percent'>Firstname</td>
            <td><input type='text' name='firstname' class='form-control' required value="<?php echo isset($_POST['firstname']) ? safeinputs($_POST['firstname']) : "";  ?>" /></td>
        </tr>
 
        <tr>
            <td>Lastname</td>
            <td><input type='text' name='lastname' class='form-control' required value="<?php echo isset($_POST['lastname']) ? safeinputs($_POST['lastname']) : "";  ?>" /></td>
        </tr>
 
        <tr>
            <td>Email</td>
            <td><input type='email' name='email' class='form-control' required value="<?php echo isset($_POST['email']) ? safeinputs($_POST['email']) : "";  ?>" /></td>
        </tr>
        
        <tr>
            <td>Contact Number</td>
            <td><input type='text' name='contact_number' class='form-control' required value="<?php echo isset($_POST['contact_number']) ? safeinputs($_POST['contact_number']) : "";  ?>" /></td>
        </tr>
 
        <tr>
            <td>Address</td>
            <td><textarea name='address' class='form-control'><?php echo isset($_POST['address']) ? safeinputs($_POST['address']) : "";  ?></textarea></td>
        </tr>
 
 
        <tr>
            <td>Password</td>
            <td><input type='password' name='password' class='form-control' required id='passwordInput'></td>
        </tr>
 
        <tr>
            <td></td>
            <td>
                <button type="submit" class="btn btn-primary">
                    <span class="glyphicon glyphicon-plus"></span> Register
                </button>
            </td>
        </tr>
 
    </table>
</form>
<?php
 
echo "</div>";
 
//**include page footer HTML
include_once "templates/layout_footer.php";
?>