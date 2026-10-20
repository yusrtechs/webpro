<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_blog extends CI_Migration {

    public function up() {

		$otherdb = $this->load->database('info', true);
            //$clients = $otherdb->get('client')->row();
				 //$otherdb->where('isAccounting',1);

			 $q = $otherdb->get('client');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $clients[] = $row;
            }
		}

        $otherdb = $this->load->database('onlineinfo', true);
            //$clients = $otherdb->get('client')->row();
				 //$otherdb->where('isAccounting',1);

			 $q = $otherdb->get('client');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $clients[] = $row;
            }
		}




        $add_col1 = array(
            'spark_ui' => array('type' => 'TINYINT', 'constraint' => '1' , 'null' => FALSE ,'default' => 0  )
        );


		/*$add_data = array(
					array('id' => 10,
						  'name' => "تحويلات بنكية",
						  'second_name' => "Bank Transfer",
						  'isActive' => "1",
						  'icon' => NUll,
						  'isCredit' => "0",
						  'code' => "bank_transfer",
						  'ischeque' => "0",
						  'isCC' => "0",
						  )

					);
		*/

        foreach ($clients as $client) {


			$company = $client->company;
			$config['dsn'] = '';
            $config['hostname'] = 'localhost';
            $config['username'] = 'cpesoft';
            $config['password'] = 'enter-password';
            $config['database'] = 'cpesoft_'.$company;
            //$config['username'] = 'root';
            //$config['password'] = '';
            //$config['database'] = $company;
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = 'sma_';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = FALSE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';
            $config['cachedir'] = '';
            $config['swap_pre'] = '';
            $config['encrypt'] = FALSE;
            $config['compress'] = FALSE;
            $config['stricton'] = FALSE;
            $config['failover'] = array();
            $config['save_queries'] = FALSE;

            $CI = &get_instance();
            $data = $CI->load->database($config, TRUE);
			$this->mydb =$data;
			 $this->myforge  = $this->load->dbforge($data, TRUE);

			   //$this->myforge->add_column('pos_settings', $add_col); // blog its refere to Table
			   $this->myforge->add_column('settings', $add_col1); // blog its refere to Table
			   //$this->myforge->add_column('settings', $add_col2); // blog its refere to Table
			   //$this->myforge->add_column('pos_settings', $add_col2); // blog its refere to Table
			   //$this->myforge->modify_column('users', $add_col1); // blog its refere to Table
			   //$this->myforge->add_column('purchases', $add_col1); // blog its refere to Table
			   //if($client->isAccounting==1)
			   //$this->myforge->add_column('gl_accounts_settings', $add_col3); // blog its refere to Table

		//$this->mydb->insert('payment_type', ['id'=>'10','name' => 'تحويلات بنكية','second_name' => 'Bank Transfer','isActive' => '1','icon' => NUll,'isCredit' => '0','code' => 'bank_transfer','ischeque' => '0','isCC' => '0']);
		//$this->sma->print_arrays($this->myforge);

       // $this->mydb->insert('payment_type', ['id'=>10,'name' => "تحويلات بنكية",'second_name' => "Bank Transfer",'isActive' => "1",'icon' => NUll,'isCredit' => "0",'code' => "bank_transfer",'ischeque' => "0",'isCC' => "0"]);

		}
      /*  $add = array(
            'expense_prefix' => array('type' => 'varchar', 'constraint' => '20', 'null' => TRUE)
        );
        $this->dbforge->add_column('blog', $add);
        $add = array(
            'ex' => array('type' => 'int', 'constraint' => '11', 'default' => 1)
        );
        $this->dbforge->add_column('blog', $add);
        */
    }

}