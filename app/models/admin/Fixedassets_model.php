<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fixedassets_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getGroupAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('ast_group');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addGroup($data)
    {
        if ($this->db->insert('ast_group', $data)) {
            return true;
        }
        return false;
    }

    public function getGroupByID($id)
    {
        $q = $this->db->get_where('ast_group', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateGroup($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('ast_group', $data)) {
            return true;
        }
        return false;
    }

    public function deleteGroup($id)
    {
        if ($this->db->delete('ast_group', ['id' => $id])) {
            return true;
        }
        return false;
    }

     public function getParentClassesAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('ast_parent_classes');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addParentClasses($data)
    {
        if ($this->db->insert('ast_parent_classes', $data)) {
            return true;
        }
        return false;
    }

    public function getParentClassesByID($id)
    {
        $q = $this->db->get_where('ast_parent_classes', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateParentClasses($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('ast_parent_classes', $data)) {
            return true;
        }
        return false;
    }

    public function deleteParentClasses($id)
    {
        if ($this->db->delete('ast_parent_classes', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getParentClasses()
    {
        $q = $this->db->get('ast_parent_classes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getSubClassesAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('ast_sub_classes');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addSubClasses($data)
    {
        if ($this->db->insert('ast_sub_classes', $data)) {
            return true;
        }
        return false;
    }

    public function getSubClassesByID($id)
    {
        $q = $this->db->get_where('ast_sub_classes', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateSubClasses($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('ast_sub_classes', $data)) {
            return true;
        }
        return false;
    }

    public function deleteSubClasses($id)
    {
        if ($this->db->delete('ast_sub_classes', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getGroups()
    {
        $q = $this->db->get('ast_group');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function addFixedAsset($data)
    {
        if ($this->db->insert('ast_fixed_assets', $data)) {
            return true;
        }
        return false;
    }

    public function getFixedAssetByID($id)
    {
        $q = $this->db->get_where('ast_fixed_assets', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateFixedAsset($id,$data)
    {
        if ($this->db->update('ast_fixed_assets', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getSubClassesByParentId($parent_id)
    {
        $this->db->select('id as id, name as text')
        ->where('parent_id', $parent_id)->order_by('name');
        $q = $this->db->get('ast_sub_classes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function delete_fixed_asset($id)
    {
        if ($this->db->delete('ast_fixed_assets', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function addDepreciations($data)
    {
        if ($this->db->insert_batch('ast_depreciations', $data)) {
            return true;
        }
        return false;
    }

    public function deleteDepreciations($fixed_asset_id)
    {
        if ($this->db->delete('ast_depreciations', ['fixed_asset_id' => $fixed_asset_id])) {
            return true;
        }
        return false;
    }

    public function getFixedAssets($id = null, $group_id = null)
	{
		$this->db->select('id,code,name');

		$this->db->where('active', 1);
		if ($id) {
			$this->db->where('id', $id);
		}
        if ($group_id) {
            $this->db->where('group_id', $group_id);
		}
        $this->db->order_by('id', 'asc');
		$q = $this->db->get('ast_fixed_assets');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {

					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

    public function getDepreciationsByFAId($fixed_asset_id, $startDate = NULL, $endDate = NULL)
	{


			$this->db->where('fixed_asset_id', $fixed_asset_id);


        if ($startDate && $endDate) {

			$this->db->where('period_date BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}

        $this->db->order_by('id', 'asc');
		$q = $this->db->get('ast_depreciations');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {

					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

    public function getValuationsByFAId($fixed_asset_id, $startDate = NULL, $endDate = NULL)
	{

        $this->db->select('Max(start_value) as start_value,Min(end_value) as end_value,(Max(start_value) - Min(end_value)) as accumulated_value');

        $this->db->where('fixed_asset_id', $fixed_asset_id);


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

    public function getAllYearFromDepreciations()
	{
		//$this->db->select('period_year');
		$this->db->distinct();
		$this->db->select('Year(period_date) as period_year');
		$this->db->order_by('Year(period_date)', 'desc');

		$q = $this->db->get('ast_depreciations');
		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {
				$data[] = $row;
			}
			return $data;
		}
		return false;
	}

    public function getRevaluationsAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('ast_revaluations');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addRevaluation($data)
    {
        if ($this->db->insert('ast_revaluations', $data)) {
            return true;
        }
        return false;
    }

    public function getRevaluationByID($id)
    {
        $q = $this->db->get_where('ast_revaluations', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateRevaluation($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('ast_revaluations', $data)) {
            return true;
        }
        return false;
    }

    public function deleteRevaluation($id)
    {
        if ($this->db->delete('ast_revaluations', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getIncreasingAutoCode()
    {

		$this->db->select('COALESCE(max(id+1),1) as id');

		$q = $this->db->get('ast_increasing');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function addIncreasing($data)
    {
        if ($this->db->insert('ast_increasing', $data)) {
            return true;
        }
        return false;
    }

    public function getIncreasingByID($id)
    {
        $q = $this->db->get_where('ast_increasing', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function updateIncreasing($id, $data = [])
    {
        $this->db->where('id', $id);
        if ($this->db->update('ast_increasing', $data)) {
            return true;
        }
        return false;
    }

    public function deleteIncreasing($id)
    {
        if ($this->db->delete('ast_increasing', ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getIncreasingByFAId($fixed_asset_id, $increasing_type = NULL, $startDate = NULL, $endDate = NULL)
	{


			$this->db->where('fixed_asset_id', $fixed_asset_id);


        if ($startDate && $endDate) {

			$this->db->where('period_date BETWEEN "' . $startDate . '" and "' . $endDate . '"');
		}

        if ($increasing_type) {

			$this->db->where('increasing_type', $increasing_type);
		}

        $this->db->order_by('id', 'asc');
		$q = $this->db->get('ast_increasing');

		if ($q->num_rows() > 0) {
			foreach (($q->result()) as $row) {

					$data[] = $row;
			}
			return $data;
		}
		return false;
	}

    public function addIncreasingPayment($data = [], $payment_type = null)
    {
        if ($this->db->insert('ast_increasing_payments', $data)) {
            if ($this->site->getReference('aipay',$data['brId'] ? $data['brId']  : null) == $data['reference_no']) {
                $this->site->updateReference('aipay',$data['brId'] ? $data['brId']  : null);
            }
            $this->site->syncIncreasingPayments($data['increasing_id']);





			if ($this->Settings->accounting){

                $increasing      = $this->getIncreasingByID($data['increasing_id']);
                $fixed_asset_details = $this->getFixedAssetByID($increasing->fixed_asset_id);


			$entryDescription="Payments_increasing_asset#".$fixed_asset_details->code;

			//$increasing = $this->getIncreasingByID($data['increasing_id']);

                $order_tax =0;
                if($increasing->tax!=0){
                    $order_tax_id = $increasing->tax_id;
                    if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                        if ($order_tax_details->type == 2) {
                            $order_tax = $order_tax_details->rate;
                        }
                        if ($order_tax_details->type == 1) {
                            $order_tax = (($data['amount']) * $order_tax_details->rate) / 100;
                        }
                    }
                }

				$entry_type="Payments_increasing_asset";




					$entryDebitAccount= [];
					$entryDebitBalance= [];


					//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						$entryDebitAcc=$data['accountID'];

					 $entryDebitAccount[] =  $entryDebitAcc;




                    $entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']-$order_tax);
                if($order_tax!=0){
                    $entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
					$entryDebitBalance[] =  $this->sma->formatDecimal($order_tax);
                }



					$commisson_amount=0;



					$entryCreditAccount= [];
					//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
					$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
						if($entryCreditAcc=="")
						$entryCreditAcc=$payment_type;
					 	$entryCreditAccount[] =  $entryCreditAcc;




					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);











					if(isset($this->Settings->isGrouping) && $this->Settings->isGrouping==1)
					{

						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription,$data['grpId'],$data['coId'],$data['brId']);

					}else{
						$this->site->createEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null,$data['date'],$entryDescription);
					}






			}


            return true;
        }
        return false;
    }

    public function getIncreasingPayments($increasing_id)
    {
        $q = $this->db->get_where('ast_increasing_payments', ['increasing_id' => $increasing_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getIncreasingPaymentByID($id)
    {
        $q = $this->db->get_where('ast_increasing_payments', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function updateIncreasingPayment($id, $data = [], $payment_type = null)
    {
        if ($this->db->update('ast_increasing_payments', $data, ['id' => $id])) {
            $this->site->syncIncreasingPayments($data['increasing_id']);

            if ($this->Settings->accounting){

                $increasing      = $this->getIncreasingByID($data['increasing_id']);
                $fixed_asset_details = $this->getFixedAssetByID($increasing->fixed_asset_id);


			$entryDescription="Payments_increasing_asset#".$fixed_asset_details->code;

			//$increasing = $this->getIncreasingByID($data['increasing_id']);

                $order_tax =0;
                if($increasing->tax!=0){
                    $order_tax_id = $increasing->tax_id;
                    if ($order_tax_details = $this->site->getTaxRateByID($order_tax_id)) {
                        if ($order_tax_details->type == 2) {
                            $order_tax = $order_tax_details->rate;
                        }
                        if ($order_tax_details->type == 1) {
                            $order_tax = (($data['amount']) * $order_tax_details->rate) / 100;
                        }
                    }
                }

				$entry_type="Payments_increasing_asset";




					$entryDebitAccount= [];
					$entryDebitBalance= [];


					//$entryDebitAccount[] =  $this->site->getAccountByRefId($purchase->warehouse_id,'warehouses')->id;
						$entryDebitAcc=$data['accountID'];

					 $entryDebitAccount[] =  $entryDebitAcc;




                    $entryDebitBalance[] =  $this->sma->formatDecimal($data['amount']-$order_tax);
                if($order_tax!=0){
                    $entryDebitAccount[] =  $this->site->getAccountsSettings()->purchases_vat_acct;
					$entryDebitBalance[] =  $this->sma->formatDecimal($order_tax);
                }



					$commisson_amount=0;



					$entryCreditAccount= [];
					//$entryCreditAccount[] =  $this->site->getPaymentTypeByID($payment_type)->payment_account;
					$entryCreditAcc=$this->site->getPaymentTypeByID($payment_type)->payment_account;
						if($entryCreditAcc=="")
						$entryCreditAcc=$payment_type;
					 	$entryCreditAccount[] =  $entryCreditAcc;




					$entryCreditBalance= [];
					$entryCreditBalance[] =  $this->sma->formatDecimal($data['amount']+$commisson_amount);












						$this->site->updateEntryMultiSide($entryDebitAccount,$entryCreditAccount,$entryDebitBalance,$entryCreditBalance,$data['reference_no'],$entry_type,null,null,null,null);







			}

            return true;
        }
        return false;
    }

    public function deleteIncreasingPayment($id)
    {
        $opay = $this->getIncreasingPaymentByID($id);
        if ($this->db->delete('ast_increasing_payments', ['id' => $id])) {
            $this->site->syncIncreasingPayments($opay->increasing_id);
            return true;
        }
        return false;
    }

    public function getDepreciationByID($fixed_asset_id, $period_date)
    {
        $q = $this->db->get_where('ast_depreciations', ['fixed_asset_id' => $fixed_asset_id,'period_date' => $period_date], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return false;
    }

    public function deleteDepreciationsv2($fixed_asset_id, $period_date)
    {
        $this->db->where('period_date >= "' . $period_date . '"');
        if ($this->db->delete('ast_depreciations', ['fixed_asset_id' => $fixed_asset_id])) {
            return true;
        }
        return false;
    }

    public function getDepreciationsByYear($startDate,$endDate)
	{

		$startDate = $startDate.' 00:00:00';
        $endDate = $endDate.' 23:59:58';
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

}
