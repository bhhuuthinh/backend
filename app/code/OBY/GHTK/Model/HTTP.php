<?php 

namespace OBY\GHTK\Model;

class HTTP
{
    public $url = "";
    public $headers = [];
    public $verify = false;
    public $delay = 0;

    public function __construct()
    {
        $this->headers =  [
            "Accept" => "application/json",
        ];
    }

    public function post($end_point, $params = null)
    {
        return $this->call('POST', $end_point, $params, null);
    }

    public function get($end_point, $query = null)
    {
        return $this->call('GET', $end_point, null, $query);
    }

    /**
     * @return array
     */
    protected function getOptions($params = null, $query = null){
        return [
            'headers'   => $this->headers,
            'verify'    => $this->verify,
            'query'     => $query,
        ];
    }

    /**
     * @return string
     */
    protected function getDomain(){
        return parse_url($this->url)['host'];
    }

    /**
     * @return string[]
     */
    protected function getHeaderArrayString(){
        $result = [];
        foreach($this->headers as $key => $value){
            $result[]   = $key.': '.$value;
        }
        return $result;
    }

    /** @return string|false */        
    public function call($method = 'GET', $end_point, $params = null, $query = null)
    {
        sleep($this->delay);
        $headers    = $this->getHeaderArrayString();
        $uri        = http_build_query($query ?: []);
        $uri        = (!empty($uri)) ? '?'.$uri : $uri;

        $params     = is_array($params) ? json_encode($params, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_LINE_TERMINATORS) : $params;
        // echo $params; die();
        $curl   = curl_init();
        $curlopt_array  = [
            CURLOPT_URL => $this->url . $end_point . $uri,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $params,
        ];

        curl_setopt_array($curl, $curlopt_array);
        $responseText = curl_exec($curl);

        // Then, after your curl_exec call:
        $errorMessage = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE );
        curl_close($curl);

        $response   = [];
        $response['isSuccess']  = 200 <= $httpCode && $httpCode < 300;
        
        if($response['isSuccess']){
            $encode_res = $this->encode($responseText);
            $response['data']  = $encode_res;
        }
        else{
            $response['message']    = $errorMessage;
            $response['data']  = $this->encode($responseText);
        }

		return json_encode($response);
    }

    /**
     * @return mixed
     */
    protected function encode($input){
        return $input;
    }

    /**
     * @return mixed
     */
    protected function decode($input){
        return $input;
    }
}