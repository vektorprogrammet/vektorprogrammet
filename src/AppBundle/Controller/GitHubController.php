<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GitHubController extends Controller
{
    public function deployAction(Request $request)
    {
        // Check if request is from GitHub
        $ipIsFromGitHub = $this->ipIsFromGitHub($request->getClientIp());
        if (!$ipIsFromGitHub) {
            die('Ip is not from GitHub');
        }

        // Get data
        $payload = json_decode($request->getContent(), true);
        $isMaster = array_key_exists('ref', $payload) && $payload['ref'] === 'refs/heads/master';

        // Execute deploy script if there is a push to master
        if ($isMaster) {
            shell_exec($this->getParameter('kernel.root_dir').'/../deploy.sh');

            return new JsonResponse(['status' => 'Deployed']);
        } else {
            return new JsonResponse(['status' => 'Not a master push']);
        }
    }

    public function ipIsFromGitHub($ip)
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
            if ($this->cidr_match($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    public function cidr_match($ip, $range)
    {
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }
}
