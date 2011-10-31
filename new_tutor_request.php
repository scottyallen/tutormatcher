<div class=title>Create New Tutor Request</div>

<form method="post">
<input type="hidden" name="action" value="create_tutor_request">
<input type="hidden" name="page" value="student_home">
<input type="hidden" name="student_id" value="<? echo $_SESSION["student_id"] ?>">
<table>
<tr><td align="right">What subject would you like tutoring in:</td>
    <td><? subjects_select(array(),'single'); ?></td>
</tr>
<tr><td align="right">Who is your teacher:</td>
    <td><input name="teacher"/></td>
</tr>
<tr>
    <td valign="top" align="right" width=50%>Do you have a preference for what group
    your tutor comes from:<br><b>(Please select as many as possible, by holding down
    ctrl)</b></td>
    <td valign="top"><? tutor_category_select(null,'multiple') ?> </td>
</tr>
<tr>
    <td align="right">How many times per week do you want to be tutored:<br>
    (55-min sessions)</td>
    <td valign="top"><input name="times_per_week" size=3></td>
</tr>
</table>
<? question_table("student"); ?>
<input type="submit" value="Submit">
</form>
