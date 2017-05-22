<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 5/20/17
 * Time: 1:34 AM
 */

if (isset($_GET["action"]) && isset($_GET["id"]) && $_GET["action"] == "get_user") // if the get parameter action is get_user and if the id is set, call the api to get the user information
{
    $user_info = file_get_contents('http://86.52.212.76:8113/iTax/RestApiTest.php?action=get_user&id=' . $_GET["id"]);
    $user_info = json_decode($user_info, true);

    // THAT IS VERY QUICK AND DIRTY !!!!!
    ?>
    <table>
        <tr>
            <td>Name: </td><td> <?php echo $user_info["last_name"] ?></td>
        </tr>
        <tr>
            <td>First Name: </td><td> <?php echo $user_info["first_name"] ?></td>
        </tr>
        <tr>
            <td>Age: </td><td> <?php echo $user_info["age"] ?></td>
        </tr>
    </table>
    <a href="http://86.52.212.76:8113/iTax/RestApiUserTest.php?action=get_userlist" alt="user list">Return to the user list</a>
    <?php
}
else // else take the user list
{
    $user_list = file_get_contents('http://86.52.212.76:8113/iTax/RestApiTest.php?action=get_user_list');
    $user_list = json_decode($user_list, true);
    // THAT IS VERY QUICK AND DIRTY !!!!!
    ?>
    <ul>
        <?php foreach ($user_list as $user): ?>
            <li>
                <a href=<?php echo "86.52.212.76:8113/iTax/RestApiUserTest.php?action=get_user&id=" . $user["id"]  ?> alt=<?php echo "user_" . $user_["id"] ?>><?php echo $user["name"] ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}

?>