<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 08.04.2015
 * Time: 08:07
 *
 * Script must be run after composer installs studio-42/elfinder.
 * ElFinder is not a bundle with the normal Resources/public structure, this script copies the scripts and stylesheets
 * to web folder, updates some paths, does some renaming.
 *
 * DENNE ER IKKE I BRUK NÅ
 *
 */

//Some constants
define("TWIG_FOLDERS", "app/Resources/views");
define("WEB_ASSETS_FOLDER", "bundles");

//ElFinder constants
define("ELFINDER_INSTALLATION_FOLDER", "vendor/studio-42/elfinder");
define("ELFINDER_TWIG_SUBFOLDER", "elfinder");
define("ELFINDER_TWIG_FILE", "elfinder.html.twig");


//Check if WEB_ASSETS_FOLDER exists. If not exit, this script is not meant to do anything else than configure elfinder.
//It assumes everything else is in place
if (!file_exists("web/" . WEB_ASSETS_FOLDER)){
   exit("FilemanagerConfigurator.php: Could not find WEB_ASSETS_FOLDER. Exiting script.");
}

//Check if target twig folder exists, if not create the folder.
$targetTwigFolder = TWIG_FOLDERS.'/'.ELFINDER_TWIG_SUBFOLDER;
if (!file_exists($targetTwigFolder)){
    mkdir($targetTwigFolder);
}
//Copy the html file in elFinder's installation folder to a twig file in $targetTwigFolder
copy(ELFINDER_INSTALLATION_FOLDER.'/'."elfinder.html", $targetTwigFolder.'/'."elfinder.html.twig");
//Fix the paths in elfinder.html.twig
fix_paths($targetTwigFolder.'/'."elfinder.html.twig", WEB_ASSETS_FOLDER.'/elfinder');


//Recursively copy the entire folder structure of elfinder, copying only js, css and image files
$targetElfinderAssetFolder = 'web/'.WEB_ASSETS_FOLDER .'/elfinder';
copy_assets(ELFINDER_INSTALLATION_FOLDER, $targetElfinderAssetFolder, array('gif', 'js', 'css', 'jpg', 'jpeg', 'png'));



function fix_paths($file, $prependString){
    //The regex to search for in $file
    $pattern_to_search_for = "/[\"](css[^\"]*|js[^\"]*)/";
    //The replacement string
    $replacement = "\"{{ asset('" . $prependString . "/$1') }}";
    //Read current content of file
    $file_content = file_get_contents($file);
    //$file_content = str_replace("./", "", $file_content);
    //Replace the matched strings with replacement pattern
    $file_content = preg_replace($pattern_to_search_for, $replacement, $file_content);
    //Put the new content into the file
    file_put_contents( $file, $file_content);
}



/*
 * Copies all js, css and image files and recreates the folder structure at target
 */
function copy_assets($source, $target, $file_types){
    echo "copy_asset(".$source.", ".$target.", ".$file_types.")";
    foreach(scandir($source) as $file) {
        //echo "loop:" . $file . "\n";
        if ($file == '.'  ||  $file == '..'){
            continue;
        }
        if (!is_dir($source."/".$file)) {
            echo "!is_dir: " . $file . "\n";
            $file_info = pathinfo($file);
            if (array_key_exists('extension', $file_info) &&
                in_array($file_info['extension'], $file_types)){
                //Copy the file
                echo "trying to copy " . $file_info['filename'] . " to " . $target . "/" . $file_info['filename'] . "\n";
                copy($source . "/" . $file, $target.'/'.$file);
                //If it is css or js, fix paths
                if ($file_info['extension']=='js' || $file_info['extension']=='css'){
                    //fix_paths($target.'/'.$file, false);
                }
            }
        } else {
            echo "is_dir\n";
            //TODO: count files first , check > 0
            $next_source = $source ."/" . $file;
            $count = file_count($next_source, $file_types);
            if ($count == 0){
               //echo "counter is 0 for " . $next_source . "\n";
               continue;
            }
            $next_target = $target ."/" . $file;
            mkdir($next_target);
            copy_assets($next_source,$next_target, $file_types);
        }

    }
}

/*
 * Function that count number of files in a folder and subfolder that has one of specified extensions
 */
function file_count($source, $file_types){
    $counter = 0;
    foreach(scandir($source) as $file) {
        if ($file == '.'  ||  $file == '..'){
            continue;
        }
        if (!is_dir($source."/".$file)) {
            $file_info = pathinfo($file);
            if (array_key_exists('extension', $file_info) &&
                in_array($file_info['extension'], $file_types)){
                $counter++;
            }
        } else {
            $source = $source ."/" . $file;
            $counter += file_count($source, $file_types);
        }
    }
    return $counter;
}


/*
 * Php delete function that deals with directories recursively
 * Copy/pasted from: http://www.paulund.co.uk/php-delete-directory-and-files-in-directory
 */
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        foreach( $files as $file ){
            delete_files( $file );
        }
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );
    }
}