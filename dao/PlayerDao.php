<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/26
 * Time: 11:47
 */

include 'core/Model.php';

class PlayerDao {

    private $Model = null;

    public function __construct() {
        $this->Model = Model::instance();
    }

    /**
     * 获取选手信息
     * @param string $where
     * @return array|bool
     */
    public function getPlayers($where = '') {
        $sql = "select *from vote_player where 1=1 " . $where;
        $result = $this->Model->get_all($sql);
        if (is_array($result)) {
            return $result;
        }
        return array();
    }


    /**
     * 添加选手
     * @param array $data
     * @return bool|int
     */
    public function addPlayer($data = array()) {
        if ('' == $data || !is_array($data) || count($data) <= 0) {
            return 0;
        }

        $sql = splicingSql($data, 'vote_player', '', 1);
        if ('' == $sql) {
            return 0;
        }
        return $this->Model->execute($sql);
    }

    /**
     * 修改选手
     * @param array $data
     * @return int
     */
    public function updPlayer($data = array()) {
        if ('' == $data || !is_array($data) || count($data) <= 0) {
            return 0;
        }
        $data['create_datetime'] = date('Y-m-d H:i:s');
        $sql = splicingSql($data, 'vote_player', 'id = ' . $data['id'], 2);

        if ('' == $sql) {
            return 0;
        }
        return $this->Model->execute($sql);
    }

    /**
     * 删除选手
     * @param int $id
     * @return bool|int
     */
    public function delPlayer($id = 0) {
        if ('' == $id || 0 === $id) {
            return 0;
        }
        $sql = "delete from vote_player where id = '" . $id . "'";
        return $this->Model->execute($sql);
    }
}