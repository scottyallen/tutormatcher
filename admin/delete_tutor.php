<?
$tutor_id=$_REQUEST["tutor_id"];
$tutor_info=tutor_info($tutor_id);
?>

<div class=title>Delete Tutor?</div>
Only bogus accounts should be deleted.  All others should be disabled for historical tracking.

<form method="post">
<input type="submit" value="Delete">
<input type="hidden" name="action" value="delete_tutor">
<input type="hidden" name="page" value="<? echo $_REQUEST['set_page'] ?>">
<input type="hidden" name="tutor_id" value="<? echo $tutor_id ?>">
<table>
<tr><td align="right">Name:</td>
    <td><? echo $tutor_info['name']?></td>
</tr>
<tr><td align="right">Username:</td>
    <td><? echo $tutor_info['username']?></td>
</tr>
<tr><td valign="top" align="right">Address:</td>
    <td><?echo $tutor_info['address']?></td>
</tr>
<tr>
    <td align="right">Phone Number (with areacode):</td>
    <td><?echo $tutor_info['phone']?></td>
</tr>
<tr>
    <td align="right">Email Address:</td>
    <td><?echo $tutor_info['email']?></td>
</tr>
<tr>
    <td valign="top" align="right">Category:</td>
    <td><? echo $tutor_info['category_name'] ?> </td>
</tr>
<tr>
    <td align="right">Grade:</td>
    <td><? echo $tutor_info['grade'] ? $tutor_info['grade'].'th' : '' ?> </td>
</tr>
<tr>
    <td align="right" valign="top">Subjects you are willing to tutor in:<br>(use ctrl to
    select multiple)</td>
    <td> <?  subjects_select(tutor_subjects($tutor_id)); ?> </td>
</tr>
<tr>
    <td align='right'>Total hours you'd like to tutor per week:</td>
    <td><? echo $tutor_info['total_hours_desired'] ?></td>
</tr>
<tr>
    <td valign="top" align='right'>Availability:</td>
    <td>
    <?
        availability_matrix(get_tutor_avail($tutor_id));
    ?>
    </td>
</table>
</form>
