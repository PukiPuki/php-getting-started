<?php
function showUser() {
    return '<div>
        <div class="container">
            <h2 class="w3-text-teal"> Add User </h2>
            <form action="admin.php" method="POST">
                <div class="container">
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>
                    <label for="password"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required>
                    <label for="phone"><b>Phone Number</b></label>
                    <input type="text" placeholder="Enter Phone Number" name="phone" required>
                    <label for="isAdmin"><b>Admin Privileges</b></label>
                    <input type="hidden" name="isAdmin" value="0"> </input>
                    <input type="checkbox" name="isAdmin" value="1"> Admin </input>
                    <button type="submit" name="add_user">Add User</button>
                </div>
            </form>
        </div>

        <form action="admin.php" method="POST">
            <div class="container">
                <h2 class="w3-text-teal"> Edit User </h2>
                <label for="username"><b>Username</b></label>
                <input type="text" placeholder="Enter User to Edit" name="username" required>

                <label for="newphone"><b>New Phone Number</b></label>
                <input type="text" placeholder="Enter New Phone Number" name="newphone" required>

                <label for="isAdmin"><b>Admin Privileges</b></label>
                <input type="hidden" name="isAdmin" value="0"> </input>
                <input type="checkbox" name="isAdmin" value="1"> Admin </input>
                <button type="submit" name="edit_user">Edit User</button>
            </div>
        </form>

        <div class="container">
            <h2 class="w3-text-teal"> Remove User </h2>
            <form action="admin.php" method="POST">
                <div class="container">
                    <label for="username"><b>Username</b></label>
                    <input type="text" placeholder="Enter User to Remove" name="username" required>
                    <button type="submit" name="remove_user">Remove User</button>
                </div>
            </form>
    </div>';

}
?>
