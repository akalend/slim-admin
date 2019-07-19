<?php

class DbOrder  extends DbModel {

	protected static $_table = 'orders';

	public function setStatus(array $data) {

		$query = "UPDATE orders SET status='{$data['status']}' WHERE id={$data['id']}";
		$this->query($query);

		print($query); exit;
	}

	public function setTest(int $id, int $set2test ) {
		$data = $set2test ? '1' : 'NULL';
		$query = "UPDATE orders SET is_test=$data WHERE id=$id";
		echo $query;
		$this->query($query);
	}

	public function stats(int $shop_id ) {
		$shop = '';
		if ($shop_id == 1) $shop = 'Razor';
		if ($shop_id == 2) $shop = 'Leggins short';
		if ($shop_id) $shop = " AND shop='$shop'";


		$query = "SELECT concat( month(ts_create),\".\",  day(ts_create)) as day, 
				 count(*) as 'all', sum(sum) as 'sum'
 				 FROM orders WHERE  is_test IS NULL $shop
				 GROUP BY concat( month(ts_create),\".\",  day(ts_create)) ";
		$stats1 = $this->exec($query);
		$stats = [];

		foreach ($stats1 as  $item) {
			$stats[$item['day']] = ['all' => $item['all'], 'sum' => $item['sum'],'approved' => 0]; 
		}


		$query = "SELECT concat( month(ts_create),\".\",  day(ts_create)) as day,  count(*) as 'approved' 
				 FROM orders WHERE  is_test IS NULL AND status='CC: approved'  $shop
				 GROUP BY concat( month(ts_create),\".\",  day(ts_create)) ";

		$stats2 = $this->exec($query);

		foreach ($stats2 as  $item) {
			$stats[$item['day']]['approved'] = $item['approved']; 
		}

		return $stats;
	}

}