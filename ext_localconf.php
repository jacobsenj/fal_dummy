<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['registeredDrivers']['LocalDummy'] = [
    'class' => \HDNET\FalDummy\Resource\Driver\LocalDummyDriver::class,
    'shortName' => 'LocalDummy',
    'flexFormDS' => 'FILE:EXT:core/Configuration/Resource/Driver/LocalDriverFlexForm.xml',
    'label' => 'Local dummy',
];
