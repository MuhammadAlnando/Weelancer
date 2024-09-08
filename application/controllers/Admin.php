<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
  protected $metadata = array(
    'title' => null,
    'description' => 'Weelancer is an online job finding platform that helps admin find the right people for their company.'
  );

  public function __construct() {
    parent::__construct();
    $this->load->model('Companies_model');
    $this->load->model('admin_model');
    $this->load->model('Jobs_model');
    $this->load->model('Users_model');
  }

  // ##########################################################################
  // ##################### SIGN UP AND ACTIVATION (EMAIL) #####################
  // ##########################################################################

  // Εγγραφή Εργοδότη
  public function signup() {
    $this->metadata['title'] = 'Sign Up Company';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if($this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You are already connected!"); 
      redirect('/');
    }

    $currentYear = date("Y");
    $json_data = file_get_contents(base_url('categories.json'));
    $categoriesArray = json_decode($json_data, true);
    $data = array(
      'categories' => $categoriesArray
    );

    // Έλεγχος στοιχείων εταιρείας
    $this->form_validation->set_rules('image', 'Image', 'callback_check_image');
    $this->form_validation->set_rules('title', 'Title', 'required|min_length[2]|max_length[100]');
    $this->form_validation->set_rules('category', 'Category', 'required|callback_check_category');
    $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email|callback_check_email');
    $this->form_validation->set_rules('phone', 'Phone', 'numeric');
    $this->form_validation->set_rules('address', 'Address', 'required');
    $this->form_validation->set_rules('start', 'Start year', 'required|integer|greater_than[1870]|less_than_equal_to['.$currentYear.']');
    $this->form_validation->set_rules('description', 'Description', 'min_length[2]');
    $this->form_validation->set_rules('facebook', 'Facebook', 'valid_url');
    $this->form_validation->set_rules('linkedin', 'Linkedin', 'valid_url');
    $this->form_validation->set_rules('website', 'Website', 'valid_url');
    $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|max_length[50]|regex_match[/^[a-zA-Z _-]+$/]');
    $this->form_validation->set_rules('admin_email', 'Email', 'required|min_length[5]|max_length[100]|valid_email|callback_check_admin_email');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[4]|max_length[100]');
    $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');

    // Έλεγχος αν πέτυχε το validation των στοιχείων της εταιριας
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_admin', $this->metadata);
      $this->load->view('public/admin/signup_company', $data);
      $this->load->view('public/includes/footer_admin');
    } else {
      $company = array(
        'title' => $this->input->post('title'),
        'category' => $this->input->post('category'),
        'email' => $this->input->post('email'),
        'phone' => $this->input->post('phone'),
        'address' => $this->input->post('address'),
        'start' => $this->input->post('start'),
        'description' => $this->input->post('description'),
        'facebook' => $this->input->post('facebook'),
        'linkedin' => $this->input->post('linkedin'),
        'website' => $this->input->post('website'),
        'lat' => $this->input->post('lat'),
        'long' => $this->input->post('lng')
      );

      if(isset($_FILES['image']['name']) && $_FILES['image']['name']!="" && $_FILES['image']['size'] != 0) {
        $config['upload_path']          = './assets/img/uploads/companies/';
        $config['allowed_types']        = 'jpg|png';
        $config['max_size']             = 512;
        $config['max_width']            = 1000;
        $config['max_height']           = 1000;
        $config['overwrite']            = FALSE;
        $config['encrypt_name']         = TRUE;

        $this->load->library('upload', $config);

        if($this->upload->do_upload('image')) {
          $image = $this->upload->data();
        } else {
          $this->session->set_flashdata('error', "Error uploading your image!"); 
          $this->load->view('public/includes/header_admin', $this->metadata);
          $this->load->view('public/pages/alert');
          $this->load->view('public/includes/footer_admin');  
          die(); 
        }
        
        $company['image'] = 'assets/img/uploads/companies/'.$image['file_name'];
      } 

      $resultCompany = $this->companies_model->add($company);

      $admin = array(
        'username' => $this->input->post('username'),
        'company_id' => $resultCompany['comany_id'],
        'email' => $this->input->post('admin_email'),
        'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT, array('cost' => 10)),
        'hash' => md5(rand(0, 100)),
        'profession' => 'Boss'
      );

      $resultAdmin = $this->admin_model->add($admin);

      if($resultCompany['message'] === 'error' || $resultAdmin === 'error') {
        $this->session->set_flashdata('error', "There was an error with your sign up!"); 
        redirect('admin/signup');
      } else if($resultCompany['message'] === 'success' && $resultAdmin === 'success') {
        $this->activation_email($company, $admin);

        $this->load->view('public/includes/header_admin', $this->metadata);
        $this->load->view('public/admin/activation');
        $this->load->view('public/includes/footer_admin');    
      }
    }
  }

 



  // Sign up validation rule για το category της εταιρείας 
  public function check_category($category) {
    $json_data = file_get_contents(base_url('categories.json'));
    $categoriesArray = json_decode($json_data, true);
    $categoriesString = "";
    $numberOfCategories = count($categoriesArray);
    $i = 0;

    foreach($categoriesArray as $categoryArray) {
      if(++$i === $numberOfCategories)
        $categoriesString .= $categoryArray;
      else
        $categoriesString .= $categoryArray.", ";
    }

    $this->form_validation->set_message('check_category', 'The <b>{field}</b> must be one of: '.$categoriesString);

    foreach($categoriesArray as $categoryArray)
      if($category == $categoryArray)
        return TRUE;

    return FALSE;
  }

  // Sign up validation rule για το email της εταιρείας 
  public function check_email($email) {
    $this->form_validation->set_message('check_email', 'This <b>{field}</b> already exists. Please use a different one!');

    if($this->companies_model->check_email($email)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  // Sign up validation rule για το email του εργοδότη 
  public function check_admin_email($email) {
    $this->form_validation->set_message('check_admin_email', 'This <b>{field}</b> already exists. Please use a different one!');

    if($this->admin_model->check_email($email)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  // Αποστολή email στο χρήστη για ενεργοποίηση λογαριασμού
  public function activation_email($company, $admin) {
    $data = array(
      'admin' => $admin,
      'company' => $company,
      'year' => date("Y")
    );

    $message = $this->load->view('public/emails/admin_activation', $data, TRUE);
    
    $this->load->library('email');

    $this->email->from('info@Weelancer.com', 'Weelancer');
    $this->email->to($admin['email']);
    $this->email->cc($company['email']);
    $this->email->subject('Account Activation');
    $this->email->message($message);

    // Έλεγχος αν έγινε η αποστολή του email
    if($this->email->send()) {
      $this->session->set_flashdata('success_active', "You have successfully created your account!."); 
    } else {
      $this->session->set_flashdata('error_active', "You have successfully created your account!."); 
    }
  }

  // Μήνυμα ενεργοποίησης λογαριασμού μέσω του link που στάλθηκε σε email στον εργοδότη
  public function activation() {
    $this->metadata['title'] = 'Account Activation';

    $admin = array(
      'email' => $this->input->get('email'),
      'hash' => $this->input->get('hash')
    );

    // Έλεγχος αν στο url υπάρχει email και hash χρήστη
    // Αν δεν υπάρχουν να μην υπάρχει πρόσβαση στη μέθοδο
    if($admin['email'] !== NULL && $admin['hash'] !== NULL) {
      $adminDB = $this->admin_model->search_by_email($admin['email']);

      // Έλεγχος αν υπάρχει το email στη Βάση Δεδομένων
      // Έλεγχος αν το hash που υπάρχει στο url είναι ίδιο με το hash της Βάσης Δεδομένων
      if(is_null($adminDB)) {
        redirect('/');
      } else if($admin['hash'] == $adminDB['hash']) {
        $result = $this->admin_model->activation($admin);

        if($result === 'success') {
          $this->session->set_flashdata('success_active', "The activation of your account was successful.<br>You can now login!"); 
        } else if($result === 'error') {
          $this->session->set_flashdata('error_active', "Your account is already activated.<br>You can now login!"); 
        }
      } else {
        redirect('/');
      }
    } else {
      redirect('/');
    }

    $this->load->view('public/includes/header_admin', $this->metadata);
    $this->load->view('public/admin/activation');
    $this->load->view('public/includes/footer_admin');
  }

  // #########################################################################
  // ##################### LOGIN AND FORGOT PASS (EMAIL) #####################
  // #########################################################################

  // Σύνδεση Εργοδότη
  public function login() {
    $this->metadata['title'] = 'Login Admin';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if($this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You are already connected!"); 
      redirect('admin/dashboard');
    }

    $this->form_validation->set_rules('email', 'Email', 'required');
    $this->form_validation->set_rules('password', 'Password', 'required');

    // Έλεγχος αν πέτυχε το validation των στοιχείων του εργοδότη
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_loginadmin', $this->metadata);
      $this->load->view('public/admin/login_admin');
      $this->load->view('public/includes/footer_admin');
    } else {
      $admin = array(
        'email' => $this->input->post('email'),
        'password' => $this->input->post('password')
      );

      // Εύρεση στοιχείων χρήστη στη Βάση Δεδομένων
      $result = $this->admin_model->login($admin);

      if($result['message'] === 'success') {
        $user_id = $result['user_id'];
        $this->session->set_userdata('admin_id', $user_id);
        redirect('admin/dashboard');
      } else if($result['message'] === 'error') {
        $this->session->set_flashdata('error', "Credentials are wrong. Try again!"); 
        $this->load->view('public/includes/header_loginadmin', $this->metadata);
        $this->load->view('public/admin/login_admin');
        $this->load->view('public/includes/footer_admin');
      } else if($result['message'] === 'error_active') {
        $this->session->set_flashdata('error', "You have to activate your account!"); 
        $this->load->view('public/includes/header_loginadmin', $this->metadata);
        $this->load->view('public/admin/login_admin');
        $this->load->view('public/includes/footer_admin');
      }
    } 
  }

  // Forgot και reset κωδικού εργοδότη
  public function forgotpass() {

    $admin = array(
      'email' => $this->input->get('email'),
      'hash' => $this->input->get('hash')
    );

    // Αν στο url δεν υπάρχουν το email και το hash του εργοδότη 
    // να του εμφανίσει την σελίδα για να συμπληρώσει το email του
    // Αν υπάρχουν και ειναι σωστά να αλλαζει ο κωδικός του χρήστη
    if($admin['email'] === NULL && $admin['hash'] === NULL) {
      $this->metadata['title'] = 'Forgot Password';

      // Έλεγχος email εργοδότη
      $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email|callback_check_email_forgot');

      // Έλεγχος αν πέτυχε το validation των στοιχείων του εργοδότη
      if ($this->form_validation->run() === FALSE) {
        $this->load->view('public/includes/header_admin', $this->metadata);
        $this->load->view('public/admin/forgotpass');
        $this->load->view('public/includes/footer_admin');
      } else {
        $email = $this->input->post('email');
        
        $adminDB = $this->admin_model->search_by_email($email);

        $this->forgotpass_email($adminDB);

        $this->load->view('public/includes/header_admin', $this->metadata);
        $this->load->view('public/admin/forgotpass_message');
        $this->load->view('public/includes/footer_admin');
      }
    } else if($admin['email'] !== NULL && $admin['hash'] !== NULL) {
      $adminDB = $this->admin_model->search_by_email($admin['email']);

      // Έλεγχος αν το hash που υπάρχει στο url είναι ίδιο με το hash της Βάσης Δεδομένων
      if($admin['hash'] == $adminDB['hash']) {
        $this->metadata['title'] = 'Reset Password';

        // Παραμετροι url
        $data['url'] = '?email='.$adminDB['email'].'&'.'hash='.$adminDB['hash'];

        // Έλεγχος password χρήστη
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[4]|max_length[100]');
        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');

        // Έλεγχος αν πέτυχε το validation των password
        if ($this->form_validation->run() === FALSE) {
          $this->load->view('public/includes/header_admin', $this->metadata);
          $this->load->view('public/admin/resetpass', $data);
          $this->load->view('public/includes/footer_admin');
        } else {
          $adminDBnewPass = array(
            'id' => $adminDB['id'],
            'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT, array('cost' => 10))
          );
        
          // Εισαγωγή password χρήστη στη Βάση Δεδομένων
          $result = $this->admin_model->update($adminDBnewPass);

          if($result === 'success') {
            $this->session->set_flashdata('success', "You successfully changed your password.<br>You can now login!"); 
          } else if($result === 'error') {
            $this->session->set_flashdata('error', "There was an error reseting your password.<br>Try again!"); 
          }

          $this->load->view('public/includes/header_admin', $this->metadata);
          $this->load->view('public/admin/forgotpass_message');
          $this->load->view('public/includes/footer_admin');
        }
      } else {
        redirect('/');
      }
    }
  }

  // Validation Rule για το email του εργοδότη στο forgot pass
  public function check_email_forgot($email) {
    $this->form_validation->set_message('check_email_forgot', 'This <b>{field}</b> doesn&apos;t exists.');

    if($this->admin_model->check_email($email)) {
      return false;
    } else {
      return true;
    }
  }

  // Αποστολή email στο χρήστη για επαναφορά κωδικού
  public function forgotpass_email($admin) {
    $data = array(
      'admin' => $admin,
      'year' => date("Y")
    );

    $message = $this->load->view('public/emails/admin_forgotpass', $data, TRUE);
    
    $this->load->library('email');

    $this->email->from('info@Weelancer.com', 'Weelancer');
    $this->email->to($admin['email']);
    $this->email->subject('Password Reset');
    $this->email->message($message);

    // Έλεγχος αν έγινε η αποστολή του email
    if($this->email->send()) {
      $this->session->set_flashdata('success', "A password reset email was to your email address.<br>Please click the link in that email to reset your password!"); 
    } else {
      $this->session->set_flashdata('error', "There was a problem senting you the password reset email.<br>Try again!"); 
    }
  }

  // ##################################################
  // ##################### LOGOUT #####################
  // ##################################################

  // Αποσύνδεση Εργοδότη
  public function logout() {
    $this->metadata['title'] = 'Logout';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if($this->session->userdata('admin_id')) {
      $this->session->unset_userdata('admin_id');
      redirect('admin/login');
    } else {
      redirect('/');
    }
  }

  // #################################################
  // ###################### ... ######################
  // #################################################

  // Admin Dashboard
  public function dashboard() {
    $this->metadata['title'] = 'Dashboard';
    $this->load->model('Users_model');
        $this->load->model('Employers_model');
        $this->load->model('Companies_model');
        $this->load->model('Jobs_model');
        $this->load->model('admin_model');


        $data['users_count'] = $this->Users_model->get_users_count();
        $data['employers_count'] = $this->Employers_model->get_employers_count();
        $data['companies_count'] = $this->Companies_model->get_companies_count();
        $data['jobs_count'] = $this->Jobs_model->get_jobs_count();

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $admin = $this->admin_model->search($this->session->userdata('admin_id'));

    $data['admin'] = $admin;
  
    $this->load->view('public/includes/header_admin', $this->metadata);
    $this->load->view('public/admin/dashboard', $data);
    $this->load->view('public/includes/footer_admin');
  }

  // In application/controllers/Admin.php

// In application/controllers/Admin.php

public function employers() {
  $this->metadata['title'] = 'Employers';
  $this->load->model('Employers_model'); // Ensure this model is loaded
  $this->load->model('Companies_model'); // Pastikan model ini sudah ada

  // Fetch the data
  $data['employers'] = $this->Employers_model->get_all_employers();

    // Ambil data title dari tabel companies
    $data['companies'] = $this->Companies_model->get_companies_titles();

// Έλεγχος αν ο εργοδότης έχει κάνει login
if(!$this->session->userdata('admin_id')) {
$this->session->set_flashdata('error', "You must login to your account!"); 
redirect('admin/login');
}

  // Load the view
  $admin = $this->admin_model->search($this->session->userdata('admin_id'));

    $data['admin'] = $admin;

  $this->load->view('public/includes/header_admin', $this->metadata);
  $this->load->view('public/admin/employers', $data);
  $this->load->view('public/includes/footer_admin');
}

public function edit($id) {
  $this->load->model('employers_model');
  
  // Ambil data employer berdasarkan ID
  $employer = $this->employers_model->get_employer_by_id($id);
  
  if ($this->input->post()) {
      // Update data employer
      $data = array(
          'username' => $this->input->post('username'),
          'email' => $this->input->post('email')
      );
      
      $this->employers_model->update_employer($id, $data);
      redirect('admin/employers');
  }
  
  $data['employer'] = $employer;
  $this->load->view('admin/edit_employer', $data);
}

public function delete_employer($id)
{
    $this->load->model('Employers_model');
    
    // Cek apakah employer ada
    if ($this->Employers_model->delete_employer($id)) {
        // Set pesan sukses dan redirect ke halaman employers
        $this->session->set_flashdata('success', 'Employer deleted successfully.');
    } else {
        // Set pesan error jika gagal
        $this->session->set_flashdata('error', 'Failed to delete employer.');
    }

    // Redirect kembali ke halaman employers
    redirect('admin/employers');
}




public function employer($id = NULL) {
  if ($id === NULL) {
      $this->load->view('public/admin/employer_form');
  } else {
      $data['employer'] = $this->Employers_model->get_employer($id);
      $this->load->view('public/admin/employer_form', $data);
  }
}

public function save_employer() {
  $id = $this->input->post('id');
  $data = array(
      'name' => $this->input->post('name'),
      'contact' => $this->input->post('contact')
  );

  if ($id) {
      $this->Employers_model->update_employer($id, $data);
  } else {
      $this->Employers_model->insert_employer($data);
  }

  redirect('admin/employers');
}


public function users() {
  $this->metadata['title'] = 'Users';
  $this->load->model('Users_model'); // Ensure this model is loaded

  // Fetch the data
  $data['users'] = $this->Users_model->get_all_users();

// Έλεγχος αν ο εργοδότης έχει κάνει login
if(!$this->session->userdata('admin_id')) {
$this->session->set_flashdata('error', "You must login to your account!"); 
redirect('admin/login');
}

  // Load the view
  $admin = $this->admin_model->search($this->session->userdata('admin_id'));

    $data['admin'] = $admin;

  $this->load->view('public/includes/header_admin', $this->metadata);
  $this->load->view('public/admin/users', $data);
  $this->load->view('public/includes/footer_admin');
  
}

public function user($id = NULL) {
  if ($id === NULL) {
      $this->load->view('public/admin/user_form');
  } else {
      $data['users'] = $this->Users_model->get_users($id);
      $this->load->view('public/admin/user_form', $data);
  }
}

public function save_user() {
  $id = $this->input->post('id');
  $data = array(
      'name' => $this->input->post('name'),
      'email' => $this->input->post('email')
  );

  if ($id) {
      $this->Users_model->update_user($id, $data);
  } else {
      $this->Users_model->insert_user($data);
  }

  redirect('admin/users');
}

public function delete_user($id) {
  if ($this->Users_model->delete_user($id)) {
      $this->session->set_flashdata('success', 'User deleted successfully.');
  } else {
      $this->session->set_flashdata('error', 'Failed to delete user.');
  }
  redirect('admin/users');
}

public function jobs() {
  $this->metadata['title'] = 'Jobs';
  $this->load->model('Jobs_model'); // Ensure this model is loaded

  // Fetch the data
  $data['jobs'] = $this->Jobs_model->get_all_jobs();

// Έλεγχος αν ο εργοδότης έχει κάνει login
if(!$this->session->userdata('admin_id')) {
$this->session->set_flashdata('error', "You must login to your account!"); 
redirect('admin/login');
}

  // Load the view
  $admin = $this->admin_model->search($this->session->userdata('admin_id'));

    $data['admin'] = $admin;

  $this->load->view('public/includes/header_admin', $this->metadata);
  $this->load->view('public/admin/jobs', $data);
  $this->load->view('public/includes/footer_admin');
}
public function job($id = NULL) {
  if ($id === NULL) {
      $this->load->view('public/admin/job_form');
  } else {
      $data['job'] = $this->Jobs_model->get_job($id);
      $this->load->view('public/admin/job_form', $data);
  }
}

public function save_job() {
  $id = $this->input->post('id');
  $data = array(
      'title' => $this->input->post('title'),
      'description' => $this->input->post('description')
  );

  if ($id) {
      $this->Jobs_model->update_job($id, $data);
  } else {
      $this->Jobs_model->insert_job($data);
  }

  redirect('admin/jobs');
}

public function delete_job($id) {
  // Check if the ID is valid
  if ($id && $this->Jobs_model->delete_job($id)) {
      // Set success message and redirect
      $this->session->set_flashdata('success', 'Job deleted successfully');
  } else {
      // Set error message and redirect
      $this->session->set_flashdata('error', 'Failed to delete job');
  }
  redirect('admin/jobs');
}




  // Delete company και admin
  public function companies() {
    $this->load->model('Companies_model');

    // Fetch data for the view
    $data['companies'] = $this->Companies_model->get_all_companies(); // Adjust method as needed

    // Load the view
    $this->load->view('admin/companies', $data);
  }

public function delete_company($id) {
    if ($id) {
        // Menghapus perusahaan
        if ($this->Companies_model->delete_company($id)) {
            $this->session->set_flashdata('success', 'Company deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete company');
        }
    } else {
        $this->session->set_flashdata('error', 'Invalid company ID');
    }
    redirect('admin/companies');
}


  // Company Profile
  public function profile() {
    $this->metadata['title'] = 'Company Profile';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $admin = $this->admin_model->search($this->session->userdata('admin_id'));
    $company = $this->companies_model->search($admin['company_id']);
    $currentYear = date("Y");
    $json_data = file_get_contents(base_url('categories.json'));
    $categoriesArray = json_decode($json_data, true);
    $data = array(
      'company' => $company,
      'categories' => $categoriesArray
    );

    $this->form_validation->set_rules('image', 'Image', 'callback_check_image');
    $this->form_validation->set_rules('title', 'Title', 'required|min_length[2]|max_length[100]');
    $this->form_validation->set_rules('category', 'Category', 'required|callback_check_category');
    $this->form_validation->set_rules('phone', 'Phone', 'numeric');
    $this->form_validation->set_rules('address', 'Address', 'required');
    $this->form_validation->set_rules('start', 'Start year', 'required|integer|greater_than[1870]|less_than_equal_to['.$currentYear.']');
    $this->form_validation->set_rules('description', 'Description', 'min_length[2]');
    $this->form_validation->set_rules('facebook', 'Facebook', 'valid_url');
    $this->form_validation->set_rules('linkedin', 'Linkedin', 'valid_url');
    $this->form_validation->set_rules('website', 'Website', 'valid_url');

    if($company['email'] != $this->input->post('email')) {
      $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email|callback_check_email');
    } else {
      $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email');
    }

    // Έλεγχος αν πέτυχε το validation των στοιχείων της εταιρείας
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_admin', $this->metadata);
      $this->load->view('public/includes/admin_menu', $this->metadata);
      $this->load->view('public/admin/company-profile', $data);
      $this->load->view('public/includes/footer_admin');
    } else {
      $company_inputs = array(
        'id' => $company['id'],
        'title' => $this->input->post('title'),
        'category' => $this->input->post('category'),
        'email' => $this->input->post('email'),
        'phone' => $this->input->post('phone'),
        'address' => $this->input->post('address'),
        'start' => $this->input->post('start'),
        'description' => $this->input->post('description'),
        'facebook' => $this->input->post('facebook'),
        'linkedin' => $this->input->post('linkedin'),
        'website' => $this->input->post('website'),
        'lat' => $this->input->post('lat'),
        'long' => $this->input->post('lng')
      );

      if(isset($_FILES['image']['name']) && $_FILES['image']['name']!="" && $_FILES['image']['size'] != 0) {
        $config['upload_path']          = './assets/img/uploads/companies/';
        $config['allowed_types']        = 'jpg|png';
        $config['max_size']             = 512;
        $config['max_width']            = 1000;
        $config['max_height']           = 1000;
        $config['overwrite']            = FALSE;
        $config['encrypt_name']         = TRUE;

        $this->load->library('upload', $config);

        if($this->upload->do_upload('image')) {
          $image = $this->upload->data();
        } else {
          $this->session->set_flashdata('error', "Error uploading your image!"); 
          $this->load->view('public/includes/header_admin', $this->metadata);
          $this->load->view('public/pages/alert');
          $this->load->view('public/includes/footer_admin');  
          die(); 
        }
        
        $company_inputs['image'] = 'assets/img/uploads/companies/'.$image['file_name'];
      } 

      // Update στοιχείων χρήστη στη Βάση Δεδομένων
      if($this->companies_model->update($company_inputs) === 'success') {
        $this->session->set_flashdata('success', "Changes registered."); 
      } else {
        $this->session->set_flashdata('error', "No changes to update."); 
      }
      
      redirect('admin/profile');
    }
  }

  // Validation rule για το image του company
  public function check_image($image) {
    //$this->form_validation->set_message('check_image', 'Max image dimensions: 1000x1000px. Max image size: 512KB. Allowed image types: JPG/PNG.');

    if(!empty($_FILES['image']['name']) || $_FILES['image']['name'] != "" || $_FILES['image']['size'] > 0 ) {
      $file = $_FILES["image"]["tmp_name"];
      $info = getimagesize($file);
      $message = '';
      $flag = TRUE;

      if($info[0] > "1000" || $info[1] > "1000") {
        $message .= 'Max image dimensions: 1000x1000px. ';
        $this->form_validation->set_message('check_image', $message);
        $flag = FALSE;
      }
      
      if(filesize($file) > 512000) {
        $message .= 'Max image size: 512KB. ';
        $this->form_validation->set_message('check_image', $message);
        $flag = FALSE;
      } 

      if($info["mime"] != 'image/jpeg' && $info["mime"] != 'image/jpg' && $info["mime"] != 'image/png') {
        $message .= 'Allowed image types: JPG/PNG. ';
        $this->form_validation->set_message('check_image', $message);
        $flag = FALSE;
      } 

      return $flag;
    } else {
      return TRUE;
    }
  }

  // Post Job
  public function postjob() {
    $this->metadata['title'] = 'Post Job';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $json_data = file_get_contents(base_url('categories.json'));
    $categoriesArray = json_decode($json_data, true);
    $data = array(
      'categories' => $categoriesArray
    );

    $this->form_validation->set_rules('title', 'Title', 'required|min_length[2]|max_length[100]');
    $this->form_validation->set_rules('category', 'Category', 'required|callback_check_category');
    $this->form_validation->set_rules('address', 'Address', 'required');
    $this->form_validation->set_rules('type', 'Type', 'required|callback_check_job_type');
    $this->form_validation->set_rules('salary', 'Salary', 'numeric');
    $this->form_validation->set_rules('description', 'Description', 'min_length[2]');

    // Έλεγχος αν πέτυχε το validation των στοιχείων του job
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_admin', $this->metadata);
      $this->load->view('public/includes/admin_menu', $this->metadata);
      $this->load->view('public/admin/postjob', $data);
      $this->load->view('public/includes/footer_admin');
    } else {
      $admin = $this->admin_model->search($this->session->userdata('admin_id'));
      $company = $this->companies_model->search($admin['company_id']);

      $job = array(
        'company_id' => $company['id'],
        'title' => $this->input->post('title'),
        'category' => $this->input->post('category'),
        'address' => $this->input->post('address'),
        'type' => $this->input->post('type'),
        'description' => $this->input->post('description'),
        'lat' => $this->input->post('lat'),
        'long' => $this->input->post('lng')
      );

      if(!is_null($this->input->post('salary'))) {
        $job['salary'] = $this->input->post('salary');
      }

      // Εισαγωγή στοιχείων χρήστη στη Βάση Δεδομένων
      if($this->jobs_model->add($job) === 'success') {
        $this->session->set_flashdata('success', "Job posted."); 
      } else {
        $this->session->set_flashdata('error', "There was an error posting the job. Try again."); 
      }
      
      redirect('admin/managejobs');
    }
  }

  // Edit Job
  public function editjob($id) {
    $this->metadata['title'] = 'Edit Job';

    $job = $this->jobs_model->search($id);

    if(is_null($job)) {
      redirect('admin/managejobs');
    }

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $json_data = file_get_contents(base_url('categories.json'));
    $categoriesArray = json_decode($json_data, true);
    $data = array(
      'job' => $job,
      'categories' => $categoriesArray
    );

    $this->form_validation->set_rules('title', 'Title', 'required|min_length[2]|max_length[100]');
    $this->form_validation->set_rules('category', 'Category', 'required'); //callback_check_job_category
    $this->form_validation->set_rules('address', 'Address', 'required');
    $this->form_validation->set_rules('type', 'Type', 'required|callback_check_job_type');
    $this->form_validation->set_rules('salary', 'Salary', 'required|numeric');
    $this->form_validation->set_rules('description', 'Description', 'min_length[2]');

    // Έλεγχος αν πέτυχε το validation των στοιχείων του job
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_admin', $this->metadata);
      $this->load->view('public/includes/admin_menu', $this->metadata);
      $this->load->view('public/admin/editjob', $data);
      $this->load->view('public/includes/footer_admin');
    } else {
      $admin = $this->admin_model->search($this->session->userdata('admin_id'));
      $company = $this->companies_model->search($admin['company_id']);

      $job = array(
        'id' => $id,
        'company_id' => $company['id'],
        'title' => $this->input->post('title'),
        'category' => $this->input->post('category'),
        'address' => $this->input->post('address'),
        'type' => $this->input->post('type'),
        'description' => $this->input->post('description'),
        'lat' => $this->input->post('lat'),
        'long' => $this->input->post('lng')
      );

      if(!is_null($this->input->post('salary'))) {
        $job['salary'] = $this->input->post('salary');
      }

      // Εισαγωγή στοιχείων χρήστη στη Βάση Δεδομένων
      if($this->jobs_model->update($job) === 'success') {
        $this->session->set_flashdata('success', "Job updated."); 
      } else {
        $this->session->set_flashdata('error', "There was an error updating the job. Try again."); 
      }
      
      redirect('admin/managejobs');
    }
  }

  // Job validation rule για το type
  public function check_job_type($type) {
    $this->form_validation->set_message('check_job_type', 'The <b>{field}</b> must be one of: Full Time, Freelance, Part Time, Internship.');

    if($type == "Full Time" || $type == "Freelance" || $type == "Part Time" || $type == "Internship") {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  // Delete jobs
  public function deletejob($id) {
    $this->metadata['title'] = 'Delete Jobs';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $this->jobs_model->delete($id);

    $this->session->set_flashdata('success', "Job deleted."); 
  
    redirect('admin/managejobs');
  }

  // Manage Jobs
  public function managejobs() {
    $this->metadata['title'] = 'Manage Jobs';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $admin = $this->admin_model->search($this->session->userdata('admin_id'));
    $company = $this->companies_model->search($admin['company_id']);
    $data['jobs'] = $this->jobs_model->select_company($company['id']);

    $this->load->view('public/includes/header_admin', $this->metadata);
    $this->load->view('public/includes/admin_menu', $this->metadata);
    $this->load->view('public/admin/manage-jobs', $data);
    $this->load->view('public/includes/footer_admin');
  }

  // Manage Candidates
  public function managecandidates($id) {
    $this->metadata['title'] = 'Manage Candidates';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    switch($this->input->get('status', TRUE)) {
      case 'pending': 
        $data['flag'] = 'pending';
        $data['candidates'] = $this->jobs_model->job_applications_pending($id);
        break;
      case 'approved':
        $data['flag'] = 'approved';
        $data['candidates'] = $this->jobs_model->job_applications_approved($id);
        break;
      default:
        redirect('admin/managejobs');
    }

    $data['job_id'] = $id;
    
    $this->load->view('public/includes/header_admin', $this->metadata);
    $this->load->view('public/includes/admin_menu', $this->metadata);
    $this->load->view('public/admin/manage-candidates', $data);
    $this->load->view('public/includes/footer_admin');
  }

  // Approved Candidate - Notification Email
  public function candidate_approval($id) {
    $status = array(
      'state' => 'Approved'
    );

    // Ενημέρωση του status στη Βάση Δεδομένων
    if($this->jobs_model->status_application($id, $status) === 'success') {
      $data = $this->jobs_model->job_application_data($id);
      $data['year'] = date("Y");

      $message = $this->load->view('public/emails/candidate_approval', $data, TRUE);
      
      $this->load->library('email');

      $this->email->from('info@Weelancer.com', 'Weelancer');
      $this->email->to($data['email']);
      $this->email->subject('Candidate Approval');
      $this->email->message($message);

      // Έλεγχος αν έγινε η αποστολή του email
      if($this->email->send()) {
        $this->session->set_flashdata('success', 'You have successfully approved '.$data['username'].'!'); 
      } else {
        $this->session->set_flashdata('error', 'You have successfully approved '.$data['username'].'!<br>Unfortunately there was a problem sending the notification email to the candidate!'); 
      }
    } else {
      $this->session->set_flashdata('error', "There was an error approving the candidate. Try again."); 
    }

    redirect('admin/managecandidates/'.$data['id'].'?status=pending');
  }

  public function candidate_rejection($id) {
    $status = array(
      'state' => 'Rejected'
    );

    // Ενημέρωση του status στη Βάση Δεδομένων
    if($this->jobs_model->status_application($id, $status) === 'success') {
      $data = $this->jobs_model->job_application_data($id);
      $data['year'] = date("Y");

      $message = $this->load->view('public/emails/candidate_rejection', $data, TRUE);
      
      $this->load->library('email');

      $this->email->from('info@Weelancer.com', 'Weelancer');
      $this->email->to($data['email']);
      $this->email->subject('Candidate Rejection');
      $this->email->message($message);

      // Έλεγχος αν έγινε η αποστολή του email
      if($this->email->send()) {
        $this->session->set_flashdata('success', 'You have successfully rejected '.$data['username'].'!'); 
      } else {
        $this->session->set_flashdata('error', 'You have successfully rejected '.$data['username'].'!<br>Unfortunately there was a problem sending the notification email to the candidate!'); 
      }
    } else {
      $this->session->set_flashdata('error', "There was an error rejecting the candidate. Try again."); 
    }

    redirect('admin/managecandidates/'.$data['id'].'?status=pending');
  }

   // View candidate profile
   public function viewcandidate($id) {
    $this->metadata['title'] = 'Candidate Profile';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $candidate = $this->jobs_model->user_application_data($id);

    $this->load->view('public/includes/header_admin', $this->metadata);
    $this->load->view('public/includes/admin_menu', $this->metadata);
    $this->load->view('public/admin/user-profile', $candidate);
    $this->load->view('public/includes/footer_admin');
  }

  // Manage admin
  public function manageadmin() {
    $this->metadata['title'] = 'Manage Admin';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $admin = $this->admin_model->search($this->session->userdata('admin_id'));
    $company = $this->companies_model->search($admin['company_id']);
    $data['admin'] = $this->admin_model->select_company($company['id']);

    $this->load->view('public/includes/header_admin', $this->metadata);
    $this->load->view('public/includes/admin_menu', $this->metadata);
    $this->load->view('public/admin/manage-admin', $data);
    $this->load->view('public/includes/footer_admin');
  }

  // Add admin
  public function addadmin() {
    $this->metadata['title'] = 'Add Admin';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $this->form_validation->set_rules('username', 'Full Name', 'required|min_length[4]|max_length[50]|regex_match[/^[a-zA-Z _-]+$/]');
    $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email|callback_check_admin_email');
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[4]|max_length[100]');
    $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');
    $this->form_validation->set_rules('profession', 'Profession', 'required|callback_check_profession');

    // Έλεγχος αν πέτυχε το validation των στοιχείων του εργοδότη
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_admin', $this->metadata);
      $this->load->view('public/includes/admin_menu', $this->metadata);
      $this->load->view('public/admin/add-admin');
      $this->load->view('public/includes/footer_admin');
    } else {
      $admin = $this->admin_model->search($this->session->userdata('admin_id'));
      $company = $this->companies_model->search($admin['company_id']);

      $admin_inputs = array(
        'username' => $this->input->post('username'),
        'company_id' => $company['id'],
        'email' => $this->input->post('email'),
        'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT, array('cost' => 10)),
        'hash' => md5(rand(0, 100)),
        'profession' => $this->input->post('profession')
      );

      if(!is_null($this->input->post('active'))) {
        $admin_inputs['active'] = $this->input->post('active');
      }

      // Εισαγωγή στοιχείων χρήστη στη Βάση Δεδομένων
      if($this->admin_model->add($admin_inputs) === 'success') {
        $this->session->set_flashdata('success', "Admin added."); 
      } else {
        $this->session->set_flashdata('error', "There was an error adding the admin. Try again."); 
      }
      
      redirect('admin/manageadmin');
    }
  }

  // Edit admin
  public function editadmin($id) {
    $this->metadata['title'] = 'Edit Admin';

    $admin = $this->admin_model->search($id);

    if(is_null($admin)) {
      redirect('admin/manageadmin');
    }

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $this->form_validation->set_rules('username', 'Full Name', 'required|min_length[4]|max_length[50]|regex_match[/^[a-zA-Z _-]+$/]');
    $this->form_validation->set_rules('profession', 'Profession', 'required|callback_check_profession');

    if($admin['email'] != $this->input->post('email')) {
      $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email|callback_check_admin_email');
    } else {
      $this->form_validation->set_rules('email', 'Email', 'required|min_length[5]|max_length[100]|valid_email');
    }

    if(!empty($this->input->post('password')) || !empty($this->input->post('password_confirm'))) {
      $this->form_validation->set_rules('password', 'Password', 'required|min_length[4]|max_length[100]');
      $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required|matches[password]');
    }

    // Έλεγχος αν πέτυχε το validation των στοιχείων του εργοδότη
    if ($this->form_validation->run() === FALSE) {
      $this->load->view('public/includes/header_admin', $this->metadata);
      $this->load->view('public/includes/admin_menu', $this->metadata);
      $this->load->view('public/admin/edit-admin', $admin);
      $this->load->view('public/includes/footer_admin');
    } else {
      $admin_inputs = array(
        'id' => $id,
        'username' => $this->input->post('username'),
        'email' => $this->input->post('email'),
        'profession' => $this->input->post('profession')
      );

      if(!empty($this->input->post('password'))) {
        $admin_inputs['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT, array('cost' => 10));
      }

      if(!is_null($this->input->post('active'))) {
        $admin_inputs['active'] = $this->input->post('active');
      } else {
        $admin_inputs['active'] = '1';
      }

      // Update στοιχείων χρήστη στη Βάση Δεδομένων
      if($this->admin_model->update($admin_inputs) === 'success') {
        $this->session->set_flashdata('success', "Changes registered."); 
      } else {
        $this->session->set_flashdata('error', "No changes to update."); 
      }
      
      redirect('admin/editadmin/'.$id);
    }
  }

  // Add/Edit admin validation rule για το profession του admin
  public function check_profession($profession) {
    $this->form_validation->set_message('check_profession', 'The <b>{field}</b> must be one of: Boss, Simple Admin.');

    if($profession == "Boss" || $profession == "Simple Admin") {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  // Delete admin
  public function deleteadmin($id) {
    $this->metadata['title'] = 'Delete Admin';

    // Έλεγχος αν ο εργοδότης έχει κάνει login
    if(!$this->session->userdata('admin_id')) {
      $this->session->set_flashdata('error', "You must login to your account!"); 
      redirect('admin/login');
    }

    $this->admin_model->delete($id);

    $this->session->set_flashdata('success', "Admin deleted."); 
  
    redirect('admin/manageadmin');
  }
}