<?php
if ( ! function_exists('seconds_interval'))
{
	/**
	 * Seconds Interval
	 *
	 * Returns interval of two datetime objects in seconds.
	 *
	 * @param	datetime	Start DateTime
	 * @param	datetime	End DateTime
	 * @return	int	difference in seconds
	 */
	function seconds_interval($datetime1,$datetime2)
	{
		$time1 = $datetime1->getTimestamp();
		$time2 = $datetime2->getTimestamp();
		return ($time1 - $time2);
	}
}

if ( ! function_exists('datetime_mysql_string'))
{
	/**
	 * datetime_mysql_string
	 *
	 * Returns Datetime as string in MySQL format (Y-m-d h:i:s)
	 *
	 * @param	int	unix timestamp
	 * @return	string datetime in MySQL format
	 */
	function datetime_mysql_string($time = '')
	{
		if(empty($time) || !is_integer($time)) {
			$time = time();	
		}
		return date("Y-m-d h:i:s", $time);
	}
}

if ( ! function_exists('days_interval'))
{
	/**
	 * Seconds Interval
	 *
	 * Returns interval of two datetime objects in days.
	 *
	 * @param	datetime	Start DateTime
	 * @param	datetime	End DateTime
	 * @return	int	difference in days
	 */
	function days_interval($datetime1,$datetime2)
	{
		if(is_string($datetime1)) {
			$datetime1 = new DateTime($datetime1);
		}

		if(is_string($datetime2)) {
			$datetime2 = new DateTime($datetime2);
		}

		$diff = date_diff($datetime1, $datetime2);
		return intval($diff->format("%a"));
	}
}

if ( ! function_exists('duration_string'))
{
	/**
	 * Duration string
	 *
	 * Returns interval of two datetime objects in long string.
	 *
	 * @param	datetime	Start DateTime
	 * @param	datetime	End DateTime
	 * @return	int	difference in seconds
	 */
	function duration_string($datetime1,$datetime2)
	{
		if (!($datetime1 instanceof DateTime)){
			$datetime1 = new DateTime($datetime1);
		}

		if (!($datetime2 instanceof DateTime)){
			$datetime2 = new DateTime($datetime2);
		}

		$diff = date_diff($datetime1, $datetime2);
        $string_duration = '';
        $days = intval($diff->format("%a"));
        $hours = intval($diff->format("%h"));
        $minutes = intval($diff->format("%i"));
        $seconds = intval($diff->format("%s"));

        if($days > 0){
            $string_duration .= "{$days} day".($days>1 ? 's' : '');
        }

        if($hours > 0){
            if(!empty($string_duration)){
                $string_duration .= ' ';
            }
            $string_duration .= "{$hours} hr".($hours>1 ? 's' : '');
        }

        if($minutes > 0){
            if(!empty($string_duration)){
                $string_duration .= ' ';
            }
            $string_duration .= "{$minutes} min".($minutes>1 ? 's' : '');
        }

        if($seconds > 0){
            if(!empty($string_duration)){
                $string_duration .= ' ';
            }
            $string_duration .= "{$seconds} sec".($seconds>1 ? 's' : '');
        }

		return $string_duration;
	}
}

if ( ! function_exists('duration_string_to_seconds'))
{
	/**
	 * Duration string
	 *
	 * Returns interval of two datetime objects in long string.
	 *
	 * @param	datetime	Start DateTime
	 * @param	datetime	End DateTime
	 * @return	int	difference in seconds
	 */
	function duration_string_to_seconds($duration)
	{
		if (empty($duration)){
			return 0;
		}

		$arr = explode(':', $duration);
	    if (count($arr) === 3) {
	        return ($arr[0] * 3600) + ($arr[1] * 60) + $arr[2];
	    }
	    return ($arr[0] * 60) + $arr[1];

	}
}

if ( ! function_exists('is_dmy_string'))
{
	/**
	 * is_dmy_string
	 *
	 * Check whether spullied string is a valid DMY string
	 *
	 * @param	string	date string
	 * @return	bool the string in dmy formatt
	 */
	function is_dmy_string($date_string) {
		//valid formats are dd-mm-YYYY, dd/mm/YYYY, dd.mm.YYYY
		return (preg_match('/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]|(?:Jan|Mar|May|Jul|Aug|Oct|Dec)))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2]|(?:Jan|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec))\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)(?:0?2|(?:Feb))\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9]|(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep))|(?:1[0-2]|(?:Oct|Nov|Dec)))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/', $date_string));
	}
}

if ( ! function_exists('get_date_string_format'))
{
	/**
	 * get_date_string_format
	 *
	 * Return the format of supplied date string
	 *
	 * @param	string	date string
	 * @return	string	the format of the date string
	 */
	function get_date_string_format($date_string) {
		if (preg_match("/^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}$/", $date_string)) {
            return 'd-m-Y';
        } elseif (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/", $date_string)) {
            return 'd/m/Y';
        } elseif (preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $date_string)) {
            return 'Y-m-d';
        }
        return '';
	}
}

if ( ! function_exists('change_date_string_format'))
{
	/**
	 * change_date_string_format
	 *
	 * Change a date string from one format to another format
	 *
	 * @param	string	starting format
	 * @param	string	end format
	 * @param	string	date string
	 * @return	string the modified date string
	 */
	function change_date_string_format($from, $to, $date_string) {
		if(get_date_string_format($date_string) === $to) {
			return $date_string;
		}

		if($from ==='d-m-Y' || $from ==='d/m/Y') {
			if(!is_dmy_string($date_string)) {
				$date_string = '';
				$from = '';
			}
		}

		$dateObject = date_create_from_format($from, $date_string);
		return $dateObject->format($to);
	}
}

if ( ! function_exists('validateDate'))
{
	/**
	 * validateDate
	 *
	 * Validate a date string based on specified format
	 *
	 * @param	string	date string
	 * @param	string	date format
	 * @return	boolean the date string is valid
	 */
	function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && ($d->format($format) == $date);
    }
}

