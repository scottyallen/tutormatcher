<?
$tutor_id=$_REQUEST["tutor_id"];
$request_id=$_REQUEST["request_id"];
$request_info=request_info($request_id);
$tutor_info=tutor_info($tutor_id);
$student_id=$request_info['student_id'];
$student_info=student_info($student_id);
?>

<div class=title>Make a Match</div>

<form method="post">
<input type="hidden" name="action" value="<? echo $_REQUEST["set_action"] ?>">
<input type="hidden" name="page" value="match_list">
<input type="hidden" name="request_id" value="<? echo $request_id ?>">
<input type="hidden" name="tutor_id" value="<? echo $tutor_id ?>">
<table class="border">
    <tr>
        <td align=right>Student:</td>
        <td>
            <?echo $student_info['name']?>
        </td>
        <td>
            <a
            href="?set_action=edit_student&page=edit_student&student_id=<?echo
            $student_info['id']?>">Details</a>
        </td>
    </tr>
    <tr>
        <td align=right>Tutor:</td>
        <td>
            <?echo $tutor_info['name']?>
        </td>
        <td>
            <a
            href="?set_action=edit_tutor&page=edit_tutor&tutor_id=<?echo
            $tutor_info['id']?>">Details</a>
        </td>
    </tr>
    <tr>
        <td align=right>Subject:</td>
        <td>
            <? echo $request_info['subject_name']; ?>
        </td>
    </tr>
    <tr>
        <td align=right>Start Date:<br>(mm-dd-yy)</td>
        <td>
            <input name="start_date" size=8>
        </td>
    </tr>
    <tr>
        <td align=right>Times:</td>
        <td>
            <? match_times_matrix(get_common_times($tutor_id,$student_id)); ?>
        </td>
    </tr>
    <tr>
        <td align=right>Notes:</td>
        <td>
            <textarea name="notes" rows=5 cols=40></textarea>
        </td>
    </tr>
</table>
<input type="submit" value="Submit"/>
</form>
