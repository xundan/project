<?php
namespace Home\Controller;
use Think\Controller;
class ComController extends Controller {
	public function _initialize(){
		if(empty($_SESSION['user_info'])){
			$this->error("请先登陆",U('Home/Login/login_yzm'));
		}
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
    public function getProductAttr($product_id){
        $productStrArray=array();
//        $product=M('Product');
        $tempProduct=M('product')->find($product_id);
//        $tempProduct为产品记录，包含产品各种信息
        $productStrArray['sulfur']=$tempProduct['sulfur'];
        $productStrArray['ash_content']=$tempProduct['ash_content'];
        $productStrArray['total_water']=$tempProduct['total_water'];
        $productStrArray['volatile_parts']=$tempProduct['volatile_parts'];
//        煤炭种类名
        $temp=M('product_type')->find($tempProduct['protype_id']);
        $tempStr=$temp['type'];
        $productStrArray['type']=$tempStr;
        //        煤炭品质名
        $temp=M('product_descri')->find($tempProduct['descri_id']);
        $tempStr=$temp['descri'];
        $productStrArray['descri']=$tempStr;
        //        煤炭种类名
        $temp=M('product_granularity')->find($tempProduct['granularity_id']);
        $tempStr=$temp['granularity'];
        $productStrArray['granularity']=$tempStr;
        return $productStrArray;
    }
    public function getDelineTime(){
        $role_id=$_SESSION['user_info']['role_id'];
        if($role_id==null){
            return strtotime('+3 day');
        }
        if($role_id==0){
            return strtotime('+3 day');
        }
        if($role_id==1){
            return strtotime('+7 day');
        }

    }
    /*
    * 判断省市区级别
    */
    public function getLevel($id){
        $i=100000;
        $j=0;
        $idTemp=$id;
        $districtModel=M("districts");
        while($i>0){
            $tempStr=$districtModel->find($idTemp);
            $i=$tempStr['pid'];
            $idTemp=$tempStr['pid'];
            $j=$j+1;
        }
        //$districtModel->save(array("id"=>$id,"dis_type"=>$j));
        return $j;
    }
    public function saveL(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $districtModels=M("districts")->select();
        foreach($districtModels as $ddd){
            $this->getLevel($ddd['id']);
        }
        echo 1;
    }
}