<?php

namespace Tests\AppBundle\Api;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\BaseWebTestCase;

class ProfilePhotoApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testUploadPhotoRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/me/photo', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUploadPhotoSucceeds(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        // Create a small valid test image
        $tmpPath = sys_get_temp_dir().'/test_photo_'.uniqid().'.png';
        $image = imagecreatetruecolor(10, 10);
        imagepng($image, $tmpPath);

        $uploadedFile = new UploadedFile(
            $tmpPath,
            'test_photo.png',
            'image/png',
            null,
            true
        );

        $client = static::createClient();
        $client->request('POST', '/api/me/photo', [], ['photo' => $uploadedFile], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(204);

        @unlink($tmpPath);
    }

    public function testUploadNonImageFileReturns400(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        // Create a plain text file (not an image)
        $tmpPath = sys_get_temp_dir().'/test_not_image_'.uniqid().'.txt';
        file_put_contents($tmpPath, 'This is not an image file.');

        $uploadedFile = new UploadedFile(
            $tmpPath,
            'not_an_image.txt',
            'text/plain',
            null,
            true
        );

        $client = static::createClient();
        $client->request('POST', '/api/me/photo', [], ['file' => $uploadedFile], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(400);

        @unlink($tmpPath);
    }
}
