<html>
<head>
<style>
    div.header img{
        float: right;
    }
    div.header {
        height: 80px;
        text-align: center;
        border: 4px double black;
        color: #043fd6;
        font-size: 24pt;
        text-align: center;
        vertical-align: center;
        padding: 10px;
        margin: 20px;
    }
    div.title {
        font-size: 20pt;
        font-weight: bold;
    }
    div.message {
        font-size: 15pt;
        font-weight: bold;
    }
    div.main {
        border: 1px black solid;
        background: #eee;
        padding: 10px;
        margin: 20px;
    }
    table.border {
        border: 1px solid black;
        border-collapse: collapse;
    }
    table.border td {
        border: 1px solid black;
        padding: 5px;
    }
    table.border th {
        background: #fff;
        border: 1px solid black;
        padding: 5px;
    }
    div.menu {
        font-size: 14pt;
    }
</style>
</head>
<body>
<div class="header">
    <img src="newlogo.gif"/>
    Boulder High School - Tutor Matching System<br>
    <div class=menu>
    <? if ($_SESSION['logged_in'] == true && $_SESSION['student_id']) { 
        $student_info = student_info($_SESSION['student_id']);
        ?>
        Logged in as: <? echo $student_info['name']; ?><br>
        <a href="?page=student_home">Main</a> |
        <a href="?action=logout&page=main">Logout</a>
    <? } ?>
    <? if ($_SESSION['logged_in'] == true && $_SESSION['tutor_id']) { 
        $tutor_info = tutor_info($_SESSION['tutor_id']);
        ?>
        Logged in as: <? echo $tutor_info['name']; ?><br>
        <a href="?page=tutor_home">Main</a> |
        <a href="?action=logout&page=main">Logout</a>
    <? } ?>
    </div>
</div>
</body>
</html>
