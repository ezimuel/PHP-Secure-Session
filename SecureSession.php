<?php
/**
 * ------------------------------------------------
 * Encrypt PHP session data using files
 * ------------------------------------------------
 * The encryption is built using mcrypt extension 
 * and the randomness is managed by openssl
 * 
 * @author Enrico Zimuel (enrico@zimuel.it)
 * @copyright GNU General Public License
 */
class SecureSession {
	const CIPHER= MCRYPT_RIJNDAEL_256;
	const CIPHER_MODE= MCRYPT_MODE_CBC;
	/**
	 * Key for encryption/decryption
	 * 
	 * @var string
	 */
	private static $_key;
	/**
	 * Path of the session file
	 *
	 * @var string
	 */
	private static $_path;
	/**
	 * Session name (optional)
	 * 
	 * @var string
	 */
	private static $_name;
	/**
	 * Size of the IV vector for encryption
	 * 
	 * @var integer
	 */
	private static $_ivSize;
	/**
	 * Cookie variable name of the key
	 * 
	 * @var string
	 */
	private static $_keyName;
	/**
	 * Generate a random key
	 * fallback to mt_rand if PHP < 5.3 or no openssl available
	 * 
	 * @param integer $length
	 * @return string
	 */
	private static function _randomKey($length=32) {
		if(function_exists('openssl_random_pseudo_bytes')) {
    		$rnd = openssl_random_pseudo_bytes($length, $strong);
        	if($strong === TRUE) 
        		return $rnd;
		}
		for ($i=0;$i<$length;$i++) {
			$sha= sha1(mt_rand());
			$char= mt_rand(0,30);
			$rnd.= chr(hexdec($sha[$char].$sha[$char+1]));
		}	
		return $rnd;
	}
    /**
     * Open the session
     * 
     * @param string $save_path
     * @param string $session_name
     * @return bool
     */
    public static function open($save_path, $session_name) {
		self::$_path= $save_path.'/';	
		self::$_name= $session_name;
		self::$_keyName= "KEY_$session_name";
		self::$_ivSize= mcrypt_get_iv_size(self::CIPHER, self::CIPHER_MODE);
		
		if (empty($_COOKIE[self::$_keyName])) {
			$keyLength= mcrypt_get_key_size(self::CIPHER, self::CIPHER_MODE);
			self::$_key= self::_randomKey($keyLength);
			$cookie_param = session_get_cookie_params();
            setcookie(
                self::$_keyName,
                base64_encode(self::$_key),
                $cookie_param['lifetime'],
                $cookie_param['path'],
                $cookie_param['domain'],
                   $cookie_param['secure'],
                   $cookie_param['httponly']
            );
		} else {
			self::$_key= base64_decode($_COOKIE[self::$_keyName]);
		} 
		return true;
    }
    /**
     * Close the session
     * 
     * @return bool
     */
    public static function close() {
        return true;
    }
    /**
     * Read and decrypt the session
     * 
     * @param integer $id
     * @return string 
     */
    public static function read($id) {
        $sess_file = self::$_path.self::$_name."_$id";
  		$data= @file_get_contents($sess_file);
  		if (empty($data)) {
  			return false;
  		}
  		$iv= substr($data,0,self::$_ivSize);
  		$encrypted= substr($data,self::$_ivSize);
  		$decrypt = mcrypt_decrypt(
                self::CIPHER,
                self::$_key,
                $encrypted,
                self::CIPHER_MODE,
                $iv
        );
        return rtrim($decrypt, "\0"); 
    }
    /**
     * Encrypt and write the session
     * 
     * @param integer $id
     * @param string $data
     * @return bool
     */
    public static function write($id, $data) {
        $sess_file = self::$_path.self::$_name."_$id";
		$iv= mcrypt_create_iv(self::$_ivSize, MCRYPT_RAND);
	  	if ($fp = @fopen($sess_file, "w")) {
	  		$encrypted= mcrypt_encrypt(
                self::CIPHER,
                self::$_key,
                $data,
                self::CIPHER_MODE,
                $iv
            );
	    	$return = fwrite($fp, $iv.$encrypted);
	    	fclose($fp);
	    	return $return;
	  	} else {
	    	return false;
	  	}
    }
    /**
     * Destoroy the session
     * 
     * @param int $id
     * @return bool
     */
    public static function destroy($id) {
        $sess_file = self::$_path.self::$_name."_$id";
        setcookie (self::$_keyName, '', time() - 3600);
		return(@unlink($sess_file));
    }
    /**
     * Garbage Collector
     * 
     * @param int $max 
     * @return bool
     */
    public static function gc($max) {
    	foreach (glob(self::$_path.self::$_name.'_*') as $filename) {
    		if (filemtime($filename) + $max < time()) {
      			@unlink($filename);
    		}
  		}
  		return true;
    }
}
// Set the custom PHP session handler
ini_set('session.save_handler', 'user');
session_set_save_handler(array('SecureSession', 'open'),
                         array('SecureSession', 'close'),
                         array('SecureSession', 'read'),
                         array('SecureSession', 'write'),
                         array('SecureSession', 'destroy'),
                         array('SecureSession', 'gc')
                        );