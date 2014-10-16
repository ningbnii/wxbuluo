<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {
	public $breadcrumb = array();

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category();
		
		$ids = M('Category')->field('id')->where(array('pid'=>$category['id']))->select();
		if($ids){
			$idarr = array();
			foreach ($ids as $key => $value) {
				$idarr[] = $value['id'];
			}
			$idstring = implode(',', $idarr);
			$idstring = $category['id'].','.$idstring;
		}else{
			$idstring = $category['id'];
		}


		//频道页只显示模板，默认不读取任何内容
		//内容可以通过模板标签自行定制
        $limit = 10;
        $data = D('Document')->lists($idstring, $limit, 'id,title,description,create_time,category_id,view');
        
        /* 获取最新文章 */ 
		$recent = D('Document')->recent(null, $limit, 'id,title');
        $this->assign('lists', $data[1]);
        
        $this->assign('page',$data[2]);
        $this->assign('recent', $recent); 
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		
		$this->display($category['template_index']);
	}

	/* 文档模型列表页 */
	public function lists($p = 1){
		/* 分类信息 */
		$category = $this->category();

		/* 获取当前分类列表 */
		$Document = D('Document');
		$list = $Document->page($p, $category['list_row'])->lists($category['id']);
		if(false === $list){
			$this->error('获取列表数据失败！');
		}

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('list', $list);
		$this->display($category['template_lists']);
	}

	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		/* 标识正确性检测 */

		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}

		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$Document = D('Document');
		$info = $Document->detail($id);
		if(!$info){
			$this->error($Document->getError());
		}

		/* 获取下一篇文章 */
		$next = $Document->next($info);
		/* 获取上一篇文章 */
		$prev = $Document->prev($info);
		
		/* 分类信息 */
		$category = $this->category($info['category_id']);
		$arr['title'] = $category['title'];
		$arr['name'] = $category['name'];
		array_push($this->breadcrumb, $arr);
		
		if($category['pid'] != 0){
			$this->breadcrumb = $this->getPid($category['pid'], $this->breadcrumb);
		}
		
		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else { //使用默认模板
			$tmpl = 'Article/'. get_document_model($info['model_id'],'name') .'/detail';
		}

		/* 更新浏览数 */
		$map = array('id' => $id);
		$Document->where($map)->setInc('view');

		/* 获取最新文章 */
		$limit = 5; 
		$recent = D('Document')->recent(null, $limit, 'id,title');
		

		/* 模板赋值并渲染模板 */
		
		$this->assign('breadcrumb',$this->breadcrumb);
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('page', $p); //页码
		$this->assign('recent', $recent);
		$this->assign('next',$next);
		$this->assign('prev',$prev);
		$this->display($tmpl);
	}

	/**
	 * 根据id获取pid，知道pid=0
	 */
	private function getPid($id = 0, $breadcrumb){
		$data = $this->category($id);
		$arr['title'] = $data['title'];
		$arr['name'] = $data['name'];
		array_push($breadcrumb, $arr);
		if($data['pid'] != 0){
			$this->getPid($id=0, $this->breadcrumb);
		}
		return $breadcrumb; 
	}

	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);

		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}

}
