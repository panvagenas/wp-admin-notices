<?php
/**
 * build.php description
 *
 * @author    Panagiotis Vagenas <Panagiotis.Vagenas@interactivedata.com>
 * @date      2015-11-09
 * @version   $Id$
 * @copyright Copyright (c) 2015 Interactive Data Managed Solutions Ltd.
 */

ini_set('display_errors', 1);

$projectRoot     = dirname(dirname(__FILE__));
$buildRoot       = __DIR__;
$pharFileName    = 'wp-admin-notices.phar';
$pharFileAbsPath = $buildRoot . "/$pharFileName";

$excludeDirs = array(
    '.git',
    'tests',
    'build',
    'docs',
);

$excludeFiles = array(
    'LICENCE.txt',
    '.coveralls.yml',
    'composer.lock',
    '.gitignore',
    '.travis.yml',
    'composer.json',
    'README.md',
);

/**
 * @param SplFileInfo                     $file
 * @param mixed                           $key
 * @param RecursiveCallbackFilterIterator $iterator
 *
 * @return bool True if you need to recurse or if the item is acceptable
 */
$filter = function ($file, $key, $iterator) use ($excludeDirs, $excludeFiles)
{
    if ($iterator->hasChildren()
        && !in_array(
            $file->getFilename(), $excludeDirs
        )
    )
    {
        return true;
    }

    return $file->isFile() && !in_array($file->getFilename(), $excludeFiles);
};


$p = new Phar(
    $pharFileName, FilesystemIterator::CURRENT_AS_FILEINFO
                   | FilesystemIterator::KEY_AS_FILENAME
);
//issue the Phar::startBuffering() method call to buffer changes made to
// the archive until you issue the Phar::stopBuffering() command
$p->startBuffering();

//set the signature algorithm for the phar archive
$p->setSignatureAlgorithm(Phar::SHA512);

//set the Phar file stub
//the file stub is merely a small segment of code that gets run initially when
//the Phar file is loaded, and it always ends with a __HALT_COMPILER()
$p->setStub(
    '<?php Phar::mapPhar(); include "phar://'
    . $pharFileName
    . '/index.php"; __HALT_COMPILER(); ?>'
);
$p->setDefaultStub('index.php');

if (file_exists($pharFileAbsPath) && is_readable($pharFileAbsPath))
{
    unlink($pharFileAbsPath);
}
$gzFile = $pharFileAbsPath . '.gz';
if (file_exists($gzFile) && is_readable($gzFile))
{
    unlink($gzFile);
}

//Adding files to an archive using Phar::buildFromDirectory()
//adds all of the PHP files in the stated directory to the Phar archive

$innerIterator = new RecursiveDirectoryIterator(
    $projectRoot, RecursiveDirectoryIterator::SKIP_DOTS
);
$iterator      = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator($innerIterator, $filter)
);

$p->buildFromIterator($iterator, $projectRoot);

//Compresses the entire Phar archive using Gzip or Bzip2 compression
$p->compress(Phar::GZ);
//Note that a regular .phar archive will also be created besides the compresssed one

//Stop buffering write requests to the Phar archive, and save changes to disk
$p->stopBuffering();
echo "phar archive has been saved";
