<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Logic;

/**
 * 文档模型子模型 - 文章模型
 */
class ArticleLogic extends BaseLogic{
    /* 自动验证规则 */
    protected $_validate = array(
        array('content', 'getContent', '内容不能为空！', self::MUST_VALIDATE , 'callback', self::MODEL_BOTH),
    );

    /**
     * 获取文章的详细内容
     * @return boolean
     * @author huajie <banhuajie@163.com>
     */
    protected function getContent(){
        $type = I('post.type');
        $content = I('post.content');
        if($type > 1){//主题和段落必须有内容
            if(empty($content)){
                return false;
            }
        }else{  //目录没内容则生成空字符串
            if(empty($content)){
                $_POST['content'] = ' ';
            }
        }
        return true;
    }

    /**
     * 新增或添加模型数据
     * @param  number $id 文章ID
     * @return boolean    true-操作成功，false-操作失败
     */
    public function update($id = 0) {
        /* 获取数据 */
        $data = $this->create();
        if ($data === false) {
            return false;
        }
        preg_match_all('<img.*src=[\"](.*?)[\"].*?>', $data['content'], $match);
        if($match[1][0] != ''){
            $data['img_url'] = $match[1][0];
        }else{
            $data['img_url'] = '/Public/static/assets/img/main/'.rand(1,20).
            '.jpg';
        }
        
        if (empty($data['id'])) {//新增数据
            $data['id'] = $id;
            $id = $this->add($data);
            if (!$id) {
                $this->error = '新增数据失败！';
                return false;
            }
        } else { //更新数据
            $status = $this->save($data);
            if (false === $status) {
                $this->error = '更新数据失败！';
                return false;
            }
        }
        return true;
    }

}
