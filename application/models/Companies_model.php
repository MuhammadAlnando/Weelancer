<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// application/models/Companies_model.php

// application/models/Companies_model.php

class Companies_model extends CI_Model {

  public function __construct() {
      parent::__construct();
      $this->load->database();
  }

  public function get_all_companies() {
      // Mengambil semua data perusahaan
      $query = $this->db->get('companies');
      return $query->result();
  }

  public function delete_company($id) {
      // Menghapus perusahaan berdasarkan ID
      return $this->db->delete('companies', array('id' => $id));
  }

  public function get_companies_titles() {
      // Mengambil judul perusahaan
      $this->db->select('id, title');
      $query = $this->db->get('companies');
      return $query->result();
  }

  public function get_companies_count() {
    return $this->db->count_all('companies');
}
}
