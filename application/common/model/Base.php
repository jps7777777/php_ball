<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2019-8-13 0013
 * Time: 16:05
 */
namespace app\common\model;

use think\Model;

class Base extends Model
{

    public function __construct($data = [])
    {
        parent::__construct($data);
    }

    /**
     * 获取单条信息
     * @param $condition
     * @param $field
     * @return array|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_info($condition, $field) {
        $res = $this->where($condition)->field($field)->find();
        if ($res) {
            return $res->toArray();
        } else {
            return $res;
        }
    }

    /**
     * 根据id返回信息
     * @param $id
     * @param string $field
     * @return array|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_info_by_id($id, $field = '*') {
        $data = $this->where('id','=',$id)->field($field)->find();
        return $data ? $data->toArray() : null;
    }

    /**
     * 返回多条信息
     * @param $condition
     * @param string $field
     * @param string $order
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_list($condition,$field = '*',$order = "id asc") {
        $res = $this->where($condition)->order($order)->field($field)->select();
        if (count($res)) {
            return $res->toArray();
        } else {
            return null;
        }
    }

    /**
     * 多条信息分页返回
     * @param $condition
     * @param string $field
     * @param string $order
     * @param int $skip
     * @param int $limit
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function get_list_by_page($condition, $field = '*',$order = "id asc", $skip = 1, $limit = 20 ) {
        $res = $this->where($condition)->order($order)->page($skip, $limit)->field($field)->select();
        if (count($res)) {
            return $res->toArray();
        } else {
            return null;
        }
    }

    /**
     * 返回查询条数
     * @param $condition
     * @return float|string
     */
    public function get_count($condition) {
        return $this->where($condition)->count();
    }



}