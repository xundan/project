<?php
/**
 * 小消息的审核与更新
 * User: LX
 * Date: 2016/8/12
 * Time: 10:29
 */

namespace Views\Controller;

use Think\Controller;

class DisplayMessagesController extends Controller
{

    public function showDemo($id)
    {
//        dump($_SESSION['cur_user']);
        if (true) {
//        if ($_SESSION['cur_user']) {
            // 有where包装的时候不要直接find($id)，否则where会失效
            $data = D('DisplayMessages')->where("invalid_id=0 AND status=0 AND id=" . $id)->find();
            $this->assign("data", $data);
            $this->assign("id", $id);
            $cur_user = $_SESSION['cur_user'];
            $username = $cur_user['name'];
            $this->assign('username', $username);
            $id_minus = $this->find_prev($id);
            $id_plus = $this->find_next($id);
            if (!$data) {
                echo "<br><h1>没有更多数据了。</h1>";
            }

            if ($id_minus == -1) {
                echo "<br><h1>已经是第一条了。</h1>";
                $id_minus = $id;
            }
            if ($id_plus == -1) {
                echo "<br><h1>已经是最后一条了。</h1>";
                $id_plus = $id;
            }
            $url_prev = U('Views/DisplayMessages/showDemo') . "?id=$id_minus";
            $url_next = U('Views/DisplayMessages/showDemo') . "?id=$id_plus";
//        $url_delete = U('Views/DisplayMessages/delete')."?id=$id";
            $this->assign("prev", $url_prev);
            $this->assign("next", $url_next);
            $this->display();
        } else {
            header("Content-Type:text/html; charset=utf-8");//解决乱码
            $this->redirect('StaffsLogin/Login', '', 3, "您并没有登录，正在返回登录");
        }
    }

    public function check($id)
    {
        $tags = I('post.tag');
        // 检测tags里有没有包含必选项
        $main_tag = $this->is_check_valid($tags);

        if ($main_tag) {
            // 更新与标签关系表
            $wx_arr = $this->update_relation_label($id, $tags);
            // 更新与微信关系表
            $this->add_relation_wx($id, $wx_arr);
            // 更新消息表状态
            $this->update_message($id, $main_tag);

            $this->success('提交成功', 'showDemo?id=' . $this->find_next($id));
        } else {
            $this->error('四个主要类型（求购，供应，求车，其他）至少选一个。', 'showDemo?id=' . $id);
        }
    }

    public function add_relation_wx($id, $wx_arr)
    {
        // 更新前把旧数据都删掉
        $delete_data['invalid_id'] = 2;
        D('RelationM2W')->where("msg_id='$id'")->save($delete_data);

        // 添加新的关系数据
        foreach ($wx_arr as $wx) {
            $data['msg_id'] = $id;
            $data['wx'] = $wx;
            $data['invalid_id'] = 0;
            D("RelationM2W")->add($data);
        }

    }

    private function exist($rid, $label_name)
    {
        if (D('RelationLabel')->
        where("object_rid='%s' and label_name='%s'", array($rid, $label_name))->find()
        ) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $update_delete = array(
            "id" => $id,
            "status" => -1,
            "invalid_id" => 2,
        );

        if (D('DisplayMessages')->save($update_delete)) echo 'deleted';
    }

    public function find_prev($id)
    {
        if ($id < 1) return -1;
        $prev = D('DisplayMessages')->where("invalid_id=0 AND status=0 AND id<" . $id)
            ->order('id desc')->find();
        if ($prev) {
            return $prev['id'];
        } else {
            return -1;
        }
    }

    public function find_next($id)
    {
        $next = D('DisplayMessages')->where("invalid_id=0 AND status=0 AND id>" . $id)
            ->order('id asc')->find();
        if ($next) {
            return $next['id'];
        } else {
            return -1;
        }
    }

    /**
     * 将tag下的微信号去重后放入wx_arr数组中并返回
     * @param $tag
     * @param $arr
     * @return array
     */
    public function assemble($tag, $arr)
    {
        $wx_arr = $arr;
// 查询该label对应的微信号，放入数组
        $labels = D('Label')->where("invalid_id=%d and label_name='%s'", array(0, $tag))->select();
        if ($labels) {
            // 遍历所有$tag作为标签名对应的微信号
            foreach ($labels as $label_info) {
                $exist_flag = false;
                // 检查$wx_arr的重复性
                foreach ($wx_arr as $wx) {
                    if ($wx == $label_info['remark']) {
                        $exist_flag = true;
                        break;
                    }
                }
                if (!$exist_flag) {
                    array_push($wx_arr, $label_info['remark']);
                }
            }
        }
        return $wx_arr;
    }

    /**
     * 根据信息标的id和标签数组更新 与标签关系表 返回id对应的wx号列表
     * @param $id
     * @param $tags
     * @return array
     */
    public function update_relation_label($id, $tags)
    {
        $wx_arr = array();
        $not_in_where = "label_name not in (";
        $rid = 'MSSG' . $id;
        $Relations = D('RelationLabel');
        foreach ($tags as $tag) {
            $data['object_rid'] = $rid;
            $data['label_name'] = $tag;
            $data['invalid_id'] = 0;
            // 如果关系已经存在，刷新有效性，否则就insert
            if ($this->exist($data['object_rid'], $data['label_name'])) {
                // 如果数据库没有变化，下面表达式会返回0，（所以不能把它作为成功失败的判断依据）
                // save只返回变化的行数
                $Relations->
                where("object_rid='%s' and label_name='%s'", array($rid, $tag))->save($data);
            } else {
                $Relations->add($data);
            }
            $wx_arr = $this->assemble($tag, $wx_arr);
            $not_in_where .= "'" . $tag . "',"; //拼接
        }
        $not_in_where .= "'whereEnd')"; // 作为单独结束
        $bad_data['invalid_id'] = 2; // 审核不通过
        // 把同ID下取消掉的标签改为审核不通过
        $Relations->where("object_rid='$rid' and " . $not_in_where)->save($bad_data);
        return $wx_arr;
    }

    /**
     * @param $id
     * @param $main_tag
     */
    private function update_message($id, $main_tag)
    {
        $update_trans = array(
            "id" => $id,
            "category" => $main_tag,
            "status" => 102,
        );
        $check = D('Message')->save($update_trans);
        if ($check == false) {
            //TODO 写日志
            $this->error('提交失败401', 'showDemo?id=' . $id);
        }
    }

    /**
     * @param $tags
     * @return bool
     */
    private function is_check_valid($tags)
    {
        $valid_check = false;
        foreach ($tags as $tag) {
            if ($tag == "求购" || $tag == "供应" || $tag == "求车" || $tag == "其他") {
                $valid_check = $tag;
                break;
            }
        }
        return $valid_check;
    }


}