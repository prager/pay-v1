<?php
class Manager_model extends CI_Model {
	
    function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function get_member($id) {
        $this->db->select('*');
	    $this->db->where('id_members', $id);
		return $this->db->get('tMembers')->row();
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
		$payments = $this->db->get('mem_payments')->result();
		foreach($payments as $payment) {
			if($payment->id_payaction == 1) {
				$retarr['renewal'] = $payment->amount;
			}
			if($payment->id_payaction == 5) {
				$retarr['repeater_donation'] = $payment->amount;
			}
			if($payment->id_payaction == 7) {
				$retarr['mdarc_donation'] = $payment->amount;
			}
			if($payment->id_payaction == 10) {
				$retarr['carrier'] = $payment->amount;
			}
		}
		return $retarr;
	}
}