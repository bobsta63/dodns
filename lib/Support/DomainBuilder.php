<?php namespace Ballen\Dodns\Support;

/**
 * DODNS
 *
 * DODNS (DigitalOcean DNS) is a PHP library for managing DNS records hosted on
 * DigitalOcean.
 *
 * @author Bobby Allen <ballen@bobbyallen.me>
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/bobsta63/dodns
 * @link http://www.bobbyallen.me
 *
 */
class DomainBuilder extends Builder implements BuilderInterface
{

    const EXCEPTION_DOMAIN_FORMAT_INVALID = "Domain name format is invalid";
    const EXCEPTION_IP_FORMAT_INVALID = "The IP address format is invalid";

    /**
     * The object data.
     * @var array
     */
    private $object_data = [];

    /**
     * Create a new instance of a Domain entity.
     * @param string $name The domain name to create (eg. "mydomain.com")
     * @param string $ip_address The IP address to initially create the base A record with (eg. "192.24.122.32")
     * @return void
     */
    public function __construct($name, $ip_address = '127.0.0.1')
    {
        $this->object_data['name'] = $name;
        $this->object_data['ip_address'] = $ip_address;
    }

    /**
     * Sets or updates the domain name
     * @param string $domain The domain name (eg. "mydomain.com")
     * @return void
     */
    public function setDomainName($domain)
    {
        $this->object_data['name'] = $domain;
    }

    /**
     * Sets or updates the IP address for the domain.
     * @param string $ip_address The IP address to initially create the base A record with (eg. "192.24.122.32")
     * @return void
     */
    public function setIpAddress($ip_address)
    {
        $this->object_data['ip_address'] = $ip_address;
    }

    /**
     * Constructs and returns the API request body.
     * @return string
     */
    public function requestBody()
    {
        return json_encode($this->object_data);
    }

    private function validateDomainName($domain)
    {
        if (!preg_match('/([0-9a-z-]+\.)?[0-9a-z-]+\.[a-z]{2,7}/', $domain)) {
            throw new \Ballen\Dodns\Exceptions\DataFormatException(self::EXCEPTION_IP_FORMAT_INVALID);
        }
    }

    private function validateIpAddress($ip_address)
    {
        $octals = explode('.', $ip_address);

        if (count($octals) > 4) {
            throw new \Ballen\Dodns\Exceptions\DataFormatException('');
        }
        foreach ((int) $octals as $octal) {
            if (!($octal >= 0) || ($octal <= 255)) {
                throw new \Ballen\Dodns\Exceptions\DataFormatException(self::EXCEPTION_IP_FORMAT_INVALID);
            }
        }
    }
}
