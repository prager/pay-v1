<?php
class Manager_model extends CI_Model {
	
    function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function get_member($id) {
        $this->db->select('*');
	    $this->db->where('id_members', $id);
		$memData = $this->db->get('tMembers')->row();
		$retval['member'] = $memData;
		if($memData->cur_year < intval(date('Y', time()))){
			if(intval(date('m', time())) > 9){ 
				$retval['cur_year'] = date('Y', time()) + 1;
			} else {
				$retval['cur_year'] = date('Y', time());
			}
		}
		else {
			$retval['cur_year'] = $memData->cur_year + 1;
		}
		return $retval;
    }

	public function get_paydata($valStr) {
		$retarr['mdarc_mem'] = 0;
		$retarr['renewal'] = 0;
		$retarr['mdarc_donation'] = 0;
		$retarr['repeater_donation'] = 0;
		$retarr['carrier'] = 0;
		$this->db->select('*');
		$this->db->where('val_string', $valStr);
		$retarr['id_member'] = $this->db->get('mem_payments')->first_row()->id_member;

		$this->db->select('*');
		$this->db->where('val_string', $valStr);
		$payments = $this->db->get('mem_payments')->result();
		foreach($payments as $payment) {
			$paym = $payment->amount;
			if($payment->id_payaction == 1) {
				$retarr['renewal'] = $paym;
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
		}
		return $retarr;
	}
	public function save_paydata($param) {
		$this->db->select('*');
		$this->db->where('val_string', $param['idStr']);
		$id_member = $this->db->get('mem_payments')->first_row()->id_member;
		if($param['action'] == 'renewal/') {
			$data = array('cur_year' => $param['cur_year'], 'paym_date' => time());
			$this->db->where('id_members', $id_member);
			$this->db->update('tMembers', $data);
		}

		$data = array('flag' => 0, 'result' => 'success');
		$this->db->where('val_string', $param['idStr']);
		$this->db->update('mem_payments', $data);
	}
}