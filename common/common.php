<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/24
 * Time: 14:50
 */

/**
 * 数据插入更新sql语句拼接函数
 * @param array $data       操作数据
 * @param string $table     操作表
 * @param string $where     执行条件
 * @param int $operate      操作类型(1,添加;2修改)
 * @return string
 */
function splicingSql($data = array(), $table = '', $where = '',  $operate = 0) {
    if ('' == $operate || 0 == $operate) {
        return '';
    }

    if ('' == $data || !is_array($data) || count($data) <= 0) {
        return '';
    }

    if (1 == $operate) {
        $sql    = "insert into $table (";
        $field  = "";
        $values = " values(";
        foreach ($data as $key => $val) {
            if ('' != $val && '' != $key) {
                $field .= $key . ",";
                $values .= "'" . $val . "',";
            }
        }
        $field = rtrim($field, ',') . ')';
        $values = rtrim($values, ',') . ')';
        $sql .= $field . $values;
        return $sql;
    } else if (2 == $operate) {
        $sql    = "update " . $table . " set ";
        foreach ($data as $key => $val) {
            $sql .= $key . "='" . $val . "',";
        }
        $sql = rtrim($sql, ',');
        $sql .= " where " . $where;
        return $sql;
    } else {
        return '';
    }
}


/**
 * @param string $code 返回码
 * @param array $data 数据数组
 * @param string $message 返回信息
 * @return string
 */
function sendJsonInfo($code = '', $data = array(), $message = '') {
    if ('' == $data)
        $data = array();
    $param            = array();
    $param['code']    = $code;
    $param['data']    = $data;
    $param['message'] = $message;

    return json_encode($param, true);
}