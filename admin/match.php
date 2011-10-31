<?
$request_id=addslashes($_REQUEST[request_id]);
$matches = find_match($request_id);
$request_info = request_info($request_id);
$student_info = student_info($request_info['student_id']);
?>

<div class=title>Make a Match</div>

<table>
<tr>
<td>
<table>
<tr>
    <td align='right'>Name:</td>
    <td><? echo $student_info['name'] ?></td>
</tr>
<tr>
    <td align='right'>Subject:</td>
    <td><? echo $request_info['subject_name'] ?></td>
</tr>
<tr>
    <td align='right'>Teacher:</td>
    <td><? echo $request_info['teacher'] ?></td>
</tr>
<tr>
    <td align='right'>Grade:</td>
    <td><? echo $student_info['grade'] ?></td>
</tr>
<tr>
    <td align='right'>Number of times per week student wants tutoring:</td>
    <td><? echo $request_info['times_per_week'] ?></td>
</tr>
<tr>
    <td align='right' valign="top">Requested Tutor Categories:</td>
    <td>
        <?
        $requested_categories =get_request_categories($request_info['id']);
        foreach ($requested_categories as $category_id){
            echo category_name($category_id)."<br/>";
        }
        ?>
    </td>
</tr>
</table>
</td>

<td valign="top">
<table>
<tr>
    <td align='right'>Address:</td>
    <td><? echo $student_info['address'] ?></td>
</tr>
<tr>
    <td align='right'>Phone Number:</td>
    <td><? echo $student_info['phone'] ?></td>
</tr>
<tr>
    <td align='right'>Email:</td>
    <td><? echo $student_info['email'] ?></td>
</tr>
<tr>
    <td align='right'>Username:</td>
    <td><? echo $student_info['username'] ?></td>
</tr>
<tr>
    <td align='right'>Has Signed Contract:</td>
    <td><? echo $student_info['signed_contract']?'yes':'no' ?></td>
</tr>
</table>
</td></tr></table>

<br/>
<br/>
<table class="border">
    <tr>
        <th>Name</th>
        <th>Category</th>
        <th>Times Available</th>
        <th>Hours Desired</th>
        <th>Notes</th>
    </tr>

    <?
    foreach ($matches as $match){
        $tutor_info=tutor_info($match[0]);
        ?>
        <tr>
            <td><? echo $tutor_info['name'] ?></td>
            <td><? echo $tutor_info['category_name'] ?></td>
            <td><? print_busy(get_student_tutor_busy($tutor_info['id'],
                                             $student_info['id'])); ?></td>
            <td><? echo $tutor_info['total_hours_desired']?></td>
            <td><? echo $tutor_info["notes"] ?></td>
            <td><a href="?set_action=edit_tutor&page=edit_tutor&tutor_id=<?echo $tutor_info['id']?>">Details</a></td>
            <td><a href="?set_action=create_match&page=make_match&tutor_id=<?echo $tutor_info['id']?>&request_id=<?echo $request_id?>">Match</a></td>
        </tr>
        <?
    }
    ?>

</table>
