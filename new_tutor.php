<div class=title>Create Tutor Account</div>

<form method="post">
<input type="hidden" name="action" value="create_tutor">
<input type="hidden" name="page" value="tutor_home">
<table class=border cellpadding=0 cellspacing=0>
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
    <td align="right" valign="top">Phone Number:<br>(with areacode)</td>
    <td valign="top"><input name="phone"/></td>
</tr>
<tr>
    <td align="right">Email Address:</td>
    <td><input name="email"/></td>
</tr>
<tr>
    <td valign="top" align="right">Category:</td>
    <td><? tutor_category_select() ?> </td>
</tr>
<tr>
    <td align="right" valign="top">Subjects you are willing to tutor in:<br><b>(use ctrl to
    select multiple)</b></td>
    <td><? subjects_select() ?> </td>
</tr>
<tr>
    <td align="right">Grade:<br>(if you are a BHS student)</td>
    <td valign="top"><? grade_select() ?> </td>
</tr>
<tr>
    <td align='right'>Desired Password:<br>(6-20 characters)</td>
    <td valign='top'><input name="password" type="password"/></td>
</tr>
<tr>
    <td align='right'>Retype Password:</td>
    <td><input name="password2" type="password"/></td>
</tr>
<tr>
    <td align='right'>Total hours you'd like to tutor per week:</td>
    <td><input name="total_hours_desired" size=3/></td>
</tr>
<tr>
    <td valign="top" align='right'>Availability:<br>(click on checkboxes to select times)</td>
    <td>
    <?
        availability_matrix();
    ?>
    </td>
</table>
<? question_table("tutor"); ?>
<input type="submit" value="Submit">
</form>
