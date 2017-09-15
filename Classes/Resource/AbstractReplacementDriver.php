<?php
/**
 * Local dummy driver
 * - Replace existing image files.
 *
 */

namespace HDNET\FalDummy\Resource\Driver;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * Local dummy driver
 * - Replace existing image files.
 *
 */
abstract class AbstractReplacementDriver extends AbstractDriver
{
    /**
     * Directly output the contents of the file to the output
     * buffer. Should not take care of header files or flushing
     * buffer before. Will be taken care of by the Storage.
     *
     * @param string $identifier
     */
    public function dumpFileContents($identifier)
    {
        if ($this->useParentDriver($identifier)) {
            parent::dumpFileContents($identifier);
        }
        echo $this->getFileContents($identifier);
    }

    /**
     * Checks if a file exists.
     *
     * @param string $fileIdentifier
     *
     * @return bool
     */
    public function fileExists($fileIdentifier)
    {
        if ($this->useParentDriver($fileIdentifier)) {
            return parent::fileExists($fileIdentifier);
        }

        return true;
    }

    /**
     * Returns the contents of a file. Beware that this requires to load the
     * complete file into memory and also may require fetching the file from an
     * external location. So this might be an expensive operation (both in terms
     * of processing resources and money) for large files.
     *
     * @param string $fileIdentifier
     *
     * @throws \RuntimeException
     *
     * @return string The file contents
     */
    public function getFileContents($fileIdentifier)
    {
        if ($this->useParentDriver($fileIdentifier)) {
            return parent::getFileContents($fileIdentifier);
        }

        $errorReport = [];
        $imageUrl = $this->getPublicUrl($fileIdentifier);
        $result = GeneralUtility::getUrl($imageUrl, 0, false, $errorReport);

        if ($result === false) {
            throw new \RuntimeException(sprintf(
                'Error fetching placeholder image %s, occured error was: ',
                $imageUrl
            ) . $errorReport['message']);
        }

        return $result;
    }

    /**
     * Downloads the file from the placeholder service and stores it in the temporary file.
     *
     * @param string $fileIdentifier
     * @param bool   $writable
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function getFileForLocalProcessing($fileIdentifier, $writable = true)
    {
        if ($this->useParentDriver($fileIdentifier)) {
            return parent::getFileForLocalProcessing($fileIdentifier, $writable);
        }

        $temporaryPath = $this->getTemporaryPathForFile($fileIdentifier);
        $content = $this->getFileContents($fileIdentifier);
        $result = file_put_contents($temporaryPath, $content);
        touch($temporaryPath, $this->getFileObjectByIdentifier($fileIdentifier)
            ->getModificationTime());

        if ($result === false) {
            throw new \RuntimeException('Copying file ' . $fileIdentifier . ' to temporary path failed.', 1320577649);
        }

        return $temporaryPath;
    }

    /**
     * Returns information about a folder, no matter if it exists.
     *
     * @param string $folderIdentifier in the case of the LocalDriver, this is the (relative) path to the file
     *
     * @return array
     *
     * @throws \TYPO3\CMS\Core\Resource\Exception\FolderDoesNotExistException
     */
    public function getFolderInfoByIdentifier($folderIdentifier)
    {
        if (is_dir($this->getAbsolutePath($folderIdentifier))) {
            return parent::getFolderInfoByIdentifier($folderIdentifier);
        }

        return [
            'identifier' => $folderIdentifier,
            'name' => PathUtility::basename($folderIdentifier),
            'storage' => $this->storageUid,
        ];
    }

    /**
     * We always return read and write permissions if the file does not exist.
     *
     * @param string $identifier
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function getPermissions($identifier)
    {
        if (file_exists($this->getAbsolutePath($identifier))) {
            return parent::getPermissions($identifier);
        }

        return [
            'r' => true,
        ];
    }

    /**
     * Returns the public URL to a file.
     * Either fully qualified URL or relative to PATH_site (rawurlencoded).
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getPublicUrl($identifier)
    {
        if ($this->useParentDriver($identifier)) {
            return parent::getPublicUrl($identifier);
        }

        return $this->getReplaceUrl($identifier);
    }

    /**
     * Creates a (cryptographic) hash for a file.
     *
     * @param string $fileIdentifier
     * @param string $hashAlgorithm  The hash algorithm to use
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function hash($fileIdentifier, $hashAlgorithm)
    {
        if ($this->useParentDriver($fileIdentifier)) {
            return parent::hash($fileIdentifier, $hashAlgorithm);
        }

        if (!in_array($hashAlgorithm, $this->supportedHashAlgorithms)) {
            throw new \InvalidArgumentException(
                'Hash algorithm "' . $hashAlgorithm . '" is not supported.',
                1304964032
            );
        }

        if ($hashAlgorithm === 'sha1') {
            return $this->getFileObjectByIdentifier($fileIdentifier)
                ->getProperty('sha1');
        }

        $content = $this->getFileContents($fileIdentifier);

        return md5($content);
    }
}
