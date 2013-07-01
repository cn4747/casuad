<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Messages
 *
 * This model works with messages
 *
 * @author	Anton Martynenko
 */
class Messages extends CI_Model
{
    /**
     * @var string Table with messages
     */
	private $table_messages = 'messages';

    /**
     * @var string Table with user data
     */
    private $table_users = 'users';

    /**
     * @var Object CodeIgniter instance pointer
     */
    private $ci;

	function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
        $this->ci->load->database();
	}

    public function delete($id)
    {
        $this->ci->db->where('id', $id)
                ->delete($this->table_messages);
    }

    public function get_messages($id_user)
    {
        $query = $this->ci->db->select('id, title AS message_title, body, date, read')
                            ->where('id_user', $id_user)
                            ->order_by('date', 'desc')
                            ->get($this->table_messages);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

    public function get_message($id)
    {
        $query = $this->ci->db->select('id, title AS message_title, body')
                            ->where('id', $id)
                            ->get($this->table_messages);

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

    public function set_read($id)
    {
        $this->ci->db->where('id', $id)
                    ->update($this->table_messages, array('read' => '1'));
    }

    public function get_messages_count()
    {
        //$query = $this->ci->db->select('id')->get($this->table_messages);
        return 0;
    }

    public function get_new_messages_count($id)
    {
        $query = $this->ci->db->where(array(
            'id_user'   =>  $id,
            'read'      =>  '0'))
                        ->get($this->table_messages);

        return $query->num_rows();
    }

    public function send_message($data)
    {
        $this->ci->db->insert($this->table_messages, $data);
    }
}

/* End of file messages.php */
/* Location: ./application/models/messages.php */