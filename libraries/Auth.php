<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Auth
 *
 * Authentication library for CodeIgniter.
 *
 * @package		Auth
 * @author		Anton Martynenko (http://niggaslife.ru)
 * @version		0.1
 * @license		WTFPL
 */
class Auth {
    private $error = '';

	function __construct()
	{
		$this->ci =& get_instance();

        $this->ci->lang->load('auth', 'russian');
		$this->ci->load->config('auth', TRUE);

		$this->ci->load->library(array('session', 'input'));
		$this->ci->load->model('auth/users');

		//if user has cookie, autologin
        if($this->ci->input->cookie('cookie_id') !== FALSE)
        {
            $this->auth_by_cookie($this->ci->input->cookie('cookie_id'));
        }
	}

    public function get_error()
    {
        return $this->error;
    }

    /**
     * Log user in and set session data
     *
     * @param string $login
     * @param string $password
     * @param bool   $remember
     * @return bool
     */
    public function login($login, $password, $remember = FALSE)
    {
        if($this->ci->users->is_active($login) == FALSE)
        {
            $this->error = $this->ci->lang->line('auth_inactive_user');
            return FALSE;
        }
        else if($this->ci->users->login_user($login, $password) === TRUE)
        {
            $userdata = $this->ci->users->get_userdata();

            //check if we received data correctly
            if($userdata == NULL)
            {
                log_message('error', 'Received NULL while logging user in. Check users model and user_id');
                return FALSE;
            }

            $this->ci->session->set_userdata($userdata);

            //if the user wants to be remembered
            if($remember !== FALSE)
            {
                $cookie = array(
                    'name'      => 'cookie_id',
                    'value'     => $this->ci->users->get_cookie_id(),
                    'expire'    => time() + $this->ci->config->item('cookie_time', 'auth')
                );
                $this->ci->input->set_cookie($cookie);
            }
        }
        else
        {
            $this->error = $this->ci->lang->line('auth_wrong_auth');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Auth user with cookie he's got
     *
     * @param string $cookie_id
     * @return bool
     */
    public function auth_by_cookie($cookie_id)
    {
        //get user data by cookie
        $userdata = $this->ci->users->get_userdata($cookie_id);

        //if wrong cookie...
        if($userdata === NULL)
        {
            //...write log
            log_message('error', 'Received NULL while logging user in by cookie. Check cookie_id');

            return FALSE;
        }
        else
        {
            //set data if everything OK
            $this->ci->session->set_userdata($userdata);
        }
    }

    /**
     * Auth user by social networks
     *
     * @param string $idstr User ID, nick or email
     * @return bool TRUE if logged in
     */
    public function auth_by_social($idstr)
    {
        $cookie_id = $this->ci->users->login_with_social($idstr);

        //get user data by cookie
        $userdata = $this->ci->users->get_userdata($cookie_id);

        //if wrong cookie...
        if($userdata === NULL)
        {
            //...write log
            log_message('error', 'Received NULL while logging user in by cookie. Check cookie_id');

            return FALSE;
        }
        else
        {
            //set data if everything OK
            $this->ci->session->set_userdata($userdata);

            return TRUE;
        }
    }

    /**
     * Check if current user is admin
     *
     * @return bool
     */
    public function is_admin()
    {
        $role = $this->ci->session->userdata('role');
        if($role === FALSE)
        {
            return FALSE;
        }
        else if($role == 'admin')
        {
            return TRUE;
        }
    }

    /**
     * Check if user with specified email or nick exists
     *
     * @param string $idstr User ID - nick or email
     * @return bool TRUE if user exists
     */
    public function is_registered($idstr)
    {
        return $this->ci->users->is_registered($idstr);
    }

    /**
     * Destroy session and/or clear cookies
     */
    public function logout()
    {
        if($this->ci->input->cookie('cookie_id') !== FALSE)
        {
            $this->ci->users->clear_cookie($this->ci->session->userdata('user_id'));
            $this->ci->input->set_cookie('cookie_id');
        }

        $this->ci->session->sess_destroy();
    }

    public function register_by_social($idstr)
    {
        $this->ci->users->insert_social($idstr);
        $this->auth_by_social($idstr);
    }

    public function update_userdata($data = array())
    {
        if(count($data) > 0)
        {
            $userdata = $this->ci->users->get_userdata_by_params($data);

            $this->ci->session->set_userdata($userdata);
        }
        else
        {
            return FALSE;
        }
    }
}
/* End of file auth.php */
/* Location: ./application/libraries/auth.php */