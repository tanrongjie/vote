<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/28
 * Time: 18:07
 */

header('Access-Control-Allow-Origin: http://192.168.0.107');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    return true;
}

include 'common/common.php';
include 'dao/PlayerVoteDao.php';

$activity_id = isset($_POST['activity_id']);
$player_id   = isset($_POST['player_id']);

if ('' == $activity_id) {
    echo sendJsonInfo(600, '', '未获取到活动id');
    exit();
}

if ('' == $player_id) {
    echo sendJsonInfo(600, '', '未获取到选手id');
    exit();
}

$PlayerVoteDao = new PlayerVoteDao();

/*
 * 投票前校验(1:活动是否发布,2:活动是否开始,3:活动是否结束)
 */
$check = $PlayerVoteDao->vote_check($activity_id);

$activity_flag = isset($check['activity_flag']) ? $check['activity_flag'] : 9;
$starttime     = isset($check['activity_starttime']) ? strtotime($check['activity_starttime']) : time();
$endtime       = isset($check['activity_endtime']) ? strtotime($check['activity_endtime']) : time();

if (9 == $activity_flag) {
    echo sendJsonInfo(600, '', '活动未发布');
    exit();
}

if (time() - $starttime < 0) {
    echo sendJsonInfo(600, '', '活动未开始');
    exit();
} else if (time() - $endtime > 0) {
    echo sendJsonInfo(600, '', '活动已结束');
    exit();
}

$data = array(
    'activity_id' => $activity_id,
    'player_id'   => $player_id
);
if ($PlayerVoteDao->vote($data)) {
    echo sendJsonInfo(200, '', '投票成功');
} else {
    echo sendJsonInfo(600, '', '投票失败');
}

