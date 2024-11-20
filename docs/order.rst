.. _top:
.. title:: Order

`Back to index <index.rst>`_

=====
Order
=====

.. contents::
    :local:


List order id's
```````````````

List order id's using the generator syntax

.. code-block:: php
    
    foreach ($client->order->listIds() as $orderId) {
        $orderId;
    }


Get order by id
```````````````

.. code-block:: php
    
    $orderId = ;
    $order = $client->order->get($orderId);


List orders
```````````

List orders using the generator syntax

.. code-block:: php
    
    foreach ($client->order->list() as $order) {
        $order;
    }


List orders created after
`````````````````````````

List orders using the generator syntax

.. code-block:: php
    
    $after = (new DateTime())->modify('-1 day');
    foreach ($client->order->listCreatedAfter($after) as $order) {
        $order;
    }


`Back to top <#top>`_