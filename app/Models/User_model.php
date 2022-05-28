<?php
namespace App\Models;
use CodeIgniter\Model;

class User_model extends Model
{
		protected $table = 'tbl_users';
    // .. other member variables
    private $db;

    function __construct()
    {
			// Call the Model constructor
			parent::__construct();
			$this->db = \Config\Database::connect();
			// $this->load->library('session');
			// $this->load->library('dppsession');
    }
    
    function add($postdata) {
	    
	    // If e-mail already exists, kick them to a "your e-mail already exists" page and force them to verify
	    
	    // if (!$postdata['email']) show_error("E-mail address is required.");
	    $email = $postdata['email'];
	    $phone = $postdata['phone'];
	    $code = substr( md5(rand()), 0, 5);
	    $data = [ 
	    	'email'=>$postdata['email'], 
				'phone'=>$postdata['phone'], 
				'language'=>$postdata['language'], 
				'started'=>time(),
				'verification_code'=>$code,
				'last_activity' => time(),
				'visit_count' => 0
			];

			$this->db->insert('users', $data);
	    $user_id = $this->db->insert_id();
	    
	    // Set session data
	    $this->session->set_userdata([
				'user_id'=>$user_id,
				'email'=>$postdata['email'], 
				'phone'=>$postdata['phone'], 
				'language'=>$postdata['language']
	    ]);
	    
	    return [
			'user_id'=>$user_id,
			'email'=>$postdata['email'], 
			'phone'=>$postdata['phone'], 
			'language'=>$postdata['language'],
			'verification_code'=>$code,
	    ];

		/*
        if((isset($phone) && $phone!="") && (isset($email) && $email!="")){

           $query = "SELECT * FROM users WHERE email = '".$email."' OR phone = '".$phone."' LIMIT 1;";

        }else if(isset($phone) && $phone!=""){

           $query = "SELECT * FROM users WHERE phone = '".$phone."' LIMIT 1;";

        }else if(isset($email) && $email!=""){

           $query = "SELECT * FROM users WHERE email = '".$email."'  LIMIT 1;";
        }
	    $alreadyExist = $this->db->query($query)->row();

	    //echo "<pre>"; print_r($alreadyExist->user_id);"</pre>";exit();
        if($alreadyExist){
	        	// Set session data
		    $this->session->set_userdata([
				'user_id'=>$alreadyExist->user_id,
				'email'=>$alreadyExist->email, 
				'phone'=>$alreadyExist->phone, 
				'language'=>$postdata['language']
		    ]);
		  
	    }else{

		    $this->db->insert('users', $data);
		    $user_id = $this->db->insert_id();
		    
		    // Set session data
		    $this->session->set_userdata([
				'user_id'=>$user_id,
				'email'=>$postdata['email'], 
				'phone'=>$postdata['phone'], 
				'language'=>$postdata['language']
		    ]);
	    }
	    */

	    //$user_id = $this->session->user_id;
    	//print_r($user_id);exit();

	    
    }

    function changeLanguage($language) {
	    
		$user_id = isset($_COOKIE['dpp_user_id']) ? $_COOKIE['dpp_user_id'] : false;

	    $this->db->update('users', ['language'=>$language], ['user_id'=>$user_id]);
	    
    }
    
    function set_completed() {
	    $user_id = isset($_COOKIE['dpp_user_id']) ? $_COOKIE['dpp_user_id'] : false;
		
	    $this->db->update('users', ['completed'=>time()], ['user_id'=>$user_id]);
    }
    
    function get_existing_user($postdata) {
	    
	    if ($postdata['email']) {
	    	$query = $this->db->get_where('users', ['email'=>$postdata['email'], 'completed'=>null]);
				if ($query->num_rows() > 0) {
					return $query->row();
				} else {
					return false;
				}
			}
	    
	    if ($postdata['phone']) {
	    	$query = $this->db->get_where('users', ['phone'=>$postdata['phone'], 'completed'=>null]);
				if ($query->num_rows() > 0) {
					return $query->row();
				} else {
					return false;
				}
	    }
	    
	    return false;
	    
    }

		public function update_user_visit($user_id) {
			if($user_id) {
				$this->db->where('user_id', $user_id);
				$query = $this->db->get('users');
				$result = $query->row();

				$updates = array(
					'visit_count' => $result->visit_count,
					'last_activity' => $result->last_activity,
				);

				$visit_count = $result->visit_count;
				$last_activity = $result->last_activity;
				$elapsed_days = ( time() - $last_activity)/3600/24;

				// increment visit_count if more than a day since last visit
				if ($visit_count == NULL){
					$updates['visit_count'] = 1;
				} elseif( $visit_count && ($elapsed_days >= 1 )){
					$updates['visit_count'] += 1;
				}

				// increment last_activity
				if($last_activity == NULL ){
					$updates['last_activity'] = time();
				} elseif( $last_activity ) {
					$updates['last_activity'] = time();
				}

				$this->db->update('users', $updates, array('user_id' => $user_id));
				return $updates;

			}

		}

		function increment_visit_count($user_id) {
			$this->db->where('user_id', $user_id)->set('visit_count', 'visit_count+1', FALSE )->update('users');
		}
		function increment_last_activity($user_id) {
			$this->db->where('user_id', $user_id)->set('last_activity', time(), FALSE )->update('users');
		}

} // end class