<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Operate_Users
 *
 * This model creates, updated and deletes users
 *
 * @author	Anton Martynenko
 */
class Operate_Users extends CI_Model
{
    /**
     * @var string Table with user data
     */
	private $table = 'users';
    
    /**
     * @var string Table with user roles
     */
	private $table_roles = 'user_roles';

    /**
     * @var string Salt value from config file, to generate hashes
     */
    private $hashing_salt;

    /**
     * @var Object CI instance, assigned by &get_instance()
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

    /**
     * Get inner variable that contains roles table name
     *
     * @return string Roles table name
     */
    public function get_roles_table()
    {
        return $this->table_roles;
    }

    /**
     * Get inner variable that contains users table name
     *
     * @return string Users table name
     */
    public function get_users_table()
    {
        return $this->table;
    }

    /**
     * Add new user to database
     *
     * @param array $data Array, where keys are fields in table
     */
    public function add_user($data)
    {
        $data['password'] = sha1($data['password'] . $this->hashing_salt);
        $data['created']  = time();

        $this->ci->db->insert($this->table, $data);
    }

    /**
     * Add new role to database
     *
     * @param array $data Array, where keys are fields in table
     */
    public function add_role($data)
    {
        $this->ci->db->insert($this->table_roles, $data);
    }

    public function delete_avatar($old)
    {
        $avatar_file = './uploads/avatars/' . $old;
        if(file_exists($avatar_file))
        {
            unlink($avatar_file);
        }
        else
        {
            $this->ci->db->update($this->table, array('avatar' => ''));
        }
    }

    /**
     * Delete specified role
     *
     * @param int $id Role ID
     */
    public function delete_role($id)
    {
        $this->ci->db->where('id', $id)->delete($this->table_roles);
    }

    /**
     * Delete specified user
     *
     * @param int $id User ID
     */
    public function delete_user($id)
    {
        $this->ci->db->where('id', $id)->delete($this->table);
    }

    public function get_avatar($email)
    {
        $query = $this->ci->db->select('avatar')->where('email', $email)->get($this->table);

        $result = $query->result_array();

        return $result[0]['avatar'];
    }

    public function get_confirm_email($id)
    {
        $this->ci->db->select('confirm_email')->where('id', $id);
        $query = $this->ci->db->get($this->table);
        
        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            $result = $query->result_array();

            return $result[0]['confirm_email'];
        }
    }

    public function get_user_id($email)
    {
        $this->ci->db->select('id')->where('email', $email);
        $query = $this->ci->db->get($this->table);

        if($query->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            $result = $query->result_array();
            return $result[0]['id'];
        }
    }

    public function get_user_data($email)
    {
        $this->ci->db->select('id, nick, email, avatar')->where('email', $email);
        $query = $this->ci->db->get($this->table);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            $result = $query->result_array();
            return $result[0];
        }
    }
    
    public function get_user_data_by_id($id)
    {
        $query = $this->ci->db->select('nick, email')->where('id', $id)->get($this->table);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            $result = $query->result_array();

            return $result[0];
        }
    }

    /**
     * Get users by role name
     *
     * @param string $role_name Role name to filter selection
     * @return array Returns array with users selected by role name
     */
    public function get_users($role_name, $page = 0, $per_page = 0)
    {
        $role_id = $this->get_role_id($role_name);

        $this->ci->db->select('id, role_id, active, created, nick, email')->where('role_id', $role_id);

        if($page > 0 && $per_page > 0)
        {
            $this->ci->db->limit($per_page, ($page - 1) * $per_page);
        }

        return $this->ci->db->get($this->table)->result_array();
    }

    /**
     * Get role id by name
     *
     * @param string $role_name Role name, e.g. admin
     * @return int Returns role id
     */
    public function get_role_id($role_name)
    {
        $query = $this->ci->db->select('id')
                               ->where('name', $role_name)
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
     * Get role title by role name
     *
     * @param string $role_name Role name, e.g. admin
     * @return string Returns role title
     */
    public function get_role_title($role_name)
    {
        $result = $this->ci->db->select('title')
                               ->where('name', $role_name)
                               ->get($this->table_roles)
                               ->result_array();

        return $result[0]['title'];
    }

    /**
     * Get array with user roles (groups)
     *
     * @return array Returns list of user roles as array
     */
    public function get_roles()
    {
        $this->ci->db->select('id, name, title, descr');

        return $this->ci->db->get($this->table_roles)->result_array();
    }

    /**
     * Get roles in key => value pairs, where key is ID in db
     *
     * @return array Key => Value paired list, where Keys are ID in db
     */
    public function get_roles_list()
    {
        foreach($this->get_roles() as $item)
        {
            $roles_list[$item['id']] = $item['title'];
        }

        return $roles_list;
    }

    public function get_users_count($role = NULL)
    {
        if($role !== NULL)
        {
            $this->ci->db->join($this->table_roles, "{$this->table_roles}.id = {$this->table}.role_id")
                        ->where($this->table_roles . '.name', $role);
        }

        $query = $this->ci->db->select($this->table . '.id')->get($this->table);

        return $query->num_rows();
    }

    /**
     * Set new user data
     *
     * @param array $data Data to update, keys are fields, values are data
     */
    public function update_user($data)
    {
        //unset password if empty
        if(empty($data['password']))
        {
            unset($data['password']);
        }
        //or hash with salt if there is a value
        else
        {
            $data['password'] = sha1($data['password'] . $this->hashing_salt);
        }
        
        //get integer value from active flag
        if(isset($data['active']))
            $data['active'] = intval($data['active']);

        $this->ci->db->where('id', $data['id']);
        unset($data['id']);

        $this->ci->db->update($this->table, $data);
    }

    /**
     * Update user role data
     *
     * @param array $data Keys => Values array with data, Keys are fields
     */
    public function update_role($data)
    {
        $this->ci->db->where('id', $data['id']);
        unset($data['id']);
        $this->ci->db->update($this->table_roles, $data);
    }
}

/* End of file operate_users.php */
/* Location: ./application/models/operate_users.php */