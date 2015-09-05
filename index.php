<?php
//modtest
//echo fmod(0.075,0.025);
//echo intval(0.076/0.025);
$scriptfilename = $_SERVER["SCRIPT_FILENAME"];
$startTime=microtime(true);
$database = 'log';
include '../log.php';
include '../mysql.php';
function addquot($instring) {
	return str_replace('"','&quot;',$instring);
}
function addinfo($file,$in,$scalekey,$notedir,$notelength,$connection) {

	$f=mysqli_real_escape_string($connection,$file);
	$i=mysqli_real_escape_string($connection,$in);
	$scalekey=mysqli_real_escape_string($connection,$scalekey);
	//$startindex=mysqli_real_escape_string($connection,$startindex);
	$notedir=mysqli_real_escape_string($connection,$notedir);
	$notelength=mysqli_real_escape_string($connection,$notelength);
	$q="insert into musiclist (file,text,scalekey,notedir,notelength) values ('".$f."','".$i."','".$scalekey."','".$notedir."','".$notelength."')";
	mysqli_query($connection,$q);
}
function getrecent($connection) {
	$q='select file,text,time,scalekey,notedir,notelength from musiclist order by time desc limit 10';
	$result = mysqli_query($connection,$q);
	while ($row = mysqli_fetch_assoc($result)) {
		echo $row['time'].' Scale: <span style="color:#aa33aa;">'.$row['scalekey'].'</span> Note Type: <span style="color:#aa3333;">'.$row['notedir'].'</span> Note Length: <span style="color:orange;">'.$row['notelength'].'</span><br><a href="'.$row['file'].'">'.$row['text'].'</a><br>';
		echo "\n";
	}
}
function return11ScalesFromG($gscale,$scalename) {
$musicalnotation = array('gb', 'f&nbsp;', 'e&nbsp;', 'eb', 'd&nbsp;', 'db', 'c&nbsp;', 'b&nbsp;', 'bb', 'a&nbsp;', 'ab', 'g&nbsp;');
$gslength=count($gscale);
$tmp3 = array();
$thescales = array();
$tmpfullgscale=$gscale;
//for ($i=1;$i<10;$i++) {
$i=1;
while ((count($tmpfullgscale)+1)<49) {
	
		foreach ($gscale as $note) {
			$fullscalenote = ($note+(12*$i));
			//echo $fullscalenote.',';
			array_push($tmpfullgscale,$fullscalenote);
		}
		//array_push($tmpfullgscale,48);
$i++;
}
$fullgscale=$tmpfullgscale;
//echo 'fullgscalee';
//print_r($fullgscale);
//echo $gslength.' gslength';
for ($i=0;$i<12;$i++) {

	$tmp3 = array();
	foreach ($fullgscale as $note) {

		$newnote=($note-($i+1));
		array_push($tmp3,$newnote);

	}

	$tmp2 = array();
	foreach ($tmp3 as $note) {
		if ($note>=0) {
			array_push($tmp2,$note);
		}
	}
	//print_r($tmp2);
	$tmp = array();
	for ($j=0;$j<($gslength);$j++) {
		array_push($tmp,$tmp2[$j]);
	}
	$tmp4= $tmp;
	for ($j=1;$j<4;$j++) {
		foreach ($tmp as $note) {
			$fullscalenote = ($note+(12*$j));
			array_push($tmp4,$fullscalenote);
		}
	}
	for ($j=0;$j<count($tmp4);$j++) {
		if ($tmp4[$j]>48) {
			unset($tmp4[$j]);
		}
	}
	if ($tmp4[0]==0) {
		array_push($tmp4,48);
	}
	/*
	if ($tmp4[count($tmp4)-1]==49) {
		array_pop($tmp4);
	}
	*/
	$thescales[$musicalnotation[$i].$scalename]=$tmp4;

}
//$thescales['g'.$scalename]=$gscale;
return $thescales;
}
$errorarray = array();
$alphabet = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
$allnotes = array("0000.wav","0001.wav","0002.wav","0003.wav","0004.wav","0005.wav","0006.wav","0007.wav","0008.wav","0009.wav","0010.wav","0011.wav","0012.wav","0013.wav","0014.wav","0015.wav","0016.wav","0017.wav","0018.wav","0019.wav","0020.wav","0021.wav","0022.wav","0023.wav","0024.wav","0025.wav","0026.wav","0027.wav","0028.wav","0029.wav","0030.wav","0031.wav","0032.wav","0033.wav","0034.wav","0035.wav","0036.wav","0037.wav","0038.wav","0039.wav","0040.wav","0041.wav","0042.wav","0043.wav","0044.wav","0045.wav","0046.wav","0047.wav");
$basedir = '/home/mouse/ffmpegwork/';
$allscales = array();
$emajor = array(0,2,4,5,7,9,11);
$gmajor = array(0,2,3,5,7,8,10);
$gmajorarpeggio= array(3,7,10);
$gpentatonicmajor = array(0,3,5,7,10);
$gscales = array(
	' Major' => array(0,2,3,5,7,8,10),
	' Minor' => array(2,3,5,6,8,10,11),
	' Pentatonic Major' => array(0,3,5,7,10),
	' Pentatonic Minor' => array(1,3,6,8,10),
	' Major Blues' => array(0,3,5,6,7,10),
	' Major Arpeggio' => array(3,7,10),
	' Minor Arpeggio' => array(3,6,10),
	' Bebop Major' => array(0,2,3,5,7,8,10,11),
	' Arabian (a)' => array(0,2,3,5,6,8,9,11),
	' Arabian (b)' => array(1,3,5,7,8,9,11),
	' Wholetone' => array(1,3,5,7,9,11),
	' Augmented' => array(3,7,11),
	' Divide Octave' => array(3,9)
);
//$majorscalenames = array('gbmajor', 'fmajor', 'emajor', 'ebmajor', 'dmajor', 'dbmajor', 'cmajor', 'bmajor', 'bbmajor', 'amajor', 'abmajor', 'gmajor');
//$notedirectories = array('sawtooth.125/', 'sawtooth.25/', 'sawtooth.5/', 'sawtooth1.0/');
$notedirectories = array('sawtooth-fade','sawtooth-hard');
$notelengths = array();

//test return11ScalesFromG($gscale,$scalename)
//echo '<pre>';
//print_r(return11ScalesFromG($gpentatonicmajor,'pentatonicmajor'));
//print_r(return11ScalesFromG($gmajorarpeggio,'majorarpeggio'));


//////////////////////////////////////////////

foreach ($notedirectories as $dir) {
	$scandirarray = scandir($basedir.'/'.$dir);
	$scandirtmp=array();
	foreach ($scandirarray as $scandir) {
	if ($scandir!='..' && $scandir!='.') {
		array_push($scandirtmp,$scandir);
	}
	}
	$notelengths[$dir]=$scandirtmp;
}


//echo '</pre>';
// add chromatic variable names don't make sense
$chromaticscale= array();
for ($i=0;$i<49;$i++) {
	array_push($chromaticscale,$i);
}
$allscales['Chromatic']=$chromaticscale;
foreach ($gscales as $scalename => $scale) {
$genscalearr = return11ScalesFromG($scale,$scalename);
	foreach ($genscalearr as $genscalename => $genscale) {
		$allscales[$genscalename]=$genscale;
	}
}
//array_push($majorscalenames,'chromatic');

//$allmajorscales['gmajor']=$majorscales['gmajor'];

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
table {
table-layout:fixed;

}
div.centerdiv {
text-align:center;
}
div.mobilebutton div.content {
background-color:rgba(85,100,85,255) !important;
}
textarea {
	width:99%;
	margin-left:auto;
	margin-right:auto;
	background-color:#262626;
	color:#ffffff;
	text-align:left;
}
table, td, tr {
	table-layout:fixed;
	border-collapse:collapse;
}
.alphabet td {
	border-left:1px solid #ffffff;
	padding:2px;
	font-family:monospace;
}
.guitar tr.gnumbers td {
	color:#ffffff !important;
	cursor:default;
}
.guitar td {
	text-align:center;
	color:#33ff33;
	border-style: solid;
	border-width:1px;
	border-color:#ffffff;
	cursor:pointer;
	font-family:monospace;
	font-size:1.2em;
	padding:0;
	margin:0;
}
.guitar td:nth-of-type(25n-24) {
	
	border-style:none;

}
.box {
	padding:3px;
	border:dashed 1px rgb(127,127,127);
}
.fret {
	font-size:1.2em;
	color:#ff6666;
}

@media only screen and (max-width: 500px) {
.fret, .guitar td { 
	font-size:.9em;
	padding:0;

}


}
@media only screen and (max-width: 450px) {
.fret, .guitar td { 
	font-size:.8em;
	padding:0;
}
}
@media only screen and (max-width: 340px) {
.fret, .guitar td { 
	font-size:.7em;
	padding:0;
}
}
@media only screen and (max-width: 290px) {
.fret, .guitar td { 
	font-size:.6em;
	padding:0;
}
}
.transpose {
	font-size:1.2em;
	color:#6666ff;
}
.reverse {
	font-size:1.2em;
	color:#ff7e00;
}
.insert {
	font-size:1.2em;
	color:#ffff00;
}
.shuffle {
	font-size:1.2em;
	color:#cc66ff;
}
div.box {
	border:dashed 1px rgb(127,127,127);
	margin-top:2px;
	margin-bottom:2px;
}
div.error {
	font-size:2em !important;
	color:#ff0000;
}
input {
font-family:monospace;
}

</style>
<script type="text/javascript">
function init() {
	windowwidth = window.innerWidth;
	transposei = document.getElementById("transpose");
	semitones = document.getElementById("semitones");
	reversei = document.getElementById("reverse");
	scales = [];
	alphabet = [<?php 
	foreach ($alphabet as $letter) {
		if ($alphabet[count($alphabet)-1]==$letter) {
			echo '"'.$letter.'"';
		} else {
			echo '"'.$letter.'",';
		}
	}
	?>];
	notetypes = [<?php
	foreach ($notedirectories as $jnotetype) {
		if ($notedirectories[count($notedirectories)-1]==$jnotetype) {
			echo '"'.$jnotetype.'"';
		} else {
			echo '"'.$jnotetype.'",';
		}
	}
	?>];
	<?php 
	foreach ($allscales as $jskey => $jsscale) {
		echo 'scales["'.str_replace('&nbsp;',' ',$jskey).'"]=[';
		foreach ($jsscale as $jsnote) {
			if ($jsscale[count($jsscale)-1]==$jsnote) {
				echo ''.$jsnote.'];';
			} else {
				echo ''.$jsnote.',';
			}
		}
		echo "\n";
	}
	?>
	scalenotenames=[];
	scalekey='Chromatic';
	for (i=0;i<scales[scalekey].length;i++) {
			scalenotenames[i] = alphabet[scales[scalekey][i]];
	}
	allas = document.getElementsByTagName("a");
	input = document.getElementById("input");
	for (i=0;i<(allas.length-1);i++) {
		if (allas[i].getAttribute("class")=="letter") {
			//document.body.innerHTML+=i;
			allas[i].addEventListener('click',function (event) { input.value+=event.target.innerHTML; });
		}
	}
	alltds = document.getElementsByTagName("td");
	for (i=0;i<(alltds.length);i++) {
		if (alltds[i].getAttribute("class")=="letter") {
			//document.body.innerHTML+=i;
			alltds[i].addEventListener('click',function (event) { input.value+=event.target.innerHTML; });
		}
	}
	frettds = [];
	for (i=0;i<25;i++) {
		frettds[i]=document.getElementById("g"+i);
	}
	firstfret=true;
	fretlengths=[]
	for (i=0;i<25;i++) {
		if (i!=0) {
			//frettds[i].style.width=((25-i)/5)+"em";
			fretboardlength=windowwidth*2;
			//frettds[i].style.width=((25-i)/10)+"em";

			
			if (firstfret==true) {
				previousfretlength=(fretboardlength-(fretboardlength/(Math.pow(2,(i/12)))));
				frettds[i].style.width=(fretboardlength-(fretboardlength/(Math.pow(2,(i/12)))))+"px";
				firstfret=false;	
			} else {		

				frettds[i].style.width=((fretboardlength-(fretboardlength/(Math.pow(2,(i/12)))))-previousfretlength)+"px";
				previousfretlength=(fretboardlength-(fretboardlength/(Math.pow(2,(i/12)))));


			}
		}
	}

	changefrets();
	charcount();
	setInterval(function () {charcount();},1000);
	disablenotelength();
	selectnotelengths(document.getElementById("notetype"));
	disableother(document.getElementById("randomnotelength"));
	disableother(document.getElementById("sequence"));

	document.getElementById("showhidetools").addEventListener("click", function () { hideshow(document.getElementById("showhidetools"),document.getElementById("tools")); console.log(1); });
	document.getElementById("generaterandom").addEventListener("click", function () { generaterandom() });
	document.getElementById("generaterandomnorepeat").addEventListener("click", function () { generaterandom(true) });

}
function disablenotelength() {
	randomnotelength = document.getElementById("randomnotelength");
	
	for (i=0;i<notetypes.length;i++) {
			
		if (randomnotelength.checked==true) {
			document.getElementById(notetypes[i]+"-notelengths").disabled=true;
		} else {
			document.getElementById(notetypes[i]+"-notelengths").disabled=false;
		}
	}
}
function charcount() {
	//chars = input.value.length
	chars=0;	
	for (i=0;i<input.value.length;i++) {
		
		inputsub=input.value.substr(i,1);
		//console.log(inputsub);		
		for (j=0;j<alphabet.length;j++) {
			if (inputsub==alphabet[j]) {
				chars++;
			}
		}
	}
	document.getElementById("charcount").innerHTML="Char Count:"+chars;
}
function mapchartofrets() {

}
function changefrets() {
	scalekey = document.getElementById('guitarscales').value;
	
		
	for (j=0;j<(alltds.length);j++) {
		if (alltds[j].getAttribute("class")=="letter") {
			
			alltds[j].style.color="#aaaaaa";
			alltds[j].style.backgroundColor="#131926";
			
		}
	}
		
	
	for (i=0;i<scales[scalekey].length;i++) {
		
		for (j=0;j<(alltds.length);j++) {
			if (alltds[j].getAttribute("class")=="letter") {
				if (alltds[j].innerHTML==alphabet[scales[scalekey][i]]) {
					alltds[j].style.color="#33ff33";
					alltds[j].style.backgroundColor="#556455";
					//document.body.innerHTML+=scales[scalekey][i];
				}
			}
		}
		
	}
	scalenotenames=[];
	for (i=0;i<scales[scalekey].length;i++) {
			scalenotenames[i] = alphabet[scales[scalekey][i]];
			console.log(alphabet[scales[scalekey][i]]);
	}
		
}
function transposenotes(add) {
	transposearr = [];
	tinput = transposei.value;
	s = parseInt(semitones.value);

	for (i=0;i<tinput.length;i++) {
		letter=tinput.substring(i,i+1);
		//transposearr[i]=tinput.substring(i,i+1)
		for (j=0;j<alphabet.length;j++) {
			if (letter==alphabet[j]) {
				if ((j+s)<49 && (j+s)>=0) {
					//alert(j+s);
					transposearr[transposearr.length]=j+s;
				}
			}
		}
	}
	/*
	tmp = [];
	for (i=0;i<transposearr.length;i++) {
		j=0;
		alert(transposearr[i]);
		if (transposearr[i]<52 && transposearr[i]>=0) {
			tmp[j]=transposearr[i];
			j++;
		}
	}
	*/
	//alert(alphabet.length);
	//alert(input.length);
	//alert(transposearr);
	//alert(tmp);
	tstring = ''
	for (i=0;i<transposearr.length;i++) {
		tstring+=alphabet[transposearr[i]];
	}
	if (add==true) {
		input.value+=tstring;
		transposei.value=tstring;
	} else {
		transposei.value=tstring;
	}
	charcount();
}
function reverseString(add) {
	inputr = reversei.value;
	output = ''
	//alert(inputr.length);
	for (i=(inputr.length-1);i>=0;i--) {
		//alert(inputr.substr(i,i+1)+i);
		output+=inputr.substr(i,1);
		
	}
	if (add==true) {
		input.value+=output;
		reversei.value=output;
	} else {
		reversei.value=output;
	}
	charcount();
}
function insertString(add) {
	//<textarea id="insert"></textarea> Into <textarea id="insertinto"></textarea> Every <input type="text" placeholder="Every X Characters" id="insertevery">
	instring = document.getElementById("insert").value;
	intostring = document.getElementById("insertinto");
	
	everyx = parseInt(document.getElementById("insertevery").value);
	finishedstring = instring;
	finishedstring=''
	intostringlength = intostring.value.length;
	if (everyx<1) {
		everyx=1;
	}
	
	for (i=0;i<intostringlength;i+=everyx) {
		//if (i==0) {
		//	j=1;
		//} else {
			
			j=i+everyx;
		//}
		console.log(j+","+i);
		finishedstring+=instring+intostring.value.substring(i,j);
		//finishedstring+=intostring.value.substr((((i*everyx)-((1*i)))/(everyx+1)),(everyx))+instring;
		//console.log(((i*everyx)-((1*i))));
	}
	//finishedstring+=instring;
	if (add==true) {
		input.value+=finishedstring;
		intostring.value=finishedstring;
	} else {
		intostring.value=finishedstring;
	}
		
}
function selectnotelengths(notetype) {
	hideidpart=notetype.value
	for (i=0;i<notetypes.length;i++) {
		if (hideidpart!=notetypes[i]) {
			document.getElementById(notetypes[i]+"-notelengths").style.display="none";
			document.getElementById(notetypes[i]+"-notelengths").setAttribute("name","notelength-disabled");
		}
	}
	document.getElementById(hideidpart+"-notelengths").style.display="inline";
	document.getElementById(hideidpart+"-notelengths").setAttribute("name","notelength");
}
function disableother(object) {
	current = object.getAttribute("name");
	/*
Between <input id="sequencestart" type="text" value=".05"> and <input id="sequenceend" type="text">
<br>
Multiples of <input id="sequencemultiples" type="text"> offset by <input id="sequencemultiplesoffset" type="text" value="0"> <input type="button" value="GO" onclick="generatesequence()"> <input type="button" value="Add" onclick="generatesequence(true)">
*/
	if (current=='sequence' && object.checked==true) {
		document.getElementById("notelengths");
		for (i=0;i<notetypes.length;i++) {
			document.getElementById(notetypes[i]+"-notelengths").disabled=true;
		}
		document.getElementById("randomnotelength").checked=false;
		document.getElementById("randomstart").disabled=true;
		document.getElementById("randomend").disabled=true;
		document.getElementById("multiples").disabled=true;
	}
	if (current=='sequence' && object.checked==false) {
		document.getElementById("sequencestart").disabled=true;
		document.getElementById("sequenceend").disabled=true;
		document.getElementById("sequencemultiples").disabled=true;
		document.getElementById("sequencemultiplesoffset").disabled=true;
	}
	
	
	if (current=='randomnotelength' && object.checked==true) {
		for (i=0;i<notetypes.length;i++) {
			document.getElementById(notetypes[i]+"-notelengths").disabled=true;
		}
		document.getElementById("sequence").checked=false;
		document.getElementById("randomstart").disabled=false;
		document.getElementById("randomend").disabled=false;
		document.getElementById("multiples").disabled=false;
	}
	if (document.getElementById('sequence').checked==false) {
		document.getElementById("randomstart").disabled=false;
		document.getElementById("randomend").disabled=false;
		document.getElementById("multiples").disabled=false;
	} else {
		document.getElementById("sequencestart").disabled=false;
		document.getElementById("sequenceend").disabled=false;
		document.getElementById("sequencemultiples").disabled=false;
		document.getElementById("sequencemultiplesoffset").disabled=false;
	}
	if (document.getElementById('randomnotelength').checked==false) {
		document.getElementById("randomstart").disabled=true;
		document.getElementById("randomend").disabled=true;
		document.getElementById("multiples").disabled=true;
	}
	if (document.getElementById('randomnotelength').checked==false && document.getElementById('sequence').checked==false) {
		for (i=0;i<notetypes.length;i++) {
			document.getElementById(notetypes[i]+"-notelengths").disabled=false;
		}
	}


}
function generatesequence(add) {

	sequencestart = document.getElementById("sequencestart");
	sequenceend = document.getElementById("sequenceend");
	sequencemultiples = document.getElementById("sequencemultiples");
	sequencemo = document.getElementById("sequencemultiplesoffset");
	sequencelist = document.getElementById("sequencelist");
	completesequence='';
	
	if (parseFloat(sequencestart.value)*1000<parseFloat(sequenceend.value)*1000+1) {
		console.log("inloop");
		for (i=parseFloat(sequencestart.value)*1000;i<(parseFloat(sequenceend.value)*1000)+1;i+=1) {
		console.log(i);
		console.log((i+(parseFloat(sequencemo.value)*1000))%(parseFloat(sequencemultiples.value)*1000));
			if ((i)%((parseFloat(sequencemultiples.value)*1000)+(parseFloat(sequencemo.value)*1000))==0) {
				console.log("inif");
				completesequence+=''+(i/1000)+',';
			}
		}
	}
	console.log(completesequence);
	completesequence=completesequence.substr(0,(completesequence.length-1));
	if (add==true) {
		sequencelist.value+=','+completesequence;
	} else {
		sequencelist.value=completesequence;
		
	}
}
function hideshow(changetextelement,object) {
		console.log(changetextelement,object.style.display);
		
	if (object.style.display=="none") {
		object.style.display="block";	
		changetextelement.innerHTML="Hide Tools";
	} else {
		object.style.display="none";
		changetextelement.innerHTML="Show Tools";
	}
}
function generaterandom(norepeat) {
rlength = document.getElementById("randomlength").value;
i=0;
while (i<rlength) {
	if (i>0) {
		lastnotetmp=lastnote;
	}

	lastnote=Math.round(Math.random()*(scalenotenames.length-1));

	if (i>0) {	
		if (norepeat==true) {
			if (lastnotetmp==lastnote) {
				continue;
			}
		}
	}
	input.value+=scalenotenames[lastnote];
	i++;
}
}
////http://bost.ocks.org/mike/shuffle/
function shufflea(array) {
  var currentIndex = array.length, temporaryValue, randomIndex ;

  // While there remain elements to shuffle...
  while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }

  return array;
}
//////////////
function shuffletext(add) {
	shufflet = document.getElementById("shuffle");
	shufflelength = shufflet.value.length;
	shufflearray=[];
	for (i=0;i<shufflelength;i++) {
		shufflearray[i]=shufflet.value[i];
	}
	shufflearray=shufflea(shufflearray);
	shufflet.value='';
	finishedstring='';
	for (i=0;i<shufflelength;i++) {
	finishedstring=finishedstring+shufflearray[i];
	}
	shufflet.value=finishedstring;
		if (add==true) {
			input.value+=finishedstring;
		}
	//console.log(shufflea(shufflearray));	
	/*	
	for (i=0; i<shufflelength-1; i++) {
		shufflearray[i]=Math.round((shufflelength-1)*Math.random())
	}
	
	shuffleduplicates=1;
	while (shuffleduplicates>=1) {
	shuffleduplicates=0;
		for (i=0; i<shufflelength-1; i++) {
			for (j=0; j<shufflelength-1; j++) {
				if (shufflearray[j]==shufflearray[i]) {
					shufflearray[i]=Math.round((shufflelength-1)*Math.random());
					shuffleduplicates++;
				}
			}
		}
	}
	*/
	//alert(shufflearray);
		
}
</script>
</head>
<body onload="init()">
<div class="page">
<header>
	<h1 class="ross"><a href="/">Ross Carley</a></h1><h2> > Music Generator</h2>
</header>
<a href="catalog.php?page=1&limit=25">
<div class="content" style="word-wrap: break-word;margin:0;background-color:rgba(85,100,85,255);">

<h2 style="color:#00ff00 !important;">View Catalog</h2>

</div>
</a>
<div class="content">
<!-- <pre> -->

<?php

// scale selection
if (isset($_POST['scale'])) {
	foreach ($allscales as $scalename => $scale) {
		if ($_POST['scale']==$scalename) {
			$scalekey=$scalename;
			break;
		} else {
			$scalekey='Chromatic';
		}
			
	}
} else {
	$scalekey = 'Chromatic';
}
//start index selection
/*
if (isset($_POST['startindex']) && is_numeric($_POST['startindex'])) {
	$startindex = $_POST['startindex'];	
	$scalelength = (count($allmajorscales[$scalekey])-1);
	if (($startindex+52)>$scalelength) {
		array_push($errorarray,'index exceeds (scale length)+52');
	}
} else {
	$startindex=0;
}
*/
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
//select note length
if (isset($_POST['notelength']) && $_POST['notelength']!='') {
	$pnotelength = $_POST['notelength'];
	foreach ($notelengths[$notedir] as $dir) {
		if ($dir==$pnotelength) {
			$notelength=$pnotelength;
			break;
		} else {
			$notelength=$notelengths[$notedir][0];
		}
	}
		
} else {
	$notelength=$notelengths[$notedir][0];
}
//sequence selection
if (isset($_POST['sequence']) && $_POST['sequence']=='true' && !isset($_POST['randomnotelength'])) {
	$sequencestring=$_POST['sequencelist'];
	$psequencearray = explode(',',$_POST['sequencelist']);
	//print_r($psequencearray);
	if (count($psequencearray)>0) {
		$sequencearray = array();	
		foreach ($psequencearray as $seqnotelength) {
			foreach ($notelengths[$notedir] as $dir) {
				if (floatval($seqnotelength)==floatval($dir)) {
					array_push($sequencearray,$dir);
				}
			}
		}
		//print_r($sequencearray);
		$notelength='Sequence';
	} else {
		array_push($errorarray,'Sequence not defined');
	}
				
}
// create the file list for ffmpeg
$filenames = array();
$inputarray = array();
$startindex=0;
foreach ($allscales[$scalekey] as $note) {
	$fname = sprintf('%04d.wav',($note+$startindex)); //add notedir
	array_push($filenames,$fname);
}
//print_r($filenames);
// input cleansing, the input string
if (isset($_POST['input']) && $_POST['input']!='') {
	$textareasave = str_replace('"','&quot;',$_POST['input']);
	$input=($_POST['input']);
	$tmps = '';
	$ilen = strlen($input);
	for ($i=0;$i<$ilen;$i++) {
		foreach ($alphabet as $key => $letter) {
			if ($input[$i]==$letter) {
				if ($key<count($filenames)) {
					
				$tmps.=$letter;
				array_push($inputarray,$key);
				} else {
					
					$currentscalelength = count($filenames);
					
					$key=$key%($currentscalelength);
					$tmps.=$letter;
					array_push($inputarray,$key);
				}
			}
		}
	}
	$input = $tmps;
	if ($input=='') {
		unset($input);
		array_push($errorarray,'No Parsable Input <div style="font-size:1em !important !important">Input needs to contain at least one letter a-z or A-Z</div>');
	}
}
if (isset($_POST['input']) && $_POST['input']=='') {
	array_push($errorarray,'Empty Input');
}
//$lastlog = file_get_contents($basedir.'moved.log');
//echo $lastlog;

//random note length
if (isset($_POST['randomnotelength']) && $_POST['randomnotelength']=='true' && !isset($sequence)) {
	if (isset($_POST['randomstart']) && isset($_POST['randomend'])) {
		if (is_numeric($_POST['randomstart']) && is_numeric($_POST['randomend'])) {
			$prandomstart=floatval($_POST['randomstart']);
			$prandomend=floatval($_POST['randomend']);
			if ($prandomstart<$prandomend) {
				foreach ($notelengths[$notedir] as $rnotekey => $rnotelength) {
					$rnotelength=floatval($rnotelength);
					if ($rnotelength==$prandomstart) {
						$randomstart=$rnotelength;
						$randomstartkey=$rnotekey;
						//array_push($errorarray,'Start length Invalid');
						//break;
					}
					if ($rnotelength==$prandomend) {
						$randomend=$rnotelength;
						$randomendkey=$rnotekey+1;
						//array_push($errorarray,'End length Invalid '.$randomend.' '.$rnotelength);
						//break;
					}
				

				}
				if (isset($randomstart) && isset($randomend) && isset($randomstartkey) && isset($randomendkey)) {
					if (isset($_POST['multiples']) && is_numeric($_POST['multiples'])) {
						$prmultiples=floatval($_POST['multiples']);
						$multipleskeyarray=array();
						for ($i=$randomstartkey;$i<$randomendkey;$i++) {
							//echo (($notelengths[$notedir][$i])/$prmultiples).'<br>';
							//if (fmod(floatval($notelengths[$notedir][$i]),$prmultiples)==0) {
							$currentnote = floatval($notelengths[$notedir][$i])*1000;
							$multipletest = $prmultiples*1000;
							if ($currentnote%$multipletest==0) {

								//echo fmod(floatval($notelengths[$notedir][$i]),$prmultiples);
								//echo $notelengths[$notedir][$i].' '.$prmultiples.' '.$i.', '.$randomstart.' '.$randomend.','.$randomstartkey.' '.$randomendkey.',,'.(($notelengths[$notedir][$i])/$prmultiples);
								echo ($notelengths[$notedir][$i]).',';
								array_push($multipleskeyarray,$i);
								$multiplesexist=true;
							}
								
						}
						if (isset($multiplesexist) && $multiplesexist==true) {
							$inputarraycount = count($inputarray);
							$randomnotelengths = array();
							for ($i=0;$i<$inputarraycount;$i++) {
								$randommultipleindex=rand(0,(count($multipleskeyarray)-1));
								array_push($randomnotelengths,$notelengths[$notedir][$multipleskeyarray[$randommultipleindex]]);
							}
						} else {
							array_push($errorarray,'Multiples of '.$prmultiples.' do not exist between '.$randomstart.' and '.$randomend);
						}
					} else {
						$inputarraycount = count($inputarray);
						$randomnotelengths = array();
						for ($i=0;$i<$inputarraycount;$i++) {
							$randomindex = rand($randomstartkey,($randomendkey));
							array_push($randomnotelengths,$notelengths[$notedir][$randomindex]);
						}
					}
					
				} else {
					array_push($errorarray,'Start or End note Length Invalid');
				}
			} else {
				array_push($errorarray,'Start note Length greater than end note length');
			}
			
				
		} else {
			array_push($errorarray,'Start or End note Length Non-Numeric');
		}
	} else {
		$inputarraycount = count($inputarray);
		$notelengthscount = count($notelengths[$notedir]);
		$randomnotelengths = array();
		for ($i=0;$i<$inputarraycount;$i++) {
			$randomindex = rand(0,($notelengthscount-1));
			array_push($randomnotelengths,$notelengths[$notedir][$randomindex]);
		}
	}
}
//print_r($randomnotelengths);
if (isset($randomnotelengths)) {
	$notelength='Random';
}
if ($errorarray==array()) {
	if (isset($input)) {
		if (true) {
			if (strlen($input)<2001) {
				$tmp = array();
				foreach ($inputarray as $letterkey) {
					array_push($tmp,$filenames[$letterkey]);
				}
				$songfiles = $tmp;
				$filestring = '';
			
				$foreachc=0;
				if (isset($sequencearray)) {
					$sequencecount=count($sequencearray);
				}
				foreach ($songfiles as $file) {
					if (isset($randomnotelengths)) {
						$filestring.="file '".$basedir.$notedir.'/'.$randomnotelengths[$foreachc].'/'.$file."'\n";
						$foreachc++;
					} else {
						if (isset($sequencearray)) {
						
							$filestring.="file '".$basedir.$notedir.'/'.$sequencearray[fmod(($foreachc+$sequencecount),($sequencecount))].'/'.$file."'\n";
							//echo (fmod(($foreachc+($sequencecount)),($sequencecount))).'<br>';
							//echo $sequencecount;
							$foreachc++;
						} else {
							$filestring.="file '".$basedir.$notedir.'/'.$notelength.'/'.$file."'\n";
						}
					}
				}
				if (isset($randomnotelengths)) {
					$notelength='Random';
					$listname = md5($input.$scalekey.$startindex.$notelength.$notedir.$startTime);
				} elseif (isset($sequencearray)) {
					$listname = md5($input.$scalekey.$startindex.$notelength.$notedir.$sequencestring);
				} else {
					$listname = md5($input.$scalekey.$startindex.$notelength.$notedir);
				}
				file_put_contents($basedir.$listname.'.txt',$filestring);
				$filename = $listname.'.wav';
				//$bash = 'cd '.$basedir.' && ffmpeg -y -f concat -i '.$basedir.'phplist.txt -c copy '.$basedir.'output2.wav 2>&1 > '.$basedir.'out.log';
				//$bash = 'cd '.$basedir.' && ffmpeg -y -f concat -i phplist.txt -c copy output2.wav 2>&1 > out.log';
				if ($scriptfilename=='/var/www/local/music/index.php') {
					$info=false;
					$movedir = '/var/www/local/music/';
				} else {
					$info=true;
					$movedir = '/var/www/html/music/';			
				}
				//echo $movedir.$filename;
				$bash = 'cd '.$basedir.' && ffmpeg -y -f concat -i '.$basedir.$listname.'.txt -c copy '.$filename.' > out.log 2>> eout.log && mv -v '.$filename.' '.$movedir.$filename.' > /dev/null 2> /dev/null && echo -n 1 > moved.log 2> /dev/null';
				//$bash = 'ls 2>&1 > '.$basedir.'out.log';
				//file_put_contents($basedir.'moved.log','0');
				$str = shell_exec($bash);
				if ($info) {
					addinfo($filename,$input,$scalekey,$notedir,$notelength,$connection);
				}
				//$movefile = 'cd '.$basedir.' && mv -v output2.wav /var/www/html/music';
	
				//file_put_contents($basedir.'eout.log',"1",FILE_APPEND);
				?>

				<?php
				echo $str;
			} else {
				array_push($errorarray,'Input is over 2000 characters');
			}
		} else {
			array_push($errorarray,'Another file is working');
		}
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
if ($errorarray!=array()) {
	foreach ($errorarray as $error) {
		echo '<div class="content error" style="margin:0;color:#ff0000 !important;background-color:#000000;">'.$error.'</div>';
	}
}
if (isset($filename)) {
?>
<h1><a href="<?php echo $filename; ?>">Download</a></h1>
<br>

<?php
echo $input;
}
?>
<?php /*

<table class="alphabet">

<?php
	echo '<tr>';
	foreach ($alphabet as $letter) {
		echo '<td><a href="javascript:void(0);" class="letter">'.$letter.'</a></td>';
	}
	echo '</tr><tr>';
	for ($i=0;$i<52;$i++) {
		if ($i>48) {
		echo '<td>'.(($i-1)%48).'</td>';
		} else {
		echo '<td>'.$i.'</td>';
		}
	}
	echo '</tr>';
?>
</table>
*/ ?>
<div class="box">
<span class="fret">Fretboard</span>
Highlight Scale <select onchange="changefrets()" id="guitarscales" style="font-family:monospace;" onkeydown="changefrets()" onkeyup="changefrets()">
<?php
$replaceletters = array('&nbsp;','a','b','c','d','e','f','g',' ');

$foreachc=0;
foreach ($allscales as $scalename => $scale) {
	if ($foreachc==0) {
		$tmpscalename = str_replace($replaceletters,'',$scalename);
	}
	if ($tmpscalename!=str_replace($replaceletters,'',$scalename) || $scalename=='gb Arabian (b)') {
		echo '<option value="!">--------------------</option>';
	}
	if (isset($scalekey) && $scalekey==$scale) {
	echo '<option value="'.str_replace('&nbsp;',' ',$scalename).'" selected>'.$scalename.'</option>';
	} else {
	echo '<option value="'.str_replace('&nbsp;',' ',$scalename).'">'.$scalename.'</option>';
	}
	$foreachc++;
	$tmpscalename = str_replace($replaceletters,'',$scalename);

}
?>
</select>
<form method="post" action="">
<div>
Add random notes from highlighted scale, Length <input type="text" name="randomjslength" value="<?php if (isset($_POST['randomjslength'])) {echo addquot(strip_tags($_POST['randomjslength'])); } else { echo '100'; } ?>" size="5" maxlength="4" id="randomlength"> <input id="generaterandom" type="button" value="Generate"> <input type="button" id="generaterandomnorepeat" value="Generate (No Repeat)">
</div>

<table class="guitar">
<?php
$rows = 6;
$cols = 25;
	echo '<tr class="gnumbers">';	
	for ($h=0;$h<$cols;$h++) {
		if ($h<10) {
		echo '<td id="g'.$h.'">&nbsp;'.$h.'</td>';
		} else {
		echo '<td id="g'.$h.'">'.$h.'</td>';
		}
	}
	echo '</tr>';
$guitarindex = (5*5)-1;
$onnote=24;
	for ($i=0;$i<$rows;$i++) {
		echo "<tr>\n";
		for ($h=0;$h<$cols;$h++) {
						
				//$notenum=(($i+1)*$h);
				$notenum=$onnote+$h;
			
			echo '<td class="letter" id="notenum-'.$notenum.'">'.$alphabet[$h+$guitarindex].'</td>'."\n";
			
		}
		if ($i==1) {
		$guitarindex-=4;
		$onnote-=4;
		} else {
		$guitarindex-=5;
		$onnote-=5;
		}
		
		echo "</tr>\n";
	}
?>
</table>
</div>

<!-- </pre> -->
Enter english alphabet letters you wish to be converted to music, max 2000 notes.<br>

<div class="centerdiv">
<textarea onchange="charcount()" onkeydown="charcount()" onkeyup="charcount()" name="input" cols="50" rows="20" placeholder="Text to music!" id="input"><?php
if (isset($textareasave)) {
	echo $textareasave;
}
?></textarea>
</div>
<span id="charcount">Char Count:0</span><br>
<a href="javascript:void(0);" style="display:block;"><div id="showhidetools" class="content mobilebutton" style="background-color:rgba(85,100,85,255);">Hide Tools</div></a>
<div id="tools">
	<h2>Tools</h2>
	<br>
	<div class="box">
	<span class="transpose">Transpose</span>
	<br>
	Semitones <input type="text" value="0" id="semitones"> <input type="button" value="GO" onclick="transposenotes()"> <input type="button" value="Add" onclick="transposenotes(true)">
	<br>
	<div class="centerdiv">
	<textarea id="transpose" rows="3"></textarea>
	</div>
	</div>

	<div class="box">
	<span class="reverse">Reverse</span> <input type="button" value="GO" onclick="reverseString()"> <input type="button" value="Add" onclick="reverseString(true)">
	<br>
	<div class="centerdiv">
	<textarea id="reverse" rows="2"></textarea>
	</div>
	</div>
	<div class="box">
	<span class="insert">Insert</span> <input type="button" value="GO" onclick="insertString()"> <input type="button" value="Add" onclick="insertString(true)">
	<br>
	<div class="centerdiv">
	<textarea id="insert"></textarea></div> Into <div class="centerdiv"><textarea id="insertinto"></textarea></div> Every <input type="text" placeholder="Every X Characters" id="insertevery"> Characters

	</div>

	<div class="box">
		<span class="shuffle">Shuffle</span> <input type="button" value="GO" onclick="shuffletext()"> <input type="button" value="Add" onclick="shuffletext(true)">
		<br>

		<div class="centerdiv"><textarea id="shuffle"></textarea></div>
	</div>	
</div>
Scale <select name='scale' id="scales">
<?php
foreach ($allscales as $scalename => $scale) {
	if (isset($scalekey) && $scalekey==$scale) {
	echo '<option value="'.$scalename.'" selected>'.$scalename.'</option>';
	} else {
	echo '<option value="'.$scalename.'">'.$scalename.'</option>';
	}
}
?>
</select>
<br>
Sample Type <select name="notetype" onchange="selectnotelengths(this)" id="notetype">
<?php
foreach ($notedirectories as $note) {
	if (isset($notedir) && $notedir==$note) {
	echo '<option value="'.$note.'" selected>'.$note.'</option>';
	} else {
	echo '<option value="'.$note.'">'.$note.'</option>';
	}
}
?>
</select>
<br>
Note Length
<?php
foreach ($notelengths as $notetype => $lengths) {
	echo '<select name="notelength" id="'.$notetype.'-notelengths">';
	foreach ($lengths as $length) {
		if ($length!='.' && $length!='..') {
			if (isset($notelength) && $notelength==$length) {
				echo '<option value="'.$length.'" selected>'.$length.'</option>';
			} else {
				echo '<option value="'.$length.'">'.$length.'</option>';
			}
		}
		
	}
	echo '</select>';
}
?>
 all numbers are in seconds. 
<div class="box">
<input type="checkbox" onchange="disableother(this)" name="randomnotelength" value="true" id="randomnotelength" <?php if (isset($randomnotelengths)) { echo 'checked'; } ?>> Random<br>
Between <input name="randomstart" id="randomstart" type="text" value="<?php if (isset($randomstart)) { echo $randomstart;} else { echo '0.050'; } ?>"> and <input name="randomend" id="randomend" type="text" value="<?php if (isset($randomend)) { echo $randomend;} else { echo '0.100'; } ?>"><br>
Multiples of <input name="multiples" id="multiples" type="text" value="<?php if (isset($prmultiples)) { echo $prmultiples;} else { echo '0.050'; } ?>">
</div>
<div class="box">
<input type="checkbox" onchange="disableother(this)" name="sequence" value="true" id="sequence" <?php if (isset($sequencearray)) { echo 'checked'; } ?>> Sequence<br>
Generate
<br>
Between <input id="sequencestart" type="text" value=".05"> and <input id="sequenceend" type="text">
<br>
Multiples of <input id="sequencemultiples" type="text"> offset by <input id="sequencemultiplesoffset" type="text" value="0"> <input type="button" value="GO" onclick="generatesequence()"> <input type="button" value="Add" onclick="generatesequence(true)">
<div class="centerdiv">
<textarea name="sequencelist" id="sequencelist" rows="3"><?php
if (isset($sequencearray)) { 
	$foreachc=1;
	foreach ($sequencearray as $seqnote) {
		
		echo $seqnote;
		if (count($sequencearray)>$foreachc) {
			echo ',';
		}
		$foreachc++;
	} 
} 
?>
</textarea>
</div>
</div>
<?php /*
Start Index <input type="text" name="startindex" size="3" maxlength="2" value="<?php if (isset($startindex)) { echo $startindex; } ?>">
<br>
*/
?>
<input type="submit" value="Submit Query" style="font-size:2em;margin-bottom:1em;">
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
<div class="content" style="word-wrap: break-word;padding:0;">
<a href="catalog.php?page=1&limit=25">
<div class="content" style="word-wrap: break-word;margin:0;background-color:rgba(85,100,85,255);">

<h2 style="color:#00ff00 !important;">View Catalog</h2>

</div>
</a>
<h2>Recent Files</h2>
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
<a href="https://github.com/mouseboyx/php-music">View Source</a> on github.
<hr>
<?php
include '../stats.php';
?>
</div>
</div>
</div>
<?php /* ?>
<pre>
<?php print_r($allscales); ?>
</pre>
<?php */ ?>


</body>
