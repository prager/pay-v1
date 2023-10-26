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

}