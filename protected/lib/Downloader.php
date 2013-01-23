<?php
class Downloader {
        static public $mode = 'internal';
        static public $method = 'curl' ;
        static public $maxMultiDownload = 3;
        static public $referer ;
        static function curlDownload($uri, $filepath, $option = array() ){
                $curlRes = curl_init( $uri );
                if( !file_exists( dirname( $filepath ) ) ){
                        mkdir( dirname( $filepath ) );
                }
                $fileRes = fopen( $filepath, 'w+');
                curl_setopt($curlRes, CURLOPT_FILE, $fileRes);
                curl_setopt($curlRes, CURLOPT_REFERER, self::$referer);
                curl_exec( $curlRes );
                curl_close( $curlRes );
                fclose( $fileRes );
        }
        
        static function curlDownloadAll( $imgs, $referer = null){
                if( $referer ){
                        self::$referer = $referer;
                }
                $fileHandles = array();
                $multiCurlHandle = curl_multi_init();
                $curlHandles = array();
                foreach( $imgs as $url => $path ){
                        if( !file_exists( dirname( $path ) ) ){
                                mkdir( dirname( $path ) );
                        
                        }
                        $path = trim(urldecode($path));
                        //var_dump($path);die;
                        $fp = fopen($path, 'w+');
                        $fileHandles[] = $fp;
                        $curlHandles[] = $curlHandle = curl_init( $url );
                        curl_setopt_array($curlHandle, 
                                                          array(CURLOPT_FILE => $fp, 
                                                                        CURLOPT_REFERER => self::$referer ));
                        curl_multi_add_handle($multiCurlHandle, $curlHandle);
                }
                $active = null;
                do{
                        curl_multi_exec($multiCurlHandle, $active);
                        usleep( 100 );
                } while ( $active > 0 );
                foreach( $curlHandles as $curlHandle ){
                        curl_multi_remove_handle($multiCurlHandle, $curlHandle);
                }
                foreach( $fileHandles as $fileHandle ){
                        fclose( $fileHandle );
                }
                curl_multi_close( $multiCurlHandle );
            }
}
                        
