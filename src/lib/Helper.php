<?php

class Helper {

	static public function checkLogin() {
		return isset($_SESSION['isAutorize']) ? $_SESSION['isAutorize'] : false;
	}

	static function autorize(array $params) {
		if ($params['user'] != 'admin' ) return false;
		if ($params['psw']  != 'admin' ) return false;
		
		$_SESSION['isAutorize'] = true;
		return true;
	}

	static function showPage($request, $response, $args, $db, array $parm) {

    $page_id = isset($args['id']) ? (int)$args['id'] : 0;
    $shop = $request->getQueryParam('shop');
    $status = $request->getQueryParam('status');
    $search = $request->getQueryParam('search');

    $where = null;
    if ($shop) {
        if ($shop == 1) $where = "shop='Leggins short'";
        if ($shop == 2) $where = "shop='Razor'";
    }

    if ($status) {
        $where = empty($where) ? "status='$status'" :  "$where AND status='$status'";
    }    

    if ($search) {
        if (is_numeric($search[0]))
            $where = 'order_id='. (int)$search;
        else
            $where = "name LIKE '{$search}%'";
    }
    // echo '<pre>'; echo $where; exit;


    $count = $db->count($where);

    $current = isset($args['id']) ? $args['id'] : 0;


    if (!$current) $current = 0;
    $skip = $current * PAGESIZE > $count ? $count - PAGESIZE : $current * PAGESIZE;  


    $res = $db->getList( $skip, PAGESIZE, $parm['orderBy'].' DESC', $where);

    foreach ($res as &$item) {
        if(!isset($item['ts'])) 
        	$item['ts'] = $item['ts_create'];
    
// var_dump($item); exit;
        if (empty($item['result'])) continue;
        $str = $item['result'];
        $str[0]=' ';
        $str[strlen($str)-1]=' ';
        $result = json_decode($str);
        $item['phone'] = is_object($result) && isset($result->order->phone) ? $result->order->phone : '';
        
    }

    $parm['route']->view->render($response, 'content.html', [
      'entries' => $res,
      'pager'   => ['count' => $count, 
                    'pages' => (int)($count /PAGESIZE),
                    'page' => PAGESIZE,
                    'current' => $current,
                    'pred' =>  $current > 0 ? $current - 1 : 0  
                    ],
       'shop'   => $shop ? "?shop=$shop" : '',
       'title'  => $parm['title'],
       'url'    => $parm['url'],
       'details'=> $parm['details'],
    ]  );

	}

}