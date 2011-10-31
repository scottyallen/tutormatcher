<?php
    $start_date = $_REQUEST['start_date'];
    $end_date = $_REQUEST['end_date'];
    $subject_counts = get_matches_subject_counts($start_date,$end_date);
    $six_week_matches = get_students_tutored_longer_than($start_date,$end_date,6*7);
?>
<h3>Matches by subject:</h3>
<table border=1>
    <tr>
        <th>Subject</th>
        <th>Matches</th>
    </tr>
<?php
    foreach($subject_counts as $subject => $count){
        ?>
        <tr>
            <td><?php echo $subject;?></td>
            <td><?php echo $count;?></td>
        </tr>
        <?
    }
?>
</table>

<h3>Students who have been tutored for 6 weeks or more:</h3>
<table border=1>
    <tr>
        <th>Student Name</th>
        <th>Subject</th>
        <th>Length of match</th>
        <th>Start Date</th>
        <th>End Date</th>
    </tr>
<?
    foreach($six_week_matches as $row){
        ?>
            <tr>
                <td><? echo $row['name']; ?></td>
                <td><? echo $row['subject']; ?></td>
                <? if($row['length'] > 0){
                        echo "<td>".$row['length']."</td>";
                    } else {
                        echo "<td>unknown - still running</td>";
                    }
                ?>
                <td><? echo $row['start_date']; ?></td>
                <td><? echo $row['end_date']; ?></td>
            </tr>
        <?
    }

?>
</table>

<h3>Total hours tutored:</h3>
<table>
    <tr>
        <th>Tutor Category</th>
        <th>Hours Tutored</th>
    </tr>
<?

?>
</table>
