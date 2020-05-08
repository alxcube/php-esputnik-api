<?php

namespace Esputnik;

use Esputnik\Api\AddressBooks;
use Esputnik\Api\Balance;
use Esputnik\Api\Callouts;
use Esputnik\Api\Contact;
use Esputnik\Api\Contacts;
use Esputnik\Api\Group;
use Esputnik\Api\Groups;
use Esputnik\Api\Message;
use Esputnik\Exception\InvalidArgumentException;
use Esputnik\HttpClient\HttpClient;

class Client
{
    protected $options = [
        'base_url' => 'https://esputnik.com/api/',
        'api_version' => 'v1',
        'user_agent' => 'php-esputnik-api (http://)',
        'cache_dir' => null,
        'content_type' => 'application/json; charset=UTF-8',
    ];

    private $httpClient;

    public function __construct($httpClient = null)
    {
        $this->httpClient = $httpClient;
    }

    public function api($name)
    {
        switch ($name) {
            case 'balance':
                $api = new Balance($this);
                break;
            case 'contacts':
                $api = new Contacts($this);
                break;
            case 'contact':
                $api = new Contact($this);
                break;
            case 'callouts':
            case 'callout':
                $api = new Callouts($this);
                break;
            case 'addressbook':
            case 'addressbooks':
                $api = new AddressBooks($this);
                break;
            case 'group':
                $api = new Group($this);
                break;
            case 'groups':
                $api = new Groups($this);
                break;
            case 'message':
                $api = new Message($this);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    public function authenticate($login, $password)
    {
        $this->options['login'] = $login;
        $this->options['password'] = $password;
    }

    public function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient($this->options);
        }

        return $this->httpClient;
    }
}
