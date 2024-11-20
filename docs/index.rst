.. title:: Index

===========
Basic Usage
===========

Setup

.. code-block:: php
    
    require 'vendor/autoload.php';
    
    use Onetoweb\ShopifyOrder\Client;
    
    // params
    $shopUrl = 'https://example.myshopify.com/';
    $accessToken = '{access_token}';
    
    // (optional) params
    $apiVersion = '2024-10'; // if api version is ommited it defaults to 2024-10
    
    // setup client
    $client = new Client($apiKey, $testModus, $apiVersion);


=================
Endpoint Examples
=================

* `Order <order.rst>`_