<?php
$global = new stdClass();
$auth = json_decode($_REQUEST["auth"]);
$projectData = json_decode($_REQUEST["projectData"]);
$options = array('auth' => $auth);
initCurlHandler();
setBaseURI("https://cirrus-drive.firebaseio.com");
$id = $auth->uid;
$result = json_decode(writeData(getPath($id), $projectData, "POST", $options));
if ($result->error) {
	echo $result->error;
} else {
	echo "success";
}


function getPath($id) {
	return "requests/" . $id . "/";
}

////////////////////////////////////////
//probably don't touch below this line//
///////////////////////////////////////

function initCurlHandler() {
        $GLOBALS['global']->_curlHandler = curl_init();
}

function closeCurlHandler() {
        curl_close($GLOBALS['global']->_curlHandler);
}

function setBaseURI($baseURI) {
        $baseURI .= (substr($baseURI, -1) == '/' ? '' : '/');
        $GLOBALS['global']->_baseURI = $baseURI;
}

function get($path, $options = array()) {
        try {
            $ch = _getCurlHandler($path, 'GET', $options);
            $return = curl_exec($ch);
        } catch (Exception $e) {
            $return = null;
        }
        return $return;
}

function writeData($path, $data, $method = 'POST', $options = array()) {
        $jsonData = json_encode($data);
        $header = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        );
        try {
            $ch = _getCurlHandler($path, $method, $options);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            $return = curl_exec($ch);
        } catch (Exception $e) {
            $return = null;
        }
        return $return;
}

function _getJsonPath($path, $options = array()) {
        $url = $GLOBALS['global']->_baseURI;
        if ($GLOBALS['global']->_token !== '') {
            $options['auth'] = $GLOBALS['global']->_token;
        }
        $path = ltrim($path, '/');
        return $url . $path . '.json?' . http_build_query($options);
    }

function _getCurlHandler($path, $mode, $options = array()) {
        $url = _getJsonPath($path, $options);
        $ch = $GLOBALS['global']->_curlHandler;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $GLOBALS['global']->_timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $GLOBALS['global']->_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $mode);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        return $ch;
    }
?>