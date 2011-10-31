<div class=title>Create Student Account</div>

<form method="post">
<input type="hidden" name="action" value="create_student">
<input type="hidden" name="page" value="student_home">
<table>
<tr><td align="right">First Name:</td>
    <td><input name="firstname"/></td>
</tr>
<tr><td align="right">Last Name:</td>
    <td><input name="lastname"/></td>
</tr>
<tr><td valign="top" align="right">Address:</td>
    <td><textarea name="address"/></textarea></td>
</tr>
<tr>
    <td align="right">Phone Number (with areacode):</td>
    <td><input name="phone"/></td>
</tr>
<tr>
    <td align="right">Email Address:</td>
    <td><input name="email"/></td>
</tr>
<tr>
    <td align="right">Grade:</td>
    <td><? grade_select() ?> </td>
</tr>
<tr>
    <td align='right'>Desired Password (6-20 characters):</td>
    <td><input name="password" type="password"/></td>
</tr>
<tr>
    <td align='right'>Retype Password:</td>
    <td><input name="password2" type="password"/></td>
</tr>
<tr>
    <td valign="top" align='right'>Availability:<br>(click on checkboxes to select times)</td>
    <td>
    <?
        availability_matrix();
    ?>
    </td>
</table>
<input type="submit" value="Submit">
</form>
