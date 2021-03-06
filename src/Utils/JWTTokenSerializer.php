<?php
/**
 * Created by PhpStorm.
 * User: omni
 * Date: 20.06.2018
 * Time: 15:48
 */

namespace Umbrella\AFCTokenBundle\Utils;


use Umbrella\AFCTokenBundle\Entity\RefreshTokenHash;
use Umbrella\AFCTokenBundle\Entity\Token;
use Umbrella\AFCTokenBundle\TokenInterface;
use Firebase\JWT\JWT;
use Umbrella\JCLibPack\JCEncrypter;

/**
 * Class JWTTokenSerializer
 *
 * @package Umbrella\AFCTokenBundle\Service
 */
class JWTTokenSerializer
{
	private const TYPE = 'HS512';

	/**
	 * @param \Umbrella\AFCTokenBundle\TokenInterface $Token
	 * @param string|NULL                             $privateKey
	 * @return array
	 */
	static public function ArraySerialize(TokenInterface $Token, string $privateKey = null) :array{

		return [
			'exp'       => $Token->getAt()->modify('+'. $Token->getTtl() . ' seconds')->getTimestamp(),
			'salt'      => $Token->getSalt(),
			'shared'    => $Token->getSharedData(),
			'crypt'     => JCEncrypter::encrypt_RSA(json_encode($Token->getEncryptedData()), $privateKey),
			'refresh'   => $Token->getRefreshHash(),
		];
	}

	/**
	 * !Key must be in .pem format
	 * @param \Umbrella\AFCTokenBundle\TokenInterface $Token
	 * @param string                             $secret
	 * @param string                             $privateKey
	 * @return string
	 */
	static public function serialize(TokenInterface $Token, string $secret, string $privateKey): string {

		$jwt = JWT::encode(static::ArraySerialize($Token, $privateKey), $secret, static::TYPE);

		return $jwt;
	}

	/**
	 * !Key must be in .pem format
	 *
	 * @param string $tokenString
	 * @param string $secret
	 * @param string $publicKey
	 * @return \Umbrella\AFCTokenBundle\TokenInterface
	 * @throws \Exception
	 */
	static public function deserialize(string $tokenString, string $secret, string $publicKey): TokenInterface {

		$tokenData = JWT::decode($tokenString, $secret, [static::TYPE]);

		$decryptedData = json_decode(JCEncrypter::decrypt_RSA($tokenData->crypt, $publicKey));

		$Token = new Token(
			$decryptedData->resource,
			$decryptedData->ttl,
			new RefreshTokenHash($tokenData->refresh)
		);

//		$Token->setAt($decryptedData->at);
		$Token->setSalt($tokenData->salt);

		return $Token;
	}
}