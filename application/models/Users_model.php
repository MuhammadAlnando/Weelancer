<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

  // Μαζί με το model φορτώνουμε την database
  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  // ###########################################################
  // ######################### SEARCHES ########################
  // ###########################################################

  // Εύρεση Χρήστη με το ID
  public function search($id) {
    $query = $this->db->get_where('users', array('id' => $id));
    return $query->row_array();
  }

  // Εύρεση Χρήστη με το email
  public function search_by_email($email) {
    $query = $this->db->get_where('users', array('email' => $email));
    return $query->row_array();
  }

  // Αναζήτηση Email Χρήστη (για validation)
  public function check_email($email){
    $query = $this->db->get_where('users', array('email' => $email));
    
    if(empty($query->row_array())){
      return true;
    } else {
      return false;
    }
  }

  // ########################################################
  // ######################### LOGIN ########################
  // ########################################################

  // Σύνδεση Χρήστη
  public function login($user) {
    $query = $this->db->get_where('users', array('email' => $user['email']));
    $search = $query->row_array();

    // Αν το query δεν επιστρέφει αποτελέσματα τότε ο χρήστης δεν υπάρχει
    if($query->num_rows = 0) {
      $result = array(
        'user_id' => 0,
        'message' => 'error'
      );

      return $result;
    } else {
      // Έλεγχος αν οι κωδικοί ταιριάζουν
      if(password_verify($user['password'], $search['password'])) {
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

  // Δημιουργία Χρήστη
  public function add($user) {
    // Ensure 'active' field is set to 1 by default
    $user['active'] = 1;

    $this->db->insert('users', $user);

    if ($this->db->affected_rows() > 0) {
        $result = array(
            'user_id' => $this->db->insert_id(),
            'message' => 'success'
        );
    } else {
        $result = array(
            'user_id' => 0,
            'message' => 'error'
        );
    }

    return $result;
}


  // Επεξεργασία Χρήστη
  public function update($user) {
    $this->db->where('id', $user['id']);
    $this->db->update('users', $user);

    if($this->db->affected_rows() > 0) {
      return 'success';
    } else {
      return 'error';
    }
  }

  // Διαγραφή Χρήστη
  public function delete_user($id) {
    $this->db->where('id', $id);
    return $this->db->delete('users');
}


  public function get_users_count() {
    return $this->db->count_all('users');
}





public function get_all_users() {
  $query = $this->db->get('users');
  return $query->result(); // Fetches all rows as an array of objects
}

public function insert_user($data) {
  return $this->db->insert('users', $data);
}

public function update_user($id, $data) {
  $this->db->where('id', $id);
  return $this->db->update('users', $data);
}


  // Ενεργοποίηση Λογαριασμού (update active status)
  public function activation($user) {
    $this->db->where('email', $user['email']);
    $this->db->update('users', array('active' => 1));

    if($this->db->affected_rows() > 0) {
      return 'success';
    } else {
      return 'error';
    }
  }
}
