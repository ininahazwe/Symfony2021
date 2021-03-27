<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private SluggerInterface $slugger;
    private string $uploadsDirectory;
    public function __construct(SluggerInterface $slugger, string $uploadsDirectory)
    {
        $this->slugger = $slugger;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * upload a file and return it's filename and filepath
     *
     * @param UploadedFile $file The uplaod file.
     * @return array{fileName: string, filePath: string}
     */
    public function upload(UploadedFile $file): array
    {
        $filename = $this->generateUniqueFilename($file);
        try {
            $file->move($this->uploadsDirectory, $filename);
        } catch (FileException $fileException){
            throw $fileException;
        }

        return [
          'fileName' => $filename,
          'filePath' => $this->uploadsDirectory . $filename
        ];
    }

    /**
     * Generate a unique filename for the uploaded file.
     *
     * @param UploadedFile $file The uploaded file.
     * @return string The Unique filename slugged
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originalFileNameSlugged = $this->slugger->slug(strtolower($originalFileName));
        $randomID = uniqid();
        return "{$originalFileNameSlugged}-{$randomID}.{$file->guessExtension()}";
    }
}