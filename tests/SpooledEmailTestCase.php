<?php

namespace Tests;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

abstract class SpooledEmailTestCase extends KernelTestCase
{
    public function setUp()
    {
        $this->bootKernel(array(
            'environment' => 'test',
            'debug' => true,
        ));
    }

    public function purgeSpool()
    {
        $filesystem = new Filesystem();
        $finder = $this->getSpooledEmails();

        /** @var File $file */
        foreach ($finder as $file) {
            $filesystem->remove($file->getRealPath());
        }
    }

    /**
     * @return Finder
     */
    public function getSpooledEmails()
    {
        $finder = new Finder();
        $spoolDir = $this->getSpoolDir();
        $finder->files()->in($spoolDir);

        return $finder;
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function getEmailContent($file)
    {
        return unserialize(file_get_contents($file));
    }

    /**
     * @return string
     */
    protected function getSpoolDir()
    {
        dump(self::$kernel->getContainer()->getParameter('swiftmailer.'));
        return self::$kernel->getContainer()->getParameter('swiftmailer.spool.path');
    }
}
