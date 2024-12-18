Generated from CyberSource WSDL v1.224 via Wsdl2PhpGenerator library. https://github.com/wsdl2phpgenerator/wsdl2phpgenerator

generator.php
```php
<?php
require __DIR__ . '/vendor/autoload.php';

$generator = new \Wsdl2PhpGenerator\Generator();
$generator->generate(
	new \Wsdl2PhpGenerator\Config(array(
        'inputFile' => 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.224.wsdl',
        'outputDir' => 'output',
        'namespaceName' => 'ParadoxLabs\CyberSource\Gateway\Api',
    ))
);
``` 

```sh
composer require wsdl2phpgenerator/wsdl2phpgenerator
php7.4 generator.php
```

Clean up output with phpcsfixer or similar.

Resulting classes will have `amount` types that must be changed to `float`.
