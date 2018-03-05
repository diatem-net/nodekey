Node Key
=========
The Node Key module generate a unique key for every node.  
This key can be used to load an node without relying on its id.


REQUIREMENTS
-------------
Drupal 8.x.


INSTALLATION
-------------
Install this module as usual. Please see
http://drupal.org/documentation/install/modules-themes/modules-8


CONFIGURATION
--------------
Global module settings can be found at admin/config/nodekey.


USAGE
--------------

    // Get the url of a node
    $url = NodeKeyEntity::url('my_node_key');

    // Get a node entity
    $node = NodeKeyEntity::load('my_node_key');

    // Create a new key
    $nodekey = NodeKeyEntity::create($node);

You can find the list of all created keys at `/admin/config/nodekey/list`.  
This module currently does not allow edition, but feel free to edit the `node_nodekey` table in your database (just make sure taht there are no duplicates).