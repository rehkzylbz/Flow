<?php

class DB {
	
	private $db;
    
    public function __construct() {
		try {    
			$this->db = new PDO('sqlite:'.$_SERVER['DOCUMENT_ROOT'].'/app/sqliteDBs/data.db');
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->exec( 'PRAGMA foreign_keys = ON;' );
		} catch (PDOException $e) {
			 die ('Ошибка соединения с БД: '.$e->getMessage());
		}
    }
    
    public function truncate_table($table_name = '') {
		$result = [
			'status' => false,
			'content' => 'Запрос к БД неудачен.'
		];
		try {
			$query = $this->db->prepare('DELETE FROM '.$table_name);
			$query->execute();
			$query = $this->db->prepare('DELETE FROM SQLITE_SEQUENCE WHERE name = "'.$table_name.'"');
			$query->execute();
			$query = $this->db->prepare('VACUUM');
			if ( $query->execute() ) {			
				$result['status'] = true;
				$result['content'] = 'Таблица очищена.';
			}
		}
		catch (PDOException $e) {
			$result['content'] = 'Выполнение запроса к БД не удалось. '.$e->getMessage();
			return $result;
		}	
        return $result;
    }
    
    public function add_cat($cat = []) {
		$result = [
			'status' => false,
			'content' => 'Запрос к БД неудачен.'
		];
		try {
			$query = $this->db->prepare('INSERT INTO cats (cat_id, parent_id, name) VALUES (:cat_id, :parent_id, :name)');	
			$query->bindValue(':cat_id', $cat['@attributes']['id'], PDO::PARAM_INT);
			$query->bindValue(':parent_id', isset($cat['@attributes']['parentId'])?$cat['@attributes']['parentId']:0, PDO::PARAM_INT);
			$query->bindValue(':name', $cat['name'], PDO::PARAM_STR);
			if ( $query->execute() && $id = $this->db->lastInsertId() ) {			
				$result['status'] = true;
				$result['content'] = 'Запись произведена.';
			}
		}
		catch (PDOException $e) {
			$result['content'] = 'Выполнение запроса к БД не удалось. '.$e->getMessage();
			return $result;
		}	
        return $result;
    }
    
    public function add_products($products = []) {
		$result = [
			'status' => false,
			'content' => 'Запрос к БД неудачен.'
		];
		$products = $products['Products']['Product'];
		$count_step = 150;
		$done = 0;
		while ( $done < count($products) ) {
    		$sql = [];
    		for ( $i = $done; $i < min($done+$count_step, count($products)); $i++) {
    		    $sql[] = '(:product_id_'.$products[$i]['Id'].', :cat_id_'.$products[$i]['Id'].', :name_'.$products[$i]['Id'].', :price_'.$products[$i]['Id'].', :img_'.$products[$i]['Id'].', :descr_'.$products[$i]['Id'].')';
    		};
    		$sql = 'INSERT INTO products (product_id, cat_id, name, price, img, descr) VALUES '.implode($sql, ',');
    		$query = $this->db->prepare($sql);
    		for ( $i = $done; $i < min($done+$count_step, count($products)); $i++) {
    		    $query->bindValue(':product_id_'.$products[$i]['Id'], $products[$i]['Id'], PDO::PARAM_INT);
    			$query->bindValue(':cat_id_'.$products[$i]['Id'], $products[$i]['Category']['@attributes']['id'], PDO::PARAM_INT);
    			$query->bindValue(':name_'.$products[$i]['Id'], $products[$i]['Name'], PDO::PARAM_STR);
    			$query->bindValue(':price_'.$products[$i]['Id'], $products[$i]['Price'], PDO::PARAM_INT);
    			$query->bindValue(':img_'.$products[$i]['Id'], isset($products[$i]['Picture'])?$products[$i]['Picture']:'', PDO::PARAM_STR);
    			$query->bindValue(':descr_'.$products[$i]['Id'], isset($products[$i]['Annotation'])?$products[$i]['Annotation']:'', PDO::PARAM_STR);
    		};
    		try {
    			if ( $query->execute() && $id = $this->db->lastInsertId() ) {			
    				$result['status'] = true;
    				$result['content'] = 'Запись произведена.';
    			}
    		}
    		catch (PDOException $e) {
    			$result['content'] = 'Выполнение запроса к БД не удалось. '.$e->getMessage();
    			return $result;
    		}
    		$done += $count_step;
		}
        return $result;
    }
    
    public function get_all_cats() {
        $result = [
			'status' => false,
			'content' => 'Запрос к БД неудачен.'
		];
		try {
    		$query = $this->db->prepare('SELECT cats.cat_id, cats.parent_id, cats.name, count(products.id) as products_count FROM cats LEFT JOIN products ON cats.cat_id = products.cat_id GROUP BY cats.cat_id');
    		if ( $query->execute() ) {
        		$query_results = $query->fetchAll();
        		foreach ( $query_results as $query_result )
        		    $result[$query_result['parent_id']][] = $query_result;
    		}
		}
		catch (PDOException $e) {
			$result['content'] = 'Выполнение запроса к БД не удалось. '.$e->getMessage();
		}
		return $result;
    }
    
    public function get_products_by_cat($cat_id = 0) {
        $result = [
			'status' => false,
			'content' => 'Запрос к БД неудачен.'
		];
		try {
    		$query = $this->db->prepare('SELECT * FROM products WHERE cat_id = :cat_id');
    		$query->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
    		if ( $query->execute() ) 
        		$result = $query->fetchAll();
		}
		catch (PDOException $e) {
			$result['content'] = 'Выполнение запроса к БД не удалось. '.$e->getMessage();
		}
		return $result;
    }
    
    public function get_cat_by_id($cat_id = 0) {
        $result = [
			'status' => false,
			'content' => 'Запрос к БД неудачен.'
		];
		try {
    		$query = $this->db->prepare('SELECT * FROM cats WHERE cat_id = :cat_id');
    		$query->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
    		if ( $query->execute() ) 
        		$result = $query->fetch();
		}
		catch (PDOException $e) {
			$result['content'] = 'Выполнение запроса к БД не удалось. '.$e->getMessage();
		}
		return $result;
    }
    
}