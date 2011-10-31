<?
    $tutors=search_tutors();
?>

Found <? echo count($tutors) ?> tutors:
<table class="border">
    <tr>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Category</th>
        <th>Grade</th>
        <th>Requested Hours</th>
        <th>Active Matches</th>
        <th>Dead Matches</th>
        <th>Signed Confidentiality Agreement</th>
        <th>Notes</th>
    <?
    foreach($tutors as $tutor_info){
        ?>
        <tr>
            <td><? echo $tutor_info['name'] ?>
            <td><? echo $tutor_info['username'] ?></td>
            <td><a href="mailto:<? echo $tutor_info['email']?>"><? echo $tutor_info['email']?></a></td>
            <td><? echo $tutor_info['phone'] ?>
            <td><? echo $tutor_info['category_name'] ?></td>
            <td><? echo $tutor_info['grade'] ? $tutor_info['grade'].'th' : '' ?></td>
            <td><? echo $tutor_info['total_hours_desired'] ?></td>
            <td><a href="?page=list_matches&tutor_id=<?echo $tutor_info['id']?>&active=1"><? echo num_active_matches(null,$tutor_info['id']) ?></a></td>
            <td><a href="?page=list_matches&tutor_id=<?echo $tutor_info['id']?>&active=0"><? echo num_dead_matches(null,$tutor_info['id']) ?></a></td>
            <td><? echo $tutor_info['signed_confidentiality']?'yes':'no' ?></td>
            <td><? echo $tutor_info['notes']?'yes':'' ?></td>
            <td><a href='?set_action=edit_tutor&page=edit_tutor&tutor_id=<? echo $tutor_info['id'] ?>'>edit</a></td>
            <td><a href='?page=delete_tutor&set_page=list_tutors&tutor_id=<? echo $tutor_info['id'] ?>'>delete</a></td>
       </tr>
       <?
    }
    ?>
</table>
