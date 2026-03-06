<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUploader
{
    public function __construct(
        private readonly string $signatureFolder,
        private readonly string $logoFolder,
        private readonly string $receiptFolder,
        private readonly string $profilePhotoFolder,
        private readonly string $articleFolder,
        private readonly string $sponsorFolder,
    ) {
    }

    /**
     * @return string absolute file path
     */
    public function uploadSponsor(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->sponsorFolder);
    }

    /**
     * @return string absolute file path
     */
    public function uploadSignature(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->signatureFolder);
    }

    /**
     * @return string
     */
    public function uploadLogo(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        return $this->uploadFile($file, $this->logoFolder);
    }

    /**
     * @return string
     */
    public function uploadReceipt(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        $mimeType = $file->getMimeType();
        $fileType = explode('/', (string) $mimeType)[0];
        if ($fileType === 'image' || $mimeType === 'application/pdf') {
            return $this->uploadFile($file, $this->receiptFolder);
        }
        throw new BadRequestHttpException('Filtypen må være et bilde eller PDF.');
    }

    /**
     * @return string
     */
    public function uploadProfileImage(Request $request)
    {
        $file = $this->getFileFromRequest($request);

        $mimeType = $file->getMimeType();
        $fileType = explode('/', (string) $mimeType)[0];
        if ($fileType === 'image') {
            return $this->uploadFile($file, $this->profilePhotoFolder);
        }
        throw new BadRequestHttpException('Filtypen må være et bilde.');
    }

    /**
     * @return string
     */
    public function uploadArticleImage(Request $request, string $id)
    {
        $file = $this->getFileFromRequest($request, $id);
        if (!$file) {
            return null;
        }

        $mimeType = $file->getMimeType();
        $fileType = explode('/', (string) $mimeType)[0];
        if ($fileType === 'image') {
            return $this->uploadFile($file, $this->articleFolder);
        }
        throw new BadRequestHttpException('Filtypen må være et bilde.');
    }

    /**
     * @return string absolute file path
     */
    public function uploadFile(UploadedFile $file, string $targetFolder)
    {
        $fileExt = $file->guessExtension();
        $fileName = $this->generateRandomFileNameWithExtension($fileExt);

        if (!is_dir($targetFolder)) {
            mkdir($targetFolder, 0o775, true);
        }

        try {
            $file->move($targetFolder, $fileName);
        } catch (FileException) {
            $originalFileName = $file->getClientOriginalName();
            $relativePath = $this->getRelativePath($targetFolder, $fileName);

            throw new UploadException('Could not copy the file '.$originalFileName.' to '.$relativePath);
        }

        return $this->getAbsolutePath($targetFolder, $fileName);
    }

    public function deleteSponsor(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->sponsorFolder/$fileName");
    }

    public function deleteSignature(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->signatureFolder/$fileName");
    }

    public function deleteReceipt(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->receiptFolder/$fileName");
    }

    public function deleteProfileImage(string $path)
    {
        if (empty($path)) {
            return;
        }

        $fileName = $this->getFileNameFromPath($path);

        $this->deleteFile("$this->profilePhotoFolder/$fileName");
    }

    public function deleteFile(string $path)
    {
        if (file_exists($path)) {
            if (!unlink($path)) {
                throw new FileException('Could not remove file '.$path);
            }
        }
    }

    private function getFileFromRequest(Request $request, $id = null)
    {
        $fileKey = $id ?? current($request->files->keys());
        $file = $request->files->get($fileKey);

        if (is_array($file)) {
            return current($file);
        }

        return $file;
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
