<?php 
/**
 * 公告通知表
 * PHP version 5.5
 *
 * @category	Web
 * @package     Web
 * @subpackage  Model
 * @copyright   2016 GDWST
 * @version     GIT: index.php 43 2016-01-14 21:23:59 Huangzj $
 */
namespace Web\Model;
use Think\Model;

class WstContentModel extends Model {
    
    protected $tableName = 'wst_content';
	
    public function getContent($page,$pagesize,$sortid=null){
        
    	if($sortid) $cond['sortid'] = $sortid;
    	
        $data = $this->field('articleid,title,addtime')
                     ->where($cond)
                     ->order('articleid DESC')
                     ->limit($page,$pagesize)
                     ->select();
        
        $data = string_gb2312_to_utf8($data);
        foreach($data as $k=>$v){
            $data[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
            $data[$k]['addtime1'] = date('m.d',strtotime($v['addtime']));
        }
        return $data;
        
    }


    //递归获取某个分类的内容
    public function getContentRecursion($page,$pagesize,$sortid){
        $sorts = M('wst_sort')->where("sortid=$sortid or parentid=$sortid")->field('sortid')->select(false);
        $data = $this->where("sortid in ($sorts)")->limit($page,$pagesize)->order('articleid DESC')->select();
        $data = string_gb2312_to_utf8($data);
        foreach($data as $k=>$v){
            $data[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
            $data[$k]['addtime1'] = date('m.d',strtotime($v['addtime']));
            $data[$k]['subcontent'] = utf8_strcut(strip_tags($data[$k]['content']),0 ,500);
        }
        return $data;
    }

    //递归获取某个分类的内容
    public function getContentRecursionOrderByHit($page,$pagesize,$sortid){
        $sorts = M('wst_sort')->where("sortid=$sortid or parentid=$sortid")->field('sortid')->select(false);
        $data = $this->where("sortid in ($sorts)")->limit($page,$pagesize)->order('hits DESC')->select();
        $data = string_gb2312_to_utf8($data);
        foreach($data as $k=>$v){
            $data[$k]['addtime'] = date('Y-m-d',strtotime($v['addtime']));
            $data[$k]['addtime1'] = date('m.d',strtotime($v['addtime']));
            $data[$k]['subcontent'] = utf8_strcut(strip_tags($data[$k]['content']),0 ,500);
        }
        return $data;
    }


    //递归获取某个分类的内容的数量
    public function getContentRecursionCount($sortid){
        $sorts = M('wst_sort')->where("sortid=$sortid or parentid=$sortid")->field('sortid')->select(false);
        $count = $this->where("sortid in ($sorts)")->count(1);
        return $count;
    }
   
	
}