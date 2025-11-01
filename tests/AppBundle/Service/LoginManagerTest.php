<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\LoginManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class LoginManagerTest extends TestCase
{
    /**
     * @var LoginManager
     */
    private $service;

    /**
     * @var Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    private $twig;

    /**
     * @var AuthenticationUtils|\PHPUnit\Framework\MockObject\MockObject
     */
    private $authenticationUtils;

    /**
     * @var RouterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $router;

    protected function setUp()
    {
        $this->twig = $this->createMock(Environment::class);
        $this->authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->service = new LoginManager($this->twig, $this->authenticationUtils, $this->router);
    }

    public function testRenderLogin()
    {
        $message = 'Test message';
        $redirectPath = 'home';
        $generatedPath = '/home';

        $this->authenticationUtils->expects($this->once())
            ->method('getLastAuthenticationError')
            ->willReturn(null);

        $this->router->expects($this->once())
            ->method('generate')
            ->with($redirectPath)
            ->willReturn($generatedPath);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('login/login.html.twig', [
                'last_username' => null,
                'error' => null,
                'message' => $message,
                'redirect_path' => $generatedPath,
            ])
            ->willReturn('<html>Login form</html>');

        $result = $this->service->renderLogin($message, $redirectPath);

        $this->assertEquals('<html>Login form</html>', $result);
    }

    public function testRenderLoginWithError()
    {
        $message = 'Test message';
        $redirectPath = 'home';
        $generatedPath = '/home';
        $error = $this->createMock(\Symfony\Component\Security\Core\Exception\AuthenticationException::class);

        $this->authenticationUtils->expects($this->once())
            ->method('getLastAuthenticationError')
            ->willReturn($error);

        $this->router->expects($this->once())
            ->method('generate')
            ->with($redirectPath)
            ->willReturn($generatedPath);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('login/login.html.twig', [
                'last_username' => null,
                'error' => $error,
                'message' => $message,
                'redirect_path' => $generatedPath,
            ])
            ->willReturn('<html>Login form with error</html>');

        $result = $this->service->renderLogin($message, $redirectPath);

        $this->assertEquals('<html>Login form with error</html>', $result);
    }
}

