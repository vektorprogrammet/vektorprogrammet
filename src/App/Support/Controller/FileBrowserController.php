<?php

declare(strict_types=1);

namespace App\Support\Controller;

use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FileBrowserController extends BaseController
{
    public function __construct(
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * The controller that opens elFinder when the user presses the folde icon under "Min Vektor".
     *
     * @return Response
     */
    #[Route('/filebrowser', name: 'file_browser', methods: ['GET'])]
    public function browseAction()
    {
        $response = $this->forward('FMElfinderBundle:ElFinder:show', [
            'instance' => 'admin_access',
        ]);

        return $response;
    }

    /**
     * Cam be used to stream a binary file to requesting user.
     * If a user requests a file that is on a path that routes to this controller, the file will be streamed
     * to user.
     *
     * @return BinaryFileResponse
     */
    #[Route('/Offentlige filer/{file_path}', name: 'public_files', requirements: ['file_path' => '.+'], methods: ['GET'])]
    public function fileStreamAction(Request $request)
    {
        $prefix = substr($request->getPathInfo(), 1); // removes leading '/'
        // Had some trouble with paths. Differenet behaviours on different systems...
        $prefix = str_replace('%20', ' ', $prefix); // Must replace the %20 that blank space is replaced with in the request
        $prefix = str_replace('%5C', '%2F', $prefix); // Must replace the %5C that / is replaced with in the request (in some browsers only?)

        return new BinaryFileResponse($prefix);
    }

    /**
     * This controller renders and returns a page with links to all the files in the public folder (the same folder
     * shown under "Offentlige filer" in the file browser. At delivery there isn't actually a link to this page
     * on the website. But both this controller and the twig template should be ready to use.
     *
     * @return Response
     */
    public function showPublicFilesAction($folder)
    {
        // Read the public folder from paramters.yml
        $publicFolder = $this->getParameter('public_uploads');
        // Create the path that should be scanned
        if ($folder == 'all') {
            $path = $publicFolder;
        } else {
            $path = $publicFolder.'/'.$folder;
        }
        // Scan the folder for files, exclude hidden files (those that starts with dot)
        $files = preg_grep('/^([^.])/', scandir($path));
        // Prepend the path to each file &=call by reference
        foreach ($files as &$file) {
            $file = $path.'/'.$file;
        }
        // Make an array without directories included.
        $filesWithoutFolders = [];
        foreach ($files as $file) {
            if (!is_dir($file)) {
                $filesWithoutFolders[] = $file;
            }
        }

        // Render the twig
        return $this->render('file_system/public_files.html.twig', ['files' => $filesWithoutFolders]);
    }
}
