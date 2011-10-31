<?
    $matches_to_call_tutor=get_tutors_to_call();
    $matches_to_call_student=get_students_to_call();
?>

<form method="POST">
<input type=hidden name=page value="main">
<input type=hidden name=action value="save_calling_screen">
<div class=title>Students to call</div>
<? echo count($matches_to_call_student) ?> students to call
<table class="border" cellspacing=0>
<tr>
    <th>Name</th>
    <th>Phone Number</th>
    <th>Subject</th>
    <th>Tutor</th>
    <th>Start Date</th>
    <th>Match Times</th>
    <th>Confirmed</th>
    <th>Notes</th>
</tr>

<?
foreach ($matches_to_call_student as $match_id){
    $match_info=match_info($match_id);
    $request_info=request_info($match_info["request_id"]);
    $student_info=student_info($request_info["student_id"]);
    $tutor_info=tutor_info($match_info["tutor_id"]);
    ?>
    <tr>
        <td><?echo $student_info["name"]?></td>
        <td><?echo $student_info["phone"]?></td>
        <td><?echo $request_info["subject_name"]?></td>
        <td><?echo $tutor_info["name"]?></td>
        <td><?echo $match_info["start_date"]?></td>
        <td><? print_match_times($match_info['id']) ?></td>
        <td><input name="<?echo $match_info["id"]?>_confirm_student" type="checkbox"/></td>
        <td><textarea name="<?echo $match_info["id"]?>_notes_student"><?echo $match_info["student_ack_note"]?></textarea></td>
    </tr>
    <?
}
?>
</table>

<div class=title>Tutors to call</div>
<? echo count($matches_to_call_tutor) ?> students to call
<table class="border" cellspacing=0>
<tr>
    <th>Name</th>
    <th>Phone Number</th>
    <th>Subject</th>
    <th>Tutor</th>
    <th>Start Date</th>
    <th>Match Times</th>
    <th>Confirmed</th>
    <th>Notes</th>
</tr>
<?
foreach ($matches_to_call_tutor as $match_id){
    $match_info=match_info($match_id);
    $request_info=request_info($match_info["request_id"]);
    $student_info=student_info($request_info["student_id"]);
    $tutor_info=tutor_info($match_info["tutor_id"]);
    ?>
    <tr>
        <td><?echo $tutor_info["name"]?></td>
        <td><?echo $tutor_info["phone"]?></td>
        <td><?echo $request_info["subject_name"]?></td>
        <td><?echo $student_info["name"]?></td>
        <td><?echo $match_info["start_date"]?></td>
        <td><? print_match_times($match_info['id']) ?></td>
        <td><input name="<?echo $match_info["id"]?>_confirm_tutor" type="checkbox"/></td>
        <td><textarea name="<?echo $match_info["id"]?>_notes_tutor"><?echo $match_info["tutor_ack_note"]?></textarea></td>
    </tr>
    <?
}
?>
</table>
<br>
<input type="submit" value="Done">
</form>
