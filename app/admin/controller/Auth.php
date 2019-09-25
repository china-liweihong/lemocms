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

use app\admin\model\AuthGroup;
use app\admin\model\AuthRule;
use app\admin\model\Admin;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;

class Auth extends Base
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }
    /*-----------------------管理员管理----------------------*/
    // 管理员列表
    public function adminList()
    {
        if(Request::isPost()){
            $where=Request::post();
            $map=[];
            $map1=[];
            $map2=[];
            if(isset($where['keys'])) {
                $map = [
                    ['a.username', 'like', "%" . $where['keys'] . "%"],

                ];
                $map1 = [
                    ['a.email', 'like', "%" . $where['keys'] . "%"],

                ];
                $map2= [
                    ['a.mobile', 'like', "%" . $where['keys'] . "%"],
                ];

            }
            $list=Db::name('admin')->alias('a')
                ->join('auth_group ag','a.group_id = ag.id','left')
                ->field('a.*,ag.title')
                ->whereOr($map,$map1,$map2)
                ->select();

            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list];
        }

        return View::fetch();
    }

    // 管理员添加
    public function adminAdd()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try{
                $this->validate($data, 'Admin');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $data['password'] = md5(trim($data['password']));
            //添加
            $result = Admin::create($data);
            if ($result) {
                $this->success(lang('add success'), url('adminList'));
            } else {
                $this->error(lang('add fail'));
            }
        } else {
            $info = '';
            $auth_group = AuthGroup::where('status', 1)
                ->select();
            $view = [
                'info'  =>$info,
                'authGroup' => $auth_group,
                'title' => lang('add'),
            ];
            View::assign($view);
            return View::fetch();
        }
    }

    // 管理员删除
    public function adminDel()
    {
        $id = Request::post('id');
        if ($id > 1) {
            Admin::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('supper man cannot delete'));

        }
    }



    // 管理员状态修改
    public function adminState()
    {
        if (Request::isPost()) {
            $id = Request::post('id');
            if (empty($id)) {
                $this->error('id'.lang('not exist'));
            }
            if ($id == 1) {
                $this->error(lang('supper man cannot edit state'));
            }
//            if(Session::get('admin.id')==3){
//                $this->error(lang('test data cannot edit'));
//
//            }

            $admin = Admin::find($id);
            $status = $admin['status'] == 1 ? 0 : 1;
            $admin->status = $status;
            $admin->save();
            $this->success(lang('edit success'));
        }
    }

    /**
     * 管理员修改
     */
    public function adminEdit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if(!$data['username']) $this->error(lang('username').lang('cannot null'));
            if(!$data['password']) $this->error(lang('password').lang('cannot null'));
            if(!$data['group_id']) $this->error(lang('adminGroup').lang('cannot null'));
            $admin = Admin::find($data['id']);
            if($admin['password']==$data['password']){
                unset($data['password']);
            }else{
                $data['password'] = Request::post('password', '123456', 'md5');
            }
            Admin::update($data);
            if(Session::get('admin.id')==$data['id']){
                Session::set('admin',null);
            }
            $this->success(lang('edit success'), url('Auth/adminList'));

        } else {
            $id = Request::param('id')?Request::param('id'):Session::get('admin.id');
            if ($id) {
                $auth_group = AuthGroup::select();
                $admin = Admin::find($id);
                $view = [
                    'info' => $admin,
                    'authGroup' => $auth_group,
                    'title' => lang('edit'),
                ];
                View::assign($view);
                return View::fetch('admin_add');
            }
        }
    }

    /*-----------------------用户组管理----------------------*/

    // 用户组管理
    public function group()
    {
        if(Request::isPost()){
            //条件筛选
            $title = Request::param('title');
            //全局查询条件
            $where = [];
            if ($title) {
                $where[] = ['title', 'like', '%' . $title . '%'];
            }
            //显示数量
            $pageSize = Request::param('page_size', Config::get('app.page_size'));

            //查出所有数据
            $list = AuthGroup::where($where)
                ->paginate(
                    $this->pageSize, false,
                    ['query' => Request::param()]
                )->toArray();
            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data']];
        }
        return View::fetch();
    }

    // 用户组删除
    public function groupDel()
    {
        $id = Request::post('id');
        if ($id > 1) {
            AuthGroup::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('supper man cannot delete'));
        }

    }

    // 用户组添加
    public function groupAdd()
    {
        if (Request::isPost()) {
            $data = Request::post();
            try {
                $this->validate($data, 'AuthGroup');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
            $result = AuthGroup::create($data);
            if ($result) {
                $this->success(lang('add success'), 'Auth/adminGroup');
            } else {
                $this->error(lang('add fail'));
            }

        } else {
            $view = [
                'info' => null
            ];
            View::assign($view);
            return View::fetch('group_add');
        }
    }

    // 用户组修改
    public function groupEdit()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if($data['id']==1){
                $this->error(lang('supper man cannot edit'));
            }
            try{
                $this->validate($data, 'AuthGroup');
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $where['id'] = $data['id'];
            $res = AuthGroup::update($data, $where);
            if($res){

                $this->success(lang('edit success'), url('group'));
            }else{
                $this->error(lang('edit fail'));

            }

        } else {
            $id = Request::param('id');
            $info = AuthGroup::find(['id' => $id]);
            $view = [
                'info' => $info,
                'title' => lang('edit')
            ];
            View::assign($view);
            return View::fetch();
        }
    }

    // 用户组状态修改
    public function groupState()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AuthGroup::find($id);
            $info->status = $info['status'] == 1 ? 0 : 1;
            $info->save();
            $this->success(lang('edit success'));

        }
    }

    // 用户组批量删除
    public function groupSelectDel()
    {
        $id = Request::post('id');
        if ($id > 1) {
            AuthGroup::destroy($id);
            $this->success(lang('delete success'));
        } else {
            $this->error(lang('delete fail'));
        }
    }

    // 用户组显示权限
    public function groupAccess()
    {
        $admin_rule = AuthRule::field('id, pid, title')
            ->where('status',1)
            ->order('sort asc')
            ->select()->toArray();
        $rules = AuthGroup::where('id', Request::param('id'))
            ->where('status',1)
            ->value('rules');
        $group_id = Request::param('id');
        $idList = AuthRule::column('id');
        $list = auth_checked($admin_rule, $pid = 0, $rules);
        sort($idList);
        $view = [
            'list' => $list,
            'idList' => $idList,
            'group_id' => $group_id,
        ];
        View::assign($view);
        return View::fetch('group_access');
    }
    // 用户组保存权限
    public function groupSetaccess()
    {
        $rules = Request::post('rules');
        if (empty($rules)) {
            $this->error(lang('please choose rule'));
        }
        $data = Request::post();
        $rules = auth_normal($rules);
        $rls = '';
        foreach ($rules as $k=>$v){
            $rls.=$v['id'].',';
        }
        $where['id'] = $data['group_id'];
        $where['rules'] = $rls;
        if (AuthGroup::update($where)) {
            $this->success(lang('rule assign success'),url('group'));
        } else {
            $this->error(lang('rule assign fail'));
        }
    }

    /********************************权限管理*******************************/
    // 权限列表
    public function adminRule()
    {
        if(Request::isPost()){
            $arr = cache('authRuleList');
            if(!$arr){
                $arr = Db::name('auth_rule')
                    ->order('pid asc,sort asc')
                    ->select()->toArray();
                foreach($arr as $k=>$v){
                    $arr[$k]['lay_is_open']=false;
                }
                cache('authRuleList', $arr, 3600);
            }
            return $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$arr,'is'=>true,'tip'=>'操作成功'];
        }
        return View::fetch('admin_rule');
    }

    // 权限菜单显示或者隐藏
    public function ruleState()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AuthRule::find($id);
            $info->menu_status = $info['menu_status'] == 1 ? 0 : 1;
            $info->save();
            $this->success(lang('edit success'));

        }
    }

    // 设置权限是否验证
    public function ruleOpen()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $info = AuthRule::find($id);
            $info->auth_open = $info['auth_open'] == 1 ? 0 : 1;
            $info->save();
            $this->success(lang('edit success'));
        }
    }

    // 设置权限排序
    public function ruleSort()
    {
        if (Request::isPost()) {
            $id = Request::param('id');
            $sort = Request::param('sort');
            $info = AuthRule::find($id);
            $info->sort = $sort;
            $info->save();
            $this->success(lang('edit success'));
        }
    }

    // 权限删除
    public function ruleDel()
    {
        $id = Request::param('id');
        $child = AuthRule::where('pid',$id)->find();
        if ($id && !$child) {
            AuthRule::destroy($id);
            $this->success(lang('delete success'));
        }elseif($child){
            $this->error(lang('delete child first'));

        }else{
            $this->error('id'.lang('not exist'));
        }
    }

    // 权限批量删除
    public function ruleSelectDel()
    {
        $id = Request::param('id');
        if ($id) {
            AuthRule::destroy($id);
            $this->success(lang('delete success'));
        }

    }

    // 权限增加
    public function ruleAdd()
    {
        if (Request::isPost()) {
            $data = Request::post();
            if (empty($data['title'])) {
                $this->error(lang('rule name cannot null'));
            }
            if (empty($data['sort'])) {
                $this->error(lang('sort').lang(' cannot null'));
            }
            if (AuthRule::create($data)) {
                $this->success(lang('add success'), url('adminRule'));
            } else {
                $this->error(lang('add fail'));
            }
        } else {
            $list = Db::name('auth_rule')
                ->order('sort ASC')
                ->select();
            $list = cate_tree($list);
            $pid = Request::param('id') ? Request::param('id') : 0;
            $view = [
                'info' => null,
                'pid' => $pid,
                'ruleList' => $list
            ];
            View::assign($view);
            return View::fetch('rule_add');
        }
    }

    //权限修改
    public function ruleEdit()
    {
        if (request()->isPost()) {
            $data = Request::param();
            $where['id'] = $data['id'];
            AuthRule::update($data);
            $this->success(lang('edit success'), url('Auth/adminRule'));
        } else {
            $list = Db::name('auth_rule')
                ->order('sort asc')
                ->select();
            $list = cate_tree($list);
            $id = Request::param('id');
            $info = AuthRule::find($id)->toArray();
            $view = [
                'info' => $info,
                'ruleList' => $list,
            ];
            View::assign($view);
            return View::fetch('rule_add');
        }
    }


}