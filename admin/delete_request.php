<?
$request_id=$_REQUEST["request_id"];
$request_info=request_info($request_id);
$student_info=student_info($request_info['student_id']);
?>

<div class=title>Edit Tutoring Request</div>

<form method="post">
<input type="submit" value="Delete">
<input type="hidden" name="action" value="delete_request">
<input type="hidden" name="page" value="<? echo $_REQUEST["set_page"]?>">
<input type="hidden" name="request_id" value="<? echo $request_id ?>">
<input type="hidden" name="student_id" value="<? echo $_REQUEST["student_id"] ?>">
<table>
<tr><td align="right">Student:</td>
    <td><? echo $student_info['name'] ?></td>
</tr>
<tr><td align="right">Teacher:</td>
    <td><? echo $request_info['teacher']?></td>
</tr>
<tr><td align="right">Subject:</td>
    <td><? echo $request_info['subject_name'] ?></td>
</tr>
<tr><td align="right">Times per week:</td>
    <td><? echo $request_info['times_per_week']?></td>
</tr>
<tr>
    <td align='right'>Notes:</td>
    <td><? echo $request_info['notes'] ?></td>
</tr>
</table>
</form>
