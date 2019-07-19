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

	static function showPage($request, $response, $args, $db, array $parm, $isDownload = false) {

        // var_dump($isDownload); exit;

        $page_id = isset($args['id']) ? (int)$args['id'] : 0;
        $shop = $request->getQueryParam('shop');
        $status = $request->getQueryParam('status');
        $search = $request->getQueryParam('search');
        $filter = $request->getQueryParam('filter');
 // echo '<pre>'; var_dump( $filter ); exit;
        $where = null;
        if ($shop) {
            if ($shop == 1) $where = "shop='Leggins short'";
            if ($shop == 2) $where = "shop='Razor'";
        }

        if ($status) {
            $where = empty($where) ? "status='$status'" :  "$where AND status='$status'";
        }    

        if ($filter) {
            switch ($filter){
                case 'day' : 
                    $ts = strtotime('today');
                    $where = empty($where) ? "ts_update > FROM_UNIXTIME($ts)" :  "$where AND ts_update > FROM_UNIXTIME( $ts ) ";
                    break;

                case 'yesterday' : 
                    $yesterday = strtotime('yesterday');
                    $today     = strtotime('today');
                    $where = empty($where) ? "ts_update BETWEEN FROM_UNIXTIME($yesterday) AND FROM_UNIXTIME($today)" :  "$where AND ts_update BETWEEN FROM_UNIXTIME($yesterday) AND FROM_UNIXTIME($today)";
                    break;

                case 'yesterday' : 
                    $day  =  date ( 'd' );
                    $mon  =  date ( 'm' );
                    $ts = mktime(0,0,0,$mon, 1);

                    $where = empty($where) ? "ts_update > FROM_UNIXTIME($ts)" :  "$where AND ts_update > FROM_UNIXTIME( $ts ) ";
                    break;
            }
        }

    // echo $where; exit;

        if ($search) {
            if (is_numeric($search[0]))
                $where = 'order_id='. (int)$search;
            else
                $where = "name LIKE '{$search}%'";
        }
        // echo '<pre>'; var_dump( $where ); exit;


        $count = $db->count($where);

        $current = isset($args['id']) ? $args['id'] : 0;


        if (!$current) $current = 0;
        $skip = $current * PAGESIZE > $count ? $count - PAGESIZE : $current * PAGESIZE;  

        $res = $isDownload ? 
            $db->getList( null, null, $parm['orderBy'].' DESC', $where):
            $db->getList( $skip, PAGESIZE, $parm['orderBy'].' DESC', $where);

        foreach ($res as &$item) {
            if(!isset($item['ts'])) 
            	$item['ts'] = $item['ts_create'];

            if(isset($item['ts_update'])) { 
                $item['ts_update'] = substr($item['ts_update'], 0, 16);
            } else {
                $item['ts_update'] = '';
            }
        
    // var_dump($item); exit;
            if (empty($item['result'])) continue;
            $str = $item['result'];
            $str[0]=' ';
            $str[strlen($str)-1]=' ';
            $result = json_decode($str);
            $item['phone'] = is_object($result) && isset($result->order->phone) ? $result->order->phone : '';
            
        }
        if ($isDownload) {

            $parm['route']->view->render($response, 'exel.html', [
              'entries' => $res,
            ]  );
        } else {
            $url = $request->getUri()->getQuery();
            $parm['route']->view->render($response, 'content.html', [
              'entries' => $res,
              'pager'   => ['count' => $count, 
                            'pages' => (int)($count /PAGESIZE),
                            'page' => PAGESIZE,
                            'current' => $current,
                            'pred' =>  $current > 0 ? $current - 1 : 0  ,
                            ],
               'shop'   => empty($url) ? '' : '?' . $url, //$shop ? "?shop=$shop" : '',
               'query' => $request->getUri()->getQuery(),
               'title'  => $parm['title'],
               'url'    => $parm['url'],
               'details'=> $parm['details'],
            ]  ); 
        }   
	}

}