<?php


if(isset($_GET['lo']))
	{
	$list = $_COOKIE['li'];
	if($list < 0)
		{
			$list = 1;	
		}
	else
		{
		$list = $list + 1;	
		setcookie ("li", $list, time()+604800);
		}	
	}
	


if(isset($_GET['listid']))
	{
			
	$listid = $_GET['listid'];	
	
	}
else
{
	$listid = 0;
	}
	
if(isset($_GET['clearlist']))

	{
		setcookie ("li", 0, time()+604800);
		
	}
	
	

$i=1;
echo '<table width="610" align="center" border="1" color="black">';
while ($i<$list)
{
   
	echo '<tr><td width="40">'.$i.'</td>';
if($i % 2)
	{
		echo '<td width="550">';
	}
	else
	{
		echo '<td width="550"  bgcolor="#cccccc">';
	}	
	echo $list.'</td><td width="40" align="center">Edit</td><td width="40" align="center">Delete</td></tr>';
	$i++;
}

echo '</table>';

?>