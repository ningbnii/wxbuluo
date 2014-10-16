<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HomeController extends Controller {

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}


    protected function _initialize(){
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }

        /* nav */
        $nav = $this->nav();

        // dump($nav);
        $this->assign('nav',$nav);
    }

/**
 * 获取分类树，限制两层，即最多有二级分类
 * 根据sort排序
 */
    protected function nav(){
        $arr = array();
    	$nav1 = M('Category')->field('id,name,title,pid,sort')->where(array('pid'=>0))->order('sort')->select();

    	foreach($nav1 as $k1=>$v1){
            $arr[$k1][0] = $v1;
            
            $nav2 = M('Category')->field('id,name,title,pid,sort')->where(array('pid'=>$v1['id']))->order('sort')->select();
            foreach($nav2 as $k2=>$v2){
                $arr[$k1][$k2+1] = $v2;
            }
        }

        return $arr;
    }

	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}

}
