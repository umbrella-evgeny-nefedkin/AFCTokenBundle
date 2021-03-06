<?php
/**
 * Created by PhpStorm.
 * User: omni
 * Date: 18.07.2018
 * Time: 21:50
 */

namespace Umbrella\AFCTokenBundle\Entity;


use Umbrella\AFCTokenBundle\RefreshTokenInterface;
use Umbrella\AFCTokenBundle\TokenInterface;

class RefreshToken implements RefreshTokenInterface
{

	/**
	 * @var string
	 */
	private $refreshToken;

	/**
	 * RefreshToken constructor.
	 *
	 * @param string $refreshToken
	 */
	public function __construct(string $refreshToken){
		$this->refreshToken = $refreshToken;
	}

	/**
	 * @param \Umbrella\AFCTokenBundle\TokenInterface $Token
	 * @return string
	 */
	public function generateHash(TokenInterface $Token): string {
		$s1 = $this->refreshToken;
		$s2 = sha1($Token->getSalt() . $Token->getResource() . /*$Token->getAt() .*/ $Token->getTtl());

		$hash = sha1( $s1 . $s2);

//		var_dump('<<< Token >>>');
//		var_dump("salt: {$Token->getSalt()};\n ref-str: $this->refreshToken;\n");
//		var_dump("s1: $s1;\n s2: $s2;\n sum: $hash");
//		var_dump('<<< /Token >>>');

		return $hash;;
	}

	/**
	 * @return string
	 */
	public function getSource(): string {
		return $this->refreshToken;
	}
}