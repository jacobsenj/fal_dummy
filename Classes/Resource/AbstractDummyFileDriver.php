<?php
/**
 * Local dummy driver
 * - Create configurable dummy image file.
 *
 */

namespace HDNET\FalDummy\Resource\Driver;

/**
 * Local dummy driver
 * - Create configurable dummy image file.
 *
 */
abstract class AbstractDummyFileDriver extends AbstractReplacementDriver
{
    /**
     * Dummy file identifier.
     *
     * @var string
     */
    protected $dummyFileIdentifier = '/dummyImage.png';

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
        if ($fileIdentifier === $this->dummyFileIdentifier) {
            return md5($fileIdentifier);
        }

        return parent::hash($fileIdentifier, $hashAlgorithm);
    }

    /**
     * Returns a list with the names of all files and folders in a path, optionally recursive.
     *
     * @param string $path         The absolute path
     * @param bool   $recursive    If TRUE, recursively fetches files and folders
     * @param bool   $includeFiles
     * @param bool   $includeDirs
     * @param string $sort
     * @param bool   $sortRev
     *
     * @return array
     */
    protected function retrieveFileAndFoldersInPath(
        $path,
        $recursive = false,
        $includeFiles = true,
        $includeDirs = true,
        $sort = '',
        $sortRev = false
    ) {
        $directoryEntries = parent::retrieveFileAndFoldersInPath($path, $recursive, $includeFiles, $includeDirs, $sort, $sortRev);
        if ($this->getAbsolutePath('/') === $path && $includeFiles) {
            $entryArray = [
                'identifier' => $this->dummyFileIdentifier,
                'name' => 'dummyImage.png',
                'type' => 'file',
            ];
            $directoryEntries[$this->dummyFileIdentifier] = $entryArray;
        }

        return $directoryEntries;
    }

    /**
     * Returns information about a file.
     *
     * @param string $fileIdentifier      in the case of the LocalDriver, this is the (relative) path to the file
     * @param array  $propertiesToExtract Array of properties which should be extracted, if empty all will be extracted
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getFileInfoByIdentifier($fileIdentifier, array $propertiesToExtract = [])
    {
        if ($fileIdentifier == $this->dummyFileIdentifier) {
            return [
                'name' => 'dummyImage.png',
                'identifier' => $fileIdentifier,
                'mimetype' => 'png/image',
                'storage' => $this->storageUid,
                'identifier_hash' => $this->hashIdentifier($fileIdentifier),
                'folder_hash' => $this->hashIdentifier($this->getParentFolderIdentifierOfIdentifier($fileIdentifier)),
            ];
        }

        return parent::getFileInfoByIdentifier($fileIdentifier, $propertiesToExtract);
    }
}
