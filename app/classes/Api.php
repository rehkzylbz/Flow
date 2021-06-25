<?php
class Api {
    
    protected static $_settings;
    
    public function __construct($settings = [])	{
		self::$_settings = $settings;
	}
	
	public static function check_data() {
        $result = ['Запрос отправлен!'];
		$result[] = ['Категории' => self::check_cats()];
		$result[] = ['Продукты' => self::check_products()];
        return $result;
    }
	
	private static function check_cats() { 
        $result = false;
        $result = $cats = self::get_from_api(self::$_settings['api_cats_method'], false);
        if ( $result ) {
            $cats = self::xml_to_array($cats);
            $db = new DB();
            $result = $db->truncate_table('cats'); 
            if ( $result['status'] === true ) {
                foreach ( $cats['Categories']['Category'] as $cat ) {
                    $result = $db->add_cat($cat);   
                };
            };
        };
        return $result;
    }
    
    private static function check_products() {
        $result = false;
        $products_left = 0;
        $params = [
                    [
                        'closed' => true,
                        'name' => 'Limit',
                        'attributes' => [
                            'offset' => 0,
                            'row_count' => 1000
                    ]
                ]
        ];
        $db = new DB();
        $result = $db->truncate_table('products'); 
        if ( $result['status'] === true ) {
            do {
                $result = $products = self::get_from_api(self::$_settings['api_products_method'], $params);
                $products = preg_replace(['/<Category(.*?)>/', '/<\/Category>/'], ['<Category$1><name>', '</name></Category>'], $products);
                if ( $result ) {
                    $products = self::xml_to_array($products);
                    $result = $db->add_products($products);
                };
                $total_products = (int)$products['TotalProducts'];
                $products_left = $total_products - (int)$products['Limit']['@attributes']['offset'] - (int)$products['Limit']['@attributes']['row_count'];
                $params[0]['attributes']['offset'] = (int)$products['Limit']['@attributes']['offset'] + (int)$products['Limit']['@attributes']['row_count'];
                
            } while ( $products_left > 0 or !$result);
        };
        return $result;
    }
    
    private static function get_from_api($method = [], $params = []) {
        $result = false;
        $request = self::render_xml_request($method['name'], $params);
        $url = self::$_settings['api_url'].$method['url'];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: '.strlen($request)
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    
    private static function render_xml_request($method_name = '', $params = false) {
        $result = '';
        $transation_id = time();
        $login = self::$_settings['api_login'];
        $password = self::$_settings['api_password'];
        $hash = md5($transation_id.$method_name.$login.$password);
        $result = "<?xml version='1.0' encoding='utf-8'?>
        <Request>
            <Authentication>
                <Login>$login</Login>
                <TransactionID>$transation_id</TransactionID>
                <MethodName>$method_name</MethodName>
                <Hash>$hash</Hash>
            </Authentication>";
        if ( $params !== false ) {
            $result .= '<Parameters>';
            foreach ( $params as $param ) {
                if ( $param['closed'] === true ) {
                    $result .= '<'.$param['name'];
                    foreach ( $param['attributes'] as $key => $value ) {
                        $result .= ' '.$key.'="'.$value.'"';            
                    };
                    $result .= '/>';
                }  
                else {
                    //todo формировоние вложенных параметров
                }
            };
            $result .= '</Parameters>';
        }
        $result .= '</Request>';
        return $result;
    }
    
    private static function xml_to_array($string = '') {
        $result = false;
        $xml = simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        $result = json_decode(json_encode($xml), true);
        return $result;
    }
    
}