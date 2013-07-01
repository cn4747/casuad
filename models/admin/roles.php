<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Roles
 *
 * This model works with roles and shit
 *
 * @author	Anton Martynenko
 */
class Roles extends CI_Model
{
    //table with roles
	private $table = 'user_roles';
    //table with modules
	private $table_modules = 'modules';
    //table with permissions
	private $table_perm = 'module_permissions';
    //CI instance pointer
    private $ci;
    
	function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
        $this->ci->load->database();
	}

    public function get_roles()
    {
        $this->ci->db->select('id, name, title');
        return $this->ci->db->get($this->table)->result_array();
    }

    public function get_modules_by_role_name($role_name)
    {
        $where = array(
            'm.is_admin' => '1',
            'r.name'     => $role_name
        );

        $this->ci->db->select('m.file, m.name, mp.perm');
        $this->ci->db->from($this->table_modules . ' AS m');
        $this->ci->db->join($this->table_perm . ' AS mp', 'mp.id_module = m.id');
        $this->ci->db->join($this->table . ' AS r', 'r.id = mp.id_role');
        $this->ci->db->where($where);
        
        $query = $this->ci->db->get();

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

    public function update_permissions($role_name)
    {
        $query = $this->ci->db->select('id')->from($this->table)->where('name', $role_name)->get()->result_array();
        $role_id = $query[0]['id'];

        $query = $this->ci->db->select('id')->from($this->table_modules)->where('is_admin', '1')->get();

        if($query->num_rows() == 0)
        {
            return FALSE;
        }

        foreach($query->result_array() as $item)
        {
            $insert = array(
                'id_module' => $item['id'],
                'id_role'   => $role_id,
                'perm'      => '0'
            );

            //set where condition
            $where = $insert;
            //without extra values, only module and role id's
            unset($where['perm']);
            //compose query and get data
            $check = $this->db->select('id')->from($this->table_perm)->where($where)->get();
            
            if($check->num_rows() == 0)
            {
                $this->ci->db->insert($this->table_perm, $insert);
            }
        }
    }

    public function update_module_permissions($data)
    {
        $query = $this->ci->db->select('id')
                    ->from($this->table)
                    ->where('name', $data['role'])
                    ->get()->result_array();
        $id_role = $query[0]['id'];

        $query = $this->ci->db->select('id')
                    ->from($this->table_modules)
                    ->where('file', $data['file'])
                    ->get()->result_array();
        $id_module = $query[0]['id'];

        $update = array(
            'id_role'   => $id_role,
            'id_module' => $id_module,
            'perm'      => intval($data['perm'])
        );

        $where = $update;
        unset($where['perm']);
        $this->ci->db->where($where)
                     ->update($this->table_perm, $update);
    }
}

/* End of file modules.php */
/* Location: ./application/models/modules.php */