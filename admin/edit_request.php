<?
$request_id=$_REQUEST["request_id"];
$request_info=request_info($request_id);
$student_info=student_info($request_info['student_id']);
?>

<div class=title>Edit Tutoring Request</div>

<form method="post">
<input type="hidden" name="action" value="<? echo $_REQUEST["set_action"] ?>">
<input type="hidden" name="page" value="edit_request">
<input type="hidden" name="request_id" value="<? echo $request_id ?>">
<input type="hidden" name="matched" value="<? echo $_REQUEST["matched"] ?>">
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
<tr>
    <td valign="top" align="right">Tutor categories requested:</td>
    <td valign="top"><? tutor_category_select(get_request_categories($request_info['id']),'multiple') ?> </td>
</tr>
<tr><td align="right">Times per week:</td>
    <td><input name="times_per_week" value="<? echo $request_info['times_per_week']?>"></td>
</tr>
<tr>
    <td align='right'>Notes:</td>
    <td><textarea name="notes" rows=4 cols=80><? echo $request_info['notes'] ?></textarea></td>
</tr>
</table>
<? question_table("student",get_answers($request_id)); ?>
<input type="submit" value="Submit">
</form>
<form>
<input type="hidden" name="page" value="cancel_request">
<input type="hidden" name="request_id" value="<?echo $request_id?>">
<input type="submit" value="Cancel">
</form>
