<?php

namespace App\Support\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Support\Infrastructure\LogService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GitHubController extends BaseController
{
    private $repositoryName = 'vektorprogrammet/vektorprogrammet';

    public function __construct(
        private readonly LogService $logService,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/webhook/github', name: 'github_webook', methods: ['POST'])]
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
            $this->logService->info(
                "New commit on master by *$committer*:\n".
                "```$message```\n".
                'Deploying changes...'
            );
            shell_exec($this->getParameter('kernel.project_dir').'/deploy.sh');
            $this->logService->info('Deploy complete');

            return new JsonResponse(['status' => 'Deployed']);
        }

        return new JsonResponse(['status' => 'Not a master push to '.$this->repositoryName]);
    }

    private function ipIsFromGitHub($ip)
    {
        // Use curl to create a GET request to the GitHub api
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/meta');
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'User-Agent: NoBrowser v0.1 beta',
            ]
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
        [$subnet, $bits] = explode('/', (string) $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }
}
