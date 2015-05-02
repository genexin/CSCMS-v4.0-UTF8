<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Session {

	var $lifetime = 1800; //session默认保存时间
	var $table = 'session';

	function __construct()
	{
		   log_message('debug', "Native Session Class Initialized");
		   if(CS_Session_Is==2){ //MYsql
		           $this->CI =& get_instance();
	 	           if (!isset($ci->db)){
        	               $this->CI->load->database();
	 	           }
    	           session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
		   }else{  //FIles
		           $path = FCPATH."cache/sessions/";
		           ini_set('session.save_handler', 'files');
		           ini_set('session.gc_divisor', 1);  
		           ini_set('session.gc_maxlifetime', $this->lifetime);
		           ini_set('session.cookie_lifetime',$this->lifetime);
		           session_save_path($path);
		   }
           session_start();
	}

    //session_set_save_handler  open方法
    public function open($save_path, $session_name) {
		return true;
    }

    //session_set_save_handler  close方法
    public function close() {
        return $this->gc($this->lifetime);
    } 

    //读取session_id
    public function read($id) {
		$this->CI->db->where('sessionid',$id);
		$this->CI->db->select('data');
		$row = $this->CI->db->get($this->table)->row();
		return $row ? $row->data : '';
    } 

    //写入session_id 的值
    public function write($id, $data) {
    	$uid = isset($_SESSION['cscms__id']) ? $_SESSION['cscms__id'] : 0;
		$plub = defined('PLUBPATH') ? PLUBPATH : '';
		if(strlen($data) > 255) $data = '';
		$ip = getip();
		$sessiondata = array(
							'sessionid'=>$id,
							'uid'=>$uid,
							'ip'=>$ip,
							'addtime'=>time(),
							'plub'=>$plub,
							'data'=>$data,
						); 
		$this->CI->db->where('sessionid',$id);
		$row = $this->CI->db->get($this->table)->row();
		if($row){
			unset($sessiondata['sessionid']);
			$this->CI->db->where('sessionid',$id);
	        $this->CI->db->update($this->table,$sessiondata);
			return true; 
		}else{
			if(!empty($data)){
		        $this->CI->db->insert($this->table,$sessiondata);
			}
		    return true; 
		}
    }

    //删除指定的session_id
    public function destroy($id) {
		$this->CI->db->where('sessionid',$id);
		return $this->CI->db->delete($this->table);
    }

    //删除过期的 session
    public function gc($maxlifetime) {
		$expiretime = time() - $maxlifetime;
		return $this->CI->db->query("delete from ".CS_SqlPrefix.$this->table." where addtime<$expiretime");
    }
}
