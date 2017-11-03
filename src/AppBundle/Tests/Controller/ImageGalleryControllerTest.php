<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageGalleryControllerTest extends BaseWebTestCase
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

    private function fillForm($form)
    {
        $form['imageGallery[title]'] = 'En bra tittel';
        $form['imageGallery[referenceName]'] = 'Et informativt referansenavn';
        $form['imageGallery[description]'] = 'En flott beskrivelse';
        return $form;
    }

    public function testCreate()
    {
        $client = $this->createTeamLeaderClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier');
        $form = $crawler->selectButton('Opprett bildegalleri')->form();

        $client->submit($this->fillForm($form));
        $this->assertEquals(1, $client->followRedirect()->filter('h1:contains("En bra tittel")')->count());
    }

    public function testEdit()
    {
        $client = $this->createTeamLeaderClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier/galleri/1');
        $form = $crawler->selectButton('Lagre endringer')->form();

        $client->submit($this->fillForm($form));
        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->filter('h1:contains("En bra tittel")')->count());
        $this->assertEquals(1, $crawler->filter('p:contains("En flott beskrivelse")')->count());
    }

    public function testUploadImage()
    {
        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier/galleri/1/ny');
        $form = $crawler->selectButton('Lagre')->form();
        $imageFile = tempnam(sys_get_temp_dir(), 'img');
        imagepng(imagecreatetruecolor(1, 1), $imageFile);

        $uploadedImageFile = new UploadedFile($imageFile, 'image.png', null, null, null, true);

        $form['image[uploadedFile]'] = $uploadedImageFile;
        $form['image[description]'] = 'En flott beskrivelse';

        $client->submit($form);
        $this->assertEquals(1, $client->followRedirect()->filter('img[alt="En flott beskrivelse"]')->count());
    }

    public function testDelete()
    {
        // Create an empty gallery
        $client = $this->createTeamLeaderClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier');
        $form = $crawler->selectButton('Opprett bildegalleri')->form();

        $client->submit($this->fillForm($form));

        // Delete this
        $crawler = $client->followRedirect();
        $deleteButton = $crawler->filter('.secretly-not-a-button')->first()->form();
        $client->submit($deleteButton);
        $crawler = $client->followRedirect();
        $this->assertEquals(0, $crawler->filter('a:contains("En bra tittel")')->count());
    }

    public function testEditImage()
    {
        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier/rediger/1');
        $form = $crawler->selectButton('Endre beskrivelse')->form();

        $form['image[description]'] = 'En flott beskrivelse';

        $client->submit($form);
        $this->assertEquals(1, $client->followRedirect()->filter('img[alt="En flott beskrivelse"]')->count());
    }

    public function testDeleteImage()
    {
        $client = $this->createTeamMemberClient();
        $crawler = $client->request('GET', '/kontrollpanel/bildegallerier/rediger/1');
        $form = $crawler->selectButton('Slett bilde')->form();

        $client->submit($form);

        $client->request('GET', '/kontrollpanel/bildegallerier/rediger/1');
        $this->assertEquals(404, $client->getResponse()->getStatusCode()); // Doesn't exist
    }

    protected function tearDown()
    {
        parent::tearDown();

        $directoryExists = file_exists('images/image_galleries');
        if (!$directoryExists) {
            return;
        }

        // Delete all new files
        $finder = new Finder();
        $finder->files()->in('images/image_galleries');
        foreach ($finder as $file) {
            $fileIsNew = !in_array($file->getRealPath(), $this->imagePaths);
            if ($fileIsNew) {
                unlink($file);
            }
        }

        $imageGalleryDirectoryIsEmpty = count(glob('images/image_galleries/*')) === 0;
        $imageDirectoryIsEmpty = count(glob('images/*')) === 0;
        if ($imageGalleryDirectoryIsEmpty) {
            rmdir('images/image_galleries');
        }
        if ($imageDirectoryIsEmpty) {
            rmdir('images');
        }
    }
}
