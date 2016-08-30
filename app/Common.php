<?php

class Common {

    /**
     *
     * @param string|array $dirty
     * @return string|array
     */
    static public function clean_input($dirty) {
        if (!is_array($dirty)) {
            $dirty = self::clear_input_one($dirty);
        } else if (is_array($dirty)) {
            foreach ($dirty as $k => $v) {
                $dirty[$k] = self::clean_input($v);
            }
        }

        return $dirty;
    }
	
	static public function clean_input_post($dirty)
	{
		if (!is_array($dirty)) {
			$dirty = self::clear_input_one($dirty);
		} else if (is_array($dirty)) {
			foreach ($dirty as $k => $v) {
				$dirty[$k] = self::clean_input($v);
			}
		}

		return $dirty;
	}

	static private function clear_input_one($dirty)
	{
		if (preg_match("/^-?([0-9])+([\.|,]([0-9])*)?$/", $dirty)) {
			$dirty = str_replace(",", ".", $dirty);
		}

		$clean = trim(strip_tags(htmlspecialchars(stripslashes($dirty), ENT_QUOTES, 'UTF-8', false), ''));

		return $clean;
	}

    static function cleanOutput($data) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = htmlspecialchars_decode(stripslashes($v), ENT_QUOTES);
            }
        } else {
            $data = htmlspecialchars_decode(stripslashes($data), ENT_QUOTES);
        }

        return $data;
    }
	
	static public function clean($dirty)
	{
		return self::clean_input($dirty);
	}

}