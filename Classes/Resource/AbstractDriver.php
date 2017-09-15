<?php
/**
 * Local dummy driver
 * - Genral basic functions.
 *
 */

namespace HDNET\FalDummy\Resource\Driver;

use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;
use TYPO3\CMS\Core\Resource\Index\FileIndexRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Local dummy driver
 * - Genral basic functions.
 *
 */
abstract class AbstractDriver extends LocalDriver
{
    /**
     * The path to the local dummy resources.
     *
     * @var string
     */
    protected $localDummyResourcePath;

    /**
     * Placeholder service URL.
     *
     * @var string
     */
    protected $placeholderServiceUrl = 'https://placehold.it/%dx%d&text=%s';

    /**
     * Initializes this object. This is called by the storage after the driver
     * has been attached.
     */
    public function initialize()
    {
        parent::initialize();
        $this->localDummyResourcePath = ExtensionManagementUtility::siteRelPath('hdnet') . 'Resources/Public/DummyFiles/';
    }

    /**
     * get replace URL.
     *
     * @param $identifier
     *
     * @return string
     */
    protected function getReplaceUrl($identifier)
    {
        $file = $this->getFileObjectByIdentifier($identifier);

        $publicUrl = $this->getLocalUrl($file);

        if ($publicUrl !== null) {
            return $publicUrl;
        }

        return sprintf(
            $this->placeholderServiceUrl,
            $file->getProperty('width'),
            $file->getProperty('height'),
            $this->getFileText($file)
        );
    }

    /**
     * get File Text.
     *
     * @param \TYPO3\CMS\Core\Resource\File $file
     *
     * @return string
     */
    protected function getFileText($file)
    {
        $properties = $file->getProperties();
        $fallBacks = [
            'title',
            'alternative',
            'description',
            'name',
        ];
        foreach ($fallBacks as $fallBack) {
            if (isset($properties[$fallBack]) && trim($properties[$fallBack]) != '') {
                return $properties[$fallBack];
            }
        }

        return '';
    }

    /**
     * get local URL.
     *
     * @param \TYPO3\CMS\Core\Resource\File $file
     *
     * @todo file handling excel, word etc.
     */
    protected function getLocalUrl($file)
    {
        $extension = $file->getExtension();
        $dummyFile = $this->localDummyResourcePath . $extension . '.' . $extension;

        if (is_file($dummyFile)) {
            $dummyFileObject = $this->getResourceFactory()
                ->getFileObjectFromCombinedIdentifier($dummyFile);
        }

        if (isset($dummyFileObject) && $dummyFileObject->exists()) {
            return $dummyFileObject->getPublicUrl();
        } else {
            return null;
        }
    }

    /**
     * Checks if this driver should be used for the given file.
     * We only use a dummy image if the real file does not exist and if the file is an image.
     *
     * @param string $fileIdentifier
     *
     * @return bool
     */
    protected function useParentDriver($fileIdentifier)
    {
        if ($fileIdentifier === '/dummyImage.png') {
            return false;
        }
        if (is_file($this->getAbsolutePath($fileIdentifier))) {
            return true;
        }

        $storage = $this->getResourceFactory()
            ->getStorageObject($this->storageUid);

        if ($storage->isWithinProcessingFolder($fileIdentifier)) {
            return true;
        }

        $fileData = $this->getFileIndexRepository()
            ->findOneByStorageUidAndIdentifier($storage->getUid(), $fileIdentifier);
        if ($fileData === false) {
            return true;
        }

        $file = $this->getResourceFactory()
            ->getFileObjectByStorageAndIdentifier($this->storageUid, $fileIdentifier);

        if (isset($file)) {
            $fileProperties = $file->getProperties();

            if (isset($fileProperties['type']) && (int) $fileProperties['type'] === AbstractFile::FILETYPE_IMAGE) {
                return false;
            }
            // @todo prüfen ob es die Datei Lokal gibt
        }

        return true;
    }

    /**
     * get File Object by Identifier.
     *
     * @param string $identifier
     *
     * @return null|\TYPO3\CMS\Core\Resource\File|\TYPO3\CMS\Core\Resource\ProcessedFile
     */
    protected function getFileObjectByIdentifier($identifier)
    {
        return $this->getResourceFactory()
            ->getFileObjectByStorageAndIdentifier($this->storageUid, $identifier);
    }

    /**
     * Returns an instance of the FileIndexRepository.
     *
     * @return FileIndexRepository
     */
    protected function getFileIndexRepository()
    {
        return FileIndexRepository::getInstance();
    }

    /**
     * Returns an instance of the ResourceFactory.
     *
     * @return ResourceFactory
     */
    protected function getResourceFactory()
    {
        return ResourceFactory::getInstance();
    }
}
