Hi <? echo $tutor_info['name'] ?>,

I've found a student who needs tutoring in <? echo $request_info['subject_name'] ?>.  The student's name is <? echo $student_info['name']; ?>. <? if ($student_info['email']){ ?>You can reach them via email at <? echo $student_info['email']; ?> or by phone at <? echo $student_info['phone'] ?><? } else { ?> You can reach them via phone at <? echo $student_info['phone']; } ?>.  I have scheduled you to meet on the following schedule:

<? echo get_email_schedule($match_id) ?>

I have arranged for you meet for the first time on <? echo mysql_date2standard_date($match_info['start_date']) ?>. Come to the O-Zone desk and I'll introduce you to your student.

Please let me know that you have successfully received this email by clicking on the following link:

<? echo $domainPath?>/?page=confirm_email&action=confirm&tutor_id=<?echo $tutor_info["id"]?>&match_id=<?echo $match_id?>


If you have any questions, or can't make your initial meeting with your student, you can come see me in the O-Zone, or call me at <?echo $coordinatorPhone?>.

Thanks,

<?echo $coordinatorName?>
