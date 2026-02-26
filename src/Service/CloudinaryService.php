<?php

namespace App\Service;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct(string $cloudinaryUrl)
    {
        // Require the URL to be present (it should be configured in .env / Railway)
        $this->cloudinary = new Cloudinary(Configuration::instance($cloudinaryUrl));
    }

    /**
     * Upload an image to Cloudinary and return the secure public URL
     */
    public function uploadImage(UploadedFile $file, string $folder = 'outfitshare'): string
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getPathname(),
        [
            'folder' => $folder
        ]
        );

        return $result['secure_url'];
    }
}