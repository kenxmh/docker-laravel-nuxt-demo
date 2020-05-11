<?php
namespace App\Services;

use OSS\OssClient;
use OSS\Core\OssException;

use OSS\Http\RequestCore;
use OSS\Http\ResponseCore;

class ImageService {

    private static $oss = null;
    protected $bucket;

    private static $UploadCategory = [
        "ARTICLE_IMAGE" => ['article/%s.png', null],
    ];

    // protected $CityURLArray = [
    //     '杭州' => 'oss-cn-hangzhou',
    //     '上海' => 'oss-cn-shanghai',
    //     '青岛' => 'oss-cn-qingdao',
    //     '北京' => 'oss-cn-beijing',
    //     '张家口' => 'oss-cn-zhangjiakou',
    //     '深圳' => 'oss-cn-shenzhen',
    //     '香港' => 'oss-cn-hongkong',
    //     '硅谷' => 'oss-us-west-1',
    //     '弗吉尼亚' => 'oss-us-east-1',
    //     '新加坡' => 'oss-ap-southeast-1',
    //     '悉尼' => 'oss-ap-southeast-2',
    //     '日本' => 'oss-ap-northeast-1',
    //     '法兰克福' => 'oss-eu-central-1',
    //     '迪拜' => 'oss-me-east-1',
    // ];

    // 上传图片
    public static function uploadImage($key, $yourObjectName, $yourLocalFile) {
        $path = self::$UploadCategory[$key][0];
        if (!$path) {
            return false;
        }
        $yourObjectName = sprintf($path, $yourObjectName);
        return self::uploadFile($yourObjectName, $yourLocalFile);
    }

    // 获取无时效的对象链接
    public static function getUrl($key, $yourObjectName) {
        try {
            $oss = ImageService::getOss();

            $path = self::$UploadCategory[$key][0];
            if (!$path) {
                return false;
            }
            $yourObjectName = sprintf($path, $yourObjectName);

            $style = '';
            if (!empty(self::$uploadCategory[$key][1])) {
                $style = '?x-oss-process=' . $self::$uploadCategory[$key][1];
            }
            $unsignedUrl = sprintf("https://%s.oss-cn-shenzhen.aliyuncs.com/%s%s", env('ALIYUN_OSS_BUCKET'), $yourObjectName, $style);
            return $unsignedUrl;

        } catch (OssException $e) {
            return null;
        }
    }

    // 获取有时效的对象链接
    // private static function getSignUrl($object, $style) {

    //     $oss = OssService::getOss();
    //     if (empty($oss)) {
    //         return null;
    //     }
    //     if ($style) {
    //         $options = array(
    //             OssClient::OSS_PROCESS => $style
    //         );
    //     } else {
    //         $options = null;
    //     }
    //     return $oss -> signUrl(env('ALIYUN_OSS_BUCKET'), $object, self::OSS_ACCESS_LIFECYCLE, "GET", $options);

    // }

    // 通用上传对象
    private static function uploadFile($yourObjectName, $yourLocalFile){
        
        $result = null;
        try {
            $oss = ImageService::getOss();
            $result = $oss->uploadFile(env('ALIYUN_OSS_BUCKET'), $yourObjectName, $yourLocalFile);
        } catch (OssException $e){
            // var_dump($e->getMessage());
            $result = false;
        }
        return $result;
    }

    // 获得OSS实例
    private static function getOss() {
        $oss = ImageService::$oss;
        if (empty(ImageService::$oss)) {
            $oss = new OssClient(
                env('ALIYUN_OSS_ACCESS_KEY_ID'), 
                env('ALIYUN_OSS_ACCESS_KEY_SECRET'),
                env('ALIYUN_OSS_ENDPOINT')
            );
            ImageService::$oss = $oss;
        }
        return $oss;
    }

   

    



}