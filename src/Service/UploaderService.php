<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploaderService
{
  public function  __construct(private SluggerInterface $slugger) {}

/* ────────────────────────── UPLOAD PHOTO / FILE ──────────────────────────*/
  public function upload(UploadedFile $file, String $directory) : String
  {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        try {
            $file->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {
        }

        return $newFilename;
  }
}
