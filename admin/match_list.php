<?

// TODO The "Times" column should enumerate times student is available,
// with already matched times in red - also should show number of times per
// week requested tutoring in seperate col
$request_ids = get_unmatched_students();
?>

<div class=title>Unmatched Students</div>
<table class="border" cellspacing=0>
<tr>
    <th>Name</th>
    <th>Grade</th>
    <th>Subject</th>
    <th>Times</th>
</tr>

<?
foreach ($request_ids as $request_id){
    $request_info=request_info($request_id);
    $student_info=student_info($request_info['student_id']);
    ?>
    <tr>
        <td><? echo $student_info['name'] ?></td>
        <td><? echo $student_info['grade'] ?></td>
        <td><? echo $request_info['subject_name'] ?></td>
        <td><? echo $request_info['times_per_week'] ?></td>
        <td><a href='?set_action=edit_student&page=edit_student&student_id=<? echo $request_info['student_id'] ?>'>Details</a></td>
        <td><a href='?page=match&request_id=<? echo $request_id ?>'>Find Match</a></td>
        <td><a href='?set_action=edit_request&page=edit_request&request_id=<? echo $request_id ?>'>Edit Request</a></td>
        <td><a href='?page=cancel_request&request_id=<? echo $request_id ?>'>Cancel Request</a></td>
    </tr>
    <?
}
?>

</table>
