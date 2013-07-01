<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Modules
 *
 * This model works with modules data
 *
 * @author	Anton Martynenko
 */
class Modules extends CI_Model
{
    /**
     * @var string Table with modules
     */
	private $table = 'modules';

    /**
     * @var string Table with module permissions
     */
    private $table_perm = 'module_permissions';

    /**
     * @var string Table with roles
     */
    private $table_roles = 'user_roles';

    /**
     * @var Object CI instance, assigned by &get_instance()
     */
    private $ci;
    
	function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
        $this->ci->load->database();
	}

    /**
     * Get all modules as array from database
     *
     * @param bool $admin TRUE if you need control panel modules,
     *                    FALSE if you need frontend modules
     * @return array Returns fetched array from db with list of modules
     */
    public function get_modules($admin)
    {
        $where = array();
        if($admin === TRUE)
        {
            $where['is_admin'] = '1';
        }
        else
        {
            $where['is_admin'] = '0';
        }

        $this->ci->db->select('file, name, enabled');
        $this->ci->db->where($where);
        
        return $this->ci->db->get($this->table)->result_array();
    }

    /**
     * Get only enabled modules with corresponding user rights from db
     *
     * @param bool $admin TRUE if you need control panel modules<br/>
     *                    FALSE if you need frontend modules
     * @return array Returns enabled modules as array, fetched from db
     */
    public function get_enabled_modules($admin)
    {
        $where = array();
        if($admin === TRUE)
        {
            $where['m.is_admin'] = '1';
        }
        else
        {
            $where['m.is_admin'] = '0';
        }

        $where['m.enabled'] = '1';
        $where['mp.perm <>'] = '0';
        $where['r.name'] = $this->ci->session->userdata('role');

        $this->ci->db->select('m.file, m.name')
                     ->from($this->table . ' AS m')
                     ->join($this->table_perm . ' AS mp', 'mp.id_module = m.id')
                     ->join($this->table_roles . ' AS r', 'r.id = mp.id_role')
                     ->where($where);

        return $this->ci->db->get()->result_array();
    }

    /**
     * Method get title from db for the specified module
     *
     * @param string $file File name (with extension)
     * @return string Title of the specified module. Empty string if no title
     */
    public function get_title($file)
    {
        $query = $this->ci->db->select('name')->where('file', $file)
                               ->get($this->table);

        if($query->num_rows() == 0)
        {
            return '';
        }
        else
        {
            $result = $query->result_array();
            
            return $result[0]['name'];
        }
    }

    /**
     * Check if user role is permitted to access the module
     *
     * @param string $file Module file name
     * @param string $role_name User role name
     * @return bool Returns TRUE if permitted
     */
    public function is_permitted($file, $role_name)
    {
        $where = array(
            'm.file' => $file,
            'r.name' => $role_name,
            'p.perm <>' => '0'
        );

        $query = $this->ci->db->select('m.id')
                     ->from($this->table . ' AS m')
                     ->join($this->table_perm . ' AS p', 'p.id_module = m.id')
                     ->join($this->table_roles . ' AS r', 'r.id = p.id_role')
                     ->where($where)->get();

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
     * Update the modules with data
     *
     * @param array $data string ['file']  - file name to update<br>
     *                    string ['name']  - module name, displayed as title<br>
     *                    bool ['enabled'] - enabled or disable module
     * @return bool Returns boolean value to indicate whether the update
     *              process wass successful or not
     */
    public function update_module($data)
    {
        //if one of obligatory fields is not set, return FALSE
        if( ! isset($data['file']) OR ! isset($data['enabled']) OR ! isset($data['name']))
        {
            return FALSE;
        }
        else
        {
            $insert = array();

            //convert enabled parameter to integer
            $insert['enabled'] = intval($data['enabled']);

            if( ! empty($data['name']))
            {
                $insert['name'] = $data['name'];
            }

            if( ! empty($insert))
            {
                $this->db->where('file', $data['file']);
                $this->db->update($this->table, $data);
            }

            return TRUE;
        }
    }

    /**
     * Update modules table in db from given list of file names
     *
     * @param array $list list of file names
     * @param int $admin 0 if module is frontend<br>
     *                   1 if module is in admin panel
     */
    public function update_modules_list($list, $admin)
    {
        foreach($list as $item)
        {
            //check if current file exists in db
            $sql = "SELECT file FROM {$this->table} WHERE file = '{$item}'";
            $query = $this->db->query($sql);
            //make an insert if not
            if($query->num_rows() == 0)
            {
                $data = array(
                    'file'     => $item,
                    'is_admin' => $admin
                );
                $this->db->insert($this->table, $data);
            }
        }
    }

    /**
     * Deletes module from db. However, it remains on the disk
     *
     * @param string $file File name to delete
     */
    public function delete_module($file)
    {
        $this->ci->db->delete($this->table, array('file' => $file));
    }
}

/* End of file modules.php */
/* Location: ./application/models/modules.php */