<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Banners
 *
 * This model works with advertising blocks
 *
 * @author	Anton Martynenko
 */
class Banners extends CI_Model
{
    /**
     * @var string Table with banners
     */
	private $table_banners = 'banners';

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

    public function add($data)
    {
        $this->ci->db->insert($this->table_banners, $data);
    }

    public function delete($id)
    {
        $query = $this->ci->db->select('image')
                            ->where('id', $id)
                            ->get($this->table_banners);

        if($query->num_rows() != 0)
        {
            $result = $query->result_array();
            unlink('uploads/banners/' . $result[0]['image']);

            $this->ci->db->where('id', $id)
                    ->delete($this->table_banners);
        }
    }

	public function get_banner($id)
    {
        $query = $this->ci->db->select('id, text1, text2, image, url')
							->where('id', $id)
                            ->get($this->table_banners);

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

    public function get_banners()
    {
        $query = $this->ci->db->select('id, text1, text2, image, date, url')
                            ->order_by('date', 'desc')
                            ->get($this->table_banners);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

    public function get_data($id)
    {
        $query = $this->ci->db->select('text1, text2, image, url')
                            ->where('id', $id)
                            ->get($this->table_banners);

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

    public function update($data, $id)
    {
        $this->ci->db->where('id', $id)
                    ->update($this->table_banners, $data);
    }
}

/* End of file banners.php */
/* Location: ./application/models/banners.php */