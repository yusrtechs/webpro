<?php
defined('BASEPATH') or exit('No direct script access allowed');
//ini_set('memory_limit', '-1');
//ini_set('max_execution_time', '-1');

class Accounts extends MY_Controller
{

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function __construct()
	{
		parent::__construct();
		if (!$this->loggedIn) {
			$this->session->set_userdata('requested_page', $this->uri->uri_string());
			$this->sma->md('login');
		}
		if ($this->Supplier) {
			$this->session->set_flashdata('warning', lang('access_denied'));
			redirect($_SERVER['HTTP_REFERER']);
		}
		$this->lang->admin_load('sales', $this->Settings->user_language);
		$this->lang->admin_load('quotations', $this->Settings->user_language);
		$this->lang->admin_load('returns', $this->Settings->user_language);
		$this->lang->admin_load('reports', $this->Settings->user_language);
		$this->load->library('form_validation');
		$this->load->admin_model('accounts_model');
		$this->load->admin_model('db_model');


		$this->digital_upload_path = 'files/';
		$this->upload_path         = 'assets/uploads/';
		$this->thumbs_path         = 'assets/uploads/thumbs/';
		$this->image_types         = 'gif|jpg|jpeg|png|tif';
		$this->digital_file_types  = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
		$this->allowed_file_size   = '1024';

		if (!$this->Settings->accounting_type) {
			$this->session->set_flashdata('error', lang('Please_select_a_type_of_accounting'));
			admin_redirect('system_settings');
		}
	}

	public function index()
	{
		$this->dashboard();
	}

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function accounts_tree()
	{
		$this->sma->checkPermissions('accounts_tree', true);
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');


		//$this->sma->print_arrays($this->Settings->user_language);
		$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Accounts_tree')]];
		$meta = ['page_title' => lang('Accounts_tree'), 'bc' => $bc];
		$this->page_construct('accounts/accounts_tree', $meta, $this->data);
		//$this->load->view('items');
	}

	public function cost_centers()
	{
		$this->sma->checkPermissions('cost_centers', true);
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');


		//$this->sma->print_arrays($this->Settings->user_language);
		$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Cost_centers')]];
		$meta = ['page_title' => lang('Cost_centers'), 'bc' => $bc];
		$this->page_construct('accounts/cost_centers', $meta, $this->data);
		//$this->load->view('items');
	}
	/*
	public function accounts_types()
    {
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Accounts_types')]];
        $meta                = ['page_title' => lang('Accounts_types'), 'bc' => $bc];
        $this->page_construct('accounts/accounts_types', $meta, $this->data);
    }
*/
	public function payment_types()
	{
		$this->sma->checkPermissions('payment_types_index', true);
		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');

		$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Payment_types')]];
		$meta                = ['page_title' => lang('Payment_types'), 'bc' => $bc];
		$this->page_construct('accounts/payment_types', $meta, $this->data);
	}

	public function accounting_period()
	{
		$this->sma->checkPermissions('accounting_period_index', true);
		$this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		$this->data['accounts_settings']         = $this->accounts_model->getAccountsSettings();
		$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Accounting_Period')]];
		$meta                = ['page_title' => lang('Accounting_Period'), 'bc' => $bc];
		$this->page_construct('accounts/accounting_period', $meta, $this->data);
	}


	public function getAccountsTypes()
	{
		$this->load->library('datatables');
		$this->datatables
			->select("{$this->db->dbprefix('gl_accounts_categories')}.categoryID as id, {$this->db->dbprefix('gl_accounts_categories')}.categoryName, {$this->db->dbprefix('gl_accounts_categories')}.categoryNameEn, {$this->db->dbprefix('gl_accounts_categories')}.categoryDescription", false)
			->from('gl_accounts_categories')
			->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');


		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

		echo $this->datatables->generate();
	}

	public function getLedgersList()
	{

		$this->load->library('datatables');
		$this->datatables
			->select("{$this->db->dbprefix('gl_accounts')}.id as id, {$this->db->dbprefix('gl_accounts')}.accountName, {$this->db->dbprefix('gl_accounts')}.accountNameEn, {$this->db->dbprefix('gl_accounts')}.accountCategory", false)
			->from('gl_accounts')
			//->where('warehouse_id', $warehouse_id)
			->where(" ({$this->db->dbprefix('gl_accounts')}.accountDebit > 0 OR {$this->db->dbprefix('gl_accounts')}.accountCredit > 0) ")

			//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');
			->add_column('Actions', "<div class='text-center'><a class=\"tip\" title='" . lang('Ledgers') . "' href='" . admin_url('accounts/ledgers/$1') . "'><span class='label label-primary'>" . lang('Ledgers') . '</span></a></div>', 'id');



		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

		echo $this->datatables->generate();
	}

	public function getReportsList()
	{

		$this->load->library('datatables');
		$this->datatables
			->select("{$this->db->dbprefix('gl_accounts')}.id as id, {$this->db->dbprefix('gl_accounts')}.accountName, {$this->db->dbprefix('gl_accounts')}.accountNameEn, {$this->db->dbprefix('gl_accounts')}.accountCategory", false)
			->from('gl_accounts')
			//->where('warehouse_id', $warehouse_id)
			->where(" ({$this->db->dbprefix('gl_accounts')}.accountDebit > 0 OR {$this->db->dbprefix('gl_accounts')}.accountCredit > 0) ")

			//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');
			->add_column('Actions', "<div style='padding:3px;' class='text-center'><a class=\"tip\" title='" . lang('Account_statement') . "' href='" . admin_url('accounts/accountstatement/$1') . "'><span class='label label-primary'>" . lang('Account_statement') . "</span></a></div><div  style='padding:3px;' class='text-center'><a class=\"tip\" title='" . lang('Account_balance') . "' href='" . admin_url('accounts/accountbalance/$1') . "'><span class='label label-primary'>" . lang('Account_balance') . '</span></a></div>', 'id');



		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

		echo $this->datatables->generate();
	}

	public function getPaymentTypes()
	{
		$this->load->library('datatables');
		if ($this->Settings->user_language == 'arabic') {

			$this->datatables
				->select("{$this->db->dbprefix('gl_payment_methods')}.id as id, {$this->db->dbprefix('gl_payment_methods')}.payment_name, {$this->db->dbprefix('gl_payment_methods')}.payment_ename, {$this->db->dbprefix('gl_accounts')}.accountName, CONCAT({$this->db->dbprefix('gl_payment_methods')}.payment_type,'__',(CASE WHEN {$this->db->dbprefix('gl_payment_methods')}.payment_cc_id  !=0 THEN ({$this->db->dbprefix('payment_type')}.code) ELSE '' END)) as payment_type", false)

				->from('gl_payment_methods')
				->join('gl_accounts', 'gl_accounts.id=gl_payment_methods.payment_account', 'left')
				->join('payment_type', 'payment_type.id=gl_payment_methods.payment_cc_id', 'left')

				//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_payment_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_payment_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');
				->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_payment_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_payment_type') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_payment_type') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('accounts/delete_payment_type/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
		} else {
			$this->datatables
				->select("{$this->db->dbprefix('gl_payment_methods')}.id as id, {$this->db->dbprefix('gl_payment_methods')}.payment_name, {$this->db->dbprefix('gl_payment_methods')}.payment_ename, {$this->db->dbprefix('gl_accounts')}.accountNameEn, CONCAT({$this->db->dbprefix('gl_payment_methods')}.payment_type,'__',(CASE WHEN {$this->db->dbprefix('gl_payment_methods')}.payment_cc_id  !=0 THEN ({$this->db->dbprefix('payment_type')}.code) ELSE '' END)) as payment_type", false)

				->from('gl_payment_methods')
				->join('gl_accounts', 'gl_accounts.id=gl_payment_methods.payment_account', 'left')
				->join('payment_type', 'payment_type.id=gl_payment_methods.payment_cc_id', 'left')

				//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_payment_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_payment_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');
				->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_payment_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_payment_type') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('Delete_payment_type') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('accounts/delete_payment_type/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
		}


		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

		echo $this->datatables->generate();
	}

	public function getAccountingPeriod()
	{
		$this->load->library('datatables');

		$this->datatables
			->select("{$this->db->dbprefix('gl_accounting_period')}.id as id, {$this->db->dbprefix('gl_accounting_period')}.period_code, {$this->db->dbprefix('gl_accounting_period')}.period_year, DATE_FORMAT({$this->db->dbprefix('gl_accounting_period')}.period_from_date, '%d/%m/%Y') as period_from_date, DATE_FORMAT({$this->db->dbprefix('gl_accounting_period')}.period_to_date, '%d/%m/%Y') as period_to_date,{$this->db->dbprefix('gl_accounting_period')}.period_status", false)

			->from('gl_accounting_period')
			->where('gl_accounting_period.period_year_status', 1);

		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_payment_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_payment_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');



		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');

		echo $this->datatables->generate();
	}

	public function getEntries()
	{

		/*

		$rows = $this->accounts_model->getEntries();

            foreach ($rows as $row) {
                $pr[]     = ['id' => $row->entryID, 'entryID' => $row->entryID, 'entryDate' => $row->entryDate, 'reference_no' => $row->reference_no, 'entryDebitAccount' => $row->entryDebitAccount, 'entryCreditAccount' => $row->entryCreditAccount, 'entryStatus' => $lang('approved')];
            }
			 $this->sma->send_json($pr);
		*/

		//$ai .= $this->getEntryHTML("SUBSTRING_INDEX(gl_entries.entryDebitAccount,'1', 1) ");


		$this->load->library('datatables');
		$this->datatables
			->select("{$this->db->dbprefix('gl_entries')}.entryID as id, {$this->db->dbprefix('gl_entries')}.entryID as entryID, {$this->db->dbprefix('gl_entries')}.entryDate as entryDate, IF({$this->db->dbprefix('gl_entries')}.reference_no is null,'" . lang('Manual_Accounting_Entry') . "', '" . lang('Automated_Accounting_Entry') . "') as entryType, {$this->db->dbprefix('gl_entries')}.entryDebitAccount as entryDebitAccount, {$this->db->dbprefix('gl_entries')}.entryDebitBalance as entryDebitBalance, {$this->db->dbprefix('gl_entries')}.entryCreditAccount as entryCreditAccount, {$this->db->dbprefix('gl_entries')}.entryCreditBalance as entryCreditBalance, IF({$this->db->dbprefix('gl_entries')}.entryStatus=1,'" . lang('approved') . "', '" . lang('unapproved') . "') as entryStatus ", false)
			//->select("{$this->db->dbprefix('gl_entries')}.entryID as id, {$this->db->dbprefix('gl_entries')}.entryID as entryID, {$this->db->dbprefix('gl_entries')}.entryDate as entryDate, IF({$this->db->dbprefix('gl_entries')}.reference_no is null,'".lang('Manual_Accounting_Entry')."', '".lang('Automated_Accounting_Entry')."') as entryType, IF({$this->db->dbprefix('gl_entries')}.entryDebitAccount is not null,FAI.entryDebitAccount, '".lang('unapproved')."'), {$this->db->dbprefix('gl_entries')}.entryCreditAccount as entryCreditAccount, {$this->db->dbprefix('gl_entries')}.entryCreditBalance as entryCreditBalance, IF({$this->db->dbprefix('gl_entries')}.entryStatus=1,'".lang('approved')."', '".lang('unapproved')."') as entryStatus ", false)
			->from('gl_entries')
			//->join($ai, 'FAI.entryID=gl_entries.entryID', 'left')
			//->edit_column('entryDebitAccount', "<div class=\"text-center\">" . $this->getEntryHTML('entryDebitAccount'). "</div>", 'entryDebitAccount, entryDebitBalance')
			//->edit_column('entryCreditAccount', "<div class=\"text-center\">" . '$1_$2'. "</div>", 'entryCreditAccount, entryCreditBalance')
			->edit_column('entryDebitAccount', '$1_$2', 'entryDebitAccount, entryDebitBalance')
			->edit_column('entryCreditAccount', '$1_$2', 'entryCreditAccount, entryCreditBalance')
			->unset_column('entryDebitBalance')
			->unset_column('entryCreditBalance');
		//->edit_column('entryType', lang('$1'), 'entryType')

		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/edit_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account_type') . "'><i class=\"fa fa-edit\"></i></a></div>", 'id');


		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
		//$this->sma->print_arrays($this->datatables->generate());
		echo $this->datatables->generate();
	}

	public function getEntries1()
	{




		//->add_column('Actions', "<div class=\"text-center\"><a href='" . admin_url('accounts/add_account_type/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('edit_unit') . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang('delete_unit') . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('system_settings/delete_unit/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", 'id');
		//$this->sma->print_arrays($this->datatables->generate());
		//echo $this->datatables->generate();
		$entryList = $this->accounts_model->getEntries();
		foreach ($entryList as $entry) {
			$entry = (array) $entry;
			$entry['entryDebitAccount'] = json_decode($entry['entryDebitAccount']);
			$entry['entryDebitBalance'] = json_decode($entry['entryDebitBalance']);
			$entry['entryCreditAccount'] = json_decode($entry['entryCreditAccount']);
			$entry['entryCreditBalance'] = json_decode($entry['entryCreditBalance']);







			$dbEnt = "";
			$x = 0;

			foreach ($entry['entryDebitAccount'] as $debitAccount) {

				//$account = $debitAccount[key($debitAccount)];
				//print_r($account);

				//$debitAccount = key($debitAccount);
				//$accountInfo = $this->accounts_model->getAccountByID($$entry['entryDebitAccount'][$x]);

				//if ($accountInfo->id == $account){
				//echo '<strong>'.$this->sma->formatMoney($balance,' ').'</strong><br />';
				$dbEnt = $dbEnt . " " . $this->accounts_model->getAccountByID($entry['entryDebitAccount'][$x])->accountName . '<br /><strong>' . $this->sma->formatMoney($entry['entryDebitBalance'][$x], 0) . '</strong><br />';



				$x++;
				//}


				//$entryNumber = array_search($debitAccount, $entry['entryDebitAccount']);
				//echo $this->accounts_model->getAccountByID($debitAccount)->accountName.'<br /><strong>'.$this->sma->formatMoney($entry['entryDebitBalance'][$entryNumber], 0).'</strong><br />';
			}



			$crEnt = "";
			$y = 0;
			foreach ($entry['entryCreditAccount'] as $creditAccount) {
				$crEnt = $crEnt . " " . $this->accounts_model->getAccountByID($entry['entryCreditAccount'][$y])->accountName . '<br /><strong>' . $this->sma->formatMoney($entry['entryCreditBalance'][$y], 0) . '</strong><br />';



				$y++;
				//	$entryNumber = array_search($creditAccount, $entry['entryCreditAccount']);
				//	echo $this->accounts_model->getAccountByID($creditAccount)->accountName.'<br /><strong>'.$this->sma->formatMoney($entry['entryCreditBalance'][$entryNumber], 0).'</strong><br />';
			}




			$entComment = "";
			if ($entry['entryStatusComment'] == NULL) {
				//$entComment='<div class="text-center"></div>';

			} else {

				//$entComment="<td><div class='text-center'><a href='" . admin_url('welcome/download/'.$entry['entryStatusComment']) ."' class='tip' title='". lang('download')."' ><i class='fa fa-file'></i></a></div>";

			}



			$entryStatus = "";
			if ($entry['entryStatus'] == 1) {

				//$entryStatus="<div class='text-center'><span class='row_status label label-success'>'". lang('approved')."'</span></div>";

			} else {

				//$entryStatus="<div class='text-center'><span class='row_status label label-warning'>'". lang('unapproved')."'</span></div>";

			}




			//$enActions="<div class='text-center'><a href='". admin_url('accounts/duplicate_entry/'.$entry['entryID']) ."' data-toggle='modal' data-target='#myModal' class='tip' title='". lang('Duplicate_entry') ."'><i style='padding:2px;' class='fa fa-clone'></i></a><a href='". admin_url('accounts/reverse_entry/'.$entry['entryID']) ."' data-toggle='modal' data-target='#myModal' class='tip' title='". lang('Reverse_entry') ."'><i style='padding:2px;' class='fa fa-exchange'></i></a><a href='". admin_url('accounts/edit_entry/'.$entry['entryID']) ."' data-toggle='modal' data-target='#myModal' class='tip' title='". lang('Edit_entry') ."'><i style='padding:2px;' class='fa fa-edit'></i></a></div>";

			$product = [
				$entry['entryID'],
				$entry['entryID'],
				$this->sma->hrsd($entry['entryDate']),
				trim($entry['entryType']) != "Adjusting" ? lang($entry['entryType']) : lang($entry['entryDescription']),
				$dbEnt,
				$crEnt,
				$entComment,
				$entryStatus,
				$enActions,

			];
			$products[] = ($product);
		}
		echo json_encode($products);
	}


	public function getEntryHTML($entryList = null)
	{
		//return $entryList;
		//$this->sma->print_arrays($entryList);
		//$entryList      = $this->input->post('entryList');

		//$entryList = $entryList;
		//return json_decode($entryList);
		/*$entryList=str_replace('"]',"",$entryList);
		$entryList=str_replace('["',"",$entryList);
		$entryList=str_replace('"',"",$entryList);


		$entryList=explode("_",$entryList);
		$entryAccount=$entryList[0];
		$entryAccount=explode(",",$entryAccount);

		$entryBalance=$entryList[1];
		$entryBalance=explode(",",$entryBalance);
		*/
		//$this->sma->print_arrays($entryAccount);
		//$this->sma->print_arrays($entryBalance);

		//return $entryBalance;
		//return json_decode($entryList);

		//$entryAccount=str_replace("]","",$entryAccount);
		//return (($entryAccount));
		//settype($entryList, "object");
		//$entryAccount =  $entryList;
		//$entryList      = strval (($entryList));
		//$entryList=str_replace('1','8',$entryList."");
		//$entryList=str_replace('["',"",$entryList);
		//$entryList=str_replace('"',"",$entryList);
		//$entryList=substr($entryList,0,2);
		//$entryList=substr($entryList,1);

		//$entryList=explode(']',$entryList);

		//$entryList=chop($entryList,'["');
		//$entryList = json_decode($entryList, true);

		//$this->sma->send_json($this->getEntryJSON(($entryList)));
		//$entryList=explode("",$entryList);

		$r = ($entryList);

		return $r;

		$r = str_replace('[', '', $r);
		return $r;
		/*$r=str_split($r);

			    $entryDebitAccount="";

		                  foreach ($r as $rr){

								//$entryNumber = array_search($debitAccount, $entry['entryDebitAccount']);
								$entryDebitAccount=$entryDebitAccount." ". $rr;
								//echo $this->sma->formatMoney($entry['entryDebitBalance'][$dt_index]).'</td><td>';


							}

		   return  $entryDebitAccount;




		  $entryList = explode(",", $entryList);
		  return   ($entryList[0]);

		  $entry = (array) $entry;
		  $entry['entryDebitAccount'] = ($entryList);
		  return   $entry['entryDebitAccount'];

		  $entryDebitAccount="";

		                  foreach ($entry['entryDebitAccount'] as $debitAccount){

								$entryNumber = array_search($debitAccount, $entry['entryDebitAccount']);
								$entryDebitAccount=$entryDebitAccount." ". $this->accounts_model->getAccountByID($debitAccount)->accountName;
								//echo $this->sma->formatMoney($entry['entryDebitBalance'][$dt_index]).'</td><td>';


							}

		   return  $entryDebitAccount;



			$entryList=explode("_",$entryList);
			$entryAccount= (Array)$entryList[0];

		    $entryBalance= $entryList[1];
			*/
		//return $entryList[0];

		/*$entryAccount=str_replace('"]',"",$entryAccount);
		$entryAccount=str_replace('["',"",$entryAccount);
		$entryAccount=str_replace('"',"",$entryAccount);
		*/
		//return json_decode((Array)$entryAccount);
		//$entryAccount=explode(",",$entryAccount);
		/*
		foreach($entryList[0] as $key => $Account){
				return  print_r($Account);


		}

		$content="";
		$i = isset($entryAccount) ? sizeof($entryAccount) : 0;
		return $balance1;
            for ($r = 0; $r < $i; $r++) {

            $content = $content." ".$this->accounts_model->getAccountByID($entryAccount[$r])->accountName." <br>".$this->sma->formatMoney($entryBalance[$r])." <br>";

		}
		//$this->sma->print_arrays($content);

		//return "<div class=\"text-center\">".$content."<div>";
		return $content;
			//$this->sma->send_json(['content' => $content]);
		*/
	}

	public function getEntryJSON($entryList = null)
	{
		//$this->sma->print_arrays($entryList);
		$entryList      = $this->input->post('entryList');

		//$entryList = $entryList;
		//return json_decode($entryList);
		$entryList = str_replace('"]', "", $entryList);
		$entryList = str_replace('["', "", $entryList);
		$entryList = str_replace('"', "", $entryList);


		$entryList = explode("_", $entryList);
		$entryAccount = $entryList[0];
		$entryAccount = explode(",", $entryAccount);

		$entryBalance = $entryList[1];
		$entryBalance = explode(",", $entryBalance);

		//$this->sma->print_arrays($entryAccount);
		//$this->sma->print_arrays($entryBalance);

		//return $entryBalance;
		//return json_decode($entryList);

		//$entryAccount=str_replace("]","",$entryAccount);
		//return (($entryAccount));
		//$entryAccount = (Array) $entryAccount;
		$content = "";
		$i = isset($entryAccount) ? sizeof($entryAccount) : 0;
		for ($r = 0; $r < $i; $r++) {

			$content = $content . " " . $this->accounts_model->getAccountByID($entryAccount[$r])->accountName . " <br>" . $this->sma->formatMoney($entryBalance[$r]) . " <br>";
		}
		//$this->sma->print_arrays($content);

		//return "<div class=\"text-center\">".$content."<div>";
		$this->sma->send_json(['content' => $content]);
	}
	public function edit_account_type($categoryID = null)
	{
		$this->sma->checkPermissions('edit_account_type', true);
		$this->form_validation->set_rules('categoryName', lang('Account_type'), 'trim|required');
		$account_type_details = $this->accounts_model->getAccountTypeByID($categoryID);
		if ($this->form_validation->run() == true) {
			$data = [
				'categoryName'            => $this->input->post('categoryName'),
				'categoryNameEn'            => $this->input->post('categoryNameEn'),
				'categoryDescription'            => $this->input->post('categoryDescription'),
			];
		} elseif ($this->input->post('edit_account_type')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounts_types');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->updateAccountType($categoryID, $data)) {
			$this->session->set_flashdata('message', lang('Account_type_successfully_updated'));
			admin_redirect('accounts/accounts_types');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$this->data['account_type']       = $account_type_details;
			$this->load->view($this->theme . 'accounts/edit_account_type', $this->data);
		}
	}



	public function accountstatement($id = null, $pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		$this->sma->checkPermissions('accountstatement', true);

		if ($pdf || $xls) {


			$search_description    = $this->input->get('search_description') ? $this->input->get('search_description') : null;
			$show_style    = $this->input->get('show_style') ? $this->input->get('show_style') : 1;
			$show_Opening_balance    = $this->input->get('show_Opening_balance'); // ? $this->input->get('show_Opening_balance') : 1;
			$Carry_over_balance    = $this->input->get('Carry_over_balance'); // ? $this->input->get('show_Opening_balance') : 1;

			$account    = $this->input->get('account') ? $this->input->get('account') : null;
			$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
			$currency     = $this->input->get('currency') ? $this->input->get('currency') : null;

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			//$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));

			/*if($id){
        if($show_style==1)
            $this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($id)[0]->accountID);
        else
            $this->data['accountList'] = $this->accounts_model->getAccounts($id);
        }else{
        if($show_style==1)
            $this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)[0]->accountID);
        else
            $this->data['accountList'] = $this->accounts_model->getAccounts($account);

        }*/
			// $id=1;
			if ($id) {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
				//else
				$accountList = $this->accounts_model->getAccounts($id);
			} else {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
				//else
				if($show_style==2 || $show_style==3){
					$accountList = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)['0']->accountID);
				}else{
				$accountList = $this->accounts_model->getAccounts($account);
					}
			}
			asort($accountList);



			$accounts = $this->accounts_model->getAccounts();

			//$this->sma->print_arrays($this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID));

			//



			if ($start_date && $end_date) {
				//$start_date = $this->sma->fld($start_date);
				//$end_date   = $this->sma->fld($end_date);
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				//$date     = explode(' ', $start_date);
				//$start_date = $date[0];
				//$date     = explode(' ', $end_date);
				//$end_date = $date[0];
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				//	$this->sma->print_arrays($prev_start_date , $end_date ,$show_Opening_balance,date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59');
				$entryList  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance,$grpId, $coId, $brId,$currency);
				//$this->data['all_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date , $end_date ,$show_Opening_balance);
				if ($Carry_over_balance)
					$prev_entryList  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance,$grpId, $coId, $brId,$currency);
				//$this->sma->print_arrays($this->data['entryList'],$this->data['prev_entryList']);
				//$this->sma->print_arrays($start_date ,$prev_start_date , date('Y-m-d', strtotime('-1 days', strtotime($start_date))));

				//



			} else {
				$entryList  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance,NULL,NULL,$grpId, $coId, $brId,$currency);
			}
			$filter_id  = $id;

			//$this->sma->print_arrays($filter_id);


			//$this->sma->print_arrays($accountList1);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Account_statement'));

			$this->excel->getActiveSheet()->mergeCells('A1:H1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Account_statement'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 20,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);





			$this->excel->getActiveSheet()->mergeCells('A2:H2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

			$this->excel->getActiveSheet()->mergeCells('A3:H3');
			$this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Account_statement') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);




			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)
			);

			$styleArrayGreen = array(

				'font' => array(
					'color' => array('rgb' => '28a745')
				)
			);

			$styleArrayRed = array(

				'font' => array(
					// 'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dc3545')
				)
			);

			/*$styleArray2 = array(
              'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
                  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray1);

			$accountSide = "";

			$accountInfo = $this->accounts_model->getAccountByID($_GET['account']);
			$accountSide = $accountInfo->accountSide;

			if ($accountSide == "") {
				if ($_GET['account'] == 1)
					$accountSide = "Debit";
				if ($_GET['account'] == 2)
					$accountSide = "Credit";
				if ($_GET['account'] == 3)
					$accountSide = "Debit";
				if ($_GET['account'] == 4)
					$accountSide = "Credit";
			}
			//if(!isset($_GET['account']))
			//unset($accountList);
			//$accountList = $this->accounts_model->getAccounts($_GET['account']);

			$row = 4;

			foreach ($accountList as $account1) {




				$account1 = (array) $account1;
				if($this->input->get('show_style')==2 || $this->input->get('show_style')==3){
					if($_GET['account']){
						if($_GET['account']!=$account1['id']){
					if ($this->accounts_model->IsAccountIsChild($_GET['account'],$account1['parent_id']) == '0'){
						continue;
					}
				}
				}
				}
				$accountSide = "";

				if ($accountSide == "") {
					$accountInfo = $this->accounts_model->getAccountByID($account1['id']);
					$accountSide = $accountInfo->accountSide;
				}
				//if($accountSide==""){
				if ($account1['id'] == 1)
					$accountSide = "Debit";
				if ($account1['id'] == 2)
					$accountSide = "Credit";
				if ($account1['id'] == 3)
					$accountSide = "Debit";
				if ($account1['id'] == 4)
					$accountSide = "Credit";
				//}

				//echo $account1['id']." ".$accountSide."<br/>";


				//$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);
				if($show_style==2 || $show_style==3){

					$accountList1=$this->accounts_model->getAccountByAccountIDV2($account1['accountID']);
				}else{
				$accountList1 = $this->accounts_model->getAccountChildsWithLevels($account1['accountID'], $levels);
				}

				$root_parent_key = $account1['id'];


				//$accountList1 = $this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID);//


				//$account1=="";
				//if (!empty($accountEntries))

				{
					if ($this->Settings->user_language == 'arabic' || $account1['accountNameEn'] == '') {
						$account1 = $account1['accountName'];
					} else {
						$account1 = $account1['accountNameEn'];
					}


					$this->excel->getActiveSheet()->mergeCells('A' . $row . ':H' . $row);
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $account1);
					//$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$row++;
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Sr#'));
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("Entry_number"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("date"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("reference_no"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Detailed_account"));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("Debit") . ' (' . ((isset($_GET['currency']) && $_GET['currency']!='') ? $_GET['currency'] : $this->Settings->symbol) . ')');
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('G' . $row, lang("Credit") . ' (' . ((isset($_GET['currency']) && $_GET['currency']!='') ? $_GET['currency'] : $this->Settings->symbol) . ')');
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('H' . $row, lang("balance") . ' (' . ((isset($_GET['currency']) && $_GET['currency']!='') ? $_GET['currency'] : $this->Settings->symbol) . ')');
					$this->excel->getActiveSheet()->getStyle('H' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->applyFromArray($styleArray1);

					$row++;
				}

				$balanceDebit = 0;
				$balanceCredit = 0;
				$fbalance = 0;
				$sr = 0;

				$prev_fbalance = 0;



				foreach ($accountList1 as $account) {
					//$prev_fbalance=0;

					$account = (array) $account;
					if($this->input->get('show_style')==1 || $this->input->get('show_style')==4){
						if($root_parent_key!=$account['id']){
					if ($this->accounts_model->IsAccountIsChild($root_parent_key,$account['parent_id']) == '0'){
						continue;
					}
				}
				}
					//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
					{

						$prev_accountEntries = array();

						$id      = $account['id'];
						$accountBalance = $account['accountDebit'] - $account['accountCredit'];


						foreach ($prev_entryList as $prev_entry) {
							$prev_entry = (array) $prev_entry;
							//
							if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))) {
								array_push($prev_accountEntries, $prev_entry);
							}
						}





						if (empty($_GET)) {

							//
							$check_period = $this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
							if ($check_period || $check_period->period_status == 1) {
								$_GET['start_date'] = $this->sma->hrsd($check_period->period_from_date);
								$_GET['end_date'] = $this->sma->hrsd($check_period->period_to_date);
							}
						}


						//if (!empty($prev_accountEntries))
						{

							//print_r($prev_accountEntries);




							foreach ($prev_accountEntries as $entry) {
								$entry = (array) $entry;

								$entryDebitAccounts = array();
								$entryDebitBalances = 0;

								$entryCreditAccounts = array();
								$entryCreditBalances = 0;

								foreach (json_decode($entry['entryDebitAccount']) as $debitAccount) {
									array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]/((isset($_GET['currency']) && $_GET['currency']!='') ? $entry['currency_rate'] : 1)));
									$entryDebitBalances += 1;
								}

								foreach (json_decode($entry['entryCreditAccount']) as $creditAccount) {
									array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]/((isset($_GET['currency']) && $_GET['currency']!='') ? $entry['currency_rate'] : 1)));
									$entryCreditBalances += 1;
								}



								//print_r($entryDebitAccounts);
								foreach ($entryDebitAccounts as $account) {
									$balance = $account[key($account)];
									$account = key($account);
									$accountInfo = $this->accounts_model->getAccountByID($account);
									//print_r($accountInfo->id);


									//$accountInfo = $this->entry_model->getAccount($account);

									$x = 0;
									if ($accountInfo->id == $id) {
										//$prev_fbalance=$prev_fbalance+$balance;
										if ($accountSide == "Debit")
											$prev_fbalance = $prev_fbalance + $balance;
										else
											$prev_fbalance = $prev_fbalance - $balance;




										//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



										$x++;
									}
								}
								foreach ($entryCreditAccounts as $account) {
									$balance = $account[key($account)];
									$account = key($account);
									$accountInfo = $this->accounts_model->getAccountByID($account);
									//echo $balance;



									$y = 0;
									if ($accountInfo->id == $id) {
										//$prev_fbalance=$prev_fbalance-$balance;
										if ($accountSide == "Credit")
											$prev_fbalance = $prev_fbalance + $balance;
										else
											$prev_fbalance = $prev_fbalance - $balance;




										$y++;
									}
								}
							}
						}
					}
				}
				//$this->sma->print_arrays($prev_fbalance);



				if ($prev_fbalance) {

					$sr++;

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);



					$this->excel->getActiveSheet()->SetCellValue('B' . $row, '');
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);


					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('Before') . ' ' . (isset($_GET['start_date']) ? $_GET['start_date'] : ''));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, ' ');
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);


					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang('Carry-over_balance'));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(11);



					if ($accountSide == "Debit") {
						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal(abs($prev_fbalance)));
						$balanceDebit = $balanceDebit + $prev_fbalance;
						$fbalance = $fbalance + $prev_fbalance;
					}
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(11);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

					if ($accountSide == "Credit") {

						$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($prev_fbalance)));
						$balanceCredit = $balanceCredit + $prev_fbalance;
						$fbalance = $fbalance + $prev_fbalance;
					}
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);

					/*
                    $this->excel->getActiveSheet()->SetCellValue('G'.$row,$this->sma->formatDecimal($fbalance));
                    $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setSize(11);
                    $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
                    */
					//$this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($styleArray1);


					if ($accountSide == "Debit") {
						if ($fbalance > 0) {
							$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayGreen);
						} else {
							$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayRed);
						}
					}

					if ($accountSide == "Credit") {
						if ($fbalance > 0) {
							$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayGreen);
						} else {
							$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayRed);
						}
					}


					$row++;
				}








				foreach ($accountList1 as $account) {

					$account = (array) $account;
					if($this->input->get('show_style')==1 || $this->input->get('show_style')==4){
						if($root_parent_key!=$account['id']){
					if ($this->accounts_model->IsAccountIsChild($root_parent_key,$account['parent_id']) == '0'){
						continue;
					}
				}
				}
					$accountEntries = array();


					//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
					{


						$id      = $account['id'];
						$accountBalance = $account['accountDebit'] - $account['accountCredit'];


						foreach ($entryList as $entry) {
							$entry = (array) $entry;
							if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))) {
								array_push($accountEntries, $entry);
							}
						}
						//
						//print_r($accountEntries);

						if (!empty($accountEntries)) {


							if ($this->Settings->user_language == 'arabic' || $account['accountNameEn'] == '') {
								$accountName = $account['accountName'];
							} else {
								$accountName = $account['accountNameEn'];
							}



							foreach ($accountEntries as $entry) {
								$entry = (array) $entry;

								$currencyRate = ((isset($_GET['currency']) && $_GET['currency'] != '') ? ($entry['currency_rate'] ?: 1) : 1);

								$entryDebitAccounts = json_decode($entry['entryDebitAccount'], true) ?: array();
								$entryDebitBalances = json_decode($entry['entryDebitBalance'], true) ?: array();
								$entryDebitDescriptions = json_decode($entry['entryDebitDescription'], true) ?: array();

								$entryCreditAccounts = json_decode($entry['entryCreditAccount'], true) ?: array();
								$entryCreditBalances = json_decode($entry['entryCreditBalance'], true) ?: array();
								$entryCreditDescriptions = json_decode($entry['entryCreditDescription'], true) ?: array();

								$entryMovements = array();

								foreach ($entryDebitAccounts as $k => $acc) {
									if ($acc == $id) {
										$bal = isset($entryDebitBalances[$k]) ? ($entryDebitBalances[$k] / $currencyRate) : 0;
										$desc = isset($entryDebitDescriptions[$k]) ? $entryDebitDescriptions[$k] : '';
										$entryMovements[] = array(
											'side' => 'debit',
											'amount' => $bal,
											'description' => $desc,
										);
									}
								}

								foreach ($entryCreditAccounts as $k => $acc) {
									if ($acc == $id) {
										$bal = isset($entryCreditBalances[$k]) ? ($entryCreditBalances[$k] / $currencyRate) : 0;
										$desc = isset($entryCreditDescriptions[$k]) ? $entryCreditDescriptions[$k] : '';
										$entryMovements[] = array(
											'side' => 'credit',
											'amount' => $bal,
											'description' => $desc,
										);
									}
								}

								foreach ($entryMovements as $movement) {
									if ($entry['entryType'] != "Opening_balance") {
										$entryDescriptionText = $movement['description'];
										if ($entryDescriptionText == "") {
											$entryDescriptionText = $entry['entryDescription'];
										}

										$entryDescription = str_replace("<p>", "", ($entryDescriptionText) . "");
										$entryDescription = str_replace("</p>", "", ($entryDescription) . "");
										$iof = strpos(trim($entryDescription), "#");
										if ($iof > -1) {
											$entryDescription = lang(substr($entryDescription, 0, $iof)) . " " . substr($entryDescription, $iof + 1);
										}
										$finalDescription = lang($entryDescription);
									} else {
										$finalDescription = lang($entry["entryType"]);
									}

									if (trim($search_description) != '') {
										if (!is_numeric(strpos($finalDescription, $search_description))) {
											continue;
										}
									}

									$sr++;

									$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
									if (empty($entry['entry_no'])) {
										$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entryID']);
									} else {
										$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entry_no']);
									}

									$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrsd($entry['entryDate']));
									$this->excel->getActiveSheet()->SetCellValue('D' . $row, $entry['reference_no']);
									$this->excel->getActiveSheet()->SetCellValue('E' . $row, $finalDescription);
									$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);

									if ($movement['side'] == 'debit') {
										$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($movement['amount']));
										$balanceDebit = $balanceDebit + $movement['amount'];
										if ($accountSide == "Debit") {
											$fbalance = $fbalance + $movement['amount'];
										} else {
											$fbalance = $fbalance - $movement['amount'];
										}
									} elseif ($movement['side'] == 'credit') {
										$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($movement['amount']));
										$balanceCredit = $balanceCredit + $movement['amount'];
										if ($accountSide == "Credit") {
											$fbalance = $fbalance + $movement['amount'];
										} else {
											$fbalance = $fbalance - $movement['amount'];
										}
									}

									if ($accountSide == "Debit") {
										if ($fbalance > 0) {
											$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayGreen);
										} else {
											$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayRed);
										}
									}

									if ($accountSide == "Credit") {
										if ($fbalance > 0) {
											$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayGreen);
										} else {
											$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(11);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
											$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayRed);
										}
									}

									$this->excel->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

									$row++;
								}
							}
						}
					}
				}




				$total = 0;
				$diffDebit = 0;
				$diffCredit = 0;
				if ($balanceDebit > $balanceCredit) {
					$total = $balanceDebit;
					$diffDebit = ($balanceDebit - $balanceCredit);
				} else {
					$total = $balanceCredit;
					if ($balanceCredit > $balanceDebit)
						$diffCredit = ($balanceCredit - $balanceDebit);
				}


				if ($diffDebit != 0 || $diffCredit != 0) {
					if ($diffDebit != 0)
						$diffDebit = $this->sma->formatDecimal($diffDebit);
					else
						$diffDebit = '';

					if ($diffCredit != 0)
						$diffCredit = $this->sma->formatDecimal($diffCredit);
					else
						$diffCredit = '';
				}

				$this->excel->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("total"));
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);


				$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($balanceDebit));
				$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('F' . $row)->getBorders()
					->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);


				$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($balanceCredit));
				$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('G' . $row)->getBorders()
					->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);



				if ($accountSide == "Debit") {
					if ($fbalance > 0) {
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(13);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayGreen);
					} else {

						$this->excel->getActiveSheet()->getStyle('H' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(13);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayRed);
					}
				}
				//$this->sma->print_arrays($fbalance);
				if ($accountSide == "Credit") {
					if ($fbalance > 0) {
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(13);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayGreen);
					} else {
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getBorders()
							->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
						$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($fbalance));
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setSize(13);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
						$this->excel->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArrayRed);
					}
				}





				$row++;
			}



















			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);



			$filename = lang('Account_statement');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {


			$show_style    = $this->input->post('show_style') ? $this->input->post('show_style') : 1;
			$show_Opening_balance    = $this->input->post('show_Opening_balance'); // ? $this->input->post('show_Opening_balance') : 1;
			$Carry_over_balance    = $this->input->post('Carry_over_balance'); // ? $this->input->post('show_Opening_balance') : 1;

			$account    = $this->input->post('account') ? $this->input->post('account') : null;
			$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : null;
			$currency     = $this->input->post('currency') ? $this->input->post('currency') : null;

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			//$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));

			/*if($id){
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($id)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		}else{
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($account);

		}*/

			if ($id) {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
				//else
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);
			} else {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
				//else
				//$this->data['accountList'] = $this->accounts_model->getAccounts($account);

				if($show_style==2 || $show_style==3){
				$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)['0']->accountID);
				}else{
					$this->data['accountList'] = $this->accounts_model->getAccounts($account);
				}
				//$this->sma->print_arrays($this->data['accountList']);
			}


			$this->data['accounts'] = $this->accounts_model->getAccounts();
			//$this->sma->print_arrays($this->accounts_model->getAccounts($id));

			//$this->sma->print_arrays($this->accounts_model->isMembersTree($this->accounts_model->getAccountByID($account)->id,$this->accounts_model->getAccountByID($account)->id,0));

			//



			if ($start_date && $end_date) {
				//$start_date = $this->sma->fld($start_date);
				//$end_date   = $this->sma->fld($end_date);
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				//$date     = explode(' ', $start_date);
				//$start_date = $date[0];
				//$date     = explode(' ', $end_date);
				//$end_date = $date[0];
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				//	$this->sma->print_arrays($prev_start_date , $end_date ,$show_Opening_balance,date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59');
				$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance,$grpId, $coId, $brId,$currency);
				//$this->data['all_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date , $end_date ,$show_Opening_balance);
				if ($Carry_over_balance)
					$this->data['prev_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance,$grpId, $coId, $brId,$currency);
				//$this->sma->print_arrays($this->data['entryList'],$this->data['prev_entryList']);
				//$this->sma->print_arrays($start_date ,$prev_start_date , date('Y-m-d', strtotime('-1 days', strtotime($start_date))));

				//



			} else {
				$this->data['entryList']  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance,NULL,NULL,$grpId, $coId, $brId,$currency);
			}
			//$this->sma->print_arrays($_POST,$this->data['entryList']);

			$this->data['filter_id']  = $id;


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{
             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['currencies'] = $brId ? $this->site->getCurrenciesBybrId($brId) : $this->site->getAllCurrencies();
        	}else{
            	$this->data['currencies'] = $this->site->getAllCurrencies();
        	}
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Account_statement'), 'page' => lang('Account_statement')], ['link' => '#', 'page' => lang('Account_statement')]];
			$meta                = ['page_title' => lang('Account_statement'), 'bc' => $bc];
			$this->page_construct('accounts/accountstatement', $meta, $this->data);
		}
	}


	public function accountbalance($id = null)
	{


		$show_style    = $this->input->post('show_style') ? $this->input->post('show_style') : 0;
		$account    = $this->input->post('account') ? $this->input->post('account') : null;
		$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : null;
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : null;

		/*if($id){
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($id)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		}else{
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($account);

		}*/

		if ($id) {
			if ($show_style == 1)
				$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
			else
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		} else {
			if ($show_style == 1)
				$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
			else
				$this->data['accountList'] = $this->accounts_model->getAccounts($account);
		}



		$this->data['accounts'] = $this->accounts_model->getAccounts();



		//$this->sma->print_arrays($this->data['accountList']);
		if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date);
			$end_date   = $this->sma->fld($end_date);
			$date     = explode(' ', $start_date);
			$start_date = $date[0];
			$date     = explode(' ', $end_date);
			$end_date = $date[0];

			$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date);
		} else {
			$this->data['entryList']  = $this->accounts_model->getEntriesApproved();
		}
		$this->data['filter_id']  = $id;


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Account_balances'), 'page' => lang('Account_balances')], ['link' => '#', 'page' => lang('Account_balances')]];
		$meta                = ['page_title' => lang('Account_balances'), 'bc' => $bc];
		$this->page_construct('accounts/accountbalance', $meta, $this->data);
	}

	public function accountstatementprint($id = null)
	{


		$search_description    = $this->input->get('search_description') ? $this->input->get('search_description') : null;
		$show_style    = $this->input->get('show_style') ? $this->input->get('show_style') : 1;
		$show_Opening_balance    = $this->input->get('show_Opening_balance'); // ? $this->input->post('show_Opening_balance') : 1;
		$Carry_over_balance    = $this->input->get('Carry_over_balance'); // ? $this->input->get('show_Opening_balance') : 1;
		$account    = $this->input->get('account') ? $this->input->get('account') : null;
		$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
		$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
		$currency     = $this->input->get('currency') ? $this->input->get('currency') : null;

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			//$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));
		if ($id) {
			//if($show_style==1)
			//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
			//else
			$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		} else {
			//if($show_style==1)
			//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
			//else
			if($show_style==2 || $show_style==3){
				$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)['0']->accountID);
				}else{
			$this->data['accountList'] = $this->accounts_model->getAccounts($account);
				}
		}



		//$this->sma->print_arrays($account);
		/*if ($start_date && $end_date) {
			//$start_date = $this->sma->fld($start_date);
			//$end_date   = $this->sma->fld($end_date);
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';

			//$date     = explode(' ', $start_date);
			//$start_date = $date[0];
			//$date     = explode(' ', $end_date);
			//$end_date = $date[0];

			$this->data['start_date'] = $start_date;
			$this->data['end_date'] = $end_date;

			$ts1 = strtotime($start_date);
			$year1 = date('Y', $ts1);
			$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
			$prev_start_date = $prev_start_date . ' 00:00:00';


			if ($Carry_over_balance)
				$this->data['prev_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance);

			$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance);
		} else {
			$this->data['entryList']  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance);
		}*/

		if ($start_date && $end_date) {
			//$start_date = $this->sma->fld($start_date);
			//$end_date   = $this->sma->fld($end_date);
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
			//$date     = explode(' ', $start_date);
			//$start_date = $date[0];
			//$date     = explode(' ', $end_date);
			//$end_date = $date[0];
			$ts1 = strtotime($start_date);
			$year1 = date('Y', $ts1);
			$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
			$prev_start_date = $prev_start_date . ' 00:00:00';


			//	$this->sma->print_arrays($prev_start_date , $end_date ,$show_Opening_balance,date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59');
			$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance,$grpId, $coId, $brId,$currency);
			//$this->data['all_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date , $end_date ,$show_Opening_balance);
			if ($Carry_over_balance)
				$this->data['prev_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance,$grpId, $coId, $brId,$currency);
			//$this->sma->print_arrays($this->data['entryList'],$this->data['prev_entryList']);
			//$this->sma->print_arrays($start_date ,$prev_start_date , date('Y-m-d', strtotime('-1 days', strtotime($start_date))));

			//



		} else {
			$this->data['entryList']  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance,NULL,NULL,$grpId, $coId, $brId,$currency);
		}
		$this->data['filter_id']  = $id;


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Account_statement'), 'page' => lang('Account_statement')], ['link' => '#', 'page' => lang('Account_statement')]];
		$meta                = ['page_title' => lang('Account_statement'), 'bc' => $bc];
		$this->page_construct('accounts/accountstatementprint', $meta, $this->data);
	}



	public function accountbalanceprint($id = null)
	{


		$show_style    = $this->input->get('show_style') ? $this->input->get('show_style') : 0;
		$account    = $this->input->get('account') ? $this->input->get('account') : null;
		$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
		$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
		if ($id) {
			if ($show_style == 1)
				$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
			else
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		} else {
			if ($show_style == 1)
				$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
			else
				$this->data['accountList'] = $this->accounts_model->getAccounts($account);
		}



		//$this->sma->print_arrays($account);
		if ($start_date && $end_date) {
			$start_date = $this->sma->fld($start_date);
			$end_date   = $this->sma->fld($end_date);

			$date     = explode(' ', $start_date);
			$start_date = $date[0];
			$date     = explode(' ', $end_date);
			$end_date = $date[0];

			$this->data['start_date'] = $start_date;
			$this->data['end_date'] = $end_date;

			$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date);
		} else {
			$this->data['entryList']  = $this->accounts_model->getEntriesApproved();
		}
		$this->data['filter_id']  = $id;


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Account_balances'), 'page' => lang('Account_balances')], ['link' => '#', 'page' => lang('Account_balances')]];
		$meta                = ['page_title' => lang('Account_balances'), 'bc' => $bc];
		$this->page_construct('accounts/accountbalanceprint', $meta, $this->data);
	}


	public function add_account_type()
	{


		$this->form_validation->set_rules('categoryName', lang('Account_type'), 'trim|required');



		if ($this->form_validation->run() == true) {
			$data = [
				'categoryName'            => $this->input->post('categoryName'),
				'categoryNameEn'            => $this->input->post('categoryNameEn'),
				'categoryDescription'            => $this->input->post('categoryDescription'),
			];
		} elseif ($this->input->post('add_account_type')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounts_types');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->addAccountType($data)) {

			$this->session->set_flashdata('message', lang('Account_type_successfully_added'));
			admin_redirect('accounts/accounts_types');

			//   admin_redirect('system_settings/units');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();

			$this->load->view($this->theme . 'accounts/add_account_type', $this->data);
		}
	}


	public function add_payment_type()
	{
		$this->sma->checkPermissions('add_payment_type', true);

		$this->form_validation->set_rules('payment_name', lang('Payment_type_name'), 'trim|required');
		$this->form_validation->set_rules('commisson_perc', lang('Commission_%'), 'trim|numeric');
		//$this->form_validation->set_rules('payment_account', lang('Account'), 'trim|required');



		if ($this->form_validation->run() == true) {

			$data = [
				'payment_name'            => $this->input->post('payment_name'),
				'payment_ename'            => $this->input->post('payment_ename'),
				'commisson_perc'            => $this->input->post('commisson_perc'),
				'payment_account'            => $this->input->post('payment_type') == 'cash' ? $this->input->post('payment_account') : $this->input->post('payment_account1'),
				'commisson_account'            => $this->input->post('commisson_account'),
				'description'            => $this->input->post('description'),
				'payment_type'            => $this->input->post('payment_type'),
				'tax_rate'            => $this->input->post('tax_rate'),
				'payment_cc_id'            => $this->input->post('payment_type') == 'CC' ? $this->input->post('payment_cc_id') : 0,

			];
			//$this->sma->print_arrays($data);

		} elseif ($this->input->post('add_payment_type')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/payment_types');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->addPaymentType($data)) {

			$this->session->set_flashdata('message', lang('Payment_type_successfully_added'));
			admin_redirect('accounts/payment_types');

			//   admin_redirect('system_settings/units');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$accounts_settings   = $this->accounts_model->getSettings();
			//$this->sma->print_arrays($accounts_settings);

			$this->data['payment_account_cash'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['payment_account_bank'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_banks_acct);
			$this->data['paymentType'] = $this->site->getpaymentType(1);
			$this->data['commisson_account'] = ($this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct)) ? $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct) : $this->accounts_model->getAccountByID($accounts_settings->accounts_bank_commisson_acct);
			$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['billers']  = $this->site->getAllCompanies('biller');
			$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
			//$this->sma->print_arrays($this->data['paymentType']);

			$this->load->view($this->theme . 'accounts/add_payment_type', $this->data);
		}
	}


	public function add_accounting_period()
	{

		$this->sma->checkPermissions('add_accounting_period', true);
		$this->form_validation->set_rules('period_year', lang('Year'), 'trim|required|numeric');
		$this->form_validation->set_rules('period_from_date', lang('Start_Period'), 'trim|required');
		$this->form_validation->set_rules('period_to_date', lang('End_Period'), 'trim|required');


		$period_year     = $this->input->post('period_year');
		$period_from_date     = $this->input->post('period_from_date');
		$period_to_date       = $this->input->post('period_to_date');



		if ($this->form_validation->run() == true) {


			$period_from_date = $this->sma->fld($period_from_date);
			$period_to_date = $this->sma->fld($period_to_date);

			$ts1 = strtotime($period_from_date);
			$ts2 = strtotime($period_to_date);

			$year1 = date('Y', $ts1);
			$year2 = date('Y', $ts2);

			$month1 = date('m', $ts1);
			$month2 = date('m', $ts2);

			$diff = (($year2 - $year1) * 12) + ($month2 - $month1 + 1);
			$count = 1;

			//$this->sma->print_arrays($diff);
			for ($i = $diff; $i <= 12; $i = $i + $diff) {
				$start = $period_from_date;//date(sprintf($year1 . '-%s-01', floor(($i - 1) / $diff) * $diff + 1));
				//if($diff==1){
				$end = date('Y-m-d', strtotime("+" . $diff . " months", strtotime($start)));
				$end = date('Y-m-d', strtotime("-1 days", strtotime($end)));











				$period = [
					'period_year'            => $period_year,
					'period_year_status'            => '1',
					'period_code'            => str_pad($count, 2, '0', STR_PAD_LEFT),
					'period_from_date'            => $start,
					'period_to_date'            => $end,
					'period_status'            => $count == 1 ? '1' : '2',
					'created_by'            => $this->session->userdata('user_id'),
				];
				$periods[] = $period;
				//$this->data['bond_no'] = $this->accounts_model->getMaxAccountingPeriodByYear();
				$count++;
			}
			//$this->sma->print_arrays($periods);


		} elseif ($this->input->post('add_accounting_period')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounting_period');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->addAccountingPeriod($periods)) {

			$this->session->set_flashdata('message', lang('Accounting_periods_successfully_added'));
			admin_redirect('accounts/accounting_period');

			//   admin_redirect('system_settings/units');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$accounts_settings   = $this->accounts_model->getSettings();
			//$this->sma->print_arrays($accounts_settings);

			$this->data['payment_account_cash'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['payment_account_bank'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_banks_acct);
			$this->data['commisson_account'] = ($this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct)) ? $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct) : $this->accounts_model->getAccountByID($accounts_settings->accounts_bank_commisson_acct);
			$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['billers']  = $this->site->getAllCompanies('biller');
			$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
			//$this->sma->print_arrays($this->data['commisson_account']);

			$this->load->view($this->theme . 'accounts/add_accounting_period', $this->data);
		}
	}
	public function closingaccountingperiods()
	{
		$this->sma->checkPermissions('fiscal_year_closing', true);

		$this->form_validation->set_rules('period_old_year', lang('Year'), 'trim|required|numeric');
		$period_old_year     = $this->input->post('period_old_year');


		if ($this->form_validation->run() == true) {
			//$this->sma->print_arrays($period_old_year);

			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_does_not_exist'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}
		} elseif ($this->input->post('Closing_accounting_periods')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounting_period');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->closeAccountingPeriod($period_old_year)) {

			$this->session->set_flashdata('message', lang('Accounting_periods_successfully_closed'));
			admin_redirect('accounts/accounting_period');

			//   admin_redirect('system_settings/units');
		} else {

			$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYearActive();
			//$this->sma->print_arrays($this->data['period_old_year']);
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();



			$this->load->view($this->theme . 'accounts/closingaccountingperiods', $this->data);
		}
	}


	public function closeincomestatement()
	{
		$this->sma->checkPermissions('fiscal_year_closing', true);

		$this->form_validation->set_rules('period_old_year', lang('Year'), 'trim|required|numeric');
		$period_old_year     = $this->input->post('period_old_year');


		if ($this->form_validation->run() == true) {


			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_does_not_exist'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}
			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_is_already_closed'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}

			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1', 'NOT CLOSED');


			if ($old_accounting_period_count->count != 0) {
				$this->session->set_flashdata('error', lang('There_are_unclosed_periods_please_close_them_first'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}

			$old_accounting_period = $this->accounts_model->getMaxMinAccountingPeriodByYear($period_old_year);

			//$this->sma->print_arrays($old_accounting_period->period_from_date." 00:00:00",$old_accounting_period->period_to_date." 23:59:59");


			if ($this->getCurentIncomeStatementV2($this->accounts_model->getincomeStatementAccounts(), $old_accounting_period->period_from_date . " 00:00:00", $old_accounting_period->period_to_date . " 23:59:59",$period_old_year)['Final_Net_Income'] == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_is_already_closed'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}


			//$this->sma->print_arrays();



			//$this->sma->print_arrays($old_accounting_period);
			//$dbname=str_replace("cpesoft_","",$this->db->database);
			//$dbname="test";

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            	{
					$mainBranches=$this->site->getMainBranches(null);
					foreach($mainBranches as $mainBranch){


					//$this->sma->print_arrays($mainBranch->id);

			$incomeStatemenAccounts = $this->getCurentIncomeStatementV2($this->accounts_model->getincomeStatementAccounts(), $old_accounting_period->period_from_date . " 00:00:00", $old_accounting_period->period_to_date . " 23:59:59",$period_old_year,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id);

			//$this->sma->print_arrays($incomeStatemenAccounts);

			$entryDebitAccountClose = [];
			$entryDebitBalanceClose = [];
			$entryCreditAccountClose = [];
			$entryCreditBalanceClose = [];
			$tdebitClose = 0;
			$tcreditClose = 0;


			$entry_type = "Fiscal_year_closing";
			$entryDescription = "Fiscal_year_closing#" . $period_old_year;

			$reference_no = str_replace('-', "", $old_accounting_period->period_to_date);


			//$this->sma->print_arrays($incomeStatemenAccounts);

			//$this->sma->print_arrays($incomeStatemenAccounts['Revenues']);
			$entryRevenuesDebitAccount = [];
			$entryRevenuesDebitBalance = [];
			$entryRevenuesCreditAccount = [];
			$entryRevenuesCreditBalance = [];

			foreach ($incomeStatemenAccounts['Revenues']  as $key => $balance) {
				if ($balance == 0) {
					continue;
				}

				$entryRevenuesDebitAccount[] = $key;
				$entryRevenuesDebitBalance[] = $this->sma->formatDecimal(abs($balance));


				$tdebitClose = $tdebitClose + $balance;
			}

			$entryRevenuesCreditAccount[] = $this->accounts_model->getAccountByAccountID('51')->id;
			$entryRevenuesCreditBalance[] = $this->sma->formatDecimal(abs($tdebitClose));

			//$this->sma->print_arrays($entryRevenuesDebitAccount,$entryRevenuesCreditAccount,$entryRevenuesDebitBalance,$entryRevenuesCreditBalance,$reference_no,$entry_type,null,null,null,null,$old_accounting_period->period_to_date." 23:59:59",$entryDescription);


			$this->site->createEntryMultiSide($entryRevenuesDebitAccount, $entryRevenuesCreditAccount, $entryRevenuesDebitBalance, $entryRevenuesCreditBalance, $reference_no, $entry_type, null, null, null, null, $old_accounting_period->period_to_date . " 23:59:59", $entryDescription,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id);




			$entryExpensesDebitAccount = [];
			$entryExpensesDebitBalance = [];
			$entryExpensesCreditAccount = [];
			$entryExpensesCreditBalance = [];


			foreach ($incomeStatemenAccounts['Expenses']  as $key => $balance) {

				if ($balance == 0) {
					continue;
				}
				$entryExpensesCreditAccount[] = $key;
				$entryExpensesCreditBalance[] = $this->sma->formatDecimal(abs($balance));


				$tcreditClose = $tcreditClose + $balance;
			}

			$entryExpensesDebitAccount[] = $this->accounts_model->getAccountByAccountID('51')->id;
			$entryExpensesDebitBalance[] = $this->sma->formatDecimal(abs($tcreditClose));



			$this->site->createEntryMultiSide($entryExpensesDebitAccount, $entryExpensesCreditAccount, $entryExpensesDebitBalance, $entryExpensesCreditBalance, $reference_no, $entry_type, null, null, null, null, $old_accounting_period->period_to_date . " 23:59:59", $entryDescription,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id);

				}

				}else{



			$incomeStatemenAccounts = $this->getCurentIncomeStatementV2($this->accounts_model->getincomeStatementAccounts(), $old_accounting_period->period_from_date . " 00:00:00", $old_accounting_period->period_to_date . " 23:59:59",$period_old_year);

			//$this->sma->print_arrays($incomeStatemenAccounts);

			$entryDebitAccountClose = [];
			$entryDebitBalanceClose = [];
			$entryCreditAccountClose = [];
			$entryCreditBalanceClose = [];
			$tdebitClose = 0;
			$tcreditClose = 0;


			$entry_type = "Fiscal_year_closing";
			$entryDescription = "Fiscal_year_closing#" . $period_old_year;

			$reference_no = str_replace('-', "", $old_accounting_period->period_to_date);


			//$this->sma->print_arrays($incomeStatemenAccounts);

			//$this->sma->print_arrays($incomeStatemenAccounts['Revenues']);
			$entryRevenuesDebitAccount = [];
			$entryRevenuesDebitBalance = [];
			$entryRevenuesCreditAccount = [];
			$entryRevenuesCreditBalance = [];

			foreach ($incomeStatemenAccounts['Revenues']  as $key => $balance) {
				if ($balance == 0) {
					continue;
				}

				$entryRevenuesDebitAccount[] = $key;
				$entryRevenuesDebitBalance[] = $this->sma->formatDecimal(abs($balance));


				$tdebitClose = $tdebitClose + $balance;
			}

			$entryRevenuesCreditAccount[] = $this->accounts_model->getAccountByAccountID('51')->id;
			$entryRevenuesCreditBalance[] = $this->sma->formatDecimal(abs($tdebitClose));

			//$this->sma->print_arrays($entryRevenuesDebitAccount,$entryRevenuesCreditAccount,$entryRevenuesDebitBalance,$entryRevenuesCreditBalance,$reference_no,$entry_type,null,null,null,null,$old_accounting_period->period_to_date." 23:59:59",$entryDescription);


			$this->site->createEntryMultiSide($entryRevenuesDebitAccount, $entryRevenuesCreditAccount, $entryRevenuesDebitBalance, $entryRevenuesCreditBalance, $reference_no, $entry_type, null, null, null, null, $old_accounting_period->period_to_date . " 23:59:59", $entryDescription);




			$entryExpensesDebitAccount = [];
			$entryExpensesDebitBalance = [];
			$entryExpensesCreditAccount = [];
			$entryExpensesCreditBalance = [];


			foreach ($incomeStatemenAccounts['Expenses']  as $key => $balance) {

				if ($balance == 0) {
					continue;
				}
				$entryExpensesCreditAccount[] = $key;
				$entryExpensesCreditBalance[] = $this->sma->formatDecimal(abs($balance));


				$tcreditClose = $tcreditClose + $balance;
			}

			$entryExpensesDebitAccount[] = $this->accounts_model->getAccountByAccountID('51')->id;
			$entryExpensesDebitBalance[] = $this->sma->formatDecimal(abs($tcreditClose));



			$this->site->createEntryMultiSide($entryExpensesDebitAccount, $entryExpensesCreditAccount, $entryExpensesDebitBalance, $entryExpensesCreditBalance, $reference_no, $entry_type, null, null, null, null, $old_accounting_period->period_to_date . " 23:59:59", $entryDescription);
		}
			/*
	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_ready_funds_acct);
		$acc = (array) $acc[0];


	//$this->sma->print_arrays($incomeStatemenAccounts['Final_Net_Income']);
	if($incomeStatemenAccounts['Final_Net_Income']>0){
		$this->site->createEntryMultiSide($acc['id'],$this->accounts_model->getAccountByAccountID('51')->id,$this->sma->formatDecimal(abs($incomeStatemenAccounts['Final_Net_Income'])),$this->sma->formatDecimal(abs($incomeStatemenAccounts['Final_Net_Income'])),$reference_no,$entry_type,null,null,null,null,$old_accounting_period->period_to_date." 23:59:59",$entryDescription);

	}else if($incomeStatemenAccounts['Final_Net_Income']<0){
		//$this->sma->print_arrays($this->accounts_model->getAccountByAccountID('51')->id,$acc['id'],$this->sma->formatDecimal(abs($incomeStatemenAccounts['Final_Net_Income'])),$this->sma->formatDecimal(abs($incomeStatemenAccounts['Final_Net_Income'])),$reference_no,$entry_type,null,null,null,null,$old_accounting_period->period_to_date." 23:59:59",$entryDescription);

		$this->site->createEntryMultiSide($this->accounts_model->getAccountByAccountID('51')->id,$acc['id'],$this->sma->formatDecimal(abs($incomeStatemenAccounts['Final_Net_Income'])),$this->sma->formatDecimal(abs($incomeStatemenAccounts['Final_Net_Income'])),$reference_no,$entry_type,null,null,null,null,$old_accounting_period->period_to_date." 23:59:59",$entryDescription);

	}

	*/
			$this->session->set_flashdata('message', lang('Close_the_income_statement_successfully_added'));
			admin_redirect('accounts/accounting_period');
		} elseif ($this->input->post('closeincomestatement')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounting_period');
		}


		$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
		$this->data['modal_js']   = $this->site->modal_js();
		$accounts_settings   = $this->accounts_model->getSettings();
		//$this->sma->print_arrays($accounts_settings);
		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYearActive();

		$this->data['payment_account_cash'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_boxes_acct);
		$this->data['payment_account_bank'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_banks_acct);
		$this->data['commisson_account'] = ($this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct)) ? $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct) : $this->accounts_model->getAccountByID($accounts_settings->accounts_bank_commisson_acct);
		$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
		$this->data['billers']  = $this->site->getAllCompanies('biller');
		$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
		$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
		//$this->sma->print_arrays($this->data['commisson_account']);

		$this->load->view($this->theme . 'accounts/closeincomestatement', $this->data);
	}

	public function fiscal_year_closing()
	{
		$this->sma->checkPermissions('fiscal_year_closing', true);
		ini_set('max_execution_time', 600);

		$this->form_validation->set_rules('period_old_year', lang('Year'), 'trim|required|numeric');
		$this->form_validation->set_rules('period_new_year', lang('The_new_year'), 'trim|required|numeric');
		$this->form_validation->set_rules('period_from_date', lang('Start_Period'), 'trim|required');
		$this->form_validation->set_rules('period_to_date', lang('End_Period'), 'trim|required');


		$period_old_year     = $this->input->post('period_old_year');
		$period_year     = $this->input->post('period_new_year');
		$period_from_date     = $this->input->post('period_from_date');
		$period_to_date       = $this->input->post('period_to_date');



		if ($this->form_validation->run() == true) {


			/*$old_accounting_period=$this->accounts_model->getAllAccountingPeriodByYear($period_old_year);
		$new_accounting_period=$this->accounts_model->getAllAccountingPeriodByYear($period_year);

		$old_accounting_period_count=$this->accounts_model->getCountAccountingPeriodByYear($period_old_year);
		$new_accounting_period_count=$this->accounts_model->getCountAccountingPeriodByYear($period_year);
		*/
			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_does_not_exist'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}
			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_is_already_closed'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}

			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1', 'NOT CLOSED');


			if ($old_accounting_period_count->count != 0) {
				$this->session->set_flashdata('error', lang('There_are_unclosed_periods_please_close_them_first'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}


			$new_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_year);
			if ($new_accounting_period_count->count != 0) {
				$this->session->set_flashdata('error', lang('The_new_fiscal_year_is_already_here'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}




			$period_from_date = $this->sma->fld($period_from_date);
			$period_to_date = $this->sma->fld($period_to_date);

			$ts1 = strtotime($period_from_date);
			$ts2 = strtotime($period_to_date);

			$year1 = date('Y', $ts1);
			$year2 = date('Y', $ts2);

			$month1 = date('m', $ts1);
			$month2 = date('m', $ts2);

			$diff = (($year2 - $year1) * 12) + ($month2 - $month1 + 1);

			$first_day = $period_from_date;//date(sprintf($year1 . '-%s-01', floor(($diff - 1) / $diff) * $diff + 1));

			//$this->sma->print_arrays($first_day);



			$old_accounting_period = $this->accounts_model->getMaxMinAccountingPeriodByYear($period_old_year);
			//$this->sma->print_arrays($old_accounting_period);
			//$dbname=str_replace("cpesoft_","",$this->db->database);
			//$dbname="test";






			$balancesheetaccounts = $this->accounts_model->getbalanceSheetAccounts();


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            	{
					$mainBranches=$this->site->getMainBranches(null);

					foreach($mainBranches as $mainBranch){



							//$this->sma->print_arrays($mainBranch);
			//


			$entryDebitAccount = [];
			$entryDebitBalance = [];
			$entryCreditAccount = [];
			$entryCreditBalance = [];
			$tdebit = 0;
			$tcredit = 0;


			foreach ($balancesheetaccounts as $account) {
				$account_id = $account->id;
				//$type       = $_POST['type'][$r];
				//$quantity   = $_POST['quantity'][$r];
				//$cost   = $_POST['cost'][$r];
				//$value   = $_POST['value'][$r];

				$type       = "addition"; //$_POST['type'][$r];
				if($this->Settings->accounting_type=='periodic' && $account->account_type=="warehouses" && $account->account_ref_id>0){
					//$this->sma->print_arrays($levels);
					$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2($old_accounting_period->period_from_date . ' 00:00:00',$old_accounting_period->period_to_date . ' 23:59:59',$account->account_ref_id,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id);
					//$this->sma->print_arrays($end_of_period_stock_arr);
					$total_end_of_period_stock=0;
	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
	$amount = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));
		//$this->sma->print_arrays($end_of_period_stock_arr);
				}else{
					$amount = ($this->accounts_model->getAccountTotal($account->id, $old_accounting_period->period_from_date . ' 00:00:00', $old_accounting_period->period_to_date . ' 23:59:59',null,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id));
				}

				if ($amount == 0) {
					continue;
				}

				if ($account->accountSide == "Debit") {
					if ($amount > 0) {

						$debit   = (abs($amount));
						$credit   = 0;
					} else {
						$credit   = (abs($amount));
						$debit   = 0;
					}
				} else {
					if ($amount > 0) {
						$debit   = (abs($amount));
						$credit   = 0;
					} else {
						$credit   = (abs($amount));
						$debit   = 0;
					}
				}
				$balance   = $debit > 0 ? $debit : $credit;
				$tdebit = $tdebit + $debit;
				$tcredit = $tcredit + $credit;

				//'accountID1'   => $account->accountID,
				//'accountName'   => $account->accountName,

				$accounts[] = [
					'accountID'   => $account_id,
					'accountName'   => $account->accountName,
					'debit'         => $this->sma->formatDecimal($debit),
					'credit'     => $this->sma->formatDecimal($credit),
					'balance'     => $this->sma->formatDecimal($balance),

				];
				if ($debit != 0) {
					$entryDebitAccount[] = $account_id;
					$entryDebitBalance[] = $this->sma->formatDecimal($debit);
				}
				if ($credit != 0) {

					$entryCreditAccount[] = $account_id;
					$entryCreditBalance[] = $this->sma->formatDecimal($credit);
				}

		}

			//


			$pr_lo = $this->getCurentIncomeStatement($this->accounts_model->getincomeStatementAccounts(), $old_accounting_period->period_from_date . " 00:00:00", $old_accounting_period->period_to_date . " 23:59:58",$period_old_year,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id);

			//$this->sma->print_arrays($pr_lo);

			//$incomeStatemenAccounts=$this->getCurentIncomeStatementV2($this->accounts_model->getincomeStatementAccounts(),$old_accounting_period->period_from_date." 00:00:00",$old_accounting_period->period_to_date." 23:59:58");
			//$this->sma->print_arrays($pr_lo,$accounts);


			$accounts_settings   = $this->accounts_model->getSettings();

			if ($pr_lo > 0) {
				$entryDebitAccount[] = $accounts_settings->accounts_losses_stage_acct;

				$accounts[] = [
					'accountID'   => $accounts_settings->accounts_losses_stage_acct,
					'debit'         => $this->sma->formatDecimal(abs($pr_lo)),
					'credit'     => 0,
					'balance'     => $this->sma->formatDecimal(abs($pr_lo)),

				];
				$entryDebitBalance[] = $this->sma->formatDecimal(abs($pr_lo));
				$tdebit = $tdebit + abs($pr_lo);
			} else {
				$entryCreditAccount[] = $accounts_settings->accounts_profits_stage_acct;

				$accounts[] = [
					'accountID'   => $accounts_settings->accounts_profits_stage_acct,
					'debit'         => 0,
					'credit'     => $this->sma->formatDecimal(abs($pr_lo)),
					'balance'     => $this->sma->formatDecimal(abs($pr_lo)),

				];

				$entryCreditBalance[] = $this->sma->formatDecimal(abs($pr_lo));
				$tcredit = $tcredit + abs($pr_lo);
			}
			//$this->sma->print_arrays($accounts,$tdebit,' ',$tcredit,' ',$pr_lo);
			if ($this->sma->formatDecimal(abs($tdebit)) != $this->sma->formatDecimal(abs($tcredit))) {
				$this->session->set_flashdata('error', lang('The_balance_sheet_is_not_equal'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}


			$data1 = [
				'openingDate'         => $first_day . ' 00:00:00',
				'note'         => '',
				'userID'   => $this->session->userdata('user_id'),
			];
			//$this->sma->print_arrays($data1);

			//$this->accounts_model->updatePeriodYearStatus($period_old_year, '0');
			$this->accounts_model->addOpeningBalanceV2($data1, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance,null,null,null,null,$mainBranch->grpId,$mainBranch->coId,$mainBranch->id);


	}
	}else{


			//

			$entryDebitAccount = [];
			$entryDebitBalance = [];
			$entryCreditAccount = [];
			$entryCreditBalance = [];
			$tdebit = 0;
			$tcredit = 0;


			foreach ($balancesheetaccounts as $account) {
				$account_id = $account->id;
				//$type       = $_POST['type'][$r];
				//$quantity   = $_POST['quantity'][$r];
				//$cost   = $_POST['cost'][$r];
				//$value   = $_POST['value'][$r];
				$type       = "addition"; //$_POST['type'][$r];
				if($this->Settings->accounting_type=='periodic' && $account->account_type=="warehouses" && $account->account_ref_id>0){
					//$this->sma->print_arrays($levels);
					$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2($old_accounting_period->period_from_date . ' 00:00:00',$old_accounting_period->period_to_date . ' 23:59:59',$account->account_ref_id,1,1,1);
					//$this->sma->print_arrays($end_of_period_stock_arr);
					$total_end_of_period_stock=0;
					foreach($end_of_period_stock_arr as $end_of_period_stock){
						$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
					}
					$amount = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));
					//$this->sma->print_arrays($end_of_period_stock_arr);
				}else{
					$amount = ($this->accounts_model->getAccountTotal($account->id, $old_accounting_period->period_from_date . ' 00:00:00', $old_accounting_period->period_to_date . ' 23:59:59'));
				}
				if ($amount == 0) {
					continue;
				}

				if ($account->accountSide == "Debit") {
					if ($amount > 0) {

						$debit   = abs($amount);
						$credit   = 0;
					} else {
						$credit   = abs($amount);
						$debit   = 0;
					}
				} else {
					if ($amount > 0) {
						$debit   = abs($amount);
						$credit   = 0;
					} else {
						$credit   = abs($amount);
						$debit   = 0;
					}
				}
				$balance   = $debit > 0 ? $debit : $credit;
				$tdebit = $tdebit + $debit;
				$tcredit = $tcredit + $credit;

				//'accountID1'   => $account->accountID,
				//'accountName'   => $account->accountName,

				$accounts[] = [
					'accountID'   => $account_id,
					'accountName'   => $account->accountName,
					'debit'         => $this->sma->formatDecimal($debit),
					'credit'     => $this->sma->formatDecimal($credit),
					'balance'     => $this->sma->formatDecimal($balance),

				];
				if ($debit != 0) {
					$entryDebitAccount[] = $account_id;
					$entryDebitBalance[] = $this->sma->formatDecimal($debit);
				}
				if ($credit != 0) {

					$entryCreditAccount[] = $account_id;
					$entryCreditBalance[] = $this->sma->formatDecimal($credit);
				}
			}

			//


			$pr_lo = $this->getCurentIncomeStatement($this->accounts_model->getincomeStatementAccounts(), $old_accounting_period->period_from_date . " 00:00:00", $old_accounting_period->period_to_date . " 23:59:58",$period_old_year);

			//$this->sma->print_arrays($pr_lo);

			//$incomeStatemenAccounts=$this->getCurentIncomeStatementV2($this->accounts_model->getincomeStatementAccounts(),$old_accounting_period->period_from_date." 00:00:00",$old_accounting_period->period_to_date." 23:59:58");
			//$this->sma->print_arrays($pr_lo,$accounts);


			$accounts_settings   = $this->accounts_model->getSettings();

			if ($pr_lo > 0) {
				$entryDebitAccount[] = $accounts_settings->accounts_losses_stage_acct;

				$accounts[] = [
					'accountID'   => $accounts_settings->accounts_losses_stage_acct,
					'debit'         => $this->sma->formatDecimal(abs($pr_lo)),
					'credit'     => 0,
					'balance'     => $this->sma->formatDecimal(abs($pr_lo)),

				];
				$entryDebitBalance[] = $this->sma->formatDecimal(abs($pr_lo));
				$tdebit = $tdebit + abs($pr_lo);
			} else {
				$entryCreditAccount[] = $accounts_settings->accounts_profits_stage_acct;

				$accounts[] = [
					'accountID'   => $accounts_settings->accounts_profits_stage_acct,
					'debit'         => 0,
					'credit'     => $this->sma->formatDecimal(abs($pr_lo)),
					'balance'     => $this->sma->formatDecimal(abs($pr_lo)),

				];

				$entryCreditBalance[] = $this->sma->formatDecimal(abs($pr_lo));
				$tcredit = $tcredit + abs($pr_lo);
			}
			//$this->sma->print_arrays($accounts,$tdebit,' ',$tcredit,' ',$pr_lo);
			if ($this->sma->formatDecimal(abs($tdebit)) != $this->sma->formatDecimal(abs($tcredit))) {
				$this->session->set_flashdata('error', lang('The_balance_sheet_is_not_equal'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}


			$data1 = [
				'openingDate'         => $first_day . ' 00:00:00',
				'note'         => '',
				'userID'   => $this->session->userdata('user_id'),
			];
			//$this->sma->print_arrays($data1);


			$this->accounts_model->addOpeningBalance($data1, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance);

	}

			$this->accounts_model->updatePeriodYearStatus($period_old_year, '0');



			$count = 1;
			for ($i = $diff; $i <= 12; $i = $i + $diff) {
				$start = $period_from_date;//date(sprintf($year1 . '-%s-01', floor(($i - 1) / $diff) * $diff + 1));
				$end = $period_to_date;//date("Y-m-t", strtotime(date(sprintf($year1 . '-%s-t', floor(($i + ($diff - 1)) / $diff) * $diff))));;









				//$this->sma->print_arrays($i,$_POST,$diff,$start,$end);



				$period = [
					'period_year'            => $period_year,
					'period_year_status'            => '1',
					'period_code'            => str_pad($count, 2, '0', STR_PAD_LEFT),
					'period_from_date'            => $start,
					'period_to_date'            => $end,
					'period_status'            => $count == 1 ? '1' : '2',
					'created_by'            => $this->session->userdata('user_id'),
				];
				$periods[] = $period;
				//$this->data['bond_no'] = $this->accounts_model->getMaxAccountingPeriodByYear();
				$count++;
			}
			//$this->sma->print_arrays($periods);


		} elseif ($this->input->post('fiscal_year_closing')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounting_period');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->addAccountingPeriod($periods)) {

			$this->session->set_flashdata('message', lang('Accounting_periods_successfully_added'));
			admin_redirect('accounts/accounting_period');

			//   admin_redirect('system_settings/units');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$accounts_settings   = $this->accounts_model->getSettings();
			//$this->sma->print_arrays($accounts_settings);
			$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear();

			$this->data['payment_account_cash'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['payment_account_bank'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_banks_acct);
			$this->data['commisson_account'] = ($this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct)) ? $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct) : $this->accounts_model->getAccountByID($accounts_settings->accounts_bank_commisson_acct);
			$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['billers']  = $this->site->getAllCompanies('biller');
			$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
			//$this->sma->print_arrays($this->data['commisson_account']);

			$this->load->view($this->theme . 'accounts/fiscal_year_closing', $this->data);
		}
	}


	public function edit_payment_type($id = null)
	{
		$this->sma->checkPermissions('edit_payment_type', true);
		$this->form_validation->set_rules('payment_name', lang('Payment_type_name'), 'trim|required');
		$this->form_validation->set_rules('commisson_perc', lang('Commission_%'), 'trim|numeric');

		$payment_type_details = $this->accounts_model->getPaymentTypeByID($id);
		if ($this->form_validation->run() == true) {
			$data = [
				'payment_name'            => $this->input->post('payment_name'),
				'payment_ename'            => $this->input->post('payment_ename'),
				'commisson_perc'            => $this->input->post('commisson_perc'),
				'payment_account'            => $this->input->post('payment_type') == 'cash' ? $this->input->post('payment_account') : $this->input->post('payment_account1'),
				'commisson_account'            => $this->input->post('commisson_account'),
				'description'            => $this->input->post('description'),
				'payment_type'            => $this->input->post('payment_type'),
				'tax_rate'            => $this->input->post('tax_rate'),
				'payment_cc_id'            => $this->input->post('payment_type') == 'CC' ? $this->input->post('payment_cc_id') : 0,
			];
		} elseif ($this->input->post('edit_payment_type')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/payment_types');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->updatePaymentType($id, $data)) {
			$this->session->set_flashdata('message', lang('Payment_type_successfully_updated'));
			admin_redirect('accounts/payment_types');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$this->data['payment_type']       = $payment_type_details;
			$this->data['paymentType'] = $this->site->getpaymentType(1);
			$accounts_settings   = $this->accounts_model->getSettings();
			$this->data['payment_account_cash'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['payment_account_bank'] = $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_main_banks_acct);
			$this->data['commisson_account'] = ($this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct)) ? $this->accounts_model->getAccountChildsByID($accounts_settings->accounts_bank_commisson_acct) : $this->accounts_model->getAccountByID($accounts_settings->accounts_bank_commisson_acct);
			$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
			$this->data['billers']  = $this->site->getAllCompanies('biller');
			$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
			$this->load->view($this->theme . 'accounts/edit_payment_type', $this->data);
		}
	}

	public function reorder_accountsid()
	{
		$accounts = $this->accounts_model->getAccountsReOrder();
		//$this->sma->print_arrays($accounts);

		foreach ($accounts as $account) {
			if (strlen($account->accountID) > 1) {

				$this->accounts_model->reOrderAccounts_temp($account->id, $account->accountID);
				//$this->sma->print_arrays($account);
			}
		}
	}

	public function reorder_cost_centersid()
	{
		$accounts = $this->accounts_model->getCostCentersReOrder();
		//$this->sma->print_arrays($accounts);

		foreach ($accounts as $account) {
			//if(strlen($account->costID)>1){
			if ($account->costID != 0) {


				$this->accounts_model->reOrderCostCenters_temp($account->id, $account->costID);
				//$this->sma->print_arrays($account);
			}
		}
	}

	public function add_account($id = null)
	{
		$this->sma->checkPermissions('add_account', true);

		$this->form_validation->set_rules('accountID', lang('Account_No'), 'trim|required');
		$this->form_validation->set_rules('accountName', lang('Account_name'), 'trim|required');
		if ($this->input->post('id')) {
			$id = $this->input->post('id');
		}
		$account_count = $this->accounts_model->getCountAccountByID($id);
		$account_details = $this->accounts_model->getAccountByID($id);
		$newAccountSide=0;
		if($account_details->accountCategory=='الإلتزامات وحقوق الملكية'){
			$newAccountSide='1';
		}
		if($account_details->accountCategory=='الإيرادات'){
			$newAccountSide='1';
		}

		if (strlen($account_details->accountID) <= 1)
			$accountID = $account_details->accountID . "" . $account_count->count;
		else
			$accountID = $account_details->accountID . "0" . $account_count->count;


		$level = ($account_details->level) + 1;




		$account_type = "";
		$accounts_settings   = $this->accounts_model->getSettings();
		if ($account_details->id == $accounts_settings->warehouses_acct) {
			$account_type = "warehouses";
		}
		if ($account_details->id == $accounts_settings->sales_acct) {
			$account_type = "sales";
		}
		if ($account_details->id == $accounts_settings->sales_return_acct) {
			$account_type = "return_sales";
		}


		if ($account_details->id == $accounts_settings->accounts_international_suppliers_acct) {
			$account_type = "international_suppliers";
		}

		if ($account_details->id == $accounts_settings->accounts_local_suppliers_acct) {
			$account_type = "local_suppliers";
		}



		if ($account_details->id == $accounts_settings->accounts_international_customers_acct) {
			$account_type = "international_customers";
		}

		if ($account_details->id == $accounts_settings->accounts_local_customers_acct) {
			$account_type = "local_customers";
		}

		if ($account_details->id == $accounts_settings->accounts_main_boxes_acct) {
			$account_type = "billers";
		}

		if ($account_details->id == $accounts_settings->business_guarantee_insurance_acct) {
			$account_type = "insurance";
		}

		if ($account_details->id == $accounts_settings->advance_payments_acct) {
			$account_type = "advance";
		}





		//

		if ($this->form_validation->run() == true) {
			$accountSide = 'Debit';
			//$accountStatement = 'Income Statement';

			if ($this->input->post('accountSide') == 1)
				$accountSide = 'Credit';

			//if ($this->input->post('accountStatement') == 1)
				//$accountStatement = 'Balance Sheet';

			if ($_POST['costCenterId'] == array_unique($_POST['costCenterId'])) {

				$costBalance  = 0.00;

				foreach ($_POST['costCenterPercentage'] as $key => $balance) {
					$costBalance += $balance;
				}

				//if($costBalance==100){



				$data = [
					'accountID'            => $this->input->post('accountID'),
					'accountName'            => $this->input->post('accountName'),
					'accountNameEn'            => $this->input->post('accountNameEn'),
					'master_account'            => $this->input->post('master_account'),
					'accountCategory'            => $account_details->accountCategory,
					'parent_id'            => $id,
					'accountSide'            => $accountSide,
					'accountStatement'            => $account_details->accountStatement ? $account_details->accountStatement : ($id>2 ? 'Income Statement' : 'Balance Sheet'),
					'account_type'            => $account_type,
					'account_ref_id'            => $this->input->post('account_ref_id'),
					'account_ref_id2'            => $this->input->post('account_ref_id2') ? $this->input->post('account_ref_id2') : null,
					'account_ref_id3'            => $this->input->post('account_ref_id3') ? $this->input->post('account_ref_id3') : null,
					'account_ref_id4'            => $this->input->post('account_ref_id4') ? $this->input->post('account_ref_id4') : null,
					'account_ref_id5'            => $this->input->post('account_ref_id5') ? $this->input->post('account_ref_id5') : null,
					'userID'   => $this->session->userdata('user_id'),
					'costCenterId'            => json_encode((array) $_POST['costCenterId']),
					'costCenterPercentage'            => json_encode((array) $_POST['costCenterPercentage']),
					'level'            => $level,


				];
				//$this->sma->print_arrays($data,$_POST);


				/*}else {
            $this->session->set_flashdata('error', "You cannot have Cost Centers Percentage not equal 100% . Please try again.");
            admin_redirect('accounts/accounts_tree');
        }*/
			} else {
				$this->session->set_flashdata('error', "You cannot have multiple matching Cost Center. Please try again.");
				admin_redirect('accounts/accounts_tree');
			}
		} elseif ($this->input->post('add_account')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounts_tree');
		}

		//$this->sma->print_arrays($data);


		if ($this->form_validation->run() == true && $this->accounts_model->addAccount($data)) {

			$this->session->set_flashdata('message', lang('Account_successfully_added'));
			admin_redirect('accounts/accounts_tree');

			//   admin_redirect('system_settings/units');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();

			$this->data['account_type']   = $account_type;
			if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) {
				$this->data['warehouses']      = $this->site->getAllShowrooms();
			} else {
				$this->data['warehouses'] = $this->site->getAllWarehouses();
			}

			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
			//$this->sma->print_arrays($this->data['cost_centers']);
			$this->data['local_suppliers']  = $this->site->getAllCompaniesType('supplier', '1');
			$this->data['international_suppliers']  = $this->site->getAllCompaniesType('supplier', '2');
			$this->data['local_customers']  = $this->site->getAllCompaniesType('customer', '1');
			$this->data['international_customers']  = $this->site->getAllCompaniesType('customer', '2');
			$this->data['billers']  = $this->site->getAllCompanies('biller');


			$this->data['accountID']   = $accountID;
			$this->data['id']   = $id;
			$this->data['newAccountSide']   = $newAccountSide;

			$this->load->view($this->theme . 'accounts/add_account', $this->data);
		}
	}


	public function add_cost_center($id = null)
	{
		$this->sma->checkPermissions('add_cost_center', true);

		$this->form_validation->set_rules('costID', lang('Cost_center_No'), 'trim|required');
		$this->form_validation->set_rules('costName', lang('Cost_center_name'), 'trim|required');
		if ($this->input->post('id')) {
			$id = $this->input->post('id');
		}
		$cost_count = $this->accounts_model->getCountCostCentersByID($id);
		$cost_details = $this->accounts_model->getCostCentersByID($id);

		//if(strlen($cost_details->costID)<=1)
		if ($cost_details->costID == 0) {
			$costID = $cost_count->count;
		} else
			$costID = $cost_details->costID . "0" . $cost_count->count;




		//

		if ($this->form_validation->run() == true) {


			//$this->sma->print_arrays($account_type);


			$data = [
				'costID'            => $this->input->post('costID'),
				'costName'            => $this->input->post('costName'),
				'costNameEn'            => $this->input->post('costNameEn'),
				'master_cost'            => $this->input->post('master_cost'),
				'parent_id'            => $id,
				'userID'   => $this->session->userdata('user_id'),

			];
			//$this->sma->print_arrays($data);

		} elseif ($this->input->post('add_cost_center')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/cost_centers');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->addCostCenter($data)) {

			$this->session->set_flashdata('message', lang('Cost_center_successfully_added'));
			admin_redirect('accounts/cost_centers');

			//   admin_redirect('system_settings/units');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();



			$this->data['costID']   = $costID;
			$this->data['id']   = $id;

			$this->load->view($this->theme . 'accounts/add_cost_center', $this->data);
		}
	}


	public function edit_account($accountID = null)
	{
		$this->sma->checkPermissions('edit_account', true);
		$this->form_validation->set_rules('accountID', lang('Account_No'), 'trim|required');
		$this->form_validation->set_rules('accountName', lang('Account_name'), 'trim|required');
		if ($this->input->post('accountID')) {
			$accountID = $this->input->post('accountID');
		}




		$account_details = $this->accounts_model->getAccountByAccountID($accountID);
		//$this->sma->print_arrays($account_details);

		$account_type = "";
		$accounts_settings   = $this->accounts_model->getSettings();
		if ($account_details->parent_id == $accounts_settings->warehouses_acct) {
			$account_type = "warehouses";
		}
		if ($account_details->parent_id == $accounts_settings->sales_acct) {
			$account_type = "sales";
		}
		if ($account_details->parent_id == $accounts_settings->sales_return_acct) {
			$account_type = "return_sales";
		}

		if ($account_details->parent_id == $accounts_settings->accounts_international_suppliers_acct) {
			$account_type = "international_suppliers";
		}

		if ($account_details->parent_id == $accounts_settings->accounts_local_suppliers_acct) {
			$account_type = "local_suppliers";
		}

		if ($account_details->parent_id == $accounts_settings->accounts_international_customers_acct) {
			$account_type = "international_customers";
		}

		if ($account_details->parent_id == $accounts_settings->accounts_local_customers_acct) {
			$account_type = "local_customers";
		}

		if ($account_details->parent_id == $accounts_settings->accounts_main_boxes_acct) {
			$account_type = "billers";
		}

		if ($account_details->parent_id == $accounts_settings->business_guarantee_insurance_acct) {
			$account_type = "insurance";
		}

		if ($account_details->parent_id == $accounts_settings->advance_payments_acct) {
			$account_type = "advance";
		}


		if ($this->form_validation->run() == true) {
			$accountSide = 'Debit';
			//$accountStatement = 'Income Statement';

			if ($this->input->post('accountSide') == 1)
				$accountSide = 'Credit';

			//if ($this->input->post('accountStatement') == 1)
				//$accountStatement = 'Balance Sheet';




			if ($_POST['costCenterId'] == array_unique($_POST['costCenterId'])) {

				$costBalance  = 0.00;

				foreach ($_POST['costCenterPercentage'] as $key => $balance) {
					$costBalance += $balance;
				}

				//if($costBalance==100){


				$data = [
					'accountID'            => $this->input->post('accountID'),
					'accountName'            => $this->input->post('accountName'),
					'accountNameEn'            => $this->input->post('accountNameEn'),
					'master_account'            => $this->input->post('master_account'),
					'accountCategory'            => $account_details->accountCategory,
					'parent_id'            => $account_details->parent_id,
					'accountSide'            => $accountSide,
					'account_type'            => $account_type,
					'account_ref_id'            => $this->input->post('account_ref_id'),
					'account_ref_id2'            => $this->input->post('account_ref_id2') ? $this->input->post('account_ref_id2') : null,
					'account_ref_id3'            => $this->input->post('account_ref_id3') ? $this->input->post('account_ref_id3') : null,
					'account_ref_id4'            => $this->input->post('account_ref_id4') ? $this->input->post('account_ref_id4') : null,
					'account_ref_id5'            => $this->input->post('account_ref_id5') ? $this->input->post('account_ref_id5') : null,
					'accountStatement'            => $account_details->accountStatement,
					'costCenterId'            => json_encode((array) $_POST['costCenterId']),
					'costCenterPercentage'            => json_encode((array) $_POST['costCenterPercentage']),


				];

				//$this->sma->print_arrays($data,$_POST);
				/*}else {
            $this->session->set_flashdata('error', "You cannot have Cost Centers Percentage not equal 100% . Please try again.");
            admin_redirect('accounts/accounts_tree');
        }*/
			} else {
				$this->session->set_flashdata('error', "You cannot have multiple matching Cost Center. Please try again.");
				admin_redirect('accounts/accounts_tree');
			}
		} elseif ($this->input->post('edit_account')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounts_tree');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->updateAccount($accountID, $data)) {
			$this->session->set_flashdata('message', lang('Account_successfully_updated'));
			admin_redirect('accounts/accounts_tree');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$this->data['account_details']       = $account_details;
			$this->data['account_type']   = $account_type;
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
			if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) {
				$this->data['warehouses']      = $this->site->getAllShowrooms();
			} else {
				$this->data['warehouses'] = $this->site->getAllWarehouses();
			}

			//$this->sma->print_arrays($this->data['warehouses']);

			//$this->data['warehouses'] = $this->site->getAllWarehouses();
			$this->data['local_suppliers']  = $this->site->getAllCompaniesType('supplier', '1');
			$this->data['international_suppliers']  = $this->site->getAllCompaniesType('supplier', '2');
			$this->data['local_customers']  = $this->site->getAllCompaniesType('customer', '1');
			$this->data['international_customers']  = $this->site->getAllCompaniesType('customer', '2');
			$this->data['billers']  = $this->site->getAllCompanies('biller');
			$bi=1;
			if(!$account_details->account_ref_id2){
			}else{
			$bi=2;
			if(!$account_details->account_ref_id3){
			}else{
			$bi=3;
			if(!$account_details->account_ref_id4){
			}else{
			$bi=4;
			if($account_details->account_ref_id5){
			$bi=5;
			}
			}
			}
			}

			//$this->sma->print_arrays($bi);
			$this->data['bi'] = $bi;


			$this->load->view($this->theme . 'accounts/edit_account', $this->data);
		}
	}


	public function edit_cost_center($costID = null)
	{
		$this->sma->checkPermissions('edit_cost_center', true);
		$this->form_validation->set_rules('costID', lang('Cost_center_No'), 'trim|required');
		$this->form_validation->set_rules('costName', lang('Cost_center_name'), 'trim|required');
		if ($this->input->post('costID')) {
			$costID = $this->input->post('costID');
		}




		$cost_details = $this->accounts_model->getCostCenterByCostCenterID($costID);
		//$this->sma->print_arrays($cost_details);



		if ($this->form_validation->run() == true) {

			$data = [
				'costID'            => $this->input->post('costID'),
				'costName'            => $this->input->post('costName'),
				'costNameEn'            => $this->input->post('costNameEn'),
				'master_cost'            => $this->input->post('master_cost'),
				'parent_id'            => $cost_details->parent_id,


			];

			//$this->sma->print_arrays($data);

		} elseif ($this->input->post('edit_cost_center')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/cost_centers');
		}

		if ($this->form_validation->run() == true && $this->accounts_model->updateCostCenter($costID, $data)) {
			$this->session->set_flashdata('message', lang('Cost_center_successfully_updated'));
			admin_redirect('accounts/cost_centers');
		} else {
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();
			$this->data['cost_details']       = $cost_details;



			$this->load->view($this->theme . 'accounts/edit_cost_center', $this->data);
		}
	}
	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function getAccountItem()
	{
		//$data = [];
		$parent_key = '0';
		//$row = $this->db->query('SELECT * from sma_gl_accounts');
		//$this->sma->print_arrays($this->accounts_model->getAccountItem());
		//if($row->num_rows() > 0)
		if ($data = $this->accounts_model->getAccountItem()) {
			//$data = $this->accounts_model->membersTree($parent_key);
		} else {
			$data = ["id" => "0", "name" => "No Members presnt in list", "text" => "No Members is presnt in list", "nodes" => []];
		}

		echo json_encode(array_values($data));
	}

	public function getCostCentersItem()
	{
		//$data = [];
		$parent_key = '0';
		//$row = $this->db->query('SELECT * from sma_gl_accounts');
		//$this->sma->print_arrays($this->accounts_model->getAccountItem());
		//if($row->num_rows() > 0)
		if ($data = $this->accounts_model->getCostCentersItem()) {
			//$data = $this->accounts_model->membersTree($parent_key);
		} else {
			$data = ["id" => "0", "name" => "No Members presnt in list", "text" => "No Members is presnt in list", "nodes" => []];
		}

		echo json_encode(array_values($data));
	}

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function membersTree($parent_key)
	{
		$row1 = [];
		$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="' . $parent_key . '"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$row1[$key]['id'] = $value['id'];
			if ($this->Settings->user_language == 'arabic') {
				$row1[$key]['name'] = $value['accountName'];
				$row1[$key]['text'] = $value['accountName'];
			} else {
				$row1[$key]['name'] = $value['accountNameEn'];
				$row1[$key]['text'] = $value['accountNameEn'];
			}
			$row1[$key]['nodes'] = array_values($this->membersTree($value['id']));
		}

		return $row1;
	}

	public function delete_account($id = null)
	{

		$this->sma->checkPermissions(null, true);
		if ($this->accounts_model->IsAccountHaveOperationsByID($id)) {
			//$this->sma->print_arrays($this->accounts_model->IsAccountHaveChildsByID($id));
			//$this->sma->send_json(['error' => 1, 'msg' => lang('Found_Sub_Account')]);
			$this->session->set_flashdata('error', lang('There_are_accounting_operations_on_this_account'));
			//$this->sma->print_arrays($this->accounts_model->IsAccountHaveChildsByID($id));
			redirect($_SERVER['HTTP_REFERER']);
		} else if ($this->accounts_model->IsAccountHaveChildsByID($id)) {
			//$this->sma->print_arrays($this->accounts_model->IsAccountHaveChildsByID($id));
			//$this->sma->send_json(['error' => 1, 'msg' => lang('Found_Sub_Account')]);
			$this->session->set_flashdata('error', lang('Found_Sub_Account'));
			//$this->sma->print_arrays($this->accounts_model->IsAccountHaveChildsByID($id));
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			if ($this->accounts_model->deleteAccount($id)) {
				//$this->sma->send_json(['error' => 0, 'msg' => lang('Account_successfully_deleted')]);
				$this->session->set_flashdata('message', lang('Account_successfully_deleted'));
				admin_redirect('accounts/accounts_tree');
			}
		}
	}


	/*public function delete_accounting_period()
	{
		$this->sma->checkPermissions('delete_accounting_period', true);

		if ($this->accounts_model->deleteAccountingPeriod($id)) {
			//$this->sma->send_json(['error' => 0, 'msg' => lang('Account_successfully_deleted')]);
			$this->session->set_flashdata('message', lang('Accounting_period_successfully_deleted'));
			redirect($_SERVER['HTTP_REFERER']);
		}
	}*/
	public function delete_payment_type($id = null)
	{
		$this->sma->checkPermissions('add_payment_type',true);

		if (!$this->accounts_model->getPaymentBygl_payment_method_id($id)) {
			if ($this->accounts_model->deletePaymentMethods($id)) {
				//$this->sma->send_json(['error' => 0, 'msg' => lang('Account_successfully_deleted')]);
				$this->sma->send_json(['error' => 0, 'msg' => lang('Payment_type_successfully_deleted')]);
			}
		} else {
			$this->sma->send_json(['error' => 1, 'msg' => lang('You_cannot_delete_this_payment_type_There_are_relevant_processes')]);
		}
	}



	public function delete_cost_center($id = null)
	{
		$this->sma->checkPermissions(null, true);
		if ($this->accounts_model->IsCostCenterHaveChildsByID($id)) {
			//$this->sma->print_arrays($this->accounts_model->IsAccountHaveChildsByID($id));
			//$this->sma->send_json(['error' => 1, 'msg' => lang('Found_Sub_Account')]);
			$this->session->set_flashdata('error', lang('Found_Sub_Cost_centers'));
			//$this->sma->print_arrays($this->accounts_model->IsAccountHaveChildsByID($id));
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			if ($this->accounts_model->deleteCostCenter($id)) {
				//$this->sma->send_json(['error' => 0, 'msg' => lang('Account_successfully_deleted')]);
				$this->session->set_flashdata('message', lang('Cost_center_successfully_deleted'));
				admin_redirect('accounts/cost_centers');
			}
		}
	}


	public function edit_opening_balance($id)
	{
		$this->sma->checkPermissions('edit_opening_balance', true);
		//$this->sma->print_arrays($this->input->post(NULL, TRUE));
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else
		*/ {
			$opening_balance = $this->accounts_model->getOpeningBalanceByID($id);
			if (!$id || !$opening_balance) {
				$this->session->set_flashdata('error', lang('opening_balance_not_found'));
				admin_redirect('accounts/opening_balance');
			}


			$this->form_validation->set_rules('date', lang('date'), 'required');

			if ($this->form_validation->run() == true) {


				//$this->sma->print_arrays($this->input->post(NULL, TRUE));
				//$this->sma->print_arrays(serialize(array_values($_POST['product_id'])));
				//$jsonData=json_encode($_POST['product_id']);
				//$this->sma->print_arrays(rawurldecode($_POST['product_id']));
				if ($this->Owner || $this->Admin) {
					$date = $this->sma->fld($this->input->post('date'));
				} else {
					$date = $opening_balance->date;
				}

				$currency_code         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;

				$currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;


				$note         = $this->sma->clear_tags($this->input->post('note'));
				$entryDebitAccount = [];
				$entryDebitBalance = [];
				$entryCreditAccount = [];
				$entryCreditBalance = [];

				$i = isset($_POST['account_id']) ? sizeof($_POST['account_id']) : 0;
				for ($r = 0; $r < $i; $r++) {
					$account_id = $_POST['account_id'][$r];
					//$type       = $_POST['type'][$r];
					//$quantity   = $_POST['quantity'][$r];
					//$cost   = $_POST['cost'][$r];
					//$value   = $_POST['value'][$r];
					$type       = "addition"; //$_POST['type'][$r];
					$debit   = $_POST['debit'][$r];
					$credit   = $_POST['credit'][$r];
					$cost_center   = $_POST['cost_center'][$r];
					$cost_center_amount   = $_POST['cost_center_amount'][$r];
					$balance   = $debit > 0 ? $debit : $credit;




				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            	{
					$accounts[] = [
						'accountID'   => $account_id,
						'debit'         => $this->sma->formatDecimal($debit),
						'credit'     => $this->sma->formatDecimal($credit),
						'balance'     => $this->sma->formatDecimal($balance),
						'cost_center'     => $cost_center,
						'cost_center_amount'     => $this->sma->formatDecimal($cost_center_amount),
						'grpId'     => $opening_balance->grpId,
						'coId'     => $opening_balance->coId,
						'brId'     => $opening_balance->brId,
					];
				}else{
					$accounts[] = [
						'accountID'   => $account_id,
						'debit'         => $this->sma->formatDecimal($debit),
						'credit'     => $this->sma->formatDecimal($credit),
						'balance'     => $this->sma->formatDecimal($balance),
						'cost_center'     => $cost_center,
						'cost_center_amount'     => $this->sma->formatDecimal($cost_center_amount),

					];
				}

					if ($debit != 0) {
						$entryDebitAccount[] = $account_id;
						$entryDebitBalance[] = $this->sma->formatDecimal($currency_rate > 0 ? ($debit * $currency_rate) : $debit);
						$costCenterDebitId[] = json_encode((array) $cost_center);
						$costCenterDebitAm = $cost_center ? $this->sma->formatDecimal($cost_center_amount) : "";
						$costCenterDebitAmount[] = json_encode((array) $costCenterDebitAm);
					} else if ($credit != 0) {

						$entryCreditAccount[] = $account_id;
						$entryCreditBalance[] = $this->sma->formatDecimal($currency_rate > 0 ? ($credit * $currency_rate) : $credit);
						$costCenterCreditId[] = json_encode((array) $cost_center);
						$costCenterCreditAm = $cost_center ? $this->sma->formatDecimal($cost_center_amount) : "";
						$costCenterCreditAmount[] = json_encode((array) $costCenterCreditAm);
					}
				}

				if (empty($accounts)) {
					$this->form_validation->set_rules('product', lang('products'), 'required');
				} else {
					krsort($accounts);
				}

				//$this->sma->print_arrays($accounts);



				$data = [
					'openingDate'         => $date,
					'note'         => $note,
					'currency_code'         => $currency_code,
					'currency_rate'         => $currency_rate,
					'userID'   => $this->session->userdata('user_id'),

				];

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            	{
				$data  ['grpId']=$opening_balance->grpId;
                $data  ['coId']=$opening_balance->coId;
                $data  ['brId']=$opening_balance->brId;
				}

				$costCenterDebitId2 = [];
				$costCenterDebitAmount2 = [];
				$costCenterCreditId2 = [];
				$costCenterCreditAmount2 = [];

				foreach ($costCenterDebitId as $key => $acc) {
					$costCenterDebitId2[] = json_decode($acc);
				}
				foreach ($costCenterDebitAmount as $key => $acc) {
					$costCenterDebitAmount2[] = json_decode($acc);
				}
				foreach ($costCenterCreditId as $key => $acc) {
					$costCenterCreditId2[] = json_decode($acc);
				}
				foreach ($costCenterCreditAmount as $key => $acc) {
					$costCenterCreditAmount2[] = json_decode($acc);
				}
				if ($costCenterDebitId)
					$costCenterDebitId = json_encode((array) $costCenterDebitId2);
				if ($costCenterDebitAmount)
					$costCenterDebitAmount = json_encode((array) $costCenterDebitAmount2);
				if ($costCenterCreditId)
					$costCenterCreditId = json_encode((array) $costCenterCreditId2);
				if ($costCenterCreditAmount)
					$costCenterCreditAmount = json_encode((array) $costCenterCreditAmount2);


				//$this->sma->print_arrays($data, $products);

			}


			if ($this->form_validation->run() == true && $this->accounts_model->updateOpeningBalance($id, $data, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId, $costCenterCreditId, $costCenterDebitAmount, $costCenterCreditAmount,$opening_balance->grpId,$opening_balance->coId,$opening_balance->brId)) {
				$this->session->set_userdata('remove_obls', 1);
				$this->session->set_flashdata('message', lang('Opening_balance_successfully_updated'));
				admin_redirect('accounts/opening_balance');
			} else {
				$opening_balance_items = $this->accounts_model->getOpeningBalanceItems($id);
				//$this->sma->print_arrays($opening_balance_items);
				krsort($opening_balance_items);
				foreach ($opening_balance_items as $item) {
					//$this->sma->print_arrays($item);
					$c = sha1(uniqid(mt_rand(), true));
					//if ($item->counted != $item->expected) {
					//$product     = $this->site->getProductByID($item->product_id);
					$row         = json_decode('{}');
					$row->id     = $item->account_id;
					$row->accountID   = $item->accountID;
					$accountName   = $item->accounts_nameEn ? $item->accounts_nameEn :  $item->accounts_name;
					$row->accountName   = $this->Settings->user_language == 'arabic' ? $item->accounts_name :  $accountName;

					$row->accountSide    = $item->accountSide;
					$row->debit   =  $item->debit;
					$row->credit   = $item->credit;
					$row->balance    = $item->balance;
					$row->cost_center = $item->cost_center ? $item->cost_center : NULL;
					$row->cost_center_amount = $item->cost_center_amount ? $item->cost_center_amount : 0;
					//$options     = $this->products_model->getProductOptions($product->id);
					//$row->option = $item->product_variant_id ? $item->product_variant_id : 0;
					//$row->serial = '';
					$ri          = $this->Settings->item_addition ? $item->account_id : $c;

					$pr[$ri] = [
						'id' => $c, 'item_id' => $row->id, 'label' => $row->accountName . ' (' . $row->accountID . ')',
						'row'        => $row,
					];
					$c++;
					//}
				}



				$cost_centers     = $this->accounts_model->getAllNoMastersCostCenters();

				//$this->sma->print_arrays($opening_balance);
				$this->data['opening_balance_items'] = json_encode($pr);
				$this->data['cost_centers'] = json_encode($cost_centers);
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['count_id'] = $id;
				$this->data['opening_balance'] = $opening_balance;

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->data['mainGroups'] = $this->site->getMainGroup();
				$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

			}


				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_opening_balance'), 'page' => lang('Edit_opening_balance')], ['link' => '#', 'page' => lang('Edit_opening_balance')]];
				$meta                           = ['page_title' => lang('Edit_opening_balance'), 'bc' => $bc];
				$this->page_construct('accounts/edit_opening_balance', $meta, $this->data);
			}
		}
	}

	public function add_opening_balance($all_accounts = null,$all_brId = null)
	{
		$this->sma->checkPermissions('add_opening_balance', true);

		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');


		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {
			/*
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */

			$this->form_validation->set_rules('date', lang('date'), 'required');


			if ($this->form_validation->run() == true) {
				if ($this->Owner || $this->Admin) {
					$date = $this->sma->fld($this->input->post('date'));
				} else {
					$date = date('Y-m-d H:s:i');
				}

				$currency_code         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;

				$currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;


				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/
			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{
				$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : 1;
				$coId     = $this->input->post('coId') ? $this->input->post('coId') : 1;
				$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			}

				$note         = $this->sma->clear_tags($this->input->post('note'));
				$entryDebitAccount = [];
				$entryDebitBalance = [];
				$entryCreditAccount = [];
				$entryCreditBalance = [];


				$i = isset($_POST['account_id']) ? sizeof($_POST['account_id']) : 0;
				for ($r = 0; $r < $i; $r++) {
					$account_id = $_POST['account_id'][$r];
					//$type       = $_POST['type'][$r];
					//$quantity   = $_POST['quantity'][$r];
					//$cost   = $_POST['cost'][$r];
					//$value   = $_POST['value'][$r];
					$type       = "addition"; //$_POST['type'][$r];
					$debit   = $_POST['debit'][$r];
					$credit   = $_POST['credit'][$r];
					$cost_center   = $_POST['cost_center'][$r];
					$cost_center_amount   = $_POST['cost_center_amount'][$r];
					$balance   = $debit > 0 ? $debit : $credit;



					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{
					$accounts[] = [
						'accountID'   => $account_id,
						'debit'         => $this->sma->formatDecimal($debit),
						'credit'     => $this->sma->formatDecimal($credit),
						'balance'     => $this->sma->formatDecimal($balance),
						'cost_center'     => $cost_center,
						'cost_center_amount'     => $this->sma->formatDecimal($cost_center_amount),
						'grpId'     => $grpId,
						'coId'     => $coId,
						'brId'     => $brId,

					];
				}else{
					$accounts[] = [
						'accountID'   => $account_id,
						'debit'         => $this->sma->formatDecimal($debit),
						'credit'     => $this->sma->formatDecimal($credit),
						'balance'     => $this->sma->formatDecimal($balance),
						'cost_center'     => $cost_center,
						'cost_center_amount'     => $this->sma->formatDecimal($cost_center_amount),


					];
				}



					if ($debit != 0) {
						$entryDebitAccount[] = $account_id;
						$entryDebitBalance[] = $this->sma->formatDecimal($currency_rate > 0 ? ($debit * $currency_rate) : $debit);
						$costCenterDebitId[] = json_encode((array) $cost_center);
						$costCenterDebitAm = $cost_center ? $this->sma->formatDecimal($cost_center_amount) : "";
						$costCenterDebitAmount[] = json_encode((array) $costCenterDebitAm);
					} else if ($credit != 0) {

						$entryCreditAccount[] = $account_id;
						$entryCreditBalance[] = $this->sma->formatDecimal($currency_rate > 0 ? ($credit * $currency_rate) : $credit);
						$costCenterCreditId[] = json_encode((array) $cost_center);
						$costCenterCreditAm = $cost_center ? $this->sma->formatDecimal($cost_center_amount) : "";
						$costCenterCreditAmount[] = json_encode((array) $costCenterCreditAm);
					}
				}

				if (empty($accounts)) {
					$this->form_validation->set_rules('product', lang('products'), 'required');
				} else {
					krsort($accounts);
				}




				$data = [
					'openingDate'         => $date,
					'note'         => $note,
					'currency_code'         => $currency_code,
					'currency_rate'         => $currency_rate,
					'userID'   => $this->session->userdata('user_id'),

				];

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            	{
				$data  ['grpId']=$grpId;
                $data  ['coId']=$coId;
                $data  ['brId']=$brId;
				}
				//$this->sma->print_arrays($_POST);
				//$this->sma->print_arrays($accounts);

				$costCenterDebitId2 = [];
				$costCenterDebitAmount2 = [];
				$costCenterCreditId2 = [];
				$costCenterCreditAmount2 = [];

				foreach ($costCenterDebitId as $key => $acc) {
					$costCenterDebitId2[] = json_decode($acc);
				}
				foreach ($costCenterDebitAmount as $key => $acc) {
					$costCenterDebitAmount2[] = json_decode($acc);
				}
				foreach ($costCenterCreditId as $key => $acc) {
					$costCenterCreditId2[] = json_decode($acc);
				}
				foreach ($costCenterCreditAmount as $key => $acc) {
					$costCenterCreditAmount2[] = json_decode($acc);
				}
				if ($costCenterDebitId)
					$costCenterDebitId = json_encode((array) $costCenterDebitId2);
				if ($costCenterDebitAmount)
					$costCenterDebitAmount = json_encode((array) $costCenterDebitAmount2);
				if ($costCenterCreditId)
					$costCenterCreditId = json_encode((array) $costCenterCreditId2);
				if ($costCenterCreditAmount)
					$costCenterCreditAmount = json_encode((array) $costCenterCreditAmount2);

					if($costCenterDebitId=='[[""]]'){
						$costCenterDebitId='[""]';
					}
					if($costCenterCreditId=='[[""]]'){
						$costCenterCreditId='[""]';
					}
					if($costCenterDebitAmount=='[[""]]'){
						$costCenterDebitAmount='[""]';
					}
					if($costCenterCreditAmount=='[[""]]'){
						$costCenterCreditAmount='[""]';
					}
					//$this->sma->print_arrays($costCenterDebitId,$costCenterCreditId,$costCenterDebitAmount,$costCenterCreditAmount);
			}





			if ($this->form_validation->run() == true && $this->accounts_model->addOpeningBalance($data, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId, $costCenterCreditId, $costCenterDebitAmount, $costCenterCreditAmount,$grpId,$coId,$brId)) {


				$this->session->set_userdata('remove_obls', 1);
				$this->session->set_flashdata('message', lang('Openeing_balance_successfully_added'));
				admin_redirect('accounts/opening_balance');
			} else {
				if ($all_accounts == 'all') {
					$opening_balance = $this->accounts_model->getAllAccountsForOB($all_brId);
					//$this->sma->print_arrays($opening_balance);
					foreach ($opening_balance as $item) {
						$c = sha1(uniqid(mt_rand(), true));
						//if ($item->counted != $item->expected) {
						//$product     = $this->site->getProductByID($item->product_id);
						$row         = json_decode('{}');
						$row->id     = $item->id;
						$row->accountID   = $item->accountID;
						$accountName   = $item->accountNameEn ? $item->accountNameEn :  $item->accountName;
						$row->accountName   = $this->Settings->user_language == 'arabic' ? $item->accountName :  $accountName;

						$row->accountSide    = $item->accountSide;
						$row->debit   =  0;
						$row->credit   = 0;
						$row->balance    = 0;
						//$row->cost_center    = '';
						$row->cost_center = $item->cost_center ? $item->cost_center : NULL;
						$row->cost_center_amount = $item->cost_center_amount ? $item->cost_center_amount : 0;
						//$row->serial = '';
						$ri          = $this->Settings->item_addition ? $item->id : $c;

						$pr[$ri] = [
							'id' => $c, 'item_id' => $row->id, 'label' => $row->accountName . ' (' . $row->accountID . ')',
							'row'        => $row, 'cost_centers' => $cost_centers,
						];
						$c++;
						//}
					}
				}
			}
			$cost_centers     = $this->accounts_model->getAllNoMastersCostCenters();

			//$this->sma->print_arrays($pr);

			$this->data['opening_balance_items'] = json_encode($pr);
			$this->data['cost_centers'] = json_encode($cost_centers);
			$this->data['currencies'] = $this->site->getAllCurrencies();


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->data['mainGroups'] = $this->site->getMainGroup();
				$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

			}


			$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
			$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Add_opening_balance'), 'page' => lang('Add_opening_balance')], ['link' => '#', 'page' => lang('Add_opening_balance')]];
			$meta                           = ['page_title' => lang('Add_opening_balance'), 'bc' => $bc];
			$this->page_construct('accounts/add_opening_balance', $meta, $this->data);
		}
	}

	public function opening_balance()
	{

		$this->sma->checkPermissions('opening_balance', true);
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Opening_balance'), 'page' => lang('Opening_balance')], ['link' => '#', 'page' => lang('Opening_balance')]];
		$meta                = ['page_title' => lang('Opening_balance'), 'bc' => $bc];
		$this->page_construct('accounts/opening_balance', $meta, $this->data);
	}

	public function entriesRecurring(){
		$period_year = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();

		$max_min_period_year = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($period_year[0]->period_year) : (object)['period_from_date' => (isset($period_year[0]->period_year) ? $period_year[0]->period_year : date('Y')) . '-01-01', 'period_to_date' => (isset($period_year[0]->period_year) ? $period_year[0]->period_year : date('Y')) . '-12-31'];
		$check_period = $this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
		if ($check_period && isset($check_period->period_status) && $check_period->period_status == 1){
			$start_date = $check_period->period_from_date . ' 00:00:00';
			$end_date = $check_period->period_to_date . ' 23:59:59';
		}else{
			$start_date = (isset($max_min_period_year->period_from_date) ? $max_min_period_year->period_from_date : date('Y-01-01')) . ' 00:00:00';
			$end_date = (isset($max_min_period_year->period_to_date) ? $max_min_period_year->period_to_date : date('Y-12-31')) . ' 23:59:59';
		}

		$entryStatus="yes";
		$entryType="Recurring_entry";

		//$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		//		$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		//$this->sma->print_arrays($end_date);

		$entryLists = $this->accounts_model->getEntries(Null, $start_date, $end_date, $entryStatus, $entryType,Null,Null);
		if(!empty($entryLists)){
			foreach($entryLists as $entryList){
				if(empty($entryList->latest_recurring_date) || $entryList->latest_recurring_date == '0000-00-00 00:00:00' || $entryList->latest_recurring_date == '0000-00-00'){
					continue;
				}
				if($entryList->timePeriod=="monthly"){
					$startDate = new DateTime($entryList->latest_recurring_date);
    	        	$months = $this->getMonthStartsBetween($startDate->format("Y-m-d"));
					if(!empty($months)){
						foreach($months as $month){
							$clause = ['created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => lang('Recurring_entry').' - '.$entryList->entryDescription, 'entryDate' => $month['start'].' 00:00:00', 'entryType' => 'Adjusting', 'entryDebitAccount' => $entryList->entryDebitAccount, 'entryDebitBalance' => $entryList->entryDebitBalance, 'entryCreditAccount' => $entryList->entryCreditAccount, 'entryCreditBalance' => $entryList->entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $entryList->costCenterDebitId, 'costCenterDebitAmount' => $entryList->costCenterDebitAmount, 'costCenterCreditId' => $entryList->costCenterCreditId, 'costCenterCreditAmount' => $entryList->costCenterCreditAmount, 'entryDebitDescription' => $entryList->entryDebitDescription, 'entryCreditDescription' => $entryList->entryCreditDescription];
							if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                				$clause  ['grpId']=$entryList->grpId;
		                		$clause  ['coId']=$entryList->coId;
	        		        	$clause  ['brId']=$entryList->brId;
    	            			$clause  ['entry_no']=$this->site->getReference('gl_enrty',$entryList->brId);
        	    			}
							$this->accounts_model->createEntryRecurring($clause);
							//$this->sma->print_arrays($entryList,$clause,$month);
						}
						//$this->sma->print_arrays($entryList->entryID,$month['end'].' 23:59:59');
						$this->accounts_model->updateEntryRecurring($entryList->entryID,$month['end'].' 23:59:59');
					}
				}else if($entryList->timePeriod=="weekly"){
					$startDate = new DateTime($entryList->latest_recurring_date);
    	        	$months = $this->getWeekStartsBetween($startDate->format("Y-m-d"));
					if(!empty($months)){
						foreach($months as $month){
							$clause = ['created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => lang('Recurring_entry').' - '.$entryList->entryDescription, 'entryDate' => $month['start'].' 00:00:00', 'entryType' => 'Adjusting', 'entryDebitAccount' => $entryList->entryDebitAccount, 'entryDebitBalance' => $entryList->entryDebitBalance, 'entryCreditAccount' => $entryList->entryCreditAccount, 'entryCreditBalance' => $entryList->entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $entryList->costCenterDebitId, 'costCenterDebitAmount' => $entryList->costCenterDebitAmount, 'costCenterCreditId' => $entryList->costCenterCreditId, 'costCenterCreditAmount' => $entryList->costCenterCreditAmount, 'entryDebitDescription' => $entryList->entryDebitDescription, 'entryCreditDescription' => $entryList->entryCreditDescription];
							if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                				$clause  ['grpId']=$entryList->grpId;
		                		$clause  ['coId']=$entryList->coId;
	        		        	$clause  ['brId']=$entryList->brId;
    	            			$clause  ['entry_no']=$this->site->getReference('gl_enrty',$entryList->brId);
        	    			}
							$this->accounts_model->createEntryRecurring($clause);
							//$this->sma->print_arrays($entryList,$clause,$month);
						}
						//$this->sma->print_arrays($entryList->entryID,$month['end'].' 23:59:59');
						$this->accounts_model->updateEntryRecurring($entryList->entryID,$month['end'].' 23:59:59');
					}
				}else if($entryList->timePeriod=="daily"){
					$startDate = new DateTime($entryList->latest_recurring_date);
    	        	$months = $this->getDaysRangesBetween($startDate->format("Y-m-d"));
					if(!empty($months)){
						foreach($months as $month){
							$clause = ['created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => lang('Recurring_entry').' - '.$entryList->entryDescription, 'entryDate' => $month['start'].' 00:00:00', 'entryType' => 'Adjusting', 'entryDebitAccount' => $entryList->entryDebitAccount, 'entryDebitBalance' => $entryList->entryDebitBalance, 'entryCreditAccount' => $entryList->entryCreditAccount, 'entryCreditBalance' => $entryList->entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $entryList->costCenterDebitId, 'costCenterDebitAmount' => $entryList->costCenterDebitAmount, 'costCenterCreditId' => $entryList->costCenterCreditId, 'costCenterCreditAmount' => $entryList->costCenterCreditAmount, 'entryDebitDescription' => $entryList->entryDebitDescription, 'entryCreditDescription' => $entryList->entryCreditDescription];
							if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                				$clause  ['grpId']=$entryList->grpId;
		                		$clause  ['coId']=$entryList->coId;
	        		        	$clause  ['brId']=$entryList->brId;
    	            			$clause  ['entry_no']=$this->site->getReference('gl_enrty',$entryList->brId);
        	    			}
							$this->accounts_model->createEntryRecurring($clause);
							//$this->sma->print_arrays($entryList,$clause,$month);
						}
						//$this->sma->print_arrays($entryList->entryID,$month['end'].' 23:59:59');
						$this->accounts_model->updateEntryRecurring($entryList->entryID,$month['end']);
					}
				}
			}
		}
	}

	public function entries($entryID = NULL)
	{

		$this->sma->checkPermissions('entry_index', true);

		$this->entriesRecurring();

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYearFromEntries();

		$period_year     = $this->input->post('period_year') ? $this->input->post('period_year') : (isset($this->data['period_year'][0]->period_year) ? $this->data['period_year'][0]->period_year : date('Y'));
		$this->data['selected_year'] = $period_year;
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		// $this->data['entryList'] = $this->accounts_model->getEntries(NULL, NULL, NULL, NULL, NULL, false, $period_year);
		$accounts_gl_result = $this->accounts_model->getAccounts(null, null, "id,accountName,accountNameEN");
		$accounts_gl = [];
		if (!empty($accounts_gl_result)) {
			foreach ($accounts_gl_result as $key => $items) {
				$accounts_gl[$items->id] = ($this->Settings->user_language == 'arabic' || empty($items->accountNameEN)) ? $items->accountName : $items->accountNameEN;
			}
		}

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

		$this->data['accounts_gl'] = $accounts_gl;


		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Entries'), 'page' => lang('Entries')], ['link' => '#', 'page' => lang('Entries')]];
		$meta                = ['page_title' => lang('Entries'), 'bc' => $bc];
		$this->page_construct('accounts/entries_index', $meta, $this->data);
	}

	public function getEntriesDTable()
	{
		$this->load->library('datatables');
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {
		$this->datatables
			->select("
				entryID as entryID1,
				entry_no,
				entryDate,
				entryType,
				entryDescription,
				entryDebitAccount,
				entryDebitBalance,
				entryCreditAccount,
				entryCreditBalance,
				entryStatusComment,
				entryStatus,
				entryID as entryID2

			");
		}else{
			$this->datatables
			->select("
				entryID as entryID1,
				entryID,
				entryDate,
				entryType,
				entryDescription,
				entryDebitAccount,
				entryDebitBalance,
				entryCreditAccount,
				entryCreditBalance,
				entryStatusComment,
				entryStatus,
				entryID as entryID2
			");
		}

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));


		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
		$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;

		if ($start_date) {
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		}

		if ($grpId) {
			$this->datatables->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->datatables->where('coId', $coId);
		}
		if ($brId ) {
			$this->datatables->where('brId', $brId);
		}

		if (isset($_POST['year']) && $_POST['year'] != '')
			$this->datatables->where('Year(entryDate)', $_POST['year']);

		if (!is_null($start_date) && !is_null($end_date)) {
			$this->datatables->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if (isset($_GET['entryType']) && $_GET['entryType'] != ''){
			if($_GET['entryType']=='Automated_Accounting_Entry')
				$this->datatables->where('entryDescription', $_GET['entryType']);
			else if($_GET['entryType']=='Adjusting' || $_GET['entryType']=='Recurring_entry')
				$this->datatables->where('entryType', $_GET['entryType']);
		}

		if (isset($_GET['entryStatus']) && $_GET['entryStatus'] != '')
			$this->datatables->where('entryStatus', $_GET['entryStatus']);

		if (isset($_GET['entryID']) && $_GET['entryID'] != '')
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {
			$this->datatables->where('entryID', $_GET['entryID'])->or_where('entry_no', $_GET['entryID']);
		}else{
			$this->datatables->where('entryID', $_GET['entryID']);
		}


		$this->datatables->from('gl_entries');
		//$this->sma->print_arrays($this->datatables->generate());
		echo $this->datatables->generate();
	}

	public function getopeningbalance($warehouse_id = null)
	{
		//$this->sma->checkPermissions('adjustments');

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

		/*$delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_opening_balance') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('accounts/delete_opening_balance/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";
			*/
		$delete_link = "";

		$this->load->library('datatables');
		$this->datatables
			->select("{$this->db->dbprefix('gl_opening_balance')}.id as id, openingDate, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by, note")
			->from('gl_opening_balance')
			//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
			->join('users', 'users.id=gl_opening_balance.userID', 'left')
			->group_by('gl_opening_balance.id');


			if ($grpId) {
                $this->datatables->where($this->db->dbprefix('gl_opening_balance') . '.grpId', $grpId);
            }
            if ($coId) {
              $this->datatables->where($this->db->dbprefix('gl_opening_balance') . '.coId', $coId);
            }
            if ($brId) {
                $this->datatables->where($this->db->dbprefix('gl_opening_balance') . '.brId', $brId);
            }

		//$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('products/edit_adjustment/$1') . "' class='tip' title='" . lang('edit_adjustment') . "'><i class='fa fa-edit'></i></a> " . $delete_link . '</div>', 'id');
		$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('accounts/edit_opening_balance/$1') . "' class='tip' title='" . lang('Edit_opening_balance') . "'><i class='fa fa-edit'></i></a>  " . $delete_link . "</div>", 'id');


		echo $this->datatables->generate();
	}

	public function view_opening_balance($id)
	{
		$this->sma->checkPermissions('view_opening_balance', true);



		$accounts_model = $this->accounts_model->getOpeningBalanceByID($id);
		if (!$id || !$accounts_model) {
			$this->session->set_flashdata('error', lang('Opening_balance_not_found'));
			$this->sma->md();
		}

		$this->data['inv']        = $accounts_model;
		$this->data['rows']       = $this->accounts_model->getOpeningBalanceItems($id);

		//$this->sma->print_arrays($this->data['rows']);
		$this->data['created_by'] = $this->site->getUser($accounts_model->userID);
		$this->load->view($this->theme . 'accounts/view_opening_balance', $this->data);
	}

	public function view_entries($id)
	{

		$this->sma->checkPermissions('view_entries', true);

		$accounts_model = $this->accounts_model->getEntryByEntryID($id);
		if (!$id || !$accounts_model) {
			$this->session->set_flashdata('error', lang('Entry_not_found'));
			$this->sma->md();
		}

		//$this->sma->print_arrays($accounts_model);
		/*$this->sma->print_arrays($accounts_model->entryDebitAccount);
		$entryList=$accounts_model->entryDebitAccount
		$entryList=str_replace('"]',"",$entryList);
		$entryList=str_replace('["',"",$entryList);
		$entryList=str_replace('"',"",$entryList);


		$entryList=explode("_",$entryList);
		$entryAccount=$entryList[0];
		$entryAccount=explode(",",$entryAccount);

		$entryBalance=$entryList[1];
		$entryBalance=explode(",",$entryBalance);
		*/
		//$this->sma->print_arrays($accounts_model);




		$this->data['inv']        = $accounts_model;

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['branch'] = $this->site->getMainBranchesByID($accounts_model->brId);
		}
		//$this->data['rows']       = $this->accounts_model->getEntryItems($id);

		//$this->sma->print_arrays($this->data['rows']);
		$this->data['created_by'] = $this->site->getUser($accounts_model->userID);

		$accounts_map = [];
		$all_accounts = $this->accounts_model->getAllDetailsAccounts();
		if (!empty($all_accounts)) {
			foreach ($all_accounts as $acc) {
				$accounts_map[$acc->id] = $acc;
			}
		}
		$this->data['accounts_map'] = $accounts_map;

		$cost_centers_map = [];
		$all_cost_centers = $this->accounts_model->getAllNoMastersCostCenters();
		if (!empty($all_cost_centers)) {
			foreach ($all_cost_centers as $cc) {
				$cost_centers_map[$cc->id] = $cc;
			}
		}
		$this->data['cost_centers_map'] = $cost_centers_map;

		$this->load->view($this->theme . 'accounts/view_entries', $this->data);
	}


	public function qa_suggestions()
	{
		$term = $this->input->get('term', true);
		$brId = $this->input->get('brId', true);

		if (strlen($term) < 1 || !$term) {
			die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
		}

		$analyzed  = $this->sma->analyze_term($term);
		$sr        = $analyzed['term'];
		$option_id = $analyzed['option_id'];

		$rows = $this->accounts_model->getQASuggestions($sr,$brId);
		if ($rows) {
			foreach ($rows as $row) {
				/*$row->qty    = 1;
                $row->cost    = 0;
                $row->value    = 0;
                $row->expiry   = '';
                $options     = $this->products_model->getProductOptions($row->id);
                $row->unit     = $this->products_model->getUnitById($row->unit);
                $row->option = $option_id;
                $row->serial = '';*/
				$c           = sha1(uniqid(mt_rand(), true));

				$row->id     = $row->id;
				$row->accountID   = $row->accountID;
				//$row->accountName   = $this->Settings->user_language == 'arabic' ? $row->accountName :  $row->accountNameEn;
				$row->accountSide    = $row->accountSide;
				$row->debit   =  0;
				$row->credit   = 0;
				$row->balance    = 0;
				$row->cost_center    = '';

				$pr[]        = [
					'id' => $c, 'item_id' => $row->id, 'label' => $row->accountName . ' (' . $row->accountID . ')',
					'row'            => $row,
				];
			}
			$this->sma->send_json($pr);
		} else {
			$this->sma->send_json([['id' => 0, 'label' => lang('Account_no_match_found'), 'value' => $term]]);
		}
	}

	public function modal_view($accountID = null)
	{
		$this->sma->checkPermissions('tree_modal_view', true);

		//$pr_details = $this->site->getProductByID($id);
		$account_details = $this->accounts_model->getAccountByID($accountID);
		if ($account_details->account_type == "warehouses") {
			if (isset($this->Settings->isCars) && $this->Settings->isCars == 1) {
				$this->data['warehouse']   = $this->site->getShowroomByID($account_details->account_ref_id);
			} else {
				$this->data['warehouse']   = $this->site->getWarehouseByID($account_details->account_ref_id);
			}
		}

		if ($account_details->account_type == "sales") {
			$this->data['sales']   = $this->site->getWarehouseByID($account_details->account_ref_id);
		}

		if ($account_details->account_type == "return_sales") {
			$this->data['return_sales']   = $this->site->getWarehouseByID($account_details->account_ref_id);
		}


		if ($account_details->account_type == "local_suppliers") {
			$this->data['local_suppliers']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}
		if ($account_details->account_type == "international_suppliers") {
			$this->data['international_suppliers']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}

		if ($account_details->account_type == "local_customers") {
			$this->data['local_customers']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}

		if ($account_details->account_type == "international_customers") {
			$this->data['international_customers']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}
		if ($account_details->account_type == "billers") {
			$this->data['billers']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}

		if ($account_details->account_type == "insurance") {
			$this->data['insurance']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}

		if ($account_details->account_type == "advance") {
			$this->data['advance']   = $this->site->getCompanyByID($account_details->account_ref_id);
		}





		if (!$accountID || !$account_details) {
			$this->session->set_flashdata('error', lang('Acount_not_found'));
			$this->sma->md();
		}

		//$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();


		$this->data['account_details']       = $account_details;
		//$this->sma->print_arrays($this->data);

		$this->load->view($this->theme . 'accounts/modal_view', $this->data);
	}


	public function cost_centers_view($costID = null)
	{
		$this->sma->checkPermissions('cost_centers_view', true);

		//$pr_details = $this->site->getProductByID($id);
		$cost_details = $this->accounts_model->getCostCentersByID($costID);


		if (!$costID || !$cost_details) {
			$this->session->set_flashdata('error', lang('Acount_not_found'));
			$this->sma->md();
		}

		$this->data['cost_details']       = $cost_details;

		$this->load->view($this->theme . 'accounts/cost_centers_view', $this->data);
	}


	public function view_payment_types($id = null)
	{
		$this->sma->checkPermissions('view_payment_types', true);

		//$pr_details = $this->site->getProductByID($id);
		$payment_details = $this->accounts_model->getPaymentTypeViewPaymentAccountByID($id);


		if (!$id || !$payment_details) {
			$this->session->set_flashdata('error', lang('Acount_not_found'));
			$this->sma->md();
		}

		$this->data['payment_details']       = $payment_details;
		$this->data['paymentType'] = $this->site->getpaymentType(1, $payment_details->payment_cc_id);
		//$this->sma->print_arrays($this->data['paymentType']);

		$this->data['Commisson_payment_details']       = $this->accounts_model->getPaymentTypeViewCommissonAccountByID($id);
		$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();
		$this->data['billers']  = $this->site->getAllCompanies('biller');


		$this->load->view($this->theme . 'accounts/view_payment_types', $this->data);
	}

	public function view_accounting_period($id = null)
	{
		$this->sma->checkPermissions('accounting_period_index');

		//$pr_details = $this->site->getProductByID($id);
		$accounting_period_details = $this->accounts_model->getAccountingPeriodByID($id);


		if (!$id || !$accounting_period_details) {
			$this->session->set_flashdata('error', lang('Acount_not_found'));
			$this->sma->md();
		}

		$this->data['accounting_period_details']       = $accounting_period_details;


		$this->load->view($this->theme . 'accounts/view_accounting_period', $this->data);
	}

	/*public function delete_opening_balance($id = null)
	{
		//$this->sma->checkPermissions('delete', true);

		if ($this->accounts_model->deleteOpeningBalance($id)) {
			$this->sma->send_json(['error' => 0, 'msg' => lang('Opening_balance_successfully_deleted')]);
		}
	}*/


	public function receipts()
	{
		$this->sma->checkPermissions('receipts_index', true);
		/*if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			/*if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }*/

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
			$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Receipts'), 'page' => lang('Receipts')], ['link' => '#', 'page' => lang('Receipts')]];
			$meta                = ['page_title' => lang('Receipts'), 'bc' => $bc];
			$this->page_construct('accounts/receipts', $meta, $this->data);
		}
	}

	public function catch_receipts()
	{
		$this->sma->checkPermissions('catch_receipts_index', true);
		/*if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			/*if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }*/

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

			$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_settings   = $this->accounts_model->getSettings();
			$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
			$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Catch_receipts'), 'page' => lang('Catch_receipts')], ['link' => '#', 'page' => lang('Catch_receipts')]];
			$meta                = ['page_title' => lang('Catch_receipts'), 'bc' => $bc];
			$this->page_construct('accounts/catch_receipts', $meta, $this->data);
		}
	}

	public function refunding_bonds()
	{
		$this->sma->checkPermissions('refunding_bonds_index', true);
		/*if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			/*if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }*/

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Refunding_bonds'), 'page' => lang('Refunding_bonds')], ['link' => '#', 'page' => lang('Refunding_bonds')]];
			$meta                = ['page_title' => lang('Refunding_bonds'), 'bc' => $bc];
			$this->page_construct('accounts/refunding_bonds', $meta, $this->data);
		}
	}


	public function accountsguide($pdf = null, $xls = null)
	{
		$this->sma->checkPermissions('accounts_tree', true);
		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$this->data['accounts'] = $this->accounts_model->getAllMastersAccounts();
		//$this->data['accounts'] = $this->accounts_model->getAccounts();
		if ($pdf || $xls) {

			$levels = $this->input->get('levels') ? $this->input->get('levels') : NULL;



			$accounts = $this->data['accounts'];



			//$this->sma->print_arrays($levels);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Accounts_guide'));

			$this->excel->getActiveSheet()->mergeCells('A1:D1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Accounts_guide'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 25,
					'name'  => 'Cairo-Bold'
				)

			);
			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dbdee0')
				)
			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




			$this->excel->getActiveSheet()->mergeCells('A2:D2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

			$this->excel->getActiveSheet()->getStyle('A1' . ':D2')->applyFromArray($styleArray1);

			$row = 4;

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Account_code'));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('Account_name'));
			$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('English_name'));
			$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang('Account_type'));
			$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);


			$this->excel->getActiveSheet()->getStyle('A' . $row . ':D' . $row)->applyFromArray($styleArray1);



			$row++;












			if (isset($_GET['account']) && $_GET['account'] != '') {

				$accountChilds = $this->accounts_model->getAccounts($_GET['account']);
				$accountChilds = (array) $accountChilds[0];

				$accountList = $this->accounts_model->getAccountChildsGuide($accountChilds['accountID'], $levels);


				//$accountList=$this->accounts_model->getAccounts($_GET['account'],$levels);
			} else {
				$accountList = $this->accounts_model->getAccounts(null, $levels);
			}



			$accounts = array();

			foreach ($accountList as $account) {
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])) {
					$accounts[$account['accountCategory']] = array($account);
				} else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}
			//$this->sma->print_arrays($accounts);


			$accountCategories = array_keys($accounts);
			$accountOrder = 0;
			foreach ($accounts as $accountCategory) {
				$moneySign = '';
				$accountSubCategory = NULL;
				$subTotal = 0;




				foreach ($accountCategory as $account) {

					//$accountChilds = $this->accounts_model->getAccounts($account['id']);
					//$accountChilds_levels=$accountChilds;

					//$accountChilds = (array) $accountChilds[0];



					$accountStatement = "";
					if ($account['accountStatement'] === "Income Statement")
						$accountStatement = lang('Income_Statement');
					else
						$accountStatement = lang('Balance_Sheet');

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $account['accountID']);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, $account['accountName']);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, ($account['accountNameEn'] ? $account['accountNameEn'] : $account['accountName']));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(11);

					if ($account['level'] == 1) {
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $account['accountCategory']);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);
					} else {
						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $accountStatement . ' - ' . $account['accountCategory']);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);
					}

					$row++;
				}

				/*else{
					if ($equityTotal != abs($equityTotal)){
						$accountAmount = '('.number_format(abs($equityTotal), 0).')';
					}
					else {
						$accountAmount = number_format($equityTotal, 0);
					}
					echo '
					<tr>
					<td class="text-center"><strong>Total '.$accountCategories[$accountOrder].'</strong></td>
					<td class="text-right" style="text-decoration: underline; border-top: 2px solid #000000;"><strong>TK&nbsp;&nbsp;&nbsp;'.$accountAmount.'</strong></td>
					</tr>
					';
				}*/
				$accountOrder += 1;
			}

			//		if(isset($_GET['account']) ){
			$accountList = $this->accounts_model->getAccounts($_GET['account']);
			//}
















			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);








			$filename = 'Accounts_guide';
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {

			//$this->sma->print_arrays($this->Settings->user_language);
			$bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Accounts_guide')]];
			$meta = ['page_title' => lang('Accounts_guide'), 'bc' => $bc];
			$this->page_construct('accounts/accountsguide', $meta, $this->data);
		}
	}

	public function ledgers($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		$this->sma->checkPermissions('ledgers', true);
		ini_set('max_execution_time', 300);

		$this->data['accounts'] = $this->accounts_model->getAccounts();



		/*if ($id){
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);

			}
			else {
				$this->data['accountList'] = $this->accounts_model->getAccounts();
			}
			*/
		//$data['userData']   = $this->session->userdata();
		if ($pdf || $xls) {



			$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');


			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				$this->data['prev_entryList']  = $this->accounts_model->getEntriesApproved(NULL, "1", $prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $grpId, $coId, $brId);
				$prev_entryList = $this->data['prev_entryList'];

				asort($prev_entryList);
			}


			$this->data['entryList']  = $this->accounts_model->getEntriesApproved(NULL, "1", $start_date, $end_date, $grpId, $coId, $brId);

			$entryList = $this->data['entryList'];

			asort($entryList);

			//$this->sma->print_arrays($this->data['prev_entryList']);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('List_of_ledgers'));

			$this->excel->getActiveSheet()->mergeCells('A1:G1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('List_of_ledgers'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 25,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);




			$this->excel->getActiveSheet()->mergeCells('A2:G2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$row = 3;
			if($brId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_branch').' : '.($this->site->getMainBranchesByID($brId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                       // $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                }

			$this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
			$this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Ledgers') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);



			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dbdee0')
				)
			);

			/*$styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
	  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArray1);


			$row++;











			//		if(isset($_GET['account']) ){
			$accountList = $this->accounts_model->getAccounts($_GET['account']);
			//}








			foreach ($accountList as $account) {

				$account = (array) $account;
				$accountEntries = array();
				$prev_accountEntries = array();


				$id      = $account['id'];
				$accountBalance = $account['accountDebit'] - $account['accountCredit'];






				foreach ($entryList as $entry) {
					$entry = (array) $entry;
					if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))) {
						array_push($accountEntries, $entry);
					}
				}
				//
				$accountName == "";
				//if (!empty($accountEntries))

				{
					if ($this->Settings->user_language == 'arabic' || $account['accountNameEn'] == '') {
						$accountName = $account['accountName'];
					} else {
						$accountName = $account['accountNameEn'];
					}
					$this->excel->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $accountName);
					//$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);


					$row++;
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Sr#'));
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("Entry_number"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("date"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("reference_no"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Detailed_account"));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang('Debit') . ' (' . $this->Settings->symbol . ')');
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('G' . $row, lang('Credit') . ' (' . $this->Settings->symbol . ')');
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($styleArray1);


					$row++;



					$balanceDebit = 0;
					$balanceCredit = 0;




					foreach ($prev_entryList as $prev_entry) {
						$prev_entry = (array) $prev_entry;
						//
						if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))) {
							array_push($prev_accountEntries, $prev_entry);
						}
					}








					//if (!empty($prev_accountEntries))
					{




						$prev_fbalance = 0;

						$sr=0;
						foreach ($prev_accountEntries as $entry) {
							$entry = (array) $entry;

							$entryDebitAccounts = array();
							$entryDebitBalances = 0;

							$entryCreditAccounts = array();
							$entryCreditBalances = 0;

							foreach (json_decode($entry['entryDebitAccount']) as $debitAccount) {
								array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
								$entryDebitBalances += 1;
							}

							foreach (json_decode($entry['entryCreditAccount']) as $creditAccount) {
								array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
								$entryCreditBalances += 1;
							}





							foreach ($entryDebitAccounts as $account) {
								$balance = $account[key($account)];
								$account = key($account);
								$accountInfo = $this->accounts_model->getAccountByID($account);
								//print_r($accountInfo->id);

								//$accountInfo = $this->entry_model->getAccount($account);

								$x = 0;
								if ($accountInfo->id == $id) {
									$prev_fbalance = $prev_fbalance + $balance;


									//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



									$x++;
								}
							}
							foreach ($entryCreditAccounts as $account) {
								$balance = $account[key($account)];
								$account = key($account);
								$accountInfo = $this->accounts_model->getAccountByID($account);

								$y = 0;
								if ($accountInfo->id == $id) {
									$prev_fbalance = $prev_fbalance - $balance;




									$y++;
								}
							}
						}
					}
					//}
					//echo $prev_fbalance;






					if ($prev_fbalance) {
						$sr++;
						$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);



					$this->excel->getActiveSheet()->SetCellValue('B' . $row, '');
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);

						$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('Carry-over_balance') . ' ' . lang('Before') . ' ' . (isset($_GET['start_date']) ? $_GET['start_date'] : ''));

						$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(12);




						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(12);


						$this->excel->getActiveSheet()->SetCellValue('E' . $row, '');
						$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(11);

						if ($prev_fbalance >= 0) {

							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($prev_fbalance));

							$balanceDebit = $balanceDebit + $prev_fbalance;
							$fbalance = $fbalance + $prev_fbalance;
						}


						$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);


						if ($prev_fbalance < 0) {
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($prev_fbalance)));
							$balanceCredit = $balanceCredit - $prev_fbalance;
							$fbalance = $fbalance + $prev_fbalance;
						}

						$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
						$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);

						$row++;
					}






					foreach ($accountEntries as $entry) {


						$entry = (array) $entry;

						$entryDebitAccounts = array();
						$entryDebitBalances = 0;

						$entryCreditAccounts = array();
						$entryCreditBalances = 0;

						foreach (json_decode($entry['entryDebitAccount']) as $debitAccount) {
							array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
							$entryDebitBalances += 1;
						}

						foreach (json_decode($entry['entryCreditAccount']) as $creditAccount) {
							array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
							$entryCreditBalances += 1;
						}

						$sr++;
						$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
								if(empty($entry['entry_no'])){
									$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entryID']);
								}else{
									$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entry_no']);
								}

						$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrsd($entry['entryDate']));

						$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(11);

						$this->excel->getActiveSheet()->SetCellValue('D' . $row, $entry['reference_no']);
						//$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entryID']);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);

						$entryDescriptionText = $entry['entryDescription'];

									$entryDescription = str_replace("<p>", "", ($entryDescriptionText) . "");
									$entryDescription = str_replace("</p>", "", ($entryDescription) . "");
									$iof = strpos(trim($entryDescription), "#");
									if ($iof > -1)
										$entryDescription = lang(substr($entryDescription, 0, $iof)) . " " . substr($entryDescription, $iof + 1);


									$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang($entryDescription));

						/*foreach ($entryDebitAccounts as $account) {
							$balance = $account[key($account)];
							$account = key($account);
							$accountInfo = $this->accounts_model->getAccountByID($account);
							//print_r($accountInfo->id);

							//$accountInfo = $this->entry_model->getAccount($account);

							$x = 0;
							if ($accountInfo->id == $id) {
								$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal($balance));

								$balanceDebit = $balanceDebit + $balance;


								//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



								$x++;
							}
						}*/

						$Dbalance_temp = "";

								foreach ($entryDebitAccounts as $account) {
									$balance = $account[key($account)];
									$account = key($account);
									$accountInfo = $this->accounts_model->getAccountByID($account);
									//print_r($accountInfo->id);



									$x = 0;
									if ($accountInfo->id == $id) {
										$Dbalance_temp = $Dbalance_temp . "" . $this->sma->formatDecimal($balance) . "\n";
										$this->excel->getActiveSheet()->SetCellValue('F' . $row, $Dbalance_temp);
										$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);
										//echo '<strong>'.$this->sma->formatDecimal($balance).'</strong><br />';

										$balanceDebit = $balanceDebit + $balance;




										$x++;
									}
								}







						$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(11);
						//$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);


						/*foreach ($entryCreditAccounts as $account) {
							$balance = $account[key($account)];
							$account = key($account);
							$accountInfo = $this->accounts_model->getAccountByID($account);

							$y = 0;
							if ($accountInfo->id == $id) {
								$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal(abs($balance)));

								$balanceCredit = $balanceCredit + $balance;



								$y++;
							}
						}*/

						$Cbalance_temp = "";
						foreach ($entryCreditAccounts as $account) {
							$balance = $account[key($account)];
							$account = key($account);
							$accountInfo = $this->accounts_model->getAccountByID($account);

							$y = 0;
							if ($accountInfo->id == $id) {
								$Cbalance_temp = $Cbalance_temp . "" . $this->sma->formatDecimal($balance) . "\n";
								$this->excel->getActiveSheet()->SetCellValue('G' . $row, $Cbalance_temp);
								$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
								//$this->excel->getActiveSheet()->SetCellValue('F'.$row,$this->sma->formatDecimal($Cbalance_temp));
								//echo '<strong>'.$this->sma->formatDecimal($balance).'</strong><br />';
								$balanceCredit = $balanceCredit + $balance;






								$y++;
							}
						}









						$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
						//$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);

						$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$row++;
					}




					$total = 0;
					$diffDebit = 0;
					$diffCredit = 0;
					if ($balanceDebit > $balanceCredit) {
						$total = $balanceDebit;
						$diffDebit = ($balanceDebit - $balanceCredit);
					} else {
						$total = $balanceCredit;
						if ($balanceCredit > $balanceDebit)
							$diffCredit = ($balanceCredit - $balanceDebit);
					}


					if ($diffDebit != 0 || $diffCredit != 0) {
						if ($diffDebit != 0)
							$diffDebit = $this->sma->formatDecimal($diffDebit);
						else
							$diffDebit = '';

						if ($diffCredit != 0)
							$diffCredit = $this->sma->formatDecimal($diffCredit);
						else
							$diffCredit = '';
						$this->excel->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);

						$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("total"));
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

						if ($diffDebit != '') {
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getBorders()
								->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($total));
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
						} else {
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getBorders()
								->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($total));
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
						}



						$row++;


						$this->excel->getActiveSheet()->mergeCells('A' . $row . ':E' . $row);
						$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("balance"));
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);



						if ($diffDebit != '') {

							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $diffDebit);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);



							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $diffDebit);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
						} else {
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $diffCredit);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $diffCredit);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(12);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
						}
					}
					$row++;
				}
			}








			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);







			$filename = lang('List_of_ledgers');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$start_date     = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date       = $this->input->post('end_date') ? $this->input->post('end_date') : null;

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');


			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				$this->data['prev_entryList']  = $this->accounts_model->getEntriesApproved(NULL, "1", $prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $grpId, $coId, $brId);
			}


			$this->data['entryList']  = $this->accounts_model->getEntriesApproved(NULL, "1", $start_date, $end_date, $grpId, $coId, $brId);
			//$this->data['filter_id']  = $id;

			//$this->sma->print_arrays($this->data['accounts'],$start_date,$end_date);


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Ledgers'), 'page' => lang('Ledgers')], ['link' => '#', 'page' => lang('Ledgers')]];
			$meta                = ['page_title' => lang('Ledgers'), 'bc' => $bc];
			/*if ($id)
		$this->page_construct('accounts/ledgers', $meta, $this->data);
		else*/
			$this->page_construct('accounts/ledgers', $meta, $this->data);
		}
	}

	public function reports($id = NULL)
	{

		$this->data['accountList'] = $this->accounts_model->getAccounts();

		//$data['userData']   = $this->session->userdata();
		$this->data['entryList']  = $this->accounts_model->getEntriesApproved();
		$this->data['filter_id']  = $id;

		//$this->sma->print_arrays($this->data['filter_id']);


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('reports'), 'page' => lang('reports')], ['link' => '#', 'page' => lang('reports')]];
		$meta                = ['page_title' => lang('reports'), 'bc' => $bc];
		$this->page_construct('accounts/reports_list', $meta, $this->data);
	}

	public function trialbalanceintotal()
	{

		$this->data['accountList'] = $this->accounts_model->getAccounts();
		//$this->data['accountList1'] = $this->accounts_model->getAccountItem();
		//$data['userData']   = $this->session->userdata();
		//$this->data['entryList']  = $this->accounts_model->getEntries();

		//$this->sma->print_arrays($this->data['accountList'],$this->data['accountList1']);


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Trial_balance_in_total'), 'page' => lang('Trial_balance_in_total')], ['link' => '#', 'page' => lang('Trial_balance_in_total')]];
		$meta                = ['page_title' => lang('Trial_balance_in_total'), 'bc' => $bc];
		$this->page_construct('accounts/trialbalanceintotal', $meta, $this->data);
	}

	public function trialbalance($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{

		$this->sma->checkPermissions('trialbalance', true);
		ini_set('max_execution_time', 300);
		//$this->sma->print_arrays( $this->accounts_model->getAllAccountingPeriodYearFromEntries());

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			//$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		//$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
		if ($_POST)
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->input->post('period_year')) : (object)['period_from_date' => $this->input->post('period_year') . '-01-01', 'period_to_date' => $this->input->post('period_year') . '-12-31'];
		else
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];



		$levels    = $this->input->post('levels') ? $this->input->post('levels') : 0;

		if ($levels == 0)
			$this->data['accountList'] = $this->accounts_model->getAccountsV2();
		else
			$this->data['accountList'] = $this->accounts_model->getAccountsV3(NULL, $levels);

		//$this->data['accountList'] = $this->accounts_model->getAccounts();

		//$this->data['accountList'] = $this->accounts_model->getAccountsNotZeros();
		//$data['userData']   = $this->session->userdata();
		//$this->data['entryList']  = $this->accounts_model->getEntries();

		//$this->data['accountList1'] = $this->accounts_model->getAccountItemByLevel();
		//$data['userData']   = $this->session->userdata();
		//$this->data['entryList']  = $this->accounts_model->getEntries();
		/*$accounts = array();

			foreach ($this->data['accountList1'] as $account){
				$this->sma->print_arrays($account);
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])){
					$accounts[$account['accountCategory']] = array($account);
				}
				else {
					array_push($accounts[$account['accountCategory']], $account);
				}

			}

			*/
		//$this->sma->print_arrays($this->data['accountList']);
		//$this->sma->print_arrays($this->data['accountList'],$this->data['accountList1']);
		//$this->sma->print_arrays($POST);

		if ($pdf || $xls) {
			//$this->sma->print_arrays($_GET);
			$levels    = $this->input->get('levels') ? $this->input->get('levels') : 0;


			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			//$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));


			if ($levels == 0)
				$this->data['accountList'] = $this->accounts_model->getAccountsV2();
			else
				$this->data['accountList'] = $this->accounts_model->getAccountsV3(NULL, $levels);


			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Trial_balance'));

			$this->excel->getActiveSheet()->mergeCells('A1:I1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Trial_balance'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 25,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(25);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);





			$this->excel->getActiveSheet()->mergeCells('A2:I2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);


			$row = 3;


			if($grpId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_group').' : '.($this->site->getMainGroupsByID($grpId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        //$this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                        }

                        if($coId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_company').' : '.($this->site->getMainCompaniesByID($coId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        //$this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                         $row++;
                        }

                        if($brId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_branch').' : '.($this->site->getMainBranchesByID($brId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                       // $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                        }


						$this->excel->getActiveSheet()->mergeCells('A'.$row.':I'.$row);
			$this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Trial_balance') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

			$row++;
			$row++;
			$this->excel->getActiveSheet()->mergeCells('A'.$row.':A'.($row+1));
			$this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Account'));
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

			if ($_GET['show_Opening_balance'] && $_GET['show_Opening_balance'] != '0') {
				$this->excel->getActiveSheet()->mergeCells('B'.$row.':C'.$row);
				$this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('Opening_balance'));
				$this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);
			}

			$this->excel->getActiveSheet()->mergeCells('D'.$row.':E'.$row);
			$this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('Movement'));
			$this->excel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->mergeCells('F'.$row.':G'.$row);
			$this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Net_Movement'));
			$this->excel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);

			if ($_GET['show_Closing_balance'] && $_GET['show_Closing_balance'] != '0') {
				$this->excel->getActiveSheet()->mergeCells('H'.$row.':I'.$row);
				$this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('Closing_balance'));
				$this->excel->getActiveSheet()->getStyle('H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);
			}
			$row++;

			if ($_GET['show_Opening_balance'] && $_GET['show_Opening_balance'] != '0') {
				$this->excel->getActiveSheet()->SetCellValue('B'.$row, lang('Debit') . ' (' . $this->Settings->symbol . ')');
				$this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('B'.$row)->getFont()->setBold(true);

				$this->excel->getActiveSheet()->SetCellValue('C'.$row, lang('Credit') . ' (' . $this->Settings->symbol . ')');
				$this->excel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('C'.$row)->getFont()->setBold(true);
			}

			$this->excel->getActiveSheet()->SetCellValue('D'.$row, lang('Debit') . ' (' . $this->Settings->symbol . ')');
			$this->excel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('D'.$row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->SetCellValue('E'.$row, lang('Credit') . ' (' . $this->Settings->symbol . ')');
			$this->excel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('E'.$row)->getFont()->setBold(true);


			$this->excel->getActiveSheet()->SetCellValue('F'.$row, lang('Debit') . ' (' . $this->Settings->symbol . ')');
			$this->excel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('F'.$row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->SetCellValue('G'.$row, lang('Credit') . ' (' . $this->Settings->symbol . ')');
			$this->excel->getActiveSheet()->getStyle('G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);

			if ($_GET['show_Closing_balance'] && $_GET['show_Closing_balance'] != '0') {

				$this->excel->getActiveSheet()->SetCellValue('H'.$row, lang('Debit') . ' (' . $this->Settings->symbol . ')');
				$this->excel->getActiveSheet()->getStyle('H'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('H'.$row)->getFont()->setBold(true);

				$this->excel->getActiveSheet()->SetCellValue('I'.$row, lang('Credit') . ' (' . $this->Settings->symbol . ')');
				$this->excel->getActiveSheet()->getStyle('I'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('I'.$row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('I'.$row)->getFont()->setBold(true);
			}

			if ($_GET['start_date']) {
				$ts1 = strtotime($this->sma->fld($_GET['start_date']));
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';
				$prev_end_date = date('Y-m-d', strtotime('-1 days', strtotime($this->sma->fld($_GET['start_date'])))) . ' 23:59:58';
			}


			$row++;
			$totalDebitAccount  = 0;
			$totalCreditAccount = 0;
			$totalDebitAccountOB  = 0;
			$totalCreditAccountOB = 0;
			$totalDebitAccountM  = 0;
			$totalCreditAccountM = 0;
			$totalDebitAccountNM  = 0;
			$totalCreditAccountNM = 0;
			$totalDebitAccountCB  = 0;
			$totalCreditAccountCB = 0;



			$accountList = $this->data['accountList'];
			$accounts = array();
			$accountsV2 = array();

			//asort($accountList);




			if (!empty($_GET)){
				$accountTotalV2 = $this->accounts_model->getAccountTotalDebitCreditV2((isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);
			}
			else {
				$accountTotalV2 = $this->accounts_model->getAccountTotalDebitCreditV2((isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);
			}

			if($prev_start_date && $_GET['show_Opening_balance'] && $_GET['show_Opening_balance']!='0'){
				if (!empty($_GET)){
					$prev_accountTotalV2 = $this->accounts_model->getAccountTotalDebitCreditV2($prev_start_date,(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);
				}
				else {
					$prev_accountTotalV2 = $this->accounts_model->getAccountTotalDebitCreditV2($prev_start_date,(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);
				}
			}else{
				$prev_accountTotalV2 =$accountTotalV2;
			}

			foreach ($accountList as $account){
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])){
					$accounts[$account['accountCategory']] = array($account);
				}
				else {
					foreach($prev_accountTotalV2 as $entry){
						$entry = (array) $entry;
						if($levels==0){
							if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
								if (!in_array($account['id'], $accountsV2)){
									array_push($accounts[$account['accountCategory']], $account);
									array_push($accountsV2, $account['id']);
								}
							}
						}else{
							if (!in_array($account['id'], $accountsV2)){
								array_push($accounts[$account['accountCategory']], $account);
								array_push($accountsV2, $account['id']);
							}
						}
					}

				}
			}

			$accountCategories = array_keys($accounts);
			$accountOrder = 0;
			foreach ($accounts as $accountCategory) {
				if ($this->Settings->user_language == 'arabic'){
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $accountCategories[$accountOrder]);
				}else{
					$categoryNameEn=$accountCategories[$accountOrder];
					if($accountCategories[$accountOrder]=='اصول'){
						$categoryNameEn='Assets';
					}
					if($accountCategories[$accountOrder]=='الإلتزامات وحقوق الملكية'){
						$categoryNameEn='Liabilities &Equity Owners';
					}
					if($accountCategories[$accountOrder]=='المصروفات'){
						$categoryNameEn='Expenses';
					}
					if($accountCategories[$accountOrder]=='الإيرادات'){
						$categoryNameEn='Revenues';
					}
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $categoryNameEn);
				}
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

				if ($_GET['show_Opening_balance'] && $_GET['show_Opening_balance'] != '0') {
					$this->excel->getActiveSheet()->SetCellValue('B' . $row, '');
					$this->excel->getActiveSheet()->SetCellValue('C' . $row, '');
				}
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, '');

				$this->excel->getActiveSheet()->SetCellValue('F' . $row, '');
				$this->excel->getActiveSheet()->SetCellValue('G' . $row, '');

				if ($_GET['show_Closing_balance'] && $_GET['show_Closing_balance'] != '0') {
					$this->excel->getActiveSheet()->SetCellValue('H' . $row, '');
					$this->excel->getActiveSheet()->SetCellValue('I' . $row, '');
				}


				$accountOrder += 1;

				$categoryCount = 0;
				$textDecor = 'none';

				$moneySign = 'TK&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

				foreach ($accountCategory as $account){
					//echo $account['accountID'];
					$categoryCount++;
					if ($categoryCount == count($accountCategory)){
						//$textDecor = 'underline';
					}




					if (!empty($_GET)){
						$accountTotal = $this->accounts_model->getAccountTotalDebitCreditV3($account['id'],$accountTotalV2);
					}
					else {
						$accountTotal = $this->accounts_model->getAccountTotalDebitCreditV3($account['id'],$accountTotalV2);
					}

					//$this->sma->print_arrays($levels);


					if($levels){
						if($levels==$account['level'])
						{
							$accountCategory2=$this->accounts_model->getAccountItemDebitCreditByLevel($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotal['debitTotal']=$accountTotal['debitTotal']+$accountCategory2['debitTotal'];
							$accountTotal['creditTotal']=$accountTotal['creditTotal']+$accountCategory2['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}

					if (!empty($_GET)){
						$accountTotalNet = $this->accounts_model->getAccountTotalDebitCreditNetV3($account['id'],$accountTotalV2);
					}
					else {
						$accountTotalNet = $this->accounts_model->getAccountTotalDebitCreditNetV3($account['id'],$accountTotalV2);
					}

					//


					if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryNet=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalNet['debitTotal']=$accountTotalNet['debitTotal']+$accountCategoryNet['debitTotal'];
							$accountTotalNet['creditTotal']=$accountTotalNet['creditTotal']+$accountCategoryNet['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}

					//$this->sma->print_arrays($accountTotalNet);

					if($prev_start_date){
					if (!empty($_GET)){
						$accountTotalCB = $this->accounts_model->getAccountTotalDebitCreditNetV3($account['id'],$prev_accountTotalV2);
					}
					else {
						$accountTotalCB = $this->accounts_model->getAccountTotalDebitCreditNetV3($account['id'],$prev_accountTotalV2);
					}


						if($levels){
						if($levels==$account['level'])
						{
							$accountCategory22=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],$prev_start_date,(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalCB['debitTotal']=$accountTotalCB['debitTotal']+$accountCategory22['debitTotal'];
							$accountTotalCB['creditTotal']=$accountTotalCB['creditTotal']+$accountCategory22['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


					}else{
							//$this->sma->print_arrays($accountTotal);

						$accountTotalCB=$accountTotal;
					}

							//$this->sma->print_arrays($accountTotalCB);

					if($_GET['show_Opening_balance'] && $_GET['show_Opening_balance']!='0')
					{

					if (!empty($_GET)){
						$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryOB2=$this->accounts_model->getAccountItemDebitCreditByLevelOB($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$accountCategoryOB2['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$accountCategoryOB2['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


						if($prev_start_date){
						$prev_accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],$prev_start_date,$prev_end_date,$grpId,$coId,$brId);

									if($levels){
						if($levels==$account['level'])
						{
							$accountCategory3=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],$prev_start_date,$prev_end_date,$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$prev_accountTotalOB['debitTotal']=$prev_accountTotalOB['debitTotal']+$accountCategory3['debitTotal'];
							$prev_accountTotalOB['creditTotal']=$prev_accountTotalOB['creditTotal']+$accountCategory3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$prev_accountTotalOB['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$prev_accountTotalOB['creditTotal'];



						}


						$accountTotalM = $this->accounts_model->getAccountTotalDebitCreditM($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

						if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryM3=$this->accounts_model->getAccountItemDebitCreditByLevelM($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalM['debitTotal']=$accountTotalM['debitTotal']+$accountCategoryM3['debitTotal'];
							$accountTotalM['creditTotal']=$accountTotalM['creditTotal']+$accountCategoryM3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}


					}
					else {
						$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);


							if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryOB2=$this->accounts_model->getAccountItemDebitCreditByLevelOB($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$accountCategoryOB2['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$accountCategoryOB2['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}





						if($prev_start_date){
						$prev_accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditNet($account['id'],$prev_start_date,$prev_end_date,$grpId,$coId,$brId);



									if($levels){
						if($levels==$account['level'])
						{
							$accountCategory3=$this->accounts_model->getAccountItemDebitCreditByLevelNet($account['id'],$prev_start_date,$prev_end_date,$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$prev_accountTotalOB['debitTotal']=$prev_accountTotalOB['debitTotal']+$accountCategory3['debitTotal'];
							$prev_accountTotalOB['creditTotal']=$prev_accountTotalOB['creditTotal']+$accountCategory3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}

							$accountTotalOB['debitTotal']=$accountTotalOB['debitTotal']+$prev_accountTotalOB['debitTotal'];
							$accountTotalOB['creditTotal']=$accountTotalOB['creditTotal']+$prev_accountTotalOB['creditTotal'];
						}


						$accountTotalM = $this->accounts_model->getAccountTotalDebitCreditM($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

						if($levels){
						if($levels==$account['level'])
						{
							$accountCategoryM3=$this->accounts_model->getAccountItemDebitCreditByLevelM($account['id'],(isset($_GET['start_date']) && $_GET['start_date']!=NULL ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) && $_GET['end_date']!=NULL ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							//$accountTotal=$accountTotal+$accountCategory2['accountBalance'];
							$accountTotalM['debitTotal']=$accountTotalM['debitTotal']+$accountCategoryM3['debitTotal'];
							$accountTotalM['creditTotal']=$accountTotalM['creditTotal']+$accountCategoryM3['creditTotal'];

						//echo $accountBalance."<br>";
						}
					}
					}
					//echo $prev_accountTotalOB;





					}else{
						$accountTotalM=$accountTotal;
					}

						$accountTotalNM=$accountTotalM;

					//$this->sma->print_arrays($accountTotalM);
					//print_r($accountTotal);

					// if ($accountTotal == 0){
					// 	continue;
					// }

					/*if($accountTotalCB['debitTotal']==0 && $accountTotalCB['creditTotal']==0){
					}else*/{

					if($_GET['show_Opening_balance'] && $_GET['show_Opening_balance']!='0')
					{
						/*if($accountTotalOB['debitTotal']>$accountTotalOB['creditTotal']){
							$accountTotalOB['debitTotal']=($accountTotalOB['debitTotal']-$accountTotalOB['creditTotal']);
							$accountTotalOB['creditTotal']=0;
						}else if($accountTotalOB['debitTotal']<$accountTotalOB['creditTotal']){
							$accountTotalOB['creditTotal']=($accountTotalOB['creditTotal']-$accountTotalOB['debitTotal']);
							$accountTotalOB['debitTotal']=0;
						}else{
							$accountTotalOB['debitTotal']=0;
							$accountTotalOB['creditTotal']=0;
						}*/



					}
					//$this->sma->print_arrays($accountTotalOB);

					$totalDebitAccountOB += $accountTotalOB['debitTotal'];
					$totalCreditAccountOB += $accountTotalOB['creditTotal'];





					$totalDebitAccountNM += $accountTotalNM['debitTotal'];
					$totalCreditAccountNM += $accountTotalNM['creditTotal'];


						/*if($accountTotalM['debitTotal']>$accountTotalM['creditTotal']){
							$accountTotalM['debitTotal']=($accountTotalM['debitTotal']-$accountTotalM['creditTotal']);
							$accountTotalM['creditTotal']=0;
						}else if($accountTotalM['debitTotal']<$accountTotalM['creditTotal']){
							$accountTotalM['creditTotal']=($accountTotalM['creditTotal']-$accountTotalM['debitTotal']);
							$accountTotalM['debitTotal']=0;
						}else{
							$accountTotalM['debitTotal']=0;
							$accountTotalM['creditTotal']=0;
						}*/


					$totalDebitAccountM += $accountTotalNet['debitTotal'];
					$totalCreditAccountM += $accountTotalNet['creditTotal'];



						/*if($accountTotalCB['debitTotal']>$accountTotalCB['creditTotal']){
							$accountTotalCB['debitTotal']=($accountTotalCB['debitTotal']-$accountTotalCB['creditTotal']);
							$accountTotalCB['creditTotal']=0;
						}else if($accountTotalCB['debitTotal']<$accountTotalCB['creditTotal']){
							$accountTotalCB['creditTotal']=($accountTotalCB['creditTotal']-$accountTotalCB['debitTotal']);
							$accountTotalCB['debitTotal']=0;
						}else{
							$accountTotalCB['debitTotal']=0;
							$accountTotalCB['creditTotal']=0;
						}
						*/


					$totalDebitAccountCB += $accountTotalCB['debitTotal'];
					$totalCreditAccountCB += $accountTotalCB['creditTotal'];



						$zeroFlage=0;
					if($_GET['show_Opening_balance'] && $_GET['show_Opening_balance']!='0')
					{
						if($accountTotalOB['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalOB['creditTotal']!=0)
							$zeroFlage=1;

					}
						if($accountTotalNM['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalNM['creditTotal']!=0)
							$zeroFlage=1;

						if($accountTotalNet['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalNet['creditTotal']!=0)
							$zeroFlage=1;


					if($_GET['show_Closing_balance'] && $_GET['show_Closing_balance']!='0')
					{
						if($accountTotalCB['debitTotal']!=0)
							$zeroFlage=1;
						if($accountTotalCB['creditTotal']!=0)
							$zeroFlage=1;

					}



						if($zeroFlage!=0)
						{
							$row++;
							if ($this->Settings->user_language == 'arabic' || $account['accountNameEn']==''){
						$this->excel->getActiveSheet()->SetCellValue('A' . $row, '('.$account['accountID'].') '.$account['accountName']);
							}else{
								$this->excel->getActiveSheet()->SetCellValue('A' . $row, '('.$account['accountID'].') '.$account['accountNameEn']);
							}
						$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(12);
						//$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

						if ($_GET['show_Opening_balance'] && $_GET['show_Opening_balance'] != '0') {
							if ($accountTotalOB['debitTotal'] != abs($accountTotalOB['debitTotal'])){
								$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($accountTotalOB['debitTotal'])));
							} else {
								$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal($accountTotalOB['debitTotal']));
							}
							if ($accountTotalOB['creditTotal'] != abs($accountTotalOB['creditTotal'])){
								$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal(abs($accountTotalOB['creditTotal'])));
							} else {
								$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal($accountTotalOB['creditTotal']));
							}
						}

						if ($accountTotalNM['debitTotal'] != abs($accountTotalNM['debitTotal'])){
							$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal(abs($accountTotalNM['debitTotal'])));
						} else {
							$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal($accountTotalNM['debitTotal']));
						}

						if ($accountTotalNM['creditTotal'] != abs($accountTotalNM['creditTotal'])){
							$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal(abs($accountTotalNM['creditTotal'])));
						} else {
							$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($accountTotalNM['creditTotal']));
						}


						if ($accountTotalNet['debitTotal'] != abs($accountTotalNet['debitTotal'])){
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal(abs($accountTotalNet['debitTotal'])));
						} else {
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($accountTotalNet['debitTotal']));
						}

						if ($accountTotalNet['creditTotal'] != abs($accountTotalNet['creditTotal'])){
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($accountTotalNet['creditTotal'])));
						} else {
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($accountTotalNet['creditTotal']));
						}

						if ($_GET['show_Closing_balance'] && $_GET['show_Closing_balance'] != '0') {
							if ($accountTotalCB['debitTotal'] != abs($accountTotalCB['debitTotal'])){
								$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal(abs($accountTotalCB['debitTotal'])));
							} else {
								$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($accountTotalCB['debitTotal']));
							}

							if ($accountTotalCB['creditTotal'] != abs($accountTotalCB['creditTotal'])){
								$this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal(abs($accountTotalCB['creditTotal'])));
							} else {
								$this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($accountTotalCB['creditTotal']));
							}
						}
					}
					}
				}
				$row++;
			}
			/*foreach ($data as $data_row) {
                    $this->excel->getActiveSheet()->SetCellValue('A' . $row, $data_row->company);
                    $this->excel->getActiveSheet()->SetCellValue('B' . $row, $data_row->name);
                    $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($data_row->amount_cash));
                    $this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatMoney($data_row->amount_CC));
                    $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney(0));
                    $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($data_row->net));
                    $row++;
					$total += $data_row->amount_cash;
                    $total_discount += $data_row->amount_CC;
                    $total_amount += $data_row->net;

                }

				$this->excel->getActiveSheet()->getStyle('C' . $row . ':F' . $row)->getBorders()
                    ->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($total));
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatMoney($total_discount));
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney(0));
				$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney($total_amount));
				*/
			$this->excel->getActiveSheet()->getStyle('B' . $row . ':I' . $row)->getBorders()
				->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('total'));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

			if ($_GET['show_Opening_balance'] && $_GET['show_Opening_balance'] != '0') {
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal($totalDebitAccountOB));
				$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatDecimal($totalCreditAccountOB));
				$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);
			}

			$this->excel->getActiveSheet()->SetCellValue('D' . $row, $this->sma->formatDecimal($totalDebitAccountNM));
			$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal($totalCreditAccountNM));
			$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

			$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal($totalDebitAccountM));
			$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($totalCreditAccountM));
			$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);

			if ($_GET['show_Closing_balance'] && $_GET['show_Closing_balance'] != '0') {
				$this->excel->getActiveSheet()->SetCellValue('H' . $row, $this->sma->formatDecimal($totalDebitAccountCB));
				$this->excel->getActiveSheet()->getStyle('H' . $row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->SetCellValue('I' . $row, $this->sma->formatDecimal($totalCreditAccountCB));
				$this->excel->getActiveSheet()->getStyle('I' . $row)->getFont()->setBold(true);
			}




			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
			if ($_GET['show_Opening_balance'] && $_GET['show_Opening_balance'] != '0') {
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			} else {
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(0);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(0);
			}
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			if ($_GET['show_Closing_balance'] && $_GET['show_Closing_balance'] != '0') {
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
			} else {
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(0);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(0);
			}


			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dee2e6')
				)
			);

			/*$styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
	  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray1);
			$this->excel->getActiveSheet()->getStyle('A5:I6')->applyFromArray($styleArray1);





			$filename = 'Trial_balance';
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				 $this->data['mainGroups'] = $this->site->getMainGroup();
				 $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				 $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




				}

			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Trial_balance'), 'page' => lang('Trial_balance')], ['link' => '#', 'page' => lang('Trial_balance')]];
			$meta                = ['page_title' => lang('Trial_balance'), 'bc' => $bc];
			$this->page_construct('accounts/trialbalance', $meta, $this->data);
		}
	}


	public function distributioncostcenters($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{

		$this->sma->checkPermissions('distributioncostcenters', true);
		if ($pdf || $xls) {

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

			$show_style    = $this->input->get('show_style') ? $this->input->get('show_style') : 1;
			$show_Opening_balance    = $this->input->get('show_Opening_balance'); // ? $this->input->get('show_Opening_balance') : 1;

			$costcenter    = $this->input->get('costcenter') ? $this->input->get('costcenter') : null;
			$account    = $this->input->get('account') ? $this->input->get('account') : null;
			$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;


			if ($id) {
				$accountList = $this->accounts_model->getCostCenters($id);
			} else {
				$accountList  = $this->accounts_model->getCostCenters($costcenter);
			}


			$costcenters = $this->accounts_model->getCostCenters();

			$accounts = $this->accounts_model->getAccounts();


			if ($start_date && $end_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';



				$entryList  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance, $grpId, $coId, $brId);

				$prev_entryList  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance, $grpId, $coId, $brId);
			} else {
				$entryList  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance,'','', $grpId, $coId, $brId);
			}


			$filter_id  = $id;
			$filter_id1  = $id1;

			//$this->sma->print_arrays($filter_id);


			//$this->sma->print_arrays($entryList);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Cost_centers_Report'));

			$this->excel->getActiveSheet()->mergeCells('A1:G1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Cost_centers_Report'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 20,
					'name'  => 'Cairo-Bold'
				)

			);
			$Font11Bold = array(
				'font' => array(
					'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);





			$this->excel->getActiveSheet()->mergeCells('A2:G2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

			$row = 3;


			if($grpId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_group').' : '.($this->site->getMainGroupsByID($grpId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        //$this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                        }

                        if($coId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_company').' : '.($this->site->getMainCompaniesByID($coId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        //$this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                         $row++;
                        }

                        if($brId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_branch').' : '.($this->site->getMainBranchesByID($brId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                       // $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                        }

			$this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
			$this->excel->getActiveSheet()->SetCellValue('A'.$row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Cost_centers_Report') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);




			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)
			);

			$styleArrayGreen = array(

				'font' => array(
					'color' => array('rgb' => '28a745')
				)
			);

			$styleArrayRed = array(

				'font' => array(
					// 'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dc3545')
				)
			);

			/*$styleArray2 = array(
              'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
                  );*/
			$styleArrayF11Bold = array(
				'font' => array(
					'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);

			$this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArray1);
			$row++;
			$accountSide = "Debit";

			if (!isset($_GET['costcenter']))
				unset($accountList); {
				foreach ($accountList as $account1) {
					$account1 = (array) $account1;

					if ($accountSide == "Debit") {
						$accountInfo = $this->accounts_model->getCostCentersByID($account1['id']);
					}

					$accountList1 = $this->accounts_model->getCostCenterChilds($account1['costID'], $account1['parent_id']);

					if ($this->Settings->user_language == 'arabic' || $account1['costNameEn'] == '') {
						$account1 = $account1['costName'];
					} else {
						$account1 = $account1['costNameEn'];
					}



					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $account1);
					$this->excel->getActiveSheet()->mergeCells('A' . $row . ':C' . $row);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($styleArrayF11Bold);



					$row++;


					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Sr#'));
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("Entry_number"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("date"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Account") . ' - ' . lang("description"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Debit") . ' (' . $this->Settings->symbol . ')');
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("Credit") . ' (' . $this->Settings->symbol . ')');
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('G' . $row, lang("balance") . ' (' . $this->Settings->symbol . ')');
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($styleArray1);



				$row++;




				$balanceDebit = 0;
				$balanceCredit = 0;
				$fbalance = 0;
				$sr = 0;

				$prev_fbalance = 0;


				foreach ($accountList1 as $account) {

					$account = (array) $account;
					$accountEntries = array();

					//$this->sma->print_arrays($account['id']);


					$id      = $account['id'];
					//$accountBalance = $account['accountDebit'] - $account['accountCredit'];


					foreach ($entryList as $entry) {
						$entry = (array) $entry;

						$jsonArr_costCenterDebit = json_decode($entry['costCenterDebitId']);
						$jsonArr_costCenterCredit = json_decode($entry['costCenterCreditId']);
						//$jsonArr_costCenter=$jsonArr_costCenter + json_decode($entry['costCenterCreditId']);
						$jsonArr_costCenterDebit = (array) $jsonArr_costCenterDebit;
						$jsonArr_costCenterCredit = (array) $jsonArr_costCenterCredit;

						//print_r($jsonArr_costCenter);
						foreach ($jsonArr_costCenterDebit as $ent) {


							if (in_array($account['id'], ($ent))) {
								//print_r($entry);
								array_push($accountEntries, $entry);
							}
						}

						foreach ($jsonArr_costCenterCredit as $ent) {


							if (in_array($account['id'], ($ent))) {
								//print_r($entry);
								array_push($accountEntries, $entry);
							}
						}
					}

					$accountEntriesGlogal = [];
					$accountEntriesGlogal_temp = [];

					//$accountEntriesGlogal=$accountEntries;

					$countDublicat = 0;
					foreach ($accountEntries as $accountEntry) {

						if (!in_array($accountEntry['entryID'], $accountEntriesGlogal)) {

							//echo '_'.$countDublicat.'+ser='.array_search($accountEntry['entryID'], $accountEntriesGlogal);
							array_push($accountEntriesGlogal, $accountEntry['entryID']);
							array_push($accountEntriesGlogal_temp, $countDublicat);
						}

						$countDublicat++;
					}
					$accountEntries_Arr = array();

					foreach ($accountEntriesGlogal_temp as $accountEntriesGlogal_temp_index) {
						array_push($accountEntries_Arr, $accountEntries[$accountEntriesGlogal_temp_index]);
					}




					if ($this->Settings->user_language == 'arabic' || $account['costNameEn'] == '') {
						$costName = $account['costName'];
					} else {
						$costName = $account['costNameEn'];
					}



					foreach ($accountEntries_Arr as $entry) {
						$entry = (array) $entry;

						$costCenterDebitIds = array();
						$costCenterDebitAmounts = 0;

						$costCenterCreditIds = array();
						$costCenterCreditAmounts = 0;

						$costCenterEntry = array();


						foreach (json_decode($entry['costCenterDebitId']) as $debitAccount) {
							$costCenterDebitAmountsInner = 0;
							foreach ($debitAccount as $ent) {

								array_push($costCenterDebitIds, array((string) $ent => json_decode($entry['costCenterDebitAmount'])[$costCenterDebitAmounts][$costCenterDebitAmountsInner]));
								$costCenterDebitAmountsInner += 1;
							}
							$costCenterDebitAmounts += 1;
						}

						foreach (json_decode($entry['costCenterCreditId']) as $creditAccount) {
							$costCenterCreditAmountsInner = 0;
							foreach ($creditAccount as $ent) {
								array_push($costCenterCreditIds, array((string) $ent => json_decode($entry['costCenterCreditAmount'])[$costCenterCreditAmounts][$costCenterCreditAmountsInner]));
								$costCenterCreditAmountsInner += 1;
							}
							$costCenterCreditAmounts += 1;
						}
						$accountFlage = 1;
						$accountName = $costName;
						if (isset($_GET['account']) && $_GET['account'] != "") {
							$accountFlage = 0;
							$accountInfo = $this->accounts_model->getAccountByID($_GET['account']);
							if ($this->Settings->user_language == 'arabic' || $accountInfo->accountNameEn == '') {
								$accountName = $accountInfo->accountName;
							} else {
								$accountName = $accountInfo->accountNameEn;
							}
							if (in_array($_GET['account'], json_decode($entry['entryDebitAccount'])) || in_array($_GET['account'], json_decode($entry['entryCreditAccount']))) {
								$accountFlage = 1;
							}
						}


						$dt_index = 0;
						foreach (json_decode($entry['entryDebitAccount']) as $debitAccount) {
							$dtc_index = 0;
							foreach ((array)json_decode($entry['costCenterDebitId'])[$dt_index] as $costDebitId) {

								if (isset($_GET['account']) && $_GET['account'] != "") {
									if (json_decode($entry['costCenterDebitId'])[$dt_index][$dtc_index] == $id && $debitAccount == $_GET['account']) {


										array_push($costCenterEntry, $entry['entryID']);
									}
								} else {
									if (json_decode($entry['costCenterDebitId'])[$dt_index][$dtc_index] == $id) {


										array_push($costCenterEntry, $entry['entryID']);
									}
								}
								$dtc_index++;
							}
							$dt = $dt + $entry['entryDebitBalance'][$dt_index];
							$dt_index++;
						}

						$ct_index = 0;
						foreach (json_decode($entry['entryCreditAccount']) as $CreditAccount) {
							$ctc_index = 0;
							foreach ((array)json_decode($entry['costCenterCreditId'])[$ct_index] as $costCreditId) {
								if (isset($_GET['account']) && $_GET['account'] != "") {
									if (json_decode($entry['costCenterCreditId'])[$ct_index][$ctc_index] == $id && $CreditAccount == $_GET['account']) {


										array_push($costCenterEntry, $entry['entryID']);
									}
								} else {
									if (json_decode($entry['costCenterCreditId'])[$ct_index][$ctc_index] == $id) {


										array_push($costCenterEntry, $entry['entryID']);
									}
								}
								$ctc_index++;
							}
							$ct = $ct + $entry['entryCreditBalance'][$ct_index];
							$ct_index++;
						}


						if ($accountFlage == 1 && in_array($entry['entryID'], $costCenterEntry)) {
							$x = 0;

							foreach ($costCenterDebitIds as $account) {


								$balance = $account[key($account)];
								$account = key($account);
								$accountInfo = $this->accounts_model->getCostCentersByID($account);
								if ($accountInfo->id == $id) {
									$x++;
								}
							}


							$sr++;

							$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
							$this->excel->getActiveSheet()->SetCellValue('B' . $row, $entry['entryID']);
							$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrsd($entry['entryDate']));
							if ($entry['entryType'] != "Opening_balance") {
								$entryDescription = str_replace("<p>", "", ($entry['entryDescription']) . "");
								$entryDescription = str_replace("</p>", "", ($entryDescription) . "");
								$this->excel->getActiveSheet()->SetCellValue('D' . $row, $accountName . '-' . lang($entryDescription));
							} else {
								$this->excel->getActiveSheet()->SetCellValue('D' . $row, $accountName . '-' . lang($entry['entryType']));
							}




							$entryBalanceDebit = "";
							$$balance = "";
							foreach ($costCenterDebitIds as $account) {
								$balance = $account[key($account)];
								$account = key($account);
								$accountInfo = $this->accounts_model->getCostCentersByID($account);

								$x = 0;
								if ($accountInfo->id == $id) {
									$entryBalanceDebit = $entryBalanceDebit . "" . $this->sma->formatMoney($balance, ' ') . "\n";

									$balanceDebit = $balanceDebit + $balance;
									$fbalance = $fbalance - $balance;
									$x++;
								}
							}
							$this->excel->getActiveSheet()->SetCellValue('E' . $row, $entryBalanceDebit);
							$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);


							$entryBalanceCredit = "";
							$$balance = "";
							foreach ($costCenterCreditIds as $account) {
								$balance = $account[key($account)];
								$account = key($account);
								$accountInfo = $this->accounts_model->getCostCentersByID($account);

								$y = 0;
								if ($accountInfo->id == $id) {
									$entryBalanceCredit = $entryBalanceCredit . "" . $this->sma->formatMoney($balance, ' ') . "\n";

									$balanceCredit = $balanceCredit + $balance;
									$fbalance = $fbalance + $balance;
									$y++;
								}
							}
							$this->excel->getActiveSheet()->SetCellValue('F' . $row, $entryBalanceCredit);
							$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setWrapText(true);


							//$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney(abs($fbalance), ' '));

							if ($fbalance > 0) {
								$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($fbalance)));
								$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
								$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayGreen);
							} else {
								$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($fbalance)));
								$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
								$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
								$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayRed);
							}

							$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$row++;
						}
						//$this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


					}
				}




				$total = 0;
				$diffDebit = 0;
				$diffCredit = 0;
				if ($balanceDebit > $balanceCredit) {
					$total = $balanceDebit;
					$diffDebit = ($balanceDebit - $balanceCredit);
				} else {
					$total = $balanceCredit;
					if ($balanceCredit > $balanceDebit)
						$diffCredit = ($balanceCredit - $balanceDebit);
				}

				if ($diffDebit != 0 || $diffCredit != 0) {
					if ($diffDebit != 0)
						$diffDebit = $this->sma->formatMoney(abs($diffDebit, ' '));
					else
						$diffDebit = '';

					if ($diffCredit != 0)
						$diffCredit = $this->sma->formatMoney(abs($diffCredit, ' '));
					else
						$diffCredit = '';
				}




				$this->excel->getActiveSheet()->mergeCells('A' . $row . ':D' . $row);
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("total"));



				$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatMoney(abs($balanceDebit), ' '));
				$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatMoney(abs($balanceCredit), ' '));

				if ($fbalance > 0) {
					$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($fbalance)));
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayGreen);
				} else {
					$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal(abs($fbalance)));
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayRed);
				}

				//$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatMoney(abs($fbalance), ' '));


				$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($Font11Bold);
				$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($styleArray1);
				$row++;
			}
		}



			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(70);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);



			$filename = 'Cost_centers_Report';
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			$show_style    = $this->input->post('show_style') ? $this->input->post('show_style') : 1;
			$show_Opening_balance    = $this->input->post('show_Opening_balance'); // ? $this->input->post('show_Opening_balance') : 1;

			$costcenter    = $this->input->post('costcenter') ? $this->input->post('costcenter') : null;
			$account    = $this->input->post('account') ? $this->input->post('account') : null;
			$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : null;

			/*if($id){
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($id)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		}else{
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($account);

		}*/

			if ($id) {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
				//else
				$this->data['accountList'] = $this->accounts_model->getCostCenters($id);
			} else {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
				//else
				$this->data['accountList'] = $this->accounts_model->getCostCenters($costcenter);
			}


			$this->data['costcenters'] = $this->accounts_model->getCostCenters();

			$this->data['accounts'] = $this->accounts_model->getAccounts();

			//$this->sma->print_arrays($this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID));

			//



			if ($start_date && $end_date) {
				//$start_date = $this->sma->fld($start_date);
				//$end_date   = $this->sma->fld($end_date);
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				//$date     = explode(' ', $start_date);
				//$start_date = $date[0];
				//$date     = explode(' ', $end_date);
				//$end_date = $date[0];
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				//	$this->sma->print_arrays($prev_start_date , $end_date ,$show_Opening_balance,date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59');
				$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance, $grpId, $coId, $brId);
				//$this->data['all_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date , $end_date ,$show_Opening_balance);
				$this->data['prev_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance, $grpId, $coId, $brId);
				//$this->sma->print_arrays($this->data['entryList'],$this->data['prev_entryList']);
				//$this->sma->print_arrays($start_date ,$prev_start_date , date('Y-m-d', strtotime('-1 days', strtotime($start_date))));

				//



			} else {
				//$this->data['entryList']  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance);
				$this->data['entryList']  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance,'','', $grpId, $coId, $brId);
			}





			$this->data['filter_id']  = $id;
			$this->data['filter_id1']  = $id1;


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Cost_centers_Report'), 'page' => lang('Cost_centers_Report')], ['link' => '#', 'page' => lang('Cost_centers_Report')]];
			$meta                = ['page_title' => lang('Cost_centers_Report'), 'bc' => $bc];
			$this->page_construct('accounts/distributioncostcenters', $meta, $this->data);
		}
	}


	public function agedopeninvoices($pdf = null, $xls = null, $start_date = null, $end_date = null, $customer = null)
	{

		$this->sma->checkPermissions('agedopeninvoices', true);



		ini_set('max_execution_time', 300);




		$levels = 0;


		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		/*$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		//$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];

		*/
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		$ts1 = strtotime($end_date);
		$year1 = date('Y', $ts1);
		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		//$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
			//$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];
			if ($_POST)
				$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($year1) : (object)['period_from_date' => $year1 . '-01-01', 'period_to_date' => $year1 . '-12-31'];
			else
				$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];


		//$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : $this->sma->hrsd(date('Y-m-d'));
		//$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$customer     = $this->input->post('customer') ? $this->input->post('customer') : '';
		$salesman_id     = $this->input->post('salesman_id') ? $this->input->post('salesman_id') : '';
		$not_due     = isset($_POST['not_due'])  ? $_POST['not_due'] : '1';
		$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';



		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0') {
			$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_POST['compare_periods']; $i++) {

				//if($i!=$_POST['compare_periods'])
				$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			$start_date = $end_date;

		}



		$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
		if($check_period){


		$start_date=$this->sma->hrsd($check_period->period_from_date);




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
	}else{

		$start_date='';
	}
		//$end_date = $this->sma->fld($end_date) . ' 23:59:59';

//$this->sma->print_arrays($start_date,$end_date,$this->data['max_min_period_year'],$customer_acc->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);

		if($customer){
			//$customer_acc = $this->site->getAccountByRefId($customer,'local_customers') ? $this->site->getAccountByRefId($customer,'local_customers') :($this->site->getAccountByRefId($customer,'international_customers') ? $this->site->getAccountByRefId($customer,'international_customers') : null);
			$customer_acc = $this->accounts_model->getAccountByID($customer);
			$customer_acc->amount = $this->accounts_model->getAccountTotal($customer_acc->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
			$customer_acc->customer = $customer_acc->accountName;
			$customer_acc->customer_id = $customer_acc->account_ref_id;
			if($this->sma->formatDecimal($customer_acc->amount) > 0 && isset($customer_acc->account_ref_id)){
					$saleList[] = $customer_acc;
			}else{
				if($this->sma->formatDecimal($customer_acc->amount) < 0 && isset($customer_acc->account_ref_id)){

					if($this->site->getCompanyByID($customer_acc->account_ref_id)->account_type==1){
						$customer_acc->amount =($customer_acc->amount);

						$saleList[] = $customer_acc;
					}

				}
			}


		}else{
			$accounts_settings   = $this->accounts_model->getSettings();

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($this->sma->formatDecimal($toAccountID->amount) > 0 && ($toAccountID->id != $accounts_settings->accounts_local_customers_acct)){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($this->sma->formatDecimal($toAccountID->amount) > 0 && ($toAccountID->id != $accounts_settings->accounts_international_customers_acct)){
					$saleList[] = $toAccountID;
				}
			}

			$accountsLocalV2 = $this->accounts_model->getAccountChildsWithLevelsCusSup($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID,'local_customers');
			$accountsInerV2 = $this->accounts_model->getAccountChildsWithLevelsCusSup($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID,'international_customers');

			foreach ($accountsLocalV2 as $toAccountID) {

				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;

				if($this->sma->formatDecimal($toAccountID->amount) < 0  && ($toAccountID->id != $accounts_settings->accounts_local_customers_acct)){

					//if($this->site->getCompanyByID($toAccountID->account_ref_id)->account_type==1){
						$toAccountID->amount =($toAccountID->amount);

						$saleList[] = $toAccountID;
					//}

				}

			}
			foreach ($accountsInerV2 as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($this->sma->formatDecimal($toAccountID->amount) < 0 && ($toAccountID->id != $accounts_settings->accounts_international_customers_acct)){
					//if($this->site->getCompanyByID($toAccountID->account_ref_id)->account_type==1){
						$toAccountID->amount =($toAccountID->amount);
						$saleList[] = $toAccountID;
					//}
				}
			}
		}


		$this->data['saleList']=$saleList;


			//$this->sma->print_arrays($saleList);







		/*
		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate($start_date, $end_date, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate(NULL, $end_date, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		*/



		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 //$this->data['customers'] = $this->input->post('brId') ? $this->site->getCustomersBybrId($this->input->post('brId')) : $this->site->getCustomersBybrId($this->session->userdata('brId'));




            }else{
				//$this->data['customers']      = $this->site->getAllCompanies('customer');
			}

			$accounts_settings   = $this->accounts_model->getSettings();

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_local_customers_acct){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_international_customers_acct){
					$saleList[] = $toAccountID;
				}
			}

			$this->data['customers']      = $saleList;

		//$this->sma->print_arrays($start_date,$end_date);
		//$this->sma->print_arrays($this->data['saleList']);


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Aged_Open_Invoices'), 'page' => lang('Aged_Open_Invoices')], ['link' => '#', 'page' => lang('Aged_Open_Invoices')]];
		$meta                = ['page_title' => lang('Aged_Open_Invoices'), 'bc' => $bc];
		$this->page_construct('accounts/agedopeninvoices_index', $meta, $this->data);
	}

	public function agedopeninvoicessupplier($pdf = null, $xls = null, $start_date = null, $end_date = null, $supplier = null)
	{

		$this->sma->checkPermissions('agedopeninvoices', true);

		ini_set('max_execution_time', 300);




		$levels = 0;


		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		//$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];




		//$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : $this->sma->hrsd(date('Y-m-d'));
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$supplier     = $this->input->post('supplier') ? $this->input->post('supplier') : '';
		$salesman_id     = $this->input->post('salesman_id') ? $this->input->post('salesman_id') : '';
		$not_due     = isset($_POST['not_due'])  ? $_POST['not_due'] : '1';
		$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';



		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0') {
			$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_POST['compare_periods']; $i++) {

				//if($i!=$_POST['compare_periods'])
				$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			$start_date = $end_date;

		}



		$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
		if($check_period){


		$start_date=$this->sma->hrsd($this->data['max_min_period_year']->period_from_date);




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
	}else{
		$start_date ='';
	}
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';

//$this->sma->print_arrays($start_date,$end_date);

		if($supplier){
			//$supplier_acc = $this->site->getAccountByRefId($supplier,'local_suppliers') ? $this->site->getAccountByRefId($supplier,'local_suppliers') :($this->site->getAccountByRefId($supplier,'international_suppliers') ? $this->site->getAccountByRefId($supplier,'international_suppliers') : null);
			$supplier_acc = $this->accounts_model->getAccountByID($supplier);
			//$this->sma->print_arrays($supplier_acc);
			$supplier_acc->amount = $this->accounts_model->getAccountTotal($supplier_acc->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
			$supplier_acc->supplier = $supplier_acc->accountName;
			$supplier_acc->supplier_id = $supplier_acc->account_ref_id;
			if($this->sma->formatDecimal($supplier_acc->amount) > 0 && isset($supplier_acc->account_ref_id)){
					$saleList[] = $supplier_acc;
			}else{
				if($this->sma->formatDecimal($supplier_acc->amount) < 0 && isset($supplier_acc->account_ref_id)){

					if($this->site->getCompanyByID($supplier_acc->account_ref_id)->account_type==1){
						$supplier_acc->amount =abs($supplier_acc->amount);

						$saleList[] = $supplier_acc;
					}

				}
			}


		}else{
			$accounts_settings   = $this->accounts_model->getSettings();

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_suppliers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_suppliers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->supplier = $toAccountID->accountName;
				$toAccountID->supplier_id = $toAccountID->account_ref_id;
				if($this->sma->formatDecimal($toAccountID->amount) > 0 && ($toAccountID->id != $accounts_settings->accounts_local_suppliers_acct)){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->supplier = $toAccountID->accountName;
				$toAccountID->supplier_id = $toAccountID->account_ref_id;
				if($this->sma->formatDecimal($toAccountID->amount) > 0 && ($toAccountID->id != $accounts_settings->accounts_international_suppliers_acct)){
					$saleList[] = $toAccountID;
				}
			}


			$accountsLocalV2 = $this->accounts_model->getAccountChildsWithLevelsCusSup($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID,'local_customers');
			$accountsInerV2 = $this->accounts_model->getAccountChildsWithLevelsCusSup($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID,'international_customers');

			foreach ($accountsLocalV2 as $toAccountID) {

				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->supplier = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;

				if($this->sma->formatDecimal($toAccountID->amount) < 0 && ($toAccountID->id != $accounts_settings->accounts_local_customers_acct)){

					//if($this->site->getCompanyByID($toAccountID->account_ref_id)->account_type==1){
						$toAccountID->amount =abs($toAccountID->amount);

						$saleList[] = $toAccountID;
					//}

				}

			}
			foreach ($accountsInerV2 as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->supplier = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($this->sma->formatDecimal($toAccountID->amount) < 0 && ($toAccountID->id != $accounts_settings->accounts_international_customers_acct)){
					//if($this->site->getCompanyByID($toAccountID->account_ref_id)->account_type==1){
						$toAccountID->amount =abs($toAccountID->amount);
						$saleList[] = $toAccountID;
					//}
				}
			}
		}


		$this->data['saleList']=$saleList;










		/*
		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate($start_date, $end_date, $supplier, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate(NULL, $end_date, $supplier, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		*/



		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 //$this->data['suppliers'] = $this->input->post('brId') ? $this->site->getsuppliersBybrId($this->input->post('brId')) : $this->site->getsuppliersBybrId($this->session->userdata('brId'));




            }else{
				//$this->data['suppliers']      = $this->site->getAllCompanies('supplier');
			}

			$accounts_settings   = $this->accounts_model->getSettings();

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_suppliers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_suppliers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_local_suppliers_acct){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_international_suppliers_acct){
					$saleList[] = $toAccountID;
				}
			}

			$accountsLocalV2 = $this->accounts_model->getAccountChildsWithLevelsCusSup($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID,'local_customers');
			$accountsInerV2 = $this->accounts_model->getAccountChildsWithLevelsCusSup($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID,'international_customers');

			foreach ($accountsLocalV2 as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_local_customers_acct){
					//if($this->site->getCompanyByID($toAccountID->account_ref_id)->account_type==1){
						$saleList[] = $toAccountID;
					//}

				}

			}
			foreach ($accountsInerV2 as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_international_customers_acct){
					//if($this->site->getCompanyByID($toAccountID->account_ref_id)->account_type==1){
						$saleList[] = $toAccountID;
					//}
				}
			}

			$this->data['suppliers']      = $saleList;
				//$this->sma->print_arrays($saleList);
		//$this->sma->print_arrays($start_date,$end_date);
		//$this->sma->print_arrays($this->data['saleList']);

/*
		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

		//$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : $this->sma->hrsd(date('Y-m-d'));
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$supplier     = $this->input->post('supplier') ? $this->input->post('supplier') : '';
		$not_due     = isset($_POST['not_due'])  ? $_POST['not_due'] : '1';
		$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';
		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0') {
			$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_POST['compare_periods']; $i++) {

				//if($i!=$_POST['compare_periods'])
				$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			$start_date = $end_date;
		}




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';


		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0)
			$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregate($start_date, $end_date, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);
		else
			$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregate(NULL, $end_date, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['suppliers'] = $this->input->post('brId') ? $this->site->getSuppliersBybrId($this->input->post('brId')) : $this->site->getSuppliersBybrId($this->session->userdata('brId'));




            }else{
				$this->data['suppliers']      = $this->site->getAllCompanies('supplier');
			}



*/

		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Supplier_debt_aging_report'), 'page' => lang('Supplier_debt_aging_report')], ['link' => '#', 'page' => lang('Aged_Open_Invoices')]];
		$meta                = ['page_title' => lang('Supplier_debt_aging_report'), 'bc' => $bc];
		$this->page_construct('accounts/agedopeninvoicessupplier_index', $meta, $this->data);
	}

	public function agedopeninvoicesForCustomer($pdf = null, $xls = null, $start_date = null, $end_date = null, $customer = null, $salesman_id = null)
	{
		$this->sma->checkPermissions('agedopeninvoices', true);

		//$this->sma->print_arrays($_GET);
		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
		$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
		$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
		$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
		$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$customer     = $this->input->get('customer') ? $this->input->get('customer') : '';
		$salesman_id     = $this->input->get('salesman_id') ? $this->input->get('salesman_id') : '';
		$not_due     = isset($_GET['due'])  ? $_GET['due'] : '1';
		if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
			$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

				if ($i != $_GET['compare_periods'])
					$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			//$start_date=$end_date;
		}




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';


		if ($_GET['compare_periods'] && $_GET['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllSalesDue(NULL, $start_date, $end_date, 1, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllSalesDue(NULL, $start_date, $end_date, 1, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);


		//$this->sma->print_arrays($not_due);
		//$this->sma->print_arrays($this->data['saleList']);

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->get('grpId') ? $this->site->getCompaniesBygrpId($this->input->get('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->get('coId') ? $this->site->getBranchesBycoId($this->input->get('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['customers'] = $this->input->get('brId') ? $this->site->getCustomersBybrId($this->input->get('brId')) : $this->site->getCustomersBybrId($this->session->userdata('brId'));




            }else{
				$this->data['customers']      = $this->site->getAllCompanies('customer');
			}


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Aged_Open_Invoices'), 'page' => lang('Aged_Open_Invoices')], ['link' => '#', 'page' => lang('Aged_Open_Invoices')]];
		$meta                = ['page_title' => lang('Aged_Open_Invoices'), 'bc' => $bc];
		$this->page_construct('accounts/agedopeninvoices', $meta, $this->data);
	}



	public function agedopeninvoicesForSupplier($pdf = null, $xls = null, $start_date = null, $end_date = null, $customer = null)
	{
		$this->sma->checkPermissions('agedopeninvoices', true);

		//$this->sma->print_arrays($_GET);

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
		$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
		$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

		$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
		$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$supplier     = $this->input->get('supplier') ? $this->input->get('supplier') : '';
		$not_due     = isset($_GET['due'])  ? $_GET['due'] : '1';
		if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
			$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

				if ($i != $_GET['compare_periods'])
					$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_GET['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			//$start_date=$end_date;
		}




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';


		if ($_GET['compare_periods'] && $_GET['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllPurchasesDue(NULL, $start_date, $end_date, 1, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllPurchasesDue(NULL, $start_date, $end_date, 1, $supplier, $not_due, $the_oldest,$grpId,$coId,$brId);


		//$this->sma->print_arrays($not_due);
		//$this->sma->print_arrays($this->data['saleList']);

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->get('grpId') ? $this->site->getCompaniesBygrpId($this->input->get('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->get('coId') ? $this->site->getBranchesBycoId($this->input->get('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['suppliers'] = $this->input->get('brId') ? $this->site->getSuppliersBybrId($this->input->get('brId')) : $this->site->getSuppliersBybrId($this->session->userdata('brId'));




            }else{
				$this->data['suppliers']      = $this->site->getAllCompanies('supplier');
			}


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Supplier_debt_aging_report'), 'page' => lang('Supplier_debt_aging_report')], ['link' => '#', 'page' => lang('Supplier_debt_aging_report')]];
		$meta                = ['page_title' => lang('Supplier_debt_aging_report'), 'bc' => $bc];
		$this->page_construct('accounts/agedopeninvoicessupplier', $meta, $this->data);
	}
	public function ownersequity($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		$this->sma->checkPermissions('incomestatement', true);

		$this->data['incomestatement'] = $this->getCurentIncomeStatementV2($this->accounts_model->getincomeStatementAccounts(), (isset($_POST['start_date']) ? $this->sma->fld($_POST['start_date']) . ' 00:00:00' : ''), (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) . ' 23:59:58' : ''))['Final_Net_Income'];

		//$this->sma->print_arrays($this->data['incomestatement']);

		if (!$this->site->getAccountsSettings()->accounts_ready_funds_acct) {
			$this->session->set_flashdata('error', lang('Please_select_owners_equity_account'));
			admin_redirect('accounts/settings');
		}

		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Statement_of_owners_equity'), 'page' => lang('Statement_of_owners_equity')], ['link' => '#', 'page' => lang('Statement_of_owners_equity')]];
		$meta                = ['page_title' => lang('Statement_of_owners_equity'), 'bc' => $bc];
		$this->page_construct('accounts/ownersequity', $meta, $this->data);
	}
	public function incomestatement($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		$this->sma->checkPermissions('incomestatement', true);
		ini_set('max_execution_time', 300);

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			//$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		//$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
		if ($_POST)
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->input->post('period_year')) : (object)['period_from_date' => $this->input->post('period_year') . '-01-01', 'period_to_date' => $this->input->post('period_year') . '-12-31'];
		else
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];


		if ($pdf || $xls) {

			$levels    = $this->input->get('levels') ? $this->input->get('levels') : 0;
			$period_year    = $this->input->get('period_year') ? $this->input->get('period_year') : null;
			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			//$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));


			//$this->sma->print_arrays($period_year);
			if ($levels == 0)
				$accountList = $this->accounts_model->getincomeStatementAccounts();
			else
				$accountList = $this->accounts_model->getincomeStatementAccounts(NULL, $levels);

			//$this->data['accountList'] = $this->accounts_model->getAccountsNotZeros();
			//$data['userData']   = $this->session->userdata();
			//$this->data['entryList']  = $this->accounts_model->getEntries();

			//$this->sma->print_arrays($this->data['accountList']);

			if (empty($_GET)) {

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
				if ($check_period || $check_period->period_status == 1) {
					$_GET['start_date'] = $this->sma->hrsd($check_period->period_from_date);
					$_GET['end_date'] = $this->sma->hrsd($check_period->period_to_date);
				}
			}


			if (empty($_GET['compared_to']))
			$_GET['compared_to'] = '0';

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Income_Statement'));
			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0, 1, $_GET['compare_periods'], 1);
			} else {
				$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0, 1, 1, 1);
			}
			$styleArrayFill = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)

			);
			$styleArrayF13 = array(
				'font' => array(
					'bold' => true,
					'size' => 15,
					'name'  => 'Cairo-Bold'
				)

			);
			$styleArrayF11 = array(
				'font' => array(
					//'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);
			$styleArrayF11Bold = array(
				'font' => array(
					'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, lang('Income_Statement'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0, 2, $_GET['compare_periods'], 2);
			} else {
				$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0, 2, 1, 2);
			}

			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0, 3, $_GET['compare_periods'], 3);
			} else {
				$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0, 3, 1, 3);
			}


			$row = 3;






			// $this->excel->getActiveSheet()->mergeCells('A3:D3');
			$this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') . ' ' . lang('') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(12);
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);






			/*$styleArray2 = array(
  'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
      );*/

			$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArrayFill);


			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, lang('Account'));
			$this->excel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);




			//if($_POST['compare_periods'] && $_GET['compared_to']!='0')
			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));



				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, 5, $effectiveEndDate);


					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i - 1, 5, $i, 5)->applyFromArray($styleArrayF11Bold);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i - 1, 5, $i, 5)->applyFromArray($styleArrayFill);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i - 1, 5, $i, 5)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				}
			} else {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 5, lang('Amount'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 5, 1, 5)->applyFromArray($styleArrayF11Bold);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 5, 1, 5)->applyFromArray($styleArrayFill);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 5, 1, 5)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}




			$revenueTotal  = 0;
			$orevenueTotal  = 0;
			$expensesTotal = 0;
			$opexpensesTotal = 0;
			$maexpensesTotal = 0;

			$accounts = array();

			foreach ($accountList as $account) {
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])) {
					$accounts[$account['accountCategory']] = array($account);
				} else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}

			$accountCategories = array_keys($accounts);
			//print_r($accountList);
			$accountOrder = 0;

			$moneySign = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';


			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 6, lang('Revenues'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 6)->applyFromArray($styleArrayF11Bold);


				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 6, '');
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
				}
			} else {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 6, lang('Revenues'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 6)->applyFromArray($styleArrayF11Bold);
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 6, '');
			}
			$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_acct);
			$acc = (array) $acc[0];

			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 7, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 7)->applyFromArray($styleArrayF11);

				/*          echo '
						                <tr>
							                <td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							                ';

	                                       */

				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$sales_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'], $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58');

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, 7, $this->sma->formatDecimal(abs($sales_acctBalance[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, 7)->applyFromArray($styleArrayF11);
					/* echo '
							                <td class="text-right">'.$this->sma->formatDecimal(abs($sales_acctBalance[$icount])).'</td>
							                ';
                                      */
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));



					$icount++;
				}
			} else {

				$sales_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);


				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 7, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 7)->applyFromArray($styleArrayF11);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 7, $this->sma->formatDecimal(abs($sales_acctBalance)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, 7)->applyFromArray($styleArrayF11);
				/*
                                         echo '
						                <tr>
							                <td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							                <td class="text-right">'.$this->sma->formatDecimal(abs($sales_acctBalance)).'</td>
							                </tr>
			                          ';
                                      */
			}




			$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_return_acct);
			$acc = (array) $acc[0];


			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 8, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 8)->applyFromArray($styleArrayF11);





				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$sales_return_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'], $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, 8, $this->sma->formatDecimal(abs($sales_return_acctBalance[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, 8)->applyFromArray($styleArrayF11);

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {

				$sales_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 8, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 8)->applyFromArray($styleArrayF11);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, $this->sma->formatDecimal(abs($sales_return_acctBalance)));

				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, 8)->applyFromArray($styleArrayF11);
			}





			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 9, lang('net_sales'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 9)->applyFromArray($styleArrayF11Bold);

				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$net_sales[$icount] = $this->sma->formatDecimal(($sales_acctBalance[$icount])) + $this->sma->formatDecimal(($sales_return_acctBalance[$icount]));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, 9, $this->sma->formatDecimal(abs($net_sales[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, 9)->applyFromArray($styleArrayF11Bold);

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {
				$net_sales = $this->sma->formatDecimal(($sales_acctBalance)) + $this->sma->formatDecimal(($sales_return_acctBalance));

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 9, lang('net_sales'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, 9)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, 9, $this->sma->formatDecimal(abs($net_sales)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, 9)->applyFromArray($styleArrayF11Bold);
			}





			$row = 10;

			if ($this->Settings->accounting_type == 'periodic' && $period_year!=2022) {




				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('First_period_stock'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$first_period_stock[$icount] = $this->site->getOpeningBalanceForWarehouses($effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',null,$grpId,$coId,$brId)->first_period_stock;
						if($first_period_stock[$icount]==0){

							$first_period_stock[$icount] = $this->site->getLastendPeriodStock($effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',$brId);
							}
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($first_period_stock[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {

					$first_period_stock = $this->site->getOpeningBalanceForWarehouses((isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),null,$grpId,$coId,$brId)->first_period_stock;
					if($first_period_stock==0){

						$first_period_stock = $this->site->getLastendPeriodStock((isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$brId);
						}

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('First_period_stock'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($first_period_stock)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
				}



				$row++;


				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_acct);
				$acc = (array) $acc[0];

				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					/*          echo '
						                <tr>
							                <td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							                ';

	                                       */

					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$purchases_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'], $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($purchases_acctBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11);
						/* echo '
							                <td class="text-right">'.$this->sma->formatDecimal(abs($purchases_acctBalance[$icount])).'</td>
							                ';
                                      */
						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));



						$icount++;
					}
				} else {

					$purchases_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);


					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($purchases_acctBalance)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11);
					/*
                                         echo '
						                <tr>
							                <td>'.($this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']).'</td>
							                <td class="text-right">'.$this->sma->formatDecimal(abs($purchases_acctBalance)).'</td>
							                </tr>
			                          ';
                                      */
				}




				$row++;





				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_return_acct);
				$acc = (array) $acc[0];


				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);





					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$purchases_return_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'], $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($purchases_return_acctBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {

					$purchases_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($purchases_return_acctBalance)));

					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11);
				}


				$row++;






				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('net_purchases'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$net_purchases[$icount] = $this->sma->formatDecimal(($purchases_acctBalance[$icount])) + $this->sma->formatDecimal(($purchases_return_acctBalance[$icount]));

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($net_purchases[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {
					$net_purchases = $this->sma->formatDecimal(($purchases_acctBalance)) + $this->sma->formatDecimal(($purchases_return_acctBalance));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('net_purchases'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($net_purchases)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
				}





				$row++;




				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Goods_available_for_sale'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$goods_available_for_sale[$icount] = $this->sma->formatDecimal(($first_period_stock[$icount])) + $this->sma->formatDecimal(($net_purchases[$icount]));

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($goods_available_for_sale[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {
					$goods_available_for_sale = $this->sma->formatDecimal(($first_period_stock)) + $this->sma->formatDecimal(($net_purchases));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Goods_available_for_sale'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($goods_available_for_sale)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
				}





				$row++;




				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('End-of-period_stock'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$total_end_of_period_stock=0;
	$warehouses=$this->site->getAllWarehouses(($_GET['brId'] ? 1 : null),($_GET['brId'] ? 1 : null),($_GET['brId'] ? $_GET['brId'] : null));
	foreach($warehouses as $warehouse){
						$end_of_period_stock_arr[$icount] = $this->site->getEndPeriodForWarehousesV2($effectiveStartDate.' 00:00:00',$effectiveEndDate.' 23:59:58',$warehouse->id,($_GET['brId'] ? 1 : null),($_GET['brId'] ? 1 : null),($_GET['brId'] ? $_GET['brId'] : null));

	foreach($end_of_period_stock_arr[$icount] as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
	}
						$end_of_period_stock[$icount] = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));

						//$end_of_period_stock[$icount] = $this->sma->formatDecimal(($first_period_stock[$icount])+ ($end_of_period_stock[$icount]));
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($end_of_period_stock[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {
					$total_end_of_period_stock=0;
					$warehouses=$this->site->getAllWarehouses(($_GET['brId'] ? 1 : null),($_GET['brId'] ? 1 : null),($_GET['brId'] ? $_GET['brId'] : null));
					foreach($warehouses as $warehouse){
					$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2((isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),$warehouse->id,($_GET['brId'] ? 1 : null),($_GET['brId'] ? 1 : null),($_GET['brId'] ? $_GET['brId'] : null));

	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
}
	$end_of_period_stock = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));

					//$end_of_period_stock = $this->sma->formatDecimal(($first_period_stock)+ ($end_of_period_stock));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('End-of-period_stock'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($end_of_period_stock)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
				}



				$row++;






				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Cost_of_Goods_Sold'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$sales_cost_of_good_sold_acctBalance[$icount] = $this->sma->formatDecimal(($first_period_stock[$icount])) - $this->sma->formatDecimal(($end_of_period_stock[$icount]));

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($sales_cost_of_good_sold_acctBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {
					$sales_cost_of_good_sold_acctBalance = $this->sma->formatDecimal(($goods_available_for_sale)) - $this->sma->formatDecimal(($end_of_period_stock));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Cost_of_Goods_Sold'));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($sales_cost_of_good_sold_acctBalance)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
				}





				$row++;
			} else {





				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
				$acc = (array) $acc[0];

				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$sales_cost_of_good_sold_acctBalance[$icount] = $this->accounts_model->getAccountTotal($acc['id'], $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($sales_cost_of_good_sold_acctBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {

					$sales_cost_of_good_sold_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $acc['accountName'] : $acc['accountNameEn']);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($sales_cost_of_good_sold_acctBalance)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
				}

				$row++;
			}


			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Gross_income'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$Gross_income[$icount] = $this->sma->formatDecimal(($net_sales[$icount])) + $this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance[$icount]));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($Gross_income[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {
				$gross_income = $this->sma->formatDecimal(($net_sales)) + $this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance));

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Gross_income'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($gross_income)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}

			$row++;




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, '');


					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
				}
			} else {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '');
			}


			$row++;





			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Administrative_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);


				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, '');

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
				}
			} else {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Administrative_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '');
			}



			// 	case $this->startsWith($accountID, '100'):


			$row++;
			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
				//$FStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$LEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$FStartDate = date('Y-m-d', strtotime('+1 days', strtotime($LEndDate)));
				$FStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($FStartDate)));
				for ($i = 1; $i < $_GET['compare_periods']; $i++) {

					$FStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($FStartDate)));
				}
				//echo $FStartDate;

			}



			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
			$accountChilds_levels = $accountChilds;

			$accountChilds = (array) $accountChilds[0];
			/*if($levels && $levels==2)
				                    $accountCategory=$accountChilds_levels;
			                    else*/
			//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID'],$levels);
			$accountCategory = $this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'], $levels);


			foreach ($accountCategory as $account) {


				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
					$accountAllBalance = $this->accounts_model->getAccountTotal($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance = $accountAllBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}


					if ($accountAllBalance == 0) {
						continue;
					}


					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);



					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

						if ($levels) {
							if ($levels == $account->level) {
								$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',$grpId,$coId,$brId);

								//if ($accountBalance)
								//$this->sma->print_arrays($accountCategory2);
								$accountBalance[$icount] = $accountBalance[$icount] + $accountCategory2['accountBalance'];
								//echo $accountBalance."<br>";
							}
						}
						$opexpensesTotalWD[$icount] += $accountBalance[$icount];
						//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($accountBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11);
						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {

					//print_r($account);
					if (!empty($_GET)) {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					} else {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					}


					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}



					if ($accountBalance == 0) {
						continue;
					}

					$opexpensesTotal += $accountBalance;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($accountBalance)));
				}

				$row++;
			}




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Administrative_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);



				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($opexpensesTotalWD[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);


					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Administrative_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($opexpensesTotal)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}

			$row++;




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Selling_And_Markiting_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);


				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, '');

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
				}
			} else {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Selling_And_Markiting_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '');
			}


			$row++;


			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);

			$accountChilds_levels = $accountChilds;


			$accountChilds = (array) $accountChilds[0];

			//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID'],$levels);
			$accountCategory = $this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'], $levels);


			foreach ($accountCategory as $account) {

				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$accountAllBalance = $this->accounts_model->getAccountTotal($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance = $accountAllBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}

					if ($accountAllBalance == 0) {
						continue;
					}


					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);


						if ($levels) {
							if ($levels == $account->level) {
								$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',$grpId,$coId,$brId);

								//if ($accountBalance)
								//$this->sma->print_arrays($accountCategory2);
								$accountBalance[$icount] = $accountBalance[$icount] + $accountCategory2['accountBalance'];
								//echo $accountBalance."<br>";
							}
						}




						$maexpensesTotalWD[$icount] += $accountBalance[$icount];
						//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($accountBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11);


						//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {




					if (!empty($_GET)) {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					} else {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					}



					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}


					if ($accountBalance == 0) {
						continue;
					}

					$maexpensesTotal += $accountBalance;
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($accountBalance)));
				}
				$row++;
			}



			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Selling_And_Markiting_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);


				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($maexpensesTotalWD[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);



					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {


				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Selling_And_Markiting_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($maexpensesTotal)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}

			$row++;




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);



				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($opexpensesTotalWD[$icount]) + abs($maexpensesTotalWD[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($opexpensesTotalWD[$icount]) + abs($maexpensesTotalWD[$icount])));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}


			$row++;


			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Net_operating_income'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);



				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$net_operating_income[$icount] = $this->sma->formatDecimal(($Gross_income[$icount]) + (($opexpensesTotalWD[$icount]) + ($maexpensesTotalWD[$icount])));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($net_operating_income[$icount])));

					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);


					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {

				$Net_operating_income = $this->sma->formatDecimal(($gross_income) + (($opexpensesTotal) + ($maexpensesTotal)));


				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Net_operating_income'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($Net_operating_income)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}

			$row++;




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Other_Revenues'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);


				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, '');

					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
				}
			} else {


				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Other_Revenues'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '');
			}

			$row++;



			$otherRevenuesAccounts = $this->accounts_model->getOtherRevenuesAccounts($levels);
			//print_r($otherRevenuesAccounts);

			foreach ($otherRevenuesAccounts as $account) {
				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$accountAllBalance = $this->accounts_model->getAccountTotal($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);


					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance = $accountAllBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}
					if ($accountAllBalance == 0) {
						continue;
					}


					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);







					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);

						if ($levels) {
							if ($levels == $account->level) {
								$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',$grpId,$coId,$brId);

								//if ($accountBalance)
								//$this->sma->print_arrays($accountCategory2);
								$accountBalance[$icount] = $accountBalance[$icount] + $accountCategory2['accountBalance'];
								//echo $accountBalance."<br>";
							}
						}

						$orevenueTotalWD[$icount] += $accountBalance[$icount];
						//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';

						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($accountBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11);



						//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {

					if (!empty($_GET)) {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					} else {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					}

					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}


					if ($accountBalance == 0) {
						continue;
					}

					$orevenueTotal += $accountBalance;

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);



					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($accountBalance)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11);
				}

				$row++;
			}




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Other_Revenues'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);



				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($orevenueTotalWD[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);

					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Other_Revenues'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($orevenueTotal)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}





			$row++;




			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Other_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);


				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, '');


					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
				}
			} else {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Other_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '');
			}

			$row++;

			$accounts_operating_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
			$accounts_operating_expenses_acct = (array) $accounts_operating_expenses_acct[0];


			$accounts_marketing_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
			$accounts_marketing_expenses_acct = (array) $accounts_marketing_expenses_acct[0];


			$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccounts($accounts_operating_expenses_acct['accountID'], $accounts_marketing_expenses_acct['accountID'], $levels,$period_year);

			foreach ($otherExpensesAccounts as $account) {
				if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

					$accountAllBalance = $this->accounts_model->getAccountTotal($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);


					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $FStartDate . ' 00:00:00', $LEndDate . ' 23:59:58',$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountAllBalance = $accountAllBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}
					if ($accountAllBalance == 0) {
						continue;
					}


					//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
					$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
					$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));





					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);


					$icount = 0;
					for ($i = 1; $i <= $_GET['compare_periods']; $i++) {
						$accountBalance[$icount] = $this->accounts_model->getAccountTotal($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',NULL,$grpId,$coId,$brId);



						if ($levels) {
							if ($levels == $account->level) {
								$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, $effectiveStartDate . ' 00:00:00', $effectiveEndDate . ' 23:59:58',$grpId,$coId,$brId);

								//if ($accountBalance)
								//$this->sma->print_arrays($accountCategory2);
								$accountBalance[$icount] = $accountBalance[$icount] + $accountCategory2['accountBalance'];
								//echo $accountBalance."<br>";
							}
						}

						$oexpensesTotalWD[$icount] += $accountBalance[$icount];
						//echo ($opexpensesTotal[$icount]);//[$icount].' '.$icount.'<br>';
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($accountBalance[$icount])));
						$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11);


						//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 years")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-3 months")
							$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 weeks")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
						if ($_GET['compared_to'] == "-1 days")
							$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
						$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
						$icount++;
					}
				} else {

					if (!empty($_GET)) {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					} else {
						$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL,$grpId,$coId,$brId);
					}

					if ($levels) {
						if ($levels == $account->level) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),$grpId,$coId,$brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}

					if ($accountBalance == 0) {
						continue;
					}

					$oexpensesTotal += $accountBalance;

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11);

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($accountBalance)));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11);
				}
				$row++;
			}



			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Other_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);



				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs($oexpensesTotalWD[$icount])));
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF11Bold);


					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Total') . ' ' . lang('Other_Expenses'));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF11Bold);

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs($oexpensesTotal)));
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF11Bold);
			}




			$row++;



			$styleArrayBorder = array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THICK
					)
				)
			);





			if ($_GET['compare_periods'] && $_GET['compared_to'] != '0') {

				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Net_Income'));
				//  $this->excel->getActiveSheet()->getStyle(0,$row)->getFont()->setSize(15);
				//    $this->excel->getActiveSheet()->getStyle(0,$row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF13);


				//$effectiveStartDate = (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) : '');
				$effectiveEndDate = (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) : '');
				$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
				$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));


				$icount = 0;
				for ($i = 1; $i <= $_GET['compare_periods']; $i++) {

					if ((($net_operating_income[$icount]) + ($orevenueTotalWD[$icount]) + ($oexpensesTotalWD[$icount])) > 0) {
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, "(" . $this->sma->formatDecimal(abs(($net_operating_income[$icount]) + ($orevenueTotalWD[$icount]) + ($oexpensesTotalWD[$icount]))) . ")");
					} else {
						$this->excel->getActiveSheet()->setCellValueByColumnAndRow($i, $row, $this->sma->formatDecimal(abs(($net_operating_income[$icount]) + ($orevenueTotalWD[$icount]) + ($oexpensesTotalWD[$icount]))));
					}
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayF13);
					//   $this->excel->getActiveSheet()->getStyle($i,$row)->getFont()->setSize(15);
					//  $this->excel->getActiveSheet()->getStyle($i,$row)->getFont()->setBold(true);
					$this->excel->getActiveSheet()->getStyleByColumnAndRow($i, $row)->applyFromArray($styleArrayBorder);


					//$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					$effectiveStartDate = date('Y-m-d', strtotime($_GET['compared_to'], strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 years")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-3 months")
						$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 weeks")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
					if ($_GET['compared_to'] == "-1 days")
						$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
					$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
					$icount++;
				}
			} else {
				$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, lang('Net_Income'));
				//  $this->excel->getActiveSheet()->getStyle(0,$row)->getFont()->setSize(15);
				//    $this->excel->getActiveSheet()->getStyle(0,$row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(0, $row)->applyFromArray($styleArrayF13);


				if ((($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal)) > 0) {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, "(" . $this->sma->formatDecimal(abs(($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal))) . ")");
				} else {
					$this->excel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $this->sma->formatDecimal(abs(($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal))));
				}

				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayF13);
				$this->excel->getActiveSheet()->getStyleByColumnAndRow(1, $row)->applyFromArray($styleArrayBorder);
			}

			for ($i = 'A'; $i <=  $this->excel->getActiveSheet()->getHighestColumn(); $i++) {
				$this->excel->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
			}


			$filename = lang('Income_Statement');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$levels    = $this->input->post('levels') ? $this->input->post('levels') : 0;


			//$this->sma->print_arrays($levels);
			if ($levels == 0)
				$this->data['accountList'] = $this->accounts_model->getincomeStatementAccounts();
			else
				$this->data['accountList'] = $this->accounts_model->getincomeStatementAccounts(NULL, $levels);

			//$this->data['accountList'] = $this->accounts_model->getAccountsNotZeros();
			//$data['userData']   = $this->session->userdata();
			//$this->data['entryList']  = $this->accounts_model->getEntries();

			//$this->sma->print_arrays($this->data['accountList']);

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Income_Statement'), 'page' => lang('Income_Statement')], ['link' => '#', 'page' => lang('Income_Statement')]];
			$meta                = ['page_title' => lang('Income_Statement'), 'bc' => $bc];
			$this->page_construct('accounts/incomestatement', $meta, $this->data);
		}
	}
	public function balancesheet($pdf = null, $xls = null)
	{
		$this->sma->checkPermissions('balancesheet', true);
		$dbName=str_replace("cpesoft_","",$this->db->database);
        if($dbName=="ghazeel"){
			ini_set('max_execution_time', 600);
		}else{
			ini_set('max_execution_time', 300);
		}
			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			//$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));
		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		if ($_POST)
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->input->post('period_year')) : (object)['period_from_date' => $this->input->post('period_year') . '-01-01', 'period_to_date' => $this->input->post('period_year') . '-12-31'];
		else
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];

		$levels    = $this->input->post('levels') ? $this->input->post('levels') : 0;
		$this->data['accountList'] = $this->accounts_model->getbalanceSheetAccountsLiabilities(NULL, $levels);

		if ($pdf || $xls) {
			$levels    = $this->input->get('levels') ? $this->input->get('levels') : 0;

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			//$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));
			$period_year    = $this->input->get('period_year') ? $this->input->get('period_year') : null;
			//$this->sma->print_arrays($period_year);
			if ($levels == 0)
				$this->data['accountList'] = $this->accounts_model->getbalanceSheetAccountsLiabilities();
			else
				$this->data['accountList'] = $this->accounts_model->getbalanceSheetAccountsLiabilities(NULL, $levels);

			$accountList = $this->data['accountList'];
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);
			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Balance_Sheet'));
			$row = 1;
			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Balance_Sheet'));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 20,
					'name'  => 'Cairo-Bold'
				)

			);
			$fillColor = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)
			);

			$Font11Bold = array(
				'font' => array(
					'bold' => true,
					'size' => 11,
					'name'  => 'Cairo-Bold'
				)

			);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($styleArray);
			$row++;

			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

			$row++;

			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':B' . $row);
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Balance_Sheet') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
			$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($fillColor);
			$row++;

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Account'));
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang('Amount'));


			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($fillColor);
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
			$row++;


			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




			$revenueTotal  = 0;
			$orevenueTotal  = 0;
			$expensesTotal = 0;
			$opexpensesTotal = 0;
			$maexpensesTotal = 0;
			$oexpensesTotal = 0;



			$accountsIS = array();


			foreach ($accountList as $account) {
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])) {
					$accounts[$account['accountCategory']] = array($account);
				} else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}

			$accountCategories = array_keys($accounts);
			$accountOrder = 0;




			$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_acct);
			$acc = (array) $acc[0];

			$sales_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);



			$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_return_acct);
			$acc = (array) $acc[0];

			$sales_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);


			$net_sales = $this->sma->formatDecimal(($sales_acctBalance)) + $this->sma->formatDecimal(($sales_return_acctBalance));


			if ($Settings->accounting_type == 'periodic' && $period_year!=2022) {







				$first_period_stock = $this->site->getOpeningBalanceForWarehouses((isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),null,$grpId,$coId,$brId)->first_period_stock;
				if($first_period_stock==0){

					$first_period_stock = $this->site->getLastendPeriodStock((isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']).' 00:00:00' : ''),(isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']).' 23:59:58' : ''),$brId);
					}


				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_acct);
				$acc = (array) $acc[0];

				$purchases_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);


				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_return_acct);
				$acc = (array) $acc[0];



				$purchases_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);



				$net_purchases = $this->sma->formatDecimal(($purchases_acctBalance)) + $this->sma->formatDecimal(($purchases_return_acctBalance));








				$goods_available_for_sale = $this->sma->formatDecimal(($first_period_stock)) + $this->sma->formatDecimal(($net_purchases));




				$total_end_of_period_stock=0;
				$warehouses=$this->site->getAllWarehouses(($_GET['brId'] ? 1 : null),($_GET['brId'] ? 1 : null),($_GET['brId'] ? $_GET['brId'] : null));
				foreach($warehouses as $warehouse){
				$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2((isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),$warehouse->id,($_POST['brId'] ? 1 : null),($_POST['brId'] ? 1 : null),($_POST['brId'] ? $_POST['brId'] : null));

	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
}
	$end_of_period_stock = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));

				//$end_of_period_stock = $this->sma->formatDecimal(($first_period_stock)+ ($end_of_period_stock));




				$sales_cost_of_good_sold_acctBalance = $this->sma->formatDecimal(($goods_available_for_sale)) - $this->sma->formatDecimal(($end_of_period_stock));
			} else {

				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
				$acc = (array) $acc[0];

				$sales_cost_of_good_sold_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
			}



			$Gross_income = $this->sma->formatDecimal(($net_sales)) + $this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance));




			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory = $this->accounts_model->getAccountChilds($accountChilds['accountID']);
			foreach ($accountCategory as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				}



				if ($accountBalance == 0) {
					continue;
				}

				$opexpensesTotal += $accountBalance;
			}







			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory = $this->accounts_model->getAccountChilds($accountChilds['accountID']);
			foreach ($accountCategory as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				}



				if ($accountBalance == 0) {
					continue;
				}

				$maexpensesTotal += $accountBalance;
			}



			$Net_operating_income = $this->sma->formatDecimal(($Gross_income) + (($opexpensesTotal) + ($maexpensesTotal)));



			$otherRevenuesAccounts = $this->accounts_model->getOtherRevenuesAccounts();
			//print_r($this->site->getAccountsSettings()->sales_acct);

			foreach ($otherRevenuesAccounts as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				}



				if ($accountBalance == 0) {
					continue;
				}

				$orevenueTotal += $accountBalance;
			}



			$accounts_operating_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
			$accounts_operating_expenses_acct = (array) $accounts_operating_expenses_acct[0];


			$accounts_marketing_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
			$accounts_marketing_expenses_acct = (array) $accounts_marketing_expenses_acct[0];


			$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccounts($accounts_operating_expenses_acct['accountID'], $accounts_marketing_expenses_acct['accountID'],$period_year);

			foreach ($otherExpensesAccounts as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:58' : ''),NULL, $grpId, $coId, $brId);
				}



				if ($accountBalance == 0) {
					continue;
				}

				$oexpensesTotal += $accountBalance;
			}



			$accounts = array();


			foreach ($accountList as $account) {
				$account = (array) $account;

				if (!isset($accounts[$account['accountCategory']])) {
					$accounts[$account['accountCategory']] = array($account);
				} else {
					array_push($accounts[$account['accountCategory']], $account);
				}
			}
			$accountCategories = array_keys($accounts);



			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Assets') . ':');
			$this->excel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($Font11Bold);


			$row++;
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Fixed_Assets'));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($Font11Bold);

			$row++;


			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_fixed_assets_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory = $this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'], $levels);
			//$accountCategory=$this->accounts_model->getAccountChilds($accountChilds['accountID']);
			foreach ($accountCategory as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
				}

				if ($levels) {
					if ($levels == $account->level) {
						$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''), $grpId, $coId, $brId);

						//if ($accountBalance)
						//$this->sma->print_arrays($accountCategory2);
						$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
					}
				}



				if ($accountBalance == 0) {
					continue;
				}

				$fassetsTotal += $accountBalance;

				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($accountBalance)));
				//$this->excel->getActiveSheet()->getStyle('A'.$row.':B'.$row)->applyFromArray($Font11Bold);


				$row++;
			}
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Total') . ' ' . lang('Fixed_Assets'));
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($fassetsTotal)));
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
			$this->excel->getActiveSheet()->getStyle('B' . $row)->getBorders()
				->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			//$this->excel->getActiveSheet()->getFont('B' . $row)->setUnderline(true);

			$row++;

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Current_Assets'));
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
			$row++;


			$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_current_assets_acct);
			$accountChilds = (array) $accountChilds[0];

			$accountCategory = $this->accounts_model->getAccountChildsWithLevels($accountChilds['accountID'], $levels);
			foreach ($accountCategory as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
				}

				if ($levels) {
					if ($levels == $account->level) {
						$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''), $grpId, $coId, $brId);

						//if ($accountBalance)
						//$this->sma->print_arrays($accountCategory2);
						$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
						//echo $accountBalance."<br>";
					}
				}



				if ($accountBalance == 0) {
					continue;
				}

				$cassetsTotal += $accountBalance;

				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($accountBalance)));
				$row++;
			}

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Total') . ' ' . lang('Current_Assets'));
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($cassetsTotal)));
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
			$row++;
			$otherAssetsAccounts = $this->accounts_model->getOtherAssetsAccounts();

			foreach ($otherAssetsAccounts as $account) {

				if (!empty($_GET)) {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
				} else {
					$accountBalance = $this->accounts_model->getAccountTotal($account->id, (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
				}



				if ($accountBalance == 0) {
					continue;
				}

				$oassetsTotal += $accountBalance;

				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->Settings->user_language == 'arabic' ? $account->accountName : $account->accountNameEn);
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($accountBalance)));
				$row++;
			}
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Total') . ' ' . lang('Other_Assets'));
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($oassetsTotal)));
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
			$row++;


			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Total') . ' ' . lang('Assets'));
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs(($fassetsTotal) + ($cassetsTotal) + ($oassetsTotal)) - abs($provisionsTotal)));
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
			$row++;

			$accountOrder = 0;
			foreach ($accounts as $accountCategory) {
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $accountCategories[$accountOrder] . ':');
				$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);
				$row++;

				$moneySign = '';
				$accountSubCategory = NULL;
				$subTotal = 0;


				foreach ($accountCategory as $account) {
					if (!empty($_GET)) {
						//$_GET['startDate'] = '2020-01-01';
						$accountBalance = $this->accounts_model->getAccountTotal($account['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
					} else {
						$accountBalance = $this->accounts_model->getAccountTotal($account['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''),NULL, $grpId, $coId, $brId);
					}


					if ($levels) {
						if ($levels == $account['level']) {
							$accountCategory2 = $this->accounts_model->getAccountItemByLevel($account['id'], (isset($_GET['start_date']) ? $this->sma->fld($_GET['start_date']) . ' 00:00:00' : ''), (isset($_GET['end_date']) ? $this->sma->fld($_GET['end_date']) . ' 23:59:59' : ''), $grpId, $coId, $brId);

							//if ($accountBalance)
							//$this->sma->print_arrays($accountCategory2);
							$accountBalance = $accountBalance + $accountCategory2['accountBalance'];
							//echo $accountBalance."<br>";
						}
					}

					/*$accountCategoryprovisions=$this->accounts_model->isAccountChildsWithLevels($accountChilds['accountID'],$account['accountID'],$levels);
					if ($accountCategoryprovisions){
						continue;
					}*/
					if ($accountBalance == 0) {
						continue;
					}

					if ($account['accountCategory'] == 'الإلتزامات وحقوق الملكية') {
						$liabilitiesTotal += $accountBalance;
					} else {
						$equityTotal += $accountBalance;
					}

					$subTotal += $accountBalance;
					if ($accountSubCategory != $account['accountCategorySub']) {
						$accountSubCategory = $account['accountCategorySub'];
						$this->excel->getActiveSheet()->SetCellValue('A' . $row, $account);
						$row++;
						$subTotal = 0;
					}
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->Settings->user_language == 'arabic' ? $account['accountName'] : $account['accountNameEn']);
					//$this->excel->getActiveSheet()->SetCellValue('B'.$row, $accountBalance);


					if ($accountBalance != abs($accountBalance)) {
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($accountBalance)));
					} else {
						$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(($accountBalance)));
						//	 $this->sma->formatDecimal($accountBalance);
					}
					$row++;
					$moneySign = '';
				}
				if ($account['accountCategory'] == 'الإلتزامات وحقوق الملكية') {
					if ($liabilitiesTotal != abs($liabilitiesTotal)) {
						$accountAmount = $this->sma->formatDecimal(abs($liabilitiesTotal));
					} else {
						$accountAmount = $this->sma->formatDecimal($liabilitiesTotal);
					}
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Total') . ' ' . $accountCategories[$accountOrder]);
					$this->excel->getActiveSheet()->SetCellValue('B' . $row, $accountAmount);
					$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);

					$row++;
				}
				$accountOrder += 1;
			}
			$Final_Net_Income = ($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal);

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Net_Income'));
			if ($Final_Net_Income > 0)
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, ' ( ' . $this->sma->formatDecimal(abs($Final_Net_Income)) . ' ) ');
			else
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs($Final_Net_Income)));

			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);

			$row++;

			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Total'));
			$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->formatDecimal(abs(($liabilitiesTotal) + ($Final_Net_Income))));
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':B' . $row)->applyFromArray($Font11Bold);

			$row++;

			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);

			$this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			//   $this->excel->getActiveSheet()->getStyle('C2:D' . $row)->getAlignment()->setWrapText(true);
			$filename = 'Balance_Sheet';
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Balance_Sheet'), 'page' => lang('Balance_Sheet')], ['link' => '#', 'page' => lang('Balance_Sheet')]];
			$meta                = ['page_title' => lang('Balance_Sheet'), 'bc' => $bc];
			$this->page_construct('accounts/balancesheet', $meta, $this->data);
		}
	}

	/*public function ledgers_temp($id = NULL)
    {
        if ($id){
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);

			}
			else {
				$this->data['accountList'] = $this->accounts_model->getAccounts();
			}
			//$data['userData']   = $this->session->userdata();
			$this->data['entryList']  = $this->accounts_model->getEntries();
			$this->data['filter_id']  = $id;

		//$this->sma->print_arrays($this->data['filter_id']);


        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Ledgers'), 'page' => lang('Ledgers')], ['link' => '#', 'page' => lang('Ledgers')]];
        $meta                = ['page_title' => lang('Ledgers'), 'bc' => $bc];
        $this->page_construct('accounts/ledgers_temp', $meta, $this->data);


    }*/


	public function getReceipts($warehouse_id = null)
	{
		//$this->sma->checkPermissions('adjustments');

		/*$delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_receipt') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('accounts/delete_receipt/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";
        */

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

			$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
			$bond_no       = $this->input->get('bond_no') ? $this->input->get('bond_no') : null;
			$reference_no       = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;

		if ($start_date) {
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		}

		$this->load->library('datatables');
		if ($this->Settings->user_language == 'arabic') {

			$this->datatables

				->select("{$this->db->dbprefix('gl_receipts')}.id as id, bond_no, reference_no, date, pm1.accountName  as fromAccountID, pm2.accountName as toAccountID, total_amount,paid_by, attachment,fromAccountText,toAccountText")

				->from('gl_receipts')
				//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_receipts.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_receipts.toAccountID', 'left');
		} else {
			$this->datatables

				->select("{$this->db->dbprefix('gl_receipts')}.id as id, bond_no, reference_no, date, pm1.accountNameEn   as fromAccountID, pm2.accountNameEn as toAccountID, total_amount,paid_by, attachment,fromAccountText,toAccountText")

				->from('gl_receipts')
				//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_receipts.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_receipts.toAccountID', 'left');
		}

		if ($grpId) {
			$this->datatables->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->datatables->where('coId', $coId);
		}
		if ($brId) {
			$this->datatables->where('brId', $brId);
		}
		if ($start_date && $end_date) {
			$this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}
		if ($bond_no) {
			$this->datatables->where('bond_no', $bond_no);
		}
		if ($reference_no) {
			$this->datatables->where('reference_no', $reference_no);
		}

		$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('accounts/edit_receipt/$1') . "'  data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_receipt') . "'><i class='fa fa-edit'></i></a>  " . $delete_link . "</div>", 'id');


		echo $this->datatables->generate();
	}

	public function getCatchReceipts($warehouse_id = null)
	{
		//$this->sma->checkPermissions('adjustments');

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
		$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
		$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
		$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
		$bond_no       = $this->input->get('bond_no') ? $this->input->get('bond_no') : null;
		$reference_no       = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;

		if ($start_date) {
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		}


		$this->load->library('datatables');
		if ($this->Settings->user_language == 'arabic') {

			$this->datatables

				->select("{$this->db->dbprefix('gl_catch_receipts')}.id as id, bond_no, reference_no, date, pm1.accountName as fromAccountID, pm2.accountName as toAccountID, salesman_name, total_amount,paid_by, attachment,fromAccountText,toAccountText")

				->from('gl_catch_receipts')
				//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_catch_receipts.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_catch_receipts.toAccountID', 'left');
		} else {
			$this->datatables

				->select("{$this->db->dbprefix('gl_catch_receipts')}.id as id, bond_no, reference_no, date, pm1.accountNameEn as fromAccountID, pm2.accountNameEn as toAccountID, salesman_name, total_amount,paid_by, attachment,fromAccountText,toAccountText")

				->from('gl_catch_receipts')
				//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_catch_receipts.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_catch_receipts.toAccountID', 'left');
		}

		if ($grpId) {
			$this->datatables->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->datatables->where('coId', $coId);
		}
		if ($brId) {
			$this->datatables->where('brId', $brId);
		}
		if ($start_date && $end_date) {
			$this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}
		if ($bond_no) {
			$this->datatables->where('bond_no', $bond_no);
		}
		if ($reference_no) {
			$this->datatables->where('reference_no', $reference_no);
		}

		$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('accounts/edit_catch_receipt/$1') . "'   data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_catch_receipt') . "'><i class='fa fa-edit'></i></a>  " . $delete_link . "</div>", 'id');


		echo $this->datatables->generate();
	}

	public function getRefundingBonds($warehouse_id = null)
	{
		//$this->sma->checkPermissions('adjustments');

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
		$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
		$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
		$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
		$bond_no       = $this->input->get('bond_no') ? $this->input->get('bond_no') : null;
		$reference_no       = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;

		if ($start_date) {
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		}

		$this->load->library('datatables');
		if ($this->Settings->user_language == 'arabic') {

			$this->datatables

				->select("{$this->db->dbprefix('gl_refunding_bonds')}.id as id, bond_no, reference_no, date, pm1.accountName as fromAccountID, pm2.accountName as toAccountID, total_amount,paid_by, attachment")

				->from('gl_refunding_bonds')
				//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_refunding_bonds.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_refunding_bonds.toAccountID', 'left');
		} else {
			$this->datatables

				->select("{$this->db->dbprefix('gl_refunding_bonds')}.id as id, bond_no, reference_no, date, pm1.accountNameEn as fromAccountID, pm2.accountNameEn as toAccountID, total_amount,paid_by, attachment")

				->from('gl_refunding_bonds')
				//->join('warehouses', 'warehouses.id=adjustments.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_refunding_bonds.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_refunding_bonds.toAccountID', 'left');
		}
		if ($grpId) {
			$this->datatables->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->datatables->where('coId', $coId);
		}
		if ($brId) {
			$this->datatables->where('brId', $brId);
		}
		if ($start_date && $end_date) {
			$this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}
		if ($bond_no) {
			$this->datatables->where('bond_no', $bond_no);
		}
		if ($reference_no) {
			$this->datatables->where('reference_no', $reference_no);
		}

		$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('accounts/edit_refunding_bonds/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_refunding_bonds') . "'><i class='fa fa-edit'></i></a>  " . $delete_link . "</div>", 'id');


		echo $this->datatables->generate();
	}



	public function edit_receipt($id = null)
	{
		$this->sma->checkPermissions('edit_receipt', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getReceiptByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'required');
			$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Spend_from'), 'required');


			if ($this->form_validation->run() == true) {
				//       if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//      } else {
				//         $date = $receipt->date;
				//    }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}

				$note         = $this->sma->clear_tags($this->input->post('note'));



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);













				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => $this->input->post('toAccountID'),
					'fromAccountID'         => $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],

				];

				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);



				// $this->sma->print_arrays($data, $products);
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateReceipt($id, $data)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Receipt_successfully_updated'));
				admin_redirect('accounts/receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();


				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondreceipt();


				$this->data['bond_no'] = $this->accounts_model->getMaxBondreceipt();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $this->accounts_model->getAllAccounts();
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;
				//$this->sma->print_arrays($receipt);
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['modal_js']   = $this->site->modal_js();
				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_receipt'), 'page' => lang('Edit_receipt')], ['link' => '#', 'page' => lang('Edit_receipt')]];
				$meta                           = ['page_title' => lang('Edit_receipt'), 'bc' => $bc];
				if($receipt->fromAccountID==0)
					$this->load->view($this->theme . 'accounts/edit_receiptV3', $this->data);
				else if($receipt->toAccountID==0)
					$this->load->view($this->theme . 'accounts/edit_receiptV2', $this->data);
				else
					$this->load->view($this->theme . 'accounts/edit_receipt', $this->data);



				//$this->page_construct('accounts/edit_receipt', $meta, $this->data);
			}
		}
	}

	public function edit_catch_receipt($id = null)
	{
		$this->sma->checkPermissions('edit_catch_receipt', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getCatchReceiptByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'required');
			$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Received_from'), 'required');


			if ($this->form_validation->run() == true) {
				//    if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//  } else {
				//      $date = $receipt->date;
				// }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}

				$note         = $this->sma->clear_tags($this->input->post('note'));



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);













				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => $this->input->post('toAccountID'),
					'fromAccountID'         => $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],


				];


				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);



				// $this->sma->print_arrays($data, $products);
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateCatchReceipt($id, $data)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Catch_receipt_successfully_updated'));
				admin_redirect('accounts/catch_receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();



				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondCatchreceipt();

				//$this->sma->print_arrays($receipt);


				$this->data['bond_no'] = $this->accounts_model->getMaxBondCatchreceipt();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $this->accounts_model->getAllAccounts();
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;

				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();


				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_catch_receipt'), 'page' => lang('Edit_catch_receipt')], ['link' => '#', 'page' => lang('Edit_catch_receipt')]];
				$meta                           = ['page_title' => lang('Edit_catch_receipt'), 'bc' => $bc];
				if($receipt->fromAccountID==0)
					$this->load->view($this->theme . 'accounts/edit_catch_receiptV3', $this->data);
				else if($receipt->toAccountID==0)
					$this->load->view($this->theme . 'accounts/edit_catch_receiptV2', $this->data);
				else
					$this->load->view($this->theme . 'accounts/edit_catch_receipt', $this->data);
				//$this->page_construct('accounts/edit_catch_receipt', $meta, $this->data);
			}
		}
	}



	public function edit_refunding_bonds($id = null)
	{
		$this->sma->checkPermissions('edit_refunding_bonds', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getRefundingBondByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			if ($this->input->post('bond_no') != $receipt->bond_no) {
				$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_refunding_bonds.bond_no]|required');
			}
			$this->form_validation->set_rules('toAccountID', lang('The_account_transferred_to'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('The_account_transferred_from'), 'required');


			if ($this->form_validation->run() == true) {
				//    if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//    } else {
				//        $date = $receipt->date;
				//    }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



				//$note         = $this->sma->clear_tags($this->input->post('note'));
				$note         = $_POST['note'];


				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);













				/*$data = [
                'date'         => $date,
                'bond_no'         => $this->input->post('bond_no'),
                'reference_no'         => $this->input->post('reference_no'),
                'amount'         => $this->sma->formatDecimal($this->input->post('amount'),4),
                'tax_rate'         => $this->input->post('tax_rate'),
                'tax_amount'         => $this->input->post('tax_amount'),
                'total_amount'         => $this->input->post('total_amount'),
                'paid_by'         => $this->input->post('paid_by'),
                'cheque_no'         => $cheque_no,
                'cc_type'         => $cc_type,
                'toAccountID'         => $this->input->post('toAccountID'),
                'fromAccountID'         => $payment_type_details->payment_account,
                'receipt_name'         => $this->input->post('receipt_name'),
                'cost_center'         => $this->input->post('cost_center'),
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
            ];*/





				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => $this->input->post('toAccountID'),
					'fromAccountID'         => $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],

				];

				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);



				 //$this->sma->print_arrays($data, $products);
			} elseif ($this->input->post('edit_refunding_bonds')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/refunding_bonds');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateRefundingBond($id, $data,$receipt->bond_no)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Refunding_bond_successfully_updated'));
				admin_redirect('accounts/refunding_bonds');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();


				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondRefundingBond();

				//$this->sma->print_arrays($receipt);


				$this->data['bond_no'] = $this->accounts_model->getMaxBondRefundingBond();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();



				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_refunding_bonds'), 'page' => lang('Edit_refunding_bonds')], ['link' => '#', 'page' => lang('Edit_refunding_bonds')]];
				$meta                           = ['page_title' => lang('Edit_refunding_bonds'), 'bc' => $bc];
				$this->load->view($this->theme . 'accounts/edit_refunding_bonds', $this->data);
				//$this->page_construct('accounts/edit_refunding_bonds', $meta, $this->data);
			}
		}
	}



	public function add_receipt($id = null)
	{
		$this->sma->checkPermissions('add_receipt', true);

		$this->form_validation->set_rules('amount', lang('amount'), 'required');
		$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_receipts.bond_no]|required');


		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : 1;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : 1;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
		$exchange_rate=$this->input->post('exchange_rate');
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {
			/*
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			//$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Spend_from'), 'required');


			if ($this->form_validation->run() == true) {


				//  if ($this->Owner || $this->Admin ) {
				$date = $this->sma->fld($this->input->post('date'));
				//} else {
				//  $date = date('Y-m-d H:s:i');
				// }
				if (!$this->input->post('date'))
					$date = date('Y-m-d H:s:i');

				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/



				$note         = $_POST['note'];

				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



			$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
			$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

			$entryDebitAccount_arr = [];
			foreach ($jsonArr_entryDebitAccount as $product_arr) {
				$entryDebitAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitAccount']);
			$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



			$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
			$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

			$entryDebitBalance_arr = [];
				$equivalent_entryDebitBalance_arr= [];
				foreach ($jsonArr_entryDebitBalance as $product_arr) {
					$entryDebitBalance_arr[] =  $product_arr->value;
					$equivalent_entryDebitBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryDebitBalance']);
				$_POST['entryDebitBalance'] = $entryDebitBalance_arr;
				$_POST['equivalent_entryDebitBalance'] = $equivalent_entryDebitBalance_arr;


			$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
			$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

			$entryDebitDescription_arr = [];
			foreach ($jsonArr_entryDebitDescription as $product_arr) {
				$entryDebitDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitDescription']);
			$_POST['entryDebitDescription'] = $entryDebitDescription_arr;



			$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
			$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

			$costCenterDebitId_arr_0 = [];
			foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
				$costCenterDebitId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterDebitId_0']);
			$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




			$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
			$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

			$costCenterDebitAmount_arr_0 = [];
			foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
				$costCenterDebitAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterDebitAmount_0']);
			$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






			$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
			$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
			$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



			$r                = sizeof($entryDebitAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterDebitId_arr = [];
				foreach ($jsonArr_costCenterDebitId as $product_arr) {
					if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
						$costCenterDebitId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitId_' . $i]);
				$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
				unset($costCenterDebitId_arr);





				//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




				$costCenterDebitAmount_arr = [];
				foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
					if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
						$costCenterDebitAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_' . $i]);
				$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
				unset($costCenterDebitAmount_arr);
			}

			$toAccountText="";
			$costDebitCount = 0;
				foreach ($_POST['entryDebitAccount'] as $key => $acc) {
					if($costDebitCount==0)
					$toAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
					else
					$toAccountText=$toAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
					if ($_POST['costCenterDebitId_' . $costDebitCount]) {
						$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
						$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
					} else {
						$costCenterDebitId[] = "";
						$costCenterDebitAmount[] = "";
					}
					$costDebitCount++;
				}



				////////Credit

				$paid_by=' ';

				$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
			$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

			$entryCreditAccount_arr = [];
			foreach ($jsonArr_entryCreditAccount as $product_arr) {
				$payment_type_details = $this->accounts_model->getPaymentTypeByID($product_arr->value);
				$entryCreditAccount_arr[] =  $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type;

				$fromAccount       = $this->accounts_model->getAccountByID($payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type);
				$fromAccountName   = $fromAccount->accountNameEn ? $fromAccount->accountNameEn :  $fromAccount->accountName;
				$fromAccountName   = $this->Settings->user_language == 'arabic' ? $fromAccount->accountName . ' ' . $cheque_no :  $fromAccountName . ' ' . $cheque_no;

				$paid_by=$paid_by.' - '.($payment_type_details->payment_type=='Cheque' ? lang('cheque').' / '.$fromAccountName : lang($payment_type_details->payment_type).' / '.$fromAccountName);
			}

			unset($_POST['entryCreditAccount']);
			$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



			$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
			$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

			$entryCreditBalance_arr = [];
				$equivalent_entryCreditBalance_arr = [];
				foreach ($jsonArr_entryCreditBalance as $product_arr) {
					$entryCreditBalance_arr[] =  $product_arr->value;
					$equivalent_entryCreditBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryCreditBalance']);
				$_POST['entryCreditBalance'] = $entryCreditBalance_arr;
				$_POST['equivalent_entryCreditBalance'] = $equivalent_entryCreditBalance_arr;


			$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
			$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

			$entryCreditDescription_arr = [];
			foreach ($jsonArr_entryCreditDescription as $product_arr) {
				$entryCreditDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditDescription']);
			$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


			$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
			$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

			$costCenterCreditId_arr_0 = [];
			foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
				$costCenterCreditId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterCreditId_0']);
			$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




			$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
			$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

			$costCenterCreditAmount_arr_0 = [];
			foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
				$costCenterCreditAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterCreditAmount_0']);
			$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






			$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
			$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
			$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



			$r                = sizeof($entryCreditAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterCreditId_arr = [];
				foreach ($jsonArr_costCenterCreditId as $product_arr) {
					if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
						$costCenterCreditId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditId_' . $i]);
				$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
				unset($costCenterCreditId_arr);





				//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




				$costCenterCreditAmount_arr = [];
				foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
					if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
						$costCenterCreditAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_' . $i]);
				$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
				unset($costCenterCreditAmount_arr);
			}

			$fromAccountText="";
			$costCreditCount = 0;
				foreach ($_POST['entryCreditAccount'] as $key => $acc) {
					if($costCreditCount==0)
					$fromAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
					else
					$fromAccountText=$fromAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
					if ($_POST['costCenterCreditId_' . $costCreditCount]) {
						$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
						$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
					} else {
						$costCenterCreditId[] = "";
						$costCenterCreditAmount[] = "";
					}
					$costCreditCount++;
				}



				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $paid_by,
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => 0,//$this->input->post('toAccountID'),
					'fromAccountID'         => 0,//$payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note ? $note : $toAccountText,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $costCenterDebitId,
					'costCenterDebitAmount'            => $costCenterDebitAmount,
					'costCenterCreditId'            => $costCenterCreditId,
					'costCenterCreditAmount'            => $costCenterCreditAmount,
					'entryDebitAccount'            => $_POST['entryDebitAccount'],
					'entryDebitBalance'            => $_POST['entryDebitBalance'],
					'equivalent_entryDebitBalance'            => $_POST['equivalent_entryDebitBalance'],
					'entryCreditAccount'            => $_POST['entryCreditAccount'],
					'entryCreditBalance'            => $_POST['entryCreditBalance'],
					'equivalent_entryCreditBalance'            => $_POST['equivalent_entryCreditBalance'],
					'toAccountText'         => $toAccountText,
					'fromAccountText'         => $fromAccountText,
					'entryDebitDescription'            => $_POST['entryDebitDescription'],
					'entryCreditDescription'            => $_POST['entryCreditDescription'],






				];

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
				$data  ['grpId']=$grpId;
                $data  ['coId']=$coId;
                $data  ['brId']=$brId;
				}

				//'costCenterId'            => json_encode((Array) $_POST['costCenterId']),
				//'costCenterAmount'            => json_encode((Array) $_POST['costCenterAmount']),

				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}


				//$this->sma->print_arrays(json_encode((Array) $costCenterDebitId));




				//$this->sma->print_arrays($data);
			} elseif ($this->input->post('add_receipt')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/receipts');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->addReceipt($data)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Receipt_successfully_added'));
				admin_redirect('accounts/receipts');
			} else {
				//


				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();


				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
				{

					$this->data['mainGroups'] = $this->site->getMainGroup();
					$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
					$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
					$bond_no = $this->accounts_model->getMaxBondreceipt($brId);
				}else{
					$bond_no = $this->accounts_model->getMaxBondreceipt();
				}
				$accounts = $this->accounts_model->getAllDetailsAccounts();


				$this->data['bond_no'] = $bond_no;
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;

				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();






				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Add_receipt'), 'page' => lang('Add_receipt')], ['link' => '#', 'page' => lang('Add_receipt')]];
				$meta                           = ['page_title' => lang('Add_receipt'), 'bc' => $bc];

				$this->data['modal_js']   = $this->site->modal_js();
				$this->load->view($this->theme . 'accounts/add_receipt', $this->data);

				//$this->page_construct('accounts/add_receipt', $meta, $this->data);
			}
		}
	}

	public function add_catch_receipt($id = null)
	{
		$this->sma->checkPermissions('add_catch_receipt', true);
		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : 1;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : 1;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
		$exchange_rate=$this->input->post('exchange_rate');
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {
			/*
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_catch_receipts.bond_no]|required');

			//$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Received_from'), 'required');


			if ($this->form_validation->run() == true) {
				//    if ($this->Owner || $this->Admin ) {
				$date = $this->sma->fld($this->input->post('date'));
				//  } else {
				//    $date = date('Y-m-d H:s:i');
				// }
				if (!$this->input->post('date'))
					$date = date('Y-m-d H:s:i');

				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



				$note         = $_POST['note'];



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);



				$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
				$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

				$entryCreditAccount_arr = [];
				foreach ($jsonArr_entryCreditAccount as $product_arr) {
					$entryCreditAccount_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditAccount']);
				$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



				$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
				$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

				$entryCreditBalance_arr = [];
				$equivalent_entryCreditBalance_arr = [];
				foreach ($jsonArr_entryCreditBalance as $product_arr) {
					$entryCreditBalance_arr[] =  $product_arr->value;
					$equivalent_entryCreditBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryCreditBalance']);
				$_POST['entryCreditBalance'] = $entryCreditBalance_arr;
				$_POST['equivalent_entryCreditBalance'] = $equivalent_entryCreditBalance_arr;


			$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
			$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

			$entryCreditDescription_arr = [];
			foreach ($jsonArr_entryCreditDescription as $product_arr) {
				$entryCreditDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditDescription']);
			$_POST['entryCreditDescription'] = $entryCreditDescription_arr;




				$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
				$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

				$costCenterCreditId_arr_0 = [];
				foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
					$costCenterCreditId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterCreditId_0']);
				$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




				$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
				$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

				$costCenterCreditAmount_arr_0 = [];
				foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
					$costCenterCreditAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_0']);
				$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






				$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
				$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;



				//$this->sma->print_arrays($_POST);



				$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
				$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



				$r                = sizeof($entryCreditAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 1; $i < $r; $i++) {


					$costCenterCreditId_arr = [];
					foreach ($jsonArr_costCenterCreditId as $product_arr) {
						if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
							$costCenterCreditId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditId_' . $i]);
					$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
					unset($costCenterCreditId_arr);





					//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




					$costCenterCreditAmount_arr = [];
					foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
						if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
							$costCenterCreditAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditAmount_' . $i]);
					$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
					unset($costCenterCreditAmount_arr);
				}

				$toAccountText="";
				$costCreditCount = 0;
					foreach ($_POST['entryCreditAccount'] as $key => $acc) {
						if($costCreditCount==0)
						$toAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$toAccountText=$toAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterCreditId_' . $costCreditCount]) {
							$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
							$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
						} else {
							$costCenterCreditId[] = "";
							$costCenterCreditAmount[] = "";
						}
						$costCreditCount++;
					}


















				////////Debit

				$paid_by=' ';

				$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
			$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

			$entryDebitAccount_arr = [];
			foreach ($jsonArr_entryDebitAccount as $product_arr) {
				$payment_type_details = $this->accounts_model->getPaymentTypeByID($product_arr->value);
				$entryDebitAccount_arr[] =  $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type;

				$fromAccount       = $this->accounts_model->getAccountByID($payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type);
				$fromAccountName   = $fromAccount->accountNameEn ? $fromAccount->accountNameEn :  $fromAccount->accountName;
				$fromAccountName   = $this->Settings->user_language == 'arabic' ? $fromAccount->accountName . ' ' . $cheque_no :  $fromAccountName . ' ' . $cheque_no;

				$paid_by=$paid_by.' - '.($payment_type_details->payment_type=='Cheque' ? lang('cheque').' / '.$fromAccountName : lang($payment_type_details->payment_type).' / '.$fromAccountName);
			}

			unset($_POST['entryDebitAccount']);
			$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



			$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
			$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

			$entryDebitBalance_arr = [];
			$equivalent_entryDebitBalance_arr = [];
			foreach ($jsonArr_entryDebitBalance as $product_arr) {
				$entryDebitBalance_arr[] =  $product_arr->value;
				$equivalent_entryDebitBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);

			}

			unset($_POST['entryDebitBalance']);
			$_POST['entryDebitBalance'] = $entryDebitBalance_arr;
			$_POST['equivalent_entryDebitBalance'] = $equivalent_entryDebitBalance_arr;



			$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
			$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

			$entryDebitDescription_arr = [];
			foreach ($jsonArr_entryDebitDescription as $product_arr) {
				$entryDebitDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitDescription']);
			$_POST['entryDebitDescription'] = $entryDebitDescription_arr;




			$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
			$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

			$costCenterDebitId_arr_0 = [];
			foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
				$costCenterDebitId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterDebitId_0']);
			$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




			$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
			$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

			$costCenterDebitAmount_arr_0 = [];
			foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
				$costCenterDebitAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterDebitAmount_0']);
			$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






			$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
			$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
			$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



			$r                = sizeof($entryDebitAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterDebitId_arr = [];
				foreach ($jsonArr_costCenterDebitId as $product_arr) {
					if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
						$costCenterDebitId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitId_' . $i]);
				$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
				unset($costCenterDebitId_arr);





				//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




				$costCenterDebitAmount_arr = [];
				foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
					if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
						$costCenterDebitAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_' . $i]);
				$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
				unset($costCenterDebitAmount_arr);
			}

			$fromAccountText="";
			$costDebitCount = 0;
				foreach ($_POST['entryDebitAccount'] as $key => $acc) {
					if($costDebitCount==0)
					$fromAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
					else
					$fromAccountText=$fromAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
					if ($_POST['costCenterDebitId_' . $costDebitCount]) {
						$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
						$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
					} else {
						$costCenterDebitId[] = "";
						$costCenterDebitAmount[] = "";
					}
					$costDebitCount++;
				}









				$salesman_id=$this->input->post('salesman_id') ? $this->input->post('salesman_id') : null;
				$salesman_name=$this->input->post('salesman_id') ? $this->site->getCompanyByID($salesman_id)->company : null;
				$amountDiff=0;
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1 && strpos($this->input->post('inv_no'), ',') === false)
					{
						$_POST['inv_no']=$this->site->getInvoiceBySaleNo($this->input->post('inv_no'))->id;
					}

					if($this->input->post('inv_no') && strpos($this->input->post('inv_no'), ',') === false){

						if($inv=$this->site->getSaleByID(trim($this->input->post('inv_no')))){
						if($inv->currency_rate!=$this->input->post('exchange_rate')){
							$amountPL=$this->sma->formatDecimal($this->input->post('local_curr_amount'), 4);
							$amount=$this->sma->formatDecimal($this->input->post('amount'), 4)*$inv->currency_rate;
							$amountDiff=$amountPL-$amount;
							if($amountDiff<0){
								$_POST['equivalent_entryDebitBalance'][0]=$_POST['equivalent_entryDebitBalance'][0]-abs($amountDiff);
							}
							if($amountDiff>0){
								$_POST['equivalent_entryCreditBalance'][0]=$_POST['equivalent_entryCreditBalance'][0]-abs($amountDiff);
							}

						}
					}
				}



				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $paid_by,
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => 0,//$this->input->post('toAccountID'),
					'fromAccountID'         => 0,//$payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note ? $note : $toAccountText,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $costCenterDebitId,
					'costCenterDebitAmount'            => $costCenterDebitAmount,
					'costCenterCreditId'            => $costCenterCreditId,
					'costCenterCreditAmount'            => $costCenterCreditAmount,
					'entryDebitAccount'            => $_POST['entryDebitAccount'],
					'entryDebitBalance'            => $_POST['entryDebitBalance'],
					'equivalent_entryDebitBalance'            => $_POST['equivalent_entryDebitBalance'],
					'entryCreditAccount'            => $_POST['entryCreditAccount'],
					'entryCreditBalance'            => $_POST['entryCreditBalance'],
					'equivalent_entryCreditBalance'            => $_POST['equivalent_entryCreditBalance'],
					'toAccountText'         => $toAccountText,
					'fromAccountText'         => $fromAccountText,
					'entryDebitDescription'            => $_POST['entryDebitDescription'],
					'entryCreditDescription'            => $_POST['entryCreditDescription'],
					'salesman_id'         => $salesman_id,
					'salesman_name'         => $salesman_name,
					'inv_no'         => $this->input->post('inv_no') ? trim($this->input->post('inv_no')) : null,
					'the_oldest'         => $this->input->post('the_oldest') ? trim($this->input->post('the_oldest')) : 0,

				];

				//$this->sma->print_arrays($data,$_POST);
				//$this->sma->print_arrays($data);
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
				$data  ['grpId']=$grpId;
                $data  ['coId']=$coId;
                $data  ['brId']=$brId;
				}


				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);






				if($this->input->post('inv_no') && strpos($this->input->post('inv_no'), ',') === false){
					if($inv=$this->site->getSaleByID(trim($this->input->post('inv_no')))){

						//$this->sma->print_arrays($_POST['equivalent_entryCreditBalance'][0],$amountDiff);
						$cus_acc=$this->accounts_model->getAccountByID($_POST['entryCreditAccount'][0]);

						if($cus_acc->account_type=='local_customers' || $cus_acc->account_type=='international_customers'){

							$customer=$this->site->getCompanyByID($cus_acc->account_ref_id);
							if($customer->id == $inv->customer_id){
								$accounts_settings   = $this->accounts_model->getSettings();

								$payment_acc=$this->accounts_model->getAccountByID($_POST['entryDebitAccount'][0]);

								if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else{
									$paid_by='cash';
								}


							}else{
								$this->session->set_flashdata('error', lang('Please_select_the_customer_account_in_the_invoice'));
								redirect($_SERVER['HTTP_REFERER']);
							}

						}else{
							$this->session->set_flashdata('error', lang('Please_select_an_account_from_the_customer_accounts'));
							redirect($_SERVER['HTTP_REFERER']);
						}



					}else{
						$this->session->set_flashdata('error', lang('Sale_invoice_not_found'));
						redirect($_SERVER['HTTP_REFERER']);
					}
				}


				if (strpos($this->input->post('inv_no'), ',') !== false)
				{
					$cus_acc=$this->accounts_model->getAccountByID($_POST['entryCreditAccount'][0]);

						if($cus_acc->account_type=='local_customers' || $cus_acc->account_type=='international_customers'){

							$customer=$this->site->getCompanyByID($cus_acc->account_ref_id);

							$saleList = explode(",", $this->input->post('inv_no'));//$this->accounts_model->getAllSalesDue(NULL, NULL, $date, 1, $customer->id, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);

							$invoices = array();
							$payments = array();
							$amount=$_POST['entryCreditBalance'][0];
							$accounts_settings   = $this->accounts_model->getSettings();
							$payment_acc=$this->accounts_model->getAccountByID($_POST['entryDebitAccount'][0]);
								if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else{
									$paid_by='cash';
								}
							$temp=0;
							foreach ($saleList as $inv){
								if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
								{
									$sale_id=$this->site->getInvoiceBySaleNo(trim($inv))->id;
									$sale=$this->site->getSaleByID(trim($sale_id));
								}else{
									$sale=$this->site->getSaleByID(trim($inv));
								}
								//$this->sma->print_arrays($sale);
								if($sale->grand_total <= $amount){
									array_push($invoices, $sale->id);
									array_push($payments, $sale->grand_total);
									$amount=$amount-$sale->grand_total;
								}else{
									if($amount>0){
										array_push($invoices, $sale->id);
										array_push($payments, $amount);
										$amount=0;
									}
									$temp=1;
								}
								if($temp==1)
									break;


							}
							if($invoices){
								$data['invoices'] = $invoices;
							}
							if($payments){
								$data['payments'] = $payments;
							}
							//$this->sma->print_arrays($saleList,$_POST['entryCreditBalance'][0],$invoices,$payments);

						}

					// $this->sma->print_arrays($data, $products);
					}

				if($this->input->post('the_oldest')==1)
				{
					$cus_acc=$this->accounts_model->getAccountByID($_POST['entryCreditAccount'][0]);

						if($cus_acc->account_type=='local_customers' || $cus_acc->account_type=='international_customers'){

							$customer=$this->site->getCompanyByID($cus_acc->account_ref_id);

							$saleList = $this->accounts_model->getAllSalesDue(NULL, NULL, $date, 1, $customer->id, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
							$invoices = array();
							$payments = array();
							$amount=$_POST['entryCreditBalance'][0];
							$accounts_settings   = $this->accounts_model->getSettings();
							$payment_acc=$this->accounts_model->getAccountByID($_POST['entryDebitAccount'][0]);
								if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else{
									$paid_by='cash';
								}
							$temp=0;
							foreach ($saleList as $sale){
								if($sale->amount <= $amount){
									array_push($invoices, $sale->id);
									array_push($payments, $sale->amount);
									$amount=$amount-$sale->amount;
								}else{
									if($amount>0){
										array_push($invoices, $sale->id);
										array_push($payments, $amount);
										$amount=0;
									}
									$temp=1;
								}
								if($temp==1)
									break;


							}
							if($invoices){
								$data['invoices'] = $invoices;
							}
							if($payments){
								$data['payments'] = $payments;
							}
							//$this->sma->print_arrays($saleList,$_POST['entryCreditBalance'][0],$invoices,$payments);

						}

					 //$this->sma->print_arrays($data, $products);
					}
					//$this->sma->print_arrays($data,$paid_by,$_POST['entryCreditBalance'][0],$invoices,$payments);
				} elseif ($this->input->post('add_catch_receipt')) {
					$this->session->set_flashdata('error', validation_errors());
					admin_redirect('accounts/catch_receipts');
				}





			if ($this->form_validation->run() == true && $this->accounts_model->addCatchReceipt($data,$paid_by,$_POST['entryCreditBalance'][0],$amountDiff)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Catch_receipt_successfully_added'));
				admin_redirect('accounts/catch_receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
				{

					$this->data['mainGroups'] = $this->site->getMainGroup();
					$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
					$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
					$bond_no = $this->accounts_model->getMaxBondCatchreceipt($brId);
				}else{
					$bond_no = $this->accounts_model->getMaxBondCatchreceipt();
				}

				$accounts = $this->accounts_model->getAllDetailsAccounts();



				$this->data['bond_no'] = $bond_no;
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();



				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Add_catch_receipt'), 'page' => lang('Add_catch_receipt')], ['link' => '#', 'page' => lang('Add_catch_receipt')]];
				$meta                           = ['page_title' => lang('Add_catch_receipt'), 'bc' => $bc];

				$this->data['modal_js']   = $this->site->modal_js();
				$this->load->view($this->theme . 'accounts/add_catch_receipt', $this->data);

				//$this->page_construct('accounts/add_catch_receipt', $meta, $this->data);
			}
		}
	}
	public function add_refunding_bonds($id = null)
	{
		$this->sma->checkPermissions('add_refunding_bonds', true);

		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : 1;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : 1;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {
			/*
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_refunding_bonds.bond_no]|required');
			$this->form_validation->set_rules('toAccountID', lang('The_account_transferred_to'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('The_account_transferred_from'), 'required');


			if ($this->form_validation->run() == true) {
				//   if ($this->Owner || $this->Admin ) {
				$date = $this->sma->fld($this->input->post('date'));
				//  } else {
				//     $date = date('Y-m-d H:s:i');
				// }
				if (!$this->input->post('date'))
					$date = date('Y-m-d H:s:i');

				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/
				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



				//$note         = $this->sma->clear_tags($this->input->post('note'));
				$note         = $_POST['note'];



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);










				/*$data = [
                'date'         => $date,
                'bond_no'         => $this->input->post('bond_no'),
                'reference_no'         => $this->input->post('reference_no'),
                'amount'         => $this->sma->formatDecimal($this->input->post('amount'),4),
                'tax_rate'         => $this->input->post('tax_rate'),
                'tax_amount'         => $this->input->post('tax_amount'),
                'total_amount'         => $this->input->post('total_amount'),
                'paid_by'         => $this->input->post('paid_by'),
                'cheque_no'         => $cheque_no,
                'cc_type'         => $cc_type,
                'toAccountID'         => $this->input->post('toAccountID'),
                'fromAccountID'         => $payment_type_details->payment_account,
                'receipt_name'         => $this->input->post('receipt_name'),
                'cost_center'         => $this->input->post('cost_center'),
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
            ];*/





				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => $this->input->post('toAccountID'),
					'fromAccountID'         => $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],


				];

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
				$data  ['grpId']=$grpId;
                $data  ['coId']=$coId;
                $data  ['brId']=$brId;
				}

				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);



				// $this->sma->print_arrays($data, $products);
			} elseif ($this->input->post('add_refunding_bonds')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/refunding_bonds');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->addRefundingBond($data)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Refunding_bond_successfully_added'));
				admin_redirect('accounts/refunding_bonds');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
				{

					$this->data['mainGroups'] = $this->site->getMainGroup();
					$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
					$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
					$bond_no = $this->accounts_model->getMaxBondRefundingBond($brId);
				}else{
					$bond_no = $this->accounts_model->getMaxBondRefundingBond();
				}


				$accounts = $this->accounts_model->getAllDetailsAccounts();



				$this->data['bond_no'] = $bond_no;
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				//$this->sma->print_arrays($this->data['payment_types']);

				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Add_refunding_bonds'), 'page' => lang('Add_refunding_bonds')], ['link' => '#', 'page' => lang('Add_refunding_bonds')]];
				$meta                           = ['page_title' => lang('Add_refunding_bonds'), 'bc' => $bc];
				$this->data['modal_js']   = $this->site->modal_js();
				$this->load->view($this->theme . 'accounts/add_refunding_bonds', $this->data);

				//$this->page_construct('accounts/add_refunding_bonds', $meta, $this->data);
			}
		}
	}

	public function view_receipt($id = null)
	{
		$this->sma->checkPermissions('view_receipt', true);
		// $this->sma->checkPermissions('deliveries');

		if ($this->input->get('id')) {
			$id = $this->input->get('id');
		}

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$receipt = $this->accounts_model->getReceiptByID($id);
		$this->data['receipt']   = $receipt;
		//$this->sma->print_arrays($receipt);

		//$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->getFRACTION($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'هللة') : $this->AmountInWords($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'Halala');
		$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->sma->AmountInWordsAr($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'هللة') : $this->sma->AmountInWordsEn($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'Halala');

		$this->data['warehouse']   = $this->site->getWarehouseByID($this->Settings->default_warehouse);

		$this->data['user']       = $this->site->getUser($receipt->created_by);
		$account       = $this->accounts_model->getAccountByID($receipt->toAccountID);
		$fromAccount       = $this->accounts_model->getAccountByID($receipt->fromAccountID);
		//$accountDetails       = $this->accounts_model->getAccountByID($receipt->toAccountID->accountName);
		$accountName   = $account->accountNameEn ? $account->accountNameEn :  $account->accountName;
		$fromAccountName   = $fromAccount->accountNameEn ? $fromAccount->accountNameEn :  $fromAccount->accountName;
		$this->data['accountName']         = $this->Settings->user_language == 'arabic' ? $account->accountName :  $accountName;
		$this->data['fromAccountName']         = $this->Settings->user_language == 'arabic' ? $fromAccount->accountName . ' ' . $receipt->cheque_no :  $fromAccountName . ' ' . $receipt->cheque_no;
		//$this->sma->print_arrays($this->data['warehouse']);
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['branch'] = $this->site->getMainBranchesByID($receipt->brId);
		}


		$this->data['page_title'] = lang('delivery_order');

		$this->load->view($this->theme . 'accounts/view_receipt', $this->data);
	}

	public function view_catch_receipt($id = null)
	{
		$this->sma->checkPermissions('view_catch_receipt', true);

		if ($this->input->get('id')) {
			$id = $this->input->get('id');
		}

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$receipt = $this->accounts_model->getCatchReceiptByID($id);
		$this->data['receipt']   = $receipt;

		//$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->getFRACTION($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'هللة') : $this->AmountInWords($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'Halala');
		$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->sma->AmountInWordsAr($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'هللة') : $this->sma->AmountInWordsEn($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'Halala');

		$this->data['warehouse']   = $this->site->getWarehouseByID($this->Settings->default_warehouse);

		$this->data['user']       = $this->site->getUser($receipt->created_by);
		$account       = $this->accounts_model->getAccountByID($receipt->toAccountID);
		$fromAccount       = $this->accounts_model->getAccountByID($receipt->fromAccountID);
		$accountName   = $account->accountNameEn ? $account->accountNameEn :  $account->accountName;
		$fromAccountName   = $fromAccount->accountNameEn ? $fromAccount->accountNameEn :  $fromAccount->accountName;
		$this->data['accountName']         = $this->Settings->user_language == 'arabic' ? $account->accountName :  $accountName;
		$this->data['fromAccountName']         = $this->Settings->user_language == 'arabic' ? $fromAccount->accountName . ' ' . $receipt->cheque_no :  $fromAccountName . ' ' . $receipt->cheque_no;
		//$this->sma->print_arrays($this->data['warehouse']);
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['branch'] = $this->site->getMainBranchesByID($receipt->brId);
		}


		$this->data['page_title'] = lang('delivery_order');

		$this->load->view($this->theme . 'accounts/view_catch_receipt', $this->data);
	}

	public function view_refunding_bond($id = null)
	{
		$this->sma->checkPermissions('view_refunding_bond', true);

		if ($this->input->get('id')) {
			$id = $this->input->get('id');
		}

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$receipt = $this->accounts_model->getRefundingBondByID($id);
		$this->data['receipt']   = $receipt;

		//$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->getFRACTION($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'هللة') : $this->AmountInWords($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'Halala');
		$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->sma->AmountInWordsAr($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'هللة') : $this->sma->AmountInWordsEn($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'Halala');


		$this->data['warehouse']   = $this->site->getWarehouseByID($this->Settings->default_warehouse);

		$this->data['user']       = $this->site->getUser($receipt->created_by);
		$account       = $this->accounts_model->getAccountByID($receipt->toAccountID);
		$accountNameTo   = $account->accountNameEn ? $account->accountNameEn :  $account->accountName;
		$this->data['accountNameTo']         = $this->Settings->user_language == 'arabic' ? $account->accountName :  $accountNameTo;
		$accountFrom       = $this->accounts_model->getAccountByID($receipt->fromAccountID);
		$accountNameFrom   = $accountFrom->accountNameEn ? $accountFrom->accountNameEn :  $accountFrom->accountName;
		$this->data['accountNameFrom']         = $this->Settings->user_language == 'arabic' ? $accountFrom->accountName :  $accountNameFrom;
		//$this->sma->print_arrays($this->data['warehouse']);
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['branch'] = $this->site->getMainBranchesByID($receipt->brId);
		}


		$this->data['page_title'] = lang('delivery_order');

		$this->load->view($this->theme . 'accounts/view_refunding_bond', $this->data);
	}

	public function getFRACTION($EQE_CR, $CURR_ANAME, $FRACTION)
	{
		$ftext = "";
		if ($CURR_ANAME == "SR")
			$CURR_ANAME = "ريال";
		if ($CURR_ANAME == "USD")
			$CURR_ANAME = "دولار أمريكي";

		//$EQE_CR=$EQE_CR.".00";
		$pos = strrpos($EQE_CR, '.');


		if ($pos == "") {
			$EQE_CR = $EQE_CR . ".00";
			$pos = strrpos($EQE_CR, '.');
		}


		$cent = substr("$EQE_CR", $pos + 1);
		$cent = substr("$cent", 0, 2);
		$EQE_CR = substr("$EQE_CR", 0, $pos);
		$len = strlen($EQE_CR);


		if ($len <= 9 && $len > 6) {
			$s = substr("$EQE_CR", 0, $len - 6);

			$ftext = $ftext .  $this->getFRACTION1($s);
			$ftext = $ftext .  " مليون ";

			//echo " و ";
			if ($len == 7)
				$s = substr("$EQE_CR", $len - 6, $len - 4);
			else if ($len == 8)
				$s = substr("$EQE_CR", $len - 6, $len - 5);
			else if ($len == 9)
				$s = substr("$EQE_CR", $len - 6, $len - 6);
			if ($s != "000")
				$ftext = $ftext . " و ";

			$ftext = $ftext .  $this->getFRACTION1($s);
			if ($s != "000")
				$ftext = $ftext .  " الف ";
			$s = substr("$EQE_CR", $len - 3, $len - 1);
			if ($s != "000")
				$ftext = $ftext .  " و ";

			//echo " و ";

			//$s= substr("$EQE_CR",$len-3, $len-1);

			$ftext = $ftext .  $this->getFRACTION1($s);
		} else if ($len <= 6 && $len > 3) {



			$s = substr("$EQE_CR", 0, $len - 3);
			$ftext = $ftext .  $this->getFRACTION1($s);
			$ftext = $ftext .  " الف ";
			$s = substr("$EQE_CR", $len - 3, $len - 1);
			if ($s != "000")
				$ftext = $ftext .  " و ";
			//$s= substr("$EQE_CR",$len-3, $len-1);

			$ftext = $ftext .  $this->getFRACTION1($s);
		} else if ($len <= 3) {



			$ftext = $ftext .  $this->getFRACTION1($EQE_CR);
		}

		$pos = strrpos($EQE_CR, '.');
		$pos1 = strlen($pos . "p");

		if ($pos1 == 1)
			$ftext = $ftext .  " " . $CURR_ANAME;
		else {
			$ftext = $ftext .  " " . $FRACTION;
		}
		if ($cent != "00") {
			$ftext = $ftext .  " و ";

			$ftext = $ftext .  $this->getFRACTION1($cent);
			$ftext = $ftext .  " " . $FRACTION;
		}
		$ftext = $ftext .  " فقط لا غير ";


		return $ftext;
	}




	public function getFRACTION1($EQE_CR)
	{
		$ftext1 = "";
		$s3 = "";
		$s1 = "";
		$s2 = "";
		$len = strlen($EQE_CR);
		for ($i = 0; $i < $len; $i++) {
			if ($i != 0)
				$s = substr("$EQE_CR", $i, $i);
			else
				$s = substr("$EQE_CR", $i, $i + 1);


			if ($s == 0)
				$s = "صفر";
			else if ($s == 1) {
				if ($len == 3 && $i == 0)
					$s3 = "مائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "واحد";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "عشر";
			} else if ($s == 2) {
				if ($len == 3 && $i == 0)
					$s3 = "مائتيين";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "اثنان";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "عشرون";
			} else if ($s == 3) {
				if ($len == 3 && $i == 0)
					$s3 = "ثلاثمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "ثلاثة";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "ثلاثون";
			} else if ($s == 4) {
				if ($len == 3 && $i == 0)
					$s3 = "اربعمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "اربعة";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "اربعون";
			} else if ($s == 5) {
				if ($len == 3 && $i == 0)
					$s3 = "خمسمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "خمسة";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "خمسون";
			} else if ($s == 6) {
				if ($len == 3 && $i == 0)
					$s3 = "ستمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "ستة";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "ستون";
			} else if ($s == 7) {
				if ($len == 3 && $i == 0)
					$s3 = "سبعمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "سبعة";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "سبعون";
			} else if ($s == 8) {
				if ($len == 3 && $i == 0)
					$s3 = "ثمانمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "ثمانية";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "ثمانون";
			} else if ($s == 9) {
				if ($len == 3 && $i == 0)
					$s3 = "تسعمائة";
				else
if (($len == 3 && $i == 2) || ($len == 2 && $i == 1) || ($len == 1 && $i == 0))
					$s1 = "تسعة";
				else
if (($len == 3 && $i == 1) || ($len == 2 && $i == 0))
					$s2 = "تسعون";
			}
		}

		//echo $len;

		if ($len == 3) {
			if ($s3 != "")
				if ($s1 == "" && $s2 == "")
					$ftext1 = $ftext1 . "" . $s3 . " ";
				else
					$ftext1 = $ftext1 . "" . $s3 . " و";
			if ($s1 != "")
				if ($s2 == "")
					$ftext1 = $ftext1 . "" . $s1 . " ";
				else {









					if ($s1 != "")
						if ($s2 == "عشر") {
							if ($s1 == "واحد")
								$s1 = "احدى";
							if ($s1 == "اثنان")
								$s1 = "اثنا";

							$ftext1 = $ftext1 . "" . $s1 . " " . $s2;
						}
				}


			if ($s2 != "")
				if ($s1 == "")
					$ftext1 = $ftext1 . "" .  $s2;
				else
					$ftext1 = $ftext1 . "" .  $s1 . " و" . $s2;
		} else if ($len == 2) {
			//echo $s2;
			if ($s1 != "")
				if ($s2 == "عشر") {
					if ($s1 == "واحد")
						$s1 = "احدى";
					if ($s1 == "اثنان")
						$s1 = "اثنا";

					$ftext1 = $ftext1 . "" .  $s1 . " " . $s2;
				} else {

					if ($s2 == "")
						$ftext1 = $ftext1 . "" .  $s1 . " ";
					else
						$ftext1 = $ftext1 . "" .  $s1 . " و";

					$ftext1 = $ftext1 . "" .  $s2;
				}
			else
				$ftext1 = $ftext1 . "" .  $s2;


			//echo $s1." و".$s2;
		} else
			$ftext1 = $ftext1 . "" .  $s1;


		return $ftext1;
	}


	function AmountInWords(float $amount, $CURR, $FRACTION)
	{
		if ($CURR == "SR")
			$CURR = "Riyals";
		if ($CURR == "USD")
			$CURR = "US dollars";

		$amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
		// Check if there is any number after decimal
		$amt_hundred = null;
		$count_length = strlen($num);
		$x = 0;
		$string = array();
		$change_words = array(
			0 => '', 1 => 'One', 2 => 'Two',
			3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
			7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
			10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
			13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
			16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
			19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
			40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
			70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
		);
		//$here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
		$here_digits = array('', 'Hundred', 'Thousand', 'Hundred', 'Crore');
		while ($x < $count_length) {
			$get_divider = ($x == 2) ? 10 : 100;
			$amount = floor($num % $get_divider);
			$num = floor($num / $get_divider);
			$x += $get_divider == 10 ? 1 : 2;
			if ($amount) {
				$add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
				$amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
				$string[] = ($amount < 21) ? $change_words[$amount] . ' ' . $here_digits[$counter] . $add_plural . '
       ' . $amt_hundred : $change_words[floor($amount / 10) * 10] . ' ' . $change_words[$amount % 10] . '
       ' . $here_digits[$counter] . $add_plural . ' ' . $amt_hundred;
			} else $string[] = null;
		}
		$implode_to_Rupees = implode('', array_reverse($string));
		$get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . "
   " . $change_words[$amount_after_decimal % 10]) . ' ' . $FRACTION . ' ' : '';
		return ($implode_to_Rupees ? $implode_to_Rupees . ' ' . $CURR . ' ' : '') . $get_paise;
	}


	public function settings()
	{
		$this->load->library('gst');
		$this->form_validation->set_rules('warehouses_acct', lang('Warehouses_account'), 'required');
		$this->form_validation->set_rules('warehouses_destruction', lang('Warehouses_destruction_account'), 'required');
		$this->form_validation->set_rules('warehouses_adjustment_inability', lang('Warehouses_adjustment_deficit_account'), 'required');
		$this->form_validation->set_rules('warehouses_adjustment_surplus', lang('Warehouses_adjustment_surplus_account'), 'required');
		$this->form_validation->set_rules('warehouses_revaluation_profite', lang('Warehouses_revaluation_profite_account'), 'required');
		$this->form_validation->set_rules('warehouses_revaluation_losses', lang('Warehouses_revaluation_losses_account'), 'required');
		$this->form_validation->set_rules('accounts_main_boxes_acct', lang('Main_Funds_Account'), 'required');
		//$this->form_validation->set_rules('accounts_main_banks_acct', lang('Main_bank_account'), 'required');
		$this->form_validation->set_rules('purchases_store_goods_by_road_acct', lang('Store_Goods_By_Road_Account'), 'required');
		//$this->form_validation->set_rules('accounts_suppliers_acct', lang('Suppliers_account'), 'required');
		$this->form_validation->set_rules('accounts_bank_commisson_acct', lang('Bank_commissons_account'), 'required');
		$this->form_validation->set_rules('accounts_shipping_return_losses', lang('Shipping_return_losses_account'), 'required');
		$this->form_validation->set_rules('purchases_vat_acct', lang('Purchases_Tax_account'), 'required');
		$this->form_validation->set_rules('sales_vat_acct', lang('Sales_Tax_account'), 'required');
		$this->form_validation->set_rules('sales_store_goods_receiving_acct', lang('Store_Goods_Receiving_Account'), 'required');
		$this->form_validation->set_rules('sales_acct', lang('Sales_account'), 'required');
		$this->form_validation->set_rules('sales_cost_of_good_sold_acct', lang('Cost_of_good_sold_account'), 'required');
		$this->form_validation->set_rules('accounts_shipping_other_revenues', lang('Other_revenues_shipping_account'), 'required');
		$this->form_validation->set_rules('sales_return_acct', lang('Sales_return_account'), 'required');
		$this->form_validation->set_rules('accounts_operating_expenses_acct', lang('Operating_expenses_account'), 'required');
		$this->form_validation->set_rules('accounts_marketing_expenses_acct', lang('Marketing_expenses_account'), 'required');
		$this->form_validation->set_rules('accounts_fixed_assets_acct', lang('Fixed_Assets_account'), 'required');
		$this->form_validation->set_rules('accounts_current_assets_acct', lang('Current_Assets_account'), 'required');
		$this->form_validation->set_rules('accounts_provisions_acct', lang('The_Provisions_account'), 'required');
		$this->form_validation->set_rules('cost_centers', lang('Cost_centers'), 'required');
		$this->form_validation->set_rules('accounts_profits_stage_acct', lang('Profits_Stage'), 'required');
		$this->form_validation->set_rules('accounts_losses_stage_acct', lang('Losses_Stage'), 'required');
		$this->form_validation->set_rules('accounts_currency_differences_profit_acct', lang('Currency_differences_profit_account'), 'required');
		$this->form_validation->set_rules('accounts_currency_spread_losses_acct', lang('Currency_spread_losses_account'), 'required');
		$this->form_validation->set_rules('accounts_gift_card_sales', lang('Gift_card_sales'), 'required');
		$this->form_validation->set_rules('accounts_gift_card_payments', lang('Gift_card_payments'), 'required');
		$this->form_validation->set_rules('accounts_discount_allowance', lang('Discount_Allowance'), 'required');
		$this->form_validation->set_rules('accounts_discount_earned', lang('Discount_Earned'), 'required');
		$this->form_validation->set_rules('expenses_shipping_acct', lang('Accrued_Expenses'), 'required');
		$this->form_validation->set_rules('accounts_local_suppliers_acct', lang('Local_Suppliers_account'), 'required');
		$this->form_validation->set_rules('accounts_international_suppliers_acct', lang('International_Suppliers_account'), 'required');
		$this->form_validation->set_rules('accounts_local_customers_acct', lang('Local_Customers_account'), 'required');
		$this->form_validation->set_rules('accounts_international_customers_acct', lang('International_Customers_account'), 'required');
		$this->form_validation->set_rules('accounts_ready_funds_acct', lang('Owners_equity_account'), 'required');
		//$this->form_validation->set_rules('accounts_bank_acct', lang('Additional_Capital_account'), 'required');
		if ($this->Settings->accounting_type == "periodic") {
			$this->form_validation->set_rules('purchases_acct', lang('Purchases_account'), 'required');
			$this->form_validation->set_rules('purchases_return_acct', lang('Purchases_return_account'), 'required');
		}

		if ($this->Settings->isDonation) {
			$this->form_validation->set_rules('donations_acct', lang('Donations_due_to_charities_account'), 'required');
		}




		if ($this->form_validation->run() == true) {

			//'accounts_suppliers_acct' => $this->input->post('accounts_suppliers_acct'),



			$data = [
				'warehouses_acct' => $this->input->post('warehouses_acct'),
				'warehouses_destruction' => $this->input->post('warehouses_destruction'),
				'warehouses_adjustment_inability' => $this->input->post('warehouses_adjustment_inability'),
				'warehouses_adjustment_surplus' => $this->input->post('warehouses_adjustment_surplus'),
				'warehouses_revaluation_profite' => $this->input->post('warehouses_revaluation_profite'),
				'warehouses_revaluation_losses' => $this->input->post('warehouses_revaluation_losses'),
				'accounts_main_boxes_acct' => $this->input->post('accounts_main_boxes_acct'),
				'accounts_main_banks_acct' => $this->input->post('accounts_main_banks_acct'),
				'purchases_store_goods_by_road_acct' => $this->input->post('purchases_store_goods_by_road_acct'),
				'purchases_vat_acct' => $this->input->post('purchases_vat_acct'),
				'accounts_local_suppliers_acct' => $this->input->post('accounts_local_suppliers_acct'),
				'accounts_international_suppliers_acct' => $this->input->post('accounts_international_suppliers_acct'),
				'accounts_bank_commisson_acct' => $this->input->post('accounts_bank_commisson_acct'),
				'accounts_shipping_return_losses' => $this->input->post('accounts_shipping_return_losses'),
				'sales_vat_acct' => $this->input->post('sales_vat_acct'),
				'sales_store_goods_receiving_acct' => $this->input->post('sales_store_goods_receiving_acct'),
				'accounts_local_customers_acct' => $this->input->post('accounts_local_customers_acct'),
				'accounts_international_customers_acct' => $this->input->post('accounts_international_customers_acct'),
				'sales_acct' => $this->input->post('sales_acct'),
				'sales_cost_of_good_sold_acct' => $this->input->post('sales_cost_of_good_sold_acct'),
				'accounts_shipping_other_revenues' => $this->input->post('accounts_shipping_other_revenues'),
				'sales_return_acct' => $this->input->post('sales_return_acct'),
				'accounts_operating_expenses_acct' => $this->input->post('accounts_operating_expenses_acct'),
				'accounts_marketing_expenses_acct' => $this->input->post('accounts_marketing_expenses_acct'),
				'accounts_fixed_assets_acct' => $this->input->post('accounts_fixed_assets_acct'),
				'accounts_current_assets_acct' => $this->input->post('accounts_current_assets_acct'),
				'accounts_provisions_acct' => $this->input->post('accounts_provisions_acct'),
				'cost_centers' => $this->input->post('cost_centers'),
				'accounts_profits_stage_acct' => $this->input->post('accounts_profits_stage_acct'),
				'accounts_losses_stage_acct' => $this->input->post('accounts_losses_stage_acct'),
				'accounts_currency_differences_profit_acct' => $this->input->post('accounts_currency_differences_profit_acct'),
				'accounts_currency_spread_losses_acct' => $this->input->post('accounts_currency_spread_losses_acct'),
				'accounts_gift_card_sales' => $this->input->post('accounts_gift_card_sales'),
				'accounts_gift_card_payments' => $this->input->post('accounts_gift_card_payments'),
				'accounts_discount_allowance' => $this->input->post('accounts_discount_allowance'),
				'accounts_discount_earned' => $this->input->post('accounts_discount_earned'),
				'expenses_shipping_acct' => $this->input->post('expenses_shipping_acct'),
				'accounts_ready_funds_acct' => $this->input->post('accounts_ready_funds_acct'),
				//'accounts_bank_acct' => $this->input->post('accounts_bank_acct'),





			];
			if ($this->Settings->IsInsuranceBusiness) {
				$data['business_guarantee_insurance_acct'] = $this->input->post('business_guarantee_insurance_acct');
				$data['advance_payments_acct'] = $this->input->post('advance_payments_acct');
			}

			if ($this->Settings->accounting_type == "periodic") {
				$data['purchases_acct'] = $this->input->post('purchases_acct');
				$data['purchases_return_acct'] = $this->input->post('purchases_return_acct');
			}

			if ($this->Settings->isDonation) {
				$data['donations_acct'] = $this->input->post('donations_acct');

			}
		}

		if ($this->form_validation->run() == true && $this->accounts_model->updateSetting($data)) {


			$this->session->set_flashdata('message', lang('setting_updated'));
			admin_redirect('accounts/settings');
		} else {
			$this->data['error']           = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['accounts']         = $this->accounts_model->getAllDetailsAccounts();
			$this->data['masterAccounts']         = $this->accounts_model->getAllMastersAccounts();

			$this->data['accounts_settings']         = $this->accounts_model->getAccountsSettings();

			$bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Account_system_settings')]];
			$meta                          = ['page_title' => lang('Account_system_settings'), 'bc' => $bc];
			$this->page_construct('accounts/settings', $meta, $this->data);
		}
	}

	public function add_entry()
	{
		$this->sma->checkPermissions('entry_add', true);
		$data['title']    = "Entries | Create Entry";
		$data['userData'] = $this->session->userdata();

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();

		$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];

		//$this->sma->print_arrays($this->data['max_min_period_year']);
		   	$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');



		if (!empty($this->input->post())) {


			$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
			$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

			$entryDebitAccount_arr = [];
			foreach ($jsonArr_entryDebitAccount as $product_arr) {
				$entryDebitAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitAccount']);
			$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



			$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
			$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

			$entryDebitBalance_arr = [];
			foreach ($jsonArr_entryDebitBalance as $product_arr) {
				$entryDebitBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitBalance']);
			$_POST['entryDebitBalance'] = $entryDebitBalance_arr;


			$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
			$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

			$entryDebitDescription_arr = [];
			foreach ($jsonArr_entryDebitDescription as $product_arr) {
				$entryDebitDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitDescription']);
			$_POST['entryDebitDescription'] = $entryDebitDescription_arr;


			$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
			$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

			$costCenterDebitId_arr_0 = [];
			foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
				$costCenterDebitId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterDebitId_0']);
			$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




			$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
			$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

			$costCenterDebitAmount_arr_0 = [];
			foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
				$costCenterDebitAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterDebitAmount_0']);
			$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






			$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
			$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
			$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



			$r                = sizeof($entryDebitAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterDebitId_arr = [];
				foreach ($jsonArr_costCenterDebitId as $product_arr) {
					if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
						$costCenterDebitId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitId_' . $i]);
				$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
				unset($costCenterDebitId_arr);





				//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




				$costCenterDebitAmount_arr = [];
				foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
					if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
						$costCenterDebitAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_' . $i]);
				$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
				unset($costCenterDebitAmount_arr);
			}




			$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
			$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

			$entryCreditAccount_arr = [];
			foreach ($jsonArr_entryCreditAccount as $product_arr) {
				$entryCreditAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditAccount']);
			$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



			$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
			$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

			$entryCreditBalance_arr = [];
			foreach ($jsonArr_entryCreditBalance as $product_arr) {
				$entryCreditBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditBalance']);
			$_POST['entryCreditBalance'] = $entryCreditBalance_arr;


			$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
			$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

			$entryCreditDescription_arr = [];
			foreach ($jsonArr_entryCreditDescription as $product_arr) {
				$entryCreditDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditDescription']);
			$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


			$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
			$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

			$costCenterCreditId_arr_0 = [];
			foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
				$costCenterCreditId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterCreditId_0']);
			$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




			$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
			$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

			$costCenterCreditAmount_arr_0 = [];
			foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
				$costCenterCreditAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterCreditAmount_0']);
			$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






			$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
			$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
			$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



			$r                = sizeof($entryCreditAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterCreditId_arr = [];
				foreach ($jsonArr_costCenterCreditId as $product_arr) {
					if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
						$costCenterCreditId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditId_' . $i]);
				$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
				unset($costCenterCreditId_arr);





				//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




				$costCenterCreditAmount_arr = [];
				foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
					if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
						$costCenterCreditAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_' . $i]);
				$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
				unset($costCenterCreditAmount_arr);
			}

			/*$arr2 = array_map(function($jsonArr_costCenterDebitId) {
    return  $jsonArr_costCenterDebitId['name'];
},$jsonArr_costCenterDebitId);


				$this->sma->print_arrays($arr2);
				*/
			//$this->sma->print_arrays($_POST['costCenterDebitId_2']);
			//$this->sma->print_arrays($_POST);

			$_POST['userID'] = $data['userData']['userID'];



			if ($this->input->post('entryDate')) {
				$date = $this->sma->fld(trim($this->input->post('entryDate')));
			} else {
				$date = date('Y-m-d H:i:s');
			}

			if($this->input->post('entryType')=='Recurring_entry'){
				$date = $this->sma->fld(trim($this->input->post('start_date')));
				$start_date = $this->sma->fld(trim($this->input->post('start_date')));
				$end_date = $this->sma->fld(trim($this->input->post('end_date')));
				$latest_recurring_date = $this->sma->fld(trim($this->input->post('start_date')));
			}



			//$ts2 = strtotime($date);

			//$year1 = date('Y', strtotime($date));
			//$this->sma->print_arrays(date('Y', strtotime($date)));


			//$check_period=$this->accounts_model->getOpenAccountingPeriodByDate($date,date('Y', strtotime($date)));
			$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);


			$ch = 1;
			if ($check_period) {
				if ($check_period->period_status == 1) {
					$ch = 0;
				} else {
					$ch = 1;
				}
			} else {
				$count = $this->accounts_model->getAllAccountingPeriod()->count;
				if ($count == 1)
					$ch = 0;
				else
					$ch = 1;
			}






			# Check Multiple Duplicate Debits
			if ($ch == 0) {
				# Check Multiple Duplicate Debits/Credits
				//if (empty(array_intersect($_POST['entryDebitAccount'], $_POST['entryCreditAccount']))){
				$debitBalance  = 0.00;
				$creditBalance = 0.00;

				foreach ($_POST['entryDebitBalance'] as $key => $balance) {
					$debitBalance += $balance;
				}
				foreach ($_POST['entryCreditBalance'] as $key => $balance) {
					$creditBalance += $balance;
				}

				$_POST['created_by'] = (int)$this->session->userdata('userID');

				//$this->sma->print_arrays($debitBalance,$creditBalance);

				# Check Debit/Credit Balance Equals
				//if ($debitBalance == $creditBalance){

				//$this->sma->print_arrays($_POST);


				$costDebitCount = 0;
				foreach ($_POST['entryDebitAccount'] as $key => $acc) {
					if ($_POST['costCenterDebitId_' . $costDebitCount]) {
						$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
						$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
					} else {
						$costCenterDebitId[] = "";
						$costCenterDebitAmount[] = "";
					}
					$costDebitCount++;
				}

				$costCreditCount = 0;

				foreach ($_POST['entryCreditAccount'] as $key => $acc) {
					if ($_POST['costCenterCreditId_' . $costCreditCount]) {
						$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
						$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
					} else {
						$costCenterCreditId[] = "";
						$costCenterCreditAmount[] = "";
					}
					//$this->sma->print_arrays($costCenterCreditAmount);
					$costCreditCount++;
				}
				//$this->sma->print_arrays($costCenterCreditId,$costCenterCreditAmount);

				$entryStatusComment = NULL;
				if ($_FILES['entryFile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('entryFile')) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo              = $this->upload->file_name;
					$entryStatusComment = $photo;
				}
				//$this->sma->print_arrays($_POST);
				//$this->sma->print_arrays($_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription'],$date,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$_POST['entryDebitDescription'],$_POST['entryCreditDescription'],$entryStatusComment,$grpId,$coId,$brId);

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {


						$createCheck = $this->accounts_model->createEntry($_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment,$grpId,$coId,$brId,$_POST['timePeriod'],$start_date,$end_date,$latest_recurring_date);
                    }else{

						$createCheck = $this->accounts_model->createEntry($_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment,null,null,null,$_POST['timePeriod'],$start_date,$end_date,$latest_recurring_date);

					}



				if ($createCheck) {




					$this->session->set_flashdata('message', lang('You_have_successfully_created_an_entry'));
					admin_redirect('accounts/entries');
				} else {
					try {
						$this->sma->write_log('ERROR', 'An errors has been occurred while adding the entry (AddEntry:Accounts.php)','accounts','Data '.json_encode($_POST));
					} catch (Exception $e) {
                    }
					$this->session->set_flashdata('error', lang('failed_to_save'));
					admin_redirect('accounts/entries');
				}
				/*}
						else {
							$this->session->set_flashdata('error', lang('The_Debit_and_Credit_account_totals_must_equal_Please_try_again'));
							redirect($_SERVER['HTTP_REFERER']);

						}*/
				/*}
					else {
						//$this->session->set_flashdata('danger', 'You cannot have the same accounts for Debit and Credit. Please try again.');
						//$this->load->template('entries/create', $data);
							$this->session->set_flashdata('error', lang('You_cannot_have_the_same_accounts_for_Debit_and_Credit_Please_try_again'));
							admin_redirect('accounts/entries');

					}*/
			} else {
				//$this->session->set_flashdata('danger', 'You cannot have multiple matching Debit accounts. Please try again.');
				$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
				redirect($_SERVER['HTTP_REFERER']);

				//admin_redirect('accounts/entries');
			}
		} else {
			$this->data['accountsList']         = $this->accounts_model->getAllDetailsAccounts();
			$this->data['entryID']         = $this->accounts_model->getMaxEntryIDEntry();
			//$this->data['masterAccounts']         = $this->accounts_model->getAllMastersAccounts();
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
			//$this->sma->print_arrays($this->data);

			//$this->data['accounts_settings']         = $this->accounts_model->getAccountsSettings();
			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->data['mainGroups'] = $this->site->getMainGroup();
				$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

			}

			$bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Add_entry')]];
			$meta                          = ['page_title' => lang('Add_entry'), 'bc' => $bc];
			//$this->page_construct('accounts/add_entry', $meta, $this->data);
			$this->data['modal_js']   = $this->site->modal_js();
			$this->load->view($this->theme . 'accounts/add_entry', $this->data);
		}
	}



	public function edit_entry($entryID)
	{
		$this->sma->checkPermissions('entry_edit', true);
		$data['title']    = "Entries | Create Entry";
		$data['userData'] = $this->session->userdata();

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();

		$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];


		if (!empty($this->input->post())) {

			$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
			$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

			$entryDebitAccount_arr = [];
			foreach ($jsonArr_entryDebitAccount as $product_arr) {
				$entryDebitAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitAccount']);
			$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



			$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
			$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

			$entryDebitBalance_arr = [];
			foreach ($jsonArr_entryDebitBalance as $product_arr) {
				$entryDebitBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitBalance']);
			$_POST['entryDebitBalance'] = $entryDebitBalance_arr;


			$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
			$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

			$entryDebitDescription_arr = [];
			foreach ($jsonArr_entryDebitDescription as $product_arr) {
				$entryDebitDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitDescription']);
			$_POST['entryDebitDescription'] = $entryDebitDescription_arr;


			$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
			$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

			$costCenterDebitId_arr_0 = [];
			foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
				$costCenterDebitId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterDebitId_0']);
			$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




			$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
			$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

			$costCenterDebitAmount_arr_0 = [];
			foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
				$costCenterDebitAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterDebitAmount_0']);
			$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






			$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
			$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;






			$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
			$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



			$r                = sizeof($entryDebitAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 0; $i < $r; $i++) {


				$costCenterDebitId_arr = [];
				foreach ($jsonArr_costCenterDebitId as $product_arr) {
					if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
						$costCenterDebitId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitId_' . $i]);
				$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
				unset($costCenterDebitId_arr);





				//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




				$costCenterDebitAmount_arr = [];
				foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
					if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
						$costCenterDebitAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_' . $i]);
				$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
				unset($costCenterDebitAmount_arr);
			}




			$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
			$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

			$entryCreditAccount_arr = [];
			foreach ($jsonArr_entryCreditAccount as $product_arr) {
				$entryCreditAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditAccount']);
			$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



			$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
			$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

			$entryCreditBalance_arr = [];
			foreach ($jsonArr_entryCreditBalance as $product_arr) {
				$entryCreditBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditBalance']);
			$_POST['entryCreditBalance'] = $entryCreditBalance_arr;


			$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
			$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

			$entryCreditDescription_arr = [];
			foreach ($jsonArr_entryCreditDescription as $product_arr) {
				$entryCreditDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditDescription']);
			$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


			$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
			$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

			$costCenterCreditId_arr_0 = [];
			foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
				/*$flage=substr($product_arr->name,19);
				$flage= str_replace("[]","",$flage);

				if($flage=="0")
				$costCenterCreditId_arr_0[]=  $product_arr->value;
			else{
				//$_POST['jsonArr_costCenterCreditId']=json_encode((array)$product_arr);
					array_push($_POST['jsonArr_costCenterCreditId'], json_encode((array)$product_arr));

			}
			*/
				$costCenterCreditId_arr_0[] =  $product_arr->value;
			}
			//$this->sma->print_arrays($_POST);

			unset($_POST['costCenterCreditId_0']);
			$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




			$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
			$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

			$costCenterCreditAmount_arr_0 = [];
			foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
				$costCenterCreditAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterCreditAmount_0']);
			$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






			$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
			$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;






			$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
			$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



			$r                = sizeof($entryCreditAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 0; $i < $r; $i++) {


				$costCenterCreditId_arr = [];
				foreach ($jsonArr_costCenterCreditId as $product_arr) {
					if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
						$costCenterCreditId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditId_' . $i]);
				$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
				unset($costCenterCreditId_arr);









				$costCenterCreditAmount_arr = [];
				foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
					if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
						$costCenterCreditAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_' . $i]);
				$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
				unset($costCenterCreditAmount_arr);
			}
			//$this->sma->print_arrays($_POST);

			/*$arr2 = array_map(function($jsonArr_costCenterDebitId) {
    return  $jsonArr_costCenterDebitId['name'];
},$jsonArr_costCenterDebitId);


				$this->sma->print_arrays($arr2);
				*/
			//$this->sma->print_arrays($_POST['costCenterDebitId_2']);
			//$this->sma->print_arrays($_POST);

			$_POST['userID'] = $data['userData']['userID'];


			if ($this->input->post('entryDate')) {
				$date = $this->sma->fld(trim($this->input->post('entryDate')));
			} else {
				$date = date('Y-m-d H:i:s');
			}


			$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
			$ch = 1;
			if ($check_period) {
				if ($check_period->period_status == 1) {
					$ch = 0;
				} else {
					$ch = 1;
				}
			} else {
				$count = $this->accounts_model->getAllAccountingPeriod()->count;
				if ($count == 1)
					$ch = 0;
				else
					$ch = 1;
			}






			# Check Multiple Duplicate Debits
			if ($ch == 0) {
				# Check Multiple Duplicate Debits/Credits
				//if (empty(array_intersect($_POST['entryDebitAccount'], $_POST['entryCreditAccount']))){
				$debitBalance  = 0.00;
				$creditBalance = 0.00;

				foreach ($_POST['entryDebitBalance'] as $key => $balance) {
					$debitBalance += $balance;
				}
				foreach ($_POST['entryCreditBalance'] as $key => $balance) {
					$creditBalance += $balance;
				}

				$_POST['created_by'] = (int)$this->session->userdata('userID');

				if ($this->sma->formatDecimal(abs($debitBalance)) == $this->sma->formatDecimal(abs($creditBalance)) && $creditBalance != 0 && $creditBalance) {
					//$this->sma->print_arrays($debitBalance,$creditBalance,"1");
					//$this->sma->print_arrays($creditBalance);

					# Check Debit/Credit Balance Equals
					//if ($debitBalance == $creditBalance && $creditBalance!=0 && $creditBalance){
					//$this->sma->print_arrays($debitBalance,$creditBalance);


					//$this->sma->print_arrays($_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription']);


					$costDebitCount = 0;
					foreach ($_POST['entryDebitAccount'] as $key => $acc) {
						if ($_POST['costCenterDebitId_' . $costDebitCount]) {
							$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
							$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
						} else {
							$costCenterDebitId[] = "";
							$costCenterDebitAmount[] = "";
						}
						$costDebitCount++;
					}

					$costCreditCount = 0;

					foreach ($_POST['entryCreditAccount'] as $key => $acc) {
						if ($_POST['costCenterCreditId_' . $costCreditCount]) {
							$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
							$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
						} else {
							$costCenterCreditId[] = "";
							$costCenterCreditAmount[] = "";
						}
						//$this->sma->print_arrays($costCenterCreditAmount);
						$costCreditCount++;
					}

					$entryStatusComment = NULL;
					if ($_FILES['entryFile']['size'] > 0) {
						$this->load->library('upload');
						$config['upload_path']   = $this->digital_upload_path;
						$config['allowed_types'] = $this->digital_file_types;
						$config['max_size']      = $this->allowed_file_size;
						$config['overwrite']     = false;
						$config['encrypt_name']  = true;
						$this->upload->initialize($config);
						if (!$this->upload->do_upload('entryFile')) {
							$error = $this->upload->display_errors();
							$this->session->set_flashdata('error', $error);
							redirect($_SERVER['HTTP_REFERER']);
						}
						$photo              = $this->upload->file_name;
						$entryStatusComment = $photo;
					}


					//$this->sma->print_arrays($entryID,$_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription'],$date,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$_POST['entryDebitDescription'],$_POST['entryCreditDescription'],$entryStatusComment);

					$createCheck = $this->accounts_model->updateEntry($entryID, $_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment);
					if ($createCheck) {


						$this->session->set_flashdata('message', lang('You_have_successfully_updated_an_entry'));
						admin_redirect('accounts/entries');
					} else {
						try {
							$this->sma->write_log('ERROR', 'An errors has been occurred while update the entry (UpdateEntry:Accounts.php)','accounts','EntryID ('.$entryID.') Data '.json_encode($_POST));
						} catch (Exception $e) {
						}

						$this->session->set_flashdata('error', lang('failed_to_save'));
						admin_redirect('accounts/entries');
					}
				} else {
					$this->session->set_flashdata('error', lang('The_Debit_and_Credit_account_totals_must_equal_Please_try_again'));
					redirect($_SERVER['HTTP_REFERER']);
				}
				/*}
					else {
						//$this->session->set_flashdata('danger', 'You cannot have the same accounts for Debit and Credit. Please try again.');
						//$this->load->template('entries/create', $data);
							$this->session->set_flashdata('error', lang('You_cannot_have_the_same_accounts_for_Debit_and_Credit_Please_try_again'));
							admin_redirect('accounts/entries');

					}*/
			} else {
				//$this->session->set_flashdata('danger', 'You cannot have multiple matching Debit accounts. Please try again.');
				$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
				redirect($_SERVER['HTTP_REFERER']);

				//admin_redirect('accounts/entries');
			}
		} else {
			$this->data['accountsList']         = $this->accounts_model->getAllDetailsAccounts();
			$this->data['entryID']         = $this->accounts_model->getMaxEntryIDEntry();
			//$this->data['masterAccounts']         = $this->accounts_model->getAllMastersAccounts();
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();


			$this->data['entry']         = $this->accounts_model->getEntries($entryID);
			//
			if ($this->data['entry'][0]->entryStatus == '1') {
				//$this->sma->print_arrays($this->data['entry'][0]>entryStatus);
				$this->session->set_flashdata('error', lang('You_cannot_Edit_an_approved_entry'));
				//$this->sma->send_json(['error' => 1, 'msg' => lang('purchase_x_action')]);
				//redirect($_SERVER['HTTP_REFERER']);
				die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('accounts/entries') . "'; }, 1);</script>");
			} else {

				$bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Edit_entry')]];
				$meta                          = ['page_title' => lang('Edit_entry'), 'bc' => $bc];
				//$this->page_construct('accounts/add_entry', $meta, $this->data);
				$this->data['modal_js']   = $this->site->modal_js();
				$this->load->view($this->theme . 'accounts/edit_entry', $this->data);
			}
		}
	}


	public function duplicate_entry($entryID)
	{
		$this->sma->checkPermissions('entry_add', true);
		$data['title']    = "Entries | Create Entry";
		$data['userData'] = $this->session->userdata();


		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

		if (!empty($this->input->post())) {

			$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
			$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

			$entryDebitAccount_arr = [];
			foreach ($jsonArr_entryDebitAccount as $product_arr) {
				$entryDebitAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitAccount']);
			$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



			$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
			$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

			$entryDebitBalance_arr = [];
			foreach ($jsonArr_entryDebitBalance as $product_arr) {
				$entryDebitBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitBalance']);
			$_POST['entryDebitBalance'] = $entryDebitBalance_arr;


			$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
			$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

			$entryDebitDescription_arr = [];
			foreach ($jsonArr_entryDebitDescription as $product_arr) {
				$entryDebitDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitDescription']);
			$_POST['entryDebitDescription'] = $entryDebitDescription_arr;


			$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
			$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

			$costCenterDebitId_arr_0 = [];
			foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
				$costCenterDebitId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterDebitId_0']);
			$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




			$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
			$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

			$costCenterDebitAmount_arr_0 = [];
			foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
				$costCenterDebitAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterDebitAmount_0']);
			$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






			$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
			$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
			$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



			$r                = sizeof($entryDebitAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterDebitId_arr = [];
				foreach ($jsonArr_costCenterDebitId as $product_arr) {
					if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
						$costCenterDebitId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitId_' . $i]);
				$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
				unset($costCenterDebitId_arr);





				//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




				$costCenterDebitAmount_arr = [];
				foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
					if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
						$costCenterDebitAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_' . $i]);
				$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
				unset($costCenterDebitAmount_arr);
			}




			$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
			$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

			$entryCreditAccount_arr = [];
			foreach ($jsonArr_entryCreditAccount as $product_arr) {
				$entryCreditAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditAccount']);
			$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



			$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
			$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

			$entryCreditBalance_arr = [];
			foreach ($jsonArr_entryCreditBalance as $product_arr) {
				$entryCreditBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditBalance']);
			$_POST['entryCreditBalance'] = $entryCreditBalance_arr;


			$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
			$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

			$entryCreditDescription_arr = [];
			foreach ($jsonArr_entryCreditDescription as $product_arr) {
				$entryCreditDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditDescription']);
			$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


			$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
			$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

			$costCenterCreditId_arr_0 = [];
			foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
				$costCenterCreditId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterCreditId_0']);
			$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




			$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
			$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

			$costCenterCreditAmount_arr_0 = [];
			foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
				$costCenterCreditAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterCreditAmount_0']);
			$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






			$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
			$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
			$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



			$r                = sizeof($entryCreditAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterCreditId_arr = [];
				foreach ($jsonArr_costCenterCreditId as $product_arr) {
					if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
						$costCenterCreditId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditId_' . $i]);
				$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
				unset($costCenterCreditId_arr);





				//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




				$costCenterCreditAmount_arr = [];
				foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
					if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
						$costCenterCreditAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_' . $i]);
				$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
				unset($costCenterCreditAmount_arr);
			}

			/*$arr2 = array_map(function($jsonArr_costCenterDebitId) {
    return  $jsonArr_costCenterDebitId['name'];
},$jsonArr_costCenterDebitId);


				$this->sma->print_arrays($arr2);
				*/
			//$this->sma->print_arrays($_POST['costCenterDebitId_2']);
			//$this->sma->print_arrays($_POST);

			$_POST['userID'] = $data['userData']['userID'];


			if ($this->input->post('entryDate')) {
				$date = $this->sma->fld(trim($this->input->post('entryDate')));
			} else {
				$date = date('Y-m-d H:i:s');
			}


			$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
			$ch = 1;
			if ($check_period) {
				if ($check_period->period_status == 1) {
					$ch = 0;
				} else {
					$ch = 1;
				}
			} else {
				$count = $this->accounts_model->getAllAccountingPeriod()->count;
				if ($count == 1)
					$ch = 0;
				else
					$ch = 1;
			}






			# Check Multiple Duplicate Debits
			if ($ch == 0) {
				# Check Multiple Duplicate Debits/Credits
				//if (empty(array_intersect($_POST['entryDebitAccount'], $_POST['entryCreditAccount']))){
				$debitBalance  = 0.00;
				$creditBalance = 0.00;

				foreach ($_POST['entryDebitBalance'] as $key => $balance) {
					$debitBalance += $balance;
				}
				foreach ($_POST['entryCreditBalance'] as $key => $balance) {
					$creditBalance += $balance;
				}

				$_POST['created_by'] = (int)$this->session->userdata('userID');

				//$this->sma->print_arrays($debitBalance,$creditBalance);

				# Check Debit/Credit Balance Equals
				//if ($debitBalance == $creditBalance){

				//$this->sma->print_arrays($_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription']);


				$costDebitCount = 0;
				foreach ($_POST['entryDebitAccount'] as $key => $acc) {
					if ($_POST['costCenterDebitId_' . $costDebitCount]) {
						$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
						$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
					} else {
						$costCenterDebitId[] = "";
						$costCenterDebitAmount[] = "";
					}
					$costDebitCount++;
				}

				$costCreditCount = 0;

				foreach ($_POST['entryCreditAccount'] as $key => $acc) {
					if ($_POST['costCenterCreditId_' . $costCreditCount]) {
						$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
						$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
					} else {
						$costCenterCreditId[] = "";
						$costCenterCreditAmount[] = "";
					}
					//$this->sma->print_arrays($costCenterCreditAmount);
					$costCreditCount++;
				}

				$entryStatusComment = NULL;
				if ($_FILES['entryFile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('entryFile')) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo              = $this->upload->file_name;
					$entryStatusComment = $photo;
				}


				//$this->sma->print_arrays(json_encode((Array) $costCenterDebitId));
				//$createCheck = $this->accounts_model->updateEntry($entryID,$_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription'],$date,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$_POST['entryDebitDescription'],$_POST['entryCreditDescription'],$entryStatusComment);
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
				{


					$createCheck = $this->accounts_model->createEntry($_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment,$grpId,$coId,$brId);
				}else{
					$createCheck = $this->accounts_model->createEntry($_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment);
				}
				if ($createCheck) {

					$this->session->set_flashdata('message', lang('You_have_successfully_created_an_entry'));
					admin_redirect('accounts/entries');
				} else {
					try {
						$this->sma->write_log('ERROR', 'An errors has been occurred while duplicate the entry (DuplicateEntry:Accounts.php)','accounts','EntryID ('.$entryID.') Data '.json_encode($_POST));
					} catch (Exception $e) {
                    }
					$this->session->set_flashdata('error', lang('failed_to_save'));
					admin_redirect('accounts/entries');
				}
				/*}
						else {
							$this->session->set_flashdata('error', lang('The_Debit_and_Credit_account_totals_must_equal_Please_try_again'));
							redirect($_SERVER['HTTP_REFERER']);

						}*/
				/*}
					else {
						//$this->session->set_flashdata('danger', 'You cannot have the same accounts for Debit and Credit. Please try again.');
						//$this->load->template('entries/create', $data);
							$this->session->set_flashdata('error', lang('You_cannot_have_the_same_accounts_for_Debit_and_Credit_Please_try_again'));
							admin_redirect('accounts/entries');

					}*/
			} else {
				//$this->session->set_flashdata('danger', 'You cannot have multiple matching Debit accounts. Please try again.');
				$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
				redirect($_SERVER['HTTP_REFERER']);

				//admin_redirect('accounts/entries');
			}
		} else {
			$this->data['accountsList']         = $this->accounts_model->getAllDetailsAccounts();
			$this->data['entryID']         = $this->accounts_model->getMaxEntryIDEntry();
			//$this->data['masterAccounts']         = $this->accounts_model->getAllMastersAccounts();
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();


			$this->data['entry']         = $this->accounts_model->getEntries($entryID);

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->data['mainGroups'] = $this->site->getMainGroup();
				$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

			}
			//

			$bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Duplicate_entry')]];
			$meta                          = ['page_title' => lang('Duplicate_entry'), 'bc' => $bc];
			//$this->page_construct('accounts/add_entry', $meta, $this->data);
			$this->data['modal_js']   = $this->site->modal_js();
			$this->load->view($this->theme . 'accounts/duplicate_entry', $this->data);
		}
	}

	public function reverse_entry($entryID)
	{
		$this->sma->checkPermissions('entry_add', true);
		$data['title']    = "Entries | Create Entry";
		$data['userData'] = $this->session->userdata();

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');


		if (!empty($this->input->post())) {

			$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
			$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

			$entryDebitAccount_arr = [];
			foreach ($jsonArr_entryDebitAccount as $product_arr) {
				$entryDebitAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitAccount']);
			$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



			$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
			$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

			$entryDebitBalance_arr = [];
			foreach ($jsonArr_entryDebitBalance as $product_arr) {
				$entryDebitBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitBalance']);
			$_POST['entryDebitBalance'] = $entryDebitBalance_arr;


			$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
			$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

			$entryDebitDescription_arr = [];
			foreach ($jsonArr_entryDebitDescription as $product_arr) {
				$entryDebitDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryDebitDescription']);
			$_POST['entryDebitDescription'] = $entryDebitDescription_arr;


			$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
			$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

			$costCenterDebitId_arr_0 = [];
			foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
				$costCenterDebitId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterDebitId_0']);
			$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




			$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
			$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

			$costCenterDebitAmount_arr_0 = [];
			foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
				$costCenterDebitAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterDebitAmount_0']);
			$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






			$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
			$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
			$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



			$r                = sizeof($entryDebitAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterDebitId_arr = [];
				foreach ($jsonArr_costCenterDebitId as $product_arr) {
					if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
						$costCenterDebitId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitId_' . $i]);
				$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
				unset($costCenterDebitId_arr);





				//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




				$costCenterDebitAmount_arr = [];
				foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
					if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
						$costCenterDebitAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_' . $i]);
				$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
				unset($costCenterDebitAmount_arr);
			}




			$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
			$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

			$entryCreditAccount_arr = [];
			foreach ($jsonArr_entryCreditAccount as $product_arr) {
				$entryCreditAccount_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditAccount']);
			$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



			$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
			$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

			$entryCreditBalance_arr = [];
			foreach ($jsonArr_entryCreditBalance as $product_arr) {
				$entryCreditBalance_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditBalance']);
			$_POST['entryCreditBalance'] = $entryCreditBalance_arr;


			$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
			$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

			$entryCreditDescription_arr = [];
			foreach ($jsonArr_entryCreditDescription as $product_arr) {
				$entryCreditDescription_arr[] =  $product_arr->value;
			}

			unset($_POST['entryCreditDescription']);
			$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


			$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
			$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

			$costCenterCreditId_arr_0 = [];
			foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
				$costCenterCreditId_arr_0[] =  $product_arr->value;
			}

			unset($_POST['costCenterCreditId_0']);
			$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




			$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
			$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

			$costCenterCreditAmount_arr_0 = [];
			foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
				$costCenterCreditAmount_arr_0[] =  $product_arr->value;
			}
			unset($_POST['costCenterCreditAmount_0']);
			$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






			$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
			$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;



			//$this->sma->print_arrays($_POST);



			$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
			$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



			$r                = sizeof($entryCreditAccount_arr);
			//$this->sma->print_arrays($i);
			for ($i = 1; $i < $r; $i++) {


				$costCenterCreditId_arr = [];
				foreach ($jsonArr_costCenterCreditId as $product_arr) {
					if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
						$costCenterCreditId_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditId_' . $i]);
				$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
				unset($costCenterCreditId_arr);





				//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




				$costCenterCreditAmount_arr = [];
				foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
					if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
						$costCenterCreditAmount_arr[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_' . $i]);
				$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
				unset($costCenterCreditAmount_arr);
			}

			/*$arr2 = array_map(function($jsonArr_costCenterDebitId) {
    return  $jsonArr_costCenterDebitId['name'];
},$jsonArr_costCenterDebitId);


				$this->sma->print_arrays($arr2);
				*/
			//$this->sma->print_arrays($_POST['costCenterDebitId_2']);
			//$this->sma->print_arrays($_POST);

			$_POST['userID'] = $data['userData']['userID'];


			if ($this->input->post('entryDate')) {
				$date = $this->sma->fld(trim($this->input->post('entryDate')));
			} else {
				$date = date('Y-m-d H:i:s');
			}


			$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
			$ch = 1;
			if ($check_period) {
				if ($check_period->period_status == 1) {
					$ch = 0;
				} else {
					$ch = 1;
				}
			} else {
				$count = $this->accounts_model->getAllAccountingPeriod()->count;
				if ($count == 1)
					$ch = 0;
				else
					$ch = 1;
			}






			# Check Multiple Duplicate Debits
			if ($ch == 0) {
				# Check Multiple Duplicate Debits/Credits
				//if (empty(array_intersect($_POST['entryDebitAccount'], $_POST['entryCreditAccount']))){
				$debitBalance  = 0.00;
				$creditBalance = 0.00;

				foreach ($_POST['entryDebitBalance'] as $key => $balance) {
					$debitBalance += $balance;
				}
				foreach ($_POST['entryCreditBalance'] as $key => $balance) {
					$creditBalance += $balance;
				}

				$_POST['created_by'] = (int)$this->session->userdata('userID');

				//$this->sma->print_arrays($debitBalance,$creditBalance);

				# Check Debit/Credit Balance Equals
				//if ($debitBalance == $creditBalance){

				//$this->sma->print_arrays($_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription']);


				$costDebitCount = 0;
				foreach ($_POST['entryDebitAccount'] as $key => $acc) {
					if ($_POST['costCenterDebitId_' . $costDebitCount]) {
						$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
						$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
					} else {
						$costCenterDebitId[] = "";
						$costCenterDebitAmount[] = "";
					}
					$costDebitCount++;
				}

				$costCreditCount = 0;

				foreach ($_POST['entryCreditAccount'] as $key => $acc) {
					if ($_POST['costCenterCreditId_' . $costCreditCount]) {
						$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
						$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
					} else {
						$costCenterCreditId[] = "";
						$costCenterCreditAmount[] = "";
					}
					//$this->sma->print_arrays($costCenterCreditAmount);
					$costCreditCount++;
				}

				$entryStatusComment = NULL;
				if ($_FILES['entryFile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload('entryFile')) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo              = $this->upload->file_name;
					$entryStatusComment = $photo;
				}


				//$this->sma->print_arrays(json_encode((Array) $costCenterDebitId));
				//$createCheck = $this->accounts_model->updateEntry($entryID,$_POST['entryDebitAccount'],$_POST['entryCreditAccount'],$_POST['entryDebitBalance'],$_POST['entryCreditBalance'],$_POST['entryType'],$_POST['entryDescription'],$date,json_encode((Array) $costCenterDebitId),json_encode((Array) $costCenterDebitAmount),json_encode((Array) $costCenterCreditId),json_encode((Array) $costCenterCreditAmount),$_POST['entryDebitDescription'],$_POST['entryCreditDescription'],$entryStatusComment);


				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
				{


					$createCheck = $this->accounts_model->createEntry($_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment,$grpId,$coId,$brId);
				}else{
					$createCheck = $this->accounts_model->createEntry($_POST['entryDebitAccount'], $_POST['entryCreditAccount'], $_POST['entryDebitBalance'], $_POST['entryCreditBalance'], $_POST['entryType'], $_POST['entryDescription'], $date, json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $_POST['entryDebitDescription'], $_POST['entryCreditDescription'], $entryStatusComment);
				}

				if ($createCheck) {


					$this->session->set_flashdata('message', lang('You_have_successfully_created_an_entry'));
					admin_redirect('accounts/entries');
				} else {
					try {
						$this->sma->write_log('ERROR', 'An errors has been occurred while reverse the entry (ReverseEntry:Accounts.php)','accounts','EntryID ('.$entryID.') Data '.json_encode($_POST));
					} catch (Exception $e) {
                    }
					$this->session->set_flashdata('error', lang('failed_to_save'));
					admin_redirect('accounts/entries');
				}
				/*}
						else {
							$this->session->set_flashdata('error', lang('The_Debit_and_Credit_account_totals_must_equal_Please_try_again'));
							redirect($_SERVER['HTTP_REFERER']);

						}*/
				/*}
					else {
						//$this->session->set_flashdata('danger', 'You cannot have the same accounts for Debit and Credit. Please try again.');
						//$this->load->template('entries/create', $data);
							$this->session->set_flashdata('error', lang('You_cannot_have_the_same_accounts_for_Debit_and_Credit_Please_try_again'));
							admin_redirect('accounts/entries');

					}*/
			} else {
				//$this->session->set_flashdata('danger', 'You cannot have multiple matching Debit accounts. Please try again.');
				$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
				redirect($_SERVER['HTTP_REFERER']);

				//admin_redirect('accounts/entries');
			}
		} else {
			$this->data['accountsList']         = $this->accounts_model->getAllDetailsAccounts();
			$this->data['entryID']         = $this->accounts_model->getMaxEntryIDEntry();
			//$this->data['masterAccounts']         = $this->accounts_model->getAllMastersAccounts();
			$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();


			$this->data['entry']         = $this->accounts_model->getEntries($entryID);
			//
			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->data['mainGroups'] = $this->site->getMainGroup();
				$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

			}
			$bc                            = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('Reverse_entry')]];
			$meta                          = ['page_title' => lang('Reverse_entry'), 'bc' => $bc];
			//$this->page_construct('accounts/add_entry', $meta, $this->data);
			$this->data['modal_js']   = $this->site->modal_js();
			$this->load->view($this->theme . 'accounts/reverse_entry', $this->data);
		}
	}

	public function update_status($id)
	{
		$this->sma->checkPermissions('update_entries_status', true);
		$this->form_validation->set_rules('entryStatus', lang('approve'), 'required');

		if ($this->form_validation->run() == true) {

			$entryDetails = $this->accounts_model->getEntryByEntryID($id);
			$s = $entryDetails->entryDate;
			$dt = new DateTime($s);

			$date = $dt->format('Y-m-d');


			$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
			$ch = 1;
			if ($check_period) {
				if ($check_period->period_status == 1) {
					$ch = 0;
				} else {
					$ch = 1;
				}
			} else {
				$count = $this->accounts_model->getAllAccountingPeriod()->count;
				if ($count == 1)
					$ch = 0;
				else
					$ch = 1;
			}






			# Check Multiple Duplicate Debits
			if ($ch == 0) {
			} else {
				//$this->session->set_flashdata('danger', 'You cannot have multiple matching Debit accounts. Please try again.');
				$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
				redirect($_SERVER['HTTP_REFERER']);

				//admin_redirect('accounts/entries');
			}

			$entryStatus = $this->input->post('entryStatus');
			$entryDescription   = $this->input->post('entryDescription') ? $this->sma->decode_html($this->input->post('entryDescription')) : $entryDetails->entryDescription;
		} elseif ($this->input->post('update')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'sales');
		}










		if ($this->form_validation->run() == true && $this->accounts_model->updateStatus($id, $entryStatus, $entryDescription)) {
			$this->session->set_flashdata('message', lang('The_status_has_been_successfully_updated'));
			admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/update_status');
		} else {
			$this->data['inv']      = $this->accounts_model->getEntryByEntryID($id);


			//$this->sma->print_arrays($date);
			//$this->sales_model->getInvoiceByID($id);

			$this->data['modal_js'] = $this->site->modal_js();
			$this->load->view($this->theme . 'accounts/update_status', $this->data);
		}
	}

	public function account_actions()
	{


		$this->form_validation->set_rules('form_action', lang('form_action'), 'required');
		//$this->sma->print_arrays($_POST);
		if ($this->form_validation->run() == true) {

			if (!empty($_POST['val'])) {
				if ($this->input->post('form_action') == 'update_status') {
					$this->sma->checkPermissions('update_entries_status', true);
					foreach ($_POST['val'] as $id) {
						$accounts_model = $this->accounts_model->getEntryByEntryID($id);
						//$this->sma->print_arrays($accounts_model);
						if ($accounts_model->entryStatus == "0")
							$this->accounts_model->updateStatus($id, "1", $accounts_model->entryDescription);
					}
					$this->session->set_flashdata('message', lang('The_status_has_been_successfully_updated'));
					redirect($_SERVER['HTTP_REFERER']);
				}


				if ($this->input->post('form_action') == 'update_status_unapprove') {
					$this->sma->checkPermissions('update_entries_status', true);
					foreach ($_POST['val'] as $id) {
						$accounts_model = $this->accounts_model->getEntryByEntryID($id);
						//$this->sma->print_arrays($accounts_model);
						if ($accounts_model->entryStatus == "1")
							$this->accounts_model->updateStatus($id, "0", $accounts_model->entryDescription);
					}
					$this->session->set_flashdata('message', lang('The_status_has_been_successfully_updated'));
					redirect($_SERVER['HTTP_REFERER']);
				}
			} else {
				$this->session->set_flashdata('error', lang('No_entry_selected'));
				redirect($_SERVER['HTTP_REFERER']);
			}
		} else {
			$this->session->set_flashdata('error', validation_errors());
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function update_accounting_period_status($id)
	{
		$this->sma->checkPermissions('update_accounting_period_status', true);
		$this->form_validation->set_rules('period_status', lang('Status'), 'required');
		$accounting_period = $this->accounts_model->getAccountingPeriodByID($id);
		$all_accounting_period = $this->accounts_model->getAllOpenAccountingPeriodByYear($accounting_period->period_year);
		$period_status = $this->input->post('period_status');


		if ($this->form_validation->run() == true) {



			if ($period_status == $accounting_period->period_status) {
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/update_accounting_period_status');
			}

			if ($all_accounting_period && ($period_status == 1 || $period_status == 2)) {
				//$this->session->set_flashdata('error', lang('There_is_an_open_accounting_period_It_must_be_closed_first'));
				//	admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/update_accounting_period_status');
			}
			//$this->sma->print_arrays($period_status);

		} elseif ($this->input->post('update')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts');
		}
		if ($this->form_validation->run() == true && $this->accounts_model->updatePeriodStatus($id, $period_status)) {
			$this->session->set_flashdata('message', lang('The_status_has_been_successfully_updated'));
			admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/update_accounting_period_status');
		} else {
			$this->data['inv']      = $accounts_model = $accounting_period;
			//$this->sales_model->getInvoiceByID($id);

			$this->data['modal_js'] = $this->site->modal_js();
			$this->load->view($this->theme . 'accounts/update_accounting_period_status', $this->data);
		}
	}

	public function dashboard()
	{
		if ($this->Settings->version == '2.3') {
			$this->session->set_flashdata('warning', 'Please complete your update by synchronizing your database.');
			admin_redirect('sync');
		}

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		if($this->Settings->spark_ui==1){

		// Period Filter Determination
		$period = $this->input->get('period') ? trim((string)$this->input->get('period')) : 'this_month';
		$warehouse_id = $this->session->userdata('warehouse_id');

		$start_datetime = null;
		$end_datetime = null;
		$display_range = '';

		switch ($period) {
			case 'today':
				$start_datetime = date('Y-m-d 00:00:00');
				$end_datetime = date('Y-m-d 23:59:59');
				$display_range = date('Y-m-d');
				break;

			case 'this_week':
				// Week starts on Saturday (Saudi standard)
				$w = (int)date('w'); // 0 (Sun) to 6 (Sat)
				$days_from_sat = ($w + 1) % 7;
				$sat_ts = strtotime("-$days_from_sat days");
				$fri_ts = strtotime("+" . (6 - $days_from_sat) . " days");
				$start_datetime = date('Y-m-d 00:00:00', $sat_ts);
				$end_datetime = date('Y-m-d 23:59:59', $fri_ts);
				$display_range = date('Y-m-d', $sat_ts) . ' → ' . date('Y-m-d', $fri_ts);
				break;

			case 'this_quarter':
				$month = (int)date('n');
				$quarter = ceil($month / 3);
				$start_m = sprintf('%02d', ($quarter - 1) * 3 + 1);
				$end_m = sprintf('%02d', $quarter * 3);
				$start_datetime = date("Y-$start_m-01 00:00:00");
				$last_day_q = date('t', strtotime(date("Y-$end_m-01")));
				$end_datetime = date("Y-$end_m-$last_day_q 23:59:59");
				$display_range = date("Y-$start_m-01") . ' → ' . date("Y-$end_m-$last_day_q");
				break;

			case 'this_year':
				$start_datetime = date('Y-01-01 00:00:00');
				$end_datetime = date('Y-12-31 23:59:59');
				$display_range = date('Y-01-01') . ' → ' . date('Y-12-31');
				break;

			case 'fiscal_year':
				$q_fiscal = $this->db->get_where('gl_accounting_period', ['period_year_status' => 1], 1);
				if ($q_fiscal && $q_fiscal->num_rows() > 0) {
					$fiscal_row = $q_fiscal->row();
					$start_datetime = $fiscal_row->period_from_date . ' 00:00:00';
					$end_datetime = $fiscal_row->period_to_date . ' 23:59:59';
					$display_range = $fiscal_row->period_from_date . ' → ' . $fiscal_row->period_to_date;
				} else {
					$start_datetime = date('Y-01-01 00:00:00');
					$end_datetime = date('Y-12-31 23:59:59');
					$display_range = date('Y-01-01') . ' → ' . date('Y-12-31');
				}
				break;

			case 'custom':
				$from_raw = $this->input->get('from') ?: ($this->input->get('start_date') ?: date('Y-m-01'));
				$to_raw = $this->input->get('to') ?: ($this->input->get('end_date') ?: date('Y-m-d'));
				$from_ts = strtotime($from_raw) ? strtotime($from_raw) : strtotime(date('Y-m-01'));
				$to_ts = strtotime($to_raw) ? strtotime($to_raw) : time();
				if ($from_ts > $to_ts) {
					$tmp = $from_ts;
					$from_ts = $to_ts;
					$to_ts = $tmp;
				}
				$start_datetime = date('Y-m-d 00:00:00', $from_ts);
				$end_datetime = date('Y-m-d 23:59:59', $to_ts);
				$display_range = date('Y-m-d', $from_ts) . ' → ' . date('Y-m-d', $to_ts);
				break;

			case 'all':
				$start_datetime = null;
				$end_datetime = null;
				$display_range = lang('all_time') ?: 'كل الفترات';
				break;

			case 'this_month':
			default:
				$period = 'this_month';
				$start_datetime = date('Y-m-01 00:00:00');
				$end_datetime = date('Y-m-t 23:59:59');
				$display_range = date('Y-m-01') . ' → ' . date('Y-m-t');
				break;
		}

		$this->data['active_period'] = $period;
		$this->data['start_datetime'] = $start_datetime;
		$this->data['end_datetime'] = $end_datetime;
		$this->data['display_date_range'] = $display_range;
		$this->data['from_date_input'] = $start_datetime ? substr($start_datetime, 0, 10) : date('Y-m-01');
		$this->data['to_date_input'] = $end_datetime ? substr($end_datetime, 0, 10) : date('Y-m-d');

		// Modern Aggregated Real-Time Accounting Data
		$this->data['stats'] = $this->accounts_model->getAccountingDashboardStats($start_datetime, $end_datetime, $warehouse_id);
		$this->data['financial_position'] = $this->accounts_model->getFinancialPositionSummary($start_datetime, $end_datetime, $warehouse_id);
		$this->data['monthly_trends'] = $this->accounts_model->getAccountingMonthlyTrends(12);
		$this->data['expense_breakdown'] = $this->accounts_model->getTopExpenseAccounts(6, $start_datetime, $end_datetime, $warehouse_id);
		$this->data['revenue_breakdown'] = $this->accounts_model->getTopRevenueAccounts(6, $start_datetime, $end_datetime, $warehouse_id);
		$this->data['cash_bank_balances'] = $this->accounts_model->getCashAndBankBalances(6, $end_datetime, $warehouse_id);
		$this->data['top_receivables'] = $this->accounts_model->getTopReceivables(5, $start_datetime, $end_datetime, $warehouse_id);
		$this->data['top_payables'] = $this->accounts_model->getTopPayables(5, $start_datetime, $end_datetime, $warehouse_id);
		$this->data['attention_items'] = $this->accounts_model->getAccountingAttentionRequired($start_datetime, $end_datetime, $warehouse_id);
		$this->data['recent_transactions'] = $this->accounts_model->getRecentAccountingTransactions(8, $start_datetime, $end_datetime, $warehouse_id);

		}else{
			$this->data['sales']     = $this->db_model->getLatestSales();
			$this->data['quotes']    = $this->db_model->getLastestQuotes();
			$this->data['purchases'] = $this->db_model->getLatestPurchases();
			$this->data['transfers'] = $this->db_model->getLatestTransfers();
			$this->data['customers'] = $this->db_model->getLatestCustomers();
			$this->data['suppliers'] = $this->db_model->getLatestSuppliers();
			$this->data['chatDataTrends']  = $this->db_model->getTChartDataTrends();

			$this->data['chatData']  = $this->db_model->getPurchasesLocationChart();
			//$this->data['stock']     = $this->db_model->getStockValue();
			$this->data['bs']        = $this->db_model->getBestPurchaseschart();
			$this->data['ubs']        = $this->db_model->getUnBestPurchaseschart();
			$this->data['bc']        = $this->db_model->getBestCustomerschart();
			$this->data['ubc']        = $this->db_model->getUnBestCustomerschart();
			$lmsdate                 = date('Y-m-d', strtotime('first day of last month')) . ' 00:00:00';
			$lmedate                 = date('Y-m-d', strtotime('last day of last month')) . ' 23:59:59';
			$this->data['lmbs']      = $this->db_model->getBestSeller($lmsdate, $lmedate);
		}


		$bc = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('accounting_dashboard')]];
		$meta = ['page_title' => lang('accounting_dashboard'), 'bc' => $bc];
		$this->page_construct('accounts/dashboard', $meta, $this->data);
	}

	public function cash_flow($pdf = null, $xls = null)
	{
		$this->sma->checkPermissions('incomestatement', true);
		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

		// Period Filter Determination (Matching Accounting Dashboard)
		$period = $this->input->get('period') ? trim((string)$this->input->get('period')) : 'this_month';
		$warehouse_id = $this->session->userdata('warehouse_id');

		$start_datetime = null;
		$end_datetime = null;
		$display_range = '';

		switch ($period) {
			case 'today':
				$start_datetime = date('Y-m-d 00:00:00');
				$end_datetime = date('Y-m-d 23:59:59');
				$display_range = date('Y-m-d');
				break;

			case 'this_week':
				// Week starts on Saturday (Saudi standard)
				$w = (int)date('w'); // 0 (Sun) to 6 (Sat)
				$days_from_sat = ($w + 1) % 7;
				$sat_ts = strtotime("-$days_from_sat days");
				$fri_ts = strtotime("+" . (6 - $days_from_sat) . " days");
				$start_datetime = date('Y-m-d 00:00:00', $sat_ts);
				$end_datetime = date('Y-m-d 23:59:59', $fri_ts);
				$display_range = date('Y-m-d', $sat_ts) . ' → ' . date('Y-m-d', $fri_ts);
				break;

			case 'this_quarter':
				$month = (int)date('n');
				$quarter = ceil($month / 3);
				$start_m = sprintf('%02d', ($quarter - 1) * 3 + 1);
				$end_m = sprintf('%02d', $quarter * 3);
				$start_datetime = date("Y-$start_m-01 00:00:00");
				$last_day_q = date('t', strtotime(date("Y-$end_m-01")));
				$end_datetime = date("Y-$end_m-$last_day_q 23:59:59");
				$display_range = date("Y-$start_m-01") . ' → ' . date("Y-$end_m-$last_day_q");
				break;

			case 'this_year':
				$start_datetime = date('Y-01-01 00:00:00');
				$end_datetime = date('Y-12-31 23:59:59');
				$display_range = date('Y-01-01') . ' → ' . date('Y-12-31');
				break;

			case 'fiscal_year':
				$q_fiscal = $this->db->get_where('gl_accounting_period', ['period_year_status' => 1], 1);
				if ($q_fiscal && $q_fiscal->num_rows() > 0) {
					$fiscal_row = $q_fiscal->row();
					$start_datetime = $fiscal_row->period_from_date . ' 00:00:00';
					$end_datetime = $fiscal_row->period_to_date . ' 23:59:59';
					$display_range = $fiscal_row->period_from_date . ' → ' . $fiscal_row->period_to_date;
				} else {
					$start_datetime = date('Y-01-01 00:00:00');
					$end_datetime = date('Y-12-31 23:59:59');
					$display_range = date('Y-01-01') . ' → ' . date('Y-12-31');
				}
				break;

			case 'custom':
				$from_raw = $this->input->get('from') ?: ($this->input->get('start_date') ?: date('Y-m-01'));
				$to_raw = $this->input->get('to') ?: ($this->input->get('end_date') ?: date('Y-m-d'));
				$from_ts = strtotime($from_raw) ? strtotime($from_raw) : strtotime(date('Y-m-01'));
				$to_ts = strtotime($to_raw) ? strtotime($to_raw) : time();
				if ($from_ts > $to_ts) {
					$tmp = $from_ts;
					$from_ts = $to_ts;
					$to_ts = $tmp;
				}
				$start_datetime = date('Y-m-d 00:00:00', $from_ts);
				$end_datetime = date('Y-m-d 23:59:59', $to_ts);
				$display_range = date('Y-m-d', $from_ts) . ' → ' . date('Y-m-d', $to_ts);
				break;

			case 'all':
				$start_datetime = null;
				$end_datetime = null;
				$display_range = lang('all_time');
				break;

			case 'this_month':
			default:
				$period = 'this_month';
				$start_datetime = date('Y-m-01 00:00:00');
				$end_datetime = date('Y-m-t 23:59:59');
				$display_range = date('Y-m-01') . ' → ' . date('Y-m-t');
				break;
		}

		$this->data['active_period'] = $period;
		$this->data['start_datetime'] = $start_datetime;
		$this->data['end_datetime'] = $end_datetime;
		$this->data['display_date_range'] = $display_range;
		$this->data['from_date_input'] = $start_datetime ? substr($start_datetime, 0, 10) : date('Y-m-01');
		$this->data['to_date_input'] = $end_datetime ? substr($end_datetime, 0, 10) : date('Y-m-d');

		// Fetch Real-Time Cash Flow Statement based on sma_gl_accounts_settings
		$this->data['cash_flow'] = $this->accounts_model->getCashFlowStatement($start_datetime, $end_datetime, $warehouse_id);

		$bc = [
			['link' => base_url(), 'page' => lang('home')],
			['link' => admin_url('accounts/dashboard'), 'page' => lang('accounting')],
			['link' => '#', 'page' => lang('cash_flow_statement')]
		];
		$meta = ['page_title' => lang('cash_flow_statement'), 'bc' => $bc];
		$this->page_construct('accounts/cash_flow', $meta, $this->data);
	}

	public function getCurentIncomeStatement($accountList, $period_from_date, $period_to_date, $period_year = null,$grpId=null,$coId=null,$brId=null)
	{


		$revenueTotal  = 0;
		$orevenueTotal  = 0;
		$expensesTotal = 0;
		$opexpensesTotal = 0;
		$maexpensesTotal = 0;
		$oexpensesTotal = 0;



		$accounts = array();

		foreach ($accountList as $account) {
			$account = (array) $account;

			if (!isset($accounts[$account['accountCategory']])) {
				$accounts[$account['accountCategory']] = array($account);
			} else {
				array_push($accounts[$account['accountCategory']], $account);
			}
		}

		$accountCategories = array_keys($accounts);
		$accountOrder = 0;




		$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_acct);
		$acc = (array) $acc[0];

		$sales_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);



		$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_return_acct);
		$acc = (array) $acc[0];

		$sales_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);


		$net_sales = $this->sma->formatDecimal(($sales_acctBalance)) + $this->sma->formatDecimal(($sales_return_acctBalance));







		if($this->Settings->accounting_type=='periodic'){


		$first_period_stock = $this->site->getOpeningBalanceForWarehouses($period_from_date . ' 00:00:00',$period_to_date . ' 23:59:59',null,$grpId,$coId,$brId)->first_period_stock;
		if($first_period_stock==0){

			$first_period_stock = $this->site->getLastendPeriodStock($period_from_date . ' 00:00:00',$period_to_date . ' 23:59:59',$brId);
			}


				$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_acct);
	$acc = (array) $acc[0];

	$purchases_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],$period_from_date . ' 00:00:00',$period_to_date . ' 23:59:59',NULL,$grpId,$coId,$brId);


	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_return_acct);
	$acc = (array) $acc[0];



	$purchases_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'],$period_from_date . ' 00:00:00',$period_to_date . ' 23:59:59',NULL,$grpId,$coId,$brId);



					$net_purchases=$this->sma->formatDecimal(($purchases_acctBalance))+$this->sma->formatDecimal(($purchases_return_acctBalance));








					$goods_available_for_sale=$this->sma->formatDecimal(($first_period_stock))+$this->sma->formatDecimal(($net_purchases));



					$total_end_of_period_stock=0;
					$warehouses=$this->site->getAllWarehouses($grpId,$coId,$brId);
					foreach($warehouses as $warehouse){
				$end_of_period_stock_arr = $this->site->getEndPeriodForWarehousesV2($period_from_date . ' 00:00:00',$period_to_date . ' 23:59:59',$warehouse->id,$grpId,$coId,$brId);

	foreach($end_of_period_stock_arr as $end_of_period_stock){
		$total_end_of_period_stock=$total_end_of_period_stock+$this->sma->formatDecimal($end_of_period_stock->balanceValue);
	}
}
	$end_of_period_stock = $this->sma->formatDecimal(/*($first_period_stock)+*/ ($total_end_of_period_stock));

				//$end_of_period_stock = $this->sma->formatDecimal(($first_period_stock)+ ($end_of_period_stock));


				//$this->sma->print_arrays($end_of_period_stock);
					$sales_cost_of_good_sold_acctBalance=$this->sma->formatDecimal(($goods_available_for_sale))-$this->sma->formatDecimal(($end_of_period_stock));











	}else{


	$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
	$acc = (array) $acc[0];

	$sales_cost_of_good_sold_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);

	}





		$Gross_income = $this->sma->formatDecimal(($net_sales)) + $this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance));




		$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
		$accountChilds = (array) $accountChilds[0];

		$accountCategory = $this->accounts_model->getAccountChilds($accountChilds['accountID']);
		foreach ($accountCategory as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}

			$opexpensesTotal += $accountBalance;
		}







		$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
		$accountChilds = (array) $accountChilds[0];

		$accountCategory = $this->accounts_model->getAccountChilds($accountChilds['accountID']);
		foreach ($accountCategory as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}

			$maexpensesTotal += $accountBalance;
		}



		$Net_operating_income = $this->sma->formatDecimal(($Gross_income) + (($opexpensesTotal) + ($maexpensesTotal)));



		$otherRevenuesAccounts = $this->accounts_model->getOtherRevenuesAccounts();
		//print_r($this->site->getAccountsSettings()->sales_acct);

		foreach ($otherRevenuesAccounts as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}

			$orevenueTotal += $accountBalance;
		}



		$accounts_operating_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
		$accounts_operating_expenses_acct = (array) $accounts_operating_expenses_acct[0];


		$accounts_marketing_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
		$accounts_marketing_expenses_acct = (array) $accounts_marketing_expenses_acct[0];


		$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccounts($accounts_operating_expenses_acct['accountID'], $accounts_marketing_expenses_acct['accountID'],null,$period_year);

		foreach ($otherExpensesAccounts as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}

			$oexpensesTotal += $accountBalance;
		}


		$Final_Net_Income = ($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal);
		return ($Final_Net_Income);
	}




	public function getCurentIncomeStatementV2($accountList, $period_from_date, $period_to_date,$period_year=null,$grpId=null,$coId=null,$brId=null)
	{


		$revenueTotal  = 0;
		$orevenueTotal  = 0;
		$expensesTotal = 0;
		$opexpensesTotal = 0;
		$maexpensesTotal = 0;
		$oexpensesTotal = 0;



		$accounts = array();

		foreach ($accountList as $account) {
			$account = (array) $account;

			if (!isset($accounts[$account['accountCategory']])) {
				$accounts[$account['accountCategory']] = array($account);
			} else {
				array_push($accounts[$account['accountCategory']], $account);
			}
		}

		$accountCategories = array_keys($accounts);
		$accountOrder = 0;




		$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_acct);
		$acc = (array) $acc[0];

		$sales_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
		//$this->sma->print_arrays($sales_acctBalance);
		//$dataIncomeStatement['sales_acctBalance']=$sales_acctBalance;
		$dataRevenues[$this->site->getAccountsSettings()->sales_acct] = $sales_acctBalance;

		$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_return_acct);
		$acc = (array) $acc[0];

		$sales_return_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
		//$dataIncomeStatement['sales_return_acctBalance']=$sales_return_acctBalance;
		$dataExpenses[$this->site->getAccountsSettings()->sales_return_acct] = $sales_return_acctBalance;


		$net_sales = $this->sma->formatDecimal(($sales_acctBalance)) + $this->sma->formatDecimal(($sales_return_acctBalance));




		$acc = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_cost_of_good_sold_acct);
		$acc = (array) $acc[0];

		$sales_cost_of_good_sold_acctBalance = $this->accounts_model->getAccountTotal($acc['id'], $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
		//$dataIncomeStatement['sales_cost_of_good_sold_acctBalance']=$sales_cost_of_good_sold_acctBalance;

		$dataExpenses[$this->site->getAccountsSettings()->sales_cost_of_good_sold_acct] = $sales_cost_of_good_sold_acctBalance;



		$Gross_income = $this->sma->formatDecimal(($net_sales)) + $this->sma->formatDecimal(($sales_cost_of_good_sold_acctBalance));




		$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
		$accountChilds = (array) $accountChilds[0];

		$accountCategory = $this->accounts_model->getAccountChilds($accountChilds['accountID']);
		foreach ($accountCategory as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}
			$dataExpenses[$account->id] = $accountBalance;

			$opexpensesTotal += $accountBalance;
		}







		$accountChilds = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
		$accountChilds = (array) $accountChilds[0];

		$accountCategory = $this->accounts_model->getAccountChilds($accountChilds['accountID']);
		foreach ($accountCategory as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}
			$dataExpenses[$account->id] = $accountBalance;


			$maexpensesTotal += $accountBalance;
		}



		$Net_operating_income = $this->sma->formatDecimal(($Gross_income) + (($opexpensesTotal) + ($maexpensesTotal)));



		$otherRevenuesAccounts = $this->accounts_model->getOtherRevenuesAccounts();
		//print_r($this->site->getAccountsSettings()->sales_acct);

		foreach ($otherRevenuesAccounts as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}
			$dataRevenues[$account->id] = $accountBalance;

			$orevenueTotal += $accountBalance;
		}



		$accounts_operating_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_operating_expenses_acct);
		$accounts_operating_expenses_acct = (array) $accounts_operating_expenses_acct[0];


		$accounts_marketing_expenses_acct = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->accounts_marketing_expenses_acct);
		$accounts_marketing_expenses_acct = (array) $accounts_marketing_expenses_acct[0];


		$otherExpensesAccounts = $this->accounts_model->getOtherExpensesAccounts($accounts_operating_expenses_acct['accountID'], $accounts_marketing_expenses_acct['accountID'],null,$period_year);

		foreach ($otherExpensesAccounts as $account) {

			if (!empty($_POST)) {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			} else {
				$accountBalance = $this->accounts_model->getAccountTotal($account->id, $period_from_date . ' 00:00:00', $period_to_date . ' 23:59:59',null,$grpId,$coId,$brId);
			}



			if ($accountBalance == 0) {
				continue;
			}
			$dataExpenses[$account->id] = $accountBalance;

			$oexpensesTotal += $accountBalance;
		}


		$Final_Net_Income = ($Net_operating_income) + ($orevenueTotal) + ($oexpensesTotal);

		$dataIncomeStatement['Revenues'] = $dataRevenues;
		$dataIncomeStatement['Expenses'] = $dataExpenses;
		$dataIncomeStatement['Final_Net_Income'] = $Final_Net_Income;

		return ($dataIncomeStatement);
	}




	public function entriesreport($pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		if ($pdf || $xls) {

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');
			$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
			$entryStatus       = $this->input->get('entryStatus');
			$entryType       = $this->input->get('entryType');


			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
			}

			if ($entryStatus == "")
				$entryStatus = null;


			$entryList = $this->accounts_model->getEntries(Null, $start_date, $end_date, $entryStatus, $entryType,Null,Null,$grpId,$coId,$brId);
			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Entries_Report'));
			//$this->excel->getActiveSheet()->mergeCellsByColumnAndRow(0,1,1,1);
			$this->excel->getActiveSheet()->mergeCells('A1:F1');


			$styleArrayFill = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)

			);
			$styleArrayFillGreen = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'C6EFCE')
				)

			);
			$styleArrayFillYellow = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FFEB9C')
				)

			);
			$styleArrayF13 = array(
				'font' => array(
					'bold' => true,
					'size' => 13
					//'name'  => 'Cairo-Bold'
				)

			);
			$styleArrayF11 = array(
				'font' => array(
					//'bold' => true,
					'size' => 11
					//'name'  => 'Cairo-Bold'
				)

			);
			$styleArrayF11Bold = array(
				'font' => array(
					'bold' => true,
					'size' => 11
					//'name'  => 'Cairo-Bold'
				)

			);
			$styleArrayF11Wight = array(
				'font' => array(
					// 'bold' => true,
					'size' => 11,
					'color' => array('rgb' => '006100')
					//'name'  => 'Cairo-Bold'
				)

			);

			$styleArrayF11Wight2 = array(
				'font' => array(
					// 'bold' => true,
					'size' => 11,
					'color' => array('rgb' => '9C6500')
					//'name'  => 'Cairo-Bold'
				)

			);
			$this->excel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, lang('Entries_Report'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);


			$this->excel->getActiveSheet()->mergeCells('A2:F2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
			$row = 3;


			if($grpId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_group').' : '.($this->site->getMainGroupsByID($grpId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                        }

                        if($coId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_company').' : '.($this->site->getMainCompaniesByID($coId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                         $row++;
                        }

                        if($brId){
                            $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
                        $this->excel->getActiveSheet()->SetCellValue('A'.$row, lang('Main_branch').' : '.($this->site->getMainBranchesByID($brId)->name));
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(15);
                        $this->excel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);

                        $this->excel->getActiveSheet()->getStyle('A1:A'.$row)->applyFromArray($styleArrayFill);
                        $row++;
                        }


			$this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
			$this->excel->getActiveSheet()->SetCellValue('A' . $row, ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Entries_Report') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A' . $row)->applyFromArray($styleArrayF13);

			$this->excel->getActiveSheet()->getStyle('A1:A' . $row)->applyFromArray($styleArrayFill);

			$row++;


			$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("Entry_number"));
			$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


			$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("Entry_date"));
			$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("Entry_type"));
			$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Debit"));
			$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Credit"));
			$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("approved"));
			$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleArrayFill);
			$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($styleArrayF11Bold);



			$row++;
			foreach ($entryList as $entry) {
				$entry = (array) $entry;
				$entry['entryDebitAccount'] = json_decode($entry['entryDebitAccount']);
				$entry['entryDebitBalance'] = json_decode($entry['entryDebitBalance']);
				$entry['entryCreditAccount'] = json_decode($entry['entryCreditAccount']);
				$entry['entryCreditBalance'] = json_decode($entry['entryCreditBalance']);



				$this->excel->getActiveSheet()->SetCellValue('A' . $row, $entry['entry_no'] ? $entry['entry_no']: $entry['entryID']);
				$this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->hrsd($entry['entryDate']));

				$entryDescription = str_replace("<p>", "", ($entry['entryDescription']) . "");
				$entryDescription = str_replace("</p>", "", ($entryDescription) . "");

				$this->excel->getActiveSheet()->SetCellValue('C' . $row, trim($entry['entryType']) != "Adjusting" ? lang($entry['entryType']) : lang($entryDescription));

				$x = 0;
				$entryDebit = "";


				foreach ($entry['entryDebitAccount'] as $debitAccount) {
					$entryDebit = $entryDebit . "" . $this->accounts_model->getAccountByID($entry['entryDebitAccount'][$x])->accountName . "\n" . $this->sma->formatMoney($entry['entryDebitBalance'][$x], ' ') . "\n";

					$x++;
				}
				$this->excel->getActiveSheet()->SetCellValue('D' . $row, $entryDebit);
				$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setWrapText(true);


				$y = 0;
				$entryCredit = "";

				foreach ($entry['entryCreditAccount'] as $creditAccount) {
					$entryCredit = $entryCredit . "" . $this->accounts_model->getAccountByID($entry['entryCreditAccount'][$y])->accountName . "\n" . $this->sma->formatMoney($entry['entryCreditBalance'][$y], ' ') . "\n";

					$y++;
				}
				$this->excel->getActiveSheet()->SetCellValue('E' . $row, $entryCredit);
				$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setWrapText(true);


				if ($entry['entryStatus'] == 1) {
					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang('approved'));
					$this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($styleArrayFillGreen);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($styleArrayF11Wight);
				} else {
					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang('unapproved'));
					$this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($styleArrayFillYellow);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->applyFromArray($styleArrayF11Wight2);
				}
				$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


				$row++;
			}

			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

			$filename = 'Entries_Report';
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {
			$start_date     = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date       = $this->input->post('end_date') ? $this->input->post('end_date') : null;
			$entryStatus       = $this->input->post('entryStatus');
			$entryType       = $this->input->post('entryType');


			if ($start_date) {
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
			}

			if ($entryStatus == "")
				$entryStatus = null;
			$accounts_gl_result = $this->accounts_model->getAccounts(null, null, "id,accountName,accountNameEN");
			$accounts_gl = [];
			foreach ($accounts_gl_result as $key => $items) {
				$accounts_gl[$items->id] = ($this->Settings->user_language == 'arabic' || $items->accountNameEN=='') ? $items->accountName : $items->accountNameEN;
			}

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

			$this->data['accounts_gl'] = $accounts_gl;
			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Entries_Report'), 'page' => lang('Entries_Report')], ['link' => '#', 'page' => lang('Entries_Report')]];
			$meta                = ['page_title' => lang('Entries_Report'), 'bc' => $bc];
			$this->page_construct('accounts/entriesreport', $meta, $this->data);
		}
	}
	public function tax()
	{
		$this->sma->checkPermissions();

		$dbName=str_replace("cpesoft_","",$this->db->database);
        if($dbName=="nakhaat" || $dbName=="flowersrwad"){
            $this->session->set_flashdata('error', lang('This_action_cannot_be_performed'));
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			//$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : (($this->Owner || $this->Admin) ? null : $this->session->userdata('brId'));

			//$brId=6;
		$start_date = $this->input->post('start_date') ? $this->input->post('start_date') : null;
		$end_date   = $this->input->post('end_date') ? $this->input->post('end_date') : null;
		$show_Opening_balance = 1;
		$show_style = 1;
		if ($start_date) {
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
			/*
			$ts1 = strtotime($start_date);
			if(date('m', $ts1)=='01' && date('d', $ts1)=='01'){
			}else{
				$prev_start_date = date('Y', $ts1).'-01-01 00:00:00';
				$prev_end_date = date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59';


			}*/
		}
		$this->data['start_date']      = isset($start_date) ? $start_date : null;
		$this->data['end_date']      = isset($end_date) ? $end_date : null;

		$this->data['sales_vat_acct']      = $this->site->getAccountsSettings()->sales_vat_acct;
		$this->data['accountListSales'] = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->sales_vat_acct);
		$accountVatTotal = $this->accounts_model->getAccountTotalDebitCreditM($this->site->getAccountsSettings()->sales_vat_acct, (isset($_POST['start_date']) && $_POST['start_date'] != NULL ? $this->sma->fld($_POST['start_date']) . ' 00:00:00' : ''), (isset($_POST['end_date']) && $_POST['end_date'] != NULL ? $this->sma->fld($_POST['end_date']) . ' 23:59:59' : ''), $grpId, $coId, $brId);
		$this->data['accountVatTotal'] = $accountVatTotal['creditTotal'] - $accountVatTotal['debitTotal'];

		$this->data['purchases_vat_acct']      = $this->site->getAccountsSettings()->purchases_vat_acct;
		$this->data['accountListPurchases'] = $this->accounts_model->getAccounts($this->site->getAccountsSettings()->purchases_vat_acct);
		$accountPuVatTotal = $this->accounts_model->getAccountTotalDebitCreditM($this->site->getAccountsSettings()->purchases_vat_acct, (isset($_POST['start_date']) && $_POST['start_date'] != NULL ? $this->sma->fld($_POST['start_date']) . ' 00:00:00' : ''), (isset($_POST['end_date']) && $_POST['end_date'] != NULL ? $this->sma->fld($_POST['end_date']) . ' 23:59:59' : ''), $grpId, $coId, $brId);
		$this->data['accountPuVatTotal'] = $accountPuVatTotal['debitTotal'] - $accountPuVatTotal['creditTotal'];


		$this->data['active_tax']        = $this->site->getTaxRatesActive()->rate;

		if (isset($_POST['start_date']) && $_POST['start_date'] != NULL && isset($_POST['end_date']) && $_POST['end_date'] != NULL) {
			$start_date = $this->sma->fld($_POST['start_date']) . ' 00:00:00';
			$end_date = $this->sma->fld($_POST['end_date']) . ' 23:59:59';

			$this->data['entryList']  = $this->accounts_model->getEntriesByDateApproved($start_date, $end_date, $show_Opening_balance, $grpId, $coId, $brId);
		} else {
			$this->data['entryList']  = $this->accounts_model->getEntriesApproved('', $show_Opening_balance,'','', $grpId, $coId, $brId);
		}

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }
		//$this->sma->print_arrays($this->data['accountVatSalesTotal']);
		$this->data['error']        = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');


		//$this->sma->print_arrays($this->reports_model->getPurchasesTaxInternationalZeroDetailsReturned($start_date, $end_date ,$warehouse_id,15));
		$bc                         = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('accounts'), 'page' => lang('accounts')], ['link' => '#', 'page' => lang('tax_report')]];
		$meta                       = ['page_title' => lang('tax_report'), 'bc' => $bc];
		$this->page_construct('accounts/tax', $meta, $this->data);
	}


	public function edit_receiptV2($id = null)
	{
		$this->sma->checkPermissions('edit_receipt', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getReceiptByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'required');
			//$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Spend_from'), 'required');


			if ($this->form_validation->run() == true) {
				//       if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//      } else {
				//         $date = $receipt->date;
				//    }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}

				$note         = $this->sma->clear_tags($this->input->post('note'));



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);






				$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
				$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

				$entryDebitAccount_arr = [];
				foreach ($jsonArr_entryDebitAccount as $product_arr) {
					$entryDebitAccount_arr[] =  $product_arr->value;
				}

				unset($_POST['entryDebitAccount']);
				$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



				$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
				$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

				$entryDebitBalance_arr = [];
				foreach ($jsonArr_entryDebitBalance as $product_arr) {
					$entryDebitBalance_arr[] =  $product_arr->value;
				}

				unset($_POST['entryDebitBalance']);
				$_POST['entryDebitBalance'] = $entryDebitBalance_arr;




				unset($_POST['entryDebitDescription']);
				$_POST['entryDebitDescription'] = $entryDebitDescription_arr;


				$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
				$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

				$costCenterDebitId_arr_0 = [];
				foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
					$costCenterDebitId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterDebitId_0']);
				$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




				$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
				$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

				$costCenterDebitAmount_arr_0 = [];
				foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
					$costCenterDebitAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_0']);
				$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






				$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
				$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;






				$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
				$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



				$r                = sizeof($entryDebitAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 0; $i < $r; $i++) {


					$costCenterDebitId_arr = [];
					foreach ($jsonArr_costCenterDebitId as $product_arr) {
						if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
							$costCenterDebitId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterDebitId_' . $i]);
					$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
					unset($costCenterDebitId_arr);





					//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




					$costCenterDebitAmount_arr = [];
					foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
						if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
							$costCenterDebitAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterDebitAmount_' . $i]);
					$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
					unset($costCenterDebitAmount_arr);
				}





				$toAccountText="";
				$costDebitCount = 0;
					foreach ($_POST['entryDebitAccount'] as $key => $acc) {
						if($costDebitCount==0)
						$toAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$toAccountText=$toAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterDebitId_' . $costDebitCount]) {
							$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
							$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
						} else {
							$costCenterDebitId[] = "";
							$costCenterDebitAmount[] = "";
						}
						$costDebitCount++;
					}






				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => 0,//$this->input->post('toAccountID'),
					'fromAccountID'         => $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note ? $note : $toAccountText,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $costCenterDebitId,
					'costCenterDebitAmount'            => $costCenterDebitAmount,
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],
					'entryDebitAccount'            => $_POST['entryDebitAccount'],
					'entryDebitBalance'            => $_POST['entryDebitBalance'],
					'toAccountText'         => $toAccountText,

				];



				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data,$_POST);



				// $this->sma->print_arrays($data, $products);
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateReceiptV2($id, $data)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Receipt_successfully_updated'));
				admin_redirect('accounts/receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();


				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondreceipt();


				$this->data['bond_no'] = $this->accounts_model->getMaxBondreceipt();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;
				//$this->sma->print_arrays($receipt);
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['modal_js']   = $this->site->modal_js();
				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_receipt'), 'page' => lang('Edit_receipt')], ['link' => '#', 'page' => lang('Edit_receipt')]];
				$meta                           = ['page_title' => lang('Edit_receipt'), 'bc' => $bc];
				$this->load->view($this->theme . 'accounts/edit_receiptV2', $this->data);

				//$this->page_construct('accounts/edit_receipt', $meta, $this->data);
			}
		}
	}

	public function edit_catch_receiptV2($id = null)
	{
		$this->sma->checkPermissions('edit_catch_receipt', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getCatchReceiptByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'required');
			//$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Received_from'), 'required');


			if ($this->form_validation->run() == true) {
				//    if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//  } else {
				//      $date = $receipt->date;
				// }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}

				$note         = $this->sma->clear_tags($this->input->post('note'));



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);





				$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
				$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

				$entryCreditAccount_arr = [];
				foreach ($jsonArr_entryCreditAccount as $product_arr) {
					$entryCreditAccount_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditAccount']);
				$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



				$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
				$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

				$entryCreditBalance_arr = [];
				foreach ($jsonArr_entryCreditBalance as $product_arr) {
					$entryCreditBalance_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditBalance']);
				$_POST['entryCreditBalance'] = $entryCreditBalance_arr;




				unset($_POST['entryCreditDescription']);
				$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


				$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
				$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

				$costCenterCreditId_arr_0 = [];
				foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
					$costCenterCreditId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterCreditId_0']);
				$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




				$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
				$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

				$costCenterCreditAmount_arr_0 = [];
				foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
					$costCenterCreditAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_0']);
				$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






				$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
				$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;






				$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
				$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



				$r                = sizeof($entryCreditAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 0; $i < $r; $i++) {


					$costCenterCreditId_arr = [];
					foreach ($jsonArr_costCenterCreditId as $product_arr) {
						if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
							$costCenterCreditId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditId_' . $i]);
					$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
					unset($costCenterCreditId_arr);





					//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




					$costCenterCreditAmount_arr = [];
					foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
						if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
							$costCenterCreditAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditAmount_' . $i]);
					$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
					unset($costCenterCreditAmount_arr);
				}





				$toAccountText="";
				$costCreditCount = 0;
					foreach ($_POST['entryCreditAccount'] as $key => $acc) {
						if($costCreditCount==0)
						$toAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$toAccountText=$toAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterCreditId_' . $costCreditCount]) {
							$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
							$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
						} else {
							$costCenterCreditId[] = "";
							$costCenterCreditAmount[] = "";
						}
						$costCreditCount++;
					}














				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => 0,//$this->input->post('toAccountID'),
					'fromAccountID'         => $payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note ? $note : $toAccountText,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $costCenterCreditId,
					'costCenterCreditAmount'            => $costCenterCreditAmount,
					'entryCreditAccount'            => $_POST['entryCreditAccount'],
					'entryCreditBalance'            => $_POST['entryCreditBalance'],
					'toAccountText'         => $toAccountText,


				];


				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data,$_POST);



				// $this->sma->print_arrays($data, $products);
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateCatchReceiptV2($id, $data)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Catch_receipt_successfully_updated'));
				admin_redirect('accounts/catch_receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();



				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondCatchreceipt();

				//$this->sma->print_arrays($receipt);


				$this->data['bond_no'] = $this->accounts_model->getMaxBondCatchreceipt();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;

				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();

				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_catch_receipt'), 'page' => lang('Edit_catch_receipt')], ['link' => '#', 'page' => lang('Edit_catch_receipt')]];
				$meta                           = ['page_title' => lang('Edit_catch_receipt'), 'bc' => $bc];
				$this->load->view($this->theme . 'accounts/edit_catch_receiptV2', $this->data);
				//$this->page_construct('accounts/edit_catch_receipt', $meta, $this->data);
			}
		}
	}

	public function depreciationoffixedassets()
	{
		$this->sma->checkPermissions('fiscal_year_closing', true);

		$this->form_validation->set_rules('period_old_year', lang('Year'), 'trim|required|numeric');
		$period_old_year     = $this->input->post('period_old_year');


		if ($this->form_validation->run() == true) {
			//$this->sma->print_arrays($period_old_year);

			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_does_not_exist'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}
			$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1');

			if ($old_accounting_period_count->count == 0) {
				$this->session->set_flashdata('error', lang('The_fiscal_year_to_be_closed_is_already_closed'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}

			/*$old_accounting_period_count = $this->accounts_model->getCountAccountingPeriodByYear($period_old_year, '1', 'NOT CLOSED');


			if ($old_accounting_period_count->count != 0) {
				$this->session->set_flashdata('error', lang('There_are_unclosed_periods_please_close_them_first'));
				admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'accounts/accounting_period');
			}*/

			$old_accounting_period = $this->accounts_model->getDepreciationsByYear($period_old_year);

			//$this->sma->print_arrays($old_accounting_period->period_from_date." 00:00:00",$old_accounting_period->period_to_date." 23:59:59");




			foreach ($old_accounting_period  as $fixed_asset_details) {

				if($fixed_asset_details->accumulated_value!=0){

					$fixed_asset_details->accountIDEX= $fixed_asset_details->accountIDEX ? $fixed_asset_details->accountIDEX : $this->site->getAccountsSettings()->depreciation_expenses_acct;
					$fixed_asset_details->accountID= $fixed_asset_details->accountID ? $fixed_asset_details->accountID : $this->site->getAccountsSettings()->depreciation_allowance_acct;

				//$this->sma->print_arrays($fixed_asset_details);
			$entryDescription="Fixed_asset_depreciation#".$fixed_asset_details->code." ".$period_old_year;
            $entry_type="Fixed_asset_depreciation";

            $this->site->createEntryMultiSide($fixed_asset_details->accountIDEX,$fixed_asset_details->accountID,$this->sma->formatDecimal($fixed_asset_details->accumulated_value),$this->sma->formatDecimal($fixed_asset_details->accumulated_value),$fixed_asset_details->code,$entry_type,null,null,null,null,$period_old_year.'-12-31 23:59:58',$entryDescription);
				}
			}

			$this->session->set_flashdata('message', lang('Close_the_depreciation_of_fixed_assets_successfully_added'));
			admin_redirect('accounts/accounting_period');





		} elseif ($this->input->post('Depreciation_of_fixed_assets')) {
			$this->session->set_flashdata('error', validation_errors());
			admin_redirect('accounts/accounting_period');
		}



			$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYearActive();
			//$this->sma->print_arrays($this->data['period_old_year']);
			$this->data['error']      = validation_errors() ? validation_errors() : $this->session->flashdata('error');
			$this->data['modal_js']   = $this->site->modal_js();



			$this->load->view($this->theme . 'accounts/depreciationoffixedassets', $this->data);

	}

	public function getMaxBondreceipt($brId = null)
    {
    if ($rows = $this->accounts_model->getMaxBondreceipt($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

	public function getMaxBondCatchreceipt($brId = null)
    {
    if ($rows = $this->accounts_model->getMaxBondCatchreceipt($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

	public function getMaxBondRefundingBond($brId = null)
    {
    if ($rows = $this->accounts_model->getMaxBondRefundingBond($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

	public function edit_receiptV3($id = null)
	{
		$this->sma->checkPermissions('edit_receipt', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getReceiptByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			if ($this->input->post('bond_no') != $receipt->bond_no) {
            	$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_receipts.bond_no]|required');
        	}
			//$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Spend_from'), 'required');


			if ($this->form_validation->run() == true) {
				//       if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//      } else {
				//         $date = $receipt->date;
				//    }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/
				$exchange_rate=$this->input->post('exchange_rate');
				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}

				$note         = $_POST['note'];



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);






				$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
				$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

				$entryDebitAccount_arr = [];
				foreach ($jsonArr_entryDebitAccount as $product_arr) {
					$entryDebitAccount_arr[] =  $product_arr->value;
				}

				unset($_POST['entryDebitAccount']);
				$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



				$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
				$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

				$entryDebitBalance_arr = [];
				$equivalent_entryDebitBalance_arr= [];
				foreach ($jsonArr_entryDebitBalance as $product_arr) {
					$entryDebitBalance_arr[] =  $product_arr->value;
					$equivalent_entryDebitBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryDebitBalance']);
				$_POST['entryDebitBalance'] = $entryDebitBalance_arr;
				$_POST['equivalent_entryDebitBalance'] = $equivalent_entryDebitBalance_arr;




				$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
				$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

				$entryDebitDescription_arr = [];
				foreach ($jsonArr_entryDebitDescription as $product_arr) {
					$entryDebitDescription_arr[] =  $product_arr->value;
				}

				unset($_POST['entryDebitDescription']);
				$_POST['entryDebitDescription'] = $entryDebitDescription_arr;



				$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
				$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

				$costCenterDebitId_arr_0 = [];
				foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
					$costCenterDebitId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterDebitId_0']);
				$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




				$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
				$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

				$costCenterDebitAmount_arr_0 = [];
				foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
					$costCenterDebitAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_0']);
				$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






				$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
				$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;






				$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
				$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



				$r                = sizeof($entryDebitAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 0; $i < $r; $i++) {


					$costCenterDebitId_arr = [];
					foreach ($jsonArr_costCenterDebitId as $product_arr) {
						if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
							$costCenterDebitId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterDebitId_' . $i]);
					$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
					unset($costCenterDebitId_arr);





					//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




					$costCenterDebitAmount_arr = [];
					foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
						if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
							$costCenterDebitAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterDebitAmount_' . $i]);
					$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
					unset($costCenterDebitAmount_arr);
				}





				$toAccountText="";
				$costDebitCount = 0;
					foreach ($_POST['entryDebitAccount'] as $key => $acc) {
						if($costDebitCount==0)
						$toAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$toAccountText=$toAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterDebitId_' . $costDebitCount]) {
							$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
							$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
						} else {
							$costCenterDebitId[] = "";
							$costCenterDebitAmount[] = "";
						}
						$costDebitCount++;
					}




					//////credit


				$paid_by=' ';

				$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
				$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

				$entryCreditAccount_arr = [];
				foreach ($jsonArr_entryCreditAccount as $product_arr) {

					$entryCreditAccount_arr[] =  $product_arr->value;

					$fromAccount       = $this->accounts_model->getAccountByID($product_arr->value);
					$fromAccountName   = $fromAccount->accountNameEn ? $fromAccount->accountNameEn :  $fromAccount->accountName;
					$fromAccountName   = $this->Settings->user_language == 'arabic' ? $fromAccount->accountName . ' ' . $cheque_no :  $fromAccountName . ' ' . $cheque_no;

					$paid_by=$paid_by.' - '.($fromAccountName);
				}

				unset($_POST['entryCreditAccount']);
				$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



				$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
				$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

				$entryCreditBalance_arr = [];
				$equivalent_entryCreditBalance_arr = [];
				foreach ($jsonArr_entryCreditBalance as $product_arr) {
					$entryCreditBalance_arr[] =  $product_arr->value;
					$equivalent_entryCreditBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryCreditBalance']);
				$_POST['entryCreditBalance'] = $entryCreditBalance_arr;
				$_POST['equivalent_entryCreditBalance'] = $equivalent_entryCreditBalance_arr;




				$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
				$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

				$entryCreditDescription_arr = [];
				foreach ($jsonArr_entryCreditDescription as $product_arr) {
					$entryCreditDescription_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditDescription']);
				$_POST['entryCreditDescription'] = $entryCreditDescription_arr;


				$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
				$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

				$costCenterCreditId_arr_0 = [];
				foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
					$costCenterCreditId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterCreditId_0']);
				$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




				$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
				$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

				$costCenterCreditAmount_arr_0 = [];
				foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
					$costCenterCreditAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_0']);
				$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






				$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
				$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;






				$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
				$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



				$r                = sizeof($entryCreditAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 0; $i < $r; $i++) {


					$costCenterCreditId_arr = [];
					foreach ($jsonArr_costCenterCreditId as $product_arr) {
						if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
							$costCenterCreditId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditId_' . $i]);
					$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
					unset($costCenterCreditId_arr);





					//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




					$costCenterCreditAmount_arr = [];
					foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
						if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
							$costCenterCreditAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditAmount_' . $i]);
					$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
					unset($costCenterCreditAmount_arr);
				}





				$fromAccountText="";
				$costCreditCount = 0;
					foreach ($_POST['entryCreditAccount'] as $key => $acc) {
						if($costCreditCount==0)
						$fromAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$fromAccountText=$fromAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterCreditId_' . $costCreditCount]) {
							$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
							$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
						} else {
							$costCenterCreditId[] = "";
							$costCenterCreditAmount[] = "";
						}
						$costCreditCount++;
					}









				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $paid_by,
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => 0,//$this->input->post('toAccountID'),
					'fromAccountID'         => 0,//$payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note ? $note : $toAccountText,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $costCenterDebitId,
					'costCenterDebitAmount'            => $costCenterDebitAmount,
					'costCenterCreditId'            => $costCenterCreditId,
					'costCenterCreditAmount'            => $costCenterCreditAmount,
					'entryDebitAccount'            => $_POST['entryDebitAccount'],
					'entryDebitBalance'            => $_POST['entryDebitBalance'],
					'equivalent_entryDebitBalance'            => $_POST['equivalent_entryDebitBalance'],
					'toAccountText'         => $toAccountText,
					'entryCreditAccount'            => $_POST['entryCreditAccount'],
					'entryCreditBalance'            => $_POST['entryCreditBalance'],
					'equivalent_entryCreditBalance'            => $_POST['equivalent_entryCreditBalance'],
					'fromAccountText'         => $fromAccountText,
					'entryDebitDescription'            => $_POST['entryDebitDescription'],
					'entryCreditDescription'            => $_POST['entryCreditDescription'],

				];



				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);



				// $this->sma->print_arrays($data, $products);
			} elseif ($this->input->post('edit_receipt')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/receipts');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateReceiptV3($id, $data,$receipt->bond_no)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Receipt_successfully_updated'));
				admin_redirect('accounts/receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();


				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondreceipt();


				$this->data['bond_no'] = $this->accounts_model->getMaxBondreceipt();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;
				//$this->sma->print_arrays($receipt);
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['modal_js']   = $this->site->modal_js();
				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_receipt'), 'page' => lang('Edit_receipt')], ['link' => '#', 'page' => lang('Edit_receipt')]];
				$meta                           = ['page_title' => lang('Edit_receipt'), 'bc' => $bc];
				$this->load->view($this->theme . 'accounts/edit_receiptV3', $this->data);

				//$this->page_construct('accounts/edit_receipt', $meta, $this->data);
			}
		}
	}

	public function edit_catch_receiptV3($id = null)
	{
		$this->sma->checkPermissions('edit_catch_receipt', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getCatchReceiptByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			if ($this->input->post('bond_no') != $receipt->bond_no) {
            	$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_catch_receipts.bond_no]|required');
        	}

			//$this->form_validation->set_rules('toAccountID', lang('The_recipient_account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('Received_from'), 'required');


			if ($this->form_validation->run() == true) {
				//    if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//  } else {
				//      $date = $receipt->date;
				// }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/
				$exchange_rate=$this->input->post('exchange_rate');

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}

				$note         = $_POST['note'];



				$cheque_no = "";
				$cc_type = "";
				$payment_type = "";
				if ($this->input->post('paid_by') == "cash")
					$payment_type = $this->input->post('cash_account');
				if ($this->input->post('paid_by') == "CC") {
					$payment_type = $this->input->post('cc_account');
					$cc_type = $this->input->post('pcc_type');
				}
				if ($this->input->post('paid_by') == "Cheque") {
					$payment_type = $this->input->post('cheque_account');
					$cheque_no = $this->input->post('cheque_no');
				}
				if ($this->input->post('paid_by') == "bank_transfer")
					$payment_type = $this->input->post('transfer_account');
				if ($this->input->post('paid_by') == "gift_card")
					$payment_type = $this->input->post('transfer_account');

				$payment_type_details = $this->accounts_model->getPaymentTypeByID($payment_type);


				//$this->sma->print_arrays($_POST);





				$jsonArr_entryCreditAccount = json_decode($_POST['jsonArr_entryCreditAccount']);
				$jsonArr_entryCreditAccount = (array) $jsonArr_entryCreditAccount;

				$entryCreditAccount_arr = [];
				foreach ($jsonArr_entryCreditAccount as $product_arr) {
					$entryCreditAccount_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditAccount']);
				$_POST['entryCreditAccount'] = $entryCreditAccount_arr;



				$jsonArr_entryCreditBalance = json_decode($_POST['jsonArr_entryCreditBalance']);
				$jsonArr_entryCreditBalance = (array) $jsonArr_entryCreditBalance;

				/*$entryCreditBalance_arr = [];
				foreach ($jsonArr_entryCreditBalance as $product_arr) {
					$entryCreditBalance_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditBalance']);
				$_POST['entryCreditBalance'] = $entryCreditBalance_arr;*/


				$entryCreditBalance_arr = [];
				$equivalent_entryCreditBalance_arr = [];
				foreach ($jsonArr_entryCreditBalance as $product_arr) {
					$entryCreditBalance_arr[] =  $product_arr->value;
					$equivalent_entryCreditBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryCreditBalance']);
				$_POST['entryCreditBalance'] = $entryCreditBalance_arr;
				$_POST['equivalent_entryCreditBalance'] = $equivalent_entryCreditBalance_arr;




				$jsonArr_entryCreditDescription = json_decode($_POST['jsonArr_entryCreditDescription']);
				$jsonArr_entryCreditDescription = (array) $jsonArr_entryCreditDescription;

				$entryCreditDescription_arr = [];
				foreach ($jsonArr_entryCreditDescription as $product_arr) {
					$entryCreditDescription_arr[] =  $product_arr->value;
				}

				unset($_POST['entryCreditDescription']);
				$_POST['entryCreditDescription'] = $entryCreditDescription_arr;



				$jsonArr_costCenterCreditId_0 = json_decode($_POST['jsonArr_costCenterCreditId_0']);
				$jsonArr_costCenterCreditId_0 = (array) $jsonArr_costCenterCreditId_0;

				$costCenterCreditId_arr_0 = [];
				foreach ($jsonArr_costCenterCreditId_0 as $product_arr) {
					$costCenterCreditId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterCreditId_0']);
				$_POST['costCenterCreditId_0'] = $costCenterCreditId_arr_0;




				$jsonArr_costCenterCreditAmount_0 = json_decode($_POST['jsonArr_costCenterCreditAmount_0']);
				$jsonArr_costCenterCreditAmount_0 = (array) $jsonArr_costCenterCreditAmount_0;

				$costCenterCreditAmount_arr_0 = [];
				foreach ($jsonArr_costCenterCreditAmount_0 as $product_arr) {
					$costCenterCreditAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterCreditAmount_0']);
				$_POST['costCenterCreditAmount_0'] = $costCenterCreditAmount_arr_0;






				$jsonArr_costCenterCreditId = json_decode($_POST['jsonArr_costCenterCreditId']);
				$jsonArr_costCenterCreditId = (array) $jsonArr_costCenterCreditId;






				$jsonArr_costCenterCreditAmount = json_decode($_POST['jsonArr_costCenterCreditAmount']);
				$jsonArr_costCenterCreditAmount = (array) $jsonArr_costCenterCreditAmount;



				$r                = sizeof($entryCreditAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 0; $i < $r; $i++) {


					$costCenterCreditId_arr = [];
					foreach ($jsonArr_costCenterCreditId as $product_arr) {
						if ($product_arr->name == "costCenterCreditId_" . $i . "[]")
							$costCenterCreditId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditId_' . $i]);
					$_POST['costCenterCreditId_' . $i] = $costCenterCreditId_arr;
					unset($costCenterCreditId_arr);





					//$this->sma->print_arrays($costCenterCreditId_arr,$_POST);




					$costCenterCreditAmount_arr = [];
					foreach ($jsonArr_costCenterCreditAmount as $product_arr) {
						if ($product_arr->name == "costCenterCreditAmount_" . $i . "[]")
							$costCenterCreditAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterCreditAmount_' . $i]);
					$_POST['costCenterCreditAmount_' . $i] = $costCenterCreditAmount_arr;
					unset($costCenterCreditAmount_arr);
				}





				$toAccountText="";
				$costCreditCount = 0;
					foreach ($_POST['entryCreditAccount'] as $key => $acc) {
						if($costCreditCount==0)
						$toAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$toAccountText=$toAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterCreditId_' . $costCreditCount]) {
							$costCenterCreditId[] = $_POST['costCenterCreditId_' . $costCreditCount];
							$costCenterCreditAmount[] = $_POST['costCenterCreditAmount_' . $costCreditCount];
						} else {
							$costCenterCreditId[] = "";
							$costCenterCreditAmount[] = "";
						}
						$costCreditCount++;
					}













					//////Debit

					$jsonArr_entryDebitAccount = json_decode($_POST['jsonArr_entryDebitAccount']);
				$jsonArr_entryDebitAccount = (array) $jsonArr_entryDebitAccount;

				$entryDebitAccount_arr = [];
				foreach ($jsonArr_entryDebitAccount as $product_arr) {
					$entryDebitAccount_arr[] =  $product_arr->value;
				}

				unset($_POST['entryDebitAccount']);
				$_POST['entryDebitAccount'] = $entryDebitAccount_arr;



				$jsonArr_entryDebitBalance = json_decode($_POST['jsonArr_entryDebitBalance']);
				$jsonArr_entryDebitBalance = (array) $jsonArr_entryDebitBalance;

				$entryDebitBalance_arr = [];
				$equivalent_entryDebitBalance_arr= [];
				foreach ($jsonArr_entryDebitBalance as $product_arr) {
					$entryDebitBalance_arr[] =  $product_arr->value;
					$equivalent_entryDebitBalance_arr[] =  ($exchange_rate>0 ? ($product_arr->value * $exchange_rate): $product_arr->value);
				}

				unset($_POST['entryDebitBalance']);
				$_POST['entryDebitBalance'] = $entryDebitBalance_arr;
				$_POST['equivalent_entryDebitBalance'] = $equivalent_entryDebitBalance_arr;







				$jsonArr_entryDebitDescription = json_decode($_POST['jsonArr_entryDebitDescription']);
				$jsonArr_entryDebitDescription = (array) $jsonArr_entryDebitDescription;

				$entryDebitDescription_arr = [];
				foreach ($jsonArr_entryDebitDescription as $product_arr) {
					$entryDebitDescription_arr[] =  $product_arr->value;
				}

				unset($_POST['entryDebitDescription']);
				$_POST['entryDebitDescription'] = $entryDebitDescription_arr;


				$jsonArr_costCenterDebitId_0 = json_decode($_POST['jsonArr_costCenterDebitId_0']);
				$jsonArr_costCenterDebitId_0 = (array) $jsonArr_costCenterDebitId_0;

				$costCenterDebitId_arr_0 = [];
				foreach ($jsonArr_costCenterDebitId_0 as $product_arr) {
					$costCenterDebitId_arr_0[] =  $product_arr->value;
				}

				unset($_POST['costCenterDebitId_0']);
				$_POST['costCenterDebitId_0'] = $costCenterDebitId_arr_0;




				$jsonArr_costCenterDebitAmount_0 = json_decode($_POST['jsonArr_costCenterDebitAmount_0']);
				$jsonArr_costCenterDebitAmount_0 = (array) $jsonArr_costCenterDebitAmount_0;

				$costCenterDebitAmount_arr_0 = [];
				foreach ($jsonArr_costCenterDebitAmount_0 as $product_arr) {
					$costCenterDebitAmount_arr_0[] =  $product_arr->value;
				}
				unset($_POST['costCenterDebitAmount_0']);
				$_POST['costCenterDebitAmount_0'] = $costCenterDebitAmount_arr_0;






				$jsonArr_costCenterDebitId = json_decode($_POST['jsonArr_costCenterDebitId']);
				$jsonArr_costCenterDebitId = (array) $jsonArr_costCenterDebitId;






				$jsonArr_costCenterDebitAmount = json_decode($_POST['jsonArr_costCenterDebitAmount']);
				$jsonArr_costCenterDebitAmount = (array) $jsonArr_costCenterDebitAmount;



				$r                = sizeof($entryDebitAccount_arr);
				//$this->sma->print_arrays($i);
				for ($i = 0; $i < $r; $i++) {


					$costCenterDebitId_arr = [];
					foreach ($jsonArr_costCenterDebitId as $product_arr) {
						if ($product_arr->name == "costCenterDebitId_" . $i . "[]")
							$costCenterDebitId_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterDebitId_' . $i]);
					$_POST['costCenterDebitId_' . $i] = $costCenterDebitId_arr;
					unset($costCenterDebitId_arr);





					//$this->sma->print_arrays($costCenterDebitId_arr,$_POST);




					$costCenterDebitAmount_arr = [];
					foreach ($jsonArr_costCenterDebitAmount as $product_arr) {
						if ($product_arr->name == "costCenterDebitAmount_" . $i . "[]")
							$costCenterDebitAmount_arr[] =  $product_arr->value;
					}
					unset($_POST['costCenterDebitAmount_' . $i]);
					$_POST['costCenterDebitAmount_' . $i] = $costCenterDebitAmount_arr;
					unset($costCenterDebitAmount_arr);
				}





				$fromAccountText="";
				$costDebitCount = 0;
					foreach ($_POST['entryDebitAccount'] as $key => $acc) {
						if($costDebitCount==0)
						$fromAccountText=$this->accounts_model->getAccountByID($acc)->accountName;
						else
						$fromAccountText=$fromAccountText." - ".$this->accounts_model->getAccountByID($acc)->accountName;
						if ($_POST['costCenterDebitId_' . $costDebitCount]) {
							$costCenterDebitId[] = $_POST['costCenterDebitId_' . $costDebitCount];
							$costCenterDebitAmount[] = $_POST['costCenterDebitAmount_' . $costDebitCount];
						} else {
							$costCenterDebitId[] = "";
							$costCenterDebitAmount[] = "";
						}
						$costDebitCount++;
					}













				$salesman_id=$this->input->post('salesman_id') ? $this->input->post('salesman_id') : null;
				$salesman_name=$this->input->post('salesman_id') ? $this->site->getCompanyByID($salesman_id)->company : null;

				$amountDiff=0;
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1  && strpos($this->input->post('inv_no'), ',') === false)
					{
						//$_POST['inv_no']=$this->site->getInvoiceBySaleNo($this->input->post('inv_no'))->id;
					}

					if($this->input->post('inv_no')){

						if($inv=$this->site->getSaleByID(trim($this->input->post('inv_no'))) && strpos($this->input->post('inv_no'), ',') === false){
							$inv=$this->site->getSaleByID(trim($this->input->post('inv_no')));
						if($inv->currency_rate!=$this->input->post('exchange_rate')){
							$amountPL=$this->sma->formatDecimal($this->input->post('local_curr_amount'), 4);
							$amount=$this->sma->formatDecimal($this->input->post('amount'), 4)*$inv->currency_rate;
							$amountDiff=$amountPL-$amount;
							if($amountDiff<0){
								$_POST['equivalent_entryDebitBalance'][0]=$_POST['equivalent_entryDebitBalance'][0]-abs($amountDiff);
							}
							if($amountDiff>0){
								$_POST['equivalent_entryCreditBalance'][0]=$_POST['equivalent_entryCreditBalance'][0]-abs($amountDiff);
							}

						}
					}
					}

					//$this->sma->print_arrays($amountDiff, $products);

				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					//'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => 0,//$this->input->post('toAccountID'),
					'fromAccountID'         => 0,//$payment_type_details->payment_account ? $payment_type_details->payment_account : $payment_type,
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note ? $note : $toAccountText,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $costCenterDebitId,
					'costCenterDebitAmount'            => $costCenterDebitAmount,
					'costCenterCreditId'            => $costCenterCreditId,
					'costCenterCreditAmount'            => $costCenterCreditAmount,
					'entryDebitAccount'            => $_POST['entryDebitAccount'],
					'entryDebitBalance'            => $_POST['entryDebitBalance'],
					'equivalent_entryDebitBalance'            => $_POST['equivalent_entryDebitBalance'],
					'toAccountText'         => $toAccountText,
					'entryCreditAccount'            => $_POST['entryCreditAccount'],
					'entryCreditBalance'            => $_POST['entryCreditBalance'],
					'equivalent_entryCreditBalance'            => $_POST['equivalent_entryCreditBalance'],
					'fromAccountText'         => $fromAccountText,
					'entryDebitDescription'            => $_POST['entryDebitDescription'],
					'entryCreditDescription'            => $_POST['entryCreditDescription'],
					'salesman_id'         => $salesman_id,
					'salesman_name'         => $salesman_name,
					'inv_no'         => $this->input->post('inv_no') ? trim($this->input->post('inv_no')) : null,
					'the_oldest'         => $this->input->post('the_oldest') ? trim($this->input->post('the_oldest')) : 0,


				];


				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}

				if($this->input->post('inv_no') && strpos($this->input->post('inv_no'), ',') === false){
					if($inv=$this->site->getSaleByID(trim($this->input->post('inv_no')))){

						$creditSize = isset($_POST['entryCreditAccount']) ? sizeof($_POST['entryCreditAccount']) : 0;

				 		if($creditSize==1){

							$debitSize = isset($_POST['entryDebitAccount']) ? sizeof($_POST['entryDebitAccount']) : 0;

				 		if($debitSize==1){

						$cus_acc=$this->accounts_model->getAccountByID($_POST['entryCreditAccount'][0]);

						if($cus_acc->account_type=='local_customers' || $cus_acc->account_type=='international_customers'){

							$customer=$this->site->getCompanyByID($cus_acc->account_ref_id);
							if($customer->id == $inv->customer_id){
								$accounts_settings   = $this->accounts_model->getSettings();

								$payment_acc=$this->accounts_model->getAccountByID($_POST['entryDebitAccount'][0]);

								if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_boxes_acct,$payment_acc->parent_id) == '1'){
										$paid_by='cash';
								}else{
									$this->session->set_flashdata('error', lang('please_select_payment_method_account_in_debit_side'));
									redirect($_SERVER['HTTP_REFERER']);
								}


							}else{
								$this->session->set_flashdata('error', lang('Please_select_the_customer_account_in_the_invoice'));
								redirect($_SERVER['HTTP_REFERER']);
							}

						}else{
							$this->session->set_flashdata('error', lang('Please_select_an_account_from_the_customer_accounts'));
							redirect($_SERVER['HTTP_REFERER']);
						}
					}else{
						$this->session->set_flashdata('error', lang('You_cannot_add_more_than_one_account_on_the_debit_side'));
						redirect($_SERVER['HTTP_REFERER']);
					}

					}else{
						$this->session->set_flashdata('error', lang('You_cannot_add_more_than_one_account_on_the_credit_side'));
						redirect($_SERVER['HTTP_REFERER']);
					}

					}else{
						$this->session->set_flashdata('error', lang('Sale_invoice_not_found'));
						redirect($_SERVER['HTTP_REFERER']);
					}
				}

				if (strpos($this->input->post('inv_no'), ',') !== false)
				{
					$cus_acc=$this->accounts_model->getAccountByID($_POST['entryCreditAccount'][0]);

						if($cus_acc->account_type=='local_customers' || $cus_acc->account_type=='international_customers'){

							$customer=$this->site->getCompanyByID($cus_acc->account_ref_id);

							$saleList = explode(",", $this->input->post('inv_no'));//$this->accounts_model->getAllSalesDue(NULL, NULL, $date, 1, $customer->id, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);

							$invoices = array();
							$payments = array();
							$amount=$_POST['entryCreditBalance'][0];
							$accounts_settings   = $this->accounts_model->getSettings();
							$payment_acc=$this->accounts_model->getAccountByID($_POST['entryDebitAccount'][0]);
								if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else{
									$paid_by='cash';
								}
							$temp=0;
							foreach ($saleList as $inv){
								if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
								{
									$sale_id=$this->site->getInvoiceBySaleNo(trim($inv))->id;
									$sale=$this->site->getSaleByID(trim($sale_id));
								}else{
									$sale=$this->site->getSaleByID(trim($inv));
								}
								//$this->sma->print_arrays($sale);
								if($sale->grand_total <= $amount){
									array_push($invoices, $sale->id);
									array_push($payments, $sale->grand_total);
									$amount=$amount-$sale->grand_total;
								}else{
									if($amount>0){
										array_push($invoices, $sale->id);
										array_push($payments, $amount);
										$amount=0;
									}
									$temp=1;
								}
								if($temp==1)
									break;


							}
							if($invoices){
								$data['invoices'] = $invoices;
							}
							if($payments){
								$data['payments'] = $payments;
							}
							//$this->sma->print_arrays($saleList,$_POST['entryCreditBalance'][0],$invoices,$payments);

						}

					// $this->sma->print_arrays($data, $products);
					}

				if($this->input->post('the_oldest')==1 && $receipt->the_oldest==0)
				{
					$cus_acc=$this->accounts_model->getAccountByID($_POST['entryCreditAccount'][0]);

						if($cus_acc->account_type=='local_customers' || $cus_acc->account_type=='international_customers'){

							$customer=$this->site->getCompanyByID($cus_acc->account_ref_id);

							$saleList = $this->accounts_model->getAllSalesDue(NULL, NULL, $date, 1, $customer->id, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
							$invoices = array();
							$payments = array();
							$amount=$_POST['entryCreditBalance'][0];
							$accounts_settings   = $this->accounts_model->getSettings();
							$payment_acc=$this->accounts_model->getAccountByID($_POST['entryDebitAccount'][0]);
								if ($this->accounts_model->IsAccountIsChild($accounts_settings->accounts_main_banks_acct,$payment_acc->parent_id) == '1'){
									$paid_by='bank_transfer';
								}else{
									$paid_by='cash';
								}
							$temp=0;
							foreach ($saleList as $sale){
								if($sale->amount <= $amount){
									array_push($invoices, $sale->id);
									array_push($payments, $sale->amount);
									$amount=$amount-$sale->amount;
								}else{
									if($amount>0){
										array_push($invoices, $sale->id);
										array_push($payments, $amount);
										$amount=0;
									}
									$temp=1;
								}
								if($temp==1)
									break;


							}
							if($invoices){
								$data['invoices'] = $invoices;
							}
							if($payments){
								$data['payments'] = $payments;
							}
							//$this->sma->print_arrays($saleList,$_POST['entryCreditBalance'][0],$invoices,$payments);

						}

					// $this->sma->print_arrays($data, $products);
					}


					//$this->sma->print_arrays($id, $data,$paid_by,$_POST['entryCreditBalance'][0]);



			} elseif ($this->input->post('edit_catch_receipt')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/catch_receipts');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateCatchReceiptV3($id, $data,$paid_by,$_POST['entryCreditBalance'][0],$receipt->bond_no,$amountDiff)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Catch_receipt_successfully_updated'));
				admin_redirect('accounts/catch_receipts');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_main_boxes_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct);
				$this->data['accounts_main_banks_acct']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();



				$accounts = $this->accounts_model->getAllDetailsAccounts();
				$bond_no = $this->accounts_model->getMaxBondCatchreceipt();

				//$this->sma->print_arrays($receipt);


				$this->data['bond_no'] = $this->accounts_model->getMaxBondCatchreceipt();
				$this->data['toAccountIDs'] = $accounts;
				$this->data['fromAccountIDs'] = $accounts;
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;

				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();


				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_catch_receipt'), 'page' => lang('Edit_catch_receipt')], ['link' => '#', 'page' => lang('Edit_catch_receipt')]];
				$meta                           = ['page_title' => lang('Edit_catch_receipt'), 'bc' => $bc];
				$this->load->view($this->theme . 'accounts/edit_catch_receiptV3', $this->data);
				//$this->page_construct('accounts/edit_catch_receipt', $meta, $this->data);
			}
		}
	}

	public function notice_creditor()
	{
		$this->sma->checkPermissions('notice_creditor_index', true);
		/*if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			/*if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse']  = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
        } else {
            $this->data['warehouses'] = null;
            $this->data['warehouse']  = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
        }*/

		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }

			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('notice_creditor'), 'page' => lang('Notice_creditor')], ['link' => '#', 'page' => lang('notice_creditor')]];
			$meta                = ['page_title' => lang('Notice_creditor'), 'bc' => $bc];
			$this->page_construct('accounts/notice_creditor', $meta, $this->data);
		}
	}


	public function add_notice_creditor($id = null)
	{
		$this->sma->checkPermissions('add_notice_creditor', true);

		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : 1;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : 1;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {
			/*
        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_notice_creditor.bond_no]|required');
			$this->form_validation->set_rules('toAccountID', lang('customer'), 'required');
			$this->form_validation->set_rules('fromAccountID', lang('Account'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('The_account_transferred_from'), 'required');


			if ($this->form_validation->run() == true) {

				//   if ($this->Owner || $this->Admin ) {
				$date = $this->sma->fld($this->input->post('date'));
				//  } else {
				//     $date = date('Y-m-d H:s:i');
				// }
				if (!$this->input->post('date'))
					$date = date('Y-m-d H:s:i');

				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/
				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



				$note         = $_POST['note'];














				/*$data = [
                'date'         => $date,
                'bond_no'         => $this->input->post('bond_no'),
                'reference_no'         => $this->input->post('reference_no'),
                'amount'         => $this->sma->formatDecimal($this->input->post('amount'),4),
                'tax_rate'         => $this->input->post('tax_rate'),
                'tax_amount'         => $this->input->post('tax_amount'),
                'total_amount'         => $this->input->post('total_amount'),
                'paid_by'         => $this->input->post('paid_by'),
                'cheque_no'         => $cheque_no,
                'cc_type'         => $cc_type,
                'toAccountID'         => $this->input->post('toAccountID'),
                'fromAccountID'         => $payment_type_details->payment_account,
                'receipt_name'         => $this->input->post('receipt_name'),
                'cost_center'         => $this->input->post('cost_center'),
                'note'         => $note,
                'created_by'   => $this->session->userdata('user_id'),
            ];*/





				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => $this->input->post('toAccountID'),
					'fromAccountID'         => $this->input->post('fromAccountID'),
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],
					'warehouse_id'         => $this->input->post('warehouse_id'),



				];

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                {
				$data  ['grpId']=$grpId;
                $data  ['coId']=$coId;
                $data  ['brId']=$brId;
				}

				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}


				$entryDescriptionCredit         = ($_POST['note']);
				$entryDescriptionCredit= str_replace("<br>","",($entryDescriptionCredit)."");
				$entryDescriptionCredit= str_replace("</br>","",($entryDescriptionCredit)."");
				$entryDescriptionCredit= str_replace("<div>","",($entryDescriptionCredit)."");
				$entryDescriptionCredit= str_replace("</div>","",($entryDescriptionCredit)."");
				if(strlen($entryDescriptionCredit)==0){
					if(!empty($this->input->post('reference_no'))){
						$accounts_settings   = $this->accounts_model->getSettings();
						if($this->input->post('fromAccountID')==$accounts_settings->sales_return_acct){
							$entryCreditDescription="Sales_Return_Notice_No#".$this->input->post('reference_no');
						}else if($this->input->post('fromAccountID')==$accounts_settings->accounts_discount_allowance){
							$entryCreditDescription="Allowed_Discount_Notice_No#".$this->input->post('reference_no');
						}else{
							$entryCreditDescription= null;
						}
					}else{
						$entryCreditDescription= null;
					}
				}else{
					$entryCreditDescription= $entryDescriptionCredit;
				}

				//$this->sma->print_arrays($_POST);



				if($_POST['product_1']){

					$adjust = [
						'date'         => $date,
						'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa'),
						'warehouse_id' => $this->input->post('warehouse_id'),
						'note'         => $note,
						'created_by'   => $this->session->userdata('user_id'),
						'count_id'     => null,
					];

					for($i=1;$i<=$_POST['products_no'];$i++){
						$products[] = [
							'product_id'   => $_POST['product_'.$i],
							'type'         => 'addition',
							'quantity'     => $_POST['quantity_'.$i] ? $_POST['quantity_'.$i] : 1,
							'warehouse_id' => $_POST['warehouse_id'],
							'option_id'    => null,
							'serial_no'    => null,
						];
					}

/*
				if($_POST['product_2']){
					$products[] = [
						'product_id'   => $_POST['product_2'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_2'] ? $_POST['quantity_2'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}

				if($_POST['product_3']){
					$products[] = [
						'product_id'   => $_POST['product_3'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_3'] ? $_POST['quantity_3'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}

				if($_POST['product_4']){
					$products[] = [
						'product_id'   => $_POST['product_4'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_4'] ? $_POST['quantity_4'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}

				if($_POST['product_5']){
					$products[] = [
						'product_id'   => $_POST['product_5'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_5'] ? $_POST['quantity_5'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}
*/
				}else{
					$products[] = null;
					$adjust =null;
				}



				 //$this->sma->print_arrays($data, $entryCreditDescription,$_POST,$adjust,$products);
			} elseif ($this->input->post('add_notice_creditor')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/notice_creditor');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->addNoticeCreditor($data,$entryCreditDescription,$adjust,$products)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Notice_creditor_successfully_added'));
				admin_redirect('accounts/notice_creditor');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_settings'] = $accounts_settings;
				$this->data['sales_return_acct']  = $this->accounts_model->getAccountByID($accounts_settings->sales_return_acct);
				$this->data['accounts_discount_allowance']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_discount_allowance);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();

				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
				{

					$this->data['mainGroups'] = $this->site->getMainGroup();
					$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
					$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
					$bond_no = $this->accounts_model->getMaxBondNoticeCreditor($brId);
				}else{
					$bond_no = $this->accounts_model->getMaxBondNoticeCreditor();
				}


				$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID);
				$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID);



				$this->data['bond_no'] = $bond_no;
				$this->data['accountsLocal'] = $accountsLocal;
				$this->data['accountsIner'] = $accountsIner;
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				//$this->sma->print_arrays($this->data['payment_types']);
				$this->data['warehouses']       = $this->site->getAllWarehouses();


				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Add_notice_creditor'), 'page' => lang('Add_notice_creditor')], ['link' => '#', 'page' => lang('Add_notice_creditor')]];
				$meta                           = ['page_title' => lang('Add_notice_creditor'), 'bc' => $bc];
				$this->data['modal_js']   = $this->site->modal_js();
				$this->load->view($this->theme . 'accounts/add_notice_creditor', $this->data);

				//$this->page_construct('accounts/add_notice_creditor', $meta, $this->data);
			}
		}
	}


	public function view_notice_creditor($id = null)
	{
		$this->sma->checkPermissions('view_notice_creditor', true);

		if ($this->input->get('id')) {
			$id = $this->input->get('id');
		}

		$this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
		$receipt = $this->accounts_model->getNoticeCreditorByID($id);
		$this->data['receipt']   = $receipt;





		$adjusts  = $this->accounts_model->getAdjustByNCId($id) ? $this->accounts_model->getAdjustByNCId($id) : null;
				if($adjusts)
				$this->data['AdjustItems']       = $this->accounts_model->getAdjustItems($adjusts->id);
				//$this->sma->print_arrays($this->data['AdjustItems']);


		/*$inv                 = $this->accounts_model->getNoticeCreditorByID($id);

        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
        $this->data['units']       = $this->site->getAllUnits();
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        $this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->sales_model->getAllInvoiceItems($id);
        $this->data['return_sale'] = $inv->return_id ? $this->sales_model->getInvoiceByID($inv->return_id) : null;
        $this->data['return_rows'] = $inv->return_id ? $this->sales_model->getAllInvoiceItems($inv->return_id) : null;
		$this->data['payments']        = $this->sales_model->getInvoicePayments($inv->id);
		$this->data['return_payments'] = $inv->return_id ? $this->sales_model->getInvoicePayments($inv->return_id) : null;

		*/

		$this->data['active_tax']        =$this->accounts_model->getTaxRatesActive()->rate;
		$toAccountID=$this->accounts_model->getAccountByID($receipt->toAccountID);
		$fromAccountID=$this->accounts_model->getAccountByID($receipt->fromAccountID);
		$this->data['toAccountID']  = $toAccountID;
		$this->data['fromAccountID']  = $fromAccountID;
		if($toAccountID->account_ref_id)
		$this->data['customer']    = $this->site->getCompanyByID($toAccountID->account_ref_id);




		//$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->getFRACTION($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'هللة') : $this->AmountInWords($this->sma->formatDecimal($receipt->total_amount, 2), 'SR', 'Halala');
		$this->data['FRACTION']       = $this->Settings->user_language == 'arabic' ? $this->sma->AmountInWordsAr($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'هللة') : $this->sma->AmountInWordsEn($this->sma->formatDecimal($receipt->total_amount, 2), $receipt->currency_code, $this->site->getCurrencyByCode($receipt->currencyCode)->name, 'Halala');


		$this->data['warehouse']   = $this->site->getWarehouseByID($receipt->warehouse_id ? $receipt->warehouse_id : $this->Settings->default_warehouse);

		$this->data['user']       = $this->site->getUser($receipt->created_by);
		$account       = $this->accounts_model->getAccountByID($receipt->toAccountID);
		$accountNameTo   = $account->accountNameEn ? $account->accountNameEn :  $account->accountName;
		$this->data['accountNameTo']         = $this->Settings->user_language == 'arabic' ? $account->accountName :  $accountNameTo;
		$accountFrom       = $this->accounts_model->getAccountByID($receipt->fromAccountID);
		$accountNameFrom   = $accountFrom->accountNameEn ? $accountFrom->accountNameEn :  $accountFrom->accountName;
		$this->data['accountNameFrom']         = $this->Settings->user_language == 'arabic' ? $accountFrom->accountName :  $accountNameFrom;
		//$this->sma->print_arrays($this->data['warehouse']);


		$this->data['page_title'] = lang('delivery_order');

		$this->load->view($this->theme . 'accounts/view_notice_creditor', $this->data);
	}

	public function getNoticeCreditor($warehouse_id = null)
	{
		//$this->sma->checkPermissions('adjustments');

		$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
		$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
		$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');

		$start_date     = $this->input->get('start_date') ? $this->input->get('start_date') : null;
		$end_date       = $this->input->get('end_date') ? $this->input->get('end_date') : null;
		$bond_no       = $this->input->get('bond_no') ? $this->input->get('bond_no') : null;
		$reference_no       = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;

		if ($start_date) {
			$start_date = $this->sma->fld($start_date) . ' 00:00:00';
			$end_date = $this->sma->fld($end_date) . ' 23:59:59';
		}




		$this->load->library('datatables');
		if ($this->Settings->user_language == 'arabic') {

			$this->datatables

				->select("{$this->db->dbprefix('gl_notice_creditor')}.id as id, bond_no, reference_no, date, pm1.accountName as fromAccountID, pm2.accountName as toAccountID, warehouses.name as wh_name, total_amount,paid_by, attachment")

				->from('gl_notice_creditor')
				->join('warehouses', 'warehouses.id=gl_notice_creditor.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_notice_creditor.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_notice_creditor.toAccountID', 'left');
		} else {
			$this->datatables

				->select("{$this->db->dbprefix('gl_notice_creditor')}.id as id, bond_no, reference_no, date, pm1.accountNameEn as fromAccountID, pm2.accountNameEn as toAccountID, warehouses.nameEn as wh_name, total_amount,paid_by, attachment")

				->from('gl_notice_creditor')
				->join('warehouses', 'warehouses.id=gl_notice_creditor.warehouse_id', 'left')
				->join('gl_accounts pm1', 'pm1.id=gl_notice_creditor.fromAccountID', 'left')
				->join('gl_accounts pm2', 'pm2.id=gl_notice_creditor.toAccountID', 'left');
		}

		if ($grpId) {
			$this->datatables->where('gl_notice_creditor.grpId', $grpId);
		}
		if ($coId) {
		  $this->datatables->where('gl_notice_creditor.coId', $coId);
		}
		if ($brId) {
			$this->datatables->where('gl_notice_creditor.brId', $brId);
		}
		if ($start_date && $end_date) {
			$this->datatables->where('date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}
		if ($bond_no) {
			$this->datatables->where('bond_no', $bond_no);
		}
		if ($reference_no) {
			$this->datatables->where('reference_no', $reference_no);
		}

		$delete_link = "<a href='#' class='tip po' title='<b>" . $this->lang->line('Delete_notice_creditor') . "</b>' data-content=\"<p>"
            . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('accounts/delete_notice_creditor/$1') . "'>"
            . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>";


		$this->datatables->add_column('Actions', "<div class='text-center'><a href='" . admin_url('accounts/edit_notice_creditor/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_notice_creditor') . "'><i class='fa fa-edit'></i></a>  " . $delete_link . "</div>", 'id');


		echo $this->datatables->generate();
	}

	public function edit_notice_creditor($id = null)
	{
		$this->sma->checkPermissions('edit_notice_creditor', true);
		//$this->sma->print_arrays($all_accounts);
		/*$this->sma->checkPermissions('adjustments', true);
		if(!$this->site->IsPermissions('products-adjustments')){
			 $this->session->set_flashdata('error', lang('access_denied'));
            //if ($js) {
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
            //}

		}else*/ {

			//$this->sma->print_arrays($id);

			$receipt = $this->accounts_model->getNoticeCreditorByID($id);
			if (!$id || !$receipt) {
				$this->session->set_flashdata('error', lang('Receipt_not_found'));
				redirect($_SERVER['HTTP_REFERER']);
			}

			//$this->sma->print_arrays($receipt);
			/*
		if($adjustment->approved==1){
			    $this->session->set_flashdata('error', lang('Its_Approved'));
                redirect($_SERVER['HTTP_REFERER']);

		}

        */
			$this->form_validation->set_rules('amount', lang('amount'), 'required');
			$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'required');
			if ($this->input->post('bond_no') != $receipt->bond_no) {
				$this->form_validation->set_rules('bond_no', lang('Bond_No'), 'trim|is_unique[gl_notice_creditor.bond_no]|required');
			}
			$this->form_validation->set_rules('toAccountID', lang('The_account_transferred_to'), 'required');
			//$this->form_validation->set_rules('fromAccountID', lang('The_account_transferred_from'), 'required');


			if ($this->form_validation->run() == true) {
				//    if ($this->Owner || $this->Admin) {
				$date = $this->sma->fld(trim($this->input->post('date')));
				//    } else {
				//        $date = $receipt->date;
				//    }
				//$reference_no = $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa');
				//$warehouse_id = $this->input->post('warehouse');
				/*if ($this->products_model->chechAdjustmentWarehouse($warehouse_id)) {
				    $this->session->set_flashdata('error', lang('There_is_a_previous_opening_balance_for_this_warehouse'));
                    redirect($_SERVER['HTTP_REFERER']);
            }*/

				$check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}



				$note         = $_POST['note'];







				$data = [
					'date'         => $date,
					'bond_no'         => $this->input->post('bond_no'),
					'reference_no'         => $this->input->post('reference_no'),
					'amount'         => $this->sma->formatDecimal($this->input->post('amount'), 4),
					'equivalent_amount'         => $this->sma->formatDecimal($this->input->post('local_curr_amount'), 4),
					'tax_rate'         => $this->input->post('tax_rate'),
					'tax_amount'         => $this->input->post('tax_amount'),
					'total_amount'         => $this->input->post('total_amount'),
					'currency_rate'         => $this->input->post('exchange_rate'),
					'currency_code'         => $this->input->post('currency'),
					'equivalent_tax_amount'         => $this->input->post('equivalent_tax_amount'),
					'equivalent_total_amount'         => $this->input->post('equivalent_total_amount'),
					'paid_by'         => $this->input->post('paid_by'),
					'cheque_no'         => $cheque_no,
					'cc_type'         => $cc_type,
					'toAccountID'         => $this->input->post('toAccountID'),
					'fromAccountID'         => $this->input->post('fromAccountID'),
					'receipt_name'         => $this->input->post('receipt_name'),
					'note'         => $note,
					'created_by'   => $this->session->userdata('user_id'),
					'costCenterDebitId'            => $_POST['costCenterDebitId'],
					'costCenterDebitAmount'            => $_POST['costCenterDebitAmount'],
					'costCenterCreditId'            => $_POST['costCenterCreditId'],
					'costCenterCreditAmount'            => $_POST['costCenterCreditAmount'],
					'warehouse_id'         => $this->input->post('warehouse_id'),

				];

				if ($_FILES['userfile']['size'] > 0) {
					$this->load->library('upload');
					$config['upload_path']   = $this->digital_upload_path;
					$config['allowed_types'] = $this->digital_file_types;
					$config['max_size']      = $this->allowed_file_size;
					$config['overwrite']     = false;
					$config['encrypt_name']  = true;
					$this->upload->initialize($config);
					if (!$this->upload->do_upload()) {
						$error = $this->upload->display_errors();
						$this->session->set_flashdata('error', $error);
						redirect($_SERVER['HTTP_REFERER']);
					}
					$photo                 = $this->upload->file_name;
					$data['attachment'] = $photo;
				}
				//$this->sma->print_arrays($data);

				$entryDescriptionCredit         = ($_POST['note']);
				$entryDescriptionCredit= str_replace("<br>","",($entryDescriptionCredit)."");
				$entryDescriptionCredit= str_replace("</br>","",($entryDescriptionCredit)."");
				$entryDescriptionCredit= str_replace("<div>","",($entryDescriptionCredit)."");
				$entryDescriptionCredit= str_replace("</div>","",($entryDescriptionCredit)."");
				if(strlen($entryDescriptionCredit)==0){
					if(!empty($this->input->post('reference_no'))){
						$accounts_settings   = $this->accounts_model->getSettings();
						if($this->input->post('fromAccountID')==$accounts_settings->sales_return_acct){
							$entryCreditDescription="Sales_Return_Notice_No#".$this->input->post('reference_no');
						}else if($this->input->post('fromAccountID')==$accounts_settings->accounts_discount_allowance){
							$entryCreditDescription="Allowed_Discount_Notice_No#".$this->input->post('reference_no');
						}else{
							$entryCreditDescription= null;
						}
					}else{
						$entryCreditDescription= null;
					}
				}else{
					$entryCreditDescription= $entryDescriptionCredit;
				}


				if($_POST['product_1']){

					$adjust = [
						'date'         => $date,
						'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('qa'),
						'warehouse_id' => $this->input->post('warehouse_id'),
						'note'         => $note,
						'created_by'   => $this->session->userdata('user_id'),
						'count_id'     => null,
						'notice_creditor_id' => $id,
					];

					for($i=1;$i<=$_POST['products_no'];$i++){
						$products[] = [
							'product_id'   => $_POST['product_'.$i],
							'type'         => 'addition',
							'quantity'     => $_POST['quantity_'.$i] ? $_POST['quantity_'.$i] : 1,
							'warehouse_id' => $_POST['warehouse_id'],
							'option_id'    => null,
							'serial_no'    => null,
						];
					}
/*

				$products[] = [
                    'product_id'   => $_POST['product_1'],
                    'type'         => 'addition',
                    'quantity'     => $_POST['quantity_1'] ? $_POST['quantity_1'] : 1,
                    'warehouse_id' => $_POST['warehouse_id'],
                    'option_id'    => null,
                    'serial_no'    => null,
                ];

				if($_POST['product_2']){
					$products[] = [
						'product_id'   => $_POST['product_2'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_2'] ? $_POST['quantity_2'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}

				if($_POST['product_3']){
					$products[] = [
						'product_id'   => $_POST['product_3'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_3'] ? $_POST['quantity_3'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}

				if($_POST['product_4']){
					$products[] = [
						'product_id'   => $_POST['product_4'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_4'] ? $_POST['quantity_4'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}

				if($_POST['product_5']){
					$products[] = [
						'product_id'   => $_POST['product_5'],
						'type'         => 'addition',
						'quantity'     => $_POST['quantity_5'] ? $_POST['quantity_5'] : 1,
						'warehouse_id' => $_POST['warehouse_id'],
						'option_id'    => null,
						'serial_no'    => null,
					];
				}
*/
				}else{
					$products[] = null;
					$adjust =null;
				}

				//$entryCreditDescription=strlen($entryDescriptionCredit)==0 ? null : $entryDescriptionCredit;

				 //$this->sma->print_arrays($data, $entryCreditDescription,$adjust,$products);
			} elseif ($this->input->post('edit_notice_creditor')) {
				$this->session->set_flashdata('error', validation_errors());
				admin_redirect('accounts/notice_creditor');
			}
			//$this->sma->print_arrays($data, $products);




			if ($this->form_validation->run() == true && $this->accounts_model->updateNoticeCreditor($id, $data,$entryCreditDescription,$adjust,$products,$receipt->bond_no)) {
				$this->session->set_userdata('remove_qals', 1);
				$this->session->set_flashdata('message', lang('Notice_creditor_successfully_updated'));
				admin_redirect('accounts/notice_creditor');
			} else {
				//

				$this->data['payment_types'] = $this->site->getAllPaymentTypes();
				$accounts_settings   = $this->accounts_model->getSettings();
				$this->data['accounts_settings'] = $accounts_settings;
				$this->data['sales_return_acct']  = $this->accounts_model->getAccountByID($accounts_settings->sales_return_acct);
				$this->data['accounts_discount_allowance']  = $this->accounts_model->getAccountByID($accounts_settings->accounts_discount_allowance);
				$this->data['tax_rates']  = $this->site->getAllTaxRatesActive();

				$adjusts  = $this->accounts_model->getAdjustByNCId($id) ? $this->accounts_model->getAdjustByNCId($id) : null;
				if($adjusts)
				$this->data['AdjustItems']       = $this->accounts_model->getAdjustItems($adjusts->id);


				//$this->sma->print_arrays($this->data['AdjustItems']);

				$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID);
				$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID);




				$this->data['bond_no'] = $this->accounts_model->getMaxBondNoticeCreditor();
				$this->data['accountsLocal'] = $accountsLocal;
				$this->data['accountsIner'] = $accountsIner;
				$this->data['receipt'] = $receipt;
				$this->data['id'] = $id;
				$this->data['cost_centers'] = $this->accounts_model->getAllNoMastersCostCenters();
				$this->data['currencies'] = $this->site->getAllCurrencies();
				$this->data['warehouses']       = $this->site->getAllWarehouses();
				$this->data['warehouse_id']   = ($receipt->warehouse_id ? $receipt->warehouse_id : $this->Settings->default_warehouse);


				$this->data['error']            = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
				$bc                             = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Edit_notice_creditor'), 'page' => lang('Edit_notice_creditor')], ['link' => '#', 'page' => lang('Edit_notice_creditor')]];
				$meta                           = ['page_title' => lang('Edit_notice_creditor'), 'bc' => $bc];
				$this->load->view($this->theme . 'accounts/edit_notice_creditor', $this->data);

			}
		}
	}


	public function add_opening_balance_by_excel()
    {
        $this->sma->checkPermissions('add_opening_balance', true);

        $brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

		$this->form_validation->set_rules('date', lang('date'), 'required');


        if ($this->form_validation->run() == true) {

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }

			$currency_code         = $this->input->post('currency') ? $this->input->post('currency') : $this->Settings->default_currency;

			$currency_rate=$this->site->getCurrencyByCode($currency_code)->rate;


            $check_period = $this->accounts_model->getOpenAccountingPeriodByDate($date);
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {
					$count = $this->accounts_model->getAllAccountingPeriod()->count;
					if ($count == 1)
						$ch = 0;
					else
						$ch = 1;
				}
				if ($ch == 1) {
					$this->session->set_flashdata('error', lang('Transaction_date_outside_the_open_accounting_period'));
					redirect($_SERVER['HTTP_REFERER']);
				}




			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{
				$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : 1;
				$coId     = $this->input->post('coId') ? $this->input->post('coId') : 1;
				$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');
			}

				$note         = $this->sma->clear_tags($this->input->post('note'));
				$entryDebitAccount = [];
				$entryDebitBalance = [];
				$entryCreditAccount = [];
				$entryCreditBalance = [];


            $data1 = [
				'openingDate'         => $date,
				'note'         => $note,
				'currency_code'         => $currency_code,
				'currency_rate'         => $currency_rate,
				'userID'   => $this->session->userdata('user_id'),

			];

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
            	{
				$data1  ['grpId']=$grpId;
                $data1  ['coId']=$coId;
                $data1  ['brId']=$brId;
				}

			//$this->sma->print_arrays($data1);


				$this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                //$config['allowed_types'] = 'csv';
                $config['allowed_types'] = 'xlsx|xls';
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('xlsx_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $csv = $this->upload->file_name;


			if (!$this->upload->do_upload('xlsx_file')) {
				$error = array('error' => $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
			}
			if(empty($error)){
			  if (!empty($data['upload_data']['file_name'])) {
				$import_xls_file = $data['upload_data']['file_name'];
			} else {
				$import_xls_file = 0;
			}
			$inputFileName = $this->digital_upload_path . $import_xls_file;


				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
				$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

				//$this->sma->print_arrays($allDataInSheet);

				$flag = true;
				$i=0;
				foreach ($allDataInSheet as $value) {
				  if($flag){
					$flag =false;
					continue;
				  }
				  //$inserdata[$i]['#'] = $value['A'];$keys   = ['code', 'quantity', 'cost', 'expiry', 'variant'];
				  $inserdata[$i]['sideFlage']=0;
				  $inserdata[$i]['account_code'] = trim($value['A']);
				  //$inserdata[$i]['debit'] = (trim($value['C'])>0) ? trim($value['C']) : 0;
				  //$inserdata[$i]['credit'] = (trim($value['D'])>0) ? trim($value['D']) : 0;

				  $inserdata[$i]['debit'] = (trim($value['C'])>0) ? trim($value['C']) : ((trim($value['C'])<0) ? trim($value['C']) : 0);
				  $inserdata[$i]['credit'] = (trim($value['D'])>0) ? trim($value['D']) : ((trim($value['D'])<0) ? trim($value['D']) : 0);
				  $inserdata[$i]['debit1']=$inserdata[$i]['debit'];
				  $inserdata[$i]['credit1']=$inserdata[$i]['credit'];

				  if($inserdata[$i]['debit']<0){
					$inserdata[$i]['credit']=abs($inserdata[$i]['debit']);
					$inserdata[$i]['debit']=0;
					$inserdata[$i]['sideFlage']=1;
				  }else if($inserdata[$i]['credit']<0){
					$inserdata[$i]['debit']=abs($inserdata[$i]['credit']);
					$inserdata[$i]['credit']=0;
					$inserdata[$i]['sideFlage']=1;
				  }
				  $inserdata[$i]['cost_center_code'] = trim($value['E']);
				  $inserdata[$i]['amount'] = (trim($value['G'])>0) ? trim($value['G']) : 0;
				  $i++;
				}
				$rw          = 2;
                $total_debit = 0;
                $total_credit = 0;
                $matches     = 0;

				//$this->sma->print_arrays($inserdata);
                foreach ($inserdata as $pr) {

					$total_debit=$total_debit+$pr['debit'];
					$total_credit=$total_credit+$pr['credit'];

					 if ($account = $this->accounts_model->getAccountByAccountID(trim($pr['account_code']))) {
						if($account->accountSide=="Debit" && $pr['debit']==0 && $pr['sideFlage']==0){
						//	$this->session->set_flashdata('error', lang('The_nature_of_the_account_is_debit') . ' (' . $pr['account_code'] . '). ' . lang('line_no') . ' ' . $rw);
                        //	redirect($_SERVER['HTTP_REFERER']);
						}
						if($account->accountSide=="Credit" && $pr['credit']==0 && $pr['sideFlage']==0){
						//	$this->session->set_flashdata('error', lang('The_nature_of_the_account_is_credit') . ' (' . $pr['account_code'] . '). ' . lang('line_no') . ' ' . $rw);
                        //	redirect($_SERVER['HTTP_REFERER']);
						}
						$cost_center="";
						if(trim($pr['cost_center_code'])){
							if($cost_center_arr = $this->accounts_model->getCostCenterByCostCenterID(trim($pr['cost_center_code']))){
								$cost_center=$cost_center_arr->id;
							}else{
								$this->session->set_flashdata('error', lang('Check_cost_center_code') . ' (' . $pr['cost_center_code'] . '). ' . lang('Cost_center_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
								redirect($_SERVER['HTTP_REFERER']);
							}

						}
						//$this->sma->print_arrays($account);

                        $balance   = $pr['debit1'] !=0 ? $pr['debit1'] : $pr['credit1'];
						if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
						{
						$accounts[] = [
							'accountID'   => $account->id,
							'debit'         => $this->sma->formatDecimal($pr['debit1']),
							'credit'     => $this->sma->formatDecimal($pr['credit1']),
							'balance'     => $this->sma->formatDecimal($balance),
							'cost_center'     => $cost_center,
							'cost_center_amount'     => $this->sma->formatDecimal($pr['amount']),
							'grpId'     => $grpId,
							'coId'     => $coId,
							'brId'     => $brId,

						];
					}else{
						$accounts[] = [
							'accountID'   => $account->id,
							'debit'         => $this->sma->formatDecimal($pr['debit1']),
							'credit'     => $this->sma->formatDecimal($pr['credit1']),
							'balance'     => $this->sma->formatDecimal($balance),
							'cost_center'     => $cost_center,
							'cost_center_amount'     => $this->sma->formatDecimal($pr['amount']),

						];
					}


					if ($pr['debit'] != 0) {
						$entryDebitAccount[] = $account->id;
						$entryDebitBalance[] = $this->sma->formatDecimal($currency_rate > 0 ? ($pr['debit'] * $currency_rate) : $pr['debit']);
						$costCenterDebitId[] = json_encode((array) $cost_center);
						$costCenterDebitAm = $cost_center ? $this->sma->formatDecimal($pr['amount']) : "";
						$costCenterDebitAmount[] = json_encode((array) $costCenterDebitAm);
					} else if ($pr['credit'] != 0) {

						$entryCreditAccount[] = $account->id;
						$entryCreditBalance[] = $this->sma->formatDecimal($currency_rate > 0 ? ($pr['credit'] * $currency_rate) : $pr['credit']);
						$costCenterCreditId[] = json_encode((array) $cost_center);
						$costCenterCreditAm = $cost_center ? $this->sma->formatDecimal($pr['amount']) : "";
						$costCenterCreditAmount[] = json_encode((array) $costCenterCreditAm);
					}




                    } else {
                        $this->session->set_flashdata('error', lang('Check_account_code') . ' (' . $pr['account_code'] . '). ' . lang('Account_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect($_SERVER['HTTP_REFERER']);
                    }




                    $rw++;


                }

				if($total_debit!=$total_credit){
					//$this->session->set_flashdata('error', lang('The_opening_balance_is_unbalanced'));
					//redirect($_SERVER['HTTP_REFERER']);
				}


            }
                // $this->sma->print_arrays($final);

            //} else {
            //    $this->form_validation->set_rules('csv_file', lang('upload_file'), 'required');
            //}


			if (empty($accounts)) {
				$this->form_validation->set_rules('accounts', lang('accounts'), 'required');
			} else {
				krsort($accounts);
			}





			$costCenterDebitId2 = [];
			$costCenterDebitAmount2 = [];
			$costCenterCreditId2 = [];
			$costCenterCreditAmount2 = [];

			foreach ($costCenterDebitId as $key => $acc) {
				$costCenterDebitId2[] = json_decode($acc);
			}
			foreach ($costCenterDebitAmount as $key => $acc) {
				$costCenterDebitAmount2[] = json_decode($acc);
			}
			foreach ($costCenterCreditId as $key => $acc) {
				$costCenterCreditId2[] = json_decode($acc);
			}
			foreach ($costCenterCreditAmount as $key => $acc) {
				$costCenterCreditAmount2[] = json_decode($acc);
			}
			if ($costCenterDebitId)
				$costCenterDebitId = json_encode((array) $costCenterDebitId2);
			if ($costCenterDebitAmount)
				$costCenterDebitAmount = json_encode((array) $costCenterDebitAmount2);
			if ($costCenterCreditId)
				$costCenterCreditId = json_encode((array) $costCenterCreditId2);
			if ($costCenterCreditAmount)
				$costCenterCreditAmount = json_encode((array) $costCenterCreditAmount2);

				if($costCenterDebitId=='[[""]]'){
					$costCenterDebitId='[""]';
				}
				if($costCenterCreditId=='[[""]]'){
					$costCenterCreditId='[""]';
				}
				if($costCenterDebitAmount=='[[""]]'){
					$costCenterDebitAmount='[""]';
				}
				if($costCenterCreditAmount=='[[""]]'){
					$costCenterCreditAmount='[""]';
				}

             //$this->sma->print_arrays($data1, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId, $costCenterCreditId, $costCenterDebitAmount, $costCenterCreditAmount,$grpId,$coId,$brId);
        }

        if ($this->form_validation->run() == true && $this->accounts_model->addOpeningBalance($data1, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId, $costCenterCreditId, $costCenterDebitAmount, $costCenterCreditAmount,$grpId,$coId,$brId)) {


			$this->session->set_userdata('remove_obls', 1);
			$this->session->set_flashdata('message', lang('Openeing_balance_successfully_added'));
			admin_redirect('accounts/opening_balance');
		} else {


			$cost_centers     = $this->accounts_model->getAllNoMastersCostCenters();

			//$this->sma->print_arrays($pr);

			$this->data['cost_centers'] = json_encode($cost_centers);
			$this->data['currencies'] = $this->site->getAllCurrencies();

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->data['mainGroups'] = $this->site->getMainGroup();
				$this->data['mainCompanies'] = $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
				$this->data['mainBranches'] = $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);

			}


            $this->data['error']      = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc                       = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('accounts'), 'page' => lang('accounts')], ['link' => '#', 'page' => lang('Add_opening_balance_by_excel')]];
            $meta                     = ['page_title' => lang('Add_opening_balance_by_excel'), 'bc' => $bc];
            $this->page_construct('accounts/add_opening_balance_by_excel', $meta, $this->data);
        }
    }



	public function collection_report($id = null, $pdf = null, $xls = null, $start_date = null, $end_date = null)
	{
		$this->sma->checkPermissions('accountstatement', true);

		$accounts_settings   = $this->accounts_model->getSettings();

		$parent_account_details = $this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->parent_id;




		if ($pdf || $xls) {




			$show_style    = $this->input->get('show_style') ? $this->input->get('show_style') : 1;
			$show_Opening_balance    = $this->input->get('show_Opening_balance'); // ? $this->input->get('show_Opening_balance') : 1;
			$Carry_over_balance    = $this->input->get('Carry_over_balance'); // ? $this->input->get('show_Opening_balance') : 1;

			$account    = $this->input->get('account') ? $this->input->get('account') : null;
			$salesman    = $this->input->get('salesman') ? $this->input->get('salesman') : null;
			$start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
			$end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;

			$grpId     = $this->input->get('grpId') ? $this->input->get('grpId') : null;
			$coId     = $this->input->get('coId') ? $this->input->get('coId') : null;
			$brId     = $this->input->get('brId') ? $this->input->get('brId') : $this->session->userdata('brId');


			/*if($id){
        if($show_style==1)
            $this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($id)[0]->accountID);
        else
            $this->data['accountList'] = $this->accounts_model->getAccounts($id);
        }else{
        if($show_style==1)
            $this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)[0]->accountID);
        else
            $this->data['accountList'] = $this->accounts_model->getAccounts($account);

        }*/
			// $id=1;
			if ($id) {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
				//else
				$accountList = $this->accounts_model->getAccounts($id);
			} else {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
				//else
				if($show_style==2 || $show_style==3){
					$accountList = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)['0']->accountID);
				}else{
				$accountList = $this->accounts_model->getAccounts($account);
					}
			}
			asort($accountList);



			$accounts = $this->accounts_model->getAccounts();

			//$this->sma->print_arrays($this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID));

			//



			if ($start_date && $end_date) {
				//$start_date = $this->sma->fld($start_date);
				//$end_date   = $this->sma->fld($end_date);
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				//$date     = explode(' ', $start_date);
				//$start_date = $date[0];
				//$date     = explode(' ', $end_date);
				//$end_date = $date[0];
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				//	$this->sma->print_arrays($prev_start_date , $end_date ,$show_Opening_balance,date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59');
				$entryList  = $this->accounts_model->getEntriesCollection($start_date, $end_date, $show_Opening_balance,$grpId, $coId, $brId);
				//$this->data['all_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date , $end_date ,$show_Opening_balance);
				if ($Carry_over_balance)
					$prev_entryList  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance,$grpId, $coId, $brId);
				//$this->sma->print_arrays($this->data['entryList'],$this->data['prev_entryList']);
				//$this->sma->print_arrays($start_date ,$prev_start_date , date('Y-m-d', strtotime('-1 days', strtotime($start_date))));

				//



			} else {
				$entryList  = $this->accounts_model->getEntriesCollection(NULL,NULL, $show_Opening_balance,$grpId, $coId, $brId);
			}
			$filter_id  = $id;
			$salesman_id  = $salesman;
			$accounts_main_boxes_acct= $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct)->id;
			$accounts_main_banks_acct= $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct)->id;
			$payment_types_boxes = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($accounts_main_boxes_acct)['0']->accountID);
			$payment_types_banks = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($accounts_main_banks_acct)['0']->accountID);





			//$this->sma->print_arrays($filter_id);


			//$this->sma->print_arrays($accountList1);

			$this->load->library('excel');
			$this->excel->setActiveSheetIndex(0);

			if ($this->Settings->user_language == 'arabic') {
				$this->excel->getActiveSheet()->setRightToLeft(true);
			}
			$this->excel->getActiveSheet()->setTitle(lang('Collection_report'));

			$this->excel->getActiveSheet()->mergeCells('A1:G1');

			$this->excel->getActiveSheet()->SetCellValue('A1', lang('Collection_report'));
			$this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$styleArray = array(
				'font' => array(
					'bold' => true,
					'size' => 20,
					'name'  => 'Cairo-Bold'
				)

			);

			//$this->excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);





			$this->excel->getActiveSheet()->mergeCells('A2:G2');
			$this->excel->getActiveSheet()->SetCellValue('A2', $this->Settings->site_name);
			$this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(18);
			$this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);

			$this->excel->getActiveSheet()->mergeCells('A3:G3');
			$this->excel->getActiveSheet()->SetCellValue('A3', ($this->input->get('start_date') ?  lang('period') . ' ' . lang('Collection_report') . ' ' . lang('From') . ' ' . $this->input->get('start_date') . ' ' . lang('to') . ' ' . $this->input->get('end_date')  : ''));
			$this->excel->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setSize(15);
			$this->excel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);




			$styleArray1 = array(

				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'f9f9f9')
				)
			);

			$styleArrayGreen = array(

				'font' => array(
					'color' => array('rgb' => '28a745')
				)
			);

			$styleArrayRed = array(

				'font' => array(
					// 'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'dc3545')
				)
			);

			/*$styleArray2 = array(
              'borders' => array(
                      'allborders' => array(
                          'style' => PHPExcel_Style_Border::BORDER_THIN
                      )
                  )
                  );*/

			$this->excel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleArray1);

			$accountSide = "";

			$accountInfo = $this->accounts_model->getAccountByID($_GET['account']);
			$accountSide = $accountInfo->accountSide;

			if ($accountSide == "") {
				if ($_GET['account'] == 1)
					$accountSide = "Debit";
				if ($_GET['account'] == 2)
					$accountSide = "Credit";
				if ($_GET['account'] == 3)
					$accountSide = "Debit";
				if ($_GET['account'] == 4)
					$accountSide = "Credit";
			}
			//if(!isset($_GET['account']))
			//unset($accountList);
			//$accountList = $this->accounts_model->getAccounts($_GET['account']);

			$row = 4;

			foreach ($accountList as $account1) {




				$account1 = (array) $account1;
				if($this->input->get('show_style')==2 || $this->input->get('show_style')==3){
					if($_GET['account']){
						if($_GET['account']!=$account1['id']){
					if ($this->accounts_model->IsAccountIsChild($_GET['account'],$account1['parent_id']) == '0'){
						continue;
					}
				}
				}
				}
				$accountSide = "";

				if ($accountSide == "") {
					$accountInfo = $this->accounts_model->getAccountByID($account1['id']);
					$accountSide = $accountInfo->accountSide;
				}
				//if($accountSide==""){
				if ($account1['id'] == 1)
					$accountSide = "Debit";
				if ($account1['id'] == 2)
					$accountSide = "Credit";
				if ($account1['id'] == 3)
					$accountSide = "Debit";
				if ($account1['id'] == 4)
					$accountSide = "Credit";
				//}

				//echo $account1['id']." ".$accountSide."<br/>";


				//$accountList1=$this->accounts_model->getAccountChilds($account1['accountID']);
				if($show_style==2 || $show_style==3){

					$accountList1=$this->accounts_model->getAccountByAccountIDV2($account1['accountID']);
				}else{
				$accountList1 = $this->accounts_model->getAccountChildsWithLevels($account1['accountID'], $levels);
				}

				$root_parent_key = $account1['id'];


				//$accountList1 = $this->accounts_model->getAccountChilds($this->accounts_model->getAccountByID($account)->accountID);//


				//$account1=="";
				//if (!empty($accountEntries))

				{
					if ($this->Settings->user_language == 'arabic' || $account1['accountNameEn'] == '') {
						$account1 = $account1['accountName'];
					} else {
						$account1 = $account1['accountNameEn'];
					}


					$this->excel->getActiveSheet()->mergeCells('A' . $row . ':G' . $row);
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $account1);
					//$this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(15);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$row++;
					$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang('Sr#'));
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('B' . $row, lang("Entry_number"));
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang("date"));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang("Detailed_account"));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('E' . $row, lang("Salesman"));
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('F' . $row, lang("reference_no"));
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

					$this->excel->getActiveSheet()->SetCellValue('G' . $row, lang("Amount") . ' (' . $this->Settings->symbol . ')');
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(13);
					$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);


					$this->excel->getActiveSheet()->getStyle('A' . $row . ':G' . $row)->applyFromArray($styleArray1);

					$row++;
				}

				$balanceDebit = 0;
				$balanceCredit = 0;
				$fbalance = 0;
				$sr = 0;

				$prev_fbalance = 0;



				foreach ($accountList1 as $account) {
					//$prev_fbalance=0;

					$account = (array) $account;
					if($this->input->get('show_style')==1 || $this->input->get('show_style')==4){
						if($root_parent_key!=$account['id']){
					if ($this->accounts_model->IsAccountIsChild($root_parent_key,$account['parent_id']) == '0'){
						continue;
					}
				}
				}
					//if($this->accounts_model->isMembersTree($root_parent_key,$account['id'])==1 || ($root_parent_key==$account['id']))
					{

						$prev_accountEntries = array();

						$id      = $account['id'];
						$accountBalance = $account['accountDebit'] - $account['accountCredit'];


						foreach ($prev_entryList as $prev_entry) {
							$prev_entry = (array) $prev_entry;
							//
							if (in_array($account['id'], json_decode($prev_entry['entryDebitAccount'])) || in_array($account['id'], json_decode($prev_entry['entryCreditAccount']))) {
								array_push($prev_accountEntries, $prev_entry);
							}
						}





						if (empty($_GET)) {

							//
							$check_period = $this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
							if ($check_period || $check_period->period_status == 1) {
								$_GET['start_date'] = $this->sma->hrsd($check_period->period_from_date);
								$_GET['end_date'] = $this->sma->hrsd($check_period->period_to_date);
							}
						}


						//if (!empty($prev_accountEntries))
						{

							//print_r($prev_accountEntries);




							foreach ($prev_accountEntries as $entry) {
								$entry = (array) $entry;

								$entryDebitAccounts = array();
								$entryDebitBalances = 0;

								$entryCreditAccounts = array();
								$entryCreditBalances = 0;

								foreach (json_decode($entry['entryDebitAccount']) as $debitAccount) {
									array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
									$entryDebitBalances += 1;
								}

								foreach (json_decode($entry['entryCreditAccount']) as $creditAccount) {
									array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
									$entryCreditBalances += 1;
								}



								//print_r($entryDebitAccounts);
								foreach ($entryDebitAccounts as $account) {
									$balance = $account[key($account)];
									$account = key($account);
									$accountInfo = $this->accounts_model->getAccountByID($account);
									//print_r($accountInfo->id);


									//$accountInfo = $this->entry_model->getAccount($account);

									$x = 0;
									if ($accountInfo->id == $id) {
										//$prev_fbalance=$prev_fbalance+$balance;
										if ($accountSide == "Debit")
											$prev_fbalance = $prev_fbalance + $balance;
										else
											$prev_fbalance = $prev_fbalance - $balance;




										//$accountDebitInfo    = (array) $this->account_model->getAccounts($account)[0];



										$x++;
									}
								}
								foreach ($entryCreditAccounts as $account) {
									$balance = $account[key($account)];
									$account = key($account);
									$accountInfo = $this->accounts_model->getAccountByID($account);
									//echo $balance;



									$y = 0;
									if ($accountInfo->id == $id) {
										//$prev_fbalance=$prev_fbalance-$balance;
										if ($accountSide == "Credit")
											$prev_fbalance = $prev_fbalance + $balance;
										else
											$prev_fbalance = $prev_fbalance - $balance;




										$y++;
									}
								}
							}
						}
					}
				}
				//$this->sma->print_arrays($prev_fbalance);



				if ($prev_fbalance) {

					$sr++;

					$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
					$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(11);



					$this->excel->getActiveSheet()->SetCellValue('B' . $row, '');
					$this->excel->getActiveSheet()->getStyle('B' . $row)->getFont()->setSize(11);


					$this->excel->getActiveSheet()->SetCellValue('C' . $row, lang('Before') . ' ' . (isset($_GET['start_date']) ? $_GET['start_date'] : ''));
					$this->excel->getActiveSheet()->getStyle('C' . $row)->getFont()->setSize(13);


					$this->excel->getActiveSheet()->SetCellValue('D' . $row, lang('Carry-over_balance'));
					$this->excel->getActiveSheet()->getStyle('D' . $row)->getFont()->setSize(11);



					if ($accountSide == "Debit") {
						$this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->formatDecimal(abs($prev_fbalance)));
						$balanceDebit = $balanceDebit + $prev_fbalance;
						$fbalance = $fbalance + $prev_fbalance;
					}
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(11);
					$this->excel->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);

					if ($accountSide == "Credit") {

						$this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->formatDecimal(abs($prev_fbalance)));
						$balanceCredit = $balanceCredit + $prev_fbalance;
						$fbalance = $fbalance + $prev_fbalance;
					}
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setSize(11);
					$this->excel->getActiveSheet()->getStyle('F' . $row)->getFont()->setBold(true);

					/*
                    $this->excel->getActiveSheet()->SetCellValue('G'.$row,$this->sma->formatDecimal($fbalance));
                    $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setSize(11);
                    $this->excel->getActiveSheet()->getStyle('G'.$row)->getFont()->setBold(true);
                    */
					//$this->excel->getActiveSheet()->getStyle('A'.$row.':G'.$row)->applyFromArray($styleArray1);


					if ($accountSide == "Debit") {
						if ($fbalance > 0) {
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayGreen);
						} else {
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayRed);
						}
					}

					if ($accountSide == "Credit") {
						if ($fbalance > 0) {
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayGreen);
						} else {
							$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($fbalance));
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(11);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
							$this->excel->getActiveSheet()->getStyle('G' . $row)->applyFromArray($styleArrayRed);
						}
					}


					$row++;
				}








				foreach ($accountList1 as $account) {

					$account = (array) $account;
					if($this->input->get('show_style')==1 || $this->input->get('show_style')==4){
						if($root_parent_key!=$account['id']){
					if ($this->accounts_model->IsAccountIsChild($root_parent_key,$account['parent_id']) == '0'){
						continue;
					}
				}
				}



				$accountEntries_cust = array();

				$id      = $account['id'];
				{

				foreach($entryList as $entry){
					$entry = (array) $entry;
					if (in_array($account['id'], json_decode($entry['entryDebitAccount'])) || in_array($account['id'], json_decode($entry['entryCreditAccount']))){
                        array_push($accountEntries_cust, $entry);
					}
				}



					$accountEntries = array();


					$accountEntries = array();

					foreach($accountEntries_cust as $entry){
						$entry = (array) $entry;
							foreach ($payment_types_boxes as $payment_type) {
								$payment_type = (array) $payment_type;
								if (in_array($payment_type['id'], json_decode($entry['entryDebitAccount'])) || in_array($payment_type['id'], json_decode($entry['entryCreditAccount']))){
									if($salesman_id){

										if($entry['entryType']=='Catch_receipts'){
											$salesman=$this->accounts_model->getCatchReceiptByBondNo($entry['reference_no']);
											$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
										}else if($entry['entryType']=='Sales_Payments' || $entry['entryType']=='sale_completed' || $entry['entryType']=='sale_pending' || $entry['entryType']=='sale_returned'){
											$salesman=$this->accounts_model->getSaleByReference($entry['reference_no']);
											$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
										}else{
											$salesman_ref_id=null;
										}

										if($salesman_ref_id &&($salesman_ref_id==$salesman_id))
										array_push($accountEntries, $entry);
									}else{
										array_push($accountEntries, $entry);
									}

								}
							}
					}

					foreach($accountEntries_cust as $entry){
						$entry = (array) $entry;
							foreach ($payment_types_banks as $payment_type) {
								$payment_type = (array) $payment_type;
								if (in_array($payment_type['id'], json_decode($entry['entryDebitAccount'])) || in_array($payment_type['id'], json_decode($entry['entryCreditAccount']))){

									if($salesman_id){

										if($entry['entryType']=='Catch_receipts'){
											$salesman=$this->accounts_model->getCatchReceiptByBondNo($entry['reference_no']);
											$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
										}else if($entry['entryType']=='Sales_Payments' || $entry['entryType']=='sale_completed' || $entry['entryType']=='sale_pending' || $entry['entryType']=='sale_returned'){
											$salesman=$this->accounts_model->getSaleByReference($entry['reference_no']);
											$salesman_ref_id=$salesman ? $salesman->salesman_id : null;
										}else{
											$salesman_ref_id=null;
										}

										if($salesman_ref_id &&($salesman_ref_id==$salesman_id))
										array_push($accountEntries, $entry);
									}else{
										array_push($accountEntries, $entry);
									}
								}
							}
					}


						//
						//print_r($accountEntries);

						if (!empty($accountEntries)) {


							if ($this->Settings->user_language == 'arabic' || $account['accountNameEn'] == '') {
								$accountName = $account['accountName'];
							} else {
								$accountName = $account['accountNameEn'];
							}



							foreach ($accountEntries as $entry) {
								//$this->sma->print_arrays($entry);
								$entry = (array) $entry;

								$entryDebitAccounts = array();
								$entryDebitBalances = 0;

								$entryCreditAccounts = array();
								$entryCreditBalances = 0;

								$entryDescriptionText = "";

								foreach (json_decode($entry['entryDebitAccount']) as $debitAccount) {
									array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entry['entryDebitBalance'])[$entryDebitBalances]));
									if (json_decode($entry['entryDebitAccount'])[$entryDebitBalances] == $id) {
										if ($entryDescriptionText == "")
											$entryDescriptionText = json_decode($entry['entryDebitDescription'])[$entryDebitBalances];
										else
											$entryDescriptionText = $entryDescriptionText . " - " . json_decode($entry['entryDebitDescription'])[$entryDebitBalances];
									}
									$entryDebitBalances += 1;
								}

								foreach (json_decode($entry['entryCreditAccount']) as $creditAccount) {
									array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entry['entryCreditBalance'])[$entryCreditBalances]));
									if (json_decode($entry['entryCreditAccount'])[$entryCreditBalances] == $id) {
										if ($entryDescriptionText == "")
											$entryDescriptionText = json_decode($entry['entryCreditDescription'])[$entryCreditBalances];
										else
											$entryDescriptionText = $entryDescriptionText . " - " . json_decode($entry['entryCreditDescription'])[$entryCreditBalances];
									}
									$entryCreditBalances += 1;
								}






								$sr++;



								$entryDescriptionTextSearch=$entryDescriptionText;

					if($entryDescriptionTextSearch==""){
						$entryDescriptionTextSearch=$entry['entryDescription'];
						$entryDescription= str_replace("<p>","",($entryDescriptionTextSearch)."");
						$entryDescription= str_replace("</p>","",($entryDescription)."");
						$iof=strpos(trim($entryDescription), "#");
						if($iof>-1)
							$entryDescriptionTextSearch=lang(substr($entryDescription,0,$iof))." ".substr($entryDescription,$iof+1);
					}

						if($entryDescriptionTextSearch=="")
							$entryDescriptionTextSearch=$entry['entryType'];


								/*	if($this->input->post('show_style')==0){
						echo '
						<tr style="display:none;">';
					}else{
						echo '
						<tr>';
					}

                    */

					if((!is_numeric(strpos(lang($entryDescriptionTextSearch), $search_description)) && trim($search_description)!='')){
					}else{


								$this->excel->getActiveSheet()->SetCellValue('A' . $row, $sr);
								if(empty($entry['entry_no'])){
									$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entryID']);
								}else{
									$this->excel->getActiveSheet()->SetCellValue('B' . $row, '#' . $entry['entry_no']);
								}

								/*
                    echo '
					<td class="text-left align-middle">'.$sr.'</td>

					<td class="align-middle">';
					?>
				<a href="<?php echo admin_url('accounts/view_entries/'.$entry['entryID']); ?>" data-toggle="modal" data-target="#myModal">
                                <?= '#'.$entry['entryID'] ?>
                            </a>

				<?php

				echo '</td>
                <td class="text-left align-middle">'.$this->sma->hrsd($entry['entryDate']).'</td>';
				*/
								$this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->hrsd($entry['entryDate']));

								if ($entry['entryType'] != "Opening_balance") {
									if ($entryDescriptionText == "")
										$entryDescriptionText = $entry['entryDescription'];

									$entryDescription = str_replace("<p>", "", ($entryDescriptionText) . "");
									$entryDescription = str_replace("</p>", "", ($entryDescription) . "");
									$iof = strpos(trim($entryDescription), "#");
									if ($iof > -1)
										$entryDescription = lang(substr($entryDescription, 0, $iof)) . " " . substr($entryDescription, $iof + 1);


									$this->excel->getActiveSheet()->SetCellValue('D' . $row, $accountName . '-' . lang($entryDescription));
									/*echo '
					<td class="text-left align-middle">'.$accountName.'-'.lang($entryDescription).'</td>
					<td class="align-middle">';
                    */
								} else {

									$this->excel->getActiveSheet()->SetCellValue('D' . $row, $accountName . '-' . lang($entry["entryType"]));
									/*
					echo '
					<td class="text-left align-middle">'.$accountName.'-'.lang($entry['entryType']).'</td>
					<td class="align-middle">';
					*/
								}

					$salesman_name='';
					$reference_no='';
					if($entry['entryType']=='Catch_receipts'){
						$salesman=$this->accounts_model->getCatchReceiptByBondNo($entry['reference_no']);
						$salesman_name=$salesman ? $salesman->salesman_name : '';
						$reference_no=$salesman ? $salesman->reference_no : '';
					}else if($entry['entryType']=='Sales_Payments' || $entry['entryType']=='sale_completed' || $entry['entryType']=='sale_pending' || $entry['entryType']=='sale_returned'){
						$salesman=$this->accounts_model->getSaleByReference($entry['reference_no']);
						$salesman_name=$salesman ? $this->site->getCompanyByID($salesman->salesman_id)->company : '';
						$reference_no=$salesman ? $salesman->reference_no : '';
					}

					$this->excel->getActiveSheet()->SetCellValue('E' . $row, $salesman_name);
					$this->excel->getActiveSheet()->SetCellValue('F' . $row, $reference_no);





								/*echo '			</td>
					<td class="align-middle">';
					*/
								$Cbalance_temp = "";
								foreach ($entryCreditAccounts as $account) {
									$balance = $account[key($account)];
									$account = key($account);
									$accountInfo = $this->accounts_model->getAccountByID($account);

									$y = 0;
									if ($accountInfo->id == $id) {
										$Cbalance_temp = $Cbalance_temp . "" . $this->sma->formatDecimal($balance) . "\n";
										$this->excel->getActiveSheet()->SetCellValue('G' . $row, $Cbalance_temp);
										$this->excel->getActiveSheet()->getStyle('G' . $row)->getAlignment()->setWrapText(true);
										//$this->excel->getActiveSheet()->SetCellValue('F'.$row,$this->sma->formatDecimal($Cbalance_temp));
										//echo '<strong>'.$this->sma->formatDecimal($balance).'</strong><br />';
										$balanceCredit = $balanceCredit + $balance;
										if ($accountSide == "Credit")
											$fbalance = $fbalance + $balance;
										else
											$fbalance = $fbalance - $balance;





										$y++;
									}
								}



								$row++;
							}
							}
						}
					}
				}




				$total = 0;
				$diffDebit = 0;
				$diffCredit = 0;
				if ($balanceDebit > $balanceCredit) {
					$total = $balanceDebit;
					$diffDebit = ($balanceDebit - $balanceCredit);
				} else {
					$total = $balanceCredit;
					if ($balanceCredit > $balanceDebit)
						$diffCredit = ($balanceCredit - $balanceDebit);
				}


				if ($diffDebit != 0 || $diffCredit != 0) {
					if ($diffDebit != 0)
						$diffDebit = $this->sma->formatDecimal($diffDebit);
					else
						$diffDebit = '';

					if ($diffCredit != 0)
						$diffCredit = $this->sma->formatDecimal($diffCredit);
					else
						$diffCredit = '';
				}

				$this->excel->getActiveSheet()->mergeCells('A' . $row . ':F' . $row);
				$this->excel->getActiveSheet()->SetCellValue('A' . $row, lang("total"));
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);





				$this->excel->getActiveSheet()->SetCellValue('G' . $row, $this->sma->formatDecimal($balanceCredit));
				$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setSize(13);
				$this->excel->getActiveSheet()->getStyle('G' . $row)->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('G' . $row)->getBorders()
					->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);









				$row++;
			}



















			$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(60);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);



			$filename = lang('Collection_report');
			$this->load->helper('excel');
			create_excel($this->excel, $filename);
		} else {


			$show_style    = $this->input->post('show_style') ? $this->input->post('show_style') : 1;
			$show_Opening_balance    = $this->input->post('show_Opening_balance'); // ? $this->input->post('show_Opening_balance') : 1;
			$Carry_over_balance    = $this->input->post('Carry_over_balance'); // ? $this->input->post('show_Opening_balance') : 1;

			$account    = $this->input->post('account') ? $this->input->post('account') : null;
			$salesman    = $this->input->post('salesman') ? $this->input->post('salesman') : null;
			$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : null;
			$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : null;

			$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
			$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
			$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

			/*if($id){
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($id)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($id);
		}else{
		if($show_style==1)
			$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)[0]->accountID);
		else
			$this->data['accountList'] = $this->accounts_model->getAccounts($account);

		}*/
		//$this->sma->print_arrays($account,$parent_account_details);
			if ($id) {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($id);
				//else
				$this->data['accountList'] = $this->accounts_model->getAccounts($id);
			} else {
				//if($show_style==1)
				//	$this->data['accountList'] = $this->accounts_model->getAllMastersAccounts($account);
				//else
				//$this->data['accountList'] = $this->accounts_model->getAccounts($account);

				if($show_style==2 || $show_style==3){
				$this->data['accountList'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($account)['0']->accountID);
				}else{
					$this->data['accountList'] = $this->accounts_model->getAccounts($account);
				}
				//$this->sma->print_arrays($this->data['accountList']);
			}


			$this->data['accounts'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($parent_account_details)['0']->accountID);//$this->accounts_model->getAccounts();
			//$this->sma->print_arrays($this->data['accounts']);
			//$this->sma->print_arrays($this->accounts_model->getAccountChilds($this->data['accountList']['0']->accountID));
			//$this->sma->print_arrays($this->accounts_model->getAccounts($id));

			//$this->sma->print_arrays($this->accounts_model->isMembersTree($this->accounts_model->getAccountByID($account)->id,$this->accounts_model->getAccountByID($account)->id,0));

			//



			if ($start_date && $end_date) {
				//$start_date = $this->sma->fld($start_date);
				//$end_date   = $this->sma->fld($end_date);
				$start_date = $this->sma->fld($start_date) . ' 00:00:00';
				$end_date = $this->sma->fld($end_date) . ' 23:59:59';
				//$date     = explode(' ', $start_date);
				//$start_date = $date[0];
				//$date     = explode(' ', $end_date);
				//$end_date = $date[0];
				$ts1 = strtotime($start_date);
				$year1 = date('Y', $ts1);
				$prev_start_date = $this->accounts_model->getMaxMinAccountingPeriodByYear($year1)->period_from_date;
				$prev_start_date = $prev_start_date . ' 00:00:00';


				//	$this->sma->print_arrays($prev_start_date , $end_date ,$show_Opening_balance,date('Y-m-d', strtotime('-1 days', strtotime($start_date))).' 23:59:59');
				$this->data['entryList']  = $this->accounts_model->getEntriesCollection($start_date, $end_date, $show_Opening_balance,$grpId, $coId, $brId);
				//$this->data['all_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date , $end_date ,$show_Opening_balance);
				if ($Carry_over_balance)
					$this->data['prev_entryList']  = $this->accounts_model->getEntriesByDateApproved($prev_start_date, date('Y-m-d', strtotime('-1 days', strtotime($start_date))) . ' 23:59:59', $show_Opening_balance,$grpId, $coId, $brId);
				//$this->sma->print_arrays($this->data['entryList'],$this->data['prev_entryList']);
				//$this->sma->print_arrays($start_date ,$prev_start_date , date('Y-m-d', strtotime('-1 days', strtotime($start_date))));

				//



			} else {
				$this->data['entryList']  = $this->accounts_model->getEntriesCollection(NULL,NULL, $show_Opening_balance,$grpId, $coId, $brId);
			}


			//$this->sma->print_arrays($this->data['entryList']);
			$this->data['salesman_id']  = $salesman;
			$this->data['filter_id']  = $id;
			$this->data['parent_account_details']  = $parent_account_details;
			$this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();
			//$this->data['payment_types'] = $this->site->getAllPaymentTypes();
			$accounts_main_boxes_acct= $this->accounts_model->getAccountByID($accounts_settings->accounts_main_boxes_acct)->id;
			$accounts_main_banks_acct= $this->accounts_model->getAccountByID($accounts_settings->accounts_main_banks_acct)->id;
			$this->data['payment_types_boxes'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($accounts_main_boxes_acct)['0']->accountID);
			$this->data['payment_types_banks'] = $this->accounts_model->getAccountChilds($this->accounts_model->getAccounts($accounts_main_banks_acct)['0']->accountID);
			//$this->sma->print_arrays($this->data['payment_types_banks']);


			$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        {

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);




            }
			$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Collection_report'), 'page' => lang('Collection_report')], ['link' => '#', 'page' => lang('Collection_report')]];
			$meta                = ['page_title' => lang('Collection_report'), 'bc' => $bc];
			$this->page_construct('accounts/collection_report', $meta, $this->data);
		}
	}

	public function suggestionsBillers()
    {
        $term = $this->input->get('term', true);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . admin_url('welcome') . "'; }, 10);</script>");
        }

        //$rows = $this->products_model->getProductNames($term);
		$rows = $this->accounts_model->getBillerSuggestions($term, $limit);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = ['id' => $row->id, 'text' => $row->company, 'name' => $row->company];
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json([['id' => 0, 'text' => lang('Biller_no_match_found'), 'value' => $term]]);
        }
    }

	public function debtliability_old($pdf = null, $xls = null, $start_date = null, $end_date = null, $customer = null)
	{

		//$this->sma->checkPermissions('debtliability', true);

		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');


		//$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : $this->sma->hrsd(date('Y-m-d'));
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$customer     = $this->input->post('customer') ? $this->input->post('customer') : '';
		$salesman_id     = $this->input->post('salesman_id') ? $this->input->post('salesman_id') : '';
		$not_due     = isset($_POST['not_due'])  ? $_POST['not_due'] : '1';
		$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';
		$show_Opening_balance    = $this->input->post('show_Opening_balance') ? $this->input->post('show_Opening_balance') : 0; // ? $this->input->get('show_Opening_balance') : 1;

		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0') {
			$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_POST['compare_periods']; $i++) {

				//if($i!=$_POST['compare_periods'])
				$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			$start_date = $end_date;
		}




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';


		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate($start_date, $end_date, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate(NULL, $end_date, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);




			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['customers'] = $this->input->post('brId') ? $this->site->getCustomersBybrId($this->input->post('brId')) : $this->site->getCustomersBybrId($this->session->userdata('brId'));




            }else{
				$this->data['customers']      = $this->site->getAllCompanies('customer');
			}



			if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0){
				$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregateWithOutCustRefId($start_date, $end_date, $customer, $not_due, $the_oldest,$grpId,$coId,$brId);
			}else{
				//$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregate(NULL, $end_date, $customer, $not_due, $the_oldest,$grpId,$coId,$brId);
				$this->data['purchaseList'] = $this->site->getAllPurchasesDueAggregateWithOutCustRefId(NULL, $end_date, $customer, $not_due, $the_oldest,$grpId,$coId,$brId);
			}

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 $this->data['suppliers'] = $this->input->post('brId') ? $this->site->getSuppliersBybrIdWithOutCustRefId($this->input->post('brId')) : $this->site->getSuppliersBybrIdWithOutCustRefId($this->session->userdata('brId'));




            }else{
				$this->data['suppliers']      = $this->site->getAllSuppliersWithOutCustRefId();

			}


			/*$i = isset($this->data['saleList']) ? sizeof($this->data['saleList']) : 0;
			for ($r = 0; $r < $i; $r++) {
				$this->data['saleList'][$r]->opening_balance_debit=0;
				$this->data['saleList'][$r]->opening_balance_credit=0;

				if($show_Opening_balance==1){

				$accountID=$this->site->getAccountByRefId($this->data['saleList'][$r]->customer_id,'local_customers')->id;
				//$this->sma->print_arrays($accountID);
				if($accountID==""){
					$accountID=$this->site->getAccountByRefId($this->data['saleList'][$r]->customer_id,'international_customers')->id;
					if($accountID==""){
						$this->data['saleList'][$r]->opening_balance_debit=0;
						$this->data['saleList'][$r]->opening_balance_credit=0;
					}else{
						$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($accountID,'','',$grpId,$coId,$brId);
					}
				}else{
					$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($accountID,'','',$grpId,$coId,$brId);
				}
				if($accountTotalOB['debitTotal']>0){
					$this->data['saleList'][$r]->opening_balance_debit=$accountTotalOB['debitTotal'];
					$this->data['saleList'][$r]->opening_balance_credit=0;
				}
				if($accountTotalOB['creditTotal']>0){
					$this->data['saleList'][$r]->opening_balance_debit=0;
					$this->data['saleList'][$r]->opening_balance_credit=$accountTotalOB['creditTotal'];
				}

			}

			}*/
			//$this->sma->print_arrays($this->data['saleList']);
			$accountEntries = array();
			foreach ($this->data['saleList'] as $sale) {
				$sale_entry = (array) $sale;
				//$this->sma->print_arrays($sale_entry);
				foreach ($this->data['purchaseList'] as $key => $purchase) {
					if ($purchase->customer_ref_id==$sale->customer_id) {
						unset($this->data['purchaseList'][$key]);
					}
				}
			}




			/*$i = isset($this->data['purchaseList']) ? sizeof($this->data['purchaseList']) : 0;
			for ($r = 0; $r < $i; $r++) {
				if($this->data['purchaseList'][$r]){
				$this->data['purchaseList'][$r]->opening_balance_debit=0;
				$this->data['purchaseList'][$r]->opening_balance_credit=0;

				if($show_Opening_balance==1){

				$accountID=$this->site->getAccountByRefId($this->data['purchaseList'][$r]->supplier_id,'local_suppliers')->id;
				//$this->sma->print_arrays($accountID);
				if($accountID==""){
					$accountID=$this->site->getAccountByRefId($this->data['purchaseList'][$r]->supplier_id,'international_suppliers')->id;
					if($accountID==""){
						$this->data['purchaseList'][$r]->opening_balance_debit=0;
						$this->data['purchaseList'][$r]->opening_balance_credit=0;
					}else{
						$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($accountID,'','',$grpId,$coId,$brId);
					}
				}else{
					$accountTotalOB = $this->accounts_model->getAccountTotalDebitCreditOB($accountID,'','',$grpId,$coId,$brId);
				}
				if($accountTotalOB['debitTotal']>0){
					$this->data['purchaseList'][$r]->opening_balance_debit=$accountTotalOB['debitTotal'];
					$this->data['purchaseList'][$r]->opening_balance_credit=0;
				}
				if($accountTotalOB['creditTotal']>0){
					$this->data['purchaseList'][$r]->opening_balance_debit=0;
					$this->data['purchaseList'][$r]->opening_balance_credit=$accountTotalOB['creditTotal'];
				}

			}
			}

			}*/
			//$this->sma->print_arrays($this->data['purchaseList']);
			$this->data['customers_suppliers']      =array_merge($this->data['customers'],$this->data['suppliers']);



		//$this->sma->print_arrays(array_merge($this->data['customers'],$this->data['suppliers']));
		//$this->sma->print_arrays($this->data['purchaseList']);


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		//$this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Debt_and_Liability'), 'page' => lang('Debt_and_Liability')], ['link' => '#', 'page' => lang('Debt_and_Liability')]];
		$meta                = ['page_title' => lang('Debt_and_Liability'), 'bc' => $bc];
		$this->page_construct('accounts/debtliability_index', $meta, $this->data);
	}


	public function debtliability($pdf = null, $xls = null, $start_date = null, $end_date = null, $customer = null)
	{

		//$this->sma->checkPermissions('agedopeninvoices', true);



		ini_set('max_execution_time', 300);




		$levels = 0;


		$grpId     = $this->input->post('grpId') ? $this->input->post('grpId') : null;
		$coId     = $this->input->post('coId') ? $this->input->post('coId') : null;
		$brId     = $this->input->post('brId') ? $this->input->post('brId') : $this->session->userdata('brId');

		$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : $this->accounts_model->getAllAccountingPeriodYearFromEntries();
		//$this->data['period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getAllAccountingPeriodYear() : ['0' => (object)['period_year' => date('Y')]];
			$this->data['max_min_period_year'] = $this->accounts_model->getAllAccountingPeriodYear() ? $this->accounts_model->getMaxMinAccountingPeriodByYear($this->data['period_year'][0]->period_year) : (object)['period_from_date' => $this->data['period_year'][0]->period_year . '-01-01', 'period_to_date' => $this->data['period_year'][0]->period_year . '-12-31'];




		//$start_date   = $this->input->post('start_date') ? $this->input->post('start_date') : $this->sma->hrsd(date('Y-m-d'));
		$end_date     = $this->input->post('end_date') ? $this->input->post('end_date') : $this->sma->hrsd(date('Y-m-d'));
		$customer     = $this->input->post('customer') ? $this->input->post('customer') : '';
		$salesman_id     = $this->input->post('salesman_id') ? $this->input->post('salesman_id') : '';
		$only_difference     = isset($_POST['only_difference'])  ? $_POST['only_difference'] : '0';
		$the_oldest     = isset($_POST['the_oldest'])  ? $_POST['the_oldest'] : '1';



		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0') {
			$effectiveEndDate = (isset($_POST['end_date']) ? $this->sma->fld($_POST['end_date']) : '');
			$effectiveStartDate = date('Y-m-d', strtotime('+1 days', strtotime($effectiveEndDate)));
			$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));



			for ($i = 1; $i <= $_POST['compare_periods']; $i++) {

				//if($i!=$_POST['compare_periods'])
				$start_date = $this->sma->hrsd($effectiveStartDate);


				$effectiveStartDate = date('Y-m-d', strtotime($_POST['compared_to'], strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 years")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 years", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-3 months")
					$effectiveEndDate = date('Y-m-d', strtotime("+3 months", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 weeks")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 weeks", strtotime($effectiveStartDate)));
				if ($_POST['compared_to'] == "-1 days")
					$effectiveEndDate = date('Y-m-d', strtotime("+1 days", strtotime($effectiveStartDate)));
				$effectiveEndDate = date('Y-m-d', strtotime("-1 days", strtotime($effectiveEndDate)));
			}
		} else {
			$start_date = $end_date;

		}



		$check_period=$this->accounts_model->getOpenAccountingPeriodByDate(date('Y-m-d'));
		if($check_period){


		$start_date=$this->sma->hrsd($this->data['max_min_period_year']->period_from_date);




		$start_date = $this->sma->fld($start_date) . ' 00:00:00';
	}else{
		$start_date ='';
	}
		$end_date = $this->sma->fld($end_date) . ' 23:59:59';

//$this->sma->print_arrays($start_date,$end_date);

		if($customer){
			//$customer_acc = $this->site->getAccountByRefId($customer,'local_customers') ? $this->site->getAccountByRefId($customer,'local_customers') :($this->site->getAccountByRefId($customer,'international_customers') ? $this->site->getAccountByRefId($customer,'international_customers') : null);
			$customer_acc = $this->accounts_model->getAccountByID($customer);
			$customer_acc->amount = $this->accounts_model->getAccountTotal($customer_acc->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
			$customer_acc->amountDT = $this->accounts_model->getAccountTotalDT($customer_acc->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
			$customer_acc->amountCR = $this->accounts_model->getAccountTotalCR($customer_acc->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
			$customer_acc->customer = $customer_acc->accountName;
			$customer_acc->customer_id = $customer_acc->account_ref_id;
			if($customer_acc->amount > 0 && isset($customer_acc->account_ref_id)){
					$saleList[] = $customer_acc;
			}else if($customer_acc->amount < 0 && isset($customer_acc->account_ref_id)){
					$parchList[] = $customer_acc;
			}


		}else{
			$accounts_settings   = $this->accounts_model->getSettings();

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountDT = $this->accounts_model->getAccountTotalDT($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountCR = $this->accounts_model->getAccountTotalCR($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($toAccountID->amount > 0 && ($toAccountID->id != $accounts_settings->accounts_local_customers_acct)){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountDT = $this->accounts_model->getAccountTotalDT($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountCR = $this->accounts_model->getAccountTotalCR($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($toAccountID->amount > 0 && ($toAccountID->id != $accounts_settings->accounts_international_customers_acct)){
					$saleList[] = $toAccountID;
				}
			}

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_suppliers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_suppliers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountDT = $this->accounts_model->getAccountTotalDT($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountCR = $this->accounts_model->getAccountTotalCR($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($toAccountID->amount < 0 && ($toAccountID->id != $accounts_settings->accounts_local_suppliers_acct)){
					$parchList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				$toAccountID->amount = $this->accounts_model->getAccountTotal($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountDT = $this->accounts_model->getAccountTotalDT($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->amountCR = $this->accounts_model->getAccountTotalCR($toAccountID->id,(($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0) ? $start_date : $start_date),$end_date,NULL,$grpId,$coId,$brId);
				$toAccountID->customer = $toAccountID->accountName;
				$toAccountID->customer_id = $toAccountID->account_ref_id;
				if($toAccountID->amount < 0 && ($toAccountID->id != $accounts_settings->accounts_international_suppliers_acct)){
					$parchList[] = $toAccountID;
				}
			}
		}


		$this->data['saleList']=$saleList;
		$this->data['parchList']=$parchList;


			//s$this->sma->print_arrays($parchList);







		/*
		if ($_POST['compare_periods'] && $_POST['compared_to'] != '0' && $the_oldest == 0)
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate($start_date, $end_date, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		else
			$this->data['saleList'] = $this->site->getAllSalesDueAggregate(NULL, $end_date, $customer, $not_due, $the_oldest,$salesman_id,$grpId,$coId,$brId);
		*/



		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
        	{

             $this->data['mainGroups'] = $this->site->getMainGroup();
             $this->data['mainCompanies'] = $this->input->post('grpId') ? $this->site->getCompaniesBygrpId($this->input->post('grpId')) : $this->site->getMainCompanies($this->Settings->enable_grp==1 ? null : 1);
             $this->data['mainBranches'] = $this->input->post('coId') ? $this->site->getBranchesBycoId($this->input->post('coId')) : $this->site->getMainBranches(($this->Settings->enable_grp==1 || $this->Settings->enable_co==1) ? null : 1);
			 //$this->data['customers'] = $this->input->post('brId') ? $this->site->getCustomersBybrId($this->input->post('brId')) : $this->site->getCustomersBybrId($this->session->userdata('brId'));




            }else{
				//$this->data['customers']      = $this->site->getAllCompanies('customer');
			}

			$accounts_settings   = $this->accounts_model->getSettings();

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_customers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_customers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_local_customers_acct){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_international_customers_acct){
					$saleList[] = $toAccountID;
				}
			}

			$accountsLocal = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_local_suppliers_acct)->accountID);
			$accountsIner = $this->accounts_model->getAccountChildsWithLevels($this->accounts_model->getAccountByID($accounts_settings->accounts_international_suppliers_acct)->accountID);

			foreach ($accountsLocal as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_local_suppliers_acct){
					$saleList[] = $toAccountID;
				}

			}
			foreach ($accountsIner as $toAccountID) {
				if($toAccountID->id != $accounts_settings->accounts_international_suppliers_acct){
					$saleList[] = $toAccountID;
				}
			}

			$this->data['customers']      = $saleList;

		//$this->sma->print_arrays($start_date,$end_date);
		//$this->sma->print_arrays($this->data['saleList']);


		$this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

		$this->data['allSalesMan'] = $this->site->getAllSalesmanCompanies();
		$bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('Debt_and_Liability'), 'page' => lang('Aged_Open_Invoices')], ['link' => '#', 'page' => lang('Aged_Open_Invoices')]];
		$meta                = ['page_title' => lang('Debt_and_Liability'), 'bc' => $bc];
		$this->page_construct('accounts/debtliability_index', $meta, $this->data);
	}

	public function accounts_log($date = null)
    {
		if($this->session->userdata('isSuper')){
		if (!$date) {
            $date = date('Y-m-d');
        }

        $dbName=str_replace("cpesoft_","",$this->db->database);
        if (!file_exists(APPPATH . 'logs/'.$dbName)) {
            mkdir(APPPATH . 'logs/'.$dbName, 0777, true);
        }

        $file = APPPATH . 'logs/'.$dbName.'/' . 'accounts-' . $date . '.log';
        if (file_exists($file)) {
            $log   = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
            $lines = explode("\n", $log);
            array_walk($lines, function (&$item) {
				if (strpos($item, 'ERROR') !== false) {
                    $item = "<span class='text-danger' style='white-space: normal;'>{$item}</span>";
                } else {
                    $item = "<span style='white-space: normal;'>{$item}</span>";
                }
            });
            $content = implode("\n\n", $lines);
        } else {
            $content = "<span class='text-danger'>" . lang('log_x_exists') . '</span>';
        }

        $this->data['log']   = urldecode($content);
        $this->data['date']  = $date;
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('accounts_log')]];
        $meta                = ['page_title' => lang('accounts_log'), 'bc' => $bc];
        $this->page_construct('accounts/log', $meta, $this->data);
    }else{
		$this->session->set_flashdata('error', lang('access_denied'));
                admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
	}
	}

	public function getMaxBondNoticeCreditor($brId = null)
    {
    if ($rows = $this->accounts_model->getMaxBondNoticeCreditor($brId)) {
        $data = json_encode($rows);
    } else {
        $data = false;
    }
    echo $data;
    }

	public function delete_notice_creditor($id = null)
    {
        $this->sma->checkPermissions('edit_notice_creditor', true);
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
        }

		$receipt = $this->accounts_model->getNoticeCreditorByID($id);
		$check_period = $this->accounts_model->getOpenAccountingPeriodByDate(($receipt->date));
				$ch = 1;
				if ($check_period) {
					if ($check_period->period_status == 1) {
						$ch = 0;
					} else {
						$ch = 1;
					}
				} else {

						$ch = 0;

				}
				if ($ch == 1) {
					$this->sma->send_json(['error' => 1, 'msg' => lang('Transaction_date_outside_the_open_accounting_period')]);

				}

        if ($this->accounts_model->deleteNoticeCreditor($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Notice_creditor_successfully_deleted')]);
        }
    }


	public function getMonthStartsBetween($dateString, $now = "now") {
        $result = [];

        // تحويل التواريخ إلى DateTime
        $startDate = new DateTime($dateString);
        $endDate   = new DateTime($now);

        // تعديل البداية ليكون أول يوم من الشهر التالي
        $startDate->modify('first day of next month');

        // التكرار حتى نهاية الفترة
        while ($startDate <= $endDate) {
            $monthStart = $startDate->format("Y-m-d");

            // نهاية نفس الشهر
            $monthEnd = $startDate->format("Y-m-t");

            $result[] = [
                'start' => $monthStart,
                'end'   => $monthEnd
            ];
            $startDate->modify('first day of next month');
        }

        return $result;
    }

	public function getWeekStartsBetween($dateString, $now = "now") {
        $result = [];

	    // تحويل التواريخ إلى DateTime
    	$startDate = new DateTime($dateString);
	    $endDate   = new DateTime($now);

    	// تعديل البداية لتكون الأحد القادم
	    $startDate->modify('sunday next week');

    	// التكرار حتى نهاية الفترة
	    while ($startDate <= $endDate) {
    	    $weekStart = $startDate->format("Y-m-d");

        	// نهاية الأسبوع (السبت)
	        $weekEndDate = clone $startDate;
    	    $weekEndDate->modify('saturday this week');

        	// إذا نهاية الأسبوع أكبر من التاريخ الحالي، نخليها التاريخ الحالي فقط
	        if ($weekEndDate > $endDate) {
    	        $weekEndDate = clone $endDate;
        	}

	        $result[] = [
    	        'start' => $weekStart,
        	    'end'   => $weekEndDate->format("Y-m-d")
        	];

	        // الانتقال للأحد القادم
    	    $startDate->modify('sunday next week');
    	}

	    return $result;
    	}

	public function getDaysRangesBetween($dateString, $now = "now") {
	    $result = [];

    	// تحويل التواريخ إلى DateTime
	    $startDate = new DateTime($dateString);
    	$endDate   = new DateTime($now);

		$startDate->modify('+1 day');
	    // التكرار يوم بيوم
    	while ($startDate <= $endDate) {
        	$day = $startDate->format("Y-m-d");

	        $result[] = [
    	        'start' => $day . " 00:00:00", // بداية اليوم
        	    'end'   => $day . " 23:59:59"  // نهاية اليوم
	        ];

    	    // الانتقال لليوم التالي
        	$startDate->modify('+1 day');
	    }

    	return $result;
	}

	public function import_csv()
    {
        $this->sma->checkPermissions('add', true);
        $this->load->helper('security');
        $this->form_validation->set_rules('csv_file', lang('upload_file'), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if (isset($_FILES['csv_file'])) {
                $this->load->library('upload');

                $config['upload_path']   = 'files/';
                $config['allowed_types'] = 'csv';
                $config['max_size']      = '2000';
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    admin_redirect('accounts/accounts_tree');
                }

                $csv = $this->upload->file_name;

                $arrResult = [];
                $handle    = fopen('files/' . $csv, 'r');
                if ($handle) {
                    while (($row = fgetcsv($handle, 5001, ',')) !== false) {
                        $row[0]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[0]);
                        $row[1]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[1]);
                        $row[3]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[3]);
                        $row[4]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[4]);
                        $row[5]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[5]);
                        $row[8]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[8]);
                        $row[16]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[16]);
                        $row[17]=iconv("Windows-1256", "UTF-8//TRANSLIT",$row[17]);


                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles         = array_shift($arrResult);
                $rw             = 2;
                $updated        = '';
                $data           = [];
                $customer_group = $this->site->getCustomerGroupByID($this->Settings->customer_group);
                $price_group    = $this->site->getPriceGroupByID($this->Settings->price_group);





                foreach ($arrResult as $key => $value) {
                    $customer = [
                        'company'             => isset($value[0]) ? trim($value[0]) : '',
                        'name'                => isset($value[1]) ? trim($value[1]) : '',
                        'email'               => isset($value[2]) ? trim($value[2]) : '',
                        'phone'               => isset($value[3]) ? trim($value[3]) : '',
                        'address'             => isset($value[4]) ? trim($value[4]) : '',
                        'city'                => isset($value[5]) ? trim($value[5]) : '',
                        'state'               => isset($value[6]) ? trim($value[6]) : '',
                        'postal_code'         => isset($value[7]) ? trim($value[7]) : '',
                        'country'             => isset($value[8]) ? trim($value[8]) : '',
                        'vat_no'              => isset($value[9]) ? trim($value[9]) : '',
                        'gst_no'              => isset($value[10]) ? trim($value[10]) : '',
                        'cf1'                 => isset($value[11]) ? trim($value[11]) : '',
                        'cf2'                 => isset($value[12]) ? trim($value[12]) : '',
                        'cf3'                 => isset($value[13]) ? trim($value[13]) : '',
                        'cf4'                 => isset($value[14]) ? trim($value[14]) : '',
                        'cf5'                 => isset($value[15]) ? trim($value[15]) : '',
                        'companyEn'                 => isset($value[16]) ? trim($value[16]) : '',
                        'nameEn'                 => isset($value[17]) ? trim($value[17]) : '',
                        'is_auto_account'                 => (isset($value[18]) && $value[18]==1 && isset($this->Settings->preAccounting) && $this->Settings->preAccounting==1) ? 1: 0,
                        'co_type'            => (isset($value[19]) && $value[19]=='International') ? 2 : 1,
                        'group_id'            => 3,
                        'group_name'          => 'customer',
                        'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                        'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                        'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                        'price_group_name'    => (!empty($price_group)) ? $price_group->name : null,
                        'code'                 => isset($value[20]) ? trim($value[20]) : '',
                    ];


                    if (empty($customer['company'])){// || empty($customer['name']) || empty($customer['email'])) {
                        //$this->session->set_flashdata('error', lang('company') . ', ' . lang('name') . ', ' . lang('email') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        $this->session->set_flashdata('error', lang('company') . ' ' . lang('are_required') . ' (' . lang('line_no') . ' ' . $rw . ')');
                        admin_redirect('accounts/accounts_tree');
                    } else {

                            $data[] = $customer;
                        $rw++;
                    }
                }
                //$this->sma->print_arrays($data);
                // $this->sma->print_arrays($data, $updated);
            }
        } elseif ($this->input->post('import')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect('accounts/accounts_tree');
        }

        if ($this->form_validation->run() == true && !empty($data)) {
            if ($this->companies_model->addCompaniesV2($data)) {
                $this->session->set_flashdata('message', lang('customers_added') . $updated);
                admin_redirect('accounts/accounts_tree');
            }
        } else {
            if (isset($data) && empty($data)) {
                if ($updated) {
                    $this->session->set_flashdata('message', $updated);
                } else {
                    $this->session->set_flashdata('warning', lang('data_x_customers'));
                }
                admin_redirect('accounts/accounts_tree');
            }

            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'accounts/import', $this->data);
        }
    }



}
