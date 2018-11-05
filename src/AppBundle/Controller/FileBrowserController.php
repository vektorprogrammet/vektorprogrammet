<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileBrowserController extends BaseController
{
    /**
     * The controller that opens elFinder when the user presses the folde icon under "Min Vektor".
     *
     * @return Response
     */
    public function browseAction()
    {
        $response = $this->forward('FMElfinderBundle:ElFinder:show', array(
            'instance' => 'admin_access',
        ));

        return $response;
    }

    /**
     * Cam be used to stream a binary file to requesting user.
     * If a user requests a file that is on a path that routes to this controller, the file will be streamed
     * to user.
     *
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
    public function fileStreamAction(Request $request)
    {
        $prefix = substr($request->getPathInfo(), 1); //removes leading '/'
        //Had some trouble with paths. Differenet behaviours on different systems...
        $prefix = str_replace('%20', ' ', $prefix); //Must replace the %20 that blank space is replaced with in the request
        $prefix = str_replace('%5C', '%2F', $prefix); //Must replace the %5C that / is replaced with in the request (in some browsers only?)
        return new BinaryFileResponse($prefix);
    }

    /**
     * This controller renders and returns a page with links to all the files in the public folder (the same folder
     * shown under "Offentlige filer" in the file browser. At delivery there isn't actually a link to this page
     * on the website. But both this controller and the twig template should be ready to use.
     *
     * @param $folder
     *
     * @return Response
     */
    public function showPublicFilesAction($folder)
    {
        //Read the public folder from paramters.yml
        $publicFolder = $this->container->getParameter('public_uploads');
        //Create the path that should be scanned
        if ($folder == 'all') {
            $path = $publicFolder;
        } else {
            $path = $publicFolder.'/'.$folder;
        }
        //Scan the folder for files, exclude hidden files (those that starts with dot)
        $files = preg_grep('/^([^.])/', scandir($path));
        //Prepend the path to each file &=call by reference
        foreach ($files as &$file) {
            $file = $path.'/'.$file;
        }
        //Make an array without directories included.
        $filesWithoutFolders = array();
        foreach ($files as $file) {
            if (!is_dir($file)) {
                $filesWithoutFolders[] = $file;
            }
        }
        //Render the twig
        return $this->render('file_system/public_files.html.twig', array('files' => $filesWithoutFolders));
    }
}
