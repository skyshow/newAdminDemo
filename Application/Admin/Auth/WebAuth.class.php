<?php 
namespace Admin\Auth;

/**
 * 
 * PHP version 5.5
 *
 * @category	Admin
 * @package     Admin
 * @subpackage  Model
 * @copyright   2016 GZSD
 * @version     GIT: index.php 2016-03-10 14:23:59 Huangzj $
 */

class WebAuth{
	/**
	 * 写入session
	 *
	 * @param string|array $var
	 * @param string $value
	 * @return void
	 * @access public
	 */
	public function write($var, $value=null)
	{
		if (is_array($var))
		{
			foreach ($var as $key => $value)
			{
				session($key, $value);
			}
		}
		else
		{
			session($var);
		}
	}
	
	/**
	 * 读取session
	 *
	 * @param string $var
	 * @return mixed
	 * @access public
	 */
	public function read($var=null)
	{
		if($var)
		{
			return session($var);;
		}
	}
	
	
	/**
	 * session值是否为空
	 *
	 * @param string $var 变量名
	 * @return bool 为空，返回true，否则返回false
	 * @access public
	 */
	public function isEmpty($var){$v=session($var); return empty($v);}
	public function isInit($var){return session("?{$var}");}
	
	
	
	/**
	 * 删除session值
	 *
	 * @param string $var
	 * @return void
	 */
	public function destroy($var=null)
	{
		if($var)
		{
			session($var,null);
		}
		else
		{
			session(null);
		}
	}
	
	protected function _completeDestroy()
	{
		session('[destroy]');
	}
}