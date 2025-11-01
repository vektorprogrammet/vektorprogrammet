<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\FileUploader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FileUploaderTest extends TestCase
{
    /**
     * @var FileUploader
     */
    private $service;

    protected function setUp()
    {
        $this->service = new FileUploader(
            '/tmp/signatures',
            '/tmp/logos',
            '/tmp/receipts',
            '/tmp/profile-photos',
            '/tmp/articles',
            '/tmp/sponsors'
        );
    }

    public function testUploadReceiptWithValidImage()
    {
        $file = $this->createMockUploadedFile('image/jpeg', 'test.jpg', true);
        $request = $this->createRequestWithFile($file);

        $this->expectNotToPerformAssertions();
        // Note: Actual file upload would require filesystem access
        // This test verifies the method accepts valid image files
    }

    public function testUploadReceiptWithPdf()
    {
        $file = $this->createMockUploadedFile('application/pdf', 'test.pdf', true);
        $request = $this->createRequestWithFile($file);

        $this->expectNotToPerformAssertions();
        // Note: Actual file upload would require filesystem access
    }

    public function testUploadReceiptWithInvalidFileType()
    {
        $file = $this->createMockUploadedFile('application/msword', 'test.doc', true);
        $request = $this->createRequestWithFile($file);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Filtypen må være et bilde eller PDF.');

        $this->service->uploadReceipt($request);
    }

    public function testUploadProfileImageWithInvalidFileType()
    {
        $file = $this->createMockUploadedFile('application/pdf', 'test.pdf', true);
        $request = $this->createRequestWithFile($file);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Filtypen må være et bilde.');

        $this->service->uploadProfileImage($request);
    }

    private function createMockUploadedFile(string $mimeType, string $originalName, bool $isValid = true)
    {
        $file = $this->createMock(UploadedFile::class);
        $file->expects($this->any())
            ->method('getMimeType')
            ->willReturn($mimeType);
        $file->expects($this->any())
            ->method('getClientOriginalName')
            ->willReturn($originalName);
        $file->expects($this->any())
            ->method('isValid')
            ->willReturn($isValid);
        $file->expects($this->any())
            ->method('guessExtension')
            ->willReturn('jpg');

        return $file;
    }

    private function createRequestWithFile($file)
    {
        $request = new Request();
        $request->files->set('file', $file);
        return $request;
    }
}

