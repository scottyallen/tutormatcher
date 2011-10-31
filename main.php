<div class=title>Main Page</div>

<ul>
<li><a href="<? echo $PHP_SELF ?>?page=new_student">Create a new student account</a>
<li><a href="<? echo $PHP_SELF ?>?page=new_tutor">Create a new tutor account</a>
</ul>

Login:
<form method="post">
    <input type="hidden" name="action" value="login">
        <table>
        <tr>
            <td>Username: </td>
            <td><input name="username"/></td>
        </tr>
        <tr>
            <td>Password: </td>
            <td><input name="password" type="password"/></td>
        </tr>
    </table>
    <input type="submit" value="Login"/>
</form>
