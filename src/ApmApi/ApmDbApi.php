<?php
namespace ApmApi;

class ApmDbApi {
    static function httpBuildQueryForCurl( $arrays, &$new = array(), $prefix = null ) {

        if ( is_object( $arrays ) ) {
            $arrays = get_object_vars( $arrays );
        }

        foreach ( $arrays AS $key => $value ) {
            $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
            if ( is_array( $value ) OR is_object( $value )  ) {
                self::httpBuildQueryForCurl( $value, $new, $k );
            } else {
                $new[$k] = $value;
            }
        }
    }

    static function getUrlContent($url, $postData = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        if (!(empty($postData))) {
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = ARRAY();
            self::httpBuildQueryForCurl($postData, $post);
            curl_setopt($ch, CURLOPT_POSTFIELDS, ($post));
        }
        $data       = curl_exec($ch);
        $httpcode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if ($httpcode >= 200 && $httpcode < 300) {
            curl_close($ch);
            return $data;
        } else {
            $curl_getinfo   = curl_getinfo($ch);
            $errorAr = [
                'error' => 1,
                'curl_getinfo' => $curl_getinfo
            ];
            curl_close($ch);
            return json_encode($errorAr);
        }

    }


    public static function _post($objectType, $objectGroup,  $arApi = array(), $debug5 = false)
    {
        global $cabinetAppAuth;
        #if ($debug5) {
            $start = microtime(true);
        #}
        if (substr($_SERVER['HTTP_HOST'], strrpos($_SERVER['HTTP_HOST'], '.')) == '.local') {
            $urlP            = 'http://' . $objectGroup . '.api.zerass.local/' . $objectType . '?';
        } else {
            $urlP            = 'http://' . $objectGroup . '.api.zerass.com/' . $objectType . '?';

        }
        $postData         = $cabinetAppAuth;
        $postData['args'] = $arApi;

        if (ISSET($_SESSION['user_access_token'])) {
            $postData['user_access_token'] = $_SESSION['user_access_token'];
        }
        $result = self::getUrlContent($urlP, $postData);

        $decodeResult = json_decode($result, true);
        if ($debug5 != -1) {
            if ($debug5 || (  (ISSET($decodeResult['error'])) && $decodeResult['error'] == 1)) {

                ?><pre>Timer: <b><?=sprintf('%.4F',  microtime(true) - $start)?></b> sec.
    <?=$objectGroup?> => <?=$objectType?>
    <hr>
    Posted data:
    <?=print_r($postData['args'], true);?>
    </pre>
                <h5>ANSWER:</h5><xmp style="background-color:orange;"><?=($decodeResult)?print_r($decodeResult):$result?></xmp><hr>
                <?php
                if (isset($decodeResult['error']) && $decodeResult['error'] == 1) {
                    $debug_backtrace = debug_backtrace();
                    echo '<pre>';
                    print_r($debug_backtrace);
                    echo '</pre>';
                }
            }
        }

        return $decodeResult;
    }
}