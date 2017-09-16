<?php

/**
 * DummyFileService
 */

namespace HDNET\FalDummy\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * DummyFileService
 */
class DummyFileService extends AbstractService
{

    /**
     * The path to the local dummy resources.
     *
     * @var string
     */
    protected $localDummyResourcePath;

    /**
     * Initializes this object. This is called by the storage after the driver
     * has been attached.
     */
    public function __construct()
    {
        $this->localDummyResourcePath = ExtensionManagementUtility::siteRelPath('fal_dummy') . 'Resources/Public/Dummy/';
    }

    /**
     * Find a Dummy file by extension
     *
     * @param string $extension
     * @return string
     */
    public function findByExtension(string $extension): string
    {

        DebuggerUtility::var_dump($extension);
        die();
        return '';


    }

}
