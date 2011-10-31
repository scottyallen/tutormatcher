<?
$match_info_list=search_matches();
?>

<div class="title">Matches</div>

Found <? echo count($match_info_list) ?> matches:
<table class="border">
    <tr>
    <th>Student</th>
    <th>Tutor</th>
    <th>Subject</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Times</th>
    </tr>
    <?
    foreach($match_info_list as $match_info){
        $request_info=request_info($match_info['request_id']);
        $student_info=student_info($request_info['student_id']);
        $tutor_info=tutor_info($match_info['tutor_id']);
        ?>
        <tr>
            <td><a href="?set_action=edit_student&page=edit_student&student_id=<? echo $student_info['id'] ?>"><? echo $student_info['name'] ?></a></td>
            <td><a href="?set_action=edit_tutor&page=edit_tutor&tutor_id=<?echo $tutor_info['id']?>"><? echo $tutor_info['name'] ?></a></td>
            <td><? echo $request_info['subject_name'] ?></td>
            <td><? echo $match_info['start_date'] ?></td>
            <td><? echo $match_info['end_date'] ?></td>
            <td><? print_match_times($match_info['id']) ?></td>
            <td><a href="?set_action=edit_match&page=edit_match&match_id=<?echo $match_info['id']?>">edit</a></td>
        </tr>
        <?
    }
    ?>
</table>
