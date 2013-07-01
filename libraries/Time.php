<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Auth
 *
 * Time transforming library for CodeIgniter.
 *
 * @package		Auth
 * @author		Anton Martynenko (http://niggaslife.ru)
 * @version		0.1
 * @license		WTFPL
 */
class Time {

	function __construct()
	{
		$this->ci =& get_instance();
    }

    public function transform($time)
    {
        $now = time();
        $time = $now - $time;

        //минуты
        if($time < 3600)
        {
            $round = ceil($time / 60);

            if($round < 2 OR substr($round, -1, 1) == '1')
                $result = $round . ' минуту';
            else if($round < 5)
                $result = $round . ' минуты';
            else if($round < 61)
                $result = $round . ' минут';
        }
        //часы
        else if($time < 86400)
        {
            $round = ceil($time / 3600);
            
            if($round < 2 OR $round == 21)
                $result = $round . ' час';
            else if($round < 5)
                $result = $round . ' часа';
            else if($round < 25)
                $result = $round . ' часов';
        }
        //дни
        else
        {
            $round = ceil($time / 86400);
            
            if($round < 2 OR substr($round, -1, 1) == '1')
                $result = $round . ' день';
            else if($round < 5)
                $result = $round . ' дня';
            else
                $result = $round . ' дней';
        }

        return $result;
    }

}
/* End of file Time.php */
/* Location: ./application/libraries/Time.php */