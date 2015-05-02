<?php 
/**
 * @Cscms 4.x open source management system
 * @copyright 2009-2015 chshcms.com. All rights reserved.
 * @Author:Cheng Jie
 * @Dtime:2014-12-09
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hits extends Cscms_Controller {

	function __construct(){
		  parent::__construct();
		  //关闭数据库缓存
          $this->db->cache_off();
	}

    //人气
	public function index()
	{
           $ac = $this->uri->segment(3);   //方式
           if($ac=='id'){

                $id = intval($this->uri->segment(4));   //ID
		        $row=$this->CsdjDB->get_row('user','rhits,zhits,yhits,hits',$id);
		        if(!$row){
			        exit();
		        }
                //增加人气
	            $updata['rhits']=$row->rhits+1;
	            $updata['zhits']=$row->zhits+1;
	            $updata['yhits']=$row->yhits+1;
	            $updata['hits']=$row->hits+1;
                $this->CsdjDB->get_update ('user',$id,$updata);

           }elseif($ac=='dt'){   //静态模式、动态人气

                $op = $this->uri->segment(4);   //类型
                $id = intval($this->uri->segment(5));   //ID

				$dos = array('hits', 'yhits', 'zhits', 'rhits', 'zanhits');
                $op= (!empty($op) && in_array($op, $dos))?$op:'hits';

		        $row=$this->CsdjDB->get_row('user',$op,$id);
		        if(!$row){
			        echo "document.write('0');";
		        }else{
                    echo "document.write('".$row->$op."');";
				}
           }
	}
}
