<?php

class DbOrder  extends DbModel {

	protected static $_table = 'orders';

	public function setStatus(array $data) {

		$query = "UPDATE orders SET status='{$data['status']}' WHERE id={$data['id']}";
		$this->query($query);

		print($query); exit;
	}
}