<?php
$database = 'log';
include '../mysql.php';

function getinfo($connection,$page,$limit) {
	$q='select count(file) from musiclist order by time desc';
	$result = mysqli_query($connection,$q);
	$row = mysqli_fetch_assoc($result);
	$totalfiles = intval($row['count(file)']);
	$totalpages=ceil($totalfiles/$limit);
	

	
	
	//echo $totalpages.'<br>';
	if (($page*$limit)>(($totalpages-1)*$limit)) {
		$page=$totalpages-1;
	}

	//echo ($page*$limit).'<br>';
	//echo (($totalpages-1)*$limit).'<br>';
	$page = mysqli_real_escape_string($connection,$page*$limit);
	$limit = mysqli_real_escape_string($connection,$limit);
	$q='select file,text,time,scalekey,notedir,notelength from musiclist order by time desc limit '.$page.','.$limit;
	$result = mysqli_query($connection,$q);
	while ($row = mysqli_fetch_assoc($result)) {
		echo $row['time'].' Scale: <span style="color:#aa33aa;">'.$row['scalekey'].'</span> Note Type: <span style="color:#aa3333;">'.$row['notedir'].'</span> Note Length: <span style="color:orange;">'.$row['notelength'].'</span><br><a href="'.$row['file'].'">'.$row['text'].'</a><br>';
		echo "\n";
	}
	
	
	
}
function makepagelist($connection,$limit) {
	$q='select count(file) from musiclist order by time desc';
	$result = mysqli_query($connection,$q);
	$row = mysqli_fetch_assoc($result);
	$totalfiles = intval($row['count(file)']);
	$totalpages=ceil($totalfiles/$limit);
	for ($i=1;$i<=$totalpages;$i++) {
		echo '<a href="catalog.php?page='.$i.'&limit='.$limit.'">'.$i.'</a> ';
	}
}
function addquot($instring) {
	return str_replace('"','&quot;',$instring);
}

if (isset($_GET['page']) && is_numeric(intval($_GET['page']))) {
	$page=$_GET['page'];
} else {
	$page=1;
}
if (isset($_GET['limit']) && is_numeric(intval($_GET['limit']))) {
	$limit = $_GET['limit'];
} else {
	$limit=25;
}
if ($limit>250) {
	$limit=250;
} elseif ($limit<10) {
	$limit=10;
}
?> 
<!DOCTYPE html>
<html>
<head>
<title>Music Generator</title>
<meta charset="utf-8">
<meta name="robots" content="index,follow">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="/style.css" rel="stylesheet">
<link rel="icon" type="image/png" href="/favicon.png">
<style type="text/css">
div.box {
	border:dashed 1px rgb(127,127,127);
	margin-top:2px;
	margin-bottom:2px;
}
.inline {
	display:inline;
}
div.info {
	padding:1em;
}
</style>
</head>
<body>
<div class="page">
<header>
	<h1 class="ross"><a href="/">Ross Carley</a></h1><h2> > <a href="/music">Music Generator</a></h2><h2> > Catalog</h2>
</header>
<div class="content" style="word-wrap: break-word;">

<nav>
<div class="box">Page <?php makepagelist($connection,$limit); ?> - <form action="" method="get" class="inline"><input type="hidden" name="page" value="<?php echo addquot($page); ?>">Limit <input type="text" maxlength="3" value="<?php echo addquot($limit); ?>" name="limit" size="3"><input type="submit" value="GO"></form></div>
</nav>


<div class="info">
<?php
getinfo($connection,($page-1),$limit);
?>
</div>
<nav>
<div class="box">Page <?php makepagelist($connection,$limit); ?> - <form action="" method="get" class="inline"><input type="hidden" name="page" value="<?php echo addquot($page); ?>">Limit <input type="text" maxlength="3" value="<?php echo addquot($limit); ?>" name="limit" size="3"><input type="submit" value="GO"></form></div>
</nav>
</div>
</div>
</body>
</html>
