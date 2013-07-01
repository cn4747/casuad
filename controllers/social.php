<?php

class Social extends MY_Controller {

    function  __construct() {
        parent::__construct();
    }

	function index()
	{
        $this->load->library('auth');
        
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        $url = "http://loginza.ru/api/authinfo?token={$token}";
        $obj = json_decode(file_get_contents($url));
        
        if( ! isset($obj->error_type))
        {
            if(strpos($obj->provider, 'twitter'))
                $idstr = $obj->nickname;
            else if(strpos($obj->provider, 'facebook'))
                $idstr = $obj->email;
            else if(strpos($obj->provider, 'google'))
                $idstr = $obj->email;
            
            if($this->auth->is_registered($idstr) === TRUE)
            {
                if($this->auth->auth_by_social($idstr) === TRUE)
                    header('Location: ' . $this->config->item('base_url') . 'mod/usercp');
                else
                    $this->template->show('social_auth_error');
            }
            else
            {
                $this->auth->register_by_social($idstr);
                
                header('Location: ' . $this->config->item('base_url') . 'mod/usercp');
            }
        }
        else
        {
            $this->template->show('social_auth_error');
        }
	}

}
?>
