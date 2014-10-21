<?php
if (isset($_SESSION['username'])) {
    if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_users')) {
        echo '<h2>No access</h2>';
        return;
    }

    $id = $GLOBALS['db']->escape($_GET['id']);
    $user = new User($id);

    echo '<div id="pop_up">';

    echo '<form id="user_form">';
    echo '<table border="0" width="100%" cellspacing="5">';
    echo '<tr><td style="text-align:right;">Name</td><td style="text-align:left;"><input value="' . $user->getName() . '" name="name" type="text" size="30" maxlength="100"></td></tr>';
    echo '<tr><td style="text-align:right;">Email</td><td style="text-align:left;"><input value="' . $user->getEmail() . '" name="email" type="text" size="30" maxlength="100"></td></tr>';
    echo '<tr><td style="text-align:right;">Password</td><td style="text-align:left;"><input value="" name="pass" type="password" size="30" maxlength="100"></td></tr>';
    echo '</table>';

    echo '<p style="text-align: center;"><input type="submit" value="Save"><input type="reset" value="Reset"></p>';

    echo '</form>';
    echo '</div>';
}

