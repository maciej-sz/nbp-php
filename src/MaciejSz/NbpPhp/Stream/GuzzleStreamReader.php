<?php
namespace MaciejSz\NbpPhp\Stream;
 
use GuzzleHttp\Client;

class GuzzleStreamReader implements IStreamReader
{
    /**
     * @var Client
     */
    private $_Client = null;

    /**
     * @param Client $GuzzleClient
     */
    private function __construct(Client $GuzzleClient)
    {
        $this->_Client = $GuzzleClient;
    }

    /**
     * @param Client $GuzzleClient
     * @return GuzzleStreamReader
     */
    public static function factory(Client $GuzzleClient = null)
    {
        if ( null === $GuzzleClient ) {
            $GuzzleClient = new Client();
        }
        return new self($GuzzleClient);
    }

    /**
     * @param string $url
     * @return string
     */
    public function getContents($url)
    {
        return $this->_Client->get($url);
    }
}
 