<?php
/**
 * Created by PhpStorm.
 * User: TRJ
 * Date: 2018/4/29
 * Time: 12:06
 */

include 'core/Model.php';

class PlayerVoteDao {
    private $Model = null;

    public function __construct() {
        $this->Model = Model::instance();
    }


    /**
     * 投票
     * @param array $data
     * @return bool|int
     */
    public function vote($data = array()) {
        if ('' == $data || !is_array($data) || count($data) <= 0) {
            return 0;
        }

        $activity_id = isset($data['activity_id']) ? $data['activity_id'] : 0;
        $player_id   = isset($data['player_id']) ? $data['player_id'] : 0;

        if (0 == $activity_id || 0 == $player_id) {
            return 0;
        }

        $sel_sql = "select ticket from vote_record where activity_id = '" . $activity_id . "' and player_id = '" . $player_id . "'";
        $result  = $this->Model->get_row($sel_sql);
        if ('' != $result && is_array($result) && isset($result['ticket'])) {
            //如果存在在记录中就将该记录的票数加一
            $where = "activity_id = '" . $activity_id . "' and player_id = '" . $player_id . "'";

            $ticket = intval($result['ticket']) + 1;
            $sql    = splicingSql(array('ticket' => $ticket), 'vote_record', $where, 2);
            return $this->Model->execute($sql);
        } else {
            $data['ticket']          = 1;
            $data['create_datetime'] = date('Y-m-d H:i:s');

            $sql = splicingSql($data, 'vote_record', '', 1);
            return $this->Model->execute($sql);
        }
    }


    /**
     * @param string $id
     * @return int
     */
    public function vote_check($id = '') {
        if ($id == '') {
            return 0;
        }

        $sql    = "select id,activity_flag,activity_starttime,activity_endtime from vote_activity where id = '" . $id . "' and activity_flag = 1";
        $resutl = $this->Model->get_row($sql);
        if ('' == $resutl || !is_array($resutl) || count($resutl) <= 0) {
            return 0;
        }
        return $resutl;
    }
}