<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use OT\DataDictionary;
use Think\Page;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){
        $limit = 10;
        $data = D('Document')->lists(null, $limit, 'id,title,description,create_time,category_id,view');
        $recent = D('Document')->recent(null, $limit, 'id,title');        
        $this->assign('recent', $recent);
        $this->assign('lists', $data[1]);
        $this->assign('page',$data[2]);
        $this->display();
    }


}