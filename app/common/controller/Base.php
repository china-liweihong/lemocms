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
 * Date: 2019/9/21
 */

namespace app\common\controller;

use app\BaseController;
use think\facade\Request;
use think\facade\Cookie;
class Base extends BaseController
{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        
    }

    public function enlang()
    {
        $lang = Request::get('lang');
        switch ($lang) {
            case 'zh-cn':
                Cookie::set('cookie_var', 'zh-cn');
                break;
            case 'en-us':
                Cookie::set('cookie_var', 'en-us');
                break;
            default:
                Cookie::set('cookie_var', 'zh-cn');
                break;
        }
        $this->success('切换成功');
    }

}