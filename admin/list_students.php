<?
    $students=search_students();
?>

Found <? echo count($students) ?> students:
<table class="border">
    <tr>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Grade</th>
        <th>Open Requests</th>
        <th>Active Matches</th>
        <th>Dead Matches</th>
        <th>Signed Contract</th>
        <th>Notes</th>
    <?
    foreach($students as $student_info){
        ?>
        <tr>
            <td><? echo $student_info['name'] ?>
            <td><? echo $student_info['username'] ?>
            <td><a href="mailto:<? echo $student_info['email']?>"><? echo $student_info['email']?></a></td>
            <td><? echo $student_info['phone'] ?>
            <td><? echo $student_info['grade'] ?>th</td>
            <td><a href="?page=list_requests&student_id=<?echo $student_info['id']?>&matched=0"><? echo num_open_requests($student_info['id']) ?></a></td>
            <td><a href="?page=list_matches&student_id=<?echo $student_info['id']?>&active=1"><? echo num_active_matches($student_info['id']) ?></a></td>
            <td><a href="?page=list_matches&student_id=<?echo $student_info['id']?>&active=0"><? echo num_dead_matches($student_info['id']) ?></a></td>
            <td><? echo $student_info['signed_contract']?'yes':'no' ?></td>
            <td><? echo $student_info['notes']?'yes':'' ?></td>
            <td><a href='?set_action=edit_student&page=edit_student&student_id=<? echo $student_info['id'] ?>'>edit</a></td>
            <td><a href='?page=delete_student&set_page=list_students&student_id=<? echo $student_info['id'] ?>'>delete</a></td>
       </tr>
       <?
    }
    ?>
</table>
