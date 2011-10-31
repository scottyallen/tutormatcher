<div class=title>Main Page</div>
<a href="?page=list_students">List Students</a><br/>
<a href="?page=list_tutors">List Tutors</a><br/>
<a href="?page=match_list">Match Requests</a><br/>
<a href="?page=list_matches">List Existing Matches</a><br/>
<table>
<tr>
    <td>Search Students:</td>
    <td>Search Tutors:</td>
</tr>
<tr><td valign=top>
    <form method="get">
        <input type="hidden" name="page" value="list_students"/>
        <input type="hidden" name="action" value="search_students"/>
        <table>
            <tr>
                <td>Name:</td>
                <td><input name="name"/></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input name="email"/></td>
            </tr>
            <tr>
                <td>Grade:</td>
                <td><? grade_select() ?></td>
            </tr>
            <tr>
                <td>Username:</td>
                <td><input name="username"/></td>
            </tr>
            <tr>
                <td>Signed Contract:</td>
                <td>
                    <select name="signed_contract">
                        <option value=""/>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Notes:</td>
                <td><input name="notes"/></td>
            </tr>
        </table>
        <input type="submit" value="Search"/>
    </form>
</td><td>
    <form method="get">
        <input type="hidden" name="page" value="list_tutors"/>
        <input type="hidden" name="action" value="search_tutors"/>
        <table>
            <tr>
                <td>Name:</td>
                <td><input name="name"/></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input name="email"/></td>
            </tr>
            <tr>
                <td>Category:</td>
                <td><? tutor_category_select() ?></td>
            </tr>
            <tr>
                <td>Username:</td>
                <td><input name="username"/></td>
            </tr>
            <tr>
                <td>Signed Confidentiality Agreement:</td>
                <td>
                    <select name="signed_confidentiality">
                        <option value=""/>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Enabled:</td>
                <td>
                    <select name="enabled">
                        <option value=""/>
                        <option value="1" selected>Yes</option>
                        <option value="0">No</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Notes:</td>
                <td><input name="notes"/></td>
            </tr>
        </table>
        <input type="submit" value="Search"/>
    </form>
</td></tr>
<tr><td>
Search Matches:
<td></tr>
<tr><td>
    <form method="get">
        <input type="hidden" name="page" value="list_matches"/>
        <input type="hidden" name="action" value="search_matches"/>
        <table>
            <tr>
                <td valign=top>Meeting Time:</td>
                <td><? availability_matrix() ?></td>
            </tr>
        </table>
        <input type="submit" value="Search"/>
    </form>
</td></tr>
</table>
Login:
<form method="post">
    <input type="hidden" name="action" value="admin_login">
        <table>
        <tr>
            <td>Username: </td>
            <td><input name="username"/></td>
        </tr>
        <tr>
            <td>Password: </td>
            <td><input name="password"/></td>
        </tr>
    </table>
    <input type="submit" value="Login"/>
</form>
