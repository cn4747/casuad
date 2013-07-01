<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User_Data
 *
 * This model works with user data in frontend
 *
 * @author	Anton Martynenko
 */
class User_Data extends CI_Model
{
    /**
     * @var string Table with user data
     */
	private $table_users = 'users';
    
    /**
     * @var string Table with user roles data
     */
	private $table_roles = 'user_roles';
    
    /**
     * @var string Salt value from config file, to generate hashes
     */
    private $hashing_salt;

    /**
     * @var Object CodeIgniter instance pointer
     */
    private $ci;
    
	function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
        $this->ci->load->database();

        $this->ci->load->config('auth', TRUE);
        $this->hashing_salt = $this->ci->config->item('salt_value', 'auth');
	}

    public function get_table_users()
    {
        return $this->table_users;
    }

    /**
     * Activate account by hash, emailed to user
     *
     * @param string $hash SHA1-hash
     * @return bool TRUE if there is such a hash
     */
    public function activate_account($hash)
    {
        if($hash == '0')
        {
            return FALSE;
        }

        $this->ci->db->select('id')->where('cookie_id', $hash);
        $query = $this->ci->db->get($this->table_users);

        if($query->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            $result = $query->result_array();
            $id = $result[0]['id'];
            
            $update = array(
                'cookie_id' =>  '0',
                'active'    =>  '1'
            );
            $this->ci->db->where('id', $id)->update($this->table_users, $update);

            return TRUE;
        }
    }

    /**
     * Add new user to db
     *
     * @param array $data Array with keys as fields names
     * @return string Hash value for user activation
     */
    public function register_user($data)
    {
        $this->ci->load->model('admin/operate_users');
        
        $data['password'] = sha1($data['password'] . $this->hashing_salt);
        $data['created']  = time();
        $data['role_id']  = $this->ci->operate_users->get_role_id('user');

        $data['cookie_id'] = sha1($data['role_id'] . $data['created'] . $data['email']);
        
        $this->ci->db->insert($this->table_users, $data);

        return $data['cookie_id'];
    }

    /**
     * Check if email is present in database
     *
     * @param string $email User mail to check for existence
     * @return bool TRUE if email exists
     */
    public function check_email($email)
    {
        $this->ci->db->select('id')->where('email', $email);
        $query = $this->ci->db->get($this->table_users);

        if($query->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    /**
     * Generate new password for specified email and update db
     *
     * @param string $email User mail
     * @return string New password
     */
    public function get_new_password_for_email($email)
    {
        $this->ci->load->helper('string');
        $this->ci->load->library('email');

        $password = random_string('alnum', 8);
        $password_hash = sha1($password . $this->hashing_salt);

        $update = array(
            'password'  =>  $password_hash
        );

        $this->ci->db->where('email', $email);
        $this->ci->db->update($this->table_users, $update);

        return $password;
    }
}

/* End of file modules.php */
/* Location: ./application/models/modules.php */