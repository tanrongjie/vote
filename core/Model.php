<?php

//数据库配置参数
$dbconfig = array(
    'DBUSER'  => 'root',                //用户名
    'DBPASS'  => 'root',                //密码
    'DBNAME'  => 'vote',                //数据库名称
    'DBHOST'  => '127.0.0.1',           //域名
    'DBPORT'  => 3306,                  //端口号
    'DBDEBUG' => false,                 //false为关闭调试模式
);


define('DBUSER', $dbconfig['DBUSER']);
define('DBPASS', $dbconfig['DBPASS']);
define('DBNAME', $dbconfig['DBNAME']);
define('DBHOST', $dbconfig['DBHOST']);
define('DBPORT', $dbconfig['DBPORT']);
define('DBDEBUG', $dbconfig['DBDEBUG']);


/**
 * 基于mysqli的数据库操作类
 * 数据库操作类升级版本--2017-08-25
 * @author wanghongyu
 * @email wanghongyu0321@163.com
 *
 * $Model = new Model();  //生产
 * $Model = new Model(true);  //开启调试模式
 *
 * echo $Model->get_errlog('br');  //输出<br>分隔错误日志
 * echo $Model->get_errlog();  //输出字符串错误日志
 *
 * $Model->get_all(); //获取所有行
 * $Model->get_row(); //获取一行
 * $Model->get_one(); //获取一个字段
 * $Model->execute(); //执行一个插入、更新或删除语句
 *
 */
class Model {
    private $debug;                         //调试开关
    private $errlog;                        //错误日志存放变量
    private $mysqli     = null;             //mysqli实例对象
    private $rs         = null;             //结果集存放变量
    private $fetch_mode = MYSQLI_ASSOC;     //获取模式

    private static $self = null;             //对象实例

    /**
     * 单例模式
     * @return Model|null
     */
    public static function instance() {
        if (null == self::$self) {
            self::$self = new Model();
        }
        return self::$self;
    }

    /**
     * 构造函数：主要用来返回一个mysqli对象
     * Model constructor.
     * @param null $debug
     */
    private function __construct($debug = null) {
        $hy_dbhost  = defined('DBHOST') ? DBHOST : '127.0.0.1';
        $hy_dbuser  = defined('DBUSER') ? DBUSER : '';
        $hy_dbpass  = defined('DBPASS') ? DBPASS : '';
        $hy_dbname  = defined('DBNAME') ? DBNAME : '';
        $hy_dbport  = defined('DBPORT') ? DBPORT : 3306;
        $hy_dbdebug = defined('DBDEBUG') ? DBDEBUG : false;

        $this->debug = $hy_dbdebug;
        if (null !== $debug) {
            $this->debug = $debug;
        }

        if ('' == $hy_dbhost) {
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . 'DBHOST_NULL' . "\n";
            return false;
        }
        if ('' == $hy_dbuser) {
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . 'DBUSER_NULL' . "\n";
            return false;
        }
        if ('' == $hy_dbpass) {
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . 'DBPASS_NULL' . "\n";
            return false;
        }
        if ('' == $hy_dbname) {
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . 'DBNAME_NULL' . "\n";
            return false;
        }
        if ('' == $hy_dbport) {
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . 'DBPORT_NULL' . "\n";
            return false;
        }

        //创建数据库连接
        $this->mysqli = new mysqli($hy_dbhost, $hy_dbuser, $hy_dbpass, $hy_dbname, $hy_dbport);
        if (mysqli_connect_errno()) {
            $this->mysqli = false;
            //追加到错误日志变量
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . mysqli_connect_error() . "\n";
            //输出错误内容
            echo '[mysql_connect_error]';
            return false;
        } else {
            $this->mysqli->set_charset("utf8");
        }
    }

    /**
     * 析构函数：主要用来释放结果集和关闭数据库连接
     */
    public function __destruct() {
        $this->free();
        $this->close();
    }

    /**
     * 释放结果集所占资源
     */
    private function free() {
        if (isset($this->rs) && is_object($this->rs)) {
            $this->rs->free_result();
            $this->rs = null;

        }
    }

    /**
     * 关闭数据库连接
     */
    private function close() {
        if (isset($this->mysqli) && null !== $this->mysqli && false !== $this->mysqli) {
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }

    /**
     * 获取结果集
     * @return mixed
     */
    private function fetch() {
        return $this->rs->fetch_array($this->fetch_mode);
    }

    /**
     * 获取查询的sql语句---insert和update语句同样可以使用此函数处理
     * @param $sql
     * @param null $limit
     * @return string
     */
    private function get_query_sql($sql, $limit = null) {
        if (@preg_match("/[0-9]+(,[ ]?[0-9]+)?/is", $limit) && !preg_match("/ LIMIT [0-9]+(,[ ]?[0-9]+)?$/is", $sql)) {
            $sql .= " limit " . $limit;
        }
        return $sql;
    }

    /**
     * 执行sql语句查询---核心查询操作函数
     * @param $sql
     * @param null $limit
     * @return bool|null
     */
    private function query($sql, $limit = null) {
        $sql      = $this->get_query_sql($sql, $limit);
        $this->rs = $this->mysqli->query($sql);
        if (!$this->rs) {
            //追加到错误日志变量
            $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . $this->mysqli->error . "\n";

            if (true === $this->debug) {
                echo '[query_sql_error]';
            }
            return false;
        } else {
            return $this->rs;
        }
    }


    /**
     * 返回单条记录的单个字段值
     * @param $sql
     * @return bool
     */
    public function get_one($sql) {
        $r = $this->query($sql, 1);
        if (false !== $r) {
            $this->fetch_mode = MYSQLI_NUM;
            $row              = $this->fetch();
            $this->free();
            return $row[0];
        } else {
            $this->free();
            return false;
        }
    }


    /**
     * 获取单条记录
     * @param $sql
     * @param int $fetch_mode
     * @return bool|mixed
     */
    public function get_row($sql, $fetch_mode = MYSQLI_ASSOC) {
        $r = $this->query($sql, 1);
        if (false !== $r) {
            $this->fetch_mode = $fetch_mode;
            $row              = $this->fetch();
            $this->free();
            return $row;
        } else {
            $this->free();
            return false;
        }
    }


    /**
     * 返回所有的结果集
     * @param $sql
     * @param null $limit
     * @param int $fetch_mode
     * @return array|bool
     */
    public function get_all($sql, $limit = null, $fetch_mode = MYSQLI_ASSOC) {
        $r = $this->query($sql, $limit);
        if (false !== $r) {
            $all_rows         = array();
            $this->fetch_mode = $fetch_mode;
            while ($rows = $this->fetch()) {
                $all_rows[] = $rows;
            }
            $this->free();
            return $all_rows;
        } else {
            $this->free();
            return false;
        }
    }

    /**
     * 数据插入更新sql语句执行函数
     * @param $sql
     * @return bool|int
     */
    public function execute($sql) {
        if (trim($sql) == '') {
            //如果提交的语句为空，直接返回false
            return false;
        } else {
            //执行sql语句
            $r = $this->mysqli->query($sql);
            if ($r) {
                //返回影响的条数
                return mysqli_affected_rows($this->mysqli);
            } else {
                //追加到错误日志变量
                $this->errlog .= "\n" . date('Y-m-d H:i:s') . "\n" . $this->mysqli->error . "\n";
                if (true === $this->debug) {
                    echo '[execute_sql_error]';
                }
                return false;
            }
        }
    }

    /**
     * 获取错误日志
     * @param string $type
     * @return string
     */
    public function get_errlog($type = '') {
        if ('br' == $type) {
            return nl2br($this->errlog);
        } else {
            return $this->errlog;
        }
    }
}




