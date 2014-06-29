<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>amazon <span class="goog_qs-tidbit goog_qs-tidbit-1">Product Advertising API practice</title>
</head>
 
<body>
<h1>amazon Product Advertising API sample</h1></span>
<form action="" method="get">
<input type="text" name="keyword" size="35" maxlength="255" value="" />
<input type="submit" value="search" />
</form>
 
<?php
date_default_timezone_set('Asia/Tokyo');

function api($array)
{
    foreach($array as $key => $value) {
        if($key != 'secret_key' && $key != 'locale') {
            if(isset($params)) {
                $params .= sprintf('&%s=%s', $key, $value);
            } else {
                $params = sprintf('%s=%s', $key, $value);
            }
        }
    }
    $url = $array['locale'] . '?' . $params;
    $url_array = parse_url($url);
    parse_str($url_array['query'], $param_array);
    $param_array['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
    ksort($param_array);
    $str = sprintf("GET\n%s\n%s\n", $url_array['host'], $url_array['path']);
    $str_param = '';
    while(list($key, $value) = each($param_array))
        $str_param .= sprintf('%s=%s&', strtr($key, '_', '.'), rawurlencode($value));
    $str .= substr($str_param, 0, strlen($str_param) - 1);
    $signature = base64_encode(hash_hmac('sha256', $str, $array['secret_key'], true));
    $url_sig =  sprintf('%s://%s?%sSignature=%s', $url_array['scheme'], $url_array['host'] . $url_array['path'], $str_param, rawurlencode($signature));
    $xml = file_get_contents($url_sig);

    if ($xml === false) {
        return false;
    }
    else {
        $xml = simplexml_load_string($xml);
        foreach ($xml->Items->Item as $Item) {
            $ret .= '<a href="' . $Item->DetailPageURL . '"><img src="' . $Item->MediumImage->URL . '"></a><br />';
            $ret .= '<a href="' . $Item->DetailPageURL . '">' . $Item->ItemAttributes->Title . '</a><br />';
        }
        return $ret;
    }
}

if (!empty($_REQUEST["keyword"])) {
    $keyword = htmlspecialchars($_REQUEST["keyword"], ENT_QUOTES, 'UTF-8');
    $data =api(
    Array(
        'locale' => 'http://ecs.amazonaws.jp/onca/xml',
        'Service' => 'AWSECommerceService',
        'Operation' => 'ItemSearch',
        'AWSAccessKeyId' => 'Your Access Key',
        'AssociateTag' => 'Your Associate Tag',
        'ResponseGroup' => 'Small,Images',
        'SearchIndex' => 'Music',
        'Keywords' => $keyword,
        'Version' => '2009-01-06',
        'secret_key' => 'Your Secret Key'
    )
    );
    
    echo $data;
    exit;
}

?>
 
<p>powered by amazon Product Advertising API</a></p>
</body>
 
</html>