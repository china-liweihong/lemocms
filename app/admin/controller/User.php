<?php
/**
 * lemocms
 * ============================================================================
 * 版权所有 2018-2027 lemocms，并保留所有权利。
 * 网站地址: https://www.lemocms.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/8/2
 */
namespace app\admin\controller;

use think\facade\Request;
use think\facade\View;
use app\common\model\UserLevel;
use app\common\model\User as UserModel;
use think\facade\Db;
use util\Excel;

class User extends Base{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    public function index(){
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('user')->alias('u')
                ->where('u.email|u.mobile|u.username','like',"%".$keys."%")
                ->leftJoin('userLevel ul','u.level_id = ul.id') //此处表别名不能加as
                ->field('u.*,ul.level_name')
                ->order('u.id desc ,u.create_time desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();
            foreach ($list['data'] as $k=>$v){
                $list['data'][$k]['create_time'] = date('Y-m-d H:s',$v['create_time']);
            }
            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];
        }
        return View::fetch();

    }

    public function add(){
        if (Request::isPost()) {
            $data = Request::post();
            try{
                $this->validate($data, 'User');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }


            $res = UserModel::create($data);
            if ($res) {
                $this->success(lang('add success'),url('index'));
            } else {
                $this->error(lang('add fail'));
            }
        }
        $userLevel = UserLevel::where('status',1)->select();

        $view = [
            'info' => '',
            'title' => lang('add'),
            'userLevel'=>$userLevel,
        ];
        View::assign($view);
        return View::fetch();
    }

    public function edit(){
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'User');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $res = UserModel::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('index'));
            } else {
                $this->error(lang('edit fail'));
            }
        }
        $info = UserModel::find(Request::get('id'));
        $userLevel = UserLevel::where('status',1)->select();
        $view = [
            'info' => $info,
            'title' => '修改',
            'userLevel'=>$userLevel,
        ];
        View::assign($view);
        return View::fetch('add');

    }
    public function state(){

        $id = Request::post('id');
        if($id){
            $info = UserModel::find($id);
            $info->status = $info->status==1?0:1;
            $info->save();
            $this->success(lang('edit success'));

        }else{
            $this->error(lang('invalid data'));
        }
    }
    public function delete(){
        $id = Request::post('id');
        if($id){

            UserModel::destroy($id);
            $this->success(lang('delete success'));

        }else{
            $this->error(lang('invalid data'));
        }
    }
    public function delAll(){
        $ids = Request::post('ids');
        if($ids){
            $res = \app\common\model\User::destroy($ids);
            if(!$res)$this->error(lang('delete fail'));

            $this->success(lang('delete success'));
        }else{
            $this->error(lang('invalid data'));
        }

    }

    /**---------------用户等级--------------------**/

    public function levelIndex(){
        if (Request::isPost()) {
            $keys = Request::post('keys', '', 'trim');
            $page = Request::post('page') ? Request::post('page') : 1;
            $list = Db::name('user_level')
                ->where('level_name','like',"%".$keys."%")
                ->order('id desc')
                ->paginate(['list_rows' => $this->pageSize, 'page' => $page])
                ->toArray();

            return $result = ['code' => 0, 'msg' => lang('get info success'), 'data' => $list['data'], 'count' => $list['total']];
        }
        return View::fetch();
    }

    public function levelAdd(){
        if (Request::isPost()) {
            $data = Request::post();
            try{
                $this->validate($data, 'UserLevel');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }

            $res = UserLevel::create($data);
            if ($res) {
                $this->success(lang('add success'),url('levelIndex'));
            } else {
                $this->error(lang('add fail'));
            }
        }

        $view = [
            'info' => '',
            'title' => lang('add'),
        ];
        View::assign($view);
        return View::fetch();
    }

    public function levelEdit(){
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'UserLevel');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $res = UserLevel::update($data);
            if ($res) {
                $this->success(lang('edit success'), url('levelIndex'));
            } else {
                $this->error(lang('edit fail'));
            }
        }
        $info = UserLevel::find(Request::get('id'));
        $view = [
            'info' => $info,
            'title' => lang('edit'),
        ];
        View::assign($view);
        return View::fetch('level_add');


    }
    public function levelState(){

        $id = Request::post('id');
        if($id){
            $info = UserLevel::find($id);
            $info->status = $info->status==1?0:1;
            $info->save();
            $this->success(lang('edit success'));
        }else{
            $this->error(lang('invalid data'));
        }
    }

    public function levelDel(){

        $id = Request::post('id');
        if($id){
            UserLevel::destroy($id);
            $this->success(lang('delete success'));
        }else{
            $this->error(lang('invalid data'));
        }
    }


}