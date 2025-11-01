<?php

namespace App\Contract;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for FileUploader service.
 * Defines contract for file upload operations.
 */
interface FileUploaderInterface
{
    /**
     * Upload sponsor file.
     *
     * @param Request $request
     * @return string absolute file path
     */
    public function uploadSponsor(Request $request);

    /**
     * Upload signature file.
     *
     * @param Request $request
     * @return string absolute file path
     */
    public function uploadSignature(Request $request);

    /**
     * Upload logo file.
     *
     * @param Request $request
     * @return string
     */
    public function uploadLogo(Request $request);

    /**
     * Upload receipt file.
     *
     * @param Request $request
     * @return string
     */
    public function uploadReceipt(Request $request);

    /**
     * Upload profile image.
     *
     * @param Request $request
     * @return string
     */
    public function uploadProfileImage(Request $request);

    /**
     * Upload article image.
     *
     * @param Request $request
     * @param string $id
     * @return string|null
     */
    public function uploadArticleImage(Request $request, string $id);

    /**
     * Upload file to target folder.
     *
     * @param UploadedFile $file
     * @param string $targetFolder
     * @return string absolute file path
     */
    public function uploadFile(UploadedFile $file, string $targetFolder);

    /**
     * Delete sponsor file.
     *
     * @param string $path
     */
    public function deleteSponsor(string $path);

    /**
     * Delete signature file.
     *
     * @param string $path
     */
    public function deleteSignature(string $path);

    /**
     * Delete receipt file.
     *
     * @param string $path
     */
    public function deleteReceipt(string $path);

    /**
     * Delete profile image.
     *
     * @param string $path
     */
    public function deleteProfileImage(string $path);

    /**
     * Delete file.
     *
     * @param string $path
     */
    public function deleteFile(string $path);
}
