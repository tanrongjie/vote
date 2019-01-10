<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/29
 * Time: 11:33
 */

header('Access-Control-Allow-Origin: http://192.168.0.107');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    return true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="Activity.php" method="post">
    <input type="text" name="method" value="getActivityPlayer"/>
    <input type="text" name="id" value="1"/>
    <button>提交</button>
</form>
</body>
</html>
