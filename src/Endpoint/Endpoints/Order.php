<?php

namespace Onetoweb\ShopifyOrder\Endpoint\Endpoints;

use Onetoweb\ShopifyOrder\Endpoint\AbstractEndpoint;
use Onetoweb\ShopifyOrder\Graph\Order as OrderGraph;
use Generator;
use DateTime;

/**
 * Order Endpoint.
 */
class Order extends AbstractEndpoint
{
    use OrderGraph;
    
    /**
     * @param string $orderId
     *
     * @return array|null
     */
    public function get(string $orderId): array
    {
        $graph = 'query { order(id: "'.$orderId.'") '.OrderGraph::fullOrder().' }';
        
        return $this->client->request($graph);
    }
    
    /**
     * Yields list of order id's using the generator syntax
     * 
     * @return array|null
     */
    public function listIds(): Generator
    {
        $cursor = '';
        do {
            
            $results =  $this->client->request("
                query {
                    orders(first: 100 $cursor) {
                        edges {
                            node {
                                id
                            }
                        }
                        pageInfo {
                            endCursor
                            hasNextPage
                        }
                    }
                }
            ");
            
            if (isset($results['orders']['edges'])) {
                
                foreach ($results['orders']['edges'] as $edge) {
                    yield ['order' => $edge['node']];
                }
            }
            
            if (isset($results['orders']['pageInfo']['endCursor'])) {
                $cursor = ', after: "' . $results['orders']['pageInfo']['endCursor'] . '"';
            }
            
        } while(isset($results['orders']['pageInfo']['hasNextPage']) and $results['orders']['pageInfo']['hasNextPage']);
    }
    
    /**
     * @return array|null
     */
    public function list(): Generator
    {
        $cursor = '';
        do {
            
            $results =  $this->client->request('
                query {
                    orders(first: 100 '.$cursor.') {
                        edges {
                            node '.OrderGraph::fullOrder().'
                        }
                        pageInfo {
                            endCursor
                            hasNextPage
                        }
                    }
                }
            ');
            
            if (isset($results['orders']['edges'])) {
                
                foreach ($results['orders']['edges'] as $edge) {
                    yield ['order' => $edge['node']];
                }
            }
            
            if (isset($results['orders']['pageInfo']['endCursor'])) {
                $cursor = ', after: "' . $results['orders']['pageInfo']['endCursor'] . '"';
            }
            
        } while(isset($results['orders']['pageInfo']['hasNextPage']) and $results['orders']['pageInfo']['hasNextPage']);
    }
    
    /**
     * @param DateTime $after
     * 
     * @return array|null
     */
    public function listCreatedAfter(DateTime $after): Generator
    {
        $cursor = '';
        do {
            
            $results =  $this->client->request('
                query {
                    orders(first: 100 '.$cursor.', query: "created_at:>'.$after->format('Y-m-d').'", sortKey: CREATED_AT, reverse: false) {
                        edges {
                            node '.OrderGraph::fullOrder().'
                        }
                        pageInfo {
                            endCursor
                            hasNextPage
                        }
                    }
                }
            ');
            
            if (isset($results['orders']['edges'])) {
                
                foreach ($results['orders']['edges'] as $edge) {
                    yield ['order' => $edge['node']];
                }
            }
            
            if (isset($results['orders']['pageInfo']['endCursor'])) {
                $cursor = ', after: "' . $results['orders']['pageInfo']['endCursor'] . '"';
            }
            
        } while(isset($results['orders']['pageInfo']['hasNextPage']) and $results['orders']['pageInfo']['hasNextPage']);
    }
}
