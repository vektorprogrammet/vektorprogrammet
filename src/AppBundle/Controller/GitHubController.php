<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GitHubController extends Controller
{
    public function deployAction(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        if (!array_key_exists('hook', $payload) || !array_key_exists('config', $payload['hook']) ||
            !array_key_exists('secret', ['hook']['config'])
        ) {
            return new JsonResponse(['status' => 'failed']);
        }
        $secret = $payload['hook']['config']['secret'];
        $isMaster = array_key_exists('ref', $payload) && $payload['ref'] === 'refs/heads/master';
        $isCorrectSecret = $secret === $this->getParameter('github_webook_secret');

        if ($isMaster && $isCorrectSecret) {
            shell_exec($this->getParameter('kernel.root_dir').'/../deploy.sh');

            return new JsonResponse(['status' => 'success']);
        } else {
            return new JsonResponse(['status' => 'failed']);
        }
    }
}
