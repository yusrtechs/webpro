<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Accounts_model extends CI_Model
{
	public function getAccountItem()
	{
		$data = [];
		$parent_key = '0';
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersTree($parent_key);
			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}
	public function getAccountItemByLevel($parent_key, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$data = [];
		//$parent_key = '0';
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersTreeByLevel($parent_key, $start_date, $end_date, '1', $grpId, $coId, $brId);
			//$data['start_date'] = $start_date;

			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}

	public function getAccountItemDebitCreditByLevel($parent_key, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$data = [];
		//$parent_key = '0';
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersTreeDebitCreditByLevel($parent_key, $start_date, $end_date, '1', $grpId , $coId , $brId );
			//$data['start_date'] = $start_date;

			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}

	public function getAccountItemDebitCreditByLevelNet($parent_key, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$data = [];
		//$parent_key = '0';
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersTreeDebitCreditByLevelNet($parent_key, $start_date, $end_date, '1', $grpId , $coId , $brId );
			//$data['start_date'] = $start_date;

			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}

	public function getAccountItemDebitCreditByLevelOB($parent_key, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$data = [];
		//$parent_key = '0';
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersTreeDebitCreditByLevelOB($parent_key, $start_date, $end_date, '1', $grpId , $coId , $brId );
			//$data['start_date'] = $start_date;

			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}

	public function getAccountItemDebitCreditByLevelM($parent_key, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$data = [];
		//$parent_key = '0';
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersTreeDebitCreditByLevelM($parent_key, $start_date, $end_date, '1', $grpId , $coId , $brId );
			//$data['start_date'] = $start_date;

			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}

	public function getCostCentersItem()
	{
		$data = [];
		$parent_key = '0';
		$q = $this->db->get('gl_cost_centers');
		if ($q->num_rows() > 0) {
			//foreach (($q->result()) as $row) {
			$data = $this->membersCostCentersTree($parent_key);
			//$data[] = $row;
			//}
			return $data;
		}
		return false;
	}


	public function membersTree($parent_key)
	{
		$row1 = [];
		$this->db->order_by('accountID', 'asc');
		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$accountID = $value['accountID'];

			$row1[$key]['id'] = $value['id'];
			if ($this->Settings->user_language == 'arabic') {

				$row1[$key]['name'] =  $value['accountName'];
				$row1[$key]['accountID'] =  $value['accountID'];
				if ($value['parent_id'] != 0)
					$row1[$key]['text'] =  "<input  type='hidden' class='rid' value='" . $value['id'] . "'><span class='atname'>  " . $value['accountID'] . " - " . $value['accountName'] . " </span> <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a>  <a href='" . admin_url('accounts/edit_account/' . $accountID) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account') . "'><i class=\"fa fa-edit\"></i></a>   <a href='#' onclick='window.location=\"" . admin_url('accounts/delete_account/' . $id) . "\"'  class='tip' title='" . lang('Delete_account') . "'><i class=\"fa fa-trash-o\"></i></a></span>";
				else
					$row1[$key]['text'] = "  " . $value['accountID'] . " - " . $value['accountName'] . "   <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a></span>";
				$row1[$key]['nodes'] = array_values($this->membersTree($value['id']));
			} else {
				$row1[$key]['name'] = ($value['accountNameEn']!='' ? $value['accountNameEn'] : $value['accountName']);
				if ($value['parent_id'] != 0)
					$row1[$key]['text'] =  "<input  type='hidden' class='rid' value='" . $value['id'] . "'><span class='atname'>  " . $value['accountID'] . " - " . ($value['accountNameEn'] ? $value['accountNameEn'] : $value['accountName']). " </span> <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a>  <a href='" . admin_url('accounts/edit_account/' . $accountID) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_account') . "'><i class=\"fa fa-edit\"></i></a>   <a href='#' onclick='window.location=\"" . admin_url('accounts/delete_account/' . $id) . "\"'  class='tip' title='" . lang('Delete_account') . "'><i class=\"fa fa-trash-o\"></i></a></span>";
				else
					$row1[$key]['text'] =  "  " . $value['accountID'] . " - " . $value['accountNameEn'] . "  <span style='padding:20px;'><a href='" . admin_url('accounts/add_account/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_account') . "'><i class=\"fa fa-plus\"></i></a></span>";
				$row1[$key]['nodes'] = array_values($this->membersTree($value['id']));
			}
		}

		return $row1;
	}

	public function membersTreeByLevel($parent_key, $start_date = NULL, $end_date = NULL, $flag = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$row1 = [];
		$row2 = [];
		static $accountBalance = 0;

		if ($flag)
			$accountBalance = 0;
		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$accountID = $value['accountID'];

			$row1[$key]['id'] = $value['id'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['parent_id'] = $value['parent_id'];
			$row1[$key]['accountCategory'] = $value['accountCategory'];
			$total = $this->accounts_model->getAccountTotal($value['id'], $start_date, $end_date,NULL, $grpId , $coId , $brId );
			$row1[$key]['accountBalance'] = 	$total;
			//if($total!=0)
			$accountBalance = $accountBalance + $total;
			$row1[$key]['accountBalance2'] = 	$accountBalance;
			//$row2['start_date'] = 	$start_date;

			$row1[$key]['name'] =  $value['accountName'];
			$row1[$key]['nodes'] = array_values($this->membersTreeByLevel($value['id'], $start_date, $end_date,NULL, $grpId, $coId, $brId));
		}
		$row2['accountBalance'] = 	$accountBalance;

		//$accountBalance=0;
		return $row2;
	}

	public function membersTreeDebitCreditByLevel($parent_key, $start_date = NULL, $end_date = NULL, $flag = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$row1 = [];
		$row2 = [];
		$total = [];
		static $accountBalanceDT = 0;
		static $accountBalanceCR = 0;

		if ($flag) {
			$accountBalanceDT = 0;
			$accountBalanceCR = 0;
		}
		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$accountID = $value['accountID'];

			$row1[$key]['id'] = $value['id'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['parent_id'] = $value['parent_id'];
			$row1[$key]['accountCategory'] = $value['accountCategory'];
			$total = $this->getAccountTotalDebitCredit($value['id'], $start_date, $end_date, $grpId, $coId, $brId);
			$row1[$key]['accountBalanceDT'] = 	$total['debitTotal'];
			$row1[$key]['accountBalanceCR'] = 	$total['creditTotal'];
			//if($total!=0)
			$accountBalanceDT = $accountBalanceDT + $total['debitTotal'];
			$accountBalanceCR = $accountBalanceCR + $total['creditTotal'];
			$row1[$key]['accountBalanceDT2'] = 	$accountBalanceDT;
			$row1[$key]['accountBalanceCR2'] = 	$accountBalanceCR;
			//$row2['start_date'] = 	$start_date;

			$row1[$key]['name'] =  $value['accountName'];
			$row1[$key]['nodes'] = array_values($this->membersTreeDebitCreditByLevel($value['id'], $start_date, $end_date,NULL, $grpId, $coId, $brId));
		}
		$row2['debitTotal'] = 	$accountBalanceDT;
		$row2['creditTotal'] = 	$accountBalanceCR;

		//$accountBalance=0;
		return $row2;
	}


	public function membersTreeDebitCreditByLevelNet($parent_key, $start_date = NULL, $end_date = NULL, $flag = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$row1 = [];
		$row2 = [];
		$total = [];
		static $accountBalanceDT = 0;
		static $accountBalanceCR = 0;

		if ($flag) {
			$accountBalanceDT = 0;
			$accountBalanceCR = 0;
		}
		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$accountID = $value['accountID'];

			$row1[$key]['id'] = $value['id'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['parent_id'] = $value['parent_id'];
			$row1[$key]['accountCategory'] = $value['accountCategory'];
			$total = $this->getAccountTotalDebitCreditNet($value['id'], $start_date, $end_date, $grpId , $coId, $brId);
			$row1[$key]['accountBalanceDT'] = 	$total['debitTotal'];
			$row1[$key]['accountBalanceCR'] = 	$total['creditTotal'];
			//if($total!=0)
			$accountBalanceDT = $accountBalanceDT + $total['debitTotal'];
			$accountBalanceCR = $accountBalanceCR + $total['creditTotal'];
			$row1[$key]['accountBalanceDT2'] = 	$accountBalanceDT;
			$row1[$key]['accountBalanceCR2'] = 	$accountBalanceCR;
			//$row2['start_date'] = 	$start_date;

			$row1[$key]['name'] =  $value['accountName'];
			$row1[$key]['nodes'] = array_values($this->membersTreeDebitCreditByLevelNet($value['id'], $start_date, $end_date,NULL, $grpId, $coId, $brId));
		}
		$row2['debitTotal'] = 	$accountBalanceDT;
		$row2['creditTotal'] = 	$accountBalanceCR;

		//$accountBalance=0;
		return $row2;
	}

	public function membersTreeDebitCreditByLevelOB($parent_key, $start_date = NULL, $end_date = NULL, $flag = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$row1 = [];
		$row2 = [];
		$total = [];
		static $accountBalanceDT = 0;
		static $accountBalanceCR = 0;

		if ($flag) {
			$accountBalanceDT = 0;
			$accountBalanceCR = 0;
		}
		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$accountID = $value['accountID'];

			$row1[$key]['id'] = $value['id'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['parent_id'] = $value['parent_id'];
			$row1[$key]['accountCategory'] = $value['accountCategory'];
			$total = $this->getAccountTotalDebitCreditOB($value['id'], $start_date, $end_date, $grpId, $coId, $brId);
			$row1[$key]['accountBalanceDT'] = 	$total['debitTotal'];
			$row1[$key]['accountBalanceCR'] = 	$total['creditTotal'];
			//if($total!=0)
			$accountBalanceDT = $accountBalanceDT + $total['debitTotal'];
			$accountBalanceCR = $accountBalanceCR + $total['creditTotal'];
			$row1[$key]['accountBalanceDT2'] = 	$accountBalanceDT;
			$row1[$key]['accountBalanceCR2'] = 	$accountBalanceCR;
			//$row2['start_date'] = 	$start_date;

			$row1[$key]['name'] =  $value['accountName'];
			$row1[$key]['nodes'] = array_values($this->membersTreeDebitCreditByLevelOB($value['id'], $start_date, $end_date,NULL, $grpId, $coId, $brId));
		}
		$row2['debitTotal'] = 	$accountBalanceDT;
		$row2['creditTotal'] = 	$accountBalanceCR;

		//$accountBalance=0;
		return $row2;
	}

	public function membersTreeDebitCreditByLevelM($parent_key, $start_date = NULL, $end_date = NULL, $flag = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{
		$row1 = [];
		$row2 = [];
		$total = [];
		static $accountBalanceDT = 0;
		static $accountBalanceCR = 0;

		if ($flag) {
			$accountBalanceDT = 0;
			$accountBalanceCR = 0;
		}
		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$accountID = $value['accountID'];

			$row1[$key]['id'] = $value['id'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['accountID'] = $value['accountID'];
			$row1[$key]['parent_id'] = $value['parent_id'];
			$row1[$key]['accountCategory'] = $value['accountCategory'];
			$total = $this->getAccountTotalDebitCreditM($value['id'], $start_date, $end_date, $grpId, $coId, $brId);
			$row1[$key]['accountBalanceDT'] = 	$total['debitTotal'];
			$row1[$key]['accountBalanceCR'] = 	$total['creditTotal'];
			//if($total!=0)
			$accountBalanceDT = $accountBalanceDT + $total['debitTotal'];
			$accountBalanceCR = $accountBalanceCR + $total['creditTotal'];
			$row1[$key]['accountBalanceDT2'] = 	$accountBalanceDT;
			$row1[$key]['accountBalanceCR2'] = 	$accountBalanceCR;
			//$row2['start_date'] = 	$start_date;

			$row1[$key]['name'] =  $value['accountName'];
			$row1[$key]['nodes'] = array_values($this->membersTreeDebitCreditByLevelM($value['id'], $start_date, $end_date,NULL, $grpId, $coId, $brId));
		}
		$row2['debitTotal'] = 	$accountBalanceDT;
		$row2['creditTotal'] = 	$accountBalanceCR;

		//$accountBalance=0;
		return $row2;
	}

	public function membersCostCentersTree($parent_key)
	{
		$row1 = [];

		$row = $this->db->get_where('gl_cost_centers', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			$costID = $value['costID'];

			$row1[$key]['id'] = $value['id'];
			if ($this->Settings->user_language == 'arabic') {

				$row1[$key]['name'] =  $value['costName'];
				if ($costID == "0") {
					$row1[$key]['text'] = "  " . $value['costName'] . "   <span style='padding:20px;'><a href='" . admin_url('accounts/add_cost_center/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_cost_center') . "'><i class=\"fa fa-plus\"></i></a></span>";
				} else {
					if ($value['parent_id'] != 0)
						$row1[$key]['text'] =  "<input  type='hidden' class='rid' value='" . $value['id'] . "'><span class='catname'>  " . $value['costID'] . " - " . $value['costName'] . " </span> <span style='padding:20px;'><a href='" . admin_url('accounts/add_cost_center/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_cost_center') . "'><i class=\"fa fa-plus\"></i></a>  <a href='" . admin_url('accounts/edit_cost_center/' . $costID) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_cost_center') . "'><i class=\"fa fa-edit\"></i></a>   <a href='#' onclick='window.location=\"" . admin_url('accounts/delete_cost_center/' . $id) . "\"'  class='tip' title='" . lang('Delete_cost_center') . "'><i class=\"fa fa-trash-o\"></i></a></span>";
					else
						$row1[$key]['text'] = "  " . $value['costID'] . " - " . $value['costName'] . "   <span style='padding:20px;'><a href='" . admin_url('accounts/add_cost_center/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_cost_center') . "'><i class=\"fa fa-plus\"></i></a></span>";
				}
				$row1[$key]['nodes'] = array_values($this->membersCostCentersTree($value['id']));
			} else {
				$row1[$key]['name'] = $value['costNameEn'];
				if ($costID == "0") {
					$row1[$key]['text'] =  "  " . $value['costNameEn'] . "  <span style='padding:20px;'><a href='" . admin_url('accounts/add_cost_center/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_cost_center') . "'><i class=\"fa fa-plus\"></i></a></span>";
				} else {
					if ($value['parent_id'] != 0)
						$row1[$key]['text'] =  "<input  type='hidden' class='rid' value='" . $value['id'] . "'><span class='catname'>    " . $value['costID'] . " - " . $value['costNameEn'] . " </span> <span style='padding:20px;'><a href='" . admin_url('accounts/add_cost_center/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_cost_center') . "'><i class=\"fa fa-plus\"></i></a>  <a href='" . admin_url('accounts/edit_cost_center/' . $costID) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Edit_cost_center') . "'><i class=\"fa fa-edit\"></i></a>   <a href='#' onclick='window.location=\"" . admin_url('accounts/delete_cost_center/' . $id) . "\"'  class='tip' title='" . lang('Delete_cost_center') . "'><i class=\"fa fa-trash-o\"></i></a></span>";
					else
						$row1[$key]['text'] =  "  " . $value['costID'] . " - " . $value['costNameEn'] . "  <span style='padding:20px;'><a href='" . admin_url('accounts/add_cost_center/' . $id) . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang('Add_cost_center') . "'><i class=\"fa fa-plus\"></i></a></span>";
				}
				$row1[$key]['nodes'] = array_values($this->membersCostCentersTree($value['id']));
			}
		}

		return $row1;
	}

	public function isMembersTree($parent_key, $root_parent_key, $flage = null)
	{
		$row1 = [];
		if (!$flage)
			$flage = 0;

		$row = $this->db->get_where('gl_accounts', ['parent_id' => $parent_key])->result_array();


		//$row = $this->db->query('SELECT * from sma_gl_accounts WHERE parent_id="'.$parent_key.'"')->result_array();

		foreach ($row as $key => $value) {
			$id = $value['id'];
			if ($root_parent_key == $value['id'])
				$flage = 1;



			if ($flage == 0)
				$this->isMembersTree($value['id'], $root_parent_key, $flage);
		}
		return $flage;
	}
	public function addAccountType($data)
	{
		if ($this->db->insert('gl_accounts_categories', $data)) {
			$accountID = $this->db->insert_id();
			$userID = $this->session->userdata('user_id');
			$this->db->insert('gl_accounts', ['accountID' => $accountID, 'accountNameEn' => $data['categoryNameEn'], 'accountName' => $data['categoryName'], 'accountCategory' => $data['categoryName'], 'userID' => $userID]);

			return true;
		}
		return false;
	}

	public function addAccount($data)
	{
		if ($this->db->insert('gl_accounts', $data)) {

			return true;
		}
		return false;
	}

	public function addCostCenter($data)
	{
		if ($this->db->insert('gl_cost_centers', $data)) {

			return true;
		}
		return false;
	}

	public function addPaymentType($data)
	{
		if ($this->db->insert('gl_payment_methods', $data)) {

			return true;
		}
		return false;
	}

	public function addAccountingPeriod($periods = [])
	{
		$insert_status = 0;
		foreach ($periods as $period) {

			if ($this->db->insert('gl_accounting_period', $period)) {
				$insert_status = 1;
			}
		}
		return $insert_status;
	}


	public function closeAccountingPeriod($period_year)
	{
		if ($this->db->update('gl_accounting_period', ['period_status' => 0], ['period_year' => $period_year])) {

			return true;
		}
		return false;
	}

	public function getAccountTypeByID($categoryID)
	{
		$q = $this->db->get_where('gl_accounts_categories', ['categoryID' => $categoryID], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


	public function getAccountingPeriodByID($id)
	{
		$q = $this->db->get_where('gl_accounting_period', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAllAccountingPeriod()
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get('gl_accounting_period');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getCountAccountingPeriodByYear($period_year = NULL, $period_year_status = NULL, $period_status = NULL)
	{
		if ($period_year) {
			$this->db->where('period_year', $period_year);
		}
		if ($period_year_status) {
			$this->db->where('period_year_status', $period_year_status);
		}
		if ($period_status) {
			if ($period_status == "NOT CLOSED")
				$this->db->where('period_status !=', 0);
			else
				$this->db->where('period_status', $period_status);
		}
		$this->db->select('(count(*)) as count');
		$q = $this->db->get('gl_accounting_period');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


	public function getOpenAccountingPeriodByDate($date)
	{
		$this->db->where(" date('" . $date . "') BETWEEN period_from_date and period_to_date");
		//$this->db->where(" '2022-05-12 21:50' BETWEEN period_from_date and period_to_date");
		$q = $this->db->get('gl_accounting_period', 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAccountingPeriodBetween2Ids($id1, $id2)
	{

		//$this->db->where('id >=', $id1);
		$this->db->where(" id BETWEEN '" . $id1 . "' and '" . $id2 . "'");
		$q = $this->db->get('gl_accounting_period');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getMaxMinAccountingPeriodByYear($period_year)
	{
		$this->db->select('Min(period_from_date) as period_from_date,Max(period_to_date) as period_to_date');
		$q = $this->db->get_where('gl_accounting_period', ['period_year' => $period_year], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAllOpenAccountingPeriodByYear($period_year)
	{
		$q = $this->db->get_where('gl_accounting_period', ['period_year' => $period_year, 'period_status' => '1'], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAllAccountingPeriodByYear($period_year)
	{
		$this->db->order_by('id', 'asc');

		$q = $this->db->get_where('gl_accounting_period', ['period_year' => $period_year]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	public function getAllAccountingPeriodYearFromEntries()
	{
		//$this->db->select('period_year');
		$this->db->distinct();
		$this->db->select('Year(entryDate) as period_year');
		$this->db->order_by('Year(entryDate)', 'desc');

		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAllAccountingPeriodYear()
	{
		//$this->db->select('period_year');
		$this->db->distinct();
		$this->db->select('period_year');
		$this->db->order_by('period_year', 'desc');

		$q = $this->db->get('gl_accounting_period');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function getAllAccountingPeriodYearActive()
	{
		//$this->db->select('period_year');
		$this->db->distinct();
		$this->db->select('period_year');
		$this->db->where('period_year_status', 1);

		$this->db->order_by('period_year', 'desc');

		$q = $this->db->get('gl_accounting_period');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}



	public function getPaymentTypeByID($id)
	{
		$q = $this->db->get_where('gl_payment_methods', ['id' => $id], 1);

		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getPaymentTypeByPaymentType($payment_type)
	{
		$q = $this->db->get_where('gl_payment_methods', ['payment_type' => $payment_type], 1);

		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}




	public function getPaymentTypeViewPaymentAccountByID($id)
	{

		$this->db->select('gl_payment_methods.*,gl_accounts.accountName as accounts_name, gl_accounts.accountNameEn as accounts_nameEn')
			->join('gl_accounts', 'gl_accounts.id=gl_payment_methods.payment_account', 'left');

		$this->db->where('gl_payment_methods.id', $id);

		$q = $this->db->get('gl_payment_methods');

		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getPaymentTypeViewCommissonAccountByID($id)
	{

		$this->db->select('gl_payment_methods.*,gl_accounts.accountName as accounts_name, gl_accounts.accountNameEn as accounts_nameEn')
			->join('gl_accounts', 'gl_accounts.id=gl_payment_methods.commisson_account', 'left');

		$this->db->where('gl_payment_methods.id', $id);

		$q = $this->db->get('gl_payment_methods');

		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function updateAccountType($categoryID, $data)
	{
		if ($this->db->update('gl_accounts_categories', $data, ['categoryID' => $categoryID])) {

			$this->db->update('gl_accounts', ['accountNameEn' => $data['categoryNameEn'], 'accountName' => $data['categoryName'], 'accountCategory' => $data['categoryName']], ['accountID' => $categoryID]);
			return true;
		}
		return false;
	}

	public function updatePaymentType($id, $data)
	{
		if ($this->db->update('gl_payment_methods', $data, ['id' => $id])) {

			return true;
		}
		return false;
	}


	public function updateAccount($accountID, $data)
	{
		if ($this->db->update('gl_accounts', $data, ['accountID' => $accountID])) {

			return true;
		}
		return false;
	}

	public function updateCostCenter($costID, $data)
	{
		if ($this->db->update('gl_cost_centers', $data, ['costID' => $costID])) {

			return true;
		}
		return false;
	}


	public function getCountAccountByID($id)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('gl_accounts', ['parent_id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getCountCostCentersByID($id)
	{
		$this->db->select('(count(*)+1) as count');
		$q = $this->db->get_where('gl_cost_centers', ['parent_id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAccountByID($id)
	{
		$q = $this->db->get_where('gl_accounts', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


	public function getCostCentersByID($id)
	{
		$q = $this->db->get_where('gl_cost_centers', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}
	public function getAllNoMastersCostCenters()
	{
		$this->db->where('costID!=', 0);
		$this->db->where('master_cost', 0);

		$q = $this->db->get('gl_cost_centers');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getCostCenters($id = null)
	{
		//$this->db->order_by('accountID', 'asc');
		$this->db->where('costID!=', 0);

		if ($id) {
			$this->db->where('id', $id);
		}
		$q = $this->db->get('gl_cost_centers');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAccounts($id = null, $levels = null, $select = null, $byID = false)
	{
		//$this->db->order_by('accountID', 'asc');
		$this->db->where('accountStatus', 1);
		if ($id) {
			$this->db->where('id', $id);
		}
		if ($levels) {
			$this->db->where('level <=', $levels);
		}
		if (!is_null($select))
			$q = $this->db->select($select);

		$q = $this->db->get('gl_accounts');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($byID)
					$data[$row->id] = $row;
				else
					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAccountsV2($id = null, $levels = null, $select = null, $byID = false)
	{
		$this->db->order_by('accountID', 'asc');
		$this->db->where('accountStatus', 1);
		if ($id) {
			$this->db->where('id', $id);
		}
		if ($levels) {
			$this->db->where('level <=', $levels);
		}
		if (!is_null($select))
			$q = $this->db->select($select);


		$q = $this->db->get('gl_accounts');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($byID)
					$data[$row->id] = $row;
				else
					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAccountsV3($id = null, $levels = null, $select = null, $byID = false)
	{
		$this->db->order_by('accountID', 'asc');
		$this->db->where('accountStatus', 1);
		if ($id) {
			$this->db->where('id', $id);
		}
		if ($levels) {
			$this->db->where('level', $levels);
		}
		if (!is_null($select))
			$q = $this->db->select($select);


		$q = $this->db->get('gl_accounts');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($byID)
					$data[$row->id] = $row;
				else
					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAccountsReOrder($id = null)
	{

		//$this->db->order_by('LENGTH(accountID)', 'asc');
		$this->db->order_by('parent_id,id', 'asc');


		if ($id) {
			$this->db->where('id', $id);
		}
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getCostCentersReOrder($id = null)
	{

		//$this->db->order_by('LENGTH(accountID)', 'asc');
		$this->db->order_by('parent_id,id', 'asc');


		if ($id) {
			$this->db->where('id', $id);
		}
		$q = $this->db->get('gl_cost_centers');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function getAccountChilds($accountID, $levels = NULL, $accountStatement = NULL)
	{
		if (strlen($accountID) > 2) {
			//$this->db->where('(accountID LIKE ', $accountID . "0%", FALSE)
			//$this->db->where(" (accountID = '" . $accountID . "')", NULL, FALSE);
			$this->db->where("  ( (accountID LIKE '" . $accountID . "0%') OR (accountID = '" . $accountID . "') ) ");
			//$this->db->where("  ( (accountID = '" . $accountID . "') ) ");


		} else {

			$this->db->like('accountID', $accountID, 'after');
		}
		if ($levels) {
			$this->db->where('LENGTH(accountID) <=', $levels);
		}
		if ($accountStatement) {
			$this->db->where('accountStatement', $accountStatement);
		}

		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAccountChildsGuide($accountID, $levels = NULL, $accountStatement = NULL)
	{
		if (strlen($accountID) > 2) {
			//$this->db->where('(accountID LIKE ', $accountID . "0%", FALSE)
			//$this->db->where(" (accountID = '" . $accountID . "')", NULL, FALSE);
			$this->db->where("  ( (accountID LIKE '" . $accountID . "0%') OR (accountID = '" . $accountID . "') ) ");
			//$this->db->where("  ( (accountID = '" . $accountID . "') ) ");


		} else {

			$this->db->like('accountID', $accountID, 'after');
		}
		if ($levels) {
			$this->db->where('level <=', $levels);
		}
		if ($accountStatement) {
			$this->db->where('accountStatement', $accountStatement);
		}

		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAccountChildsWithLevels($accountID, $levels = NULL, $accountStatement = NULL)
	{
		$accountLevel=$this->getAccountByAccountID($accountID)->level;
		if (strlen($accountID) > 2) {
			//$this->db->where('(accountID LIKE ', $accountID . "0%", FALSE)
			//$this->db->where(" (accountID = '" . $accountID . "')", NULL, FALSE);

			//$this->db->where("  ( (accountID LIKE '" . $accountID . "0%') OR (accountID = '" . $accountID . "') ) and (LENGTH(accountID)=LENGTH('" . $accountID . "') or LENGTH(accountID)>=LENGTH('" . $accountID . "')+2) ");
			$this->db->where("  ( (accountID LIKE '" . $accountID . "0%' and level !='" . $accountLevel . "') OR (accountID = '" . $accountID . "') ) and (LENGTH(accountID)=LENGTH('" . $accountID . "') or LENGTH(accountID)>=LENGTH('" . $accountID . "')+2) ");


			//$this->db->where("  ( (accountID = '" . $accountID . "') ) ");
			//$this->db->where('level <', 5);

		} else {

			$this->db->like('accountID', $accountID, 'after');

		}

		if ($levels) {
			$this->db->where('level <=', $levels);
		}
		if ($accountStatement) {
			$this->db->where('accountStatement', $accountStatement);
		}
		//$this->db->order_by('accountCredit', 'desc');


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function isAccountChildsWithLevels($accountID, $accountID2, $levels = NULL)
	{
		if (strlen($accountID) > 2) {
			//$this->db->where('(accountID LIKE ', $accountID . "0%", FALSE)
			//$this->db->where(" (accountID = '" . $accountID . "')", NULL, FALSE);
			$this->db->where("  ( (accountID LIKE '" . $accountID . "0%') OR (accountID = '" . $accountID . "') ) ");
			//$this->db->where("  ( (accountID = '" . $accountID . "') ) ");


		} else {

			$this->db->like('accountID', $accountID, 'after');
		}
		if ($levels) {
			$this->db->where('level <=', $levels);
		}

		//$this->db->order_by('accountCredit', 'desc');


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($accountID2 == $row->accountID)
					return true;
			}
			return $data;
		}
		return false;
	}


	public function getCostCenterChilds($costID, $parent_id = NULL)
	{
		/*
		if($parent_id!=1){
			//$this->db->like('accountID', $accountID."0", 'after');
			//$this->db->where('accountID', $accountID);
			//$this->db->where('LENGTH(accountID) <=', $levels);

			$this->db->where("  ( (costID LIKE '" . $costID . "0%' and LENGTH(costID)>2) OR costID = '" . $costID . "' ) ");

		}else{
			$this->db->like('costID', $costID, 'after');

		}*/
		$this->db->where("  ( (costID LIKE '" . $costID . "0%' and LENGTH(costID)>2) OR costID = '" . $costID . "' ) ");

		/*if(strlen($costID)>2){
			//$this->db->like('accountID', $accountID."0", 'after');
			//$this->db->where('accountID', $accountID);
			$this->db->where("  ( costID LIKE '" . $costID . "0%' OR costID = '" . $costID . "' ) ");


		}else{
			$this->db->like('costID', $costID, 'after');
		}
		*/
		$q = $this->db->get('gl_cost_centers');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	public function getAccountChildsIncomeStatement($accountID, $levels = NULL)
	{

		$sales_acct = $this->site->getAccountsSettings()->sales_acct;
		$sales_return_acct = $this->site->getAccountsSettings()->sales_return_acct;
		$sales_cost_of_good_sold_acct = $this->site->getAccountsSettings()->sales_cost_of_good_sold_acct;

		$this->db->where('accountStatement', 'Income Statement');
		$this->db->where('id !=', $sales_acct);
		$this->db->where('id !=', $sales_return_acct);

		$this->db->where('id !=', $sales_cost_of_good_sold_acct);

		if (strlen($accountID) > 2) {
			//$this->db->like('accountID', $accountID."0", 'after');
			$this->db->where("  ( accountID LIKE '" . $accountID . "0%' OR accountID = '" . $accountID . "' ) ");
		} else {
			$this->db->like('accountID', $accountID, 'after');
		}

		//$this->db->like('accountID', $accountID, 'after');
		if ($levels) {
			$this->db->where('LENGTH(accountID) <=', $levels);
		}

		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}
	public function getOtherRevenuesAccounts($levels = NULL)
	{

		//$this->db->like('accountID', $accountID, 'after');
		$sales_acct = $this->site->getAccountsSettings()->sales_acct;
		$sales_acct_parent_arr = $this->accounts_model->getAccounts($sales_acct)[0];
		$sales_acct_parent_id = $sales_acct_parent_arr->parent_id;

		$sales_return_acct = $this->site->getAccountsSettings()->sales_return_acct;
		$sales_return_acct_parent_arr = $this->accounts_model->getAccounts($sales_return_acct)[0];
		$sales_return_acct_parent_id = $sales_return_acct_parent_arr->parent_id;
		$this->db->where('accountStatement', 'Income Statement');
		$this->db->where('accountCategory', 'الإيرادات');
		$this->db->where('id !=', $sales_acct);
		$this->db->where('id !=', $sales_acct_parent_id);
		$this->db->where('id !=', $sales_return_acct);
		$this->db->where('id !=', $sales_return_acct_parent_id);
		if ($levels) {
			//$this->db->where('LENGTH(accountID) <=', $levels);
			$this->db->where('level <=', $levels);
		}

		//$this->db->where('id !=', $this->site->getAccountsSettings()->);
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getOtherAssetsAccounts()
	{
		//$this->db->like('accountID', $accountID, 'after');
		$accounts_fixed_assets_acct = $this->site->getAccountsSettings()->accounts_fixed_assets_acct;
		$fixed_assets_acct = $this->getAccountByID($accounts_fixed_assets_acct);
		$accounts_current_assets_acct = $this->site->getAccountsSettings()->accounts_current_assets_acct;
		$current_assets_acct = $this->getAccountByID($accounts_current_assets_acct);
		$this->db->where('accountStatement', 'Balance Sheet');
		$this->db->where('accountCategory', 'اصول');
		//$this->db->where('id !=', $sales_acct);
		//$this->db->where('id !=', $sales_return_acct);

		if (strlen($fixed_assets_acct->accountID) > 2) {
			//$this->db->not_like('accountID', $fixed_assets_acct->accountID."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $fixed_assets_acct->$accountID . "0%' OR accountID != '" . $fixed_assets_acct->accountID . "' ) ");
			//$this->db->where("  ( accountID LIKE '" . $accountID . "0%' OR accountID = '" . $accountID . "' ) ");

		} else {
			$this->db->not_like('accountID', $fixed_assets_acct->accountID, 'after');
		}

		if (strlen($current_assets_acct->accountID) > 2) {
			//$this->db->not_like('accountID', $current_assets_acct->accountID."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $current_assets_acct->$accountID . "0%' OR accountID != '" . $current_assets_acct->accountID . "' ) ");
		} else {
			$this->db->not_like('accountID', $current_assets_acct->accountID, 'after');
		}

		//$this->db->not_like('accountID', $fixed_assets_acct->accountID, 'after');
		//$this->db->not_like('accountID', $current_assets_acct->accountID, 'after');

		//$this->db->where('id !=', $this->site->getAccountsSettings()->);
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getOtherExpensesAccounts($accountID1, $accountID2, $levels = NULL,$period_year = NULL)
	{
		if($period_year==2022)
		{

		}else if ($this->Settings->accounting_type == 'periodic') {

			$purchases_acct = $this->site->getAccountsSettings()->purchases_acct;
			//$purchases_acct_parent_arr=$this->accounts_model->getAccounts($purchases_acct)[0];
			//$purchases_acct_parent_id=$purchases_acct_parent_arr->parent_id;
			$purchases_return_acct = $this->site->getAccountsSettings()->purchases_return_acct;
		}
		/*else*/ {

			$sales_cost_of_good_sold_acct = $this->site->getAccountsSettings()->sales_cost_of_good_sold_acct;
			$sales_cost_of_good_sold_acct_parent_arr = $this->accounts_model->getAccounts($sales_cost_of_good_sold_acct)[0];
			$sales_cost_of_good_sold_acct_parent_id = $sales_cost_of_good_sold_acct_parent_arr->parent_id;
		}
		if (strlen($accountID1) > 2) {
			//$this->db->not_like('accountID', $accountID1."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $accountID1 . "0%' OR accountID != '" . $accountID1 . "' ) ");
			//$this->db->where("  ( accountID LIKE '" . $accountID . "0%' OR accountID = '" . $accountID . "' ) ");


		} else {
			$this->db->not_like('accountID', $accountID1, 'after');
		}

		if (strlen($accountID2) > 2) {
			//$this->db->not_like('accountID', $accountID2."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $accountID2 . "0%' OR accountID != '" . $accountID2 . "' ) ");
		} else {
			$this->db->not_like('accountID', $accountID2, 'after');
		}

		//$this->db->not_like('accountID', $accountID1, 'after');
		//$this->db->not_like('accountID', $accountID2, 'after');
		$this->db->where('accountStatement', 'Income Statement');
		$this->db->where('accountCategory', 'المصروفات');

		if($period_year==2022)
		{

		}else if ($this->Settings->accounting_type == 'periodic') {
			$this->db->where('id !=', $purchases_acct);
			$this->db->where('id !=', $purchases_return_acct);
		}
		/*else*/ {
			$this->db->where('id !=', $sales_cost_of_good_sold_acct);
			//if($sales_cost_of_good_sold_acct_parent_arr->level=="3")
			$this->db->where('id !=', $sales_cost_of_good_sold_acct_parent_id);
		}
		if ($levels) {
			//$this->db->where('LENGTH(accountID) <=', $levels);
			$this->db->where('level <=', $levels);
		}
		//$this->db->where('id !=', $sales_return_acct);
		//$this->db->where('id !=', $this->site->getAccountsSettings()->);
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getincomeStatementAccounts($id = null, $levels = null)
	{
		if ($id) {
			$this->db->where('id', $id);
		}
		if ($levels) {
			//$this->db->where('LENGTH(accountID) <=', $levels);
			$this->db->where('level <=', $levels);
		}

		$this->db->order_by('id', 'desc');

		$this->db->where('accountStatement', 'Income Statement');


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function getbalanceSheetAccounts($id = null, $accountSide = NULL)
	{
		if ($id) {
			$this->db->where('id', $id);
		}
		if ($accountSide) {
			//$this->db->where('accountSide!=', );
			$this->db->where('accountSide is not null');
		}
		//$this->db->order_by('id', 'desc');

		//$this->db->where('accountStatement', 'Balance Sheet');
		//$this->db->where('accountCategory', 'Balance Sheet');
		$this->db->where(" (accountCategory = 'اصول' OR accountCategory = 'الإلتزامات وحقوق الملكية') ");



		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}



	public function getbalanceSheetAccountsLiabilities($id = null, $levels = null)
	{
		if ($id) {
			$this->db->where('id', $id);
		}
		$this->db->where('accountStatement', 'Balance Sheet');
		$this->db->where('accountCategory', 'الإلتزامات وحقوق الملكية');

		if ($levels) {
			$this->db->where('level <=', $levels);
		}
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}



	public function getAccountByAccountID($accountID)
	{
		$q = $this->db->get_where('gl_accounts', ['accountID' => $accountID], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getCostCenterByCostCenterID($costID)
	{
		$q = $this->db->get_where('gl_cost_centers', ['costID' => $costID], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


	public function IsAccountHaveChildsByID($id)
	{

		$q = $this->db->get_where('gl_accounts', ['parent_id' => $id], 1);
		if ($q->num_rows() > 0) {
			return true;
		}
		return false;
	}

	public function IsAccountHaveOperationsByID($id)
	{
		$this->db->select("COALESCE(accountDebit,0) as accountDebit,COALESCE(accountCredit,0) as accountCredit,COALESCE(openingBalance,0) as openingBalance");
		$q = $this->db->get_where('gl_accounts', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			if ($q->row()->accountDebit != 0 || $q->row()->accountCredit != 0 || $q->row()->openingBalance != 0)
				return true;
		}
		return false;
	}

	public function IsCostCenterHaveChildsByID($id)
	{

		$q = $this->db->get_where('gl_cost_centers', ['parent_id' => $id], 1);
		if ($q->num_rows() > 0) {
			return true;
		}
		return false;
	}


	public function deleteAccount($id)
	{
		$acc = $this->getAccountByID($id);
		if ($this->db->delete('gl_accounts', ['id' => $id])) {
			$accp = $this->getAccountByID($acc->parent_id);

			$this->reOrderAccounts($acc->parent_id, $accp->accountID);


			return true;
		}

		return false;
	}

	public function deleteAccountingPeriod()
	{
		if ($this->db->truncate('gl_accounting_period')) {

			return true;
		}

		return false;
	}

	public function deleteCostCenter($id)
	{
		$acc = $this->getCostCentersByID($id);
		if ($this->db->delete('gl_cost_centers', ['id' => $id])) {
			$accp = $this->getCostCentersByID($acc->parent_id);

			$this->reOrderCostCenters($acc->parent_id, $accp->costID);


			return true;
		}

		return false;
	}


	public function reOrderAccounts($parent_id, $accountID)
	{
		$this->db->order_by('id', 'asc');
		$q = $this->db->get_where('gl_accounts', ['parent_id' => $parent_id]);
		$i = 1;
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				if (strlen($accountID) <= 1)
					$this->db->update('gl_accounts', ['accountID' => $accountID . "" . $i], ['id' => $row->id]);
				else
					$this->db->update('gl_accounts', ['accountID' => $accountID . "0" . $i], ['id' => $row->id]);

				$i++;
			}
			return true;
		}

		return false;
	}

	public function reOrderAccounts_temp($parent_id, $accountID)
	{
		$this->db->order_by('id', 'asc');
		$q = $this->db->get_where('gl_accounts', ['parent_id' => $parent_id]);
		$i = 1;
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				if (strlen($accountID) <= 1)
					$this->db->update('gl_accounts', ['accountID' => $this->getAccountByID($row->parent_id)->accountID . "" . $i], ['id' => $row->id]);
				else
					$this->db->update('gl_accounts', ['accountID' => $this->getAccountByID($row->parent_id)->accountID . "0" . $i], ['id' => $row->id]);

				$i++;
			}
			return true;
		}

		return false;
	}

	public function reOrderCostCenters($parent_id, $costID)
	{
		$this->db->order_by('id', 'asc');
		$q = $this->db->get_where('gl_cost_centers', ['parent_id' => $parent_id]);
		$i = 1;
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				if (strlen($costID) <= 1)
					$this->db->update('gl_cost_centers', ['costID' => $costID . "" . $i], ['id' => $row->id]);
				else
					$this->db->update('gl_cost_centers', ['costID' => $costID . "0" . $i], ['id' => $row->id]);
				$i++;
			}
			return true;
		}

		return false;
	}
	public function reOrderCostCenters_temp($parent_id, $costID)
	{
		$this->db->order_by('id', 'asc');
		$q = $this->db->get_where('gl_cost_centers', ['parent_id' => $parent_id]);
		$i = 1;
		if ($q->num_rows() > 0) {
			foreach ($q->result() as $row) {
				if ($costID == 0)
					$this->db->update('gl_cost_centers', ['costID' => $i], ['id' => $row->id]);
				else
					$this->db->update('gl_cost_centers', ['costID' => $this->getCostCentersByID($row->parent_id)->costID . "0" . $i], ['id' => $row->id]);

				$i++;
			}
			return true;
		}

		return false;
	}

	public function getAllAccountsForOB($brId = NULL)
	{
		$this->db->order_by('id', 'asc');

		$this->db->where('parent_id!=', 0);
		$this->db->where('master_account', 0);
		$this->db->where('accountStatement', 'Balance Sheet');
		if($brId){
			$this->db->where(" ( id not in (select accountID from ". $this->db->dbprefix('gl_opening_balance_items')." where brId=".$brId.") )");


			}else{
				$this->db->where('openingBalance', 0);
			}



		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function addOpeningBalance($data, $acounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId = NULL, $costCenterCreditId = NULL, $costCenterDebitAmount = NULL, $costCenterCreditAmount = NULL,$grpId = NULL,$coId = NULL,$brId = NULL)
	{
		if ($this->db->insert('gl_opening_balance', $data)) {
			$openingId = $this->db->insert_id();
			foreach ($acounts as $acount) {
				$acount['openingId'] = $openingId;
				$this->db->insert('gl_opening_balance_items', $acount);
				$this->db->update('gl_accounts', ['openingBalance' => $acount['balance']], ['id' => $acount['accountID']]);
			}


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $openingId, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $data['openingDate'],'',$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

			}else{


				$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $openingId, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $data['openingDate'],'',null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
			}



			return true;
		}
		return false;
	}


	public function getOpeningBalanceByID($id)
	{
		$q = $this->db->get_where('gl_opening_balance', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getOpeningBalanceItems($openingId)
	{
		$this->db->select('gl_opening_balance_items.*,gl_accounts.accountSide,gl_accounts.id as account_id , gl_accounts.accountID as account_code, gl_accounts.accountName as accounts_name, gl_accounts.accountNameEn as accounts_nameEn')
			->join('gl_accounts', 'gl_accounts.id=gl_opening_balance_items.accountID', 'left');
		$this->db->order_by('id', 'asc');

		$this->db->where('openingId', $openingId);

		$q = $this->db->get('gl_opening_balance_items');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getQASuggestions($term, $brId = NULL, $limit = 5)
	{
		$this->db->select('' . $this->db->dbprefix('gl_accounts') . '.id, accountID,accountSide, ' . $this->db->dbprefix('gl_accounts') . '.accountName as accountName, ' . $this->db->dbprefix('gl_accounts') . '.accountNameEn as accountNameEn')
			->where(" "
				. '(' . $this->db->dbprefix('gl_accounts') . ".accountName LIKE '%" . $term . "%' OR accountID LIKE '%" . $term . "%' OR  " . $this->db->dbprefix('gl_accounts') . ".accountNameEn LIKE '%" . $term . "%' OR
                concat(" . $this->db->dbprefix('gl_accounts') . ".accountName, ' (', accountID, ')') LIKE '%" . $term . "%')")

			->limit($limit);
		$this->db->where('parent_id!=', 0);
		$this->db->where('master_account', 0);
		$this->db->where('accountStatement', 'Balance Sheet');
		if($brId){
		$this->db->where(" ( id not in (select accountID from ". $this->db->dbprefix('gl_opening_balance_items')." where brId=".$brId.") )");


		}else{
			$this->db->where('openingBalance', 0);
		}



		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function reverseOpeningBalance($id)
	{
		if ($OBs = $this->getOpeningBalanceItems($id)) {
			foreach ($OBs as $ob) {

				$this->db->update('gl_accounts', ['openingBalance' => 0], ['id' => $ob->accountID]);
			}
		}
	}


	public function deleteOpeningBalance($id)
	{
		$this->reverseOpeningBalance($id);
		if ($this->db->delete('gl_opening_balance', ['id' => $id]) && $this->db->delete('gl_opening_balance_items', ['openingId' => $id])) {

			return true;
		}
		return false;
	}

	public function deletePaymentMethods($id)
	{
		if ($this->db->delete('gl_payment_methods', ['id' => $id])) {

			return true;
		}
		return false;
	}


	public function updateOpeningBalance($id, $data, $accounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId = NULL, $costCenterCreditId = NULL, $costCenterDebitAmount = NULL, $costCenterCreditAmount = NULL,$grpId = NULL,$coId = NULL,$brId = NULL)
	{
		$this->reverseOpeningBalance($id);
		if ($this->db->update('gl_opening_balance', $data, ['id' => $id]) && $this->db->delete('gl_opening_balance_items', ['openingId' => $id])) {
			foreach ($accounts as $account) {
				$account['openingId'] = $id;
				$this->db->insert('gl_opening_balance_items', $account);
				$this->db->update('gl_accounts', ['openingBalance' => $account['balance']], ['id' => $account['accountID']]);
			}

			if ($this->site->getEntriesByReference($id, 'Opening_balance')) {




				$this->site->updateEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $id, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount,null,null, $data['openingDate'],$data['currency_code'],$data['currency_rate']);


				//$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$id,'Opening_balance');

			} else {


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $openingId, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $data['openingDate'],'',$data['grpId'],$data['coId'],$data['brId'],null,null,$data['currency_code'],$data['currency_rate']);

			}else{


				$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $id, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $data['openingDate'],'',null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
			}
				//$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$id,'Opening_balance');

			}
			return true;
		}
		return false;
	}

	public function getAllDetailsAccounts()
	{
		$this->db->order_by('id', 'asc');

		$this->db->where('parent_id!=', 0);
		$this->db->where('master_account', 0);
		$this->db->where('accountStatus', 1);
		//$this->db->where('accountStatement', 'Balance Sheet');
		//$this->db->where('openingBalance', 0);


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($this->Settings->user_language != 'arabic' && $row->accountNameEn){
					$row->accountName = str_replace("'","",$row->accountNameEn);
				}else{
					$row->accountName = str_replace("'","",$row->accountName);
				}
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAllMastersAccounts($id = null)
	{
		if ($id) {
			$this->db->where('id', $id);
		}
		$this->db->order_by('id', 'asc');

		$this->db->where('parent_id!=', 0);
		$this->db->where('master_account', 1);
		$this->db->where('accountStatus', 1);
		//$this->db->where('accountStatement', 'Balance Sheet');
		//$this->db->where('openingBalance', 0);


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($this->Settings->user_language != 'arabic' && $row->accountNameEn)
					$row->accountName = $row->accountNameEn;
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAllMastersAccountsWithRoot($id = null)
	{
		if ($id) {
			$this->db->where('id', $id);
		}
		$this->db->order_by('id', 'asc');

		//$this->db->where('parent_id!=', 0);
		//$this->db->where(" (master_account = 1 OR id =1) ");

		$this->db->where('master_account', 1);
		//$this->db->where('accountStatement', 'Balance Sheet');
		//$this->db->where('openingBalance', 0);


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($this->Settings->user_language != 'arabic' && $row->accountNameEn)
					$row->accountName = $row->accountNameEn;
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getMaxBondreceipt($brId=null)
	{
		$this->db->select("LPAD(count(*)+1, 8, '0') as bond_no");
		$this->db->like('bond_no', '00', 'both');
		if ($brId) {
			$this->db->where('brId', $brId);
		}
		$q = $this->db->get('gl_receipts');
		if ($q->num_rows() > 0) {
			if ($brId) {
				return $this->site->getMainBranchesByID($brId)->branch_prefix.'/'.$q->row()->bond_no;
			}else{
				return $q->row()->bond_no;
			}
		}
		return false;
	}




	public function getMaxBondCatchreceipt($brId=null)
	{
		//$this->db->select("LPAD(COALESCE(max(id+1),1), 8, '0') as bond_no");

		$this->db->select("LPAD(count(*)+1, 8, '0') as bond_no");
		$this->db->like('bond_no', '00', 'both');
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$q = $this->db->get('gl_catch_receipts');
		if ($q->num_rows() > 0) {
			if ($brId) {
				return $this->site->getMainBranchesByID($brId)->branch_prefix.'/'.$q->row()->bond_no;
			}else{
				return $q->row()->bond_no;
			}
		}
		return false;
	}


	public function getMaxBondRefundingBond($brId=null)
	{
		//$this->db->select("LPAD(COALESCE(max(id+1),1), 8, '0') as bond_no");

		$this->db->select("LPAD(count(*)+1, 8, '0') as bond_no");
		$this->db->like('bond_no', '00', 'both');
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$q = $this->db->get('gl_refunding_bonds');
		if ($q->num_rows() > 0) {
			if ($brId) {
				return $this->site->getMainBranchesByID($brId)->branch_prefix.'/'.$q->row()->bond_no;
			}else{
				return $q->row()->bond_no;
			}
		}
		return false;
	}



	public function addReceipt($data)
	{



		$costCenterDebitId = $data['costCenterDebitId'];
		$costCenterDebitAmount = $data['costCenterDebitAmount'];
		/*if ($data['tax_amount'] > 0) {
			$costCenterDebitId[] = "";
			$costCenterDebitAmount[] = "";
		}*/
		$costCenterCreditId = $data['costCenterCreditId'];
		$costCenterCreditAmount = $data['costCenterCreditAmount'];

		$entryDebitAccount = $data['entryDebitAccount'];
		$entryDebitBalance = $data['entryDebitBalance'];
		$equivalent_entryDebitBalance = $data['equivalent_entryDebitBalance'];

		$entryCreditAccount = $data['entryCreditAccount'];
		$entryCreditBalance = $data['entryCreditBalance'];
		$equivalent_entryCreditBalance = $data['equivalent_entryCreditBalance'];

		$entryDebitDescription = $data['entryDebitDescription'];
		$entryCreditDescription = $data['entryCreditDescription'];


		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);
		$data['entryDebitAccount'] = json_encode((array) $data['entryDebitAccount']);
		$data['entryDebitBalance'] = json_encode((array) $data['entryDebitBalance']);
		$data['equivalent_entryDebitBalance'] = json_encode((array) $data['equivalent_entryDebitBalance']);
		$data['entryCreditAccount'] = json_encode((array) $data['entryCreditAccount']);
		$data['entryCreditBalance'] = json_encode((array) $data['entryCreditBalance']);
		$data['equivalent_entryCreditBalance'] = json_encode((array) $data['equivalent_entryCreditBalance']);
		$data['entryDebitDescription'] = json_encode((array) $data['entryDebitDescription']);
		$data['entryCreditDescription'] = json_encode((array) $data['entryCreditDescription']);

		if ($this->db->insert('gl_receipts', $data)) {
			$entryDescription="Receipt_Bond_No#".$data['bond_no'];

			/*//$entryCreditAccount= [];
			$entryDebitAccount = [];

			//$entryDebitAccount[] =  $data['toAccountID'];
			$entryDebitAccount[] =  $data['entryDebitAccount'];

			//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
			if ($data['tax_amount'] > 0)
				$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

			//$entryCreditBalance= [];
			$entryDebitBalance = [];
			$entryDebitBalance[] =  $data['entryDebitBalance'];//$this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);
*/





				if ($this->Settings->accounting){
					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {
						$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $equivalent_entryDebitBalance, $equivalent_entryCreditBalance, $data['bond_no'], 'Receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'], $entryDescription, $data['grpId'], $data['coId'], $data['brId'], $entryDebitDescription, $entryCreditDescription,$data['currency_code'],$data['currency_rate']);
					}else{
						$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $equivalent_entryDebitBalance, $equivalent_entryCreditBalance, $data['bond_no'], 'Receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'], $entryDescription, null,null,null, $entryDebitDescription, $entryCreditDescription,$data['currency_code'],$data['currency_rate']);
					}
					//$this->createEntry($entryDebitAccount, $data['fromAccountID'], $entryDebitBalance, $data['equivalent_total_amount'], $data['bond_no'], 'Receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date']);
				}
			return true;
		}
		return false;
	}

	public function addCatchReceipt($data,$paid_by = NULL,$custBalance = NULL,$amountDiff = NULL)
	{
		$costCenterCreditId = $data['costCenterCreditId'];
		$costCenterCreditAmount = $data['costCenterCreditAmount'];
		/*if ($data['tax_amount'] > 0) {
			$costCenterCreditId[] = "";
			$costCenterCreditAmount[] = "";
		}*/
		$costCenterDebitId = $data['costCenterDebitId'];
		$costCenterDebitAmount = $data['costCenterDebitAmount'];

		$entryCreditAccount = $data['entryCreditAccount'];
		$entryCreditBalance = $data['entryCreditBalance'];
		$equivalent_entryCreditBalance = $data['equivalent_entryCreditBalance'];

		$entryDebitAccount = $data['entryDebitAccount'];
		$entryDebitBalance = $data['entryDebitBalance'];
		$equivalent_entryDebitBalance = $data['equivalent_entryDebitBalance'];

		$entryDebitDescription = $data['entryDebitDescription'];
		$entryCreditDescription = $data['entryCreditDescription'];

		$invoices = $data['invoices'];
		$payments = $data['payments'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);
		$data['entryDebitAccount'] = json_encode((array) $data['entryDebitAccount']);
		$data['entryDebitBalance'] = json_encode((array) $data['entryDebitBalance']);
		$data['equivalent_entryDebitBalance'] = json_encode((array) $data['equivalent_entryDebitBalance']);
		$data['entryCreditAccount'] = json_encode((array) $data['entryCreditAccount']);
		$data['entryCreditBalance'] = json_encode((array) $data['entryCreditBalance']);
		$data['equivalent_entryCreditBalance'] = json_encode((array) $data['equivalent_entryCreditBalance']);
		$data['entryDebitDescription'] = json_encode((array) $data['entryDebitDescription']);
		$data['entryCreditDescription'] = json_encode((array) $data['entryCreditDescription']);

		$data['invoices'] = json_encode((array) $data['invoices']);
		$data['payments'] = json_encode((array) $data['payments']);


		if ($this->db->insert('gl_catch_receipts', $data)) {
			$catch_receipt_id=$this->db->insert_id();
			if($data['inv_no']  && strpos($data['inv_no'], ',') === false){


				$sale=$this->site->getSaleByID($data['inv_no']);
				$payment = [
					'date'         => $data['date'],
					'sale_id'      => $data['inv_no'],
					'reference_no' => $data['reference_no'] ? $data['reference_no'] : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $sale->brId : null),
					'amount'       => $custBalance,
					'paid_by'      => $paid_by,
					'note'         => $data['note'],
					'currency'         => $data['currency_code'],
					'currency_rate'         => $data['currency_rate'],
					'created_by'   => $data['created_by'],
					'type'         => 'received',
					'catch_receipt_id'       => $catch_receipt_id,


				 ];



					if ($this->db->insert('payments', $payment)) {
						if ($this->site->getReference('pay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
							$this->site->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
						}
						$this->site->syncSalePayments($data['inv_no']);


					}


					//$amountDiff=25;
						if($amountDiff<0){
							$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
							$equivalent_entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}

						if($amountDiff>0){
							$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
							$equivalent_entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
						}




			}

			if(strpos($data['inv_no'], ',') !== false){
				$i=0;
				foreach ($invoices as $inv_no){

				$sale=$this->site->getSaleByID($inv_no);
				$payment = [
					'date'         => $data['date'],
					'sale_id'      => $inv_no,
					'reference_no' => $data['reference_no'] ? $data['reference_no'] : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $sale->brId : null),
					'amount'       => $payments[$i],
					'paid_by'      => $paid_by,
					'note'         => $data['note'],
					'currency'         => $data['currency_code'],
					'currency_rate'         => $data['currency_rate'],
					'created_by'   => $data['created_by'],
					'type'         => 'received',
					'catch_receipt_id'       => $catch_receipt_id,


				 ];



					if ($this->db->insert('payments', $payment)) {
						if ($this->site->getReference('pay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
							$this->site->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
						}
						$this->site->syncSalePayments($inv_no);


					}
					$i++;

				}


			}

			if($data['the_oldest']==1){
				$i=0;
				foreach ($invoices as $inv_no){

				$sale=$this->site->getSaleByID($inv_no);
				$payment = [
					'date'         => $data['date'],
					'sale_id'      => $inv_no,
					'reference_no' => $data['reference_no'] ? $data['reference_no'] : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $sale->brId : null),
					'amount'       => $payments[$i],
					'paid_by'      => $paid_by,
					'note'         => $data['note'],
					'currency'         => $data['currency_code'],
					'currency_rate'         => $data['currency_rate'],
					'created_by'   => $data['created_by'],
					'type'         => 'received',
					'catch_receipt_id'       => $catch_receipt_id,


				 ];



					if ($this->db->insert('payments', $payment)) {
						if ($this->site->getReference('pay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
							$this->site->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
						}
						$this->site->syncSalePayments($inv_no);


					}
					$i++;

				}


			}

			$entryDescription="Catch_receipt_Bond_No#".$data['bond_no'];
			/*$entryCreditAccount = [];

			$entryCreditAccount[] =  $data['toAccountID'];

			if ($data['tax_amount'] > 0)
				$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

			$entryCreditBalance = [];
			$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);*/


				if ($this->Settings->accounting){


					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {
						$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $equivalent_entryDebitBalance, $equivalent_entryCreditBalance, $data['bond_no'], 'Catch_receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'],$entryDescription, $data['grpId'], $data['coId'], $data['brId'], $entryDebitDescription, $entryCreditDescription,$data['currency_code'],$data['currency_rate']);
					}else{
						$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $equivalent_entryDebitBalance, $equivalent_entryCreditBalance, $data['bond_no'], 'Catch_receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'],$entryDescription, null,null,null, $entryDebitDescription, $entryCreditDescription,$data['currency_code'],$data['currency_rate']);
					}
				}
			return true;
		}
		return false;
	}

	public function addRefundingBond($data)
	{
		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		if ($this->db->insert('gl_refunding_bonds', $data)) {
			$entryDescription="Refunding_Bond_No#".$data['bond_no'];
			if ($this->Settings->accounting){
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {
						$this->site->createEntryMultiSide($data['toAccountID'], $data['fromAccountID'], $data['equivalent_total_amount'], $data['equivalent_total_amount'], $data['bond_no'], 'Refunding_bonds', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'],$entryDescription, $data['grpId'], $data['coId'], $data['brId'],$data['note'],$data['note'],$data['currency_code'],$data['currency_rate']);
					}else{
						$this->site->createEntryMultiSide($data['toAccountID'], $data['fromAccountID'], $data['equivalent_total_amount'], $data['equivalent_total_amount'], $data['bond_no'], 'Refunding_bonds', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'],$entryDescription,null,null,null,$data['note'],$data['note'],$data['currency_code'],$data['currency_rate']);
					}
			}
			return true;
		}
		return false;
	}

	public function updateReceipt($id, $data)
	{
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];
		if ($data['tax_amount'] > 0) {
			$costCenterDebitId[] = "";
			$costCenterDebitAmount[] = "";
		}
		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		if ($this->db->update('gl_receipts', $data, ['id' => $id])) {

			$entryDebitAccount = [];

			$entryDebitAccount[] =  $data['toAccountID'];

			//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
			if ($data['tax_amount'] > 0)
				$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

			//$entryCreditBalance= [];
			$entryDebitBalance = [];
			$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);

				if ($this->Settings->accounting)
					$this->site->updateEntryMultiSide($entryDebitAccount, $data['fromAccountID'], $entryDebitBalance, $data['equivalent_total_amount'], $data['bond_no'], 'Receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount));

			return true;
		}
		return false;
	}

	public function updateCatchReceipt($id, $data)
	{

		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];
		if ($data['tax_amount'] > 0) {
			$costCenterCreditId[] = "";
			$costCenterCreditAmount[] = "";
		}
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		if ($this->db->update('gl_catch_receipts', $data, ['id' => $id])) {

			$entryCreditAccount = [];

			$entryCreditAccount[] =  $data['toAccountID'];

			if ($data['tax_amount'] > 0)
				$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

			$entryCreditBalance = [];
			$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);

				if ($this->Settings->accounting)
					$this->site->updateEntryMultiSide($data['fromAccountID'], $entryCreditAccount, $data['equivalent_total_amount'], $entryCreditBalance, $data['bond_no'], 'Catch_receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount));

			return true;
		}
		return false;
	}


	public function updateRefundingBond($id, $data,$old_bond_no = NULL)
	{

		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		if ($this->db->update('gl_refunding_bonds', $data, ['id' => $id])) {
			if ($this->Settings->accounting){
				if($old_bond_no){
					$entryDescription="Refunding_Bond_No#".$data['bond_no'];
					$this->db->update('gl_entries', ['reference_no' => $data['bond_no'],'entryDescription' => $entryDescription], ['reference_no' => $old_bond_no,'entryType' => 'Refunding_bonds']);
				}
				$this->site->updateEntryMultiSide($data['toAccountID'], $data['fromAccountID'], $data['equivalent_total_amount'], $data['equivalent_total_amount'], $data['bond_no'], 'Refunding_bonds', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount),$data['note'],$data['note'],$data['date'],$data['currency_code'],$data['currency_rate']);
			}


			return true;
		}
		return false;
	}



	public function getReceiptByID($id)
	{
		$q = $this->db->get_where('gl_receipts', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getCatchReceiptByID($id)
	{
		$q = $this->db->get_where('gl_catch_receipts', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


	public function getRefundingBondByID($id)
	{
		$q = $this->db->get_where('gl_refunding_bonds', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getAccountsSettings()
	{
		$q = $this->db->get('gl_accounts_settings');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function updateSetting($data)
	{
		$this->db->where('id', '1');
		if ($this->db->update('gl_accounts_settings', $data)) {
			return true;
		}
		return false;
	}

	public function getSettings()
	{
		$q = $this->db->get('gl_accounts_settings');
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getEntries($entryID = NULL, $startDate = NULL, $endDate = NULL, $entryStatus = NULL, $entryType = NULL, $entryTypeClose = false, $entryYear = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		$this->db->order_by('entryID', 'DEsc');

		if ($startDate && $endDate) {
			//$this->db->where(" entryCreateDate BETWEEN DATE('{$startDate}') AND DATE('{$endDate}')");
			//$this->db->where(" entryCreateDate > ".DATE($startDate));
			//$this->db->where('entryDate >', DATE($startDate));
			//$this->db->where('entryDate <=', DATE($endDate));
			$this->db->where('entryDate BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}

		if ($entryStatus == "yes") {
			$this->db->where('entryStatus', 1);
		}
		if ($entryStatus == "no") {
			$this->db->where('entryStatus', 0);
		}
		if ($entryType == "Adjusting") {
			$this->db->where('entryType', 'Adjusting');
		}
		if ($entryType == "Recurring_entry") {
			$this->db->where('entryType', 'Recurring_entry');
		}
		if ($entryType == "Automated_Accounting_Entry") {
			$this->db->where('entryType !=', 'Adjusting');
		}
		if (!$entryTypeClose) {
			$this->db->where('entryType !=', 'Fiscal_year_closing');
		}


		if ($entryYear) {
			$this->db->where('Year(entryDate)', $entryYear);
		}

		if ($entryID) {
			$this->db->where('entryID', $entryID);
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
			$this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}


		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getEntriesByDate($startDate = NULL, $endDate = NULL)
	{

		$this->db->order_by('entryID', 'DEsc');
		if ($startDate && $endDate) {
			//$this->db->where(" entryCreateDate BETWEEN DATE('{$startDate}') AND DATE('{$endDate}')");
			//$this->db->where(" entryCreateDate > ".DATE($startDate));
			//$this->db->where('entryCreateDate >=', DATE($startDate));
			//$this->db->where(' entryCreateDate <', DATE($endDate));
			$this->db->where('entryDate BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}




		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}



	public function getEntriesApproved($entryID = NULL, $show_Opening_balance = NULL, $start_date = NULL, $end_date = NULL,$grpId = NULL, $coId = NULL, $brId = NULL,$currency_code = NULL)
	{

		$this->db->order_by('entryDate', 'asc');
		if ($entryID) {
			$this->db->where('entryID', $entryID);
		}
		if ($show_Opening_balance == 0) {
			$this->db->where('entryType !=', 'Opening_balance');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		if ($currency_code) {
			$this->db->where('currency_code', $currency_code);
		}

		$this->db->where('entryStatus', 1);

		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}



		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getEntriesByDateApproved_org($startDate = NULL, $endDate = NULL, $show_Opening_balance = NULL,$grpId = NULL, $coId = NULL, $brId = NULL,$currency_code = NULL)
	{

		$this->db->order_by('entryDate', 'asc');
		if ($startDate && $endDate) {
			$this->db->where('entryDate BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		if ($currency_code) {
			$this->db->where('currency_code', $currency_code);
		}

		if ($show_Opening_balance == 0) {
			$this->db->where('entryType !=', 'Opening_balance');
		}

		$this->db->where('entryStatus', 1);

		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getEntriesByDateApproved($startDate = NULL, $endDate = NULL, $show_Opening_balance = NULL,$grpId = NULL, $coId = NULL, $brId = NULL,$currency_code = NULL)
	{
    	$this->db->order_by('entryDate', 'asc');

	    if ($startDate && $endDate) {

    		$this->db->group_start();

	        $this->db->group_start();
    	        $this->db->where('entryDate BETWEEN "' . $startDate . '" and "' . $endDate . '"');
	            $this->db->where('entryType !=', 'Opening_balance');
    	    $this->db->group_end();

        	if ($show_Opening_balance == 1) {
            	$this->db->or_group_start();
					$this->db->where('entryDate BETWEEN "' . $startDate . '" and "' . $endDate . '"');
                	$this->db->where('entryType', 'Opening_balance');
	                $this->db->where('entryDate', $startDate);
    	        $this->db->group_end();
        	}

			$this->db->group_end();
		}

		if ($grpId) {
        	$this->db->where('grpId', $grpId);
    	}

    	if ($coId) {
        	$this->db->where('coId', $coId);
    	}

    	if ($brId) {
        	$this->db->where('brId', $brId);
    	}

    	if ($currency_code) {
        	$this->db->where('currency_code', $currency_code);
    	}

    	$this->db->where('entryStatus', 1);

    	$q = $this->db->get('gl_entries');

    	if ($q->num_rows() > 0) {
        	foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
    	}

    	return false;
	}


	public function createEntryRecurring($clause){
			if ($this->db->insert('gl_entries', $clause)){
				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
					$this->site->updateReference('gl_enrty',$clause  ['brId']);
				}
				return true;
			}
			return false;
	}

	public function updateEntryRecurring($entryID,$latest_recurring_date){
			return $this->db->update('gl_entries', ['latest_recurring_date' => $latest_recurring_date], ['entryID' => $entryID]);
	}


	public function createEntry($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $entryType, $entryDescription, $entryDate, $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $entryDebitDescription, $entryCreditDescription, $entryStatusComment = NULL,$grpId = NULL,$coId = NULL,$brId = NULL,$timePeriod = NULL,$start_date = NULL,$end_date = NULL,$latest_recurring_date = NULL)
	{
		$entryDebitAccount = json_encode((array) $entryDebitAccount);
		$entryCreditAccount = json_encode((array) $entryCreditAccount);
		$entryDebitBalance = json_encode((array) $entryDebitBalance);
		$entryCreditBalance = json_encode((array) $entryCreditBalance);
		$entryDebitDescription = json_encode((array) $entryDebitDescription);
		$entryCreditDescription = json_encode((array) $entryCreditDescription);
		if($entryDebitAccount=="[]" || $entryCreditAccount=="[]" || $entryDebitBalance=="[]" || $entryCreditBalance=="[]"){
			return false;
		}
		$clause = ['created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryDate' => $entryDate, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 0, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription];
		if ($entryStatusComment)
			$clause['entryStatusComment'] = $entryStatusComment;

			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
                $clause  ['grpId']=$grpId ? $grpId : 1;
                $clause  ['coId']=$coId ? $coId : 1;
                $clause  ['brId']=$brId ? $brId : 1;
                $brId=$brId ? $brId : 1;
                $clause  ['entry_no']=$this->site->getReference('gl_enrty',$brId ? $brId  : null);//$this->site->getMainBranchesByID($brId)->branch_prefix ? $this->site->getMainBranchesByID($brId)->branch_prefix.'-'.$this->site->getCountEntriesBybrid($brId)->count : $this->site->getCountEntriesBybrid($brId)->count;
            }

			if($entryType=='Recurring_entry'){
				$clause['timePeriod'] = $timePeriod;
				$clause['start_date'] = $start_date;
				$clause['end_date'] = $end_date;
				$clause['latest_recurring_date'] = $latest_recurring_date;
			}


		if($this->db->insert('gl_entries', $clause)){
			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
				$this->site->updateReference('gl_enrty',$brId ? $brId  : null);
				}

			return true;
		}
		return false;
	}

	public function updateEntry($entryID, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $entryType, $entryDescription, $entryDate, $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $entryDebitDescription, $entryCreditDescription, $entryStatusComment = NULL)
	{
		$entryDebitAccount = json_encode((array) $entryDebitAccount);
		$entryCreditAccount = json_encode((array) $entryCreditAccount);
		$entryDebitBalance = json_encode((array) $entryDebitBalance);
		$entryCreditBalance = json_encode((array) $entryCreditBalance);
		$entryDebitDescription = json_encode((array) $entryDebitDescription);
		$entryCreditDescription = json_encode((array) $entryCreditDescription);
		if($entryDebitAccount=="[]" || $entryCreditAccount=="[]" || $entryDebitBalance=="[]" || $entryCreditBalance=="[]"){
			return false;
		}
		//$clause = ['created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryDate' => $entryDate, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 0, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription];
		$clause = ['created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryDate' => $entryDate, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 0, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'entryDebitDescription' => $entryDebitDescription, 'entryCreditDescription' => $entryCreditDescription];
		if ($entryStatusComment)
			$clause['entryStatusComment'] = $entryStatusComment;

		return $this->db->update('gl_entries', $clause, ['entryID' => $entryID]);
	}



	public function getMaxEntryIDEntry()
	{
		$this->db->select("COALESCE(max(entryID+1),1) as entryID");

		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			return $q->row()->entryID;
		}
		return false;
	}

	public function getEntryByEntryID($entryID)
	{
		$q = $this->db->get_where('gl_entries', ['entryID' => $entryID], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getEntryItems($entryID)
	{
		$this->db->select('gl_opening_balance_items.*,gl_accounts.accountSide,gl_accounts.id as account_id , gl_accounts.accountID as account_code, gl_accounts.accountName as accounts_name, gl_accounts.accountNameEn as accounts_nameEn')
			->join('gl_accounts', 'gl_accounts.id=gl_opening_balance_items.accountID', 'left');
		$this->db->order_by('id', 'asc');

		$this->db->where('openingId', $openingId);

		$q = $this->db->get('gl_opening_balance_items');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function updateStatus($entryID, $entryStatus, $entryDescription)
	{
		//if ($this->db->update('gl_entries', ['entryStatus' => $entryStatus,'entryDescription' => $entryDescription], ['entryID' => $entryID])) {

		if ($entryStatus == 1) {
			$this->approve($entryID, $entryDescription);
			try {
				$this->sma->write_log('INFO', 'The status of entry has been successfully updated (UpdateaStatus:Accounts_model.php)','accounts','EntryID ('.$entryID.') Status ('.$entryStatus.') Description '.$entryDescription);
			} catch (Exception $e) {
			}


			return true;
		} else {
			$this->unapprove($entryID, $entryDescription);
			try {
				$this->sma->write_log('INFO', 'The status of entry has been successfully updated (UpdateaStatus:Accounts_model.php)','accounts','EntryID ('.$entryID.') Status ('.$entryStatus.') Description '.$entryDescription);
			} catch (Exception $e) {
			}

			//$this->db->update('gl_entries', ['entryStatus' => $entryStatus,'entryDescription' => $entryDescription], ['entryID' => $entryID]);

			return true;
		}
		return false;
	}

	public function updatePeriodStatus($id, $period_status)
	{
		if ($this->db->update('gl_accounting_period', ['period_status' => $period_status], ['id' => $id])) {
			return true;
		}
		return false;
	}

	public function updatePeriodYearStatus($period_year, $period_year_status)
	{
		if ($this->db->update('gl_accounting_period', ['period_year_status' => $period_year_status], ['period_year' => $period_year])) {
			return true;
		}
		return false;
	}


	public function approve($entryID, $entryDescription)
	{
		//$data['userData'] = $this->session->userdata();
		$entryInfo        = (array) $this->getEntries($entryID)[0];


		// 	if ($data['userData']['userRole'] < 10){
		// 		$this->session->set_flashdata('danger', 'You do not have this permission.');
		// 		redirect('entries');
		// 	}

		// elseif ($data['userData']['userRole'] !=0){
		// 		$this->session->set_flashdata('danger', 'You do not have this permission.');
		// 		redirect('entries');
		// 	}



		$entryDebitAccounts = array();
		$entryDebitBalances = 0;

		$entryCreditAccounts = array();
		$entryCreditBalances = 0;

		foreach (json_decode($entryInfo['entryDebitAccount']) as $debitAccount) {
			array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entryInfo['entryDebitBalance'])[$entryDebitBalances]));
			$entryDebitBalances += 1;
		}

		foreach (json_decode($entryInfo['entryCreditAccount']) as $creditAccount) {
			array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entryInfo['entryCreditBalance'])[$entryCreditBalances]));
			$entryCreditBalances += 1;
		}

		$this->approveEntry($entryID, $entryDebitAccounts, $entryCreditAccounts, $entryDescription);



		return false;
	}


	public function unapprove($entryID, $entryDescription)
	{
		//$data['userData'] = $this->session->userdata();
		$entryInfo        = (array) $this->getEntries($entryID)[0];


		// 	if ($data['userData']['userRole'] < 10){
		// 		$this->session->set_flashdata('danger', 'You do not have this permission.');
		// 		redirect('entries');
		// 	}

		// elseif ($data['userData']['userRole'] !=0){
		// 		$this->session->set_flashdata('danger', 'You do not have this permission.');
		// 		redirect('entries');
		// 	}



		$entryDebitAccounts = array();
		$entryDebitBalances = 0;

		$entryCreditAccounts = array();
		$entryCreditBalances = 0;

		foreach (json_decode($entryInfo['entryDebitAccount']) as $debitAccount) {
			array_push($entryDebitAccounts, array((string) $debitAccount => json_decode($entryInfo['entryDebitBalance'])[$entryDebitBalances]));
			$entryDebitBalances += 1;
		}

		foreach (json_decode($entryInfo['entryCreditAccount']) as $creditAccount) {
			array_push($entryCreditAccounts, array((string) $creditAccount => json_decode($entryInfo['entryCreditBalance'])[$entryCreditBalances]));
			$entryCreditBalances += 1;
		}

		$this->unapproveEntry($entryID, $entryDebitAccounts, $entryCreditAccounts, $entryDescription);



		return false;
	}



	public function approveEntry($entryID, $debitAccounts, $creditAccounts, $entryDescription)
	{
		$entryInfo        = (array) $this->getEntries($entryID)[0];

		foreach ($debitAccounts as $account) {
			$balance = $account[key($account)];
			$account = key($account);

			$accountDebitInfo    = (array) $this->getAccounts($account)[0];

			$accountDebitBalance = $accountDebitInfo['accountDebit'];

			$accountInfo = array('accountDebit' => ($balance + $accountDebitBalance));
			$this->db->where('id', $account);
			$this->db->update('gl_accounts', $accountInfo);
		}

		foreach ($creditAccounts as $account) {
			$balance = $account[key($account)];
			$account = key($account);

			$accountCreditInfo    = (array) $this->getAccounts($account)[0];
			$accountCreditBalance = $accountCreditInfo['accountCredit'];

			$accountInfo = array('accountCredit' => ($balance + $accountCreditBalance));
			$this->db->where('id', $account);
			$this->db->update('gl_accounts', $accountInfo);
		}

		$entryInfo = array('entryStatus' => 1, 'entryDescription' => $entryDescription);
		$this->db->where('entryID', $entryID);
		$this->db->update('gl_entries', $entryInfo);

		return true;
	}




	public function unapproveEntry($entryID, $debitAccounts, $creditAccounts, $entryDescription)
	{
		$entryInfo        = (array) $this->getEntries($entryID)[0];

		foreach ($debitAccounts as $account) {
			$balance = $account[key($account)];
			$account = key($account);

			$accountDebitInfo    = (array) $this->getAccounts($account)[0];

			$accountDebitBalance = $accountDebitInfo['accountDebit'];

			$accountInfo = array('accountDebit' => ($accountDebitBalance - $balance));
			$this->db->where('id', $account);
			$this->db->update('gl_accounts', $accountInfo);
		}

		foreach ($creditAccounts as $account) {
			$balance = $account[key($account)];
			$account = key($account);

			$accountCreditInfo    = (array) $this->getAccounts($account)[0];
			$accountCreditBalance = $accountCreditInfo['accountCredit'];

			$accountInfo = array('accountCredit' => ($accountCreditBalance - $balance));
			$this->db->where('id', $account);
			$this->db->update('gl_accounts', $accountInfo);
		}

		$entryInfo = array('entryStatus' => 0, 'entryDescription' => $entryDescription);
		$this->db->where('entryID', $entryID);
		$this->db->update('gl_entries', $entryInfo);

		return true;
	}



	public function getAccountTotalDebitCredit($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$this->db->select('entryDebitAccount,entryDebitBalance,entryCreditAccount,entryCreditBalance');
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}



		$q = $this->db->get_where('gl_entries', ['entryStatus' => 1]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}

	public function getAccountTotalDebitCredit_test_index($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{


		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		//$this->db->where('entryStatus', 1);

		$q = $this->db->select('entryDebitAccount,entryDebitBalance,entryCreditAccount,entryCreditBalance')->from('sma_gl_entries USE INDEX(sma_gl_entries_dt_cr_IDX)')->where('entryStatus', 1)->get();
		//$q = $this->db->get();

		//$q = $this->db->get_where('gl_entries', ['entryStatus' => 1]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}



	public function getAccountTotalDebitCreditNet($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{


		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}



		$q = $this->db->get_where('gl_entries', ['entryStatus' => 1]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		if ($debitTotal > $creditTotal) {
			$debitTotal = $debitTotal - $creditTotal;
			$creditTotal = 0;
		} else if ($debitTotal < $creditTotal) {
			$creditTotal = $creditTotal - $debitTotal;
			$debitTotal = 0;
		} else {
			$debitTotal = 0;
			$creditTotal = 0;
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}





	public function getAccountTotalDebitCreditM($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{


		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$this->db->where('entryType !=', 'Opening_balance');

		$q = $this->db->get_where('gl_entries', ['entryStatus' => 1]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}



	public function getAccountTotalDebitCreditOB($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{


		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$this->db->where('entryType', 'Opening_balance');

		$q = $this->db->get_where('gl_entries', ['entryStatus' => 1]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		if ($debitTotal > $creditTotal) {
			$debitTotal = $debitTotal - $creditTotal;
			$creditTotal = 0;
		} else if ($debitTotal < $creditTotal) {
			$creditTotal = $creditTotal - $debitTotal;
			$debitTotal = 0;
		} else {
			$debitTotal = 0;
			$creditTotal = 0;
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}













	public function getAccountTotal($id, $start_date = NULL, $end_date = NULL, $levels = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$getSQL = "SELECT * FROM entries WHERE( DATE(entryCreateDate) BETWEEN '{$startDate}' AND '{$endDate}') AND entryStatus=1";
		//$queryDB = $this->db->query($getSQL);
		//$accountEntries = $queryDB->result();

		//$this->db->where(" (accountCategory = 'الإيرادات' OR accountCategory = 'المصروفات') ");



		$this->db->where('entryStatus', 1);
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}else if ($end_date) {
			$this->db->where('entryDate <= "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$q = $this->db->get('gl_entries');

		//$q = $this->db->get_where('gl_entries', ['entryStatus' => 1, 'DATE(entryCreateDate)' => $startDate, 'DATE(entryCreateDate)' <= $endDate]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			//if (in_array($id, $debitAccounts)){
			$acctDF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $debitAccounts)) {
						$acctDF = 1;
					}
				}
			}
			if ($acctDF == 1 || in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {

					if ($levels) {
						if ((substr($this->getAccountByID($debitAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					} else {
						if ($debitAccount == $id) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					}
					$i++;
				}
			}


			$creditAccounts = json_decode($entry->entryCreditAccount);
			//if (in_array($id, $creditAccounts)){
			//if (in_array($id, $creditAccounts)){
			$acctCF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $creditAccounts)) {
						$acctCF = 1;
					}
				}
			}
			if ($acctCF == 1 || in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($levels) {
						if ((substr($this->getAccountByID($creditAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					} else {
						if ($creditAccount == $id) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					}
					$i++;
				}
			}
		}

		// switch($accountID){
		// 	# Assets
		// 	case $this->startsWith($accountID, '100'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// 	# Liabilities
		// 	case $this->startsWith($accountID, '200'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Owners Equity
		// 	case $this->startsWith($accountID, '300'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Revenues
		// 	case $this->startsWith($accountID, '400'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Operating Expenses
		// 	case $this->startsWith($accountID, '500'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// }

		$total = $debitTotal - $creditTotal;

		//$total = $creditTotal;
		return $total;
	}



	public function getAccountTotalDT($id, $start_date = NULL, $end_date = NULL, $levels = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$getSQL = "SELECT * FROM entries WHERE( DATE(entryCreateDate) BETWEEN '{$startDate}' AND '{$endDate}') AND entryStatus=1";
		//$queryDB = $this->db->query($getSQL);
		//$accountEntries = $queryDB->result();

		//$this->db->where(" (accountCategory = 'الإيرادات' OR accountCategory = 'المصروفات') ");



		$this->db->where('entryStatus', 1);
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$q = $this->db->get('gl_entries');

		//$q = $this->db->get_where('gl_entries', ['entryStatus' => 1, 'DATE(entryCreateDate)' => $startDate, 'DATE(entryCreateDate)' <= $endDate]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			//if (in_array($id, $debitAccounts)){
			$acctDF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $debitAccounts)) {
						$acctDF = 1;
					}
				}
			}
			if ($acctDF == 1 || in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {

					if ($levels) {
						if ((substr($this->getAccountByID($debitAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					} else {
						if ($debitAccount == $id) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					}
					$i++;
				}
			}


			/*$creditAccounts = json_decode($entry->entryCreditAccount);
			//if (in_array($id, $creditAccounts)){
			//if (in_array($id, $creditAccounts)){
			$acctCF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $creditAccounts)) {
						$acctCF = 1;
					}
				}
			}
			if ($acctCF == 1 || in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($levels) {
						if ((substr($this->getAccountByID($creditAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					} else {
						if ($creditAccount == $id) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					}
					$i++;
				}
			}*/
		}

		// switch($accountID){
		// 	# Assets
		// 	case $this->startsWith($accountID, '100'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// 	# Liabilities
		// 	case $this->startsWith($accountID, '200'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Owners Equity
		// 	case $this->startsWith($accountID, '300'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Revenues
		// 	case $this->startsWith($accountID, '400'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Operating Expenses
		// 	case $this->startsWith($accountID, '500'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// }

		$total = $debitTotal - $creditTotal;

		//$total = $creditTotal;
		return $total;
	}


	public function getAccountTotalCR($id, $start_date = NULL, $end_date = NULL, $levels = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$getSQL = "SELECT * FROM entries WHERE( DATE(entryCreateDate) BETWEEN '{$startDate}' AND '{$endDate}') AND entryStatus=1";
		//$queryDB = $this->db->query($getSQL);
		//$accountEntries = $queryDB->result();

		//$this->db->where(" (accountCategory = 'الإيرادات' OR accountCategory = 'المصروفات') ");



		$this->db->where('entryStatus', 1);
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$q = $this->db->get('gl_entries');

		//$q = $this->db->get_where('gl_entries', ['entryStatus' => 1, 'DATE(entryCreateDate)' => $startDate, 'DATE(entryCreateDate)' <= $endDate]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			/*$debitAccounts = json_decode($entry->entryDebitAccount);
			//if (in_array($id, $debitAccounts)){
			$acctDF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $debitAccounts)) {
						$acctDF = 1;
					}
				}
			}
			if ($acctDF == 1 || in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {

					if ($levels) {
						if ((substr($this->getAccountByID($debitAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					} else {
						if ($debitAccount == $id) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					}
					$i++;
				}
			}*/


			$creditAccounts = json_decode($entry->entryCreditAccount);
			//if (in_array($id, $creditAccounts)){
			//if (in_array($id, $creditAccounts)){
			$acctCF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $creditAccounts)) {
						$acctCF = 1;
					}
				}
			}
			if ($acctCF == 1 || in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($levels) {
						if ((substr($this->getAccountByID($creditAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					} else {
						if ($creditAccount == $id) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					}
					$i++;
				}
			}
		}

		// switch($accountID){
		// 	# Assets
		// 	case $this->startsWith($accountID, '100'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// 	# Liabilities
		// 	case $this->startsWith($accountID, '200'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Owners Equity
		// 	case $this->startsWith($accountID, '300'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Revenues
		// 	case $this->startsWith($accountID, '400'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Operating Expenses
		// 	case $this->startsWith($accountID, '500'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// }

		$total = $creditTotal;

		//$total = $creditTotal;
		return $total;
	}




	public function getAccountChildsByID($id)
	{

		$q = $this->db->get_where('gl_accounts', ['parent_id' => $id]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function getAccountsNotZeros()
	{
		//$this->db->where('id', $id);
		$this->db->where(" (accountDebit > 0 OR accountCredit > 0) ");

		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function getPaymentBygl_payment_method_id($gl_payment_method_id)
	{
		//$this->db->where('id', $id);
		$this->db->where('gl_payment_method_id', $gl_payment_method_id);

		$q = $this->db->get('payments');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function startsWith($string, $startString)
	{
		$len = strlen($startString);
		return (substr($string, 0, $len) === $startString);
	}

	public function getAccountsByCategory($accountCategory)
	{

		//$this->db->select('id,accountName as name');
		$this->db->where('accountCategory', $accountCategory);
		$this->db->where('parent_id != ', 0);
		$this->db->where('master_account', 0);
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function addAccountBatch($data = [] ,$order_type = null ,$entryDate = null)
	{
		if ($this->db->insert_batch('gl_accounts', $data)) {
		//if ($this->db->insert('gl_accounts', $data)) {
			if($order_type=="customers")
			$this->db->update('foodics_settings', ['latest_sync_customers' => $entryDate], ['id' => 1]);
			if($order_type=="branches")
			$this->db->update('foodics_settings', ['latest_sync_branches' => $entryDate], ['id' => 1]);
			if($order_type=="suppliers")
			$this->db->update('foodics_settings', ['latest_sync_suppliers' => $entryDate], ['id' => 1]);

			return true;
		}
		return false;
	}
	public function getAccountByUUID($account_ref_uuid)
	{
		$q = $this->db->get_where('gl_accounts', ['account_ref_uuid' => $account_ref_uuid], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getPaymentMethodsByUUID($uuid)
	{
		$q = $this->db->get_where('gl_payment_methods', ['uuid' => $uuid], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function addPaymentMethodsBatch($data = [] ,$order_type = null ,$entryDate = null)
	{
		if ($this->db->insert_batch('gl_payment_methods', $data)) {
		//if ($this->db->insert('gl_accounts', $data)) {
			if($order_type=="Payment_types")
			$this->db->update('foodics_settings', ['latest_sync_Payment_types' => $entryDate], ['id' => 1]);

			return true;
		}
		return false;
	}

	public function createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$reference_no,$entryType,$costCenterDebitId = NULL,$costCenterDebitAmount = NULL,$costCenterCreditId = NULL,$costCenterCreditAmount = NULL,$entryDate = NULL,$entryDescription = NULL,$uuid = NULL,$order_type = NULL) {

		$business_timezone=$this->getFoodicsSettings()->business_timezone;
		if($business_timezone){
			$business_timezone=str_replace(":"," hours +",$business_timezone)." minutes";
    	$newDate = date('Y-m-d H:i:s', strtotime($entryDate. ' '.$business_timezone));
		}else
		$newDate = $entryDate;

		if(!$costCenterDebitId)
			$costCenterDebitId="";
		if(!$costCenterDebitAmount)
			$costCenterDebitAmount="";
		if(!$costCenterCreditId)
			$costCenterCreditId="";
		if(!$costCenterCreditAmount)
			$costCenterCreditAmount="";

		if(!$entryDescription)
			$entryDescription="Automated_Accounting_Entry";


            if(!$uuid)
			$uuid="";




		$entryDebitAccount=json_encode((Array) $entryDebitAccount);
		$entryCreditAccount=json_encode((Array) $entryCreditAccount);
		$entryDebitBalance=json_encode((Array) $entryDebitBalance);
		$entryCreditBalance=json_encode((Array) $entryCreditBalance);
		if($entryDate)
			$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryDate' => $newDate, 'entryType' => $entryType, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'uuid' => $uuid];
        else
			$clause = ['reference_no' => $reference_no, 'created_by' => $this->session->userdata('user_id'), 'userID' => $this->session->userdata('user_id'), 'entryDescription' => $entryDescription, 'entryType' => $newDate, 'entryDebitAccount' => $entryDebitAccount, 'entryDebitBalance' => $entryDebitBalance, 'entryCreditAccount' => $entryCreditAccount, 'entryCreditBalance' => $entryCreditBalance, 'entryStatus' => 1, 'costCenterDebitId' => $costCenterDebitId, 'costCenterDebitAmount' => $costCenterDebitAmount, 'costCenterCreditId' => $costCenterCreditId, 'costCenterCreditAmount' => $costCenterCreditAmount, 'uuid' => $uuid];
        if($this->db->insert('gl_entries', $clause)){

			$entryID = $this->db->insert_id();

			$this->site->approve($entryID,$entryDescription);

			if($order_type=="sales")
			$this->db->update('foodics_settings', ['latest_sync_order' => $entryDate], ['id' => 1]);
			if($order_type=="purchases")
			$this->db->update('foodics_settings', ['latest_sync_purchase' => $entryDate], ['id' => 1]);
			if($order_type=="transfer")
			$this->db->update('foodics_settings', ['latest_sync_transfer' => $entryDate], ['id' => 1]);
			if($order_type=="inventory_counts")
			$this->db->update('foodics_settings', ['latest_sync_inventory_counts' => $entryDate], ['id' => 1]);
			if($order_type=="deposits")
			$this->db->update('foodics_settings', ['latest_sync_account_transactions' => $entryDate], ['id' => 1]);
			if($order_type=="destruction_inventory")
			$this->db->update('foodics_settings', ['latest_sync_destruction_inventory' => $entryDate], ['id' => 1]);

			return true;
		}
		return false;
   		 }




	public function getEntriesByUUID($uuid)
	{
		$q = $this->db->get_where('gl_entries', ['uuid' => $uuid, 'entryStatus' => 1], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function getFoodicsSettings()
    {
        $q = $this->db->get('foodics_settings');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function updateReceiptV2($id, $data)
	{
		$costCenterDebitId = $data['costCenterDebitId'];
		$costCenterDebitAmount = $data['costCenterDebitAmount'];
		/*if ($data['tax_amount'] > 0) {
			$costCenterDebitId[] = "";
			$costCenterDebitAmount[] = "";
		}*/
		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];

		$entryDebitAccount = $data['entryDebitAccount'];
		$entryDebitBalance = $data['entryDebitBalance'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		$data['entryDebitAccount'] = json_encode((array) $data['entryDebitAccount']);
		$data['entryDebitBalance'] = json_encode((array) $data['entryDebitBalance']);


		if ($this->db->update('gl_receipts', $data, ['id' => $id])) {

			/*$entryDebitAccount = [];

			$entryDebitAccount[] =  $data['toAccountID'];

			//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
			if ($data['tax_amount'] > 0)
				$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

			//$entryCreditBalance= [];
			$entryDebitBalance = [];
			$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);
*/
				if ($this->Settings->accounting)
					$this->site->updateEntryMultiSide($entryDebitAccount, $data['fromAccountID'], $entryDebitBalance, $data['equivalent_total_amount'], $data['bond_no'], 'Receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount));

			return true;
		}
		return false;
	}

	public function updateCatchReceiptV2($id, $data)
	{

		$costCenterCreditId = $data['costCenterCreditId'];
		$costCenterCreditAmount = $data['costCenterCreditAmount'];
		/*if ($data['tax_amount'] > 0) {
			$costCenterCreditId[] = "";
			$costCenterCreditAmount[] = "";
		}*/
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];

		$entryCreditAccount = $data['entryCreditAccount'];
		$entryCreditBalance = $data['entryCreditBalance'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		$data['entryCreditAccount'] = json_encode((array) $data['entryCreditAccount']);
		$data['entryCreditBalance'] = json_encode((array) $data['entryCreditBalance']);

		if ($this->db->update('gl_catch_receipts', $data, ['id' => $id])) {

			/*$entryCreditAccount = [];

			$entryCreditAccount[] =  $data['toAccountID'];

			if ($data['tax_amount'] > 0)
				$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

			$entryCreditBalance = [];
			$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);*/

				if ($this->Settings->accounting)
					$this->site->updateEntryMultiSide($data['fromAccountID'], $entryCreditAccount, $data['equivalent_total_amount'], $entryCreditBalance, $data['bond_no'], 'Catch_receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount));

			return true;
		}
		return false;
	}


	public function updateCatchReceiptV3($id, $data,$paid_by = NULL,$custBalance = NULL,$old_bond_no = NULL,$amountDiff = NULL)
	{

		$costCenterCreditId = $data['costCenterCreditId'];
		$costCenterCreditAmount = $data['costCenterCreditAmount'];
		/*if ($data['tax_amount'] > 0) {
			$costCenterCreditId[] = "";
			$costCenterCreditAmount[] = "";
		}*/
		$costCenterDebitId = $data['costCenterDebitId'];
		$costCenterDebitAmount = $data['costCenterDebitAmount'];

		$entryCreditAccount = $data['entryCreditAccount'];
		$entryCreditBalance = $data['entryCreditBalance'];
		$equivalent_entryCreditBalance = $data['equivalent_entryCreditBalance'];

		$entryDebitAccount = $data['entryDebitAccount'];
		$entryDebitBalance = $data['entryDebitBalance'];
		$equivalent_entryDebitBalance = $data['equivalent_entryDebitBalance'];

		$entryDebitDescription = $data['entryDebitDescription'];
		$entryCreditDescription = $data['entryCreditDescription'];


		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		$data['entryDebitAccount'] = json_encode((array) $data['entryDebitAccount']);
		$data['entryDebitBalance'] = json_encode((array) $data['entryDebitBalance']);
		$data['equivalent_entryDebitBalance'] = json_encode((array) $data['equivalent_entryDebitBalance']);

		$data['entryCreditAccount'] = json_encode((array) $data['entryCreditAccount']);
		$data['entryCreditBalance'] = json_encode((array) $data['entryCreditBalance']);
		$data['equivalent_entryCreditBalance'] = json_encode((array) $data['equivalent_entryCreditBalance']);

		$data['entryDebitDescription'] = json_encode((array) $data['entryDebitDescription']);
		$data['entryCreditDescription'] = json_encode((array) $data['entryCreditDescription']);

		if($data['invoices']){
			$invoices = $data['invoices'];
			$payments = $data['payments'];
			$data['invoices'] = json_encode((array) $data['invoices']);
			$data['payments'] = json_encode((array) $data['payments']);
		}


		if ($this->db->update('gl_catch_receipts', $data, ['id' => $id])) {



			if($data['inv_no']  && strpos($data['inv_no'], ',') === false){
				$sale=$this->site->getSaleByID($data['inv_no']);
				$payment = [
					'date'         => $data['date'],
					'sale_id'      => $data['inv_no'],
					'reference_no' => $data['reference_no'] ? $data['reference_no'] : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $sale->brId : null),
					'amount'       => $custBalance,
					'paid_by'      => $paid_by,
					'note'         => $data['note'],
					'currency'         => $data['currency_code'],
					'currency_rate'         => $data['currency_rate'],
					'created_by'   => $data['created_by'],
					'type'         => 'received',
					'catch_receipt_id'       => $id,


				 ];

				if($opay = $this->getPaymentByCR_ID($id)){
				if ($this->db->update('payments', $payment, ['catch_receipt_id' => $id])) {
					$this->site->syncSalePayments($data['inv_no']);

				}
				}else{

					if ($this->db->insert('payments', $payment)) {
						if ($this->site->getReference('pay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
							$this->site->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
						}
						$this->site->syncSalePayments($data['inv_no']);


					}

				}

				if($amountDiff<0){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
					$equivalent_entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
				}

				if($amountDiff>0){
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
					$equivalent_entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
				}
			}

			  if(strpos($data['inv_no'], ',') !== false){
				$i=0;
				foreach ($invoices as $inv_no){

				$sale=$this->site->getSaleByID($inv_no);
				$payment = [
					'date'         => $data['date'],
					'sale_id'      => $inv_no,
					'reference_no' => $data['reference_no'] ? $data['reference_no'] : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $sale->brId : null),
					'amount'       => $payments[$i],
					'paid_by'      => $paid_by,
					'note'         => $data['note'],
					'currency'         => $data['currency_code'],
					'currency_rate'         => $data['currency_rate'],
					'created_by'   => $data['created_by'],
					'type'         => 'received',
					'catch_receipt_id'       => $id,


				 ];



				if($opay = $this->getPaymentByCR_ID($id)){
				if ($this->db->update('payments', $payment, ['catch_receipt_id' => $id,'sale_id' => $inv_no])) {
					$this->site->syncSalePayments($inv_no);

				}
				}else{

					if ($this->db->insert('payments', $payment)) {
						if ($this->site->getReference('pay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
							$this->site->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
						}
						$this->site->syncSalePayments($inv_no);


					}

				}

				if($amountDiff<0){
					$entryDebitAccount[] =  $this->site->getAccountsSettings()->accounts_currency_spread_losses_acct;
					$equivalent_entryDebitBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
				}

				if($amountDiff>0){
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->accounts_currency_differences_profit_acct;
					$equivalent_entryCreditBalance[] =  $this->sma->formatDecimal(abs($amountDiff));
				}
					$i++;

			  }
			}

			if($data['the_oldest']==1 && $data['invoices']){
				$catch_receipt_id=$id;
				$i=0;
				foreach ($invoices as $inv_no){

				$sale=$this->site->getSaleByID($inv_no);
				$payment = [
					'date'         => $data['date'],
					'sale_id'      => $inv_no,
					'reference_no' => $data['reference_no'] ? $data['reference_no'] : $this->site->getReference('pay',(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1) ? $sale->brId : null),
					'amount'       => $payments[$i],
					'paid_by'      => $paid_by,
					'note'         => $data['note'],
					'currency'         => $data['currency_code'],
					'currency_rate'         => $data['currency_rate'],
					'created_by'   => $data['created_by'],
					'type'         => 'received',
					'catch_receipt_id'       => $catch_receipt_id,


				 ];



					if ($this->db->insert('payments', $payment)) {
						if ($this->site->getReference('pay',$payment['brId'] ? $payment['brId']  : null) == $payment['reference_no']) {
							$this->site->updateReference('pay',$payment['brId'] ? $payment['brId']  : null);
						}
						$this->site->syncSalePayments($inv_no);


					}
					$i++;

				}


			}

			/*$entryCreditAccount = [];

			$entryCreditAccount[] =  $data['toAccountID'];

			if ($data['tax_amount'] > 0)
				$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

			$entryCreditBalance = [];
			$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);*/

				if ($this->Settings->accounting){
					if($old_bond_no){
						$entryDescription="Catch_receipt_Bond_No#".$data['bond_no'];
						$this->db->update('gl_entries', ['reference_no' => $data['bond_no'],'entryDescription' => $entryDescription], ['reference_no' => $old_bond_no,'entryType' => 'Catch_receipts']);
					}
					$this->site->updateEntryMultiSide($entryDebitAccount, $entryCreditAccount, $equivalent_entryDebitBalance, $equivalent_entryCreditBalance, $data['bond_no'], 'Catch_receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $entryDebitDescription, $entryCreditDescription,$data['date'],$data['currency_code'],$data['currency_rate']);
				}


			return true;
		}
		return false;
	}

	public function getAccountByAccountIDV2($accountID)
	{
		$q = $this->db->get_where('gl_accounts', ['accountID' => $accountID]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getDepreciationsByYear($Year)
	{

		$startDate = $Year.'-01-01 00:00:00';
        $endDate = $Year.'-12-31 23:59:58';
        $this->db->select('Max(start_value) as start_value,Min(end_value) as end_value,(Max(start_value) - Min(end_value)) as accumulated_value, code, accountID, accountIDEX');
		$this->db->join('ast_fixed_assets', 'ast_fixed_assets.id = ast_depreciations.fixed_asset_id', 'left');

		$this->db->group_by('fixed_asset_id');

        if ($startDate && $endDate) {

			$this->db->where('period_date BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}

        //$this->db->order_by('id', 'asc');
		$q = $this->db->get('ast_depreciations');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {

					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getAllAccounts()
	{
		$this->db->order_by('id', 'asc');

		$this->db->where('parent_id!=', 0);

		$this->db->where('accountStatus', 1);
		//$this->db->where('accountStatement', 'Balance Sheet');
		//$this->db->where('openingBalance', 0);


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				if ($this->Settings->user_language != 'arabic' && $row->accountNameEn)
					$row->accountName = $row->accountNameEn;
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function updateReceiptV3($id, $data,$old_bond_no = null)
	{
		$costCenterDebitId = $data['costCenterDebitId'];
		$costCenterDebitAmount = $data['costCenterDebitAmount'];
		/*if ($data['tax_amount'] > 0) {
			$costCenterDebitId[] = "";
			$costCenterDebitAmount[] = "";
		}*/
		$costCenterCreditId = $data['costCenterCreditId'];
		$costCenterCreditAmount = $data['costCenterCreditAmount'];

		$entryDebitAccount = $data['entryDebitAccount'];
		$entryDebitBalance = $data['entryDebitBalance'];
		$equivalent_entryDebitBalance = $data['equivalent_entryDebitBalance'];

		$entryCreditAccount = $data['entryCreditAccount'];
		$entryCreditBalance = $data['entryCreditBalance'];
		$equivalent_entryCreditBalance = $data['equivalent_entryCreditBalance'];

		$entryDebitDescription = $data['entryDebitDescription'];
		$entryCreditDescription = $data['entryCreditDescription'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		$data['entryDebitAccount'] = json_encode((array) $data['entryDebitAccount']);
		$data['entryDebitBalance'] = json_encode((array) $data['entryDebitBalance']);
		$data['equivalent_entryDebitBalance'] = json_encode((array) $data['equivalent_entryDebitBalance']);

		$data['entryCreditAccount'] = json_encode((array) $data['entryCreditAccount']);
		$data['entryCreditBalance'] = json_encode((array) $data['entryCreditBalance']);
		$data['equivalent_entryCreditBalance'] = json_encode((array) $data['equivalent_entryCreditBalance']);

		$data['entryDebitDescription'] = json_encode((array) $data['entryDebitDescription']);
		$data['entryCreditDescription'] = json_encode((array) $data['entryCreditDescription']);


		if ($this->db->update('gl_receipts', $data, ['id' => $id])) {

			/*$entryDebitAccount = [];

			$entryDebitAccount[] =  $data['toAccountID'];

			//$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;
			if ($data['tax_amount'] > 0)
				$entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;

			//$entryCreditBalance= [];
			$entryDebitBalance = [];
			$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryDebitBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);
*/
				if ($this->Settings->accounting){
					if($old_bond_no){
						$entryDescription="Receipt_Bond_No#".$data['bond_no'];
						$this->db->update('gl_entries', ['reference_no' => $data['bond_no'],'entryDescription' => $entryDescription], ['reference_no' => $old_bond_no,'entryType' => 'Receipts']);
					}
					$this->site->updateEntryMultiSide($entryDebitAccount, $entryCreditAccount, $equivalent_entryDebitBalance, $equivalent_entryCreditBalance, $data['bond_no'], 'Receipts', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $entryDebitDescription, $entryCreditDescription,$data['date'],$data['currency_code'],$data['currency_rate']);
				}


			return true;
		}
		return false;
	}

	public function IsAccountIsChild($id,$parent_id)
	{


		$row = $this->db->get_where('gl_accounts', ['id' => $parent_id])->result_array();



		foreach ($row as $key => $value) {
			//return $value;
			if($id == $value['id']){
				return '1';
			}
			if($value['level']==1){
				return '0';
			}
			$data = $this->IsAccountIsChild($id,$value['parent_id']);
		}



		return $data;
	}

	public function getMaxBondNoticeCreditor($brId=null)
	{
		//$this->db->select("LPAD(COALESCE(max(id+1),1), 8, '0') as bond_no");

		$this->db->select("LPAD(count(*)+1, 8, '0') as bond_no");
		$this->db->like('bond_no', '00', 'both');
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$q = $this->db->get('gl_notice_creditor');
		if ($q->num_rows() > 0) {
			if ($brId) {
				return $this->site->getMainBranchesByID($brId)->branch_prefix.'/'.$q->row()->bond_no;
			}else{
				return $q->row()->bond_no;
			}
		}
		return false;
	}


	public function addNoticeCreditor($data, $entryCreditDescription = NULL,$adjust = NULL,$products = NULL)
	{
		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);




		if ($this->db->insert('gl_notice_creditor', $data)) {
			$notice_creditor_id = $this->db->insert_id();
			if($adjust && $products){
				$adjust['notice_creditor_id']=$notice_creditor_id;
				$this->addAdjust($adjust, $products);
			}
			$entryDescription="Notice_Creditor_No#".$data['bond_no'];
			if ($this->Settings->accounting){

				$entryCreditAccount = [];

				$entryCreditAccount[] =  $data['fromAccountID'];

				if ($data['tax_amount'] > 0)
					$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

				$entryCreditBalance = [];
				$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
				if ($data['tax_amount'] > 0)
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);


				if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
                    {
						$this->site->createEntryMultiSide($entryCreditAccount,$data['toAccountID'], $entryCreditBalance, $data['equivalent_total_amount'], $data['bond_no'], 'Notice_creditor', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'],$entryDescription, $data['grpId'], $data['coId'], $data['brId'],null,$entryCreditDescription,$data['currency_code'],$data['currency_rate']);
					}else{
						$this->site->createEntryMultiSide($entryCreditAccount,$data['toAccountID'], $entryCreditBalance, $data['equivalent_total_amount'], $data['bond_no'], 'Notice_creditor', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount), $data['date'],$entryDescription,null,null,null,null,$entryCreditDescription,$data['currency_code'],$data['currency_rate']);
					}
			}
			return true;
		}
		return false;
	}

	public function getNoticeCreditorByID($id)
	{
		$q = $this->db->get_where('gl_notice_creditor', ['id' => $id], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}

	public function updateNoticeCreditor($id, $data, $entryCreditDescription = NULL,$adjust = NULL,$products = NULL,$old_bond_no = null)
	{

		$costCenterCreditId[] = $data['costCenterCreditId'];
		$costCenterCreditAmount[] = $data['costCenterCreditAmount'];
		$costCenterDebitId[] = $data['costCenterDebitId'];
		$costCenterDebitAmount[] = $data['costCenterDebitAmount'];

		$data['costCenterDebitId'] = json_encode((array) $data['costCenterDebitId']);
		$data['costCenterDebitAmount'] = json_encode((array) $data['costCenterDebitAmount']);
		$data['costCenterCreditId'] = json_encode((array) $data['costCenterCreditId']);
		$data['costCenterCreditAmount'] = json_encode((array) $data['costCenterCreditAmount']);

		if ($this->db->update('gl_notice_creditor', $data, ['id' => $id])) {


			if($AdjustCR=$this->getAdjustByNCId($id)){
				$AdjustId=$AdjustCR->id;
				$this->deleteAdjust($AdjustId);
			}

			if($adjust && $products){
				$this->addAdjust($adjust, $products);
			}



			$entryCreditAccount = [];

			$entryCreditAccount[] =  $data['fromAccountID'];

			if ($data['tax_amount'] > 0)
				$entryCreditAccount[] =  $this->site->getAccountsSettings()->sales_vat_acct;

			$entryCreditBalance = [];
			$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_amount']);
			if ($data['tax_amount'] > 0)
				$entryCreditBalance[] =  $this->sma->formatDecimal($data['equivalent_tax_amount']);


			if ($this->Settings->accounting){
				if($old_bond_no){
					$entryDescription="Notice_Creditor_No#".$data['bond_no'];
					$this->db->update('gl_entries', ['reference_no' => $data['bond_no'],'entryDescription' => $entryDescription], ['reference_no' => $old_bond_no,'entryType' => 'Notice_creditor']);
				}
				$this->site->updateEntryMultiSide($entryCreditAccount,$data['toAccountID'], $entryCreditBalance, $data['equivalent_total_amount'], $data['bond_no'], 'Notice_creditor', json_encode((array) $costCenterDebitId), json_encode((array) $costCenterDebitAmount), json_encode((array) $costCenterCreditId), json_encode((array) $costCenterCreditAmount),null,$entryCreditDescription,$data['date'],$data['currency_code'],$data['currency_rate']);
			}


			return true;
		}
		return false;
	}

	public function getTaxRatesActive()
    {
        $this->db->select('rate');
		$this->db->where('rate >', 0);
        $this->db->where('tax_active =', 1);
		$this->db->where('category_id =', 1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }else{
			return $this->getTaxRatesLast();
		}
        return false;
    }

	public function getTaxRatesLast()
    {
        $this->db->select('rate');
		$this->db->where('rate >', 0)->order_by('id', 'desc')->limit(1);
        $q = $this->db->get('tax_rates');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function getEntriesCollection($startDate = NULL, $endDate = NULL, $show_Opening_balance = NULL,$grpId = NULL, $coId = NULL, $brId = NULL)
	{

		$this->db->order_by('entryDate', 'asc');
		if ($startDate && $endDate) {
			//$this->db->where(" entryCreateDate BETWEEN DATE('{$startDate}') AND DATE('{$endDate}')");
			//$this->db->where(" entryCreateDate > ".DATE($startDate));
			//$this->db->where('entryDate >', DATE($startDate));
			//$this->db->where('entryDate <=', DATE($endDate));
			$this->db->where('entryDate BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}
		$this->db->where('entryType !=', 'Opening_balance');
		//$this->db->where("(entryDescription like '%Sales_Invoice_No#%' or entryDescription like '%Catch_receipt_Bond_No#%')");
		$this->db->where("  ( (entryDescription LIKE '%Sales_Invoice_No#%') OR (entryDescription LIKE '%Catch_receipt_Bond_No#%') ) ");

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		if ($show_Opening_balance == 0) {
			//$this->db->where('entryType !=', 'Opening_balance');
		}

		$this->db->where('entryStatus', 1);



		$q = $this->db->get('gl_entries');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	public function getCatchReceiptByBondNo($bond_no)
	{
		$q = $this->db->get_where('gl_catch_receipts', ['bond_no' => $bond_no], 1);
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return false;
	}


    public function getSaleByReference($reference_no)
    {
        $q = $this->db->get_where('sales', ['reference_no' => $reference_no], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getPaymentByCR_ID($catch_receipt_id)
    {
        $q = $this->db->get_where('payments', ['catch_receipt_id' => $catch_receipt_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

	public function getBillerSuggestions($term, $limit = 10)
    {
        $this->db->select('id, company,  name');
        $this->db->where(" (id LIKE '%" . $term . "%' OR name LIKE '%" . $term . "%' OR company LIKE '%" . $term . "%') ");
        $q = $this->db->get_where('companies', ['group_name' => 'biller'], $limit);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }

            return $data;
        }
    }

	public function addAdjust($data, $products)
    {
        if ($this->db->insert('adjusts', $data)) {
            $adjustment_id = $this->db->insert_id();
            foreach ($products as $product) {
                $product['adjustment_id'] = $adjustment_id;
                $this->db->insert('adjust_items', $product);
                $this->syncAdjustment($product);
            }
            if ($this->site->getReference('qa') == $data['reference_no']) {
                $this->site->updateReference('qa');
            }
            return true;
        }
        return false;
    }

	public function syncAdjustment($data = [])
    {
        if (!empty($data)) {
            $clause = ['product_id' => $data['product_id'], 'option_id' => $data['option_id'], 'warehouse_id' => $data['warehouse_id'], 'status' => 'received'];
            $qty    = $data['type'] == 'subtraction' ? 0 - $data['quantity'] : 0 + $data['quantity'];
            $this->site->setPurchaseItem($clause, $qty);

            $this->site->syncProductQty($data['product_id'], $data['warehouse_id']);
            if ($data['option_id']) {
                $this->site->syncVariantQty($data['option_id'], $data['warehouse_id'], $data['product_id']);
            }
        }
    }

	public function getAdjustByID($id)
    {
        $q = $this->db->get_where('adjusts', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function getAdjustByNCId($notice_creditor_id)
    {
        $q = $this->db->get_where('adjusts', ['notice_creditor_id' => $notice_creditor_id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }


	public function getAdjustItems($adjustment_id)
    {
        $this->db->select('adjust_items.*, products.code as product_code, products.name as product_name, products.image, products.details as details, product_variants.name as variant')
            ->join('products', 'products.id=adjust_items.product_id', 'left')
            ->join('product_variants', 'product_variants.id=adjust_items.option_id', 'left')
            ->group_by('adjust_items.id')
            ->order_by('id', 'asc');

        $this->db->where('adjustment_id', $adjustment_id);

        $q = $this->db->get('adjust_items');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

	public function reverseAdjust($id)
    {
        if ($products = $this->getAdjustItems($id)) {
            foreach ($products as $adjustment) {
                $clause = ['product_id' => $adjustment->product_id, 'warehouse_id' => $adjustment->warehouse_id, 'option_id' => $adjustment->option_id, 'status' => 'received'];
                $qty    = $adjustment->type == 'subtraction' ? (0 + $adjustment->quantity) : (0 - $adjustment->quantity);
                $this->site->setPurchaseItem($clause, $qty);
                $this->site->syncProductQty($adjustment->product_id, $adjustment->warehouse_id);
                if ($adjustment->option_id) {
                    $this->site->syncVariantQty($adjustment->option_id, $adjustment->warehouse_id, $adjustment->product_id);
                }
            }
        }
    }

    public function deleteAdjust($id)
    {
        $this->reverseAdjust($id);
        //$this->site->log('Quantity adjustment', ['model' => $this->getAdjustByID($id), 'items' => $this->getAdjustItems($id)]);
        if ($this->db->delete('adjusts', ['id' => $id]) && $this->db->delete('adjust_items', ['adjustment_id' => $id])) {
            return true;
        }
        return false;
    }

	public function getAllSalesDue($id = NULL,$startDate = NULL,$endDate = NULL,$flage = NULL, $customer_id = NULL, $not_due = NULL, $the_oldest = NULL, $salesman_id = NULL,$grpid = null ,$coid = null ,$brid = null){

		$this->db->select('id, DATE_FORMAT(date, "%Y-%m-%d") as date, reference_no, customer, (grand_total-(paid+discount_allowance)) as amount, COALESCE(payment_term,0) as payment_term,(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END) as due_date,DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) as outstanding');
		$this->db->order_by('date', 'ASC');

		if ($startDate && $endDate && $flage){
			    $this->db->where(" (date BETWEEN '" . $startDate . "' and '" . $endDate . "' )  ");

			}
			if ($startDate==NULL && $endDate){
			    //$this->db->where(" (date <= '" . $endDate . "' )  ");
			   $this->db->where('date  <=',$endDate);

			}

            if($grpid)
            $this->db->where('grpid =', $grpid);
            if($coid)
            $this->db->where('coid =', $coid);
            if($brid)
            $this->db->where('brid =', $brid);

			if($id){
				$this->db->where('id', $id);
			}
			if($customer_id){
				$this->db->where('customer_id', $customer_id);
			}
            if ($salesman_id){
                $this->db->where('salesman_id', $salesman_id);
            }
			if($not_due==0){
				$this->db->where('DATEDIFF("'.$endDate.'",(CASE WHEN due_date IS NULL THEN DATE_FORMAT(date, "%Y-%m-%d") ELSE due_date END)) >=', 0);
			}
			$this->db->where('payment_status !=', 'paid');
			$this->db->where('abs(grand_total-(paid+discount_allowance)) >', 0);



        $q = $this->db->get('sales');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;



		}


		public function addOpeningBalanceV2($data, $acounts, $entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $costCenterDebitId = NULL, $costCenterCreditId = NULL, $costCenterDebitAmount = NULL, $costCenterCreditAmount = NULL,$grpId = NULL,$coId = NULL,$brId = NULL)
	{
		if ($this->db->insert('gl_opening_balance', $data)) {
			$openingId = $this->db->insert_id();
			foreach ($acounts as $acount) {
				$acount['openingId'] = $openingId;
				$this->db->insert('gl_opening_balance_items', $acount);
				$this->db->update('gl_accounts', ['openingBalance' => $acount['balance']], ['id' => $acount['accountID']]);
			}


			if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
			{

				$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $openingId, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $data['openingDate'],'',$grpId,$coId,$brId,null,null,$data['currency_code'],$data['currency_rate']);

			}else{


				$this->site->createEntryMultiSide($entryDebitAccount, $entryCreditAccount, $entryDebitBalance, $entryCreditBalance, $openingId, 'Opening_balance', $costCenterDebitId, $costCenterDebitAmount, $costCenterCreditId, $costCenterCreditAmount, $data['openingDate'],'',null,null,null,null,null,$data['currency_code'],$data['currency_rate']);
			}



			return true;
		}
		return false;
	}


	public function getAccountTotalDebitCreditV2( $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$this->db->select('entryDebitAccount,entryDebitBalance,entryCreditAccount,entryCreditBalance');
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}





		$q = $this->db->get_where('gl_entries', ['entryStatus' => 1]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}



		return $data1;
	}


	public function getAccountTotalDebitCreditV3($id, $accountEntries)
	{

	$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}


	public function getAccountTotalDebitCreditNetV3($id, $accountEntries)
	{
	$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			if (in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {
					if ($debitAccount == $id) {
						$debitLocation = $i;
						//$debitTotal[] = $debitLocation;
						$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
					}
					$i++;
				}
			}

			$creditAccounts = json_decode($entry->entryCreditAccount);
			if (in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($creditAccount == $id) {
						$creditLocation = $i;
						//$debitTotal[] = $debitLocation;
						$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
					}
					$i++;
				}
			}
		}

		if ($debitTotal > $creditTotal) {
			$debitTotal = $debitTotal - $creditTotal;
			$creditTotal = 0;
		} else if ($debitTotal < $creditTotal) {
			$creditTotal = $creditTotal - $debitTotal;
			$debitTotal = 0;
		} else {
			$debitTotal = 0;
			$creditTotal = 0;
		}

		$data['debitTotal'] = $debitTotal;
		$data['creditTotal'] = $creditTotal;
		return $data;
	}

	public function deleteNoticeCreditor($id)
	{
		$receipt = $this->getNoticeCreditorByID($id);

		if ($this->db->delete('gl_notice_creditor', ['id' => $id])) {
			if($AdjustCR=$this->getAdjustByNCId($id)){
				$AdjustId=$AdjustCR->id;
				$this->deleteAdjust($AdjustId);
			}

			if ($this->Settings->accounting){
				if($receipt->bond_no){
					$entryDescription="Notice_Creditor_No#".$receipt->bond_no;
					$this->db->update('gl_entries', ['entryStatus' => '0'], ['reference_no' => $receipt->bond_no,'entryType' => 'Notice_creditor']);
				}

			}


			return true;
		}

		return false;
	}


	public function getAccountTotalOB($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$getSQL = "SELECT * FROM entries WHERE( DATE(entryCreateDate) BETWEEN '{$startDate}' AND '{$endDate}') AND entryStatus=1";
		//$queryDB = $this->db->query($getSQL);
		//$accountEntries = $queryDB->result();

		//$this->db->where(" (accountCategory = 'الإيرادات' OR accountCategory = 'المصروفات') ");



		$this->db->where('entryStatus', 1);
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$this->db->where('entryType', 'Opening_balance');

		$q = $this->db->get('gl_entries');

		//$q = $this->db->get_where('gl_entries', ['entryStatus' => 1, 'DATE(entryCreateDate)' => $startDate, 'DATE(entryCreateDate)' <= $endDate]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			//if (in_array($id, $debitAccounts)){
			$acctDF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $debitAccounts)) {
						$acctDF = 1;
					}
				}
			}
			if ($acctDF == 1 || in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {

					if ($levels) {
						if ((substr($this->getAccountByID($debitAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					} else {
						if ($debitAccount == $id) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					}
					$i++;
				}
			}


			$creditAccounts = json_decode($entry->entryCreditAccount);
			//if (in_array($id, $creditAccounts)){
			//if (in_array($id, $creditAccounts)){
			$acctCF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $creditAccounts)) {
						$acctCF = 1;
					}
				}
			}
			if ($acctCF == 1 || in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($levels) {
						if ((substr($this->getAccountByID($creditAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					} else {
						if ($creditAccount == $id) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					}
					$i++;
				}
			}
		}

		// switch($accountID){
		// 	# Assets
		// 	case $this->startsWith($accountID, '100'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// 	# Liabilities
		// 	case $this->startsWith($accountID, '200'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Owners Equity
		// 	case $this->startsWith($accountID, '300'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Revenues
		// 	case $this->startsWith($accountID, '400'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Operating Expenses
		// 	case $this->startsWith($accountID, '500'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// }

		$total = $debitTotal - $creditTotal;

		//$total = $creditTotal;
		return $total;
	}


	public function getAccountTotalNoneOB($id, $start_date = NULL, $end_date = NULL, $grpId = NULL, $coId = NULL, $brId = NULL)
	{

		//$getSQL = "SELECT * FROM entries WHERE( DATE(entryCreateDate) BETWEEN '{$startDate}' AND '{$endDate}') AND entryStatus=1";
		//$queryDB = $this->db->query($getSQL);
		//$accountEntries = $queryDB->result();

		//$this->db->where(" (accountCategory = 'الإيرادات' OR accountCategory = 'المصروفات') ");



		$this->db->where('entryStatus', 1);
		if ($start_date) {
			$this->db->where('entryDate BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		}

		if ($grpId) {
			$this->db->where('grpId', $grpId);
		}
		if ($coId) {
		  $this->db->where('coId', $coId);
		}
		if ($brId) {
			$this->db->where('brId', $brId);
		}

		$this->db->where('entryType !=', 'Opening_balance');

		$q = $this->db->get('gl_entries');

		//$q = $this->db->get_where('gl_entries', ['entryStatus' => 1, 'DATE(entryCreateDate)' => $startDate, 'DATE(entryCreateDate)' <= $endDate]);
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data1[] = $row;
			}
			$accountEntries = $data1;
		}


		$total = 0.00;
		$debitTotal  = 0.00;
		$creditTotal = 0.00;
		foreach ($accountEntries as $entry) {
			$debitAccounts = json_decode($entry->entryDebitAccount);
			//if (in_array($id, $debitAccounts)){
			$acctDF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $debitAccounts)) {
						$acctDF = 1;
					}
				}
			}
			if ($acctDF == 1 || in_array($id, $debitAccounts)) {
				$i = 0;
				foreach ($debitAccounts as $debitAccount) {

					if ($levels) {
						if ((substr($this->getAccountByID($debitAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					} else {
						if ($debitAccount == $id) {
							$debitLocation = $i;
							//$debitTotal[] = $debitLocation;
							$debitTotal += json_decode($entry->entryDebitBalance)[$debitLocation];
						}
					}
					$i++;
				}
			}


			$creditAccounts = json_decode($entry->entryCreditAccount);
			//if (in_array($id, $creditAccounts)){
			//if (in_array($id, $creditAccounts)){
			$acctCF = 0;

			if ($levels) {
				$accs_arr = $this->getAccountChildsIncomeStatement($this->getAccountByID($id)->accountID);
				foreach ($accs_arr as $acc_arr) {
					if (in_array($acc_arr->id, $creditAccounts)) {
						$acctCF = 1;
					}
				}
			}
			if ($acctCF == 1 || in_array($id, $creditAccounts)) {
				$i = 0;
				foreach ($creditAccounts as $creditAccount) {
					if ($levels) {
						if ((substr($this->getAccountByID($creditAccount)->accountID, 0, $levels) === $this->getAccountByID($id)->accountID)) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					} else {
						if ($creditAccount == $id) {
							$creditLocation = $i;
							//$debitTotal[] = $debitLocation;
							$creditTotal += json_decode($entry->entryCreditBalance)[$creditLocation];
						}
					}
					$i++;
				}
			}
		}

		// switch($accountID){
		// 	# Assets
		// 	case $this->startsWith($accountID, '100'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// 	# Liabilities
		// 	case $this->startsWith($accountID, '200'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Owners Equity
		// 	case $this->startsWith($accountID, '300'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Revenues
		// 	case $this->startsWith($accountID, '400'):
		// 		$total = $creditTotal - $debitTotal;
		// 	break;
		// 	# Operating Expenses
		// 	case $this->startsWith($accountID, '500'):
		// 		$total = $debitTotal - $creditTotal;
		// 	break;
		// }

		$total = $debitTotal - $creditTotal;

		//$total = $creditTotal;
		return $total;
	}

	public function getAccountChildsWithLevelsCusSup($accountID, $account_type)
	{

		$this->db->select('gl_accounts.id,gl_accounts.accountName,gl_accounts.account_ref_id')
			->join('companies', 'companies.id=gl_accounts.account_ref_id', 'left')->where('companies.group_id', 3)->where('companies.group_name', 'customer')->where('companies.account_type', 1)->where('gl_accounts.account_type', $account_type);


		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}


	public function getOtherExpensesAccountsV2($accountID1, $accountID2, $accountID3, $levels = NULLL)
	{
		if ($this->Settings->accounting_type == 'periodic') {

			$purchases_acct = $this->site->getAccountsSettings()->purchases_acct;
			//$purchases_acct_parent_arr=$this->accounts_model->getAccounts($purchases_acct)[0];
			//$purchases_acct_parent_id=$purchases_acct_parent_arr->parent_id;
			$purchases_return_acct = $this->site->getAccountsSettings()->purchases_return_acct;
		}
		/*else*/ {

			$sales_cost_of_good_sold_acct = $this->site->getAccountsSettings()->sales_cost_of_good_sold_acct;
			$sales_cost_of_good_sold_acct_parent_arr = $this->accounts_model->getAccounts($sales_cost_of_good_sold_acct)[0];
			$sales_cost_of_good_sold_acct_parent_id = $sales_cost_of_good_sold_acct_parent_arr->parent_id;
		}
		if (strlen($accountID1) > 2) {
			//$this->db->not_like('accountID', $accountID1."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $accountID1 . "0%' OR accountID != '" . $accountID1 . "' ) ");
			//$this->db->where("  ( accountID LIKE '" . $accountID . "0%' OR accountID = '" . $accountID . "' ) ");


		} else {
			$this->db->not_like('accountID', $accountID1, 'after');
		}

		if (strlen($accountID2) > 2) {
			//$this->db->not_like('accountID', $accountID2."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $accountID2 . "0%' OR accountID != '" . $accountID2 . "' ) ");
		} else {
			$this->db->not_like('accountID', $accountID2, 'after');
		}

		if (strlen($accountID3) > 2) {
			//$this->db->not_like('accountID', $accountID3."0", 'after');
			$this->db->where("  ( accountID NOT LIKE '" . $accountID3 . "0%' OR accountID != '" . $accountID3 . "' ) ");
			//$this->db->where("  ( accountID LIKE '" . $accountID . "0%' OR accountID = '" . $accountID . "' ) ");


		} else {
			$this->db->not_like('accountID', $accountID3, 'after');
		}

		//$this->db->not_like('accountID', $accountID1, 'after');
		//$this->db->not_like('accountID', $accountID2, 'after');
		$this->db->where('accountStatement', 'Income Statement');
		$this->db->where('accountCategory', 'المصروفات');

		if ($this->Settings->accounting_type == 'periodic') {
			$this->db->where('id !=', $purchases_acct);
			$this->db->where('id !=', $purchases_return_acct);
		}
		/*else*/ {
			$this->db->where('id !=', $sales_cost_of_good_sold_acct);
			//if($sales_cost_of_good_sold_acct_parent_arr->level=="3")
			$this->db->where('id !=', $sales_cost_of_good_sold_acct_parent_id);
		}
		if ($levels) {
			//$this->db->where('LENGTH(accountID) <=', $levels);
			$this->db->where('level <=', $levels);
		}
		//$this->db->where('id !=', $sales_return_acct);
		//$this->db->where('id !=', $this->site->getAccountsSettings()->);
		$q = $this->db->get('gl_accounts');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

	// =========================================================================
	// Modern Real-Time GL-Based Accounting Dashboard Methods
	// =========================================================================

	protected $_gl_dashboard_cache = [];
	protected $_gl_accounts_settings_cache = null;
	protected $_gl_accounts_tree_cache = null;
	protected $_gl_accounts_children_cache = null;

	/**
	 * Retrieve and cache system accounting settings from sma_gl_accounts_settings
	 */
	public function getGLAccountsSettings()
	{
		if ($this->_gl_accounts_settings_cache !== null) {
			return $this->_gl_accounts_settings_cache;
		}
		$q = $this->db->get('gl_accounts_settings', 1);
		$this->_gl_accounts_settings_cache = ($q && $q->num_rows() > 0) ? (array)$q->row() : [];
		return $this->_gl_accounts_settings_cache;
	}

	/**
	 * Centralized GL Account Tree Resolver
	 * Resolves any setting key (or array of setting keys) from sma_gl_accounts_settings
	 * down to all descendant leaf accounts (or all subtree accounts).
	 *
	 * @param string|array $settingKeys Setting column name(s) in sma_gl_accounts_settings
	 * @param bool $leafOnly If true, returns only posting leaf accounts (master_account = 0)
	 * @return array Map of resolved account records indexed by account ID
	 */
	public function resolveConfiguredAccountTree($settingKeys, $leafOnly = true)
	{
		$settings = $this->getGLAccountsSettings();
		if (!is_array($settingKeys)) {
			$settingKeys = [$settingKeys];
		}

		// Preload chart of accounts hierarchy once
		if ($this->_gl_accounts_tree_cache === null) {
			$this->db->select('id, accountID, accountName, accountNameEn, accountCategory, accountCategorySub, master_account, level, parent_id, openingBalance, account_type');
			$this->db->from('gl_accounts');
			$this->db->where('accountStatus', 1);
			$q = $this->db->get();

			$this->_gl_accounts_tree_cache = [];
			$this->_gl_accounts_children_cache = [];
			if ($q && $q->num_rows() > 0) {
				foreach ($q->result_array() as $row) {
					$this->_gl_accounts_tree_cache[$row['id']] = $row;
					$this->_gl_accounts_children_cache[$row['parent_id']][] = $row['id'];
				}
			}
		}

		$resolved = [];
		$traverse = function($parentId, $settingKey) use (&$traverse, &$resolved, $leafOnly) {
			if (!isset($this->_gl_accounts_tree_cache[$parentId])) {
				return;
			}
			$acc = $this->_gl_accounts_tree_cache[$parentId];
			$is_leaf = ((int)$acc['master_account'] === 0);

			if (!$leafOnly || $is_leaf) {
				$resolved[$parentId] = array_merge($acc, ['configured_setting' => $settingKey]);
			}

			if (isset($this->_gl_accounts_children_cache[$parentId])) {
				foreach ($this->_gl_accounts_children_cache[$parentId] as $childId) {
					$cAcc = $this->_gl_accounts_tree_cache[$childId];
					if (!$leafOnly || ((int)$cAcc['master_account'] === 0)) {
						$resolved[$childId] = array_merge($cAcc, ['configured_setting' => $settingKey]);
					}
					$traverse($childId, $settingKey);
				}
			}
		};

		foreach ($settingKeys as $key) {
			$parentAccountID = (int)($settings[$key] ?? 0);
			if ($parentAccountID > 0) {
				$traverse($parentAccountID, $key);
			}
		}

		return $resolved;
	}

	/**
	 * Unified High-Performance GL Entries Aggregator
	 * Aggregates debits and credits from sma_gl_entries for any given period and cumulative balances
	 */
	public function getGLPeriodAggregatedData($from_date = null, $to_date = null, $warehouse_id = null)
	{
		$cache_key = ($from_date ?: 'all') . '_' . ($to_date ?: 'all') . '_' . ($warehouse_id ?: 'all');
		if (isset($this->_gl_dashboard_cache[$cache_key])) {
			return $this->_gl_dashboard_cache[$cache_key];
		}

		// 1. Fetch account metadata from sma_gl_accounts
		$accounts = [];
		$this->db->select('id, accountID, accountName, accountNameEn, accountCategory, accountCategorySub, master_account, account_type, openingBalance');
		$this->db->from('gl_accounts');
		$acc_query = $this->db->get();

		if ($acc_query && $acc_query->num_rows() > 0) {
			foreach ($acc_query->result() as $row) {
				$accounts[$row->id] = [
					'id' => (int)$row->id,
					'code' => $row->accountID,
					'name' => $row->accountName,
					'name_en' => $row->accountNameEn,
					'category' => trim((string)$row->accountCategory),
					'type' => $row->account_type,
					'master' => (int)$row->master_account,
					'opening' => (float)$row->openingBalance,
					'period_debit' => 0.0,
					'period_credit' => 0.0,
					'cum_debit' => 0.0,
					'cum_credit' => 0.0,
				];
			}
		}

		// 2. Fetch posted period entries from sma_gl_entries
		$this->db->select('entryDebitAccount, entryDebitBalance, entryCreditAccount, entryCreditBalance');
		$this->db->from('gl_entries');
		$this->db->where('entryStatus', 1);

		if ($from_date && $to_date) {
			$this->db->where('entryDate >=', $from_date);
			$this->db->where('entryDate <=', $to_date);
		} elseif ($from_date) {
			$this->db->where('entryDate >=', $from_date);
		} elseif ($to_date) {
			$this->db->where('entryDate <=', $to_date);
		}

		$period_query = $this->db->get();
		$period_entries_count = 0;
		$total_period_debit = 0.0;
		$total_period_credit = 0.0;

		if ($period_query && $period_query->num_rows() > 0) {
			foreach ($period_query->result() as $entry) {
				$period_entries_count++;
				$drAccs = json_decode($entry->entryDebitAccount, true);
				if (!is_array($drAccs)) { $drAccs = []; }
				$drBals = json_decode($entry->entryDebitBalance, true);
				if (!is_array($drBals)) { $drBals = []; }
				$crAccs = json_decode($entry->entryCreditAccount, true);
				if (!is_array($crAccs)) { $crAccs = []; }
				$crBals = json_decode($entry->entryCreditBalance, true);
				if (!is_array($crBals)) { $crBals = []; }

				$cntDr = min(count($drAccs), count($drBals));
				for ($i = 0; $i < $cntDr; $i++) {
					$accId = $drAccs[$i];
					$amt = (float)$drBals[$i];
					$total_period_debit += $amt;
					if (isset($accounts[$accId])) {
						$accounts[$accId]['period_debit'] += $amt;
					}
				}

				$cntCr = min(count($crAccs), count($crBals));
				for ($i = 0; $i < $cntCr; $i++) {
					$accId = $crAccs[$i];
					$amt = (float)$crBals[$i];
					$total_period_credit += $amt;
					if (isset($accounts[$accId])) {
						$accounts[$accId]['period_credit'] += $amt;
					}
				}
			}
		}

		// 3. Fetch cumulative entries up to $to_date for accurate Balance Sheet / Cash & Bank balances
		$this->db->select('entryDebitAccount, entryDebitBalance, entryCreditAccount, entryCreditBalance');
		$this->db->from('gl_entries');
		$this->db->where('entryStatus', 1);
		if ($to_date) {
			$this->db->where('entryDate <=', $to_date);
		}
		$cum_query = $this->db->get();

		if ($cum_query && $cum_query->num_rows() > 0) {
			foreach ($cum_query->result() as $entry) {
				$drAccs = json_decode($entry->entryDebitAccount, true);
				if (!is_array($drAccs)) { $drAccs = []; }
				$drBals = json_decode($entry->entryDebitBalance, true);
				if (!is_array($drBals)) { $drBals = []; }
				$crAccs = json_decode($entry->entryCreditAccount, true);
				if (!is_array($crAccs)) { $crAccs = []; }
				$crBals = json_decode($entry->entryCreditBalance, true);
				if (!is_array($crBals)) { $crBals = []; }

				$cntDr = min(count($drAccs), count($drBals));
				for ($i = 0; $i < $cntDr; $i++) {
					$accId = $drAccs[$i];
					$amt = (float)$drBals[$i];
					if (isset($accounts[$accId])) {
						$accounts[$accId]['cum_debit'] += $amt;
					}
				}

				$cntCr = min(count($crAccs), count($crBals));
				for ($i = 0; $i < $cntCr; $i++) {
					$accId = $crAccs[$i];
					$amt = (float)$crBals[$i];
					if (isset($accounts[$accId])) {
						$accounts[$accId]['cum_credit'] += $amt;
					}
				}
			}
		}

		// 4. Resolve dynamically configured Cash & Bank accounts from sma_gl_accounts_settings
		$configured_cash_bank = $this->resolveConfiguredAccountTree(['accounts_main_boxes_acct', 'accounts_main_banks_acct'], true);

		// 5. Compute Financial Categories based on leaf accounts
		$total_revenue = 0.0;
		$total_expenses = 0.0;
		$total_assets = 0.0;
		$total_liabilities = 0.0;
		$total_cash_bank = 0.0;

		$rev_list = [];
		$exp_list = [];
		$cash_bank_list = [];

		foreach ($accounts as $acc) {
			if ($acc['master'] == 1) {
				continue; // Process posting / leaf accounts only
			}

			// Revenue: Period Credit - Period Debit
			if ($acc['category'] === 'الإيرادات') {
				$net = (float)($acc['period_credit'] - $acc['period_debit']);
				$total_revenue += $net;
				if ($net > 0) {
					$rev_list[] = (object)[
						'id' => $acc['id'],
						'accountID' => $acc['code'],
						'accountName' => $acc['name'],
						'accountNameEn' => $acc['name_en'],
						'amount' => $net,
					];
				}
			}
			// Expenses: Period Debit - Period Credit
			elseif ($acc['category'] === 'المصروفات') {
				$net = (float)($acc['period_debit'] - $acc['period_credit']);
				$total_expenses += $net;
				if ($net > 0) {
					$exp_list[] = (object)[
						'id' => $acc['id'],
						'accountID' => $acc['code'],
						'accountName' => $acc['name'],
						'accountNameEn' => $acc['name_en'],
						'amount' => $net,
					];
				}
			}
			// Assets: Opening + Cumulative Debit - Cumulative Credit
			elseif ($acc['category'] === 'اصول') {
				$bal = (float)($acc['opening'] + $acc['cum_debit'] - $acc['cum_credit']);
				$total_assets += $bal;

				if (isset($configured_cash_bank[$acc['id']])) {
					$total_cash_bank += $bal;
					$settingKey = $configured_cash_bank[$acc['id']]['configured_setting'] ?? '';
					$cType = ($settingKey === 'accounts_main_banks_acct') ? 'bank' : 'cash';
					$cash_bank_list[] = (object)[
						'id' => $acc['id'],
						'accountID' => $acc['code'],
						'accountName' => $acc['name'],
						'accountNameEn' => $acc['name_en'],
						'account_type' => $cType,
						'balance' => $bal,
					];
				}
			}
			// Liabilities & Equity: Opening + Cumulative Credit - Cumulative Debit
			elseif ($acc['category'] === 'الإلتزامات وحقوق الملكية') {
				$bal = (float)($acc['opening'] + $acc['cum_credit'] - $acc['cum_debit']);
				$total_liabilities += $bal;
			}
		}

		$net_profit = $total_revenue - $total_expenses;
		$profit_margin = ($total_revenue > 0) ? round(($net_profit / $total_revenue) * 100, 2) : 0.0;

		// Sort Top Revenues Descending
		usort($rev_list, function($a, $b) {
			return ($b->amount < $a->amount) ? -1 : (($b->amount > $a->amount) ? 1 : 0);
		});
		$top_revenues = array_slice($rev_list, 0, 6);
		foreach ($top_revenues as &$item) {
			$item->pct = ($total_revenue > 0) ? round(((float)$item->amount / $total_revenue) * 100, 1) : 0.0;
		}

		// Sort Top Expenses Descending
		usort($exp_list, function($a, $b) {
			return ($b->amount < $a->amount) ? -1 : (($b->amount > $a->amount) ? 1 : 0);
		});
		$top_expenses = array_slice($exp_list, 0, 6);
		foreach ($top_expenses as &$item) {
			$item->pct = ($total_expenses > 0) ? round(((float)$item->amount / $total_expenses) * 100, 1) : 0.0;
		}

		// Sort Cash & Bank Descending
		usort($cash_bank_list, function($a, $b) {
			return ($b->balance < $a->balance) ? -1 : (($b->balance > $a->balance) ? 1 : 0);
		});
		$top_cash_bank = array_slice($cash_bank_list, 0, 6);

		$result = [
			'total_revenue' => $total_revenue,
			'total_expenses' => $total_expenses,
			'net_profit' => $net_profit,
			'profit_margin' => $profit_margin,
			'cash_bank_balance' => $total_cash_bank,
			'total_assets' => $total_assets,
			'total_liabilities' => $total_liabilities,
			'period_entries_count' => $period_entries_count,
			'total_period_debit' => $total_period_debit,
			'total_period_credit' => $total_period_credit,
			'top_revenues' => $top_revenues,
			'top_expenses' => $top_expenses,
			'cash_bank_balances' => $top_cash_bank,
		];

		$this->_gl_dashboard_cache[$cache_key] = $result;
		return $result;
	}

	public function getAccountingDashboardStats($from_date = null, $to_date = null, $warehouse_id = null)
	{
		$gl_data = $this->getGLPeriodAggregatedData($from_date, $to_date, $warehouse_id);

		// Receivables (Customers Outstanding)
		$this->db->select("COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as total_rec, COUNT(DISTINCT CASE WHEN (grand_total - paid) > 0 THEN customer_id END) as cust_due_count");
		$this->db->from('sales');
		if ($warehouse_id) {
			$this->db->where('warehouse_id', $warehouse_id);
		}
		if ($from_date && $to_date) {
			$this->db->where('date >=', $from_date);
			$this->db->where('date <=', $to_date);
		}
		$q_rec = $this->db->get();
		$rec_row = ($q_rec && $q_rec->num_rows() > 0) ? $q_rec->row() : null;
		$accounts_receivable = (float)($rec_row->total_rec ?? 0);
		$customers_due_count = (int)($rec_row->cust_due_count ?? 0);

		// Payables (Suppliers Outstanding)
		$this->db->select("COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as total_pay, COUNT(DISTINCT CASE WHEN (grand_total - paid) > 0 THEN supplier_id END) as supp_due_count");
		$this->db->from('purchases');
		if ($warehouse_id) {
			$this->db->where('warehouse_id', $warehouse_id);
		}
		if ($from_date && $to_date) {
			$this->db->where('date >=', $from_date);
			$this->db->where('date <=', $to_date);
		}
		$q_pay = $this->db->get();
		$pay_row = ($q_pay && $q_pay->num_rows() > 0) ? $q_pay->row() : null;
		$accounts_payable = (float)($pay_row->total_pay ?? 0);
		$suppliers_due_count = (int)($pay_row->supp_due_count ?? 0);

		return [
			'total_revenue' => (float)$gl_data['total_revenue'],
			'total_expenses' => (float)$gl_data['total_expenses'],
			'net_profit' => (float)$gl_data['net_profit'],
			'profit_margin' => (float)$gl_data['profit_margin'],
			'accounts_receivable' => $accounts_receivable,
			'accounts_payable' => $accounts_payable,
			'customers_due_count' => $customers_due_count,
			'suppliers_due_count' => $suppliers_due_count,
			'cash_bank_balance' => (float)$gl_data['cash_bank_balance'],
			'total_assets' => (float)$gl_data['total_assets'],
			'total_liabilities' => (float)$gl_data['total_liabilities'],
			'period_entries_count' => (int)$gl_data['period_entries_count'],
			'total_period_debit' => (float)$gl_data['total_period_debit'],
			'total_period_credit' => (float)$gl_data['total_period_credit'],
		];
	}

	public function getFinancialPositionSummary($from_date = null, $to_date = null, $warehouse_id = null)
	{
		$gl_data = $this->getGLPeriodAggregatedData($from_date, $to_date, $warehouse_id);

		$total_assets = (float)$gl_data['total_assets'];
		$total_liabilities = (float)$gl_data['total_liabilities'];
		$total_equity = $total_assets - $total_liabilities;
		if ($total_equity == 0 && $total_liabilities > 0) {
			$total_equity = $total_liabilities * 0.5;
		}

		$total_sum = max(1, ($total_assets + $total_liabilities));
		$assets_pct = round(($total_assets / $total_sum) * 100, 1);
		$liabilities_pct = round(($total_liabilities / $total_sum) * 100, 1);

		return [
			'total_assets' => $total_assets,
			'total_liabilities' => $total_liabilities,
			'total_equity' => $total_equity,
			'assets_pct' => $assets_pct,
			'liabilities_pct' => $liabilities_pct,
		];
	}

	public function getAccountingMonthlyTrends($limit = 12)
	{
		// Aggregate real monthly revenue and expenses from posted gl_entries or verified sales/purchases
		$this->db->select("
			DATE_FORMAT(date, '%Y-%m') as month,
			COALESCE(SUM(grand_total), 0) as revenue,
			COALESCE(SUM(paid), 0) as paid_revenue,
			COUNT(*) as count
		");
		$this->db->from('sales');
		$this->db->where('date IS NOT NULL');
		$this->db->group_by("DATE_FORMAT(date, '%Y-%m')");
		$this->db->order_by('month', 'DESC');
		$this->db->limit($limit);
		$sales_res = $this->db->get()->result();

		$this->db->select("
			DATE_FORMAT(date, '%Y-%m') as month,
			COALESCE(SUM(grand_total), 0) as expense,
			COALESCE(SUM(paid), 0) as paid_expense,
			COUNT(*) as count
		");
		$this->db->from('purchases');
		$this->db->where('date IS NOT NULL');
		$this->db->group_by("DATE_FORMAT(date, '%Y-%m')");
		$this->db->order_by('month', 'DESC');
		$this->db->limit($limit);
		$purchases_res = $this->db->get()->result();

		$months_map = [];
		if (!empty($sales_res)) {
			foreach ($sales_res as $sr) {
				$months_map[$sr->month] = [
					'month' => $sr->month,
					'revenue' => (float)$sr->revenue,
					'expense' => 0.0,
					'profit' => (float)$sr->revenue,
				];
			}
		}
		if (!empty($purchases_res)) {
			foreach ($purchases_res as $pr) {
				if (!isset($months_map[$pr->month])) {
					$months_map[$pr->month] = [
						'month' => $pr->month,
						'revenue' => 0.0,
						'expense' => (float)$pr->expense,
						'profit' => -(float)$pr->expense,
					];
				} else {
					$months_map[$pr->month]['expense'] = (float)$pr->expense;
					$months_map[$pr->month]['profit'] = $months_map[$pr->month]['revenue'] - (float)$pr->expense;
				}
			}
		}

		ksort($months_map);
		return array_values($months_map);
	}

	public function getTopExpenseAccounts($limit = 6, $from_date = null, $to_date = null, $warehouse_id = null)
	{
		$gl_data = $this->getGLPeriodAggregatedData($from_date, $to_date, $warehouse_id);
		return array_slice($gl_data['top_expenses'], 0, $limit);
	}

	public function getTopRevenueAccounts($limit = 6, $from_date = null, $to_date = null, $warehouse_id = null)
	{
		$gl_data = $this->getGLPeriodAggregatedData($from_date, $to_date, $warehouse_id);
		return array_slice($gl_data['top_revenues'], 0, $limit);
	}

	public function getCashAndBankBalances($limit = 6, $to_date = null, $warehouse_id = null)
	{
		$gl_data = $this->getGLPeriodAggregatedData(null, $to_date, $warehouse_id);
		return array_slice($gl_data['cash_bank_balances'], 0, $limit);
	}

	public function getTopReceivables($limit = 5, $from_date = null, $to_date = null, $warehouse_id = null)
	{
		$this->db->select("
			c.id as customer_id, c.name, c.company, c.phone,
			SUM(s.grand_total) as total_invoiced,
			SUM(s.paid) as total_paid,
			SUM(GREATEST(0, s.grand_total - s.paid)) as due_amount
		");
		$this->db->from('sales s');
		$this->db->join('companies c', 'c.id = s.customer_id', 'inner');
		if ($warehouse_id) {
			$this->db->where('s.warehouse_id', $warehouse_id);
		}
		if ($from_date && $to_date) {
			$this->db->where('s.date >=', $from_date);
			$this->db->where('s.date <=', $to_date);
		}
		$this->db->group_by('s.customer_id');
		$this->db->having('due_amount > 0');
		$this->db->order_by('due_amount', 'DESC');
		$this->db->limit($limit);
		$res = $this->db->get();
		return ($res && $res->num_rows() > 0) ? $res->result() : [];
	}

	public function getTopPayables($limit = 5, $from_date = null, $to_date = null, $warehouse_id = null)
	{
		$this->db->select("
			c.id as supplier_id, c.name, c.company, c.phone,
			SUM(p.grand_total) as total_purchased,
			SUM(p.paid) as total_paid,
			SUM(GREATEST(0, p.grand_total - p.paid)) as due_amount
		");
		$this->db->from('purchases p');
		$this->db->join('companies c', 'c.id = p.supplier_id', 'inner');
		if ($warehouse_id) {
			$this->db->where('p.warehouse_id', $warehouse_id);
		}
		if ($from_date && $to_date) {
			$this->db->where('p.date >=', $from_date);
			$this->db->where('p.date <=', $to_date);
		}
		$this->db->group_by('p.supplier_id');
		$this->db->having('due_amount > 0');
		$this->db->order_by('due_amount', 'DESC');
		$this->db->limit($limit);
		$res = $this->db->get();
		return ($res && $res->num_rows() > 0) ? $res->result() : [];
	}

	public function getAgedReceivables($from_date = null, $to_date = null, $warehouse_id = null)
	{
		$as_of_date = $to_date ? substr($to_date, 0, 10) : date('Y-m-d');
		$escaped_as_of = $this->db->escape($as_of_date);

		$due_expr = "GREATEST(0, s.grand_total - (s.paid + COALESCE(s.discount_allowance, 0)))";
		$due_date_expr = "COALESCE(s.due_date, DATE(s.date))";
		$diff_expr = "DATEDIFF($escaped_as_of, $due_date_expr)";

		$this->db->select("
			COALESCE(SUM(CASE WHEN $diff_expr <= 30 THEN $due_expr ELSE 0 END), 0) as b_0_30,
			COALESCE(SUM(CASE WHEN $diff_expr BETWEEN 31 AND 60 THEN $due_expr ELSE 0 END), 0) as b_31_60,
			COALESCE(SUM(CASE WHEN $diff_expr BETWEEN 61 AND 90 THEN $due_expr ELSE 0 END), 0) as b_61_90,
			COALESCE(SUM(CASE WHEN $diff_expr > 90 THEN $due_expr ELSE 0 END), 0) as b_90_plus
		", false);
		$this->db->from('sales s');
		$this->db->where("$due_expr >", 0);

		if ($warehouse_id) {
			$this->db->where('s.warehouse_id', (int)$warehouse_id);
		}
		if ($from_date && $to_date) {
			$this->db->where('s.date >=', $from_date);
			$this->db->where('s.date <=', $to_date);
		}

		$row = $this->db->get()->row();
		return [
			(float)($row->b_0_30 ?? 0),
			(float)($row->b_31_60 ?? 0),
			(float)($row->b_61_90 ?? 0),
			(float)($row->b_90_plus ?? 0),
		];
	}

	public function getAgedPayables($from_date = null, $to_date = null, $warehouse_id = null)
	{
		$as_of_date = $to_date ? substr($to_date, 0, 10) : date('Y-m-d');
		$escaped_as_of = $this->db->escape($as_of_date);

		$due_expr = "GREATEST(0, p.grand_total - (p.paid + COALESCE(p.discount_earned, 0)))";
		$due_date_expr = "COALESCE(p.due_date, DATE(p.date))";
		$diff_expr = "DATEDIFF($escaped_as_of, $due_date_expr)";

		$this->db->select("
			COALESCE(SUM(CASE WHEN $diff_expr <= 30 THEN $due_expr ELSE 0 END), 0) as b_0_30,
			COALESCE(SUM(CASE WHEN $diff_expr BETWEEN 31 AND 60 THEN $due_expr ELSE 0 END), 0) as b_31_60,
			COALESCE(SUM(CASE WHEN $diff_expr BETWEEN 61 AND 90 THEN $due_expr ELSE 0 END), 0) as b_61_90,
			COALESCE(SUM(CASE WHEN $diff_expr > 90 THEN $due_expr ELSE 0 END), 0) as b_90_plus
		", false);
		$this->db->from('purchases p');
		$this->db->where("$due_expr >", 0);

		if ($warehouse_id) {
			$this->db->where('p.warehouse_id', (int)$warehouse_id);
		}
		if ($from_date && $to_date) {
			$this->db->where('p.date >=', $from_date);
			$this->db->where('p.date <=', $to_date);
		}

		$row = $this->db->get()->row();
		return [
			(float)($row->b_0_30 ?? 0),
			(float)($row->b_31_60 ?? 0),
			(float)($row->b_61_90 ?? 0),
			(float)($row->b_90_plus ?? 0),
		];
	}

	public function getAccountingAttentionRequired($from_date = null, $to_date = null, $warehouse_id = null)
	{
		// Overdue Receivables
		$this->db->select("COUNT(*) as cnt, COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as amount");
		$this->db->from('sales');
		$this->db->where("(grand_total - paid) > 0");
		if ($warehouse_id) {
			$this->db->where('warehouse_id', $warehouse_id);
		}
		$rec = $this->db->get()->row();

		// Overdue Payables
		$this->db->select("COUNT(*) as cnt, COALESCE(SUM(GREATEST(0, grand_total - paid)), 0) as amount");
		$this->db->from('purchases');
		$this->db->where("(grand_total - paid) > 0");
		if ($warehouse_id) {
			$this->db->where('warehouse_id', $warehouse_id);
		}
		$pay = $this->db->get()->row();

		// Unposted Entries in Period
		$this->db->select("COUNT(*) as cnt");
		$this->db->from('gl_entries');
		$this->db->where('entryStatus', 0);
		if ($from_date && $to_date) {
			$this->db->where('entryDate >=', $from_date);
			$this->db->where('entryDate <=', $to_date);
		}
		$unposted = $this->db->get()->row();

		return [
			'overdue_receivables_count' => (int)($rec->cnt ?? 0),
			'overdue_receivables_amount' => (float)($rec->amount ?? 0),
			'overdue_payables_count' => (int)($pay->cnt ?? 0),
			'overdue_payables_amount' => (float)($pay->amount ?? 0),
			'unposted_entries_count' => (int)($unposted->cnt ?? 0),
		];
	}

	public function getRecentAccountingTransactions($limit = 8, $from_date = null, $to_date = null, $warehouse_id = null)
	{
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
		$this->db->select("
			e.entryID, e.entry_no, e.reference_no, e.entryDate, e.entryDescription, e.entryType, e.entryStatus,
			CONCAT(u.first_name, ' ', u.last_name) as creator_name
		");
		}else{
			$this->db->select("
			e.entryID, e.entryID as entry_no, e.reference_no, e.entryDate, e.entryDescription, e.entryType, e.entryStatus,
			CONCAT(u.first_name, ' ', u.last_name) as creator_name
		");
		}
		$this->db->from('gl_entries e');
		$this->db->join('users u', 'u.id = e.created_by', 'left');

		if ($from_date && $to_date) {
			$this->db->where('e.entryDate >=', $from_date);
			$this->db->where('e.entryDate <=', $to_date);
		} elseif ($from_date) {
			$this->db->where('e.entryDate >=', $from_date);
		} elseif ($to_date) {
			$this->db->where('e.entryDate <=', $to_date);
		}

		$this->db->order_by('e.entryID', 'DESC');
		$this->db->limit($limit);
		$res = $this->db->get();
		return ($res && $res->num_rows() > 0) ? $res->result() : [];
	}

	/**
	 * Cash Flow Statement Generator Using Accounting Settings (sma_gl_accounts_settings)
	 *
	 * Dynamically determines Cash and Bank accounts based on accounts_main_boxes_acct and
	 * accounts_main_banks_acct from sma_gl_accounts_settings without hardcoded codes or names.
	 * Accurately parses posted gl_entries (entryStatus = 1) with exact GL reconciliation.
	 *
	 * @param string|null $from_date Period start datetime (Y-m-d H:i:s)
	 * @param string|null $to_date Period end datetime (Y-m-d H:i:s)
	 * @param int|null $warehouse_id Optional warehouse/branch scope
	 * @return array
	 */
	public function getCashFlowStatement($from_date = null, $to_date = null, $warehouse_id = null)
	{
		// 1. Fetch accounting settings
		$accounts_settings = $this->site->getAccountsSettings();
		$boxes_parent_id = !empty($accounts_settings->accounts_main_boxes_acct) ? (int)$accounts_settings->accounts_main_boxes_acct : 0;
		$banks_parent_id = !empty($accounts_settings->accounts_main_banks_acct) ? (int)$accounts_settings->accounts_main_banks_acct : 0;
		$fixed_assets_parent_id = !empty($accounts_settings->accounts_fixed_assets_acct) ? (int)$accounts_settings->accounts_fixed_assets_acct : 0;
		$loans_acct_id = !empty($accounts_settings->accounts_loans_acct) ? (int)$accounts_settings->accounts_loans_acct : 0;
		$profits_stage_acct_id = !empty($accounts_settings->accounts_profits_stage_acct) ? (int)$accounts_settings->accounts_profits_stage_acct : 0;
		$losses_stage_acct_id = !empty($accounts_settings->accounts_losses_stage_acct) ? (int)$accounts_settings->accounts_losses_stage_acct : 0;

		// 2. Fetch all active accounts
		$accounts = [];
		$children_by_parent = [];
		$this->db->select('id, accountID, accountName, accountNameEn, accountCategory, accountCategorySub, master_account, level, parent_id, openingBalance, account_type');
		$this->db->from('gl_accounts');
		$this->db->where('accountStatus', 1);
		$acc_query = $this->db->get();

		if ($acc_query && $acc_query->num_rows() > 0) {
			foreach ($acc_query->result_array() as $row) {
				$accounts[$row['id']] = $row;
				$children_by_parent[$row['parent_id']][] = $row['id'];
			}
		}

		// Dynamically discover Cash Boxes, Banks, Fixed Assets, and Financing accounts
		$box_leafs = $this->resolveConfiguredAccountTree('accounts_main_boxes_acct', true);
		$bank_leafs = $this->resolveConfiguredAccountTree('accounts_main_banks_acct', true);
		$cash_bank_accounts = $box_leafs + $bank_leafs;
		$fixed_assets_leafs = $this->resolveConfiguredAccountTree('accounts_fixed_assets_acct', false);

		// Discover Financing Accounts from settings + chart of accounts hierarchy
		$financing_leafs = [];
		if ($loans_acct_id) {
			$financing_leafs += $this->resolveConfiguredAccountTree('accounts_loans_acct', false);
		}
		if ($profits_stage_acct_id) {
			$financing_leafs += $this->resolveConfiguredAccountTree('accounts_profits_stage_acct', false);
		}
		if ($losses_stage_acct_id) {
			$financing_leafs += $this->resolveConfiguredAccountTree('accounts_losses_stage_acct', false);
		}
		foreach ($accounts as $aId => $acc) {
			if ((int)$acc['master_account'] === 1 && $acc['accountCategory'] === 'الإلتزامات وحقوق الملكية') {
				if (mb_stripos($acc['accountName'], 'حقوق الملكية') !== false || mb_stripos($acc['accountName'], 'حقوق الشركاء') !== false || mb_stripos($acc['accountName'], 'قروض') !== false) {
					$financing_leafs += $this->resolveConfiguredAccountTree($aId, false);
					// Also traverse direct children if aId passed
					if (isset($children_by_parent[$aId])) {
						$traverseChild = function($pId) use (&$traverseChild, $accounts, $children_by_parent) {
							$res = [];
							if (isset($children_by_parent[$pId])) {
								foreach ($children_by_parent[$pId] as $cId) {
									$res[$cId] = array_merge($accounts[$cId], ['type' => 'equity']);
									$sub = $traverseChild($cId);
									foreach ($sub as $sId => $sAcc) {
										$res[$sId] = $sAcc;
									}
								}
							}
							return $res;
						};
						$financing_leafs += $traverseChild($aId);
					}
				}
			}
		}

		// 3. Compute Opening Cash Balance before $from_date
		$opening_cash = 0.0;
		$account_opening_balances = [];
		foreach ($cash_bank_accounts as $id => $acc) {
			$op = (float)$acc['openingBalance'];
			$opening_cash += $op;
			$account_opening_balances[$id] = $op;
		}

		if ($from_date) {
			$this->db->select('entryDebitAccount, entryDebitBalance, entryCreditAccount, entryCreditBalance');
			$this->db->from('gl_entries');
			$this->db->where('entryStatus', 1);
			$this->db->where('entryDate <', $from_date);
			$q_prior = $this->db->get();

			if ($q_prior && $q_prior->num_rows() > 0) {
				foreach ($q_prior->result() as $entry) {
					$drAccs = json_decode($entry->entryDebitAccount, true) ?: [];
					$drBals = json_decode($entry->entryDebitBalance, true) ?: [];
					$crAccs = json_decode($entry->entryCreditAccount, true) ?: [];
					$crBals = json_decode($entry->entryCreditBalance, true) ?: [];

					$cntDr = min(count($drAccs), count($drBals));
					for ($i = 0; $i < $cntDr; $i++) {
						$aId = (int)$drAccs[$i];
						if (isset($cash_bank_accounts[$aId])) {
							$amt = (float)$drBals[$i];
							$opening_cash += $amt;
							$account_opening_balances[$aId] = ($account_opening_balances[$aId] ?? 0.0) + $amt;
						}
					}

					$cntCr = min(count($crAccs), count($crBals));
					for ($i = 0; $i < $cntCr; $i++) {
						$aId = (int)$crAccs[$i];
						if (isset($cash_bank_accounts[$aId])) {
							$amt = (float)$crBals[$i];
							$opening_cash -= $amt;
							$account_opening_balances[$aId] = ($account_opening_balances[$aId] ?? 0.0) - $amt;
						}
					}
				}
			}
		}

		// 4. Process Period Entries (from_date <= entryDate <= to_date)
		if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1){
			$this->db->select('entryID, entry_no, entryDate, entryType, entryDebitAccount, entryDebitBalance, entryCreditAccount, entryCreditBalance');
		}else{
			$this->db->select('entryID, entryID as entry_no, entryDate, entryType, entryDebitAccount, entryDebitBalance, entryCreditAccount, entryCreditBalance');
		}
		$this->db->from('gl_entries');
		$this->db->where('entryStatus', 1);

		if ($from_date && $to_date) {
			$this->db->where('entryDate >=', $from_date);
			$this->db->where('entryDate <=', $to_date);
		} elseif ($from_date) {
			$this->db->where('entryDate >=', $from_date);
		} elseif ($to_date) {
			$this->db->where('entryDate <=', $to_date);
		}

		$this->db->order_by('entryDate', 'ASC');
		$period_query = $this->db->get();

		$operating_flow = 0.0;
		$investing_flow = 0.0;
		$financing_flow = 0.0;

		$operating_details = [];
		$investing_details = [];
		$financing_details = [];
		$account_period_movements = [];

		if ($period_query && $period_query->num_rows() > 0) {
			foreach ($period_query->result() as $entry) {
				$drAccs = json_decode($entry->entryDebitAccount, true) ?: [];
				$drBals = json_decode($entry->entryDebitBalance, true) ?: [];
				$crAccs = json_decode($entry->entryCreditAccount, true) ?: [];
				$crBals = json_decode($entry->entryCreditBalance, true) ?: [];

				$entry_cash_dr = 0.0;
				$entry_cash_cr = 0.0;
				$dr_lines = [];
				$cr_lines = [];

				$cntDr = min(count($drAccs), count($drBals));
				for ($i = 0; $i < $cntDr; $i++) {
					$aId = (int)$drAccs[$i];
					$amt = (float)$drBals[$i];
					$is_cb = isset($cash_bank_accounts[$aId]);
					$dr_lines[] = ['id' => $aId, 'amt' => $amt, 'is_cash' => $is_cb];
					if ($is_cb) {
						$entry_cash_dr += $amt;
						$account_period_movements[$aId] = ($account_period_movements[$aId] ?? 0.0) + $amt;
					}
				}

				$cntCr = min(count($crAccs), count($crBals));
				for ($i = 0; $i < $cntCr; $i++) {
					$aId = (int)$crAccs[$i];
					$amt = (float)$crBals[$i];
					$is_cb = isset($cash_bank_accounts[$aId]);
					$cr_lines[] = ['id' => $aId, 'amt' => $amt, 'is_cash' => $is_cb];
					if ($is_cb) {
						$entry_cash_cr += $amt;
						$account_period_movements[$aId] = ($account_period_movements[$aId] ?? 0.0) - $amt;
					}
				}

				// Skip non-cash journal entries
				if ($entry_cash_dr == 0.0 && $entry_cash_cr == 0.0) {
					continue;
				}

				// Case A: Net Cash Inflow (entry_cash_dr > entry_cash_cr)
				if ($entry_cash_dr > 0) {
					$non_cash_cr_total = 0.0;
					foreach ($cr_lines as $crl) {
						if (!$crl['is_cash']) {
							$non_cash_cr_total += $crl['amt'];
						}
					}
					if ($non_cash_cr_total > 0) {
						$inflow = max(0.0, $entry_cash_dr - $entry_cash_cr);
						foreach ($cr_lines as $crl) {
							if (!$crl['is_cash']) {
								$ratio = $crl['amt'] / $non_cash_cr_total;
								$amt = $inflow * $ratio;
								$this->_classifyCashFlowActivity(
									$crl['id'], $amt, $accounts, $fixed_assets_leafs, $financing_leafs,
									$operating_flow, $investing_flow, $financing_flow,
									$operating_details, $investing_details, $financing_details
								);
							}
						}
					}
				}

				// Case B: Net Cash Outflow (entry_cash_cr > entry_cash_dr)
				if ($entry_cash_cr > 0) {
					$non_cash_dr_total = 0.0;
					foreach ($dr_lines as $drl) {
						if (!$drl['is_cash']) {
							$non_cash_dr_total += $drl['amt'];
						}
					}
					if ($non_cash_dr_total > 0) {
						$outflow = max(0.0, $entry_cash_cr - $entry_cash_dr);
						foreach ($dr_lines as $drl) {
							if (!$drl['is_cash']) {
								$ratio = $drl['amt'] / $non_cash_dr_total;
								$amt = -$outflow * $ratio;
								$this->_classifyCashFlowActivity(
									$drl['id'], $amt, $accounts, $fixed_assets_leafs, $financing_leafs,
									$operating_flow, $investing_flow, $financing_flow,
									$operating_details, $investing_details, $financing_details
								);
							}
						}
					}
				}
			}
		}

		$net_change_in_cash = $operating_flow + $investing_flow + $financing_flow;
		$closing_cash = $opening_cash + $net_change_in_cash;

		// 5. Compute Independent GL Cumulative Closing Balance up to $to_date
		$gl_cumulative_movements = [];
		$this->db->select('entryDebitAccount, entryDebitBalance, entryCreditAccount, entryCreditBalance');
		$this->db->from('gl_entries');
		$this->db->where('entryStatus', 1);
		if ($to_date) {
			$this->db->where('entryDate <=', $to_date);
		}
		$cum_query = $this->db->get();

		if ($cum_query && $cum_query->num_rows() > 0) {
			foreach ($cum_query->result() as $entry) {
				$drAccs = json_decode($entry->entryDebitAccount, true) ?: [];
				$drBals = json_decode($entry->entryDebitBalance, true) ?: [];
				$crAccs = json_decode($entry->entryCreditAccount, true) ?: [];
				$crBals = json_decode($entry->entryCreditBalance, true) ?: [];

				$cntDr = min(count($drAccs), count($drBals));
				for ($i = 0; $i < $cntDr; $i++) {
					$aId = (int)$drAccs[$i];
					if (isset($cash_bank_accounts[$aId])) {
						$gl_cumulative_movements[$aId] = ($gl_cumulative_movements[$aId] ?? 0.0) + (float)$drBals[$i];
					}
				}

				$cntCr = min(count($crAccs), count($crBals));
				for ($i = 0; $i < $cntCr; $i++) {
					$aId = (int)$crAccs[$i];
					if (isset($cash_bank_accounts[$aId])) {
						$gl_cumulative_movements[$aId] = ($gl_cumulative_movements[$aId] ?? 0.0) - (float)$crBals[$i];
					}
				}
			}
		}

		$gl_actual_closing_total = 0.0;
		$cash_accounts_table = [];
		foreach ($cash_bank_accounts as $id => $acc) {
			$op = $account_opening_balances[$id] ?? (float)$acc['openingBalance'];
			$mov = $account_period_movements[$id] ?? 0.0;
			$close = $op + $mov;

			$gl_cum = (float)$acc['openingBalance'] + ($gl_cumulative_movements[$id] ?? 0.0);
			$gl_actual_closing_total += $gl_cum;

			$cash_accounts_table[] = [
				'id' => $id,
				'accountID' => $acc['accountID'],
				'accountName' => $acc['accountName'],
				'accountNameEn' => $acc['accountNameEn'],
				'type' => $acc['cash_type'] ?? 'cash',
				'opening' => $op,
				'movement' => $mov,
				'closing' => $close,
				'gl_closing' => $gl_cum,
			];
		}

		// Sort items by absolute net amount descending
		$sortDesc = function(&$arr) {
			usort($arr, function($a, $b) {
				return (abs($b['net']) <=> abs($a['net']));
			});
		};
		$sortDesc($operating_details);
		$sortDesc($investing_details);
		$sortDesc($financing_details);

		// Calculate subtotals for UI
		$sumFlows = function($details) {
			$inflow = 0.0;
			$outflow = 0.0;
			$net = 0.0;
			foreach ($details as $d) {
				$inflow += $d['inflow'];
				$outflow += $d['outflow'];
				$net += $d['net'];
			}
			return ['inflow' => $inflow, 'outflow' => $outflow, 'net' => $net];
		};

		$op_sums = $sumFlows($operating_details);
		$inv_sums = $sumFlows($investing_details);
		$fin_sums = $sumFlows($financing_details);

		$reconciliation_difference = $closing_cash - $gl_actual_closing_total;

		return [
			'opening_cash' => $opening_cash,
			'operating' => [
				'items' => array_values($operating_details),
				'inflow' => $op_sums['inflow'],
				'outflow' => $op_sums['outflow'],
				'net' => $op_sums['net'],
			],
			'investing' => [
				'items' => array_values($investing_details),
				'inflow' => $inv_sums['inflow'],
				'outflow' => $inv_sums['outflow'],
				'net' => $inv_sums['net'],
			],
			'financing' => [
				'items' => array_values($financing_details),
				'inflow' => $fin_sums['inflow'],
				'outflow' => $fin_sums['outflow'],
				'net' => $fin_sums['net'],
			],
			'net_change' => $net_change_in_cash,
			'closing_cash' => $closing_cash,
			'cash_accounts' => $cash_accounts_table,
			'reconciliation' => [
				'gl_balance' => $gl_actual_closing_total,
				'report_balance' => $closing_cash,
				'difference' => $reconciliation_difference,
				'is_matched' => (abs($reconciliation_difference) < 0.001),
			],
		];
	}

	/**
	 * Helper method to classify cash flow activity based on pre-indexed settings & hierarchy
	 */
	protected function _classifyCashFlowActivity(
		$accId, $amt, $accounts, $fixed_assets_leafs, $financing_leafs,
		&$operating_flow, &$investing_flow, &$financing_flow,
		&$operating_details, &$investing_details, &$financing_details
	) {
		$acc = $accounts[$accId] ?? null;
		if (!$acc) return;

		$is_investing = isset($fixed_assets_leafs[$accId]);
		$is_financing = isset($financing_leafs[$accId]);

		if ($is_investing) {
			$investing_flow += $amt;
			$this->_appendCashFlowDetailItem($investing_details, $acc, $amt);
		} elseif ($is_financing) {
			$financing_flow += $amt;
			$this->_appendCashFlowDetailItem($financing_details, $acc, $amt);
		} else {
			$operating_flow += $amt;
			$this->_appendCashFlowDetailItem($operating_details, $acc, $amt);
		}
	}

	/**
	 * Helper to add or accumulate an account's inflow/outflow item in cash flow section
	 */
	protected function _appendCashFlowDetailItem(&$details, $acc, $amt)
	{
		$id = (int)$acc['id'];
		if (!isset($details[$id])) {
			$details[$id] = [
				'id' => $id,
				'code' => $acc['accountID'],
				'name' => $acc['accountName'],
				'name_en' => $acc['accountNameEn'],
				'category' => $acc['accountCategory'],
				'inflow' => 0.0,
				'outflow' => 0.0,
				'net' => 0.0,
			];
		}
		if ($amt >= 0) {
			$details[$id]['inflow'] += $amt;
		} else {
			$details[$id]['outflow'] += abs($amt);
		}
		$details[$id]['net'] += $amt;
	}

}



