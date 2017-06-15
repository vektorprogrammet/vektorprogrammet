<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileUploader
{
    private $signatureFolder;
    private $logoFolder;
    private $receiptFolder;

    /**
     * FileUploader constructor.
     *
     * @param string $signatureFolder
     * @param string $logoFolder
     */
    public function __construct(string $signatureFolder, string $logoFolder, string $receiptFolder)
    {
        $this->signatureFolder = $signatureFolder;
        $this->logoFolder = $logoFolder;
        $this->receiptFolder = $receiptFolder;
    }

    /**
     * @param Request $request
     *
     * @return string absolute file path
     */
    public function uploadSignature(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->signatureFolder);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function uploadLogo(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->logoFolder);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function uploadReceipt(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->receiptFolder);
    }

    /**
     * @param UploadedFile $file
     * @param string       $targetFolder
     *
     * @return string absolute file path
     */
    public function uploadFile(UploadedFile $file, string $targetFolder)
    {
        $originalFileName = $file->getClientOriginalName();
        $fileExt = $file->guessExtension();

        $fileName = $this->generateRandomFileNameWithExtension($fileExt);

        $relativePath = $this->getRelativePath($targetFolder, $fileName);

        if (!is_dir($targetFolder)) {
        	mkdir($targetFolder);
        }

        $uploadSuccessful = move_uploaded_file($file->getPathname(), $relativePath);

        if (!$uploadSuccessful) {
            throw new UploadException('Could not copy the file '.$originalFileName.' to '.$relativePath);
        }

        return $this->getAbsolutePath($targetFolder, $fileName);
    }

    public function deleteSignature(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->signatureFolder/$fileName");
    }

    public function deleteFile(string $path)
    {
        if (file_exists($path)) {
            if (!unlink($path)) {
                throw new FileException('Could not remove file '.$path);
            }
        }
    }

    private function getFileFromRequest(Request $request)
    {
        $fileKey = current($request->files->keys());

        return current($request->files->get($fileKey));
    }

    private function generateRandomFileNameWithExtension(string $fileExtension)
    {
        return uniqid().'.'.$fileExtension;
    }

    private function getRelativePath(string $targetDir, string $fileName)
    {
        return "$targetDir/$fileName";
    }

    private function getAbsolutePath(string $targetDir, string $fileName)
    {
        // Removes ../, ./, //
        $absoluteTargetDir = preg_replace('/\.+\/|\/\//i', '', $targetDir);

        if ($absoluteTargetDir[0] !== '/') {
            $absoluteTargetDir = '/'.$absoluteTargetDir;
        }

        return "$absoluteTargetDir/$fileName";
    }

    private function getFileNameFromPath(string $path)
    {
        return substr($path, strrpos($path, '/') + 1);
    }
}
