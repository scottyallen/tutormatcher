<?
$match_id=$_REQUEST["match_id"];
$match_info=match_info($match_id);
$request_info=request_info($match_info['request_id']);
$student_info=student_info($request_info['student_id']);
$tutor_info=tutor_info($match_info['tutor_id']);
?>

<div class=title>Edit Tutoring Match</div>

<form method="post">
<input type="hidden" name="action" value="<? echo $_REQUEST["set_action"] ?>">
<input type="hidden" name="page" value="edit_match">
<input type="hidden" name="match_id" value="<? echo $match_id ?>">
<table>
    <tr>
        <td align="right">Student:</td>
        <td><a href="?set_action=edit_student&page=edit_student&student_id=<?echo $student_info['id']?>"><? echo $student_info['name'] ?></a></td>
    </tr>
    <tr>
        <td align="right">Tutor:</td>
        <td><a href="?set_action=edit_tutor&page=edit_tutor&tutor_id=<?echo $tutor_info['id']?>"><? echo $tutor_info['name'] ?></a></td>
        
    </tr>
    <tr>
        <td align="right">Subject:</td>
        <td>
            <? echo $request_info['subject_name']; ?>
        </td>
    </tr>
    <tr>
        <td align=right>Start Date:</td>
        <td>
            <input name="start_date" size=8 value="<? echo mysql_date2standard_date($match_info['start_date']) ?>">
        </td>
    </tr>
    <tr>
        <td align=right>End Date:</td>
        <td>
            <input name="end_date" size=8 value="<? echo (($match_info['end_date'] == '0000-00-00' || $match_info['end_date'] == '') ? '' : mysql_date2standard_date($match_info['end_date'])) ?>">
        </td>
    </tr>
    <tr>
        <td align=right>Active:</td>
        <td>
            <input name="active" type="checkbox" <?
             echo $match_info['active'] ? 'checked' : '';
             ?> />
        </td>
    </tr>
    <tr>
        <td align=right>Times:</td>
        <td>
            <? match_times_matrix(get_common_times($tutor_info['id'],$student_info['id']),get_match_times($match_id)); ?>
        </td>
    </tr>
    <tr>
        <td align=right>Notes:</td>
        <td>
            <textarea name="notes" rows=5 cols=40><? echo $match_info['notes'] ?></textarea>
        </td>
    </tr>
</table>
<input type="submit" value="Submit">
</form>
