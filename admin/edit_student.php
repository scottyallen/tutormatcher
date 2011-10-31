<?
$student_id=$_REQUEST["student_id"];
$row=student_info($student_id);
$id = $row["id"];
$name = $row["name"];
$username = $row["username"];
$address = $row["address"];
$phone = $row["phone"];
$grade = $row["grade"];
$email = $row["email"];
$notes = $row["notes"];
$signed_contract = $row["signed_contract"];
?>

<div class=title>Edit Student Account</div>

<form method="post">
<input type="hidden" name="action" value="<? echo $_REQUEST["set_action"] ?>">
<input type="hidden" name="page" value="edit_student">
<input type="hidden" name="student_id" value="<? echo $id ?>">
<table>
<tr><td align="right">Name:</td>
    <td><input name="name" value="<? echo $name?>"></td>
</tr>
<tr><td align="right">Username:</td>
    <td><input name="username" value="<? echo $username?>"></td>
</tr>
<tr><td valign="top" align="right">Address:</td>
    <td><textarea name="address"/><?echo $address?></textarea></td>
</tr>
<tr>
    <td align="right">Phone Number (with areacode):</td>
    <td><input name="phone" value="<?echo $phone?>"/></td>
</tr>
<tr>
    <td align="right">Email Address:</td>
    <td><input name="email" value="<?echo $email?>"/></td>
</tr>
<tr>
    <td align="right">Grade:</td>
    <td><? grade_select($grade) ?> </td>
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
    <td align=right>Signed Contract:</td>
    <td>
        <input name="signed_contract" type="checkbox" <?
         echo $signed_contract ? 'checked' : '';
         ?> />
    </td>
</tr>
<tr>
    <td valign="top" align='right'>Availability:</td>
    <td>
    <?
        availability_matrix(get_student_avail($student_id));
    ?>
    </td>
</tr>
<tr>
    <td align='right'>Notes:</td>
    <td><textarea rows=4 cols=80 name="notes"><? echo $notes ?></textarea></td>
</tr>
</table>
<input type="submit" value="Submit">
</form>
