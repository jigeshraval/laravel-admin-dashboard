<?php

namespace App\Objects;

use Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\File;
use App\Objects\MediaImageSize;

class Media extends IDB
{
    protected $table = 'media';

    private $size1 = null;

    private $size2 = null;

    private $storage = null;

    private $disk = null;

    private $not_found_image = 1;

    public $force_resize = false;

    public $force_webp_resize = false;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    public function saveImageSize()
    {
        if (!$this->id) {
            return;
        }

        $mis = new MediaImageSize;
        $size_folder = trim($this->size1) . trim($this->size2);

        $size_db = $mis->where('id_media', $this->id)
        ->where('size', $size_folder)
        ->first();

        if ($size_db) {
            return;
        }

        $mis->id_media = $this->id;
        $mis->size = $size_folder;
        $mis->save();
    }

    public function saveWebpImageSize()
    {
        if (!$this->id) {
            return;
        }

        $mis = new MediaImageSize;
        $size_folder = trim($this->size1) . trim($this->size2);

        $size_db = $mis->where('id_media', $this->id)
        ->where('size', $size_folder)
        ->where('webp', 1)
        ->first();

        if ($size_db) {
            return;
        }

        $mis->id_media = $this->id;
        $mis->size = $size_folder;
        $mis->webp = 1;
        $mis->save();
    }

    public function getOriginalImageURL()
    {
        if (!$this->path) {
            //Log here
        }

        $disk = $this->disk;
        $storage = Storage::disk($disk);

        if (!$this->webp_path) {
            $this->generateWebpImage();
        }

        return [
            'webp' => $storage->url($this->webp_path),
            'jpg' => $storage->url($this->path)
        ];
    }

    public function getStorageFolder($webp = false)
    {
        $type = $this->type;
        $folder = '';

        // if (config('filesystems.cloud') == 'local') {
        //     $folder .= 'app/';
        // }

        if (!$webp) {

          $folder = 'public/image/jpg';

        } else {

          $folder = 'public/image/webp';

        }

        if ($this->size1 && $this->size2) {
            $folder .= '/' . trim($this->size1) . trim($this->size2);
        }

        return $folder . '/';
    }

    // This function is temporary to move files f
    public function getOldStorageFolder()
    {
        $type = $this->type;
        $folder = '';

        // if (config('filesystems.cloud') == 'local') {
        //     $folder .= 'app/';
        // }

        $folder = 'public/image';

        if ($this->size1 && $this->size2) {
            $folder .= '/' . trim($this->size1) . trim($this->size2);
        }

        return $folder . '/';
    }

    public function makeWebpImage($image_path = null, $max_size)
    {
        $pathinfo = pathinfo($this->path);
        $ext = $pathinfo['extension'];
        $filename = $pathinfo['filename'];
        $new_name = $filename . '.webp';

        if ($ext == 'webp') {
          return;
        }

        $this->moveImageToLocalStorage();

        $storage = Storage::disk('local');

        $localPath = storage_path('app/public/image/' . $new_name);
        $bytes = $storage->size($this->path);

        $image_path = $storage->path($this->path);

        if ($bytes) {

          $size_in_kb = ceil($bytes/1000);

          if ($size_in_kb > $max_size) {

            //Optimizing image for its size and replacing at its destination
            $this->optimizeImage($image_path, false);

          }

        }

        $image_type = image_type_to_mime_type(exif_imagetype($image_path));

        if ($image_type == 'image/png') {
          $img1 = imagecreatefrompng($image_path);
        }

        if ($image_type == 'image/jpg' || $image_type == 'image/jpeg') {
          $img1 = imagecreatefromjpeg($image_path);
          imagepalettetotruecolor($img1);
        }

        if ($image_type == 'image/gif') {
          $img1 = imagecreatefromgif($image_path);
        }

        if (
          $image_type == 'image/png' ||
          $image_type == 'image/jpeg' ||
          $image_type == 'image/jpg' ||
          $image_type == 'image/gif'
        ) {
            imagepalettetotruecolor($img1);
            $create = imagewebp($img1, $localPath, 100);
            imagedestroy($img1);

            $nameinfo = pathinfo($this->name);
            $new_real_name = $pathinfo['filename'] . '.webp';

            $this->name = $new_real_name;
            $this->realname = $new_real_name;
            $this->path = 'image/' . $new_name;
            $this->format = 'webp';
            $this->save();
            $this->localStorageToS3Storage();
        }

    }

    public function retrieve($size1 = null, $size2 = null, $webp = false)
    {
        if ($this->type == 'image') {
            if (!$size1 || !$size2) {
                return $this->getOriginalImageURL();
            }

            return $this->resizeImage($size1, $size2, $webp);
        }

        if ($this->format == 'embeded') {
            return $this->name;
        }

        $disk = $this->disk;
        $url = Storage::disk($disk)->url($this->path);
        return $url;
    }

    public function getSizeFolder()
    {
        if (!$this->size1 || !$this->size2) {
            return;
        }

        return $this->size1 . $this->size2;
    }

    /*
    |
    |   Variable $filePath is the key variable which will be used to define the path
    |
    */

    public function resizeImage($size1, $size2, $webp = false)
    {
        $this->size1 = $size1;
        $this->size2 = $size2;

        //Cleaning name
        $this->cleanName($this->name);

        if ($webp) {

          return [
            'jpg' => $this->resizeOtherImage(),
            'webp' => $this->resizeWebpImage()
          ];

        } else {

          return $this->resizeOtherImage();

        }
    }

    protected function getFileNameWithoutExtension($file)
    {
        $name = pathinfo($file);
        return $name['filename'];
    }

    protected function getFileExtension($file)
    {
        $name = pathinfo($file);
        return $name['extension'];
    }


    protected function resizeWebpImage()
    {
        $disk = $this->disk;
        $storage = Storage::disk($disk);

        $resizeNotRequireFormats = [
            'svg+xml',
            'svg',
            'xml'
        ];

        // If unsupported format is there, then it should not resize
        if (in_array($this->format, $resizeNotRequireFormats)) {

            return $storage->url($this->path);

        }

        $folder = $this->getStorageFolder(true);

        //File path means folder + original path (i.e. 250250/filename.jpg) & $this->path is the real path
        $filePath = $folder . $this->getFileNameWithoutExtension($this->name) . '.webp';

        // If file exists then return it
        if ($this->sizeTableHasTheWebpImage()) {
          // if ($storage->exists($filePath)) {

          // $this->moveImage($filePath);

          $url = $storage->url($filePath);

          return $url;

        }

        if (!$this->webp_path) {
            $this->generateWebpImage();
        }

        //Image content used to resize image through intervention
        $storedImage = $storage->get($this->webp_path);

        if (!$storedImage) {

            $mesage = 'Content missing for Image (Webp) ID - ' . $this->id;

            triggerException($message, E_USER_WARNING);

            return '';
        }

        // Since we have to resize from the webp_format, we have to move file to the local storage to create binary data from the given path

        // Moving image to local storage
        $this->moveWebpImageToLocalStorage();

        // getting path of the moved image
        $local_path = Storage::disk('local')->path($this->webp_path);

        // Binary data of webp image
        $storedImage = imagecreatefromwebp($local_path);

        // Since the image processing is done, we should delete it to clear space
        Storage::disk('local')->delete($this->webp_path);

        //Making it ready for resizing with Intervention
        $img = Image::make($storedImage);
        $img->resize($this->size1, $this->size2, function ($constraint) {
            $constraint->aspectRatio();
        });

        // We need a path where resized image can be stored
        if ($this->disk == 's3') {

            $file = storage_path('app/tmp/webp/' . $this->getFileNameWithoutExtension($this->name) . '.webp'); //tmp path

        } else {

            $localStorage = Storage::disk('local');
            $localPath = $localStorage->path('');
            $localFolder = $localPath . $folder;

            if (!file_exists($localFolder)) {

                $localStorage->makeDirectory($folder);

            }

            $file = storage_path('app/' . $filePath);

        }

        //Finally saving a file
        $img->save($file);

        //Now move local file to s3 bucket
        if ($this->disk == 's3') {

            $fetchTmpFile = Storage::disk('local')->get('tmp/webp/' . $this->getFileNameWithoutExtension($this->name) . '.webp');

            //Moving local file to s3 bucket after resizing
            $storage->put($filePath, $fetchTmpFile, 'public');

            //delete local file
            Storage::disk('local')->delete('tmp/webp/' . $this->getFileNameWithoutExtension($this->name) . '.webp');

        }

        $this->saveWebpImageSize();
        return $storage->url($filePath);
    }

    protected function moveImage($newPath)
    {
        $storage = Storage::disk($this->disk);
        $filePath = $this->getOldStorageFolder() . $this->name;

        if ($storage->exists($filePath) && !$storage->exists($newPath)) {

            $storage->move($filePath, $newPath);

        }

    }

    protected function resizeOtherImage()
    {
        $disk = $this->disk;
        $storage = Storage::disk($disk);

        $resizeNotRequireFormats = [
            'svg+xml',
            'svg',
            'xml'
        ];

        // If unsupported format is there, then it should not resize
        if (in_array($this->format, $resizeNotRequireFormats)) {

            return $storage->url($this->path);

        }

        $folder = $this->getStorageFolder();

        //File path means folder + original path (i.e. 250250/filename.jpg) & $this->path is the real path
        $filePath = $folder . $this->name;

        // If file exists then return it
        if ($this->sizeTableHasTheImage()) {

          // if ($storage->exists($filePath)) {

          // $this->moveImage($filePath);

          $url = $storage->url($filePath);
          return $url;

        }

        //Image content used to resize image through intervention
        if ($storage->exists($this->path)) {

            $mime =  $storage->mimeType($this->path);

            if (!in_array($mime, [
              'image/jpeg',
              'image/png',
              'image/bmp',
              'image/gd2',
              'image/xbm',
              'image/xpm',
              'image/webp'
            ])) {

              return notFoundImage();

            }

            $storedImage = $storage->get($this->path);

        } else {

            $message = 'Content missing for Image ID - ' . $this->id;

            // triggerException($message, E_USER_WARNING);

            return notFoundImage();

        }

        //Making it ready for resizing with Intervention
        $img = Image::make($storedImage);
        $img->resize($this->size1, $this->size2, function ($constraint) {
            $constraint->aspectRatio();
        });

        // We need a path where resized image can be stored
        if ($this->disk == 's3') {

            $file = storage_path('app/tmp/' . $this->name); //tmp path

        } else {

            $localStorage = Storage::disk('local');
            $localPath = $localStorage->path('');
            $localFolder = $localPath . $folder;

            if (!file_exists($localFolder)) {

                $localStorage->makeDirectory($folder);

            }

            $file = storage_path('app/' . $filePath);

        }

        //Finally saving a file
        $img->save($file);

        //Now move local file to s3 bucket
        if ($this->disk == 's3') {

            $fetchTmpFile = Storage::disk('local')->get('tmp/' . $this->name);

            //Moving local file to s3 bucket after resizing
            $storage->put($filePath, $fetchTmpFile, 'public');

            //delete local file
            Storage::disk('local')->delete('tmp/' . $this->name);

        }

        $this->saveImageSize();
        return $storage->url($filePath);
    }

    public function moveImageToLocalStorage()
    {
        //Check if file exists locally
        $exists = Storage::disk('local')->exists($this->path);

        if (!$exists) {

            if (!Storage::disk('s3')->exists($this->path)) {
              pre($this);
            }

            //Fetch content from the s3 bucket
            $cloud_file = Storage::disk('s3')->get($this->path);

            //storing the file locally
            $test = Storage::disk('local')->put($this->path, $cloud_file, 'public');
        }
    }

    protected function moveWebpImageToLocalStorage()
    {
        //Check if file exists locally
        $exists = Storage::disk('local')->exists($this->webp_path);

        if (!$exists) {
            //Fetch content from the s3 bucket
            $cloud_file = Storage::disk('s3')->get($this->webp_path);

            //storing the file locally
            $test = Storage::disk('local')->put($this->webp_path, $cloud_file, 'public');
        }
    }

    public function compress($source, $destination, $quality = 90)
    {
            $info = getimagesize($source);

            if ($info['mime'] == 'image/jpeg') {
                $image = imagecreatefromjpeg($source);
            }

            elseif ($info['mime'] == 'image/gif') {
                $image = imagecreatefromgif($source);
            }

            elseif ($info['mime'] == 'image/png') {
                $image = imagecreatefrompng($source);
            }

            imagejpeg($image, $destination, $quality);
            imagedestroy($image);

            return $destination;
    }

    public function optimizeImage($src_path = null, $sync_image = false)
    {
        if (!$this->id) {
            return;
        }

        if ($this->disk == 's3' && $sync_image) {
            $this->moveImageToLocalStorage();
        }

        if (!$src_path) {

          $src = $src_path;
          $dest = $src_path;

        } else {

          $src = storage_path('app/' . $this->path);
          $dest = storage_path('app/' . $this->path);

        }


        $this->compress($src, $dest, 90);

        $this->localStorageToS3Storage();

        \Log::info('Image optimized - ' . $this->id);
    }

    public function localStorageToS3Storage($delete = true)
    {
        //Fetching local file
        $locallyStoredFile = Storage::disk('local')->get($this->path);

        //Putting local file into s3 bucket
        Storage::disk('s3')->put($this->path, $locallyStoredFile, 'public');

        if ($delete) {
            //Deleting local file
            Storage::disk('local')->delete($this->path);
        }

        return Storage::disk('s3')->url($this->path);
    }

    public function moveLocalWebpImagetoS3Storage($delete = true)
    {
        if (!$this->webp_path) {
          return;
        }

        //Fetching local file
        $locallyStoredFile = Storage::disk('local')->get($this->webp_path);

        //Putting local file into s3 bucket
        $putted = Storage::disk('s3')->put($this->webp_path, $locallyStoredFile, 'public');

        if ($delete) {
            //Deleting local file
            Storage::disk('local')->delete($this->webp_path);
        }
    }

    public function sizeTableHasTheImage()
    {
        if (
            !$this->size1 ||
            !$this->size2 ||
            !$this->id ||
            $this->force_resize
        ) {
            return false; //Log
        }

        $size = $this->size1 . $this->size2;
        return \App\Objects\MediaImageSize::where('size', $size)
        ->where('id_media', $this->id)
        ->first();
    }

    public function sizeTableHasTheWebpImage()
    {
        if (
            !$this->size1 ||
            !$this->size2 ||
            !$this->id ||
            $this->force_webp_resize
        ) {
            return false; //Log
        }

        $size = $this->size1 . $this->size2;

        return \App\Objects\MediaImageSize::where('size', $size)
        ->where('webp', 1)
        ->where('id_media', $this->id)
        ->first();
    }

    private function storeMedia($file, $folder, $disk, $type)
    {
        if ($type == 'image') {

            $size = ceil($file->getSize()/1000);

            if ($size >= 200) {

                $tmpStoredFilePath = $file->store($folder, 'local');

                $source = Storage::disk('local')->path($tmpStoredFilePath);
                $destination = $source;

                $this->compress($source, $destination);

                if (config('filesystems.default') == 's3') {
                    
                    $tmp_file = new File(Storage::disk('local')->path($tmpStoredFilePath));
    
                    $path = Storage::disk('s3')->putFile($folder, $tmp_file, 'public');
    
                    Storage::disk('local')->delete($tmpStoredFilePath);

                } else {

                    $path = $tmpStoredFilePath;

                }

                return $path;
            }

        }

        return $file->storePublicly($folder, $disk);
    }

    public function store($file)
    {
        $disk = $this->disk;
        $name = $file->getClientOriginalName();
        $format = $file->getClientMimeType();
        $type = explode('/', $format)[0];

        if ($type == 'image' && $format != 'image/webp') {

            $folder = 'public/' . $type . '/jpg';

        } else {

            $folder = 'public/' . $format;

        }

        //Storing file
        $storedFilePath = $this->storeMedia(
            $file, 
            $folder, 
            $disk, 
            $type
        );

        $error = $file->getError();

        if ($error) {
            return array(
                'status' => 'error',
                'message' => $error
            );
        }

        if (!$storedFilePath) {
            return array(
                'status' => 'error',
                'message' => 'File ' . $name . ' could not be uploaded'
            );
        }

        $url = Storage::disk($disk)->url($storedFilePath);

        $this->name = $name;
        $this->path = $storedFilePath;

        if ($format == 'image/webp') {

            $this->webp_path = $storedFilePath;

        }

        $this->type = $type;
        $this->format = $format;
        $this->save();
        $this->name = $this->id . '-' . $this->name;
        $this->save();

        if ($type == 'image') {

            $this->imageMirroring();
            $this->generateCommonSizes();

        }

        return array(
            'url' => $url,
            'id' => $this->id
        );
    }

    private function generateCommonSizes()
    {
        $this->retrieve(250, 250);
    }

    private function imageMirroring()
    {
        if (config('filesystems.default') != 's3') {
            return;
        }

        if ($this->format == 'webp' || $this->format == 'image/webp') {

            $this->generateJpgImage();

        } else {

            $this->generateWebpImage();

        }
    }

    private function storage()
    {
        return Storage::disk($this->disk);
    }

    public function cleanName($name)
    {
        $pathinfo = pathinfo($name);
        $ext = $pathinfo['extension'];
        $filename = preg_replace('/[^\w\-]/', '', $pathinfo['filename']);
        $new_name = $filename . '.' . $ext;

        if ($new_name != $name) {
            $this->name = $new_name;
            $this->save();

            //Delete sizes
            $mis = MediaImageSize::where('id_media', $this->id)
            ->delete();
        }
    }

    public function adjustPathForWebp()
    {
        $this->moveImageToLocalStorage();

        $image = storage_path('app/' . $this->path);

        $pathinfo = pathinfo($this->path);
        $ext = $pathinfo['extension'];
        $filename = $pathinfo['filename'];

        $local_jpg_path = storage_path('app/public/image/jpg/' . $filename . '.jpg');
        $local_webp_path = storage_path('app/public/image/webp/' . $filename . '.webp');

        $format =  image_type_to_mime_type(exif_imagetype($image));

        $type_ex = explode('/' , $format);

        $type_of_image = end($type_ex);

        if ($format == 'image/webp') {

            $image = imagecreatefromwebp($image);
            imagejpeg($image, $local_jpg_path, 100);
            imagedestroy($image);
            $this->path = 'image/jpg/' . $filename . '.jpg';
            $this->save();
            $this->webp_path = 'image/webp/' . $filename . '.webp';

        } else {

            if (!in_array($type_of_image, [
              'jpeg',
              'png',
              'bmp',
              'gd2',
              'xbm',
              'xpm'
            ])) {

              return;

            }

            $fun = 'imagecreatefrom' . $type_of_image;
            $image = $fun($image);
            imagewebp($image, $local_webp_path, 100);

            $this->webp_path = 'image/webp/' . $filename . '.webp';
            $this->save();
            pre($this->id, false);
            $this->moveLocalWebpImagetoS3Storage();

        }

        $this->localStorageToS3Storage();
    }

    /*
    |
    | This function was created to remove webp extension from the name column which was added  | previously by mistake
    |
    */
    public function adjustExtension()
    {
        $filename = $this->getFileNameWithoutExtension($this->name);
        $finalName = $filename . '.jpg';
        $this->name = $finalName;
        $this->format = 'jpg';
        $this->save();
        pre($this->id, false);
    }

    protected function generateWebpImage()
    {
        //Moving image to local storage
        $this->moveImageToLocalStorage();

        // Local storage path
        $image = Storage::disk('local')->path($this->path);

        // Fetching Format from the above path
        $format =  image_type_to_mime_type(exif_imagetype($image));

        // Exploding the format
        $type_ex = explode('/' , $format);

        // Taking the last key from the array to determin the image type
        $type_of_image = end($type_ex);

        // getting file name without extension to save image with extension webp
        $filename = $this->getFileNameWithoutExtension($this->name);
        $local_webp_path = storage_path('app/public/image/webp/' . $filename . '.webp');

        if (!in_array($type_of_image, [
          'jpeg',
          'png',
          'bmp',
          'gd2',
          'xbm',
          'xpm'
        ])) {

          return;

        }

        // As per the image type coverting it into the webp image without loosing quality
        $fun = 'imagecreatefrom' . $type_of_image;
        $image = $fun($image);
        imagepalettetotruecolor($image);
        imagewebp($image, $local_webp_path, 95);
        imagedestroy($image);

        // Saving webp path
        $this->webp_path = 'image/webp/' . $filename . '.webp';
        $this->save();

        // Finally moving file to the s3 storage
        $this->moveLocalWebpImagetoS3Storage();
    }

    protected function generateJpgImage()
    {
        //Moving image to local storage
        $this->moveImageToLocalStorage();

        // Local storage path
        $image = Storage::disk('local')->path($this->path);

        // getting file name without extension to save image with extension webp
        $filename = $this->getFileNameWithoutExtension($this->name) . '.jpg';
        $local_jpg_path = storage_path('app/public/image/jpg/' . $filename);
        // As per the image type coverting it into the webp image without loosing quality
        $image = imagecreatefromwebp($image);
        imagepalettetotruecolor($image);
        imagejpeg($image, $local_jpg_path, 100);
        imagedestroy($image);

        // Saving webp path
        $this->path = 'image/jpg/' . $filename;
        $this->save();

        // Finally moving file to the s3 storage
        $this->localStorageToS3Storage();
    }

    public function remove($with_sizes = true)
    {
        $storage = Storage::disk($this->disk);

        if ($with_sizes && $this->type == 'image') {

            $sizes = MediaImageSize::where('id_media', $this->id)
            ->get();

            foreach ($sizes as $size) {

                $path1 = 'image/jpg/' . $size->size . '/' . $this->name;

                if ($storage->exists($path1)) {
                    // $storage->delete($path1);
                }

                if ($size->webp) {

                    $path2 = 'image/webp/' . $size->size . '/' . $this->getFileNameWithoutExtension($this->name) . '.webp';

                    if ($storage->exists($path2)) {
                        $storage->delete($path2);
                    }

                }

                // Delete from the table
                $size->delete();

            }

            // Forcing the object to be deleted from the table
            $this->forceDelete();

        }
    }

    public function githubTest()
    {
        return 'test';
    }

    public function fixNaming()
    {
        $this->moveImageToLocalStorage();

        $filename = \Str::slug($this->getFileNameWithoutExtension($this->name));
        $ext = $this->getFileExtension($this->name);

        $new_name = $filename . '.' . $ext;

        $path_e = explode('/', $this->path);
        $last_key = array_key_last($path_e);
        if (isset($path_e[$last_key])) {

            $path_e[$last_key] = $new_name;
            $new_path = implode('/', $path_e);

            //Renaming to new path
            Storage::disk('local')->move($this->path, $new_path);

            $this->name = $new_name;
            $this->path = $new_path;
            $this->save();

            $this->localStorageToS3Storage();
        }
    }

    public function fixWebpNaming()
    {
        if (!$this->webp_path) {
          return;
        }

        $this->moveWebpImageToLocalStorage();

        $filename = \Str::slug($this->getFileNameWithoutExtension($this->name));
        $ext = $this->getFileExtension($this->name);

        $new_name = $filename . '.' . $ext;

        $path_e = explode('/', $this->webp_path);
        $last_key = array_key_last($path_e);

        if (isset($path_e[$last_key])) {

            $path_e[$last_key] = $new_name;
            $new_path = implode('/', $path_e);

            //Renaming to new path
            // Storage::disk('local')->move($this->webp_path, $new_path);

            $this->name = $new_name;
            $this->webp_path = $new_path;
            $this->save();

            $this->localStorageToS3Storage();

            pre(Storage::disk('s3')->url($this->webp_path));
        }
    }
}
