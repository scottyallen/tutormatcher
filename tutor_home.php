<?
    $match_info_list=search_matches();
?>

<div class=title>Tutor homepage</div>
<br>
<div class=message>
<? echo $message ?>
</div>

<br>
Current Students
<table class="border" border=1>
    <tr>
        <th>Student</th>
        <th>Student's Email</th>
        <th>Students's Phone</th>
        <th>Subject</th>
        <th>Meeting times</th>
        <th>Start date</th>
    </tr>
    <?
    if($match_info_list){
        foreach($match_info_list as $match_info){
            $request_info=request_info($match_info['request_id']);
            $student_info=student_info($request_info['student_id']);
            ?>
            <tr>
                <td><? echo $student_info['name'] ?></td>
                <td><? echo $student_info['email'] ?></td>
                <td><? echo $student_info['phone'] ?></td>
                <td><? echo $request_info['subject_name'] ?></td>
                <td><? print_match_times($match_info['id']) ?></td>
                <td><? echo $match_info['start_date'] ?></td>
           </tr>
           <?
        } ?>
    (Note: 8th period in meeting times means after school.  Thus, M-8 means you'll meet Mondays from 3-4pm with your student.)
        <?
    } else {
        ?>
        <tr><td colspan=6>No current students</td></tr>
        <?
    } ?>
</table>
<br>
