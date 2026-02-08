<?php

use Nelmio\CorsBundle\NelmioCorsBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
	public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new App\AppBundle(),
            new FOS\CKEditorBundle\FOSCKEditorBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FM\ElfinderBundle\FMElfinderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            new FOS\RestBundle\FOSRestBundle(),
        );

	    if ( in_array( $this->getEnvironment(), array( 'prod', 'staging' ) ) ) {
		    $bundles[] = new Sentry\SentryBundle\SentryBundle();
	    }

        if (in_array($this->getEnvironment(), array('dev', 'test', 'staging'))) {
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new NelmioCorsBundle();

            if ('dev' === $this->getEnvironment()) {
	            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

	public function getRootDir()
	{
		return __DIR__;
	}

	public function getCacheDir()
	{
		return dirname(__DIR__).'/var/cache/'.$this->environment;
	}

	public function getLogDir()
	{
		return dirname(__DIR__).'/var/logs/'.$this->environment;
	}

	public function getProjectDir()
	{
		return dirname(__DIR__);
	}

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(dirname(__DIR__).'/config/config_'.$this->getEnvironment().'.yml');
    }
}
