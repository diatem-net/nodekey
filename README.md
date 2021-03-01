Node Key
=========
The Node Key module generate a unique key for every node.  
This key can be used to load an node without relying on its id.


REQUIREMENTS
-------------
Drupal 9.x.


INSTALLATION
-------------
Install this module as usual. Please see
https://www.drupal.org/docs/extending-drupal/installing-modules


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

	// theme suggestions
	page--'my_node_key'
	node--'my_node_key'

You can find the list of all created keys at `/admin/config/nodekey/list`.  
This module currently does not allow edition, but feel free to edit the `node_nodekey` table in your database (just make sure taht there are no duplicates).