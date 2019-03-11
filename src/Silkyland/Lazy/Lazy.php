<?php

namespace Silkyland\Lazy;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Facade;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

/**
 * Class Lazy
 * @package Silkyland\Lazy
 */
$app = new Container();
$app->singleton('app', 'Illuminate\Container\Container');
Facade::setFacadeApplication($app);


class Lazy
{
    /**
     * @var
     */
    public $file;
    /**
     * @var int
     */
    public $baseWidth = 350;
    /**
     * @var null
     */
    public $baseHeight = null;
    /**
     * @var int
     */
    public $width = 250;
    /**
     * @var int
     */
    public $height = 250;
    /**
     * @var string
     */
    private $savePath;
    /**
     * @var
     */
    private $extension;
    /**
     * @var
     */
    private $randomNumber;
    /**
     * @var
     */
    public $filename;
    /**
     * @var
     */
    private $filePath;
    /**
     * @var
     */
    private $uploadedFile;
    /**
     * @var
     */
    private $fullPath;

    /**
     * ImageUpload constructor.
     * @param $file
     * @param string $savePath
     */
    public function __construct($file, $savePath = "/images")
    {
        $this->file = $file;
        $this->savePath = $savePath;
        if (function_exists('public_path')) {
            $this->filePath = public_path($this->savePath);
        } else {
            $this->filePath = __DIR__ . '/' . $savePath;
        }

        if (!File::exists($this->filePath)) {
            File::makeDirectory($this->filePath);
        }
        $this->generateNewName();
    }

    /**
     *
     */
    private function getExtension()
    {
        $this->extension = $this->file->getClientOriginalExtension();
    }

    /**
     *
     */
    private function getRandomNumber()
    {
        $this->randomNumber = sha1(Carbon::now() . microtime());
    }

    /**
     *
     */
    private function generateNewName()
    {
        $this->getExtension();
        $this->getRandomNumber();
        $this->fullPath = $this->filePath . '/' . $this->randomNumber . '.' . $this->extension;
        $this->filename = $this->savePath . '/' . $this->randomNumber . '.' . $this->extension;
    }

    /**
     *
     */
    public function upload()
    {
        $this->uploadedFile = Image::make($this->file);
    }

    /**
     * @param string $option = '' ? 'aspect'
     */
    public function resize($option = '')
    {
        $this->uploadedFile->resize($this->baseWidth, $this->baseHeight, function ($constraint) use ($option) {
            if ($option === 'aspect') {
                $constraint->aspectRatio();
            }
        });
    }

    /**
     *
     */
    public function crop()
    {
        $this->uploadedFile->crop($this->width, $this->height);
    }

    /**
     * @return mixed
     */
    public function save()
    {
        $this->uploadedFile->save($this->fullPath);
        return $this->filename;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $this->upload();
        $this->resize();
        $this->crop();
        return $this->save();
    }
}