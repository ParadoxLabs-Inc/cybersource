Generated from CyberSource WSDL v1.161 via Wsdl2PhpGenerator library. https://github.com/wsdl2phpgenerator/wsdl2phpgenerator

```
<?php
require __DIR__ . '/vendor/autoload.php';

$generator = new \Wsdl2PhpGenerator\Generator();
$generator->generate(
	new \Wsdl2PhpGenerator\Config(array(
        'inputFile' => 'https://ics2ws.ic3.com/commerce/1.x/transactionProcessor/CyberSourceTransaction_1.161.wsdl',
        'outputDir' => 'output',
        'namespaceName' => 'ParadoxLabs\CyberSource\Gateway\Api',
    ))
);
``` 

Resulting classes will have `amount` types that must be changed to `float`.
