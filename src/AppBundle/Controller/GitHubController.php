<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GitHubController extends BaseController
{
    private $repositoryName = 'vektorprogrammet/vektorprogrammet';

    public function deployAction(Request $request)
    {
        // Check if request is from GitHub
        $ipIsFromGitHub = $this->ipIsFromGitHub($request->getClientIp());
        if (!$ipIsFromGitHub) {
            throw new AccessDeniedException('Ip is not from GitHub');
        }

        // Get data
        $payload = json_decode($request->getContent(), true);

        $repositoryNameExists = array_key_exists('repository', $payload) && array_key_exists('full_name', $payload['repository']);
        $isCorrectRepository = $repositoryNameExists && $payload['repository']['full_name'] === $this->repositoryName;
        $isMaster = array_key_exists('ref', $payload) && $payload['ref'] === 'refs/heads/master';
        $commit = $payload['head_commit'] ?? null;
        $committer = $commit['author']['name'] ?? 'unknown';
        $message = $commit['message'] ?? 'Commit message not found';

        // Execute deploy script if there is a push to master
        if ($isCorrectRepository && $isMaster && $commit !== null) {
            $this->get('app.logger')->info(
                "New commit on master by *$committer*:\n".
                "```$message```\n".
                "Deploying changes..."
            );
            shell_exec($this->getParameter('kernel.root_dir').'/../deploy.sh');
            $this->get('app.logger')->info('Deploy complete');

            return new JsonResponse(['status' => 'Deployed']);
        } else {
            return new JsonResponse(['status' => 'Not a master push to '.$this->repositoryName]);
        }
    }

    private function ipIsFromGitHub($ip)
    {
        // Use curl to create a GET request to the GitHub api
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/meta');
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: NoBrowser v0.1 beta',
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($ch);

        // Get ip ranges that GitHub uses for their webhooks
        $ipRanges = json_decode($data, true)['hooks'];

        // Check if ip from request is from GitHub
        foreach ($ipRanges as $range) {
            if ($this->cidrMatch($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    private function cidrMatch($ip, $range)
    {
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }
}
