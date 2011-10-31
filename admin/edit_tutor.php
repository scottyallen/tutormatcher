<?
$tutor_id=$_REQUEST["tutor_id"];
$tutor_info=tutor_info($tutor_id);
?>

<div class=title>Edit Tutor Account</div>

<form method="post">
<input type="hidden" name="action" value="<? echo $_REQUEST["set_action"] ?>">
<input type="hidden" name="page" value="edit_tutor">
<input type="hidden" name="tutor_id" value="<? echo $tutor_id ?>">
<table>
<tr><td align="right">Name:</td>
    <td><input name="name" value="<? echo $tutor_info['name']?>"></td>
</tr>
<tr><td align="right">Username:</td>
    <td><input name="username" value="<? echo $tutor_info['username']?>"></td>
</tr>
<tr><td valign="top" align="right">Address:</td>
    <td><textarea name="address"/><?echo $tutor_info['address']?></textarea></td>
</tr>
<tr>
    <td align="right">Phone Number (with areacode):</td>
    <td><input name="phone" value="<?echo $tutor_info['phone']?>"/></td>
</tr>
<tr>
    <td align="right">Email Address:</td>
    <td><input name="email" value="<?echo $tutor_info['email']?>"/></td>
</tr>
<tr>
    <td valign="top" align="right">Category:</td>
    <td><? tutor_category_select($tutor_info['tutor_category']) ?> </td>
</tr>
<tr>
    <td align="right">Grade:</td>
    <td><? grade_select($tutor_info['grade']) ?> </td>
</tr>
<tr>
    <td align="right" valign="top">Subjects you are willing to tutor in:<br>(use ctrl to
    select multiple)</td>
    <td> <?  subjects_select(tutor_subjects($tutor_id)); ?> </td>
</tr>
<tr>
    <td align='right'>Total hours you'd like to tutor per week:</td>
    <td><input name="total_hours_desired" size=3/ value="<? echo $tutor_info['total_hours_desired'] ?>" ></td>
</tr>
<tr>
    <td align='right'>Change Password (6-20 characters):</td>
    <td><input name="password" type="password"/></td>
</tr>
<tr>
    <td align='right'>Retype Password:</td>
    <td><input name="password2" type="password"/></td>
</tr>
<tr>
    <td align=right>Signed Confidentiality Agreement:</td>
    <td>
        <input name="signed_confidentiality" type="checkbox" <?
         echo $tutor_info['signed_confidentiality'] ? 'checked' : '';
         ?> />
    </td>
</tr>
<tr>
    <td valign="top" align='right'>Availability:</td>
    <td>
    <?
        availability_matrix(get_tutor_avail($tutor_id));
    ?>
    </td>
</tr>
<tr>
    <td align='right'>Notes:</td>
    <td><textarea rows=4 cols=80 name="notes"><? echo $tutor_info['notes'] ?></textarea></td>
</tr>
</table>
<? question_table("tutor",get_answers(null,$tutor_id)); ?>
<input type="submit" value="Submit">
</form>
