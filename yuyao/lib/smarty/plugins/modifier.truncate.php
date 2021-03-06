<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '',
                                  $break_words = false, $middle = false)
{
    /*
    if ($length == 0)
        return '';
    if($code == 'UTF-8'){
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
    }
    else{
        $pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
    }
    preg_match_all($pa, $string, $t_string);
    if(count($t_string[0]) > $length)
        return join('', array_slice($t_string[0], 0, $length)).$etc;
    return join('', array_slice($t_string[0], 0, $length));
    */
	$l = mb_strlen($string, 'gb2312');
	if ( $l <= $length )
		return $string;

	$tmp = '';
	
	for ($i = 0; $i < $length; $i++)
	{
		$t = mb_substr($string, $i, 1);
		if (strlen($t) != 1)
		{
			$length--;
			if ($i == $length)
			{
				break;
			}
		}
		$tmp .= $t;
	}
	return $tmp . $etc;	

}


/* vim: set expandtab: */

?>
