<?php
class Wdc_Catalog_Block_Navigation extends Mage_Catalog_Block_Navigation  
{	
	public function WDCdrawItemreDux($category, $level=0, $last=false)
	{
		$html = '';
		
		try{
			if(!isset($cnt))
			{
				$cnt = 0;	
			}
			
			if (!$category->getIsActive()) {
				return $html;
			}
			if (Mage::helper('catalog/category_flat')->isEnabled()) {
				$children = $category->getChildrenNodes();
				$childrenCount = count($children);
			} else {
				$children = $category->getChildren();
				$childrenCount = $children->count();
			}
			$hasChildren = $children && $childrenCount;
			
			if($level < 2){
				$html.= '<li';
				
				if ($hasChildren) {
					$html.= ' onmouseover="toggleMenu(this,1)" onmouseout="toggleMenu(this,0)"';
				}
				
				$html.= ' class="level'.$level;
				$html.= ' nav-'.str_replace('/', '-', Mage::helper('catalog/category')->getCategoryUrlPath($category->getRequestPath()));
				if ($this->isCategoryActive($category)) {
					$html.= ' active';
				}
				if ($last) {
					$html .= ' last';
				}
				if ($hasChildren) {
					$cnt = 0;
					foreach ($children as $child) {
						if ($child->getIsActive()) {
							$cnt++;
						}
					}
					$html .= ' parent';
				}
				$html.= '">'."\n";
			}
			
			$class = $this->getClass($level);
			
				$html.= '<a href="'.$this->getCategoryUrl($category).'"><span class="'.$class.'">'.$this->getCatImage($this->htmlEscape($category->getName())).'</span></a>'."\n";
			//$html.= '<a href="'.$this->getCategoryUrl($category).'"><span class="'.$class.'">'.$this->getCatImage($this->$category->getId()).'</span></a>'."\n";
			
			if ($hasChildren){
				
				$j = 0;
				$htmlChildren = '';
				foreach ($children as $child) {
					if ($child->getIsActive()) {
						$htmlChildren.= $this->WDCdrawItemreDux($child, $level+1, ++$j >= $cnt);
					}
				}
				
				if (!empty($htmlChildren)) {
					
					if($level < 1){
						$html.= '<ul class="level' . $level . '">'."\n"
							.$htmlChildren
							.'</ul>';
					}	
					else{
						$html.= $htmlChildren;
					}
					
				}
				
			}
			if($level < 2){
				$html.= '</li>'."\n";
			}
			return $html;
		}
		catch(exception $e)
		{
			return $e;
		}
		
	}
	
	protected function getClass($level)
	{
		$class = 'top';
		switch ($level) {
			case 0:
				$class = 'top';
				break;
			case 1:
				$class = 'parent';
				break;
			case 2:
				$class = 'child';
				break;
			case 3:
				$class = 'subchild';
				break;	
			default:
				$class = 'top';
				break;						
		}
		
		return $class;
	}
	
	protected function getCatImage($category)
	{
		$this->setSessionCnt();
		
		$catId = $this->getCategoryId($category);	
		
		$image = '';
		switch ($catId) {
			case 10: //'beer':
				$image = 'MidwestMenuBar0609_a_01.gif';
				$_SESSION['cnt'] = 0;
				$_SESSION['childCnt'] = $this->getChildCount($category);
				$_SESSION['colHeadCnt'] = 5;
				break;
			case 13: //'kegging':
				$image = 'MidwestMenuBar0609_a_02.gif';
				$_SESSION['cnt'] = 0;
				$_SESSION['childCnt'] = $this->getChildCount($category);
				$_SESSION['colHeadCnt'] = 3;
				break;
			case 18: //'all-grain brewing':
				$image = 'MidwestMenuBar0609_a_03.gif';
				$_SESSION['cnt'] = 0;
				$_SESSION['childCnt'] = $this->getChildCount($category);
				$_SESSION['colHeadCnt'] = 3;
				break;
			case 138: //'wine making':
				$image = 'MidwestMenuBar0609_a_04.gif';
				$_SESSION['cnt'] = 0;
				$_SESSION['childCnt'] = $this->getChildCount($category);
				$_SESSION['colHeadCnt'] = 4;
				break;
			case 145:// 'soda-liqueurs':
				$image = 'OtherProducts.gif';
				$_SESSION['cnt'] = 0;
				$_SESSION['childCnt'] = $this->getChildCount($category);
				$_SESSION['colHeadCnt'] = 2;
				break;
			case 331: //'more':
				$image = 'MidwestLinks.gif';
				$_SESSION['cnt'] = 0;
				$_SESSION['childCnt'] = $this->getChildCount($category);
				$_SESSION['colHeadCnt'] = 1;
				break;
			default:
				$this->setHeaderCnt();
				return $this->htmlEscape($category->getName());
				break;
		}
		
		return '<img src="/skin/frontend/default/midwest/images/nav/'.$image.'" />';
	}
	
	protected function getCategoryId($category)
	{
		$arr = explode(",", $category);
		return $arr[0];	
	}
	
	protected function getChildCount($category)
	{
		$dataArray = explode(",", $category);
		return $dataArray[9];		
	}
	
	protected function getLevel($category)
	{
		$dataArray = explode(",", $category);
		return $dataArray[8];		
	}
	
	
	/**
	 * This is method getColRows
	 *
	 * @param int $cols Number of Columns on the Menu
	 * @param int $totChild Total number of Children in category
	 * @return mixed This is the return value description
	 *
	 */
	protected function getColRows($cols, $totChild)
	{
		if($totChild ==0)
			{
			return 1;
			}
			else
		{				
			return $totChild/$cols;
		}	
	}
	
	protected function GetColNum($cols, $totChild)
	{
		
		$rows = $this->getColRows($cols, $totChild);
		
		if($rows > $this->setSessionCnt())
			{
			$cols = 1;
			}
		else
			{
				(int)$col = $this->setSessionCnt()/$rows;
				
			if($col < $cols)
				{
				$col = $col + 1;
				}								
			}
		return $col;		
	}
	
	protected function checkLevel($category, $lvl=2)
	{
		$cl = false;
		
		$l = $this->getLevel($category);
		if($lvl < $l)
			{
			$cl = true;	
			}
		return $cl;			
	}
	
	protected function setSessionCnt()
	{
		if(!isset($_SESSION['cnt']))
		{
			$_SESSION['cnt'] = 1;	
		}
					
		return $_SESSION['cnt'];	
	}
	
	protected function setHeaderCnt()
	{
		if(!isset($_SESSION['childCnt']))
		{
			$_SESSION['childCnt'] = 1;				
		}					
		return $_SESSION['childCnt'];	
	}
	
	protected function setRowStart()
	{
		if(!isset($_SESSION['rowStart']))
		{
			$_SESSION['rowStart'] = true;				
		}					
		return $_SESSION['rowStart'];
	}
	
	protected function setColHeadcnt()
	{
		if(!isset($_SESSION['colHeadCnt']))
		{
			$_SESSION['colHeadCnt'] = 1;				
		}
							
		return $_SESSION['colHeadCnt'];
	}
	
	protected function CheckEndColumn($category)
	{	
		$html='<test />';
				if($this->checkLevel($category))
		{
			/** This is the Child count of each category***/
			$childcount = $this->getChildCount($category);
			/*** This is the potential for the number rows AFTER the categories are populated**/
			$totPotRows = $childcount + $this->setSessionCnt();
			/** This is the number of rows in each column for this category**/
			$colRows = $this->getColRows($this->getCatCol($category), $this->setHeaderCnt());
			/** This gives us the difference in rows**/
			$rowDif = $colRows - $totPotRows;	
			
			//was set to 0
			if(!$rowDif < 0)
			{ 
				//Was set to 5
				if($rowDif < 5)
				{
					$html='</div><div class="top-left">';
				}	
			}
		}
		
		return $html;
	}
	
	protected function CheckEndColumnBeta($category)
	{
		$this->setSessionCnt();
		$newRow = false;
		$_SESSION['rowStart'] = false;
		
		/*if($this->checkLevel($category))
		{
			/** This is the Child count of each category***/
			//$childcount = $this->getChildCount($category);
			/*** This is the potential for the number rows AFTER the categories are populated**/
			//$totPotRows = $childcount + $this->setSessionCnt();
			/** This is the number of rows in each column for this category**/
			//$colRows = (int)getColRows($this->getCatCol($category), $this->setHeaderCnt());
			/** This gives us the difference in rows**/
			//$rowDif = $colRows - $totPotRows;	
			
		/*	if(!$rowDif < 0)
			{ 
				if($rowDif < 5 )
				{
					$newRow = true;	
					$_SESSION['rowStart'] = true;
				}	
			}
		}*/
		
		if($_SESSION['cnt'] >  $this->setHeaderCnt())
		{
			$newRow = true;	
			$_SESSION['rowStart'] = true;
			//echo 'Errorflag--->'.$_SESSION['cnt'].'--->'.$this->setHeaderCnt().'<br>';
		}	
		
		return $newRow;
	}
	
	protected function setSubColCount()
	{
		if(!isset($_SESSION['subcnt']))
		{
			$_SESSION['subcnt'] = 0;	
		}	
		return $_SESSION['subcnt']++;	
	}
	
	protected function resetSubcount()
	{
		if(!isset($_SESSION['subcnt']))
		{
			$_SESSION['subcnt'] = 0;	
		}	
		return $_SESSION['subcnt'] =0;	
	}
	
	protected function getColumnBreak($category)
	{
		$html='';
		$subcnt = (int)$this->setSubColCount();
		if($this->setHeaderCnt() > $this->setSessionCnt())
			{
				/** This is the Child count of each category***/
				$childcount = (int)$this->getChildCount($category);
				/*** This is the potential for the number rows AFTER the categories are populated**/
				$totPotRows = $childcount + $subcnt;
				/** This is the number of rows in each column for this category**/
				$colRows = $this->getColRows($this->setColHeadcnt(), $this->setHeaderCnt());		
				/** This gives us the difference in rows**/
				$rowDif = (int)$colRows - (int)$totPotRows;
			//echo $totPotRows.'<br>';
			//was set to -3
				if($rowDif < -3)
			{	
				$html='</div><div class="top-left">';
				$this->resetSubcount();
			}
				
				}	
		return $html;
	}
		
	public function WdcMenuDraw($category, $level=0, $last=false)
	{
		$html = '';
		$endRow = false;
		$count = $this->setSessionCnt();
		$_SESSION['cnt'] = $_SESSION['cnt'] + 1;
		
		
		try{
			if(!isset($cnt))
			{
				$cnt = 0;	
			}
			
			if (!$category->getIsActive()) {
				return $html;
			}
			if (Mage::helper('catalog/category_flat')->isEnabled()) {
				$children = $category->getChildrenNodes();
				$childrenCount = count($children);				
			} else {
				$children = $category->getChildren();
				$childrenCount = $children->count();
			}
			
			
			$hasChildren = $children && $childrenCount;
			
			if($level == 0){
				
				/***I have to figure out how to add the columns here***/
				
				$html.= '<li';
				
				if ($hasChildren) {
					$html.= ' onmouseover="toggleMenu(this,1)" onmouseout="toggleMenu(this,0)"';
				}
				
				$html.= ' class="level'.$level;
				$html.= ' nav-'.str_replace('/', '-', Mage::helper('catalog/category')->getCategoryUrlPath($category->getRequestPath()));
				if ($this->isCategoryActive($category)) {
					$html.= ' active';
				}
				if ($last) {
					$html .= ' last';
				}
				if ($hasChildren) {
					$cnt = 0;
					foreach ($children as $child) {
						if ($child->getIsActive()) {
							$cnt++;
						}
					}
					$html .= ' parent';
				}
				$html.= '">'."\n";
				$_SESSION['rowStart'] = true;
			}
			else{
				
				if($this->setRowStart())
				{
					$html.='<div class="top-left">';
					$_SESSION['rowStart'] = false;	
					$this->resetSubcount();			
				}
				else{					
					$html.=$this->getColumnBreak($category);
				}
				/**** This the alternate on anything other than Level0**/
			}
			
			$class = 'top';
			switch ($level) {
				case 0:
					$class = 'top';
					$_SESSION['rowStart'] = true;
					break;
				case 1:
					$class = 'parent';
					$_SESSION['rowStart'] = false;
					break;
				case 2:
					$class = 'child';
					$_SESSION['rowStart'] = false;
					break;
				case 3:
					$class = 'subchild';
					$_SESSION['rowStart'] = false;
					break;
				
			}
			/*** This is where the links in the menus start***/
			
			
			
			//$html.= '<a href="'.$this->getCategoryUrl($category).'"><span class="'.$class.'">'.$this->getCatImage($category).$this->setHeaderCnt().'</span></a>'."\n";
			$html.= '<a href="'.$this->getCategoryUrl($category).'"><span class="'.$class.'">'.$this->getCatImage($category).'</span></a>'."\n";
			
			if ($hasChildren){
				
				$j = 0;
				$htmlChildren = '';
				foreach ($children as $child) {
					if ($child->getIsActive()) {
						$htmlChildren.= $this->WdcMenuDraw($child, $level+1, ++$j >= $cnt);
					}
				}
				
				if (!empty($htmlChildren)) {
					
					if($level == 0){
						$html.= '<ul class="level' . $level . '">'."\n";
						$html.= $htmlChildren;
						$html.= '</div>';
						$html.=	'</ul></li>';
						$endRow = true;
					}	
					else{
						$html.= $htmlChildren;
					}
					
				}
				
			}
			
			
			//$_SESSION['cnt'] = 0;
			return $html;
		}
		catch(exception $e)
		{
			return $e;
		}
		
	}
	
}
?>