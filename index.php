<?php
$database = 'log';
include '../log.php';
include '../mysql.php'; 
function addinfo($file,$in,$scalekey,$startindex,$connection) {

	$f=mysqli_real_escape_string($connection,$file);
	$i=mysqli_real_escape_string($connection,$in);
	$scalekey=mysqli_real_escape_string($connection,$scalekey);
	$startindex=mysqli_real_escape_string($connection,$startindex);
	$q="insert into musiclist (file,text,scalekey,startindex) values ('".$f."','".$i."','".$scalekey."','".$startindex."')";
	mysqli_query($connection,$q);
}
function getrecent($connection) {
	$q='select file,text,time,scalekey,startindex from musiclist order by time desc limit 10';
	$result = mysqli_query($connection,$q);
	while ($row = mysqli_fetch_assoc($result)) {
		echo $row['time'].' Scale: <span style="color:#aa33aa;">'.$row['scalekey'].'</span> Start Index: <span style="color:#3333aa;">'.$row['startindex'].'</span><br><a href="'.$row['file'].'">'.$row['text'].'</a><br>';
		echo "\n";
	}
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
textarea {
	width:100%;
}
</style>
</head>
<body>
<div class="page">
<div class="content">
<!-- <pre> -->
<?php
$errorarray = array();
$alphabet = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
$allnotes = array("0000.wav","0001.wav","0002.wav","0003.wav","0004.wav","0005.wav","0006.wav","0007.wav","0008.wav","0009.wav","0010.wav","0011.wav","0012.wav","0013.wav","0014.wav","0015.wav","0016.wav","0017.wav","0018.wav","0019.wav","0020.wav","0021.wav","0022.wav","0023.wav","0024.wav","0025.wav","0026.wav","0027.wav","0028.wav","0029.wav","0030.wav","0031.wav","0032.wav","0033.wav","0034.wav","0035.wav","0036.wav","0037.wav","0038.wav","0039.wav","0040.wav","0041.wav","0042.wav","0043.wav","0044.wav","0045.wav","0046.wav","0047.wav");
$basedir = '/home/mouse/ffmpegwork/';
$emajor = array(0,2,4,5,7,9,11);
$gmajor = array(0,2,3,5,7,8,10);
$majorscalenames = array('gbmajor', 'fmajor', 'emajor', 'ebmajor', 'dmajor', 'dbmajor', 'cmajor', 'bmajor', 'bbmajor', 'amajor', 'abmajor', 'gmajor');
$notedirectories = array('sawtooth.125/', 'sawtooth.25/', 'sawtooth.5/', 'sawtooth1.0/');

	
$majorscales = array('emajor' => $emajor,'gmajor' => $gmajor);
$allmajorscales = array();
// translates emajor and gmajor into 48 note scales
foreach ($majorscales as $key => $scale) {
	$tmp = $scale;
	for ($i=1;$i<4;$i++) {
		foreach ($scale as $note) {
			$fullscalenote = ($note+(12*$i));
			array_push($tmp,$fullscalenote);
		}
	}
	array_push($tmp,48);
	$majorscales[$key]=$tmp;
		
	
}
$gmajorl = $majorscales['gmajor'];
$tmp3 = array();
//array_shift($tmp);
// creates all major scale keys from gmajor
for ($i=0;$i<11;$i++) {
	$allmajorscales[$majorscalenames[$i]]=array();
	//array_push($allmajorscales[$majorscalenames[$i]],0);
	$tmp3 = array();
	foreach ($gmajorl as $note) {

		$newnote=($note-($i+1));
		array_push($tmp3,$newnote);

	}
	//$tmp = $allmajorscales[$majorscalenames[$i]]
	//print_r($tmp3);
	$tmp2 = array();
	foreach ($tmp3 as $note) {
		if ($note>=0) {
			array_push($tmp2,$note);
		}
	}
	
	$tmp = array();
	for ($j=0;$j<8;$j++) {
		array_push($tmp,$tmp2[$j]);
	}
	$tmp4= $tmp;
	for ($j=1;$j<4;$j++) {
		foreach ($tmp as $note) {
			$fullscalenote = ($note+(12*$j));
			array_push($tmp4,$fullscalenote);
		}
	}
	if ($tmp4[count($tmp4)-1]==49) {
		array_pop($tmp4);
	}
	$allmajorscales[$majorscalenames[$i]] = $tmp4;
	/*
	for ($j=count($tmp)-1;$j<0;$j--) {
		if ($tmp[$j]<0) {
			
		}
	}
	*/
	
	//array_push($allmajorscales[$majorscalenames[$i]],48);
}
$allmajorscales['gmajor']=$majorscales['gmajor'];
// scale selection
if (isset($_POST['scale'])) {
	foreach ($majorscalenames as $scalename) {
		if ($_POST['scale']==$scalename) {
			$scalekey=$scalename;
			break;
		} else {
			$scalekey='gmajor';
		}
			
	}
} else {
	$scalekey = 'gmajor';
}
//start index selection
if (isset($_POST['startindex']) && is_numeric($_POST['startindex'])) {
	$startindex = $_POST['startindex'];	
	$scalelength = (count($allmajorscales[$scalekey])-1);
	if (($startindex+26)>$scalelength) {
		array_push($errorarray,'index exceeds (scale length)+26');
	}
} else {
	$startindex=0;
}
// select the note directory
if (isset($_POST['notetype']) && $_POST['notetype']!='') {
	$pnotedir = $_POST['notetype'];
	foreach ($notedirectories as $dir) {
		if ($dir==$pnotedir) {
			$notedir=$pnotedir;
			break;
		} else {
			$notedir=$notedirectories[0];
		}
	}
		
} else {
	$notedir=$notedirectories[0];
}
// create the file list for ffmpeg
$filenames = array();
$inputarray = array();
foreach ($allmajorscales[$scalekey] as $note) {
	$fname = sprintf('%04d.wav',($note+$startindex)); //add notedir
	array_push($filenames,$fname);
}
// input cleansing, the input string
if (isset($_POST['input']) && $_POST['input']!='') {
	$input=strtolower($_POST['input']);
	$tmps = '';
	$ilen = strlen($input);
	for ($i=0;$i<$ilen;$i++) {
		foreach ($alphabet as $key => $letter) {
			if ($input[$i]==$letter) {
				$tmps.=$letter;
				array_push($inputarray,$key);
			}
		}
	}
	$input = $tmps;
}
//$lastlog = file_get_contents($basedir.'moved.log');
//echo $lastlog;



if (isset($input)) {
	if (true) {
		if (strlen($input)<1000) {
			$tmp = array();
			foreach ($inputarray as $letterkey) {
				array_push($tmp,$filenames[$letterkey]);
			}
			$songfiles = $tmp;
			$filestring = '';
			foreach ($songfiles as $file) {
				$filestring.="file '".$basedir.$notedir.$file."'\n";
			}
			$listname = md5($input.$scalekey.$startindex);
			file_put_contents($basedir.$listname.'.txt',$filestring);
			$filename = $listname.'.wav';
			//$bash = 'cd '.$basedir.' && ffmpeg -y -f concat -i '.$basedir.'phplist.txt -c copy '.$basedir.'output2.wav 2>&1 > '.$basedir.'out.log';
			//$bash = 'cd '.$basedir.' && ffmpeg -y -f concat -i phplist.txt -c copy output2.wav 2>&1 > out.log';
			$bash = 'cd '.$basedir.' && ffmpeg -y -f concat -i '.$basedir.$listname.'.txt -c copy '.$filename.' > out.log 2> eout.log && mv -v '.$filename.' /var/www/html/music/'.$filename.' > /dev/null 2> /dev/null && echo -n 1 > moved.log 2> /dev/null';
			//$bash = 'ls 2>&1 > '.$basedir.'out.log';
			//file_put_contents($basedir.'moved.log','0');
			$str = shell_exec($bash);
			addinfo($filename,$input,$scalekey,$startindex,$connection);
			//$movefile = 'cd '.$basedir.' && mv -v output2.wav /var/www/html/music';
	
			//file_put_contents($basedir.'eout.log',"1",FILE_APPEND);
			?>

			<?php
			echo $str;
		} else {
			array_push($errorarray,'Input is over 1000 characters');
		}
	} else {
		array_push($errorarray,'Another file is working');
	}
}
/*
foreach ($allnotes as $note) {
echo $note;

}
*/
?>

<?php
/*
foreach ($majorscales as $scale) {
	/*
	foreach ($scale as $note) {
		echo $note.',';
	}
	
	//print_r($scale);
	echo '<br>';
}

*/
if (isset($filename)) {
?>
<a href="<?php echo $filename; ?>"><h1>Download</h1></a>
<br>

<?php
echo $input;
}
?>
<br>
<table>

<?php
	echo '<tr>';
	foreach ($alphabet as $letter) {
		echo '<td>'.$letter.'</td>';
	}
	echo '</tr><tr>';
	for ($i=0;$i<26;$i++) {
		echo '<td>'.$i.'</td>';
	}
	echo '</tr>';
?>
</table>
<!-- </pre> -->
Enter english alphabet letters you wish to be converted to music, max 1000 notes.<br>
<form method="post" action="">
<textarea name="input" cols="50" rows="20" placeholder="Text to music!"><?php
if (isset($input)) {
	echo $input;
}
?></textarea>

<br>
Scale <select name='scale'>
<?php
foreach ($majorscalenames as $majorscale) {
	if (isset($scalekey) && $scalekey==$majorscale) {
	echo '<option value="'.$majorscale.'" selected>'.$majorscale.'</option>';
	} else {
	echo '<option value="'.$majorscale.'">'.$majorscale.'</option>';
	}
}
?>
</select>
<br>
Note length <select name='notetype'>
<?php
foreach ($notedirectories as $note) {
	if (isset($notedir) && $notedir==$note) {
	echo '<option value="'.$note.'" selected>'.$note.'</option>';
	} else {
	echo '<option value="'.$note.'">'.$note.'</option>';
	}
}
?>
</select> all numbers are in seconds.
<br>
Start Index <input type="text" name="startindex" size="3" maxlength="2" value="<?php if (isset($startindex)) { echo $startindex; } ?>">
<br>
<input type="submit">
</form>
<?php
if (isset($input)) {
	//echo $input;
	?>
	<pre>
	<?php 
	//print_r($inputarray); 
	//print_r($songfiles);
	?>
	</pre>
	<?php
}
?>
<div class="content" style="word-wrap: break-word;">
<h2>Recent Files</h2>
<br>
<?php
/*
$files = scandir('/var/www/html/music');


foreach ($files as $file) {
	if ($file != 'index.php' && $file != '.' && $file != '..') {
	echo '<a href="'.$file.'">'.$file.'</a><br>';
	}
}
*/
getrecent($connection);
?>
<hr>
<?php
include '../stats.php';
?>
</div>
</div>
</div>
<pre>
<?php print_r($allmajorscales); ?>
</pre>
</body>