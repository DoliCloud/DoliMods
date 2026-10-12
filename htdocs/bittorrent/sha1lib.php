<?php
/*
 * A PHP implementation of the Secure Hash Algorithm, SHA-1, as defined
 * in FIPS PUB 180-1
 * Adjusted from the Javascript implementation by Joror (daan@parse.nl).
 *
 * Javascript Version 2.1 Copyright Paul Johnston 2000 - 2002.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for details.
 */

// Method names are the ones of the original library (Paul Johnston), kept for compatibility
// phpcs:disable PEAR.NamingConventions.ValidFunctionName.ScopeNotCamelCaps
/**
 * Class to compute SHA-1 hashes in pure PHP
 */
class Sha1Lib
{
	/*
	 * Configurable variables. You may need to tweak these to be compatible with
	 * the server-side, but the defaults work in most cases.
	 */
	public $hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
	public $b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
	public $chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

	/**
	 * Return the SHA-1 of a string, hex encoded
	 * These are the functions you'll usually want to call
	 *
	 * @param	string	$s	String to hash
	 * @return	string	Hex encoded hash
	 */
	public function hex_sha1($s)
	{
		return $this->binb2hex($this->core_sha1($this->str2binb($s), strlen($s) * $this->chrsz));}
	/**
	 * Return the SHA-1 of a string, base-64 encoded
	 *
	 * @param	string	$s	String to hash
	 * @return	string	Base-64 encoded hash
	 */
	public function b64_sha1($s)
	{
		return $this->binb2b64($this->core_sha1($this->str2binb($s), strlen($s) * $this->chrsz));}
	/**
	 * Return the SHA-1 of a string, as a raw binary string
	 *
	 * @param	string	$s	String to hash
	 * @return	string	Raw binary hash
	 */
	public function str_sha1($s)
	{
		return $this->binb2str($this->core_sha1($this->str2binb($s), strlen($s) * $this->chrsz));}
	/**
	 * Return the HMAC-SHA1 of some data, hex encoded
	 *
	 * @param	string	$key	Key
	 * @param	string	$data	Data to sign
	 * @return	string	Hex encoded HMAC
	 */
	public function hex_hmac_sha1($key, $data)
	{
		return $this->binb2hex($this->core_hmac_sha1($key, $data));}
	/**
	 * Return the HMAC-SHA1 of some data, base-64 encoded
	 *
	 * @param	string	$key	Key
	 * @param	string	$data	Data to sign
	 * @return	string	Base-64 encoded HMAC
	 */
	public function b64_hmac_sha1($key, $data)
	{
		return $this->binb2b64($this->core_hmac_sha1($key, $data));}
	/**
	 * Return the HMAC-SHA1 of some data, raw binary
	 *
	 * @param	string	$key	Key
	 * @param	string	$data	Data to sign
	 * @return	string	Raw binary HMAC
	 */
	public function str_hmac_sha1($key, $data)
	{
		return $this->binb2str($this->core_hmac_sha1($key, $data));}

	/**
	 * Perform a simple self-test to see if the VM is working
	 *
	 * @return	bool	True if the hash of 'abc' is right
	 */
	public function sha1_vm_test()
	{
		return $this->hex_sha1("abc") == "a9993e364706816aba3e25717850c26c9cd0d89d";
	}

	/**
	 * Calculate the SHA-1 of an array of big-endian words, and a bit length
	 *
	 * @param	int[]	$x	Array of big-endian words
	 * @param	int	$len	Length in bits
	 * @return	int[]	Hash as an array of 5 words
	 */
	public function core_sha1($x, $len)
	{
		/* append padding */
		$x[$len >> 5] |= 0x80 << (24 - $len % 32);
		$x[(($len + 64 >> 9) << 4) + 15] = $len;

		$w = Array();
		$a =  1732584193;
		$b = -271733879;
		$c = -1732584194;
		$d =  271733878;
		$e = -1009589776;

		$nbx = count($x);
		for ($i = 0; $i < $nbx; $i += 16) {
			$olda = $a;
			$oldb = $b;
			$oldc = $c;
			$oldd = $d;
			$olde = $e;

			for ($j = 0; $j < 80; $j++) {
				if ($j < 16)
					$w[$j] = $x[$i + $j];
				else $w[$j] = $this->rol($w[$j-3] ^ $w[$j-8] ^ $w[$j-14] ^ $w[$j-16], 1);

				$t = $this->safe_add(	$this->safe_add($this->rol($a, 5), $this->sha1_ft($j, $b, $c, $d)),
										$this->safe_add($this->safe_add($e, $w[$j]), $this->sha1_kt($j)));
				$e = $d;
				$d = $c;
				$c = $this->rol($b, 30);
				$b = $a;
				$a = $t;
			}

			$a = $this->safe_add($a, $olda);
			$b = $this->safe_add($b, $oldb);
			$c = $this->safe_add($c, $oldc);
			$d = $this->safe_add($d, $oldd);
			$e = $this->safe_add($e, $olde);
		}

		return Array($a, $b, $c, $d, $e);
	}

	/**
	 * Joror: PHP does not have the java(script) >>> operator, so this is a
	 * replacement function. Credits to Terium.
	 *
	 * @param	int	$a	Value to shift
	 * @param	int	$b	Number of bits
	 * @return	int	Shifted value
	 */
	public function zerofill_rightshift($a, $b)
	{
		$z = hexdec(80000000);
		if ($z & $a) {
			$a >>= 1;
			$a &= (~ $z);
			$a |= 0x40000000;
			$a >>= ($b-1);
		} else {
			$a >>= $b;
		}
		return $a;
	}

	/**
	 * Perform the appropriate triplet combination function for the current
	 * iteration
	 *
	 * @param	int	$t	Iteration
	 * @param	int	$b	Word b
	 * @param	int	$c	Word c
	 * @param	int	$d	Word d
	 * @return	int	Combined value
	 */
	public function sha1_ft($t, $b, $c, $d)
	{
		if ($t < 20) return ($b & $c) | ((~$b) & $d);
		if ($t < 40) return $b ^ $c ^ $d;
		if ($t < 60) return ($b & $c) | ($b & $d) | ($c & $d);
		return $b ^ $c ^ $d;
	}

	/**
	 * Determine the appropriate additive constant for the current iteration
	 * Silly php does not understand the inline-if operator well when nested,
	 * so that's why it's ()ed now.
	 *
	 * @param	int	$t	Iteration
	 * @return	int	Additive constant
	 */
	public function sha1_kt($t)
	{
		return ($t < 20) ?  1518500249 : (($t < 40) ?  1859775393 :
				(($t < 60) ? -1894007588 : -899497514));
	}

	/**
	 * Calculate the HMAC-SHA1 of a key and some data
	 *
	 * @param	string	$key	Key
	 * @param	string	$data	Data to sign
	 * @return	int[]	HMAC as an array of 5 words
	 */
	public function core_hmac_sha1($key, $data)
	{
		$bkey = $this->str2binb($key);
		if (count($bkey) > 16) $bkey = $this->core_sha1($bkey, strlen($key) * $this->chrsz);

		$ipad = Array();
		$opad = Array();

		for ($i = 0; $i < 16; $i++) {
			$ipad[$i] = $bkey[$i] ^ 0x36363636;
			$opad[$i] = $bkey[$i] ^ 0x5C5C5C5C;
		}

		$hash = $this->core_sha1(array_merge($ipad, $this->str2binb($data)), 512 + strlen($data) * $this->chrsz);
		return $this->core_sha1(array_merge($opad, $hash), 512 + 160);
	}

	/**
	 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
	 * to work around bugs in some JS interpreters.
	 *
	 * @param	int	$x	First integer
	 * @param	int	$y	Second integer
	 * @return	int	Sum
	 */
	public function safe_add($x, $y)
	{
		$lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
		$msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
		return ($msw << 16) | ($lsw & 0xFFFF);
	}

	/**
	 * Bitwise rotate a 32-bit number to the left.
	 *
	 * @param	int	$num	Number to rotate
	 * @param	int	$cnt	Number of bits
	 * @return	int	Rotated number
	 */
	public function rol($num, $cnt)
	{
		return ($num << $cnt) | $this->zerofill_rightshift($num, (32 - $cnt));
	}

	/**
	 * Convert an 8-bit or 16-bit string to an array of big-endian words
	 * In 8-bit function, characters >255 have their hi-byte silently ignored.
	 *
	 * @param	string	$str	String to convert
	 * @return	int[]	Array of big-endian words
	 */
	public function str2binb($str)
	{
		$bin = Array();
		$mask = (1 << $this->chrsz) - 1;
		$nbbits = strlen($str) * $this->chrsz;
		for ($i = 0; $i < $nbbits; $i += $this->chrsz)
			$bin[$i >> 5] |= (ord($str[$i / $this->chrsz]) & $mask) << (24 - $i%32);

		return $bin;
	}

	/**
	 * Convert an array of big-endian words to a string
	 *
	 * @param	int[]	$bin	Array of big-endian words
	 * @return	string	Raw binary string
	 */
	public function binb2str($bin)
	{
		$str = "";
		$mask = (1 << $this->chrsz) - 1;
		$nbbits = count($bin) * 32;
		for ($i = 0; $i < $nbbits; $i += $this->chrsz)
			$str .= chr($this->zerofill_rightshift($bin[$i>>5], 24 - $i%32) & $mask);
		return $str;
	}

	/**
	 * Convert an array of big-endian words to a hex string.
	 *
	 * @param	int[]	$binarray	Array of big-endian words
	 * @return	string	Hex string
	 */
	public function binb2hex($binarray)
	{
		$hex_tab = $this->hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
		$str = "";
		$nbbytes = count($binarray) * 4;
		for ($i = 0; $i < $nbbytes; $i++) {
			$str .= $hex_tab[($binarray[$i>>2] >> ((3 - $i%4)*8+4)) & 0xF] .
					$hex_tab[($binarray[$i>>2] >> ((3 - $i%4)*8  )) & 0xF];
		}

		return $str;
	}

	/**
	 * Convert an array of big-endian words to a base-64 string
	 *
	 * @param	int[]	$binarray	Array of big-endian words
	 * @return	string	Base-64 string
	 */
	public function binb2b64($binarray)
	{
		$tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$str = "";
		$nbbytes = count($binarray) * 4;
		for ($i = 0; $i < $nbbytes; $i += 3) {
			$triplet = 	((($binarray[$i   >> 2] >> 8 * (3 -  $i   %4)) & 0xFF) << 16)
						| ((($binarray[$i+1 >> 2] >> 8 * (3 - ($i+1)%4)) & 0xFF) << 8 )
						|  (($binarray[$i+2 >> 2] >> 8 * (3 - ($i+2)%4)) & 0xFF);
			for ($j = 0; $j < 4; $j++) {
				if ($i * 8 + $j * 6 > count($binarray) * 32) $str .= $this->b64pad;
				else $str .= $tab[($triplet >> 6*(3-$j)) & 0x3F];
			}
		}
		return $str;
	}
}
// phpcs:enable

if ( !function_exists('sha1') ) {
	/**
	 * Replacement of the PHP sha1() function
	 *
	 * @param	string	$string	String to hash
	 * @param	bool	$raw_output	True to return a raw binary hash
	 * @return	string	Hash
	 */
	function sha1($string, $raw_output = false)
	{
		$library = new Sha1Lib();

		return $raw_output ? $library->str_sha1($string) : $library->hex_sha1($string);
	}
}
