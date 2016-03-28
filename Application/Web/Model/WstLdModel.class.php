<?php 
/**
 * 领导表
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

class WstLdModel extends Model {
    
    protected $tableName = 'wst_ld';
	
    public function getContent($page,$pagesize){
        
        $data = $this->where(array('scxm'=>array('egt',1)))->order('pyfy ASC')->limit($page,$pagesize)->select();
        $data = string_gb2312_to_utf8($data);
        foreach($data as $k=>$v){
                      
        }
        return $data;
        
    }
	
}