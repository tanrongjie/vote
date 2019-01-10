<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/26
 * Time: 11:46
 */

header('Access-Control-Allow-Origin: http://192.168.0.107');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    return true;
}


include 'common/common.php';
include 'dao/PlayerDao.php';


$method = isset($_POST['method']) ? $_POST['method'] : '';
if ('' == $method) {
    return sendJsonInfo(600, '', '未获取到方法');
}

$playerDao = new PlayerDao();
switch ($method) {
    case 'getPlayers':
        echo getPlayers($playerDao);
        break;
    case 'addPlayer':
        echo addPlayer($playerDao);
        break;
    case 'updatePlayer':
        echo updatePlayer($playerDao);
        break;
    case 'delActivity':
        echo delPlayer($playerDao);
        break;
    default :
        return sendJsonInfo(600, '', '未获取到方法');
}


//*************************************业务方法*********************************
/**
 * 获取选手信息
 * @param null $playerDao
 * @return string
 */
function getPlayers($playerDao = null) {
    if (null == $playerDao) {
        $playerDao = new PlayerDao();
    }
    //选手编号
    $number = isset($_POST['number']) ? $_POST['number'] : '';
    $where  = "";
    if ('' != $number) {
        $where = " and number = '" . $number . "' ";
    }
    $result = $playerDao->getPlayers($where);
    if ('' != $result && is_array($result)) {
        return sendJsonInfo(200, $result, '获取成功');
    }
    return sendJsonInfo(600, '', '获取失败');
}


/**
 * 添加选手
 * @param null $playerDao
 * @return string
 */
function addPlayer($playerDao = null) {
    if (null == $playerDao) {
        $playerDao = new PlayerDao();
    }

    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $data = json_decode($data, true);

    if ('' == $data || !is_array($data) || count($data) <= 0) {
        return sendJsonInfo(600, '', '未获取到添加数据');
    }
    $result = $playerDao->addPlayer($data);

    if ($result) {
        return sendJsonInfo(200, '', '添加成功');
    }
    return sendJsonInfo(600, '', '添加失败');
}


/**
 * 修改选手
 * @param null $playerDao
 * @return string
 */
function updatePlayer($playerDao = null) {
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $data = json_decode($data, true);

    if (null == $playerDao) {
        $playerDao = new PlayerDao();
    }

    if ('' == $data || !is_array($data) || count($data) <= 0) {
        return sendJsonInfo(600, '', '未获取到修改数据');
    }

    $result = $playerDao->updPlayer($data);
    if ($result) {
        return sendJsonInfo(200, '', '修改成功');
    }
    return sendJsonInfo(600, '', '修改失败');
}

/**
 * 删除选手
 * @param null $playerDao
 * @return string
 */
function delPlayer($playerDao = null) {
    $id = $_POST['id'];
    if ('' == $id) {
        return sendJsonInfo(600, '', '未获取到id');
    }

    if (null == $playerDao) {
        $playerDao = new PlayerDao();
    }

    if ($playerDao->delPlayer($id)) {
        return sendJsonInfo(200, '', '删除成功');
    } else {
        return sendJsonInfo(600, '', '删除失败');
    }
}