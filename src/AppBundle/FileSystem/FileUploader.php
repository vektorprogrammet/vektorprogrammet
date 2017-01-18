<?php

namespace AppBundle\FileSystem;

use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 29.04.2015
 * Time: 17:28.
 *
 * Handles file uploads by reading the Request object for files and moving them to a specified location on the
 * server file system.
 */
class FileUploader
{
    private $targetFolder;

    /**
     * Creates a file uploader that will put files in specified target folder.
     * Throws an exception if user does not have the rights to move files to target folder.
     *
     * allowedFileTypes: e.g. array('image/gif', 'image/jpeg', 'image/png');
     *
     * @param $targetFolder
     *
     * @throws \Exception
     */
    public function __construct($targetFolder)
    {
        if (!$this->userIsAllowedToUploadFilesToFolder($targetFolder)) {
            throw new \Exception('This user can not upload files to this folder.');
        }
        $this->targetFolder = $targetFolder;
    }

    /**
     * This method will read the Request object for any files and move them to target folder.
     * For each file moved it will add to the returned associative array an entry originalFileName => uniqueFilename
     * originalFileName is the name of the file in the Request object. uniqueFilename is a filename <b>with full path</b>
     * that is guaranteed to be unique, and is the name that must be used to access it on the server.
     *
     * If 1 file is not successfully moved, an exception will be thrown immediately without trying to move any more files.
     *
     * If 1 file is of a mime type not specified in the constructor, an exception will be thrown immediately without
     * trying to move any more files.
     *
     * @param $request
     *
     * @return array
     *
     * @throws \Exception
     */
    public function upload(Request $request)
    {
        //Create return array
        $returnArray = array();
        foreach ($request->files->keys() as $key) {
            $curFile = $request->files->get($key);
            if (is_array($curFile)) {
                $curFile = $curFile[array_keys($curFile)[0]];
            }

            //Original file name:
            $originalFileName = $curFile->getClientOriginalName();
            $fileExt = $curFile->guessExtension();
            //Create a unique name
            $uniqueNameWithPath = uniqid($this->targetFolder, $originalFileName).'.'.$fileExt;
            //Try to move the file to its target folder
            if (move_uploaded_file(
                $curFile->getPathName(),
                $uniqueNameWithPath
            )) {
                //Succeeded in moving this file. Add mapping to array
                $returnArray[$originalFileName] = $uniqueNameWithPath;
            } else {
                throw new \Exception('Could not copy the file '.$originalFileName.' to '.$uniqueNameWithPath);
            }
        }

        return $returnArray;
    }

    public function undo()
    {
    }

    /**
     * Checks if current user is allowed to copy a file to targetFolder.
     *
     * @param $targetFolder
     *
     * @return bool
     */
    private function userIsAllowedToUploadFilesToFolder($targetFolder)
    {
        return true;
    }
}
