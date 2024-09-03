<?php
class Manager_model extends CI_Model {
	
    function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function get_member($id) {

		$this->db->select('*');
		if($id != 0) {
			$this->db->where('id_members', $id);
			
		}
		else {
			$this->db->where('lname', 'NullMember');
		}
		$memData = $this->db->get('tMembers')->row();
		$retval['member'] = $memData;		
		$retval['cur_year'] = $memData->cur_year;
		return $retval;
    }

	public function get_paydata($valStr) {
		$retarr['mdarc_mem'] = 0;
		$retarr['renewal'] = 0;
		$retarr['new_mem'] = 0;
		$retarr['mdarc_donation'] = 0;
		$retarr['repeater_donation'] = 0;
		$retarr['carrier'] = 0;
		$retarr['id_member'] = 0;
		$this->db->select('*');
		$this->db->where('val_string', $valStr);
		$this->db->where('flag', 1);
		$this->db->from('mem_payments');
		$cnt = $this->db->count_all_results();
		if($cnt > 0){
			$retarr['id_member'] = $this->db->get('mem_payments')->last_row()->id_member;
			$this->db->select('*');
			$this->db->where('val_string', $valStr);
			$payments = $this->db->get('mem_payments')->result();
			foreach($payments as $payment) {
				$paym = $payment->amount;
				if($payment->id_payaction == 1) {
					$retarr['renewal'] = $paym;
				}
				if($payment->id_payaction == 2 || $payment->id_payaction == 11) {
					$retarr['new_mem'] = $paym;
				}
				if($payment->id_payaction == 5) {
					$retarr['repeater_donation'] = $paym;
				}
				if($payment->id_payaction == 7) {
					$retarr['mdarc_donation'] = $paym;
				}
				if($payment->id_payaction == 10) {
					$retarr['carrier'] = $paym;
				}
				if($payment->id_payaction == 12) {
					$retarr['public_renew'] = $paym;
				}
			}
		}
		return $retarr;
	}
	public function save_paydata($param) {
		$this->db->select('*');
		$this->db->where('val_string', $param['idStr']);
		$this->db->where('flag', 1);
		$id_member = $this->db->get('mem_payments')->first_row()->id_member;
		if($param['action'] == 'renewal' || $param['action'] == 'new_mem' || $param['action'] == 'public_renew') {
			if($param['carrVal'] > 0) {
				$data = array('cur_year' => $param['cur_year'], 'paym_date' => time(), 'hard_news' => 'TRUE');
			}
			else {
				$data = array('cur_year' => $param['cur_year'], 'paym_date' => time());
			}
			$this->db->where('id_members', $id_member);
			$this->db->update('tMembers', $data);
		}

		$data = array('flag' => 0, 'result' => $param['status']);
		$this->db->where('val_string', $param['idStr']);
		$this->db->update('mem_payments', $data);
	}
	public function reset_flags(){
		$this->db->select('*');
		$this->db->where('paydate <', time() - 20);
		$this->db->where('flag', 1);
		$this->db->where('result', 'none_yet');
		$this->db->update('mem_payments', array('flag'=> 0, 'result' => 'timed_out'));
	}
}