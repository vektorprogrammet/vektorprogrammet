<?php

namespace AppBundle\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class FileUploader
{
    private $signatureFolder;
    private $logoFolder;

    /**
     * FileUploader constructor.
     * @param string $signatureFolder
     * @param string $logoFolder
     */
    public function __construct(string $signatureFolder, string $logoFolder)
    {
        $this->signatureFolder = $signatureFolder;
        $this->logoFolder = $logoFolder;
    }

    /**
     * @param Request $request
     * @return string absolute file path
     */
    public function uploadSignature(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->signatureFolder);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function uploadLogo(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->logoFolder);
    }

    /**
     * @param UploadedFile $file
     * @param string $targetFolder
     * @return string absolute file path
     */
    public function uploadFile(UploadedFile $file, string $targetFolder)
    {
        $originalFileName = $file->getClientOriginalName();
        $fileExt = $file->guessExtension();

        $fileName = $this->generateRandomFileNameWithExtension($fileExt);

        $relativePath = $this->getRelativePath($targetFolder, $fileName);

        $uploadSuccessful = move_uploaded_file($file->getPathname(), $relativePath);

        if (!$uploadSuccessful) {
            throw new UploadException('Could not copy the file '.$originalFileName.' to '.$relativePath);
        }

        return $this->getAbsolutePath($targetFolder, $fileName);
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
        return str_replace('..', '', $targetDir).'/'.$fileName;
    }
}
