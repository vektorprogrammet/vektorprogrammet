<?php


namespace AppBundle\Tests\Validation;

use AppBundle\Tests\BaseWebTestCase;
use PhpCollection\Set;
use Symfony\Component\DomCrawler\Crawler;

class ValidationTest extends BaseWebTestCase
{
    const urls = array(
        "/assistenter",
        "/resetpassord",
        "/team/application/1",
        "/kontrollpanel/deltakerhistorikk/rediger/1",
        "/kontrollpanel/hovedstyret/rediger_medlem/2",
        "/kontrollpanel/teamadmin/oppdater/teamhistorie/1",
    );

    const formElements = array(
                'input',
                'select',
                'textarea',
                'datalist'
    );
    const maxNumRedirects = 20;

    public function testValidation()
    {
        foreach (self::urls as $url) {
            $client = $this->createAdminClient();
            $crawler = $client->request("GET", $url);

            // Find names all form elements
            $names = new Set();
            foreach (self::formElements as $formElement) {
                $formElementCrawler = $crawler->filter("form $formElement");
                for ($i = 0; $i < $formElementCrawler->count(); $i++) {
                    $elem = $formElementCrawler->getNode($i);
                    $name = $elem->getAttribute('name');
                    $isTypeSubmit = $elem->getAttribute('type') === 'submit';
                    $isCSRFToken = strpos($name, '[_token]') !== false;
                    if (!$isTypeSubmit && !$isCSRFToken) {
                        $names->add($name);
                    }
                }
            }

            foreach ($names as $name) {
                $client = $this->createAdminClient();
                $crawler = $client->request("GET", $url);

                $elemCrawler = $crawler->filter('form [name="'.$name.'"]');
                for ($i = 0; $i < $elemCrawler->count(); $i++) {
                    $elem = $elemCrawler->getNode($i);
                    $this->destroyElement($elem);
                }

                $form = $this->getForm($crawler);

                try {
                    $client->submit($form);
                } catch (\Error $e) {
                    $this->fail("Error when submitting form at $url without $name.\n$e");
                }

                // Sometimes the form submits fine but stores a null value in the
                // db which later results in error
                try {
                    $numRedirects = 0;
                    while ($client->getResponse()
                            ->getStatusCode() === 302 && $numRedirects < 20) {
                        $client->followRedirect();
                        $numRedirects++;
                    }
                } catch (\Error $e) {
                    $this->fail("Error when submitting form at $url without $name after redirecting.\n$e");
                }

                if ($client->getResponse()->getStatusCode() === 302) {
                    $this->fail("Validation error resulted in an infinite redirect loop (more than 20 redirects when submitting form without $name");
                }
            }
        }
    }

    /**
     * Gets the first form on the page
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     *
     * @return null|\Symfony\Component\DomCrawler\Form
     */
    private function getForm(Crawler $crawler)
    {
        $form = null;
        $buttonCrawler = $crawler->filter('form button');
        if ($buttonCrawler->count() > 0) {
            $form = $buttonCrawler->form();
        }

        $typeSubmitCrawler = $crawler->filter('form [type="submit"]');
        if ($typeSubmitCrawler->count() > 0) {
            $form = $typeSubmitCrawler->form();
        }
        if ($form === null) {
            $this->fail("Could not find form button");
        }
        return $form;
    }

    /**
     * Removes element from DOM
     *
     * @param \DOMElement $element
     */
    private function destroyElement(\DOMElement &$element)
    {
        $element->parentNode->removeChild($element);
    }
}
