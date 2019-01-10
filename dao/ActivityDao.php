<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/24
 * Time: 13:58
 */
include 'core/Model.php';

class ActivityDao {
    private $Model;

    public function __construct() {
        $this->Model = Model::instance();
    }

    /**
     * 获取活动集合
     * @param string $where 查询条件
     * @return array|bool
     */
    public function getActivity($where = '') {
        $sql    = "select *from vote_activity where 1=1 " . $where;
        $result = $this->Model->get_all($sql);
        return $result;
    }

    /**
     * 根据id获取当前数据
     * @param int $id
     * @return array|bool
     */
    public function getActivityById($id = 0) {
        $sql    = "select *from vote_activity where id = '" . $id . "'";
        $result = $this->Model->get_row($sql);
        return $result;
    }


    /**
     * 添加活动
     * @param array $players
     * @return bool|int
     */
    public function addActivity($players = array()) {
        if ('' == $players || !is_array($players) || count($players) <= 0) {
            return 0;
        }
        $players['create_datetime'] = date('Y-m-d H:i:s');
        $sql                        = splicingSql($players, 'vote_activity', '', 1);
        if ('' == $sql) {
            return 0;
        }
        return $this->Model->execute($sql);
    }

    /**
     * 更新活动
     * @param array $players
     * @return bool|int
     */
    public function updateActivity($players = array()) {
        if ('' == $players || !is_array($players) || count($players) <= 0) {
            return 0;
        }
        $sql = splicingSql($players, 'vote_activity', 'id=' . $players['id'], 2);
        if ('' == $sql) {
            return 0;
        }
        return $this->Model->execute($sql);
    }

    /**
     * 删除活动
     * @param string $id
     * @return bool|int
     */
    public function delActivity($id = '') {
        if ($id == '') {
            return 0;
        }

        $sql = "delete from vote_activity where id = '" . $id . "'";
        return $this->Model->execute($sql);
    }

    /**
     * 获取活动的总票数
     * @param string $id
     * @return int
     */
    public function getVoteTotal($id = '') {
        if ($id == '') {
            return 0;
        }

        $sql = "select sum(ticket) as sum_ticket from vote_record where activity_id = '" . $id . "'";
        return $this->Model->get_all($sql);
    }


    /**
     * 获取活动选手
     * @param string $id
     * @return array|bool|int
     */
    public function getActivityPlayer($id = '') {
        if ($id == '') {
            return 0;
        }

        $sql    = "select sum(ticket) as sum_ticket from vote_record  where activity_id = '" . $id . "'";
        $result = $this->Model->get_one($sql);

        if ('' == $result)
            $result = 0;

        $sql_player = "select *from vote_player where activity_id = '" . $id . "'";
        $players    = $this->Model->get_all($sql_player);

        /**
         * 获取每个选手的票数
         */
        foreach ($players as $key => $value) {
            $ticket_sql = "select ticket from vote_record where activity_id = '" . $id . "' and  player_id = '" . $players[$key]['id'] . "'";
            $ticket = $this->Model->get_row($ticket_sql);
            if ('' != $ticket && is_array($ticket) && count($ticket) > 0 && isset($ticket['ticket'])) {
                $players[$key]['ticket'] = $ticket['ticket'];
            } else {
                $players[$key]['ticket'] = 0;
            }
        }


        $sql_activity = "select *from vote_activity where id = '" . $id . "'";
        $activit      = $this->Model->get_row($sql_activity);

        if ('' == $players || !is_array($players) || count($players) <= 0) {
            $players = array();
        }

        if ('' == $activit || !is_array($activit) || count($activit) <= 0) {
            return 0;
        }

        $data = array(
            'sum_ticket' => $result,
            'players'    => $players,
            'activity'   => $activit,
        );
        var_dump($data);
        return $data;
    }


    /**
     * 访问量加一
     * @param string $id
     * @return bool|int
     */
    public function requestTimes($id = '') {
        if ($id == '') {
            return 0;
        }

        $sql = "update vote_activity set times = times + 1 where id = '" . $id . "'";
        return $this->Model->execute($sql);
    }
}