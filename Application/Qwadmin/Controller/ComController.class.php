<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2015-09-17
* 版    本：1.0.0
* 功能说明：后台公用控制器。
*
**/

namespace Qwadmin\Controller;
use Common\Controller\BaseController;
use Think\Auth;
class ComController extends BaseController {
	public $USER;
	public function _initialize(){
		C(setting());
		$user = cookie('user');
		$this->USER = $user;
		$url = U("Login/index");
		if(!$user){
			header("Location: {$url}");
			exit(0);
		}
		$Auth  =   new Auth();
		$allow_controller_name=array('Upload');//放行控制器名称
		$allow_action_name=array();//放行函数名称
		if(!$Auth->check(CONTROLLER_NAME.'/'.ACTION_NAME,$this->USER['uid'])&&!in_array(CONTROLLER_NAME,$allow_controller_name)&&!in_array(ACTION_NAME,$allow_action_name)){            
			$this->error('没有权限访问本页面!');
		}

		$user = member(intval($user['uid']));
		$this->assign('user',$user);

		$prefix = C('DB_PREFIX');
		$m =  M();
		$current_action_name= ACTION_NAME=='edit'? "index":ACTION_NAME;
		$current = $m->query("SELECT s.id,s.title,s.name,s.tips,s.pid,p.pid as ppid,p.title as ptitle FROM {$prefix}auth_rule s left join {$prefix}auth_rule p on p.id=s.pid where s.name='".CONTROLLER_NAME.'/'.$current_action_name."'");
		$this->assign('current',$current[0]);		
		$UID=$this->USER['uid'];
		$menu_access = $m->query("SELECT rules FROM {$prefix}auth_group g left join {$prefix}auth_group_access a on g.id=a.group_id where a.uid=$UID");
		$menu_access_id=$menu_access[0]['rules'];
		$menu = M('auth_rule')->field('id,title,pid,name,icon')->where("islink=1 AND id in ($menu_access_id)")->order('o ASC')->select();
		$menu = $this->getMenu($menu);
		$this->assign('menu',$menu);
		
    }

	public function getAllAddress($areaId){
        $areaStr="";
        $districtModel=M("Districts");
        $flag=100000;
        while($flag!=0){
            $temp=$districtModel->find($areaId);
            $areaStr=$temp['area'].$areaStr;
            $flag=$temp['pid'];
            $areaId=$temp['pid'];
        }
        return $areaStr;
    }

	
	
	protected function getMenu($items,$id='id',$pid='pid',$son = 'children'){
		$tree = array();
		$tmpMap = array();
		
		foreach ($items as $item) {
			$tmpMap[$item[$id]] = $item;
		}
		
		foreach ($items as $item) {
			if (isset($tmpMap[$item[$pid]])) {
				$tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
			} else {
				$tree[] = &$tmpMap[$item[$id]];
			}
		}
		return $tree;
	}
    //判断当前用户是否属于超级管理员组
    public function getGroups(){
        $user = cookie('user');
        $auth=new \Think\Auth();
        $groups=$auth->getGroups($user['uid']);
        foreach($groups as $group){
            if($group['group_id']==1){
                return 1;
            }
        }
        return 0;
    }
}