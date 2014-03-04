<?php
/**
 * 
 */
namespace Application\Service;

/**
 * The image manager service. Responsible for getting the list of uploaded
 * files and resizing the images.
 */
class ImageManager {
    
    /**
     * The directory where uploaded files are stored.
     * @var string
     */
    private $uploadDir = './data/upload/';
        
    /**
     * Returns the array of uploaded file names.
     * @return array List of uploaded file names.
     */
    public function getUploadedFiles() {
        
        // The directory where we plan to store uploaded files.
        
        // Check whether the directory already exists, and if not,
        // create the directory.
        if(!is_dir($this->uploadDir)) {
            if(!mkdir($this->uploadDir)) {
                throw new \Exception('Could not create directory for uploads: '. error_get_last());
            }
        }
        
        // Scan the directory and create the list of uploaded files.
        $files = array();        
        $handle  = opendir($this->uploadDir);
        while (false !== ($entry = readdir($handle))) {
            
            if($entry=='.' || $entry=='..')
                continue; // Skip current dir and parent dir.
            
            $files[] = $entry;
        }
        
        // Return the list of uploaded files.
        return $files;
    }
    
    /**
     * Resizes the image, keeping its aspect ratio.
     * @param string $fileName
     * @param int $desiredWidth
     * @return string Resulting file name.
     */
    public  function resizeImage($fileName, $desiredWidth = 240) {
        
        // Get original image dimensions.
        list($originalWidth, $originalHeight) = getimagesize($fileName);

        // Calculate aspect ratio
        $aspectRatio = $originalWidth/$originalHeight;
        // Calculate the resulting height
        $desiredHeight = $desiredWidth/$aspectRatio;

        // Resize the image
        $resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
        $originalImage = imagecreatefromjpeg($fileName);
        imagecopyresampled($resultingImage, $originalImage, 0, 0, 0, 0, 
                $desiredWidth, $desiredHeight, $originalWidth, $originalHeight);

        // Save the resized image to temporary location
        $tmpFileName = tempnam("/tmp", "FOO");
        imagejpeg($resultingImage, $tmpFileName, 80);
        
        // Return the path to resulting image.
        return $tmpFileName;
    }
}
