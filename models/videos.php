<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Videos
 *
 * This model works with videos data
 *
 * @author	Anton Martynenko
 */
class Videos extends CI_Model
{
    /**
     * @var string Table with videos
     */
	private $table_videos = 'videos';

    /**
     * @var string Table with tags data
     */
    private $table_tags = 'tags';

    /**
     * @var string Table with tags to video relations
     */
    private $table_relations = 'tag_relations';

    /**
     * @var string Table with rated videos
     */
    private $table_likes = 'video_likes';

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

    public function add($data)
    {
        $keywords = explode(',', $data['keywords']);
        unset($data['keywords']);

        $data['time'] = time();

        $this->ci->db->insert($this->table_videos, $data);

        $insert_id = $this->db->insert_id();
        $this->add_keywords($insert_id, $keywords);
    }

    public function add_keywords($id, $keywords)
    {
        foreach($keywords as $item)
        {
            $item = trim($item);
            $this->ci->db->select('id')->where('name', $item);
            $query = $this->ci->db->get($this->table_tags);

            if($query->num_rows() == 0)
            {
                $this->ci->db->insert($this->table_tags, array('name'=>$item));
                $tag_id = $this->db->insert_id();
            }
            else
            {
                $result = $query->result_array();
                $tag_id = $result[0]['id'];
            }

            $relation_item = array(
                'id_video'  =>  $id,
                'id_tag'    =>  $tag_id
            );
            $this->ci->db->insert($this->table_relations, $relation_item);
        }
    }

    public function add_points($id, $add_points)
    {
        $this->ci->db->where('id', $id)
                ->update($this->table_videos, array('points' => $add_points));
    }

    public function get_top_video()
    {
        $this->ci->db->select('title, descr, link')
                    ->order_by('views', 'DESC')
                    ->limit(1);

        $query = $this->ci->db->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return array(
                'title' =>  '',
                'descr' =>  '',
                'link'  =>  ''
            );
        }
        else
        {
            $result = $query->result_array();
            return $result[0];
        }
    }

    public function get_user_points($id_user)
    {
        $query = $this->ci->db->select('SUM(points) AS points')
                        ->where('id_user', $id_user)->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return 0;
        }
        else
        {
            $result = $query->result_array();
            return $result[0]['points'];
        }
    }

    public function get_user_videos($id_user, $page, $per_page)
    {
        $page = ($page - 1) * $per_page;
        $limit = "{$page}, {$per_page}";
        $sql = "SELECT id, rating, title AS video_title, time, views, thumb, link
                FROM
                    (SELECT @rownum:=@rownum+1 rating, videos.* FROM videos,
                        (SELECT @rownum:=0) r
                      ORDER BY views DESC, time DESC) a
                WHERE id_user = '{$id_user}' LIMIT {$limit}";
        $query = $this->ci->db->query($sql);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

    public function get_user_videos_count($id)
    {
        $this->ci->db->where('id_user', $id);
        $query = $this->ci->db->get($this->table_videos);

        return $query->num_rows();
    }

    public function get_video($id)
    {
        $this->ci->db->select('id, title AS video_title, views, thumb, link')
                        ->where('id', $id);

        $query = $this->ci->db->get($this->table_videos);

        if($query->num_rows() == 0)
        { 
            return NULL;
        }
        else
        {
            $result = $query->result_array();

            $result = $query->result_array();

            //set video likes
            $query = $this->ci->db->select('COUNT(likes) AS likes')->where(array(
                        'likes' => '1',
                        'id_video' => $result[0]['id']
                    ))->get($this->table_likes);

            if ($query->num_rows() == 0) {
                $result['likes'] = 0;
            } else {
                $likes = $query->result_array();
                $result[0]['likes'] = $likes[0]['likes'];
            }

            //set video unlikes
            $query = $this->ci->db->select('COUNT(likes) AS unlikes')->where(array(
                        'likes' => '-1',
                        'id_video' => $result[0]['id']
                    ))->get($this->table_likes);

            if ($query->num_rows() == 0) {
                $result[0]['unlikes'] = 0;
            } else {
                $likes = $query->result_array();
                $result[0]['unlikes'] = $likes[0]['unlikes'];
            }

            return $result[0];
        }
    }

    public function get_videos($page, $per_page, $type)
    {
        $this->ci->db->select('id, title AS video_title, time, views, thumb, link, points')
                        //->where('link <>', '')
                        ->limit($per_page, ($page - 1) * $per_page);

        if($type == 'fresh')
        {
            $this->ci->db->order_by('time', 'desc');
        }
        else if($type == 'rating')
        {
            $this->ci->db->order_by('views', 'desc');
        }

        $query = $this->ci->db->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

    public function get_videos_with_points($id_user, $page = 1, $per_page = 4)
    {
        $this->ci->db->select('id, title AS video_title, points, thumb, link')
                        ->where(array(
                            'id_user' => $id_user,
                            'points <>' => '0'
                            ))
                        ->limit($per_page, ($page - 1) * $per_page);

        $query = $this->ci->db->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

    public function get_videos_count()
    {
        $result = $this->ci->db->select('COUNT(id) AS count')
                        ->where('link <>', '')
                        ->get($this->table_videos)->result_array();

        return $result[0]['count'];
    }

	public function get_videos_with_points_count($id_user)
    {
        $result = $this->ci->db->select('COUNT(id) AS count')
                        ->where(array(
							'link <>'	=>	'',
							'points <>'	=>	'0',
							'id_user'	=>	$id_user
						))
                        ->get($this->table_videos)->result_array();

        return $result[0]['count'];
    }

    public function rate($id, $hash, $rating)
    {
        $this->ci->db->select('id')->where('SHA1(CONCAT(id, email)) = ', $hash);
        $query = $this->ci->db->get($this->table_users);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            if($rating === 1)
            {
                $result_likes_check = '-1';
            }
            else
            {
                $result_likes_check = '1';
            }

            $result = $query->result_array();
            $id_user = $result[0]['id'];

            $this->ci->db->select('likes')->where('id_user', $id_user);
            $query = $this->ci->db->get($this->table_likes);
            
            if($query->num_rows() == 0)
            {
                $insert = array(
                    'id_video' => $id,
                    'id_user' => $id_user,
                    'likes' => $rating
                );
                $this->ci->db->insert($this->table_likes, $insert);
            }
            else
            {
                $this->ci->db->where(array(
                    'id_video' => $id,
                    'id_user' => $id_user
                ));
                $this->ci->db->update($this->table_likes, array('likes' => $rating));
            }

            return TRUE;
        }
    }

    public function search($key, $page = 0, $per_page = 0)
    {
        $this->ci->db->select('id, title AS video_title, descr, thumb, link')
                        ->like('title', $key)
                        ->or_like('descr', $key);

        if($page > 0 && $per_page > 0)
        {
            $this->ci->db->limit($per_page, ($page - 1) * $per_page);
        }

        $query = $this->ci->db->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
    }

	public function search_hints($key)
	{
		$this->ci->db->select('title')
                        ->like('title', $key)
						->limit(10);

        $query = $this->ci->db->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return NULL;
        }
        else
        {
            return $query->result_array();
        }
	}

    public function get_search_results($key)
    {
        $this->ci->db->select('id, title AS video_title, descr, thumb, link')
                        ->like('title', $key)
                        ->or_like('descr', $key);

        $query = $this->ci->db->get($this->table_videos);

        return $query->num_rows();
    }

    public function update_thumb($id)
    {
        $this->ci->db->where('id', $id);
        $this->ci->db->update($this->table_videos, array('thumb' => '1'));
    }

    public function update_views($id)
    {
        $query = $this->ci->db->select('views')
                        ->where('id', $id)
                        ->get($this->table_videos);

        if($query->num_rows() == 0)
        {
            return FALSE;
        }
        else
        {
            $result = $query->result_array();

            $this->ci->db->where('id', $id);
            $this->ci->db->update($this->table_videos, array('views' => $result[0]['views'] + 1));
            
            return TRUE;
        }
    }
}

/* End of file videos.php */
/* Location: ./application/models/videos.php */