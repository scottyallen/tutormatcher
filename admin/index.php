<?
include('../functions.php');

session_start();

global $page;

$page = $_REQUEST["page"];

if($_REQUEST["action"]=='admin_login'){ admin_login(); }
else if($_REQUEST["action"]=='search_students'){ search_students(); }
else if($_REQUEST["action"]=='search_requests'){ search_requests(); }
else if($_REQUEST["action"]=='search_matches'){ search_matches(); }
else if($_REQUEST["action"]=='search_tutors'){ search_tutors(); }
else if($_REQUEST["action"]=='edit_student'){ edit_student(); }
else if($_REQUEST["action"]=='edit_tutor'){ edit_tutor(); }
else if($_REQUEST["action"]=='edit_request'){
    edit_request();
    $page='list_requests';
    $request_info=request_info($_REQUEST['request_id']);
    $_REQUEST['student_id'] = $request_info['student_id'];
}
else if($_REQUEST["action"]=='create_match'){ create_match(); }
else if($_REQUEST["action"]=='edit_match'){ edit_match(); }
else if($_REQUEST["action"]=='delete_student'){ delete_student(); }
else if($_REQUEST["action"]=='delete_tutor'){ delete_tutor(); }
else if($_REQUEST["action"]=='delete_request'){ delete_request(); }
else if($_REQUEST["action"]=='delete_match'){ delete_match(); }
else if($_REQUEST["action"]=='save_calling_screen'){ save_calling_screen(); }
else if($_REQUEST["action"]=='cancel_request'){ cancel_request(); }
else if($_REQUEST["action"]==''){ } //do nothing
else { show_error("action: ".$_REQUEST['action']." not understood"); }

if($page == ''){
    $page='main';
}

include('header.php');
include($page.'.php');
?>
