<?
    $_REQUEST['matched']=0;
    $_REQUEST['student_id'] = $_SESSION['student_id'];
    $requests=search_requests();
    $match_info_list=search_matches();
?>
<div class=title>Student homepage</div>

<br>
<div class=message>
<? echo $message ?>
</div>

<br>
Open Requests - <a href="<? echo $PHP_SELF ?>?page=new_tutor_request&student_id=<? echo $_SESSION['student_id'] ?>">Create a new tutor request</a>

<table class="border" border=1>
    <tr>
        <th>Teacher</th>
        <th>Subject</th>
        <th>Times per Week</th>
    </tr>
    <?
    if($requests){
        foreach($requests as $request_info){
            ?>
            <tr>
                <td><? echo $request_info['teacher'] ?></td>
                <td><? echo $request_info['subject_name'] ?></td>
                <td align=center><? echo $request_info['times_per_week'] ?></td>
                <td><a href='?set_page=student_home&set_action=edit_request&page=edit_request&request_id=<? echo $request_info['id'] ?>'>edit</a></td>
                <td><a href='?set_page=student_home&page=cancel_request&request_id=<? echo $request_info['id'] ?>'>cancel</a></td>
           </tr>
           <?
        }
    } else {
        ?>
        <tr><td colspan=3>No current requests</td></tr>
        <?
    } ?>
</table>
<br>
Current Tutors
<table class="border" border=1>
    <tr>
        <th>Tutor</th>
        <th>Tutor's Email</th>
        <th>Tutor's Phone</th>
        <th>Subject</th>
        <th>Meeting times</th>
        <th>Start date</th>
    </tr>
    <?
    if ($match_info_list){
        foreach($match_info_list as $match_info){
            $request_info=request_info($match_info['request_id']);
            $tutor_info=tutor_info($match_info['tutor_id']);
            ?>
            <tr>
                <td><? echo $tutor_info['name'] ?></td>
                <td><? echo $tutor_info['email'] ?></td>
                <td><? echo $tutor_info['phone'] ?></td>
                <td><? echo $request_info['subject_name'] ?></td>
                <td><? print_match_times($match_info['id']) ?></td>
                <td><? echo $match_info['start_date'] ?></td>
           </tr>
           <?
        } ?> 
(Note: 8th period in meeting times means after school.  Thus, M-8 means you'll meet Mondays from 3-4pm with your tutor.)
        <?
    } else {
        ?>
        <tr><td colspan=6>No current tutors</td></tr>
        <?
    } ?>
</table>
<br>
