<?php

use Nelmio\CorsBundle\NelmioCorsBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppKernel extends Kernel
{
	public function registerBundles(): iterable
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new AppBundle\AppBundle(),
            new FOS\CKEditorBundle\FOSCKEditorBundle(),
            new Twig\Extra\TwigExtraBundle\TwigExtraBundle(),
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

        }

        return $bundles;
    }

	public function getRootDir()
	{
		return __DIR__;
	}

	public function getCacheDir(): string
	{
		return dirname(__DIR__).'/var/cache/'.$this->environment;
	}

	public function getLogDir(): string
	{
		return dirname(__DIR__).'/var/logs/'.$this->environment;
	}

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->setParameter('kernel.root_dir', $this->getRootDir());
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
