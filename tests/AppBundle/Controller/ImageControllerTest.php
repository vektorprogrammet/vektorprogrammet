<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageControllerTest extends BaseWebTestCase
{
    /**
     * @var array $imagePaths
     */
    private $imagePaths;

    public function setUp()
    {
        // Keep track of all the initial files in the image folder
        $this->imagePaths = array();
        $finder = new Finder();

        if (!file_exists('images/gallery_images')) {
            return;
        }

        $finder->files()->in('images/gallery_images');
        foreach ($finder as $file) {
            array_push($this->imagePaths, $file->getRealPath());
        }
    }

    private function createMockImage()
    {
        $imageFile = tempnam(sys_get_temp_dir(), 'img');
        imagepng(imagecreatetruecolor(1, 1), $imageFile);
        return $imageFile;
    }

    public function testUpload()
    {
        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/bilde/upload/1');
        $form = $crawler->selectButton('Lagre')->form();
        $imageFile = $this->createMockImage();

        $uploadedImageFile = new UploadedFile($imageFile, 'image.png', null, null, null, true);

        $form['image[uploadedFile]'] = $uploadedImageFile;
        $form['image[description]'] = 'En flott beskrivelse';

        $client->submit($form);
        $this->assertEquals(1, $client->followRedirect()->filter('img[alt="En flott beskrivelse"]')->count());
    }

    public function testEdit()
    {
        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/bilde/rediger/1');
        $form = $crawler->selectButton('Endre beskrivelse')->form();

        $form['image[description]'] = 'En flott beskrivelse';

        $client->submit($form);
        $this->assertEquals(1, $client->followRedirect()->filter('img[alt="En flott beskrivelse"]')->count());
    }

    public function testDelete()
    {
        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/bilde/rediger/1');
        $form = $crawler->selectButton('Slett bilde')->form();

        $client->submit($form);

        $client->request('GET', '/kontrollpanel/bilde/rediger/1');
        $this->assertEquals(404, $client->getResponse()->getStatusCode()); // Doesn't exist
    }

    protected function tearDown()
    {
        parent::tearDown();

        $directoryExists = file_exists('images/gallery_images');
        if (!$directoryExists) {
            return;
        }

        // Delete all new files
        $finder = new Finder();
        $finder->files()->in('images/gallery_images');
        foreach ($finder as $file) {
            $fileIsNew = !in_array($file->getRealPath(), $this->imagePaths);
            if ($fileIsNew) {
                unlink($file);
            }
        }

        $imageGalleryDirectoryIsEmpty = count(glob('images/gallery_images/*')) === 0;
        if ($imageGalleryDirectoryIsEmpty) {
            rmdir('images/gallery_images');
        }
        $imageDirectoryIsEmpty = count(glob('images/*')) === 0;
        if ($imageDirectoryIsEmpty) {
            rmdir('images');
        }
    }
}
