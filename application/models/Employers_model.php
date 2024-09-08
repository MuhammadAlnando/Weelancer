<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Employers_model extends CI_Model {

  // Μαζί με το model φορτώνουμε την database
  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  // ###########################################################
  // ######################### SEARCHES ########################
  // ###########################################################

  // Εύρεση Εργοδοτών
  public function select_all() {
    $query = $this->db->get('employers');
    return $query->result_array();
  }

  // Εύρεση Εργοδοτών Εταιρίας
  public function select_company($id) {
    $query = $this->db->get_where('employers', array('company_id' => $id));
    return $query->result_array();
  }

  // Εύρεση Εργοδότη με βάση το ID
  public function search($id) {
    $query = $this->db->get_where('employers', array('id' => $id));
    return $query->row_array();
  }

  // Εύρεση Εργοδότη με το email
  public function search_by_email($email) {
    $query = $this->db->get_where('employers', array('email' => $email));
    return $query->row_array();
  }

  // Αναζήτηση Email Εργοδότη
  public function check_email($email){
    $query = $this->db->get_where('employers', array('email' => $email));
    
    if(empty($query->row_array())){
      return true;
    } else {
      return false;
    }
  }

  // ########################################################
  // ######################### LOGIN ########################
  // ########################################################

  // Σύνδεση Εργοδότη
  public function login($employer) {
    $query = $this->db->get_where('employers', array('email' => $employer['email']));
    $search = $query->row_array();

    // Αν το query δεν επιστρέφει αποτελέσματα τότε ο εργοδότης δεν υπάρχει
    if($query->num_rows = 0) {
      $result = array(
        'user_id' => 0,
        'message' => 'error'
      );

      return $result;
    } else {
      // Έλεγχος αν οι κωδικοί ταιριάζουν
      if(password_verify($employer['password'], $search['password'])) {
        if($search['active'] == 0) {
          $result = array(
            'user_id' => $search['id'],
            'message' => 'error_active'
          );
        } else {
          $result = array(
            'user_id' => $search['id'],
            'message' => 'success'
          );
        }
      } else {
        $result = array(
          'user_id' => 0,
          'message' => 'error'
        );
      }
      
      return $result;
    }
  }

  // ############################################################
  // #################### ADD, UPDATE, DELETE ###################
  // ############################################################

  // Δημιουργία Εργοδότη
  public function add($employer) {
    // Set 'active' menjadi 1 agar akun langsung aktif
    $employer['active'] = 1;
    
    // Insert employer data ke dalam database
    $this->db->insert('employers', $employer);

    if($this->db->affected_rows() > 0) {
        return 'success';
    } else {
        return 'error';
    }
}

  // Επεξεργασία Εργοδότη
  public function update($employer) {
    $this->db->where('id', $employer['id']);
    $this->db->update('employers', $employer);

    if($this->db->affected_rows() > 0) {
      return 'success';
    } else {
      return 'error';
    }
  }

  // Διαγραφή Εργοδότη
  public function delete($id) {
    $this->db->where('id', $id);
    $this->db->delete('employers');
  }

  public function get_employers_count() {
    return $this->db->count_all('employers');
}

public function get_all_employers() {
  $query = $this->db->get('employers');
  return $query->result(); // Fetches all rows as an array of objects
}

public function get_employer_by_id($id) {
  return $this->db->get_where('employers', array('id' => $id))->row();
}

public function update_employer($id, $data) {
  $this->db->where('id', $id);
  return $this->db->update('employers', $data);
}

public function delete_employer($id) {
  // Hapus employer berdasarkan ID
  $this->db->where('id', $id);
  return $this->db->delete('employers'); // Tabel employers
}




  // Ενεργοποίηση Λογαριασμού (update active status)
  public function activation($employer) {
    $this->db->where('email', $employer['email']);
    $this->db->update('employers', array('active' => 1));

    if($this->db->affected_rows() > 0) {
      return 'success';
    } else {
      return 'error';
    }
  }
  
}
