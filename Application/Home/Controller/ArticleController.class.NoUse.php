<?php
namespace Home\Controller;
use Think\Controller;
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
		//echo M()->getlastsql();exit;
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
			//$ask['client_id']
			//$relation = M('relation_phone_clients')->where($ask)->add($sum);
			if($client_res or $email_res or $phone_info){
				addlog('新增文章，AID：'.$aid);
				$this->success('恭喜！文章新增成功！');
			}else{
				$this->error('抱歉，未知错误！');
			}

		}
	}
	public function sign_index()
	{
		$list = M('relation_label')->table('su_relation_label srl')->field('sl.*,srl.object_rid')->order('sl.ctime desc')
			->join('su_labels sl on sl.relation_label_id = srl.id')
			->select();
		$this->assign('list',$list);
		$this->display();
	}
}