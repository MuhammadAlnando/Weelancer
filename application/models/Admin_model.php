<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

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
    $query = $this->db->get('admin');
    return $query->result_array();
  }

  // Εύρεση Εργοδοτών Εταιρίας
  public function select_company($id) {
    $query = $this->db->get_where('admin', array('company_id' => $id));
    return $query->result_array();
  }

  // Εύρεση Εργοδότη με βάση το ID
  public function search($id) {
    $query = $this->db->get_where('admin', array('id' => $id));
    return $query->row_array();
  }

  // Εύρεση Εργοδότη με το email
  public function search_by_email($email) {
    $query = $this->db->get_where('admin', array('email' => $email));
    return $query->row_array();
  }

  // Αναζήτηση Email Εργοδότη
  public function check_email($email){
    $query = $this->db->get_where('admin', array('email' => $email));
    
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
  public function login($admin) {
    $query = $this->db->get_where('admin', array('email' => $admin['email']));
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
      if(password_verify($admin['password'], $search['password'])) {
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
  public function add($admin) {
    // Set 'active' menjadi 1 agar akun langsung aktif
    $admin['active'] = 1;
    
    // Insert admin data ke dalam database
    $this->db->insert('admin', $admin);

    if($this->db->affected_rows() > 0) {
        return 'success';
    } else {
        return 'error';
    }
}

  // Επεξεργασία Εργοδότη
  public function update($admin) {
    $this->db->where('id', $admin['id']);
    $this->db->update('admin', $admin);

    if($this->db->affected_rows() > 0) {
      return 'success';
    } else {
      return 'error';
    }
  }

  // Διαγραφή Εργοδότη
  public function delete($id) {
    $this->db->where('id', $id);
    $this->db->delete('admin');
  }

  // Ενεργοποίηση Λογαριασμού (update active status)
  public function activation($admin) {
    $this->db->where('email', $admin['email']);
    $this->db->update('admin', array('active' => 1));

    if($this->db->affected_rows() > 0) {
      return 'success';
    } else {
      return 'error';
    }
  }
  
}
