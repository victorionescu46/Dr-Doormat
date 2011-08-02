<?php

class Wdc_QuickOrder_Model_listClass
{
	
	
	public function deleteRow($lid)
	{
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$sql="SELECT sid FROM quickorder_list where lid=".$lid;
		
		$result = $readonce->fetchRow($sql);

			/*foreach ($result as $row)
			{
				$sid = $row['sid'];	
			}*/
		
		$sid = $result['sid'];		
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query("delete from quickorder_list where lid=".$lid);
			
		return $this->_getArrayList($sid);
		
	}
	
	public function _getSku($sku)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$sql="SELECT a.entity_id, b.value as des, c.value as price FROM catalog_product_entity a ";
		$sql=$sql."inner join catalog_product_entity_varchar b ";
		$sql=$sql."on a.entity_id = b.entity_id ";
		$sql=$sql."left join catalog_product_entity_decimal c ";
		$sql=$sql."on a.entity_id = c.entity_id ";
		$sql=$sql."WHERE a.sku=".$sku." and b.attribute_id = 96  and c.attribute_id =99";
		
		$result = $readonce->fetchAll($sql);	
		return $result;
	}
		
	public function getSid($sessionId)
	{		
		if(strlen($sessionId) < 5 )
			{
				$sessionId = session_id();					
			}
		
		$sessionId = '\''.$sessionId.'\'';
		$sid = 0;
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql='SELECT sid FROM quickorder_user where session_id ='.$sessionId;
		
		$result = $readonce->fetchRow($sql);
		
		if($result)
		{
			$sid = $result['sid'];	
		}				
		
		if($sid < 1)
		{		
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$write->query('insert into quickorder_user (session_id) values('.$sessionId.')');
			//$write->query('insert into quickorder_user (session_id) values(\'123456\')');
			
			$sql='SELECT sid FROM quickorder_user where session_id ='.$sessionId;
			$result = $readonce->fetchRow($sql);
			//while($row = mysql_fetch_array($result))
			foreach ($result as $row)
			{
				$sid = $row['sid'];	
			}			
		}
		
		
		return $sid;
		
	}
	
	public function _getList($sessionId)
	{
		return _getArrayList($this->getSid($sessionId));				
	}	
	
	
	/**
	 * This is method addList
	 *
	 * @param int $eid Entity ID of product
	 * @param int $qty Qty of Product
	 * @param varchar $sessionId Session ID of the user
	 * @return Array Returns array to block
	 **/
	public function _addList($eid, $qty, $sessionId)
	{
		
		$sid = $this->getSid($sessionId);
		$insert = true;
		
		
		/************ Dbase Connection ****/
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		if(empty($sid))
		{
			$sid =0;	
		}	
		
		/*** Select to see if item exists in list**/
		$sql="SELECT qty FROM quickorder_list where sid =".$sid." and entity_id=".$eid;
		$result = $readonce->fetchAll($sql);
		//while($row = mysql_fetch_array($result))
		foreach ($result as $row)
		{
			$qty = $qty + $row['qty'];			
			$write->query("update quickorder_list set qty=".$qty." where sid=".$sid." and entity_id=".$eid);
			$insert = false;				
		}	
		
		
		if($insert)
		{
			$write->query("insert into quickorder_list (qty, sid, entity_id) values(".$qty.",".$sid.",".$eid.")");
		}		
		
		return $this->_getArrayList($sid);
			
	}
	
	public function _getArrayList($sid)
	{
		
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');		
		$sql="SELECT * FROM quickorder_list where sid =".$sid;
		return $readonce->fetchAll($sql);		
	}
	
	public function _getProductLabel($eid)
	{
		$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');
		
		$sql="SELECT a.sku, a.entity_id, b.value as des, c.value as price FROM catalog_product_entity a ";
		$sql=$sql."inner join catalog_product_entity_varchar b ";
		$sql=$sql."on a.entity_id = b.entity_id ";
		$sql=$sql."left join catalog_product_entity_decimal c ";
		$sql=$sql."on a.entity_id = c.entity_id ";
		$sql=$sql."WHERE a.entity_id=".$eid." and b.attribute_id = 96  and c.attribute_id =99";
		
		$result = $readonce->fetchRow($sql);			
		
		return number_format($result['price'], 2, '.', '').'</td><td>'.$result['sku'].'</td><td>'.$result['des'];
	}
	
	
	public function getTableHtml($list)
	{
		
		$tblHtml = '';
		$tblHtml = $tblHtml.'<table width="80%" align="center" class="data-table"><tr>';
		$tblHtml = $tblHtml.'<th width="5%">Qty</th><th width="5%">Price</th><th width="5%">sku</th><th>Item</th><th></th><th width="5%"></th><th width="10%"></th></tr>';

			$i=0;
			foreach ($list as $row)
			{	
		$bg= 'even';
			if($i % 2)
			{
				$bg = 'odd';	
			}
			$tblHtml = $tblHtml.'<tr class="'.$bg.'"><td>'.$row['qty'].'</td><td>'.$this->_getProductLabel($row['entity_id']).'</td><td></td><td></td><td><a onmouseover="this.style.cursor=\'hand\'" onclick="deleteRow('.$row['lid'].')">remove</a></td></tr>';		
		$i++;			
	}

		$tblHtml = $tblHtml.'</table>';
		
		return $tblHtml;
}
	
}




?>