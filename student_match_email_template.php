Hi <? echo $student_info['name'] ?>,

I've found a tutor who is willing to tutor you in <? echo $request_info['subject_name'] ?>.  The tutor's name is <? echo $tutor_info['name']; ?>. <? if ($tutor_info['email']){ ?>You can reach them via email at <? echo $tutor_info['email']; ?> or by phone at <? echo $tutor_info['phone'] ?><? } else { ?> You can reach them via phone at <? echo $tutor_info['phone']; } ?>.  I have scheduled you to meet on the following schedule:

<? echo get_email_schedule($match_id) ?>

I have arranged for you meet for the first time on <? echo mysql_date2standard_date($match_info['start_date']) ?>. Come to the desk in the O-Zone and I will introduce you to your tutor.

Please let me know that you have successfully received this email by clicking on the following link:

<? echo $domainPath?>/?page=confirm_email&action=confirm&student_id=<?echo $student_info["id"]?>&match_id=<?echo $match_id?>


If you have any questions, or can't make your initial meeting with your tutor, you can come see me right away in the O-Zone, or call me at <?echo $coordinatorPhone?>.

Thanks,

<?echo $coordinatorName?>
