<?php
/**
 * Created by PhpStorm.
 * User: liuzezhong
 * Date: 2017/9/28
 * Time: 15:41
 */

namespace Wxapp\Model;


use Think\Model;

class ScanuserModel extends Model
{
    private $_db = '';

    public function __construct() {
        $this->_db = M('scanuser');
    }

    public function addScanuser($data = array()) {
        if(!$data || !is_array($data)) {
            throw_exception('ScanuserModel addScanuser data is null !');
        }

        return $this->_db->add($data);
    }
}