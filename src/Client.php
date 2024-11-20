<?php

namespace Onetoweb\ShopifyOrder;

use Onetoweb\ShopifyOrder\Endpoint\Endpoints;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleCLient;
use Onetoweb\ShopifyOrder\Exception\JsonException;

/**
 * Shopify Order Api Client.
 */
#[\AllowDynamicProperties]
class Client
{
    /**
     * @var string
     */
    public const API_VERSION = '2024-10';
    
    /**
     * @var string
     */
    private $shopUrl;
    
    /**
     * @var string
     */
    private $accessToken;
    
    /**
     * @var int
     */
    private $requestedQueryCost;
    
    /**
     * @var int
     */
    private $actualQueryCost;
    
    /**
     * @var int
     */
    private $throttleMaximumAvailable;
    
    /**
     * @var int
     */
    private $throttleCurrentlyAvailable;
    
    /**
     * @var int
     */
    private $throttleRestoreRate;
    
    /**
     * @param string $shopUrl
     * @param string $accessToken
     * @param string $apiVersion = self::API_VERSION
     */
    public function __construct(string $shopUrl, string $accessToken, string $apiVersion = self::API_VERSION)
    {
        $this->shopUrl = $shopUrl;
        $this->accessToken = $accessToken;
        $this->apiVersion = $apiVersion;
        
        // load endpoints
        $this->loadEndpoints();
    }
    
    /**
     * @return void
     */
    private function loadEndpoints(): void
    {
        foreach (Endpoints::list() as $name => $class) {
            $this->{$name} = new $class($this);
        }
    }
    
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return rtrim($this->shopUrl, '/') . "/admin/api/{$this->apiVersion}/graphql.json";
    }
    
    /**
     * @return int|null
     */
    public function getRequestedQueryCost(): ?int
    {
        return $this->requestedQueryCost;
    }
    
    /**
     * @return int|null
     */
    public function getActualQueryCost(): ?int
    {
        return $this->actualQueryCost;
    }
    
    /**
     * @return int|null
     */
    public function getThrottleMaximumAvailable(): ?int
    {
        return $this->throttleMaximumAvailable;
    }
    
    /**
     * @return int|null
     */
    public function getThrottleCurrentlyAvailable(): ?int
    {
        return $this->throttleCurrentlyAvailable;
    }
    
    /**
     * @return int|null
     */
    public function getThrottleRestoreRate(): ?int
    {
        return $this->throttleRestoreRate;
    }
    
    /**
     * @param array $json
     * 
     * @return void
     */
    public function extractExtensionData(?array $json): void
    {
        $this->requestedQueryCost = $json['extensions']['cost']['requestedQueryCost'] ?? null;
        $this->actualQueryCost = $json['extensions']['cost']['actualQueryCost'] ?? null;
        $this->throttleMaximumAvailable = $json['extensions']['cost']['throttleStatus']['maximumAvailable'] ?? null;
        $this->throttleCurrentlyAvailable = $json['extensions']['cost']['throttleStatus']['currentlyAvailable'] ?? null;
        $this->throttleRestoreRate = $json['extensions']['cost']['throttleStatus']['restoreRate'] ?? null;
    }
    
    /**
     * Clears line endings and duplicate spaces
     * 
     * @param string $graph
     * 
     * @return string
     */
    public function cleanGraph(string $graph): string
    {
        return trim(preg_replace('/\s+/', ' ', str_replace(PHP_EOL, ' ', $graph)));
    }
    
    /**
     * @param ?array $json
     * 
     * @throws JsonException if the json contains errors
     * 
     * @return void
     */
    private function checkErrors(?array $json): void
    {
        if (isset($json['errors'])) {
            
            if (is_string($json['errors'])) {
                throw new JsonException($json['errors']);
            }
            
            $exception = null;
            $previous = null;
            foreach ($json['errors'] as $error) {
                
                $previous = $exception = (new JsonException($error['message'], 0, $previous))->setError($error);
            }
            
            if ($exception !== null) {
                throw $exception;
            }
        }
    }
    
    /**
     * @param string $graph
     * @param array $variables = []
     * 
     * @return array
     */
    public function request(string $graph, array $variables = []): array
    {
        // clean graph
        $graph = $this->cleanGraph($graph);
        
        // build json
        $json = [
            'query' => $graph
        ];
        
        // add variables
        if (count($variables) > 0) {
            $json['variables'] = $variables;
        }
        
        // build options
        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'x-shopify-access-token' => $this->accessToken
            ],
            RequestOptions::JSON => $json
        ];
        
        // make request
        $response = (new GuzzleCLient())->post($this->getUrl(), $options);
        
        // decode json
        $json = json_decode($response->getBody()->getContents(), true);
        
        // extract extension data
        $this->extractExtensionData($json);
        
        if (isset($json['data'])) {
            return $json['data'];
        }
        
        // check for errors in json data
        $this->checkErrors($json);
        
        return $json;
    }
}
