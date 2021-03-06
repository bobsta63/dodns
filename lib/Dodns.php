<?php namespace Ballen\Dodns;

use Ballen\Dodns\Entities\Domain;
use Ballen\Dodns\Entities\Record;
use Ballen\Dodns\Handlers\ApiRequest;
use Ballen\Dodns\Support\DomainBuilder;
use Ballen\Dodns\Support\RecordBuilder;

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
class Dodns
{

    /**
     * HTTP Method Constants
     */
    const GET = "GET";
    const POST = "POST";
    const PUT = "PUT";
    const DELETE = "DELETE";

    /**
     * The API request class.
     * @var Ballen\Dodns\Handlers\ApiRequest
     */
    private $api_handler;

    public function __construct(CredentialManager $credentials)
    {
        $this->api_handler = new ApiRequest($credentials);
    }

    /**
     * Return a colleciton of all domains that are configured in DigitalOcean DNS.
     * @return \Ballen\Collection\Collection
     */
    public function domains()
    {
        return $this->api_handler->request('domains', self::GET)->toCollection('domains', Domain::class);
    }

    /**
     * Return a specific domain object configured in DigitalOcean DNS.
     * @param Domain $domain The domain oject to return from the aPI.
     * @return \Ballen\Dodns\Entities\Domain
     */
    public function domain(Domain $domain)
    {
        return $this->api_handler->request('domains/' . $domain->id(), self::GET)->toEntity('domain', Domain::class);
    }

    /**
     * Creates a new domain record.
     * @param DomainBuilder $domain The domain configuration to create.
     * @return \Ballen\Dodns\Entities\Domain
     */
    public function createDomain(DomainBuilder $domain)
    {
        return $this->api_handler->request('domains', self::POST, $domain->requestBody())->toEntity('domain', Domain::class);
    }

    /**
     * Delete an entire domain including all records.
     * @param Domain $domain The domain of which the record belongs to.
     * @return boolean
     * @throws Exceptions\ApiActionException
     */
    public function deleteDomain(Domain $domain)
    {
        if ($this->api_handler->request('domains/' . $domain->id(), self::DELETE)->guzzleInstance()->getStatusCode() != 204) {
            throw new Exceptions\ApiActionException('The domain could not be deleted!');
        }
        return true;
    }

    /**
     * Return a colleciton of all domain records for a given domain.
     * @param Domain $domain The domain of which to get all records for.
     * @return \Ballen\Collection\Collection
     */
    public function records(Domain $domain)
    {
        return $this->api_handler->request('domains/' . $domain->id() . '/records', self::GET)->toCollection('domain_records', Record::class);
    }

    /**
     * Return a specific record object from a specific domain.
     * @param Domain $domain The domain of which the record belongs to.
     * @param int $record_id The record ID of which to return.
     * @return  \Ballen\Dodns\Entities\Record
     */
    public function record(Domain $domain, $record_id)
    {
        return $this->api_handler->request('domains/' . $domain->id() . '/records/' . $record_id, self::GET)->toEntity('domain_record', Record::class);
    }

    /**
     * Create a new record for a specific domain.
     * @param Domain $domain The domain of which the record will belong to.
     * @param RecordBuilder $record The record object of which to create the domain recrod with.
     * @return  \Ballen\Dodns\Entities\Record
     */
    public function createRecord(Domain $domain, RecordBuilder $record)
    {
        return $this->api_handler->request('domains/' . $domain->id() . '/records', self::POST, $record->requestBody())->toEntity('domain_record', Record::class);
    }

    /**
     * Update an existing record
     * @param Domain $domain The domain of which the record will belong to.
     * @param RecordBuilder $record The record object of which to create the domain recrod with.
     * @return  \Ballen\Dodns\Entities\Record
     */
    public function updateRecord(Domain $domain, Record $record)
    {
        return $this->api_handler->request('domains/' . $domain->id() . '/records/' . (string) $record->id(), self::PUT, $record->toJson())->toEntity('domain_record', Record::class);
    }

    /**
     * Delete a specific record for a given domain.
     * @param Domain $domain The domain of which the record belongs to.
     * @param int $record_id The record ID of which to delete
     * @return boolean
     * @throws Exceptions\ApiActionException
     */
    public function deleteRecord(Domain $domain, $record_id)
    {
        if ($this->api_handler->request('domains/' . $domain->id() . '/records/' . $record_id, self::DELETE)->guzzleInstance()->getStatusCode() != 204) {
            throw new Exceptions\ApiActionException('The domain record could not be deleted!');
        }
        return true;
    }
}
