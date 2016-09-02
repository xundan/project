<?php
namespace Qwadmin\Controller;
use Qwadmin\Controller\ComController;
use Vendor\Tree;

class ArticleController extends ComController {

	public function add(){
		$category = M('category')->field('id,pid,name')->order('o asc')->select();
		$this->assign('category',$category);//导航
		$this -> display();
	}
	//注册用户首页
	public function index($sid=0,$p=1){

		$sid = intval($sid);
		$p = intval($p)>0?$p:1;

		$article = M('users');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count =  M('users')->table('su_users su')->field("su.*,sd.id,sd.pid,sd.area")->order("su.record_time desc")->join("su_districts sd on sd.id = su.district_id")->count();
		$list  = M('users')->table('su_users su')->field("su.*,sd.id,sd.pid,sd.area,sdd.area as areadis,sddd.area as areapro,sr.type")->order("su.record_time desc")
		->join("su_districts sd on sd.id = su.city_id",'left')
		->join("su_districts sdd on sdd.id = su.district_id",'left')
		->join("su_districts sddd on sddd.id = su.province_id",'left')
		->join("su_roles sr on sr.id = su.role_id",'left')
		->limit($offset.','.$pagesize)
		->select();
		$page	=	new \Think\Page($count,$pagesize);
		$page = $page->show();
        $this->assign('list',$list);
        $this->assign('page',$page);
		$this -> display();
	}

	//自有用户首页
	public function self_clients($sid=0,$p=1){

		$sid = intval($sid);
		$p = intval($p)>0?$p:1;

		$article = M('clients');
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count =  M('clients')->table('su_clients sc')->field("sc.*,sd.id,sd.pid,sd.area,sdd.area as areadis,sddd.area as areapro,sr.type")->order("sc.record_time desc")
			->join("su_districts sd on sd.id = sc.city_id",'left')
			->join("su_districts sdd on sdd.id = sc.district_id",'left')
			->join("su_districts sddd on sddd.id = sc.province_id",'left')
			->join("su_roles sr on sr.id = sc.role_id",'left')
			->count();
		$list  = M('clients')->table('su_clients sc')->field("sc.*,sd.id,sd.pid,sd.area,sdd.area as areadis,sddd.area as areapro,sr.type")->order("sc.record_time desc")
			->join("su_districts sd on sd.id = sc.city_id",'left')
			->join("su_districts sdd on sdd.id = sc.district_id",'left')
			->join("su_districts sddd on sddd.id = sc.province_id",'left')
			->join("su_roles sr on sr.id = sc.role_id",'left')
			->limit($offset.','.$pagesize)
			->select();
		$page	=	new \Think\Page($count,$pagesize);
		$page = $page->show();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this -> display();
	}

	public function del(){

		$aids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);

				$map['uid']  = array('in',$aids);
			}else{
				$map = 'uid='.$aids;
			}
			if(M('users')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}

	}

	public function edit($aid){

		$aid = intval($aid);
		$article = M('users')->where('uid='.$aid)->find();
		if($article){

			$category = M('users')->select();
			$this->assign('article',$article);//导航

			$this->assign('article',$article);
		}else{
			$this->error('参数错误！');
		}
		$this -> display();
	}

	public function self_edit($aid){

		$aid = intval($aid);

		$article = M('clients')->table("su_clients sc")->where('cid='.$aid)->field('sc.name,sc.cid,sc.password,sc.sex,sc.headimgurl,apn.phone_number,sse.email')
			->join('su_self_email sse on sse.eid = sc.self_email_id')
			->join("su_relation_phone_clients srpc on srpc.client_id = sc.cid")
			->join("su_phone_number apn on apn.id = sc.phone_number_id")
			->find();
		if($article){
			$category = M('clients')->select();
			$this->assign('article',$article);//导航

			$this->assign('article',$article);
		}else{
			$this->error('参数错误！');
		}
		$this -> display();
	}

	public function update($aid=0){
		$aid = isset($_GET['id'])?intval($_GET['id']):0;
		$data['uid'] = $aid;
		$data['name'] = I('post.name','','strip_tags');
		$data['password'] = I('post.password','','strip_tags');
		$data['password'] = md5($data['password']);
		$data['headimgurl'] = I('post.headimgurl','','strip_tags');
		$data['sex'] = I('post.sex','','strip_tags');
		$data['email'] = I('post.email','','strip_tags');
		$data['phone_number'] = I('post.phone_number','','strip_tags');
		if(empty($_POST['name']) and empty($_POST['password']) and empty($_POST['phone_number'])){
			$this->error('警告！用户名、密码及电话号码为必填项目。');
		}
		if($aid){
			$data['record_time'] = time();
			M('users')->data($data)->where('uid='.$aid)->save();
			addlog('编辑文章，AID：'.$aid);
			$this->success('恭喜！文章编辑成功！');
		}else{
			$data['ctime'] = time();
			$aid = M('users')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}

		}
	}

	//自有用户编辑提交页面
	public function self_update($aid=0){
		$aid = isset($_GET['id'])?intval($_GET['id']):0;
		$date['cid'] = $aid;
		$data['name'] = I('post.name','','strip_tags');
		$data['password'] = I('post.password','','strip_tags');
		$data['password'] = md5($data['password']);
		$data['headimgurl'] = I('post.headimgurl','','strip_tags');
		$data['sex'] = I('post.sex','','strip_tags');
		$data_dir['email'] = I('post.email','','strip_tags');
		$data_re['phone_number'] = I('post.phone_number','','strip_tags');
		if(empty($_POST['name']) and empty($_POST['password']) and empty($_POST['phone_number'])){
			$this->error('警告！用户名、密码及电话号码为必填项目。');
		}
		if($aid){
			$data['record_time'] = time();
			$client_res = M('clients')->where($date)->save($data);
			$cli_in = M('clients')->where($date)->find();
			$tem['eid'] = $cli_in['self_email_id'];
			$email_res =  M('self_email')->where($tem)->save($data_dir);
			$asp['client_id'] = $aid;
			$phone_res = M('relation_phone_clients')->where($asp)->find();

			$web['id'] = $phone_res['phone_id'];
			$phone_info = M('phone_number')->where($web)->save($data_re);

			if($client_res or $email_res or $phone_info){
				addlog('编辑文章，AID：'.$aid);
				$this->success('恭喜！文章编辑成功！');
			}else{
				$this->error('文章编辑失败！');
			}

		}else{
			$data['ctime'] = time();
			$client_res = M('clients')->where($date)->add($data);
			$cli_in = M('clients')->order('cid desc')->find();
			$tem['eid'] = $cli_in['self_email_id'];
			$email_res =  M('self_email')->where($tem)->add($data_dir);
			$asp['client_id'] = $aid;
			$phone_res = M('relation_phone_clients')->where($asp)->find();

			$web['id'] = $phone_res['phone_id'];
			$phone_info = M('phone_number')->where($web)->add($data_re);
			if($client_res or $email_res or $phone_info){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}

		}
	}
	//标签管理页面
	public function sign_index($p=1)
	{
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count =  M('labels')->order('ctime desc')->count();
		$list = M('labels')->order('ctime desc')->limit($offset.','.$pagesize)->select();
		$page	=	new \Think\Page($count,$pagesize);
		$page = $page->show();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	//标签添加页面
	public function sign_add()
	{
		$this->display();
	}
	//标签编辑页面
	public function sign_edit()
	{
		$aid = isset($_GET['aid'])?intval($_GET['aid']):0;
		$temp['id'] = $aid;
		$article = M('labels')->where($temp)->find();
		$this->assign('article',$article);
		$this->display();
	}
	//标签添加处理页面
	public function sign_update()
	{
		$aid = isset($_GET['id'])?intval($_GET['id']):0;
		$data['label_name'] = I('post.label_name','','strip_tags');
		$data['remark'] = I('post.remark','','strip_tags');
		if(empty($_POST['label_name'])){
			$this->error('警告！标签名、备注为必填项目。');
		}
		if($aid){
			$data['record_time'] = time();
			$res = M('labels')->data($data)->where('id='.$aid)->save();
			addlog('编辑文章，AID：'.$aid);
			$this->success('恭喜！文章编辑成功！');
		}else{
			$data['ctime'] = time();
			$aid = M('labels')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}

		}
	}
	//标签ajax处理是否标签名重复页面
	public function ajax_sign($sign_name)
	{
		$res_sign['label_name'] = $sign_name;
		$ajax = M('labels')->where($res_sign)->find();
		if(empty($ajax))
		{
			echo $signn = 1;
		}
		else
		{
			echo $signn = 2;
		}
	}
	//标签删除造操作
	public function sign_del()
	{
		$aids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);

				$map['id']  = array('in',$aids);
			}else{
				$map = 'id='.$aids;
			}
			if(M('labels')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}
	}

	//消息管理页面
	public function message_index()
	{
		if(isset($_GET['title'])){$data['title'] = array('like','%'.$_GET['title'].'%');}
	    $count=M("messages")->where($data)->count();
		$page=new \Think\Page($count,5);
		$pagestr=$page->show();
		$limit = $page->firstRow . ',' . $page->listRows;		
		$message = M('messages')->table('su_messages sm')->field('su.uid,su.name,su.role_id,scc.name as name1,sm.id as ids,sm.publish_time,sm.type,sm.title,sm.status,su.phone_number,sm.remark,sm.deline_time')->where($data)
		->join('su_users su on su.uid=sm.clients_id')
		->join('su_client_company scc on scc.id=su.company_id')
		->limit($limit)
		->select();
		$now = time();//dump($now);
		$this->assign("now",$now);
		$this->assign("pagestr",$pagestr);
		$this->assign('message',$message);
		$this->display();
	}
	//消息查看页面
	public function message_edit(){
		$aid = isset($_GET['aid'])?intval($_GET['aid']):0;
		$t3['sm.id'] = $aid;

		$article = M('orders')->table('su_orders so')->where($t3)
		->join('su_users su on su.uid = so.clients_id')
		->join('su_messages sm on sm.id = so.message_id')
		->field('sm.id,su.name,so.order_num,sm.area1,sm.area2,sm.detail_area1,detail_area2,so.remark,so.ctime,sm.price')
		->select();
		foreach($article as &$list){
			if(!empty($list['area1'])){
                $list['area1_str']=$this->getAllAddress($list['area1']);
            }
            if(!empty($list['area2'])){
                $list['area2_str']=$this->getAllAddress($list['area2']);
            }
		}
		$this->assign('list',$list);
		$this->assign('article',$article);
		$this->display();
	}
	//消息删除操作
	public function message_del()
	{
		$ID_ONE = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($ID_ONE){
			if(is_array($ID_ONE)){
				$aids = implode(',',$ID_ONE);
				$TT['id']  = array('in',$ID_ONE);
			}else{
				$TT = 'id='.$ID_ONE;
			}
			if(M('messages')->where($TT)->delete()){
			$this->success("内容删除成功","message_index"); 
			}else{
			$this->success("内容删除失败","message_index"); 
			}
		}else{
			$this->error('参数错误！');
		}
			if(isset($_GET['aids'])&&!empty($_GET['aids'])){
		   $id=$_GET['aids']; 
		   $t1=M("messages");
		   $t1->where('id='.$id)->delete();
		   if($t1){
			$this->success("内容删除成功","message_index"); 
		   }else{
			$this->success("内容删除失败","message_index"); 
		   }
		 }else{
			 echo "1";
		 }
	}
	//区域管理首页
	public function area_index($p=1)
	{
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count =   M('districts')->order('id asc')->count();
		$list = M('districts')->limit($offset.','.$pagesize)->select();
		$page	=	new \Think\Page($count,$pagesize);
		$page = $page->show();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	//区域管理编辑页面
	public function area_edit()
	{
		$aid = isset($_GET['aid'])?intval($_GET['aid']):0;
		$temp['id'] = $aid;
		$article = M('districts')->where($temp)->find();
		$this->assign('article',$article);
		//下拉父类别选择
		$category = M("districts")->select();
		$this->assign('category',$category);
		$this->display();
	}
	//区域管理编辑处理页面
	public function area_update()
	{
		$aid = isset($_GET['id'])?intval($_GET['id']):0;
		$data['pid'] = I('post.pid','','strip_tags');;
		$data['area'] = I('post.area','','strip_tags');
		if($aid){
			M('districts')->data($data)->where('id='.$aid)->save();
			addlog('编辑文章，AID：'.$aid);
			$this->success('恭喜！文章编辑成功！');
		}else{
			$aid = M('districts')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}

		}
	}
	//区域管理添加页面
	public function area_add()
	{
		$category = M('districts')->select();
		$this->assign('category',$category);
		$this->display();
	}
	//区域管理删除操作
	public function area_del()
	{
		$aids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);

				$map['id']  = array('in',$aids);
			}else{
				$map = 'id='.$aids;
			}
			if(M('districts')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}

	}
	//订单管理首页
	public function order_index()
	{	
		if(isset($_GET['name'])){$ty['su.name'] = array('like','%'.$_GET['name'].'%');}
		$count =   M('orders')->table('su_orders so')->where($ty)
		->join('su_users su on su.uid = so.clients_id')
		->join('su_messages sm on sm.id = so.message_id')
		->join('su_client_company scc on scc.id=su.company_id')
		->field('su.uid,su.name as name1,su.phone_number,so.ctime,so.remark,so.id,scc.name,sm.publish_time,sm.type,sm.title,sm.remark,sm.status,su.role_id')->count();	
		$page	=	new \Think\Page($count,10);
		$pagestr = $page->show();
		$limit = $page->firstRow . ',' . $page->listRows;	
		$list = M('orders')->table('su_orders so')->where($ty)
		->join('su_users su on su.uid = so.clients_id')
		->join('su_messages sm on sm.id = so.message_id')
		->join('su_client_company scc on scc.id=su.company_id')
		->field('su.uid,su.name as name1,su.phone_number,so.ctime,so.remark,so.id,scc.name,sm.publish_time,sm.type,sm.title,sm.remark,sm.status,su.role_id')
		->limit($limit)
		->select();
		$this->assign('list',$list);	
		$this->assign('pagestr',$pagestr);		
		$this->display();
	}
	//订单详情查看页面
	public function order_edit()
	{	
		$aid = isset($_GET['aid'])?intval($_GET['aid']):0;
		$t3['sm.id'] = $aid;

		$article = M('orders')->table('su_orders so')->where($t3)
		->join('su_users su on su.uid = so.clients_id')
		->join('su_messages sm on sm.id = so.message_id')
		->field('su.name,so.order_num,sm.area1,sm.area2,sm.detail_area1,detail_area2,so.remark,so.ctime,sm.price')
		->select();
		foreach($article as &$list){
			if(!empty($list['area1'])){
                $list['area1_str']=$this->getAllAddress($list['area1']);
            }
            if(!empty($list['area2'])){
                $list['area2_str']=$this->getAllAddress($list['area2']);
            }
		}
		$this->assign('list',$list);
		$this->assign('article',$article);
		$this->display();
	}

	//订单删除操作

		public function order_del(){
		$ID_ONE = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($ID_ONE){
			if(is_array($ID_ONE)){
				$aids = implode(',',$ID_ONE);
				$TT['id']  = array('in',$ID_ONE);
			}else{
				$TT = 'id='.$ID_ONE;
			}
			if(M('orders')->where($TT)->delete()){
			$this->success("内容删除完成","order_index");
			}else{
			$this->success("内容删除失败","roder_index"); 
			}
		}else{
			$this->error('参数错误！');
		}
		
			if(isset($_GET['aids'])&&!empty($_GET['aids']))
			{
			   $id=$_GET['aids'];
			   $t1=M("orders");
			   $t1->where('id='.$id)->delete();
			   if($t1){
				$this->success("内容删除完成","order_index");
			   }else{
				$this->success("内容删除失败","roder_index"); 
			   }
		 }else{
			 echo "1";
		 }
	}
	//角色管理首页
	public function roles_index($p=1)
	{
		$pagesize = 20;#每页数量
		$offset = $pagesize*($p-1);//计算记录偏移量
		$count =  M('roles')->order('ctime desc')->count();
		$list = M('roles')->order('ctime desc')->limit($offset.','.$pagesize)->select();
		$page	=	new \Think\Page($count,$pagesize);
		$page = $page->show();
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
	}
	//标签ajax处理是否标签名重复页面
	public function ajax_roles($sign_name)
	{
		$res_sign['type'] = $sign_name;
		$ajax = M('roles')->where($res_sign)->find();
		if(empty($ajax))
		{
			echo $signn = 1;
		}
		else
		{
			echo $signn = 2;
		}
	}
	//角色判断处理页面
	public function roles_update()
	{
		$aid = isset($_GET['id'])?intval($_GET['id']):0;
		$data['type'] = I('post.type','','strip_tags');
		$data['descri'] = I('post.descri','','strip_tags');
		$data['is_us'] = I('post.is_us','','strip_tags');
		$data['is_driver'] = I('post.is_driver','','strip_tags');
		$data['need_push'] = I('post.need_push','','strip_tags');
		if($aid){
			$data['update_time'] = time();
			$res = M('roles')->data($data)->where('id='.$aid)->save();
			addlog('编辑文章，AID：'.$aid);
			$this->success('恭喜！角色编辑成功！');
		}else{
			$data['ctime'] = time();
			$aid = M('roles')->data($data)->add();
			if($aid){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！角色新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}
		}
	}
	//角色编辑页面
	public function roles_edit()
	{
		$aid = isset($_GET['aid'])?intval($_GET['aid']):0;
		$temp['id'] = $aid;
		$article = M('roles')->where($temp)->find();
		$this->assign('article',$article);
		$this->display();
	}
	//角色删除操作
	public function roles_del()
	{
		$aids = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($aids){
			if(is_array($aids)){
				$aids = implode(',',$aids);

				$map['id']  = array('in',$aids);
			}else{
				$map = 'id='.$aids;
			}
			if(M('roles')->where($map)->delete()){
				addlog('删除文章，AID：'.$aids);
				$this->success('恭喜，文章删除成功！');
			}else{
				$this->error('参数错误！');
			}
		}else{
			$this->error('参数错误！');
		}
	}
	//评论显示页面
	public function comment_index(){
		$list=M('orders_official_comment')->select();
		$this->assign('list',$list);
		$this->display();
	}
		public function comment_edit()
	{
		$aid = isset($_GET['aid'])?intval($_GET['aid']):0;
		$temp['id'] = $aid;
		$list = M('orders_official_comment')->where($temp)->find();
		$this->assign('list',$list);
		$this->display();
	}
		public function comment_edit_do(){
		$id=$_GET['id'];
		$data['comment_content']=$_POST['comment_content'];
		$res = M("orders_official_comment")->where("id=".$id)->save($data);
		if($res)
		{
			$this->success("内容修改完成","comment_index");
		}else{
			$this->error("内容修改失败","comment_index");
		}
		
	}
	public function comment_add(){
		$this->display();
	}
	public function comment_add_do(){
		$a['comment_content']=$_POST['comment_content'];
		$res=M("orders_official_comment")->add($a);	
		if($res)
		{
			$this->success("内容添加完成","comment_index");
		}else{
			$this->error("内容添加失败","comment_index");
		}		
	}
	public function comment_del(){
			if(isset($_GET['aids'])&&!empty($_GET['aids'])){
		   $id=$_GET['aids'];
		   $t1=M("orders_official_comment");
		   $t1->where('id='.$id)->delete();
		   if($t1){
			$this->success("内容删除完成","comment_index");
		   }else{
			$this->error("内容删除失败","comment_index");
		   }
		 }else{
			 echo "1";
		 }
		$ID_ONE = isset($_REQUEST['aids'])?$_REQUEST['aids']:false;
		if($ID_ONE){
			if(is_array($ID_ONE)){
				$aids = implode(',',$ID_ONE);
				$TT['id']  = array('in',$ID_ONE);
			}else{
				$TT = 'id='.$ID_ONE;
			}
			if(M('orders_official_comment')->where($TT)->delete()){
			$this->success("内容删除完成","comment_index");
			}else{
			$this->success("内容删除失败","comment_index"); 
			}
		}else{
			$this->error('参数错误！');
		}
	}
}