<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-09-20
* 版    本：1.0.0
* 功能说明：文章控制器。
*
**/

namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
use Vendor\Tree;

class CategoryController extends ComController {

	public function index(){
	
		
		$category = M('product_type')->order('id asc')->select();
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function del(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		if($id){
			$data['id'] = $id;
			$category = M('product_type');
				$category->where('id='.$id)->delete();
				addlog('删除分类，ID：'.$id);
			die('1');
		}else{
			die('0');
		}

	}
	
	public function edit(){
		
		$id = isset($_GET['id'])?intval($_GET['id']):false;
		$temp['id'] = $id;
		$category = M('product_type')->where($temp)->order('o asc')->find();
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function add(){
		
		$pid = isset($_GET['pid'])?intval($_GET['pid']):0;
		$category = M('category')->field('id,pid,name')->order('o asc')->select();
		$this->assign('category',$category);
		$this -> display();
	}
	
	public function update(){
		$aid = isset($_GET['id'])?intval($_GET['id']):0;
		$data['type'] = I('post.type','','strip_tags');
		$data['o'] = I('post.o','','strip_tags');
		if($aid){
			M('product_type')->data($data)->where('id='.$aid)->save();
			addlog('编辑文章，AID：'.$aid);
			$this->success('恭喜！类别编辑成功！');
		}else{
			$aid = M('product_type')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！类别新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}

		}
	}

}