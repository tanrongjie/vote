<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/26
 * Time: 15:45
 */


header('Access-Control-Allow-Origin: http://192.168.0.107');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    return true;
}


include 'common/common.php';
$file     = isset($_FILES['file']) ? $_FILES['file'] : '';          //得到传输的数据
$old_file = isset($_POST['old_file']) ? $_POST['old_file'] : '';    //旧文件

if ('' == $file) {
    echo sendJsonInfo(600, '', '上传失败');
    exit();
}
//得到文件名称
$name       = $file['name'];
$type       = strtolower(substr($name, strrpos($name, '.') + 1)); //得到文件类型，并且都转化成小写
$allow_type = array('jpg', 'jpeg', 'png'); //定义允许上传的类型
//判断文件类型是否被允许上传
if (!in_array($type, $allow_type)) {
    //如果不被允许，则直接停止程序运行
    echo sendJsonInfo(600, '', '文件格式类型错误');
    exit();
}

//判断是否是通过HTTP POST上传的
if (!is_uploaded_file($file['tmp_name'])) {
    //如果不是通过HTTP POST上传的
    echo sendJsonInfo(600, '', '请使用post上传');
    exit();
}

//判断图片大小
if ($_FILES['file']['size'] > 131072) {     //100kb (size大小单位为:byte 字节) 1048576(1MB)
    echo sendJsonInfo(600, '', '图片大小不能超过100kb');
    exit();
}

//上传文件的存放路径
$upload_path = "img/" . date('Ymd');
if (!is_dir($upload_path)) {
    mkdir($upload_path);
}

//设置文件名
$filename = md5($file['name'] . time()) . '.' . $type;
$filepath = $upload_path . "/" . $filename;

/**
 * 获取项目名
 */
$project = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
if ('' != $project) {
    $project = substr($project, 0, strrpos($project, '/'));
}

if ('' != $old_file) {
    $old_file = substr($old_file, 0, strrpos($old_file, $_SERVER['SERVER_NAME'] . '/' . $project));
}

//开始移动文件到相应的文件夹
//SERVER_NAME: 域名
$imgUrl = $_SERVER['SERVER_NAME'] . '/' . $project . '/' . $filepath;
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    echo sendJsonInfo(200, $imgUrl, '上传成功');
} else {
    echo sendJsonInfo(600, '', '上传失败');
}