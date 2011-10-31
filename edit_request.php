<?
$request_id=$_REQUEST["request_id"];
$request_info=request_info($request_id);
$student_info=student_info($request_info['student_id']);
?>

<div class=title>Edit Tutoring Request</div>

<form method="post">
<input type="hidden" name="action" value="edit_request">
<input type="hidden" name="page" value="student_home">
<input type="hidden" name="request_id" value="<? echo $request_id ?>">
<table>
<tr><td align="right">Student:</td>
    <td><? echo $student_info['name'] ?></td>
</tr>
<tr><td align="right">Teacher:</td>
    <td><input name="teacher" value="<? echo $request_info['teacher']?>"></td>
</tr>
<tr><td align="right">Subject:</td>
    <td><? subjects_select(array($request_info['subject']),true); ?></td>
</tr>
<tr><td align="right">Times per week:</td>
    <td><input name="times_per_week" value="<? echo $request_info['times_per_week']?>"></td>
</tr>
</table>
<input type="submit" value="Submit">
</form>
