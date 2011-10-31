<?

//set_error_handler("error_handler");

function error_handler(){
    print "<pre>";
    var_dump(debug_backtrace());
    print "</pre>";
}

include_once('config.php');

// Accepts array of available times to autofill
function availability_matrix($a=array()){ // TODO This needs to list period times as well
    global $periods,$days,$periodNames,$dayNames;

    ?>
    <table class=border cellspacing=0 cellpadding=0>
            <tr>
                <th>Period</th>
    <?

    foreach($periods as $period){
        echo "<th>$periodNames[$period]</th>";
    }

    echo "</tr>";

    foreach($days as $day){
        echo "<tr><td>$dayNames[$day]</td>";
        foreach($periods as $period){
            if($a[$day][$period]){
                $checked = "checked";
            } else {
                $checked = '';
            }
            echo "<td align='center'><input type='checkbox' name='avail-$day-$period' $checked></td>";
        }
        echo "</tr>";
    }

    echo "</table>";
}

// Accepts array of available times to display boxes for (in form of pairs of
// (period, day)) $selected is a day/period boolean matrix, which gives days
// and periods to mark as selected. If a day/period is not in $shown but in
// $selected, it is shown and checked.
function match_times_matrix($shown=array(),$selected=array()){
    global $periods,$days,$periodNames,$dayNames;

    $times=array();
    foreach ($shown as $pair){
            $times[$pair[0]][$pair[1]]=1;
    }

    ?>
    <table class=border>
            <tr>
                <th>Period</th>
    <?

    foreach($periods as $period){
        echo "<th>$periodNames[$period]</th>";
    }

    echo "</tr>";

    foreach($days as $day){
        echo "<tr><td>$dayNames[$day]</td>";
        foreach($periods as $period){
            if($times[$day][$period] || $selected[$day][$period]){
                if($selected[$day][$period]){
                    $checked = "checked";
                } else {
                    $checked = '';
                }
                echo "<td align='center'><input type='checkbox' $checked name='time-$day-$period' $checked></td>";
            } else {
                echo "<td>&nbsp;</td>";
            }
        }
        echo "</tr>";
    }

    echo "</table>";
}

function get_unmatched_students(){
    $DB = new DB;
    $result = $DB->query("select tutor_request.id from tutor_request left join student on student.id=tutor_request.student_id where student.id=tutor_request.student_id");
    $request_ids=array();
    while ($row = mysql_fetch_array($result)){
        $request_id=$row[0];
        // skip requests that are already matched
        if (!request_matched($request_id)){
            array_push($request_ids,$row[0]);
        }
    }
    return $request_ids;
}

function request_matched($request_id){
    $DB = new DB;

    $result = $DB->query("select id from match_made where request_id=$request_id");
    $row=mysql_fetch_row($result);

    return $row[0];
}

// Returns a list of tutor ids that are potential matches for a given student
// id. This is the heart of the tutor matching algorithm.
function find_match($request_id){
    $DB = new DB;

    // get request details
    $result=$DB->query("select * from tutor_request where id=$request_id");
    $row=mysql_fetch_array($result);
    $student_id=$row["student_id"];
    $subject_id=$row["subject"];
    $times_per_week=$row["times_per_week"];
    $requested_categories = get_request_categories($request_id);

    // select all tutors which have subject
    $result=$DB->query("select tutor.* from tutor left join tutor2subject on tutor2subject.tutor_id =tutor.id where tutor2subject.subject_id=$subject_id");

    // for each tutor returned
    $matches_prefered_cat=array();
    $matches_other=array();
    while ($row = mysql_fetch_array($result)){
        $tutor_id=$row[id];
        // figure out if has time left
        if (time_left($tutor_id) < 1){
            continue; // if not, skip
        }

        // get tutor category
        $tutor_category=tutor_category($row["id"]);
        $category_matches = in_array($tutor_category, $requested_categories);

        // get list of common available times between tutor and student
        $common_times=get_common_times($tutor_id,$student_id);
        $common_times_number=count($common_times);

        // put data into datastructure 
        if ($category_matches){
            array_push($matches_prefered_cat, array($tutor_id, $tutor_category, $common_times, $category_matches,$common_times_number));
        } else {
            array_push($matches_other, array($tutor_id, $tutor_category, $common_times, $category_matches,$common_times_number));
        }
    }
    // sort matches on common hours
    $matches_prefered_cat=msort($matches_prefered_cat,5);
    $matches_other=msort($matches_other,5);
    
    $matches=array_merge($matches_prefered_cat,$matches_other);
    
    // return array
    return $matches;
}

function msort($array, $column){   
  $i=0;
  $sortarr=array();
  for($i=0; $i<count($array); $i++){
   $sortarr[]=$array[$i][$column];
  }
 
  array_multisort($sortarr, $array); 
  
  return($array);
}

// Returns and array of the requested tutor category ids for a particular request id
function get_request_categories($request_id){
    $DB = new DB;

    $categories=array();
    $result = $DB->query("select category_id from tutor_request2category where request_id=$request_id");
    while($row=mysql_fetch_row($result)){
        array_push($categories,$row[0]);
    }
    return $categories;
}

//returns the number of time slots a tutor has left
function time_left($tutor_id){
    // get number of slots that have been filled
    $busy_count=count_times(get_tutor_busy($tutor_id));

    // get number of slots left that haven't been filled
    $avail_count=count_times(get_tutor_avail($tutor_id));
    $avail_count -= $busy_count;


    // get total amount of slots tutor wants
    $tutor_info=tutor_info($tutor_id);
    $total_hours_desired=$tutor_info["total_hours_desired"];
    // subtract slots filled from total amount tutor wants, and return smaller between that and slots not yet filled
    $time_left=$total_hours_desired - $busy_count;

    if ($time_left < $avail_count){
        return $time_left;
    } else {
        return $avail_count;
    }
}

// returns an array of the available times in common between a student and a tutor
function get_common_times($tutor_id, $student_id){
    global $days, $periods;
    // get times for student
    $student_times=get_student_avail($student_id);
    $student_busy=get_student_busy($student_id);

    // get times for tutor
    $tutor_times=get_tutor_avail($tutor_id);
    $tutor_busy=get_tutor_busy($tutor_id);

    $result=array();
    // compare
    foreach($days as $day){
        foreach($periods as $period){
            if($student_times[$day][$period] &&
               $tutor_times[$day][$period] &&
               $tutor_busy[$day][$period] !=1 &&
               $student_busy[$day][$period] !=1
              ){
                array_push($result, array($day,$period));
            }
        }
    }
    // return common
    return $result;
}

// returns the category id that a tutor is in for the specifed tutor id
function tutor_category($tutor_id){
    $DB = new DB;

    $result = $DB->query("select tutor_category from tutor where tutor.id=$tutor_id");
    $row=mysql_fetch_row($result);

    return $row[0];
}

function tutor_category_name($tutor_id){
    $DB = new DB;

    $result = $DB->query("select category_name from tutor,tutor_category where tutor.tutor_category=tutor_category.id and tutor.id=$tutor_id");
    $row=mysql_fetch_row($result);

    return $row[0];
}

function category_name($id){
    $DB = new DB;

    $result = $DB->query("select category_name from tutor_category where id=$id");
    $row=mysql_fetch_row($result);

    return $row[0];
}

function question_table($category, $answers=null){
    $DB = new DB;

    if ($category == 'student'){
        $student_question=1;
    }else {
        $student_question=0;
    }

    $query = "SELECT * from question where student_question=$student_question order by id asc";
    $result = $DB->query($query);

    echo "<table>";
    while ($row=mysql_fetch_array($result)){
        $id=$row["id"];
        $question=$row["question"];
        ?>
        <tr>
            <td><? echo $question ?></td>
        </tr>
        <tr>
            <td><textarea rows=4 cols=80 name="question_<? echo $id ?>"><? echo $answers[$id] ?></textarea>
            </td>
        </tr>
        <?
    }
    echo "</table>";
}

function get_answers($request_id=null,$tutor_id=null){
    $DB = new DB;
    $query="SELECT * from answer";
    if ($request_id){
        $query.=" WHERE tutor_request_id=$request_id";
    } else if ($tutor_id){
        $query.=" WHERE tutor_id=$tutor_id";
    }
    $result = $DB->query($query);

    $answers=array();
    while ($row=mysql_fetch_array($result)){
        $answers[$row["question_id"]]=$row["answer"];
    }
    return $answers;
}

function tutor_info($tutor_id){
    $DB = new DB;
    $result = $DB->query("SELECT tutor.*,tutor_category.category_name from tutor,tutor_category where tutor.tutor_category=tutor_category.id and tutor.id=$tutor_id");

    return mysql_fetch_array($result);
}

function student_info($student_id){
    $DB = new DB;
    if (!$student_id){
        print "no student id";
        print "<pre>";
        print_r(debug_backtrace());
        print "</pre>";
        return;
    }
    $result = $DB->query("SELECT student.* from student where student.id=$student_id");

    return mysql_fetch_array($result);
}

function request_info($request_id){
    $DB = new DB;
    $result = $DB->query("SELECT tutor_request.*,subject.subject_name from tutor_request, subject where tutor_request.subject=subject.id and tutor_request.id=$request_id");

    return mysql_fetch_assoc($result);
}

function tutor_category_select($currcategory=NULL,$single=null){
    $DB = new DB;
    $result = $DB->query("SELECT * from tutor_category");

    if ($single == 'multiple'){
        echo "<select multiple name='tutor_category[]'>";
    }else {
        echo "<select name='tutor_category[]'>";
        echo "<option></option>";
    }
    while ($row=mysql_fetch_array($result)){
        $id=$row["id"];
        $category=$row["category_name"];
        if ($currcategory==$id){
            $selected="selected";
        } else if ( in_array($id,$currcategory) ){
            $selected="selected";
        } else {
            $selected='';
        }
        echo "<option value=$id $selected>".$category."</option>";
    }
    echo "</select>";
}

// returns an array of subject ids that a tutor can tutor
function tutor_subjects($tutor_id){
    $DB = new DB;
    $result = $DB->query("SELECT subject_id from tutor2subject where tutor_id=$tutor_id");
    $subjects=array();
    while($row=mysql_fetch_row($result)){
        array_push($subjects,$row[0]);
    }
    return $subjects;
}

function subjects_select($currsubjects=array(),$single=null){
    $DB = new DB;
    $result = $DB->query("SELECT * from subject order by subject_name");

    if ($single == 'single'){
        echo "<select name='subjects[]'>";
        echo "<option></option>";
    }else {
        echo "<select multiple name='subjects[]'>";
    }
    while ($row=mysql_fetch_array($result)){
        $id=$row["id"];
        $subject=$row["subject_name"];
        if (in_array($id,$currsubjects)){
            $selected="selected";
        } else {
            $selected='';
        }
        echo "<option value=$id $selected>".$subject."</option>";
    }
    echo "</select>";
}

function grade_select($currgrade=0){
    echo "<select name='grade'>";
    echo "<option></option>";
    foreach (array(9,10,11,12) as $grade){
        if ($currgrade==$grade){
            $selected="selected";
        } else {
            $selected='';
        }
        echo "<option value=$grade $selected>".$grade."th</option>";
    }
    echo "</select>";
}

function search_students(){
    $search_params = array("name", "email", "grade", "username", "signed_contract", "notes");
    $query = "SELECT * from student";
    $count=0;
    foreach ($search_params as $param){
        $value = $_REQUEST[$param];
        if ($value != ''){
            $value=addslashes($value);
            if ($count++ == 0){
                $query .= " WHERE $param like '%$value%'";
            } else {
                $query .= " AND $param like '%$value%'";
            }
        }
    }

    $query .= " ORDER BY name";

    $DB = new DB;
    $result = $DB->query($query);
    $results=array();
    while ($row=mysql_fetch_assoc($result)){
        array_push($results,$row);
    }
    return $results;
}

function search_tutors(){
    $search_params = array("name", "email", "grade", "username", "signed_confidentiality", "notes");
    $query = "SELECT tutor.id,tutor.name,tutor.address,tutor.phone,tutor.email,tutor.username,tutor.tutor_category,tutor.grade,tutor.total_hours_desired,tutor.signed_confidentiality,tutor.enabled,tutor.notes,tutor_category.category_name from tutor,tutor_category where tutor.tutor_category=tutor_category.id ";
    $count=0;
    foreach ($search_params as $param){
        $value = $_REQUEST[$param];
        if ($value != ''){
            $value=addslashes($value);
            $query .= " AND $param like '%$value%'";
        }
    }
    if ($_REQUEST['tutor_category'][0]){
        $value = addslashes($_REQUEST['tutor_category'][0]);
        $query .= " AND tutor_category = $value";
    }

    $query.= " ORDER BY name";

    $DB = new DB;
    $result = $DB->query($query);
    $results=array();
    while ($row=mysql_fetch_assoc($result)){
        array_push($results,$row);

    }
    return $results;
}

function search_requests(){
    $query = "SELECT tutor_request.*,student.name,subject.subject_name,match_made.id as match_id from student,subject,tutor_request left join match_made on match_made.request_id=tutor_request.id where tutor_request.student_id=student.id and tutor_request.subject=subject.id";

    if ( ($student_id = $_SESSION['student_id']) || ($student_id = addslashes($_REQUEST['student_id'])) ){
        $query .= " AND student_id=$student_id";
    }
    $matched = addslashes($_REQUEST['matched']);
    if ($matched != NULL){
        if($matched){
            $query .= " AND match_made.id is not null";
        } else {
            $query .= " AND match_made.id is null";
        }
    }
    $DB = new DB;
    $result = $DB->query($query);
    $results=array();
    while ($row=mysql_fetch_assoc($result)){
        array_push($results,$row);
    }
    return $results;
}

function num_open_requests($student_id){
    $DB = new DB;
    $result=$DB->query("SELECT count(*) from tutor_request left join match_made on match_made.request_id=tutor_request.id where match_made.id is null and tutor_request.student_id=$student_id");
    $row=mysql_fetch_row($result);
    return $row[0];
}

function num_active_matches($student_id=null,$tutor_id=null){
    $DB = new DB;
    if ($student_id){
        $result=$DB->query("SELECT count(*) from match_made,tutor_request where match_made.request_id=tutor_request.id and student_id=$student_id and active=1");
    } else if ($tutor_id){
        $result=$DB->query("SELECT count(*) from match_made,tutor_request where match_made.request_id=tutor_request.id and tutor_id=$tutor_id and active=1");
    }
    $row=mysql_fetch_row($result);
    return $row[0];
}

function num_dead_matches($student_id=null,$tutor_id=null){
    $DB = new DB;
    if ($student_id){
        $result=$DB->query("SELECT count(*) from match_made,tutor_request where match_made.request_id=tutor_request.id and student_id=$student_id and active=0");
    } else if ($tutor_id){
        $result=$DB->query("SELECT count(*) from match_made,tutor_request where match_made.request_id=tutor_request.id and tutor_id=$tutor_id and active=0");
    }
    $row=mysql_fetch_row($result);
    return $row[0];
}

function create_match(){
    $tutor_id=addslashes($_REQUEST["tutor_id"]);
    $request_id=addslashes($_REQUEST["request_id"]);
    $start_date=check_date($_REQUEST["start_date"]);
    $notes=addslashes($_REQUEST["notes"]);
    $times=get_times();

    $DB = new DB;
    $insert_query="INSERT INTO match_made SET
                   start_date='$start_date',
                   request_id=$request_id,
                   tutor_id=$tutor_id,
                   match_made=NOW(),
                   notes='$notes',
                   active=1
                   ";
    $DB->query($insert_query);
    $match_id=mysql_insert_id(); 

    add_tutoring_times($times,$match_id);

    send_match_emails($match_id);
}

function create_student(){
    $name=addslashes($_REQUEST["firstname"]." ".$_REQUEST["lastname"]);
    $address=addslashes($_REQUEST["address"]);
    $phone=addslashes(check_phone($_REQUEST["phone"]));
    $email=addslashes(check_email($_REQUEST["email"]));
    $grade=addslashes(check_grade($_REQUEST["grade"]));
    $password=addslashes(md5(check_password($_REQUEST["password"],$_REQUEST["password2"])));
    $avail=get_avail();
    if(! count_times($avail) ){
        show_error("You haven't selected any periods that you are available for tutoring.  Please go back and fill in at least one period.");
    }
    $username=get_username($_REQUEST["firstname"],$_REQUEST["lastname"]);

    $DB = new DB;
    $insert_query="INSERT into student set
                   name='$name',
                   address='$address',
                   phone='$phone',
                   email='$email',
                   grade='$grade',
                   username='$username',
                   password='$password'
                   ";
    $DB->query($insert_query);
   
    $student_id=mysql_insert_id(); 
    $_SESSION["student_id"]=$student_id;
    $_SESSION["logged_in"]=true;
    add_availability($avail,$student_id);
    return "You account has been created.  Your username is $username.  Make sure can remember your username and password, or write them down, so you can login to this system in the future. Click on 'Create a new tutor request' below to create your first request for tutoring.<p>Please remeber to click 'logout' at the top when you're done."; 
}

// Creates a new tutor account from form input
function create_tutor(){
    // validate form input
    $name=addslashes($_REQUEST["firstname"]." ".$_REQUEST["lastname"]);
    $address=addslashes($_REQUEST["address"]);
    $phone=addslashes(check_phone($_REQUEST["phone"]));
    $email=addslashes(check_email($_REQUEST["email"]));
    $tutor_categories=check_tutor_category($_REQUEST["tutor_category"]);
    $tutor_category=$tutor_categories[0];
    $subjects=check_subjects($_REQUEST["subjects"]);
    $grade=addslashes($_REQUEST["grade"]);
    $password=addslashes(md5(check_password($_REQUEST["password"],$_REQUEST["password2"])));
    $total_hours_desired=addslashes($_REQUEST["total_hours_desired"]);
    $avail=get_avail();
    if(! count_times($avail) ){
        show_error("You haven't selected any periods that you are available for tutoring.  Please go back and fill in at least one period.");
    }
    $username=get_username($_REQUEST["firstname"],$_REQUEST["lastname"]);

    // insert data into tables
    $DB = new DB;
    $insert_query="INSERT into tutor set
                   name='$name',
                   address='$address',
                   phone='$phone',
                   email='$email',
                   tutor_category='$tutor_category',
                   grade='$grade',
                   username='$username',
                   password='$password',
                   total_hours_desired='$total_hours_desired',
                   enabled=1
                   ";
    $DB->query($insert_query);
   
    $tutor_id=mysql_insert_id(); 
    add_availability($avail,null,$tutor_id);
    add_tutor_subjects($subjects,$tutor_id);
    add_answers(null,$tutor_id);
    $_SESSION["tutor_id"]=$tutor_id;
    $_SESSION["logged_in"]=true;
    return "Thank you for volunteering to tutor with the O-zone.  Your username is $username.  Please make sure you can remember your username and password or write them down so you can login to this system in the future.<p>We will notify you via email when we have found a student for you.  If you don't have email, we will call you.<p>Please remember to click 'logout' at the top of the page when you're done."; 
}

function create_tutor_request(){
    // validate form input
    $subjects=check_subjects($_REQUEST["subjects"]);
    $subject=addslashes($subjects[0]);
    $teacher=addslashes(check_teacher($_REQUEST['teacher']));
    $tutor_categories=check_tutor_categories($_REQUEST["tutor_category"]);
    $times_per_week=addslashes(check_times_per_week($_REQUEST["times_per_week"]));
    $student_id=addslashes($_SESSION['student_id']);

    $DB = new DB;
    $insert_query="INSERT into tutor_request set
                   student_id='$student_id',
                   teacher='$teacher',
                   subject='$subject',
                   times_per_week='$times_per_week'
                   ";
    $DB->query($insert_query);
   
    $tutor_request_id=mysql_insert_id(); 
    if($tutor_categories){
        add_request_tutor_categories($tutor_categories,$tutor_request_id);
    }
    add_answers($tutor_request_id);
    return "Your request has been submitted, and we'll try and match you with a tutor as soon as we can.  We will notify you of a match via email.  Please check your email daily for the next week.  If you don't have an email, we'll call you when we've found a tutor for you."; 
}

function mysql_date2standard_date($mysql_date){
    preg_match('/\d\d(\d\d)-(\d\d)-(\d\d)/',$mysql_date,$matches);

    return $matches[2].'-'.$matches[3].'-'.$matches[1];
}
function add_answers($tutor_request_id=null,$tutor_id=null){
    $DB = new DB;
    foreach($_REQUEST as $key => $value){
        $value=addslashes($value);
        if(preg_match('/question_(\d+)/',$key,$res)){
            $DB->query("INSERT into answer set
                        tutor_request_id='$tutor_request_id',
                        tutor_id='$tutor_id',
                        question_id='$res[1]',
                        answer='$value'");
        }
    }
}

function delete_answers($tutor_request_id=null,$tutor_id=null){
    $DB = new DB;
    if ($tutor_request_id){
        $DB->query("DELETE from answer where
                    tutor_request_id='$tutor_request_id'");
    } else if ($tutor_id){
        $DB->query("DELETE from answer where
                    tutor_id='$tutor_id'");
    }
}

function update_answers($tutor_request_id=null,$tutor_id=null){
    delete_answers($tutor_request_id,$tutor_id);
    add_answers($tutor_request_id,$tutor_id);
}

function add_request_tutor_categories($tutor_categories,$tutor_request_id){
    $DB = new DB;
    foreach($tutor_categories as $tutor_category){
        $DB->query("INSERT into tutor_request2category set
                  request_id=$tutor_request_id,
                  category_id=$tutor_category");
    }
}

function add_tutoring_times($times,$match_id){
    global $days,$periods;
    $DB = new DB;
    foreach($days as $day){
        foreach($periods as $period){
            if($times[$day][$period]){
                $DB->query("INSERT into match_time set
                          match_id=$match_id,
                          day=$day,
                          period=$period");
            }
        }
    }
}

function delete_tutoring_times($match_id){
    $DB = new DB;
    $DB->query("DELETE from match_time where
                match_id=$match_id");
}

function update_tutoring_times($times,$match_id){
    delete_tutoring_times($match_id);
    add_tutoring_times($times,$match_id);
}

function add_availability($avail,$student_id=Null,$tutor_id=NULL){
    global $days,$periods;
    $DB = new DB;
    foreach($days as $day){
        foreach($periods as $period){
            if($avail[$day][$period]){
                $DB->query("INSERT into available set
                          student_id='$student_id',
                          tutor_id='$tutor_id',
                          day='$day',
                          period='$period'");
            }
        }
    }
}

function update_availability($avail,$student_id=Null,$tutor_id=NULL){
    delete_availability($student_id,$tutor_id);
    add_availability($avail,$student_id,$tutor_id);
}

function delete_availability($student_id=Null,$tutor_id=NULL){
    $DB = new DB;
    if ($student_id){
        $DB->query("DELETE from available where
                    student_id='$student_id'");
    } else if ($tutor_id){
        $DB->query("DELETE from available where
                    tutor_id='$tutor_id'");
    }
}

function add_tutor_subjects($subjects,$tutor_id){
    $DB = new DB;
    foreach($subjects as $subject){
        $DB->query("INSERT into tutor2subject set
                  tutor_id=$tutor_id,
                  subject_id=$subject");
    }
}

function update_tutor_subjects($subjects,$tutor_id){
    delete_tutor_subjects($tutor_id);
    add_tutor_subjects($subjects,$tutor_id);
}


function delete_tutor_subjects($tutor_id){
    $DB = new DB;
    $DB->query("DELETE from tutor2subject where
                tutor_id='$tutor_id'");
}


function edit_student(){
    $student_id=addslashes($_REQUEST["student_id"]);
    $name=addslashes($_REQUEST["name"]);
    $address=addslashes($_REQUEST["address"]);
    $phone=addslashes(check_phone($_REQUEST["phone"]));
    $email=addslashes(check_email($_REQUEST["email"]));
    $grade=addslashes(check_grade($_REQUEST["grade"]));
    $avail=get_avail();
    if(! count_times($avail) ){
        show_error("You haven't selected any periods that you are available for tutoring.  Please go back and fill in at least one period.");
    }
    $username=addslashes($_REQUEST["username"]);
    $notes=addslashes($_REQUEST["notes"]);
    $signed_contract=$_REQUEST["signed_contract"]?1:0;

    if($_REQUEST["password"]){
        $password=addslashes(md5(check_password($_REQUEST["password"],$_REQUEST["password2"])));
    }

    $username_id=username_exists($username);
    if ($username_id != $student_id && $username_id !=0 ){
        show_error("That username already exists");
    }

    $DB = new DB;
    $query = "UPDATE student SET\n";
    if ($password){
        $query.="password='$password',";
    }
    $query.="name='$name',
             address='$address',
             phone='$phone',
             email='$email',
             grade='$grade',
             username='$username',
             notes='$notes',
             signed_contract=$signed_contract
             WHERE id=$student_id";
    $DB->query($query);

    update_availability($avail,$student_id);
}

function edit_match(){
    $match_id=addslashes($_REQUEST["match_id"]);
    $start_date=check_date($_REQUEST["start_date"]);
    $end_date=$_REQUEST["end_date"];
    if($end_date != ''){
        $end_date=check_date($_REQUEST["end_date"]);
    }
    $active=addslashes($_REQUEST["active"])?1:0;
    $notes=addslashes($_REQUEST["notes"]);
    $times=get_times();

    $DB = new DB;
    $DB->query("UPDATE match_made SET
               start_date='$start_date',
               end_date='$end_date',
               notes='$notes',
               active=$active
               WHERE
               id=$match_id
               ");
    update_tutoring_times($times,$match_id);
}

function edit_tutor(){
    // validate form input
    $tutor_id=addslashes($_REQUEST["tutor_id"]);
    $name=addslashes($_REQUEST["name"]);
    $address=addslashes($_REQUEST["address"]);
    $phone=addslashes(check_phone($_REQUEST["phone"]));
    $email=addslashes(check_email($_REQUEST["email"]));
    $tutor_categories=check_tutor_category($_REQUEST["tutor_category"]);
    $tutor_category=$tutor_categories[0];
    $subjects=check_subjects($_REQUEST["subjects"]);
    $grade=addslashes($_REQUEST["grade"]);
    $grade=$grade?$grade:0;
    $total_hours_desired=addslashes($_REQUEST["total_hours_desired"]);
    $avail=get_avail();
    $signed_confidentiality=$_REQUEST["signed_confidentiality"] ? 1 : 0;
    if(! count_times($avail) ){
        show_error("You haven't selected any periods that you are available for tutoring.  Please go back and fill in at least one period.");
    }
    $username=addslashes($_REQUEST["username"]);

    if($_REQUEST["password"]){
        $password=addslashes(md5(check_password($_REQUEST["password"],$_REQUEST["password2"])));
    }

    $username_id=username_exists($username);
    if ($username_id != $tutor_id && $username_id !=0 ){
        show_error("That username already exists");
    }

    $DB = new DB;
    $query = "UPDATE tutor SET\n";
    if ($password){
        $query.="password='$password',";
    }
    $query.="name='$name',
             address='$address',
             phone='$phone',
             email='$email',
             grade=$grade,
             tutor_category=$tutor_category,
             total_hours_desired=$total_hours_desired,
             username='$username',
             signed_confidentiality=$signed_confidentiality
             WHERE id=$tutor_id";
    $DB->query($query);

    update_availability($avail,null,$tutor_id);
    update_tutor_subjects($subjects,$tutor_id);
    update_answers(null,$tutor_id);
}

function edit_request(){
    $request_id=addslashes($_REQUEST["request_id"]);
    $teacher=addslashes($_REQUEST["teacher"]);
    $subjects=$_REQUEST["subjects"];
    $subject=$subjects[0];
    $times_per_week=addslashes($_REQUEST["times_per_week"]);
    $notes=addslashes($_REQUEST["notes"]);

    $DB = new DB;
    $query = "UPDATE tutor_request SET
             teacher='$teacher',
             subject=$subject,
             times_per_week=$times_per_week,
             notes='$notes'
             WHERE id=$request_id";
    $DB->query($query);
}

function cancel_request(){
    $request_id=addslashes($_REQUEST["request_id"]);

    $DB = new DB;
    $query = "DELETE FROM tutor_request
             WHERE id=$request_id";
    $DB->query($query);

    delete_answers($request_id);
}

function delete_student() {
    $student_id=addslashes($_REQUEST['student_id']);
    $DB = new DB;
    $request_result=$DB->query("SELECT id from tutor_request where student_id=$student_id");
    if(mysql_num_rows($request_result)>0){
        while($row=mysql_fetch_array($request_result)){
            $tutor_request_id=$row[0];
            $DB->query("DELETE FROM answer WHERE tutor_request_id=$tutor_request_id");
            $DB->query("DELETE FROM tutor_request2category WHERE request_id=$tutor_request_id");
            $match_result=$DB->query("SELECT id from match_made where request_id=$tutor_request_id");
            if(mysql_num_rows($match_result)>0){
                while($row=mysql_fetch_array($match_result)){
                    $match_id=$row[0];
                    $DB->query("DELETE FROM match_time WHERE match_id=$match_id");
                }
            }
            $DB->query("DELETE FROM match_made WHERE request_id=$tutor_request_id");
        }
    }
    $DB->query("DELETE FROM tutor_request WHERE student_id=$student_id");
    $DB->query("DELETE FROM available WHERE student_id=$student_id");
    $DB->query("DELETE FROM student WHERE id=$student_id");
}

function delete_tutor() {
    $tutor_id=addslashes($_REQUEST['tutor_id']);
    $DB = new DB;
    $match_result=$DB->query("SELECT id from match_made where tutor_id=$tutor_id");
    if(mysql_num_rows($match_result)>0){
        while($row=mysql_fetch_array($match_result)){
            $match_id=$row[0];
            $DB->query("DELETE FROM match_time WHERE match_id=$match_id");
        }
    }
    $DB->query("DELETE FROM match_made WHERE tutor_id=$tutor_id");
    $DB->query("DELETE FROM answer WHERE tutor_id=$tutor_id");
    $DB->query("DELETE FROM available WHERE tutor_id=$tutor_id");
    $DB->query("DELETE FROM tutor WHERE id=$tutor_id");
    $DB->query("DELETE FROM tutor2subject WHERE tutor_id=$tutor_id");
}

function delete_request() {
    $tutor_request_id=addslashes($_REQUEST['request_id']);
    $DB = new DB;
    $DB->query("DELETE FROM answer WHERE tutor_request_id=$tutor_request_id");
    $DB->query("DELETE FROM tutor_request2category WHERE request_id=$tutor_request_id");
    $match_result=$DB->query("SELECT id from match_made where request_id=$tutor_request_id");
    if(mysql_num_rows($match_result)>0){
        while($row=mysql_fetch_array($match_result)){
            $match_id=$row[0];
            $DB->query("DELETE FROM match_time WHERE match_id=$match_id");
        }
    }
    $DB->query("DELETE FROM match_made WHERE request_id=$tutor_request_id");
    $DB->query("DELETE FROM tutor_request WHERE id=$tutor_request_id");
}

function delete_match() {
    $match_id=addslashes($_REQUEST['match_id']);
    $DB->query("DELETE FROM match_time WHERE match_id=$match_id");
    $DB->query("DELETE FROM match_made WHERE id=$match_id");
}

// finds a free username using the specifed first and last name.
function get_username($firstname,$lastname){
    $firstname=strip_username_chars($firstname);
    $lastname=strip_username_chars($lastname);
    $username=strtolower("$firstname.$lastname");
    if (username_exists($username)){
        for($i=2;$i<50;$i++){
            if(!username_exists($username.$i)){
                $username=$username.$i;
                break;
            }
        }
    }
    return $username;
}

// checks to see whether the specified username already exists in the system. if it does, returns the id of the username.  Otherwise, returns 0.
function username_exists($username){
    $DB = new DB;
    $result=$DB->query("select id from student where username='$username'");
    if(mysql_num_rows($result)>0){
        $row=mysql_fetch_array($result);
        return $row[0];
    }
    mysql_free_result($result);

    $result=$DB->query("select id from tutor where username='$username'");
    if(mysql_num_rows($result)>0){
        $row=mysql_fetch_array($result);
        return $row[0];
    }
    mysql_free_result($result);

    return 0;
}

function strip_username_chars($string){
    return preg_replace("/\W/","",$string);
}

function check_tutor_categories($categories){
    if ( count($categories) < 1 ){
        show_error("You must select at least one category of tutor you'd be comfortable with.");
    }

    return $categories;
}

function check_times_per_week($times){
    if ($times <1){
        show_error("You must request at least one hour of tutoring per week");
    }
    return $times;
}

function check_teacher($teacher){
    if ($teacher == ''){
        show_error("Please enter your teacher's name");
    }
    return $teacher;
}

function check_date($date){
    preg_match('/^(\d\d)-(\d\d)-(\d\d)$/',$date,$matches);
    if(!checkdate($matches[1],$matches[2],$matches[3])){
        show_error("Please enter a valid date of the format mm-dd-yy");
    }
    return $matches[3].'-'.$matches[1].'-'.$matches[2];
}

function check_password($password,$password2){
    if (strlen($password) > 20 || strlen($password) < 6){
        show_error("Your password must be between 6 and 20 characters");
    }
    if ($password != $password2){
        show_error("Your password fields don't match.  Please go back and try again.");
    }
    return $password;
}

function check_tutor_category($categories){ //XXX it would be better if this actually checked against the database, but whatever
    if (count($categories) < 1 || $categories[0] < 1){
        show_error("You need to select a category.");
    }
    return $categories;
}

function check_subjects($subjects){ //XXX it would be better if this actually checked against the database, but whatever
    if (count($subjects)<1 || $subjects[0] == ''){
        show_error("You need to select at least one subject.");
    }
    return $subjects;
}

function check_grade($grade){
    if ($grade < 9 || $grade > 12){
        show_error("You need to select a grade.");
    }
    return $grade;
}

function check_email($email){
    if (!preg_match("/\w+@\w+/",$email) && $email){
        show_error("It appears you entered an invalid email address.  Please check it.");
    }
    return $email;
}

function check_phone($phone){
    if (preg_match("/(\d\d\d).*(\d\d\d).*(\d\d\d\d)/", $phone, $regs)){
        $phone = "$regs[1]-$regs[2]-$regs[3]";
    } else {
        show_error("It appears you entered an invalid phone number. Please make sure you included an area code.");
    }
    return $phone;
}

function get_student_avail($student_id){
    return retrieve_avail('student',$student_id);
}

function get_tutor_avail($student_id){
    return retrieve_avail('tutor',$student_id);
}

function get_match_times($match_id){
    $DB=new DB;
    $result=$DB->query("SELECT * from match_time WHERE match_id=$match_id");
    while($row=mysql_fetch_array($result)){
        $a[ $row["day"] ][ $row["period"] ]=1;
    }
    return $a;
}

function retrieve_avail($type,$id){
    $DB=new DB;
    if ($type == 'student'){
        $result=$DB->query("SELECT * from available WHERE student_id='$id'");
    } else if ($type == 'tutor'){
        $result=$DB->query("SELECT * from available WHERE tutor_id='$id'");
    }
    while($row=mysql_fetch_array($result)){
        $a[ $row["day"] ][ $row["period"] ]=1;
    }
    return $a;
}

// accepts busy array from get_*_busy and prints html string of form "Tu-3 W-4 Th-8", with busy periods red ($busy=1), and common periods black ($busy=2)
function print_busy($busy_arr){
    global $dayNames;
    $busy_arr=msort($busy_arr,0);
    foreach ($busy_arr as $busy_el){
        $day=$busy_el[0];
        $period=$busy_el[1];
        $busy=$busy_el[2];
        if($dayNames[$day] == 'Thursday' || $dayNames[$day] == 'Tuesday'){
            $day_abbrev=substr($dayNames[$day],0,2);
        } else {
            $day_abbrev=substr($dayNames[$day],0,1);
        }
        if ($busy==1){
            print " <span style='color:#f00'>$day_abbrev-$period</span>";
        } else {
            print " $day_abbrev-$period";
        }
    }
}

// returns an array of days which is an array of periods, which are set to 1 for busy
function get_student_busy($student_id){
    $query="SELECT day, period
            FROM student, tutor_request, match_made, match_time
            WHERE student.id=tutor_request.student_id
            AND match_made.request_id =tutor_request.id
            AND match_time.match_id=match_made.id
            AND student.id=$student_id
            AND match_made.active=1";
    $DB = new DB;
    $results=$DB->query($query);
    $busy=array();
    while($row=mysql_fetch_array($results)){
        $busy[ $row["day"] ][ $row["period"] ]=1;
    }
    return $busy;
}

// returns an array of days which is an array of periods, which are set to 1 for busy
function get_tutor_busy($tutor_id){
    $query="SELECT day, period
            FROM match_made, match_time
            WHERE match_time.match_id=match_made.id
            AND match_made.active=1
            AND match_made.tutor_id=$tutor_id";
    $DB = new DB;
    $results=$DB->query($query);
    $busy=array();
    while($row=mysql_fetch_array($results)){
        $busy[ $row["day"] ][ $row["period"] ]=1;
    }

    return $busy;
}

// returns array($day,$period,$busy) where $busy is in (1,2)
// 1 means the tutor has the time busy, 2 means they both have it free
function get_student_tutor_busy($tutor_id,$student_id=0){
    global $days, $periods;
    $tutor_busy=get_tutor_busy($tutor_id);
    $student_busy=get_student_busy($student_id);
    $common_times=get_common_times($tutor_id,$student_id);
    $result=array();
    foreach ($common_times as $common_time){
        $day=$common_time[0];
        $period=$common_time[1];
        array_push($result, array($day,$period,2));
    }
    foreach ($days as $day){
        foreach ($periods as $period){
            if($tutor_busy[$day][$period]==1){
                array_push($result, array($day,$period,1));
            }
        }
    }
    return $result;
}

// Prints a list of tutoring times in the form "Tu-3 W-4" for the specified match id
function print_match_times($match_id){
    global $days, $periods, $dayNames;

    $match_times=get_match_times($match_id);
    foreach ($days as $day){
        foreach ($periods as $period){
            if($match_times[$day][$period]){
                if($dayNames[$day] == 'Thursday' || $dayNames[$day] == 'Tuesday'){
                    $day_abbrev=substr($dayNames[$day],0,2);
                } else {
                    $day_abbrev=substr($dayNames[$day],0,1);
                }
                print " $day_abbrev-$period";
            }
        }
    }
}

// Processes form input for the availability table and returns an array of days and periods with boolean values for availability
function get_avail(){
    global $days,$periods;
    $avail=array();
    foreach ($days as $day){
        foreach ($periods as $period){
            if($_REQUEST["avail-$day-$period"] == "on"){
                $avail[$day][$period]=1;
            }
        }
    }
    return $avail;
}

// Processes form input for the tutoring times table and returns an array of days and periods with boolean values for availability
function get_times(){
    global $days,$periods;
    $times=array();
    foreach ($days as $day){
        foreach ($periods as $period){
            if($_REQUEST["time-$day-$period"] == "on"){
                $times[$day][$period]=1;
            }
        }
    }
    return $times;
}

function login(){
    $username=$_REQUEST["username"];
    $password=md5($_REQUEST["password"]);
    $DB = new DB;
    global $page;

    $result = $DB->query("SELECT id FROM student where username='$username' and password='$password'");
    if (mysql_affected_rows() > 0){
        $row=mysql_fetch_row($result);
        $_SESSION["student_id"]=$row[0];
        $_SESSION["logged_in"]=true;
        $page='student_home';
        return "Please remember to click 'logout' at the top when you're done";
    }
    $result = $DB->query("SELECT id FROM tutor where username='$username' and password='$password'");
    if (mysql_affected_rows() > 0){
        $row=mysql_fetch_row($result);
        $_SESSION["tutor_id"]=$row[0];
        $_SESSION["logged_in"]=true;
        $page='tutor_home';
        return "Please remember to click 'logout' at the top when you're done";
    }
    $page='main';
    show_error("Invalid username or password");
    return;
}

function logout(){
    $_SESSION = array();
    session_destroy();
}

// Returns the number of times in an availibility matrix that are set to 1
function count_times($a){
    global $days,$periods;
    $found=0;
    foreach($days as $day){
        foreach($periods as $period){
            $found += $a[$day][$period];
        }
    }
    return $found;
}

function match_info($match_id){
    $DB=new DB;
    $result=$DB->query("select * from match_made where id=$match_id");
    $results=mysql_fetch_assoc($result);
    return $results;
}

function search_matches(){
    global $days,$periods;
    $query="select distinct match_made.*,
            UNIX_TIMESTAMP(match_made.start_date) as start_dateunix,
            UNIX_TIMESTAMP(match_made.match_made) as match_madeunix,
            student.name,tutor.name
            from match_made, student, tutor, tutor_request, match_time
            where match_made.request_id = tutor_request.id
            and tutor_request.student_id = student.id
            and match_made.tutor_id = tutor.id
            and match_made.id=match_time.match_id";

    if ( ($student_id=$_SESSION['student_id']) || ($student_id=addslashes($_REQUEST['student_id'])) ){
        $query .= " AND student_id=$student_id";
    }
    if ( ($tutor_id=$_SESSION['tutor_id']) || ($tutor_id=addslashes($_REQUEST['tutor_id'])) ){
        $query .= " AND tutor_id=$tutor_id";
    }
    $active=addslashes($_REQUEST['active']);
    if ($active != NULL){
        $query .= " AND active=$active";
    }

    $avail=get_avail();
    if (count_times($avail)){
        $query .= " AND (";
        $count = 0;
        foreach ($days as $day){
            foreach ($periods as $period){
                if($avail[$day][$period]){
                    if($count++ > 0){
                        $query .= "OR ";
                    }
                    $query.= " (day = $day AND period = $period) ";
                }
            }
        }
        $query .= ")";
    }
    
    $query.= " ORDER BY start_date desc";

    $DB=new DB;
    $result=$DB->query($query);

    $results=array();
    while($row=mysql_fetch_assoc($result)){
        array_push($results,$row);
    }
    return $results;
}

function show_error($error){
    //XXX This needs completing
    die("error: $error");
}


function show_header(){
    include('header.php');
}

function send_email($to,$from,$subject,$body){
    mail($to, $subject, $body,"From: $from");
}

function send_match_emails($match_id){
    // get the necessary variables for the templates
    global $coordinatorEmail, $coordinatorPhone, $coordinatorName, $domainPath;
    $match_info = match_info($match_id);
    $request_info = request_info($match_info['request_id']);
    $student_info = student_info($request_info['student_id']);
    $tutor_info = tutor_info($match_info['tutor_id']);

    if ($student_info['email']){
        // fill in the student email body template
        ob_start();
        include('student_match_email_template.php');
        $student_body = ob_get_contents();
        ob_clean();

        // send the email
        send_email($student_info['email'],$coordinatorEmail,"I've found a tutor for you",$student_body);
    }
 
    if ($tutor_info['email']){
        // fill in the tutor email body template
        ob_start();
        include('tutor_match_email_template.php');
        $tutor_body = ob_get_contents();
        ob_clean();

        // send the email
        send_email($tutor_info['email'],$coordinatorEmail,"I've found a student for you to tutor",$tutor_body);
    }
}

// Allows a student or tutor to confirm they've received an email about a new match
function confirm() {
    $student_id=$_REQUEST['student_id'];
    $tutor_id=$_REQUEST['tutor_id'];
    $match_id=$_REQUEST['match_id'];
    $match_info=match_info($match_id);

    if ( !$match_id ){
        return;
    }

    if ($student_id){
        $request_info=request_info($match_info['request_id']);

        if ($student_id==$request_info['student_id']){
            mark_match_student_acknowledged($match_id);
        }
    } else if ($tutor_id){
        if ($tutor_id == $match_info['tutor_id']){
            mark_match_tutor_acknowledged($match_id);
        }
    }
}

function mark_match_student_acknowledged($match_id){
    $query = "UPDATE match_made SET student_acknowledged = NOW() where id=$match_id";
    $DB = new DB;
    $DB->query($query);
}

function mark_match_tutor_acknowledged($match_id){
    $query = "UPDATE match_made SET tutor_acknowledged = NOW() where id=$match_id";
    $DB = new DB;
    $DB->query($query);
}

function get_email_schedule($match_id){
    $match_times=get_match_times($match_id);

    global $days, $periods, $dayNames;
    foreach ($days as $day){
        foreach ($periods as $period){
            if($match_times[$day][$period]){
                if ($period == 8){
                    $result .= "$dayNames[$day] - After school (3:05-4)\n";
                } else {
                    $result .= "$dayNames[$day] - Period $period\n";
                }
            }
        }
    }
    return $result;
}

// Returns a list of matches for which the student needs to be called
function get_students_to_call(){
    global $quick_match_call_time,$unresponded_match_call_time;
    // get a list of all the current matchesj
    $match_info_list=search_matches();
    $match_ids=array();

    // step through each of the matches
    foreach($match_info_list as $match_info){
        $show=0;
        // Skip anyone who has already acknowledged
        if( $match_info["student_acknowledged"] ){
            continue;
        }

        $request_info=request_info($match_info["request_id"]);
        $student_info=student_info($request_info["student_id"]);

        // check to see if the match starts really soon
        if ( $match_info["start_dateunix"] <
             (time() + 60*60*$quick_match_call_time) ){
            $show=1;
        }

        // check to see if the student hasn't clicked the confirmation link quickly enough
        if ( ($match_info["match_madeunix"] + 60*60*$unresponded_match_call_time) <
              time() ){
            $show=1;
        }
        
        // check to see if the student doesn't have an email address
        if ( ! preg_match("/(\S+)@(\S+)/",$student_info["email"]) ){
            $show=1;
        }

        if ($show){
            array_push($match_ids,$match_info["id"]);
        }
    }
    return $match_ids;
}

// Returns a list of students that need calling
function get_tutors_to_call(){
    global $quick_match_call_time,$unresponded_match_call_time;
    // get a list of all the current matchesj
    $match_info_list=search_matches();
    $match_ids=array();

    // step through each of the matches
    foreach($match_info_list as $match_info){
        $show=0;
        // Skip anyone who has already acknowledged
        if( $match_info["tutor_acknowledged"] ){
            continue;
        }

        $tutor_info=tutor_info($match_info["tutor_id"]);

        // check to see if the match starts really soon
//        print  "start: ". $match_info["start_dateunix"] ." ".
//              "cutoff: ".(time() + 60*60*$quick_match_call_time)."<br>\n";
        if ( $match_info["start_dateunix"] <
             (time() + 60*60*$quick_match_call_time) ){
            $show=1;
        }

        // check to see if the tutor hasn't clicked the confirmation link quickly enough
        if ( ($match_info["match_madeunix"] + 60*60*$unresponded_match_call_time) <
              time() ){
            $show=1;
        }
        
        // check to see if the tutor doesn't have an email address
        if ( ! preg_match("/(\S+)@(\S+)/",$tutor_info["email"]) ){
            $show=1;
        }

        if ($show){
            array_push($match_ids,$match_info["id"]);
        }
    }
    return $match_ids;
}

// save the calling screen table changes
function save_calling_screen(){
    $matches=search_matches();
    foreach($matches as $match_info){
        $student_ack_notes=$_REQUEST[ $match_info["id"]."_notes_student" ];
        $tutor_ack_notes=$_REQUEST[ $match_info["id"]."_notes_tutor" ];
        if( $student_ack_notes ){
            update_student_match_ack_notes($match_info["id"],$student_ack_notes);
        }
        if( $tutor_ack_notes ){
            update_tutor_match_ack_notes($match_info["id"],$tutor_ack_notes);
        }

        $student_confirmed=$_REQUEST[ $match_info["id"]."_confirm_student" ];
        $tutor_confirmed=$_REQUEST[ $match_info["id"]."_confirm_tutor" ];
        if ( $student_confirmed ){
            mark_match_student_acknowledged($match_info["id"]);
        }
        if ( $tutor_confirmed ){
            mark_match_tutor_acknowledged($match_info["id"]);
        }
    }
}

function update_student_match_ack_notes($match_id,$notes){
    $notes=addslashes($notes);
    $DB = new DB;
    $result = $DB->query("UPDATE match_made SET student_ack_note='$notes' WHERE id=$match_id");
}

function update_tutor_match_ack_notes($match_id,$notes){
    $notes=addslashes($notes);
    $DB = new DB;
    $result = $DB->query("UPDATE match_made SET tutor_ack_note='$notes' WHERE id=$match_id");
}

// fetches all students who's matches started during the time period listed, who's matches lasted longer than the given number of days
function get_students_tutored_longer_than($start_date,$end_date,$num_days){
    $DB = new DB;

    $query = "select student.name,subject.subject_name as subject,(to_days(end_date)- to_days(start_date))  as length, match_made.start_date as start_date, match_made.end_date as end_date from match_made,tutor_request,student,subject where match_made.request_id = tutor_request.id and tutor_request.student_id = student.id and tutor_request.subject=subject.id and match_made >= '$start_date' and match_made <= '$end_date' and (to_days(end_date) - to_days(start_date) >= $num_days or to_days(end_date) = 0 or end_date is null)";

    $result = $DB->query($query);
    $results = array();
    while ($row=mysql_fetch_array($result)){
        array_push($results,$row);
    }

    return $results;
}

function get_matches_subject_counts($start_date,$end_date){
    $DB = new DB;

    $query = "select subject_name, count(*) as count from match_made,tutor_request,subject where match_made.request_id = tutor_request.id and tutor_request.subject = subject.id and  match_made > '$start_date' and match_made < '$end_date' group by subject.id";
    $result = $DB->query($query);

    $results = array();
    while ($row=mysql_fetch_array($result)){
        $results[$row['subject_name']] = $row['count'];
    }
    return $results;
}

class DB {
    function DB() {
        $this->link=$this->connect();
    }

    function query($query){
        //print "<pre>";
        //print "<br/>$query<br/>\n";
        //print_r(debug_backtrace());
        //print "</pre>";
        $result = mysql_query($query) //XXX this needs to refer to open connection
           or die("Invalid query: $query :" . mysql_error() . " at ");
        return $result;
    }

    function connect(){
        $link = mysql_connect(DB_HOST,DB_USER,DB_PASS)
            or die("Couldn't connect to database: ".mysql_error());
        mysql_select_db(DB_NAME);
    }
}
?>
