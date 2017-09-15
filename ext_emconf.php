<?php
/**
 * $EM_CONF
 *
 * @package    Hdnet
 * @author     Tim Lochmüller <tim.lochmueller@hdnet.de>
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'FAL Dummy Driver',
    'description' => '',
    'category' => 'misc',
    'version' => '0.1.0',
    'dependencies' => 'hdnet',
    'state' => 'stable',
    'author' => 'HDNET GmbH & Co. KG',
    'author_email' => '',
    'author_company' => 'hdnet.de',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-0.0.0',
        ],
    ],
    'autoload' => [
        'psr-4' => ['HDNET\\FalDummy\\' => 'Classes']
    ],
];
