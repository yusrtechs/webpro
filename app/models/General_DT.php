<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class General_DT extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query($parms ) {

 
$parms['conditional_wheres'] = (array_key_exists('conditional_wheres', $parms))?  $parms['conditional_wheres'] : [];
		if($parms['select']){
          $this->db->select($parms['select']);
		}


 	   $this->db->from($parms['table']);




	   $parms['where'] = array_key_exists('where', $parms) ? $parms['where'] : [];

        if (count($parms['where'])) {

            foreach ($parms['where'] as $pt1 => $pt2) {
                if ($pt2 != '-') {
                    $this->db->where($pt1, $pt2);
                } else {
                    $this->db->where($pt1);
                }
            }
        }
        $parms['or_where'] = array_key_exists('or_where', $parms) ? $parms['or_where'] : [];
        if (count($parms['or_where'])) {
            $this->db->group_start();
            foreach ($parms['or_where'] as $pt1 => $pt2) {
                if ($pt2 != '-') {
                    $this->db->or_where($pt2);
                } else {
                    $this->db->or_where($pt1);
                }
            }
            $this->db->group_end();
        }
        $parms['join'] = array_key_exists('join', $parms) ? $parms['join'] : [];


        if (count($parms['join'])) {
            foreach ($parms['join'] as $table => $on) {
                if(is_array($on)){
                    $on[1] = array_key_exists(1,$on)? $on[1] : 'left';
                    $this->db->join($table, $on[0],$on[1]);

                }else{
                    $this->db->join($table, $on);

                }

            }
        }


        $parms['or_wheres'] = (array_key_exists('or_wheres', $parms)) ? $parms['or_wheres'] : [];
        if (count($parms['or_wheres'])) {
            foreach ($parms['or_wheres'] as $where) {
                $this->db->group_start();

                foreach ($where as $pt1 => $pt2) {
                    if ($pt2 != '-') {
                        $this->db->or_where($pt2);
                    } else {
                        $this->db->or_where($pt1);
                    }
                }
                $this->db->group_end();
            }
         }
        $parms['conditional_wheres'] = (array_key_exists('conditional_wheres', $parms)) ? $parms['conditional_wheres'] : [];
        if (count($parms['conditional_wheres'])) {
            foreach($parms['conditional_wheres'] as $where){
				$cond = eval($where['condetion']['a'] . $where['condetion']['condetion'] . $where['condetion']['b']);
				if($cond){
					$this->db->where($where['condetion']['where']);
				}else{
					$this->db->where($where['condetion']['where_else']);

				}
 			}

		}

        $i = 0;
        $parms['column_search'] = (array_key_exists('column_search', $parms))?  $parms['column_search'] : [];

        if (array_key_exists('search', $_POST) && $parms['column_search']) {
           // print_r($_POST);
           // die;
            $column_search = $parms['column_search'];
            $i = 0;
            foreach ($column_search as $item) { // loop column
                 if ($i === 0) { // first loop
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                 if ((count($column_search) - 1) == $i) { //last loop
                    $this->db->group_end(); //close bracket
                }

//  }
                $i++;
            }


        }

        $_POST['order'] = (array_key_exists('order', $_POST)) ? $_POST['order'] : [];
        if (count($_POST['order']) > 0) {
            if (array_key_exists('column_order', $parms)) {
                foreach ($_POST['order'] as $k => $v) {
                    $this->db->order_by($parms['column_order'][$v['column']], $v['dir']);
                }
            }
        } else {
            $parms['order_by'] = (array_key_exists('order_by', $parms)) ? $parms['order_by'] : [];

            if (count($parms['order_by'])) {
                foreach ($parms['order_by'] as $k => $order_by) {
                    $this->db->order_by($k, $order_by);
                }
                //    $order_by_2  = ($parms['order_by'][1] != '') ? $parms['order_by'][1] : 'ASC';
                //   $this->db->order_by( $parms['order_by'][0] ,  $order_by_2 );
            }
        }



        $parms['group_by'] = (array_key_exists('group_by', $parms))?  $parms['group_by'] : false;
 		if($parms['group_by']){
         $this->db->group_by( $parms['group_by']);
		}
        $parms['having'] = (array_key_exists('having', $parms))?  $parms['having'] : false;

        if($parms['having']){
                if( $parms['having'][2] == '0' && $parms['having'][1] == '='){
                    $this->db->having($parms['having'][0] . "= '0'  or " . $parms['having'][0]  . " is NULL");
                }else{
                    $this->db->having( $parms['having'][0] . ' ' . $parms['having'][1] . ' ' . $parms['having'][2]);
                }



           }
   


}

    function get_datatables($parms,$returnType = '') {

        $this->_get_datatables_query($parms);
        if (!isset($_POST['start'])) {
            $_POST['start'] = 0;
        }
        $_POST['length'] = array_key_exists('length' , $_POST) ? $_POST['length'] : 10;
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

		//if ($limit)
         //  $this->db->limit($limit);

        $query = $this->db->get();


      if ($query) {
          if($returnType == 'OBJ'){
            return $query->result();
          }else{
            return $query->result_array();
          }
        } else {
            return array();
        }
    }

    function count_filtered($parms) {


       $this->_get_datatables_query($parms);

       $query = $this->db->get();



        return is_object($query) ? $query->num_rows() : 0;
    }

    public function count_all($parms) {
        $this->db->select('*');
        $this->db->from($parms['table']);
	   $parms['where'] = array_key_exists('where', $parms) ? $parms['where'] : [];
        if (count($parms['where']) > 0) {
            foreach($parms['where'] as $pt1 => $pt2){
					if($pt2 != '-'){
					$this->db->where($pt1 , $pt2);
					}else{
					$this->db->where($pt1 );
					}



				}

			}
        $parms['join'] = array_key_exists('join', $parms) ? $parms['join'] : [];

        if (count($parms['join'])) {
            foreach ($parms['join'] as $table => $on) {
                if(is_array($on)){
                    $on[1] = (array_key_exists('1', $on)) ? $on[1] : 'left';
                    $this->db->join($table, $on[0],$on[1]);

                }else{
                    $this->db->join($table, $on);

                }
            }
        }

         //$this->db->where('s_p_id', $section_id);

       $query = $this->db->get();
        return is_object($query) ? $this->db->count_all_results() : 0;
    }

}
