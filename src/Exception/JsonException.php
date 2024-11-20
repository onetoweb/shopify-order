<?php

namespace Onetoweb\ShopifyOrder\Exception;

use Exception;

/**
 * Json Exception.
 */
class JsonException extends Exception
{
    /**
     * @var array
     */
    private $error;
    
    /**
     * @param array $error
     */
    public function setError(array $error): self
    {
        $this->error = $error;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getError(): ?array
    {
        return $this->error;
    }
}