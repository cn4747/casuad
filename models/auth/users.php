<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model works with user data, stored in DB
 *
 * @package	Auth
 * @author	Anton Martynenko
 */
class Users extends CI_Model
{
    //user data
	private $table			= 'users';
    //user roles
	private $table_roles	= 'user_roles';
    //CI instance pointer
    private $ci;
    //current user id
    private $user_id;
    //hashing salt value from config
    private $hashing_salt;

	function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
        $this->ci->load->database();
        $this->ci->load->config('auth', TRUE);

        //get salt value from config and set it to private var
        $this->hashing_salt = $this->ci->config->item('salt_value', 'auth');
	}

    /**
     * Check if user has activated his account
     *
     * @param string $email User email
     * @return bool TRUE if user is activated
     */
    public function is_active($email)
    {
        $where = array('email' => $email, 'active' => '1');
        $this->ci->db->select('id')->where($where);
        $query = $this->ci->db->get($this->table);

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
     * This method does all database work during user login
     *
     * @param string $email     - user mail
     * @param string $password  - password
     * @return boolean
     */
    public function login_user($email, $password)
    {
        //making a query with ActiveRecord
        $this->ci->db->select('id');
        $where = array(
            'email' => $email,
            'password' => sha1($password . $this->hashing_salt)
        );
        $query = $this->ci->db->get_where($this->table, $where);
        
        if($query->num_rows == 0)
        {
            return FALSE; //if no such username or password
        }
        else
        {
            //set user_id for further method calls for current user
            $obj = $query->result();
            $this->user_id = $obj[0]->id;

            return TRUE;
        }
    }

    /**
     * Login user with social networks and update hash (cookie_id) in db
     *
     * @param string $idstr User ID, nick or email
     * @return string Generated hash
     */
    public function login_with_social($idstr)
    {
        //making a query with ActiveRecord
        $this->ci->db->select('id');
        $where = array(
            'nick' => $idstr
        );
        $query = $this->ci->db->get_where($this->table, $where);

        if($query->num_rows == 0)
        {
            return NULL; //if no such user
        }
        else
        {
            $obj = $query->result();
            $hash = sha1($this->hashing_salt . $obj[0]->id);
            $this->set_hash($hash, $obj[0]->id);

            return $hash;
        }
    }

    /**
     * Get associated array of current user data
     *
     * @param strgin $cookie_id
     * @return <type>
     */
    function get_userdata($cookie_id = NULL)
    {
        $this->ci->db->select('u.id AS user_id, u.active, u.nick, u.email, u.avatar, ur.name AS role')
                     ->from('users u')
                     ->join($this->table_roles . ' ur', 'ur.id = u.role_id');

        //if we retrieve data by user_id
        if($cookie_id === NULL)
        {
            $this->ci->db->where('u.id', $this->user_id);
        }
        //if we login by cookie and cookie is really and sha1-hash
        else if(strlen($cookie_id) == 40)
        {
            $this->ci->db->where('u.cookie_id', $cookie_id);
        }
        //if neither, return NULL
        else
        {
            return NULL;
        }

        $query = $this->ci->db->get();
        if($query->num_rows == 0)
        {
            return NULL;
        }

        //make an array of returned data
        $data = array();
        foreach($query->result_array() as $row)
        {
            foreach($row as $key => $value)
                $data[$key] = $value;
        }
        
        return $data;
    }

    public function get_userdata_by_params($params)
    {
        $this->ci->db->select('u.id AS user_id, u.active, u.nick, u.email, u.avatar, ur.name AS role')
                     ->from('users u')
                     ->join($this->table_roles . ' ur', 'ur.id = u.role_id');

        if( ! empty($params['nick']))
        {
            $this->ci->db->where('u.nick', $params['nick']);
        }
        else if( ! empty($params['email']))
        {
            $this->ci->db->where('u.email', $params['email']);
        }
        else if( ! empty($params['id']))
        {
            $this->ci->db->where('u.id', $params['id']);
        }
        else
        {
            return NULL;
        }

        $query = $this->ci->db->get();
        if($query->num_rows == 0)
        {
            return NULL;
        }

        //make an array of returned data
        $data = array();
        foreach($query->result_array() as $row)
        {
            foreach($row as $key => $value)
                $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Get generated hash for current user
     */
    public function get_cookie_id()
    {
        //generate sha1 hash with random value (for better security)
        $cookie_id = sha1($this->user_id . $this->hashing_salt . mt_rand(1, 9999));
        //update users table to recognize the user later by hash
        $this->ci->db->where('id', $this->user_id);
        $this->ci->db->update($this->table, array('cookie_id' => $cookie_id));

        return $cookie_id;
    }

    /**
     * Check if user is registered, during the social auth
     *
     * @param string $idstr User ID - email or nick
     * @return bool TRUE if user exists
     */
    public function is_registered($idstr)
    {
        $query = $this->ci->db->select('id')
                    ->where('nick', $idstr)
                    ->get($this->table);

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
     * Clear users.cookie_id when logging out
     *
     * @param int $user_id
     * @return void
     */
    public function clear_cookie($user_id)
    {
        $this->ci->db->where('id', $user_id);
        $this->ci->db->update($this->table, array('cookie_id' => '0'));
    }

    public function insert_social($idstr)
    {
        $insert = array(
            'active'    =>  '1',
            'role_id'   =>  $this->get_role_id('user'),
            'created'   =>  time(),
            'nick'      =>  $idstr
        );

        if(strpos($idstr, '@'))
        {
            $insert['email'] = $idstr;
        }

        $this->ci->db->insert($this->table, $insert);
    }

    /**
     * Get role id by name
     *
     * @param string $name Role name
     * @return int Role id
     */
    public function get_role_id($name)
    {
        $query = $this->ci->db->select('id')
                    ->where('name', $name)
                    ->get($this->table_roles);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            $result = $query->result_array();

            return $result[0]['id'];
        }
    }

    /**
     * Set auth hash for user id
     *
     * @param string $hash Hash (cookie_id) value
     * @param int $id_user User id
     */
    public function set_hash($hash, $id_user)
    {
        $this->ci->db->where('id', $id_user)
                    ->update($this->table, array('cookie_id' => $hash));
    }
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */