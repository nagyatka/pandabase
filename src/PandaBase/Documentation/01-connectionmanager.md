#PandaBase - How to use ConnectionManager

## Get instance

In PandaBase there is a unique manager object which you can use at every part of your. You can reach it via call getInstance method.

```bash
<?php

use PandaBase\Connection\ConnectionManager;

// Get the manager instance
$connectionManager = ConnectionManager::getInstance();


```