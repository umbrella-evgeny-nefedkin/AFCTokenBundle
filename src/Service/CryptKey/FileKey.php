<?php
/**
 * Created by PhpStorm.
 * User: omni
 * Date: 30.08.2018
 * Time: 20:19
 */

namespace Umbrella\AFCTokenBundle\Service\CryptKey;

use Umbrella\AFCTokenBundle\Service\CryptKeyInterface;

/**
 * Class FileKey
 *
 * @package Service\CryptKey
 */
class FileKey implements CryptKeyInterface
{
	/**
	 * @var string
	 */
	private $keyPath;

	/**
	 * FileKey constructor.
	 *
	 * @param string $keyPath
	 * @throws \Exception
	 */
	public function __construct(string $keyPath){

		if (!file_exists($keyPath))
			throw new \Exception('Invalid key path: '. $keyPath);

		$this->keyPath = realpath($keyPath);
	}

	/**
	 * @return string
	 */
	public function __toString(): string {
		return file_get_contents($this->keyPath);
	}
}