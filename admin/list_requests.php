<?
    $requests=search_requests();
?>

<div class="title">Requests</a>

<table class="border">
    <tr>
        <th>Student Name</th>
        <th>Teacher</th>
        <th>Subject</th>
        <th>Times per Week</th>
        <th>Notes</th>
    <?
    foreach($requests as $request_info){
        ?>
        <tr>
            <td><? echo $request_info['name'] ?></td>
            <td><? echo $request_info['teacher'] ?></td>
            <td><? echo $request_info['subject_name'] ?></td>
            <td><? echo $request_info['times_per_week'] ?></td>
            <td><? echo $request_info['notes']?'yes':'' ?></td>
            <td><a href='?set_action=edit_request&page=edit_request&request_id=<? echo $request_info['id'] ?>&matched=<? echo $_REQUEST['matched'] ?>'>edit</a></td>
            <td><a href='?set_page=list_requests&page=delete_request&request_id=<? echo $request_info['id'] ?>&student_id=<? echo $request_info['student_id']?>'>delete</a></td>
       </tr>
       <?
    }
    ?>
</table>
