<?php
/**
 * AbstractDriver
 *
 * Additional functions
 */

namespace HDNET\FalDummy\Resource\Driver;

use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;
use TYPO3\CMS\Core\Resource\Index\FileIndexRepository;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * AbstractDriver
 *
 * Additional functions
 */
abstract class AbstractDriver extends LocalDriver
{


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

            DebuggerUtility::var_dump($fileProperties);
            die();

            if (isset($fileProperties['type']) && (int)$fileProperties['type'] === AbstractFile::FILETYPE_IMAGE) {
                return false;
            }
            // @todo prüfen ob es die Datei Lokal gibt
        }

        return true;
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
