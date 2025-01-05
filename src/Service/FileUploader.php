<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;


    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;

    }

    public function upload(UploadedFile $file)
    {

        $fileName = sha1(uniqid(mt_rand(), true)).'.'.$file->guessExtension();
        
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}

?>