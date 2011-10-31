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

<div class=title>Are you sure you want to delete this student?</div>
Only bogus accounts should be deleted.<br><br>

<form method="post">
<input type="hidden" name="action" value="delete_student">
<input type="hidden" name="page" value="<? echo $_REQUEST['set_page'] ?>">
<input type="hidden" name="student_id" value="<? echo $id ?>">
<input type="submit" value="Delete">
<table>
<tr><td align="right">Name:</td>
    <td><? echo $name?></td>
</tr>
<tr><td align="right">Username:</td>
    <td><? echo $username?></td>
</tr>
<tr><td valign="top" align="right">Address:</td>
    <td><?echo $address?></td>
</tr>
<tr>
    <td align="right">Phone Number (with areacode):</td>
    <td><?echo $phone?></td>
</tr>
<tr>
    <td align="right">Email Address:</td>
    <td><?echo $email?></td>
</tr>
<tr>
    <td align="right">Grade:</td>
    <td><? echo $grade ?>th</td>
</tr>
<tr>
    <td align=right>Signed Contract:</td>
    <td>
         <? echo $signed_contract ? 'yes' : 'no'; ?>
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
    <td><? echo $notes ?></td>
</tr>
</table>
</form>
