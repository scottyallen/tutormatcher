<?
include('functions.php');

session_start();

global $page;

    $page = $_REQUEST["page"];

if($_REQUEST["action"]=='create_student'){ $message = create_student(); }
if($_REQUEST["action"]=='create_tutor'){ $message = create_tutor(); }
if($_REQUEST["action"]=='create_tutor_request'){ $message = create_tutor_request(); }
if($_REQUEST["action"]=='edit_request'){ $message = edit_request(); }
if($_REQUEST["action"]=='cancel_request'){ $message = cancel_request(); }
if($_REQUEST["action"]=='login'){ $message = login(); }
if($_REQUEST["action"]=='logout'){ $message = logout(); }
if($_REQUEST["action"]=='confirm'){ $message = confirm(); }

include('header.php');
if($page == ''){
    ?>
    <div class="main">
    <?
    include('main.php');
    ?>
    </div>
    <?
} else if ($page != 'main' &&
           $page != 'new_student' &&
           $page != 'new_tutor' &&
           $page != 'confirm_email' &&
           $_SESSION['logged_in'] != true){
    show_error("You aren't logged in yet");
} else {
    ?>
    <div class="main">
    <?
    include($page.'.php');
    ?>
    </div>
    <?
}
?>
</body>
</html>
