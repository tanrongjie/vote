<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/24
 * Time: 13:51
 */

header('Access-Control-Allow-Origin: http://192.168.0.107');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    return true;
}

include "common/common.php";
include "dao/ActivityDao.php";


$method = isset($_POST['method']) ? $_POST['method'] : '';
if ('' == $method) {
    return sendJsonInfo(600, '', '未获取到方法');
}

/**
 * getActivity: 获取活动
 * addActivity: 添加活动
 * updActivity: 更新活动
 * delActivity: 删除活动
 * getVoteTotal:获取总票数
 */
$activity = new ActivityDao();
switch ($method) {
    case 'getActivity':
        echo getActivity($activity);
        break;
    case 'addActivity':
        echo addActivity($activity);
        break;
    case 'updActivity':
        echo updateActivity($activity);
        break;
    case 'delActivity':
        echo delActivity($activity);
        break;
    case 'getVoteTotal':
        echo getVoteTotal($activity);
        break;
    case 'getActivityPlayer':
        echo getActivityPlayer($activity);
        break;
    default :
        return sendJsonInfo(600, '', '未获取到方法');
}


/**
 * 获取活动内容
 * @param null $activity
 * @return string
 */
function getActivity($activity = null) {
    $id    = isset($_POST['id']) ? $_POST['id'] : '';
    $where = "";
    if ('' != $id && is_int($id)) {
        $where = " and id = '" . $id . "' ";
    }

    if (null == $activity) {
        $activity = new ActivityDao();
    }

    $data = $activity->getActivity($where);

    if ($data != '' && is_array($data) && count($data) > 0) {
        foreach ($data as $key => $value) {
            $starttime = isset($data[$key]['activity_starttime']) ? strtotime($data[$key]['activity_starttime']) : time();
            $endtime   = isset($data[$key]['activity_endtime']) ? strtotime($data[$key]['activity_endtime']) : time();

            //如果状态为发布
            if (9 == $data[$key]['activity_flag']) {
                $data[$key]['status'] = '未发布';
            }
            if (time() - $starttime < 0) {
                $data[$key]['status'] = '未开始';
            } else if (time() - $endtime > 0) {
                $data[$key]['status'] = '已结束';
            } else if (time() - $starttime >= 0 && time() - $endtime <= 0) {
                $data[$key]['status'] = '进行中';
            }

        }
        return sendJsonInfo(200, $data, '获取成功');
    }
    return sendJsonInfo(600, array(), '获取失败');
}


/**
 * 添加活动
 * @param null $activity
 * @return string
 */
function addActivity($activity = null) {
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $data = json_decode($data, true);
    if ('' == $data || !is_array($data) || count($data) <= 0) {
        echo sendJsonInfo(600, '', '未获取到添加数据');
    }

    if (null == $activity) {
        $activity = new ActivityDao();
    }

    if ($activity->addActivity($data)) {
        return sendJsonInfo(200, '', '添加成功');
    }
    return sendJsonInfo(600, '', '添加失败');
}

/**
 * 修改活动
 * @param null $activity
 * @return string
 */
function updateActivity($activity = null) {
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    $data = json_decode($data, true);
    if ('' == $data || !is_array($data) || count($data) <= 0) {
        echo sendJsonInfo(600, '', '未获取到更新数据');
    }

    if (null == $activity) {
        $activity = new ActivityDao();
    }

    if ($activity->updateActivity($data)) {
        return sendJsonInfo(200, '', '修改成功');
    }
    return sendJsonInfo(600, '', '修改失败');
}


/**
 * 删除活动
 * @param null $activity
 * @return string
 */
function delActivity($activity = null) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if ('' == $id) {
        return sendJsonInfo(600, '', '未获取到id');
    }

    if (null == $activity) {
        $activity = new ActivityDao();
    }

    if ($activity->delActivity($id)) {
        return sendJsonInfo(200, '', '删除成功');
    }
    return sendJsonInfo(600, '', '删除失败');
}

/**
 * @deprecated
 * 获取该活动的总票数
 * @param null $activity
 * @return string
 */
function getVoteTotal($activity = null) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    if ('' == $id) {
        return sendJsonInfo(600, '', '未获取到id');
    }

    if (null == $activity) {
        $activity = new ActivityDao();
    }
    $result = $activity->getVoteTotal(1);
    if ('' == $result || !is_array($result) || count($result) <= 0) {
        return sendJsonInfo(600, '', '获取失败');
    }
    return sendJsonInfo(600, $result, '获取成功');
}


/**
 * 获取活动选手
 * @param null $activity
 * @return string
 */
function getActivityPlayer($activity = null) {
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    $id = 1;
    if ('' == $id) {
        return sendJsonInfo(600, '', '未获取到id');
    }

    if (null == $activity) {
        $activity = new ActivityDao();
    }
    $activity->requestTimes($id);   //增加请求数

    $result = $activity->getActivityPlayer($id);
    if ('' == $result || !is_array($result) || count($result) <= 0) {
        return sendJsonInfo(600, '', '获取失败');
    }
    return sendJsonInfo(200, $result, '获取成功');
}