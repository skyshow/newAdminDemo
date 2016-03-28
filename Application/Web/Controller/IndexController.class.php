<?php
namespace Web\Controller;

class IndexController extends CommonController {
    
	/**
	 * 官网首页
	 */
    public function index(){
		
    	
        $info['notice']  = D('WstContent')->getContent(0,4,9); //公告通知
        $info['zxzx']    = D('WstContent')->getContent(0,5); //最新资讯
        $info['zhxx']    = D('WstContent')->getContent(0,5,377); //政府综合信息
        $info['spxw']    = D('WstContent')->getContent(0,5,363); //视频新闻
        $info['tpxw']    = D('WstContent')->getContent(0,5,18); //图片新闻
        $info['xxgk']    = D('WstContent')->getContent(0,12,330); //信息公开
        $info['leader']  = D('WstLd')->getContent(0,3); //领导

        $this->assign('info',$info);
    	
    	$this->display();
    	
    }

    //信息公开
    public function msgPublish(){
        $list = D('WstContent')->getContentRecursion(0,10,1);
        $count = D('WstContent')->getContentRecursionCount(1);

        $hitlist = D('WstContent')->getContentRecursionOrderByHit(0,10,1);
        $this->assign('hitlist',$hitlist);

        $page = new \Think\Page($count,25);
        $show = $page->show();
        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display();
    }

    //信息公开
    public function msgPublishDetail(){
        $this->display();
    }

    //网上办事
    public function onlineWork(){
        $banshi = D('WstContent')->getContent(0,10,46);
        $biaoge = D('WstContent')->getContent(0,10,188);
        //print_r($banshi);
        $this->assign('banshi',$banshi);
        $this->assign('biaoge',$biaoge);
        $this->display();
    }

    //网上办事
    public function onlineWorkDetail(){
        $this->display();
    }

    //便民服务
    public function easierPeople(){
        $this->display();
    }

    //政务专题
    public function govSubject(){
        $this->display();
    }
}