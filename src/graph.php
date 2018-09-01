<?php
//some example
//*****************example01********************
//graph_pie(array('测试'=>128,256,512,1024,2048,4096,8192,16384),31,'测试Test');
//*****************example02********************
/*
 $dat[]=125;
 $dat[]=256;
 $dat[]=2048;
 $dat['item1']=60;
 $dat['项目一']=15;
 graph_pie($dat,15,'测试Test',$saveto='graph_pie.png');
*/
//*****************example03********************
/*
 $im=garph_pie(array(2,8,6),15,'value');
 header('Content-type: image/png');
 imagepng($im);
 imagedestroy($im);
*/
//graph_bar(array(array('Mon'=>21,'Tue'=>42,'Wed'=>36,'Thu'=>4,'Fri'=>59),array(12,49,62,42,6)));
//graph_progress_bar(100,22);
//require_once 'graph.php';
//require_once 'graph.php';
function graph_pie($data,$flags=31,$title='',$saveto='page',$background='default',$bgresourcse='null') {
	//resourcse graph_pie(array $data,int $flags,string $title,string $saveto,string $background,resourcse $bgresourcse)
	//$data			定义数据，关键字为项目名，值为数据
	//$flags		标志，值为0-31，默认为31，转成二进制后可以看出:
	//					最低位指定是否启用3D效果，第二低位指定是否启用图例，第三低位指定是否启用标题，第四低位指定是否在输出到页面或文件后立即销毁资源，第五低位制定是否输出百分比
	//					所属位值为零时禁用，为一时启用
	//					如00000000 0000000 00000000 00001010（十进制10）表示无3D，带图例，无标题，销毁，无百分比
	//$title 		图表的标题，支持GB2312格式
	//$saveto		图标的输出位置,值为page直接输出图片，值为value时输出图像资源，其他则保存到文件（不支持中文）
	//$background	背景，值为default时使用白色400*300背景，值为value时使用$bgresourcse提供的图像资源，否则使用指定的文件
	//$bgresourcse	背景，由gd库创建的图像资源，仅在$background值为value时可用
	//preprocess data
	$flags+=32;
	$flags=decbin($flags);
	$f3d=substr($flags,-1,1);
	$fexam=substr($flags,-2,1);
	$ftitle=substr($flags,-3,1);
	$fdel=substr($flags,-4,1);
	$fpc=substr($flags,-5,1);
	//echo $f3d.'</br>'.$fexam.'</br>'.$ftitle.'</br>'.$fdel.'</br>'.$fpc.'</br>'.$flags;
	//die();
	$total=0;
	$item=0;
	foreach ($data as $val) {
	$total=$total+$val;
	}
	$item=count($data);
	$angle=array();
	foreach ($data as $val) {
		$angle[]=$val/$total*360;
	}
	//make black and white
	$black=imagecolorallocate($image,0,0,0);
	$back = imagecolorallocate($image,255,255,255);
	// create image
	if ($background=='default') {
		$image = imagecreatetruecolor(400, 300);
		imagefill($image,0,0,$back);
	}
	else if ($background=='value'){
		$image=$bgres;
	}
	else {
		$image=imagecreatefrompng ($background);
	}
	$color=allocate_colors($image);
	//set background color
	//define some values
	$angledat=0;
	$colorid=1;
	$iid=200;
	//make 3d
	if ($f3d==1) {
		for ($p=120;$p>105;$p--) {
			for ($i=0;$i<$item;$i++) {
				$angledata=$angle[$i];
				$angleda=$angledat+$angledata;
				imagefilledarc($image, $iid, $p, 250, 125, $angledat, $angleda, $color[$colorid], IMG_ARC_PIE);
				$angledat=$angledat+$angledata;
				$colorid=$colorid+2;
			}
			$angledat=0;
			$colorid=1;
		}
	}
	//finish pie
	$colorid=0;
	$angeldat=0;
	$angleda=0;
	$angledat=0;
	if ($f3d==0) {
		$wei=150;
		$hei=150;
		$pid=110;
	}
	else {
		$wei=250;
		$hei=120;
		$pid=105;
	}
	for ($i=0;$i<$item;$i++) {
		$angledata=$angle[$i];
		$angleda=$angledat+$angledata;
		imagefilledarc($image, $iid, $pid, $wei, $hei, $angledat, $angleda, $color[$colorid], IMG_ARC_PIE);
		$angledat=$angledat+$angledata;
		$colorid=$colorid+2;
	}
	//make description
	if ($fexam==1) {
		$x1=50;
		$y1=205;
		$y2=$y1+20;
		$x3=85;
		$y3=225;
		$colorid=0;
		$cid=0;
		$i=0;
		foreach ($data as $key=>$val) {
			$key = iconv('gb2312','utf-8',$key);
			if ($fpc==1) {
				$per=percent_onebyone($val,$total,1);
				$key.=' '.$per;
			}
			$x2=$x1+20;
			imagefilledrectangle($image,$x1,$y1,$x2,$y2,$color[$cid]);
			graph_str ($image ,$key,$x3,$y3,0,array(16,16,0));
			$cid=$cid+2;
			$x1=$x2+mb_strlen($key,'gb2312')*10+22;
			$x4=$x3+mb_strlen($key,'gb2312')*10;
			$x3=$x4+40;
			if ($x3>350) 
			{
				if ($i+1==floor(($i+2)/2)) {
					$y2=$y1;
					$y1=$y2;
				}
				$y1=$y2+25;
				$y2=$y1+20;
				$y3=$y3+25;
				$x1=50;
				$x3=80;
			}
		}
	}
	//make title
	if ($ftitle==1) {
	write_title($image,$title,20);
	}
	//output
	if ($saveto='page') {
		header('Content-type: image/png');
		imagepng($image);
	}
	else if ($saveto='value') {
		return $image;
	}
	else {
		imagepng($image,'image.png');
	}
	if ($fdel==1) {
		imagedestroy($image);
	}
}
function percent_onebyone($dat,$total,$deg=2) {
	$percent=round($dat/$total*100,$deg);
	$percent.='%';
	return $percent;
}
function percent($dat,$deg=2) {
	$total=0;
	foreach ($dat as $val) {
		$total+=$val;
	}
	$percent=arrat();
	foreach ($dat as $val) {
		$per=round($val/$total*100,$deg);
		$per.='%';
		$percent[]=$per;
	}
	return $percent;
}
function allocate_colors($image) {
	//make colors
	$color=array();
	$color[0]=imagecolorallocate($image,220,0,0);
	$color[2]=imagecolorallocate($image,0,0,220);
	$color[4]=imagecolorallocate($image,0,220,0);
	$color[6]=imagecolorallocate($image,168,168,168);
	$color[8]=imagecolorallocate($image,220,220,0);
	$color[10]=imagecolorallocate($image,220,0,220);
	$color[12]=imagecolorallocate($image,0,220,220);
	$color[14]=imagecolorallocate($image,80,80,80);
	//colors dark
	$color[1]=imagecolorallocate($image,192,0,0);
	$color[3]=imagecolorallocate($image,0,0,192);
	$color[5]=imagecolorallocate($image,0,192,0);
	$color[7]=imagecolorallocate($image,168,168,168);
	$color[9]=imagecolorallocate($image,192,192,0);
	$color[11]=imagecolorallocate($image,192,0,192);
	$color[13]=imagecolorallocate($image,0,192,192);
	$color[15]=imagecolorallocate($image,60,60,60);
	return $color;
}
function write_title($image,$title,$size,$color=0) {
	/*if ($color=0) {
		$color=imagecolorallocate($image,0,0,0);
	}*/
	$sx=(imagesx($image)-mb_strlen($title,'gb2312')*$size*4/3)/2;
	$sy=5+$size*4/3;
	//$title=iconv('gb2312','utf-8',$title);
	//imagettftext($image,$si7sy,$color,'simhei.ttf',$title);
	graph_str ($image ,$title,$sx,$sy,0,array(24,24,0));
}
function graph_bar($data,$flags=15,$saveto='output',$background='default',$bgres=null){
	$flags+=16;
	$flags=decbin($flags);
	$f3d=substr($flags,-1,1);
	$fexam=substr($flags,-2,1);
	$ftitle=substr($flags,-3,1);
	$fdel=substr($flags,-4,1);
	//echo $f3d.'</br>'.$fexam.'</br>'.$ftitle.'</br>'.$fdel.'</br>'.$fpc.'</br>'.$flags;
	//die();
	$total=0;
	$item=0;
	//foreach ($data as $it) {
	//$total=$total+$val;
	//}
	$item=count($data);
	/*if (max($data)>=pow(10,ceil(log10(max($data))))*0.5) {
		$max=ceil(pow(max($data),ceil(-log10(max($data)))));
	}
	else{
		$max=ceil(pow(max($data),ceil(-log10(max($data)))))*0.5;
	}*/
	//$max=pow(10,ceil(pow(max($data),-ceil(log10(max($data)))-2)));
	//$max=round(max($data[]),-log10(max($data[])));
	foreach ($data as $val) {
	$max[]=max($val);
//		$max[]=round(max($val),-log10(max($val)));
	}
//	$maxs=max($maxs);
	$max=max($max)*1.1;
	//if ($max<(max($maxs))) {
	//	$max+=pow(10,(log10($max)));
//	}
	//$hei=array();
	$i=0;
	$i2=0;
	foreach ($data as $val) {
		foreach ($val as $ite) {
		$numa[]=$ite;
			$it[$i2]=260-$ite/$max*220;
			$i2++;
		}
		$hei[$i]=$it;
		unset($it);
		$i++;
	}
	$i=0;
	// create image
	if ($background=='default') {
		$image = imagecreatetruecolor(400, 400);
		imagefill($image,0,0,imagecolorallocate($image,255,255,255));
	}
	else if ($background=='value'){
		$image=$bgres;
	}
	else {
		$image=imagecreatefrompng ($background);
	}
	//make black and white
	$black=imagecolorallocate($image,0,0,0);
	$back = imagecolorallocate($image,255,255,255);
	$color=allocate_colors($image);
	imageline($image,80,25,80,260,0);
	imageline($image,80,260,360,260,0);
	imageline($image,360,260,350,250,0);
	imageline($image,360,260,350,270,0);
	imageline($image,80,25,90,35,0);
	imageline($image,80,25,70,35,0);
		imageline($image,0,292,400,292,0);
	//$numy=array(260,220,180,140,100,60);
	$num=0;
	$ni=0;
	for ($ny=40;$ny<=260;$ny+=20){
		imageline($image,80,$ny,360,$ny,0);
		$nx=80-strlen($num)*8;
		/*if ($ni==0){
		$nx-=16;
		}
		if ($ni==10){
		$nx-=16;
		}*/
		imagestring($image,4,$nx,280-$ny+8,round($num),0);
		$num+=$max/10;
		$ni++;
	}
	//draw
	$i=0;
	$y=90;
	$numi=0;
	foreach ($hei as $val) {
		$colorn=$color[$i];
		$i+=2;
		foreach ($val as $h) {
			imagefilledrectangle($image,$y,$h,$y+260/(count($data,COUNT_RECURSIVE)),260,$colorn);
			imagestring($image,4,$y,$h-16,$numa[$numi],0);
			$y+=count($hei)*((260/(count($data,COUNT_RECURSIVE))));
			$y+=4;
			$numi++;
		}
		$y=90+260/count($data,COUNT_RECURSIVE)+4;
	}
	//desc
	$x=90;
	$len=2*260/(count($data,COUNT_RECURSIVE))+4;
	$is_line=1;
	foreach ($data as $item=>$sub){
		foreach ($sub as $line=>$var){
			if ($is_line==1){
				//imagestring($image,4,$x,276,$line,0);
				graph_str ($image ,$line,$x,276,0,array(16,16,0));
			}
			$x+=$len;
		}
		$is_line++;
	}
	//make description
	if ($fexam==1) {
		$x1=70;
		$y1=294;
		$y2=$y1+16;
		$x3=105;
		$y3=300;
		$colorid=0;
		$cid=0;
		$i=0;
		foreach ($data as $key=>$val) {
			//$key = iconv('gb2312','utf-8',$key);
	/*		if ($fpc==1) {
				$per=percent_onebyone($val,$total,1);
				$key.=' '.$per;
			}
*/
			$x2=$x1+16;
			imagefilledrectangle($image,$x1,$y1,$x2,$y2,$color[$cid]);
			//imagestring ($image,4,$x3,$y3,$key,$black);
			graph_str ($image,$key,$x3,$y3,0,array(16,16,0));
			$cid=$cid+2;
			$x1=$x2+mb_strlen($key,'gb2312')*10+22;
			$x4=$x3+mb_strlen($key,'gb2312')*10;
			$x3=$x4+40;
			if ($x3>350) 
			{
				if ($i+1==floor(($i+2)/2)) {
					$y2=$y1;
					$y1=$y2;
				}
				$y1=$y2+25;
				$y2=$y1+20;
				$y3=$y3+25;
				$x1=50;
				$x3=80;
			}
		}
	}
		//make title
	if ($ftitle==1) {
		write_title($image,$title,20);
	}

	//output
	if ($saveto='page') {
		//echo $max;
	header('Content-type: image/png');
		imagepng($image);
	}
	else if ($saveto='value') {
		return $image;
	}
	else {
		imagepng($image,'image.png');
	}
	if ($fdel==1) {
		imagedestroy($image);
	}
}
function graph_progress_bar($total,$now,$title='',$keies=null,$style=0,$flags=3,$saveto='page',$background='default',$bgres=null){
	$barlen=$now/$total*300;
	$image=imagecreatetruecolor(400,100);
	imagefill($image,0,0,imagecolorallocate($image,255,255,255));
	imagefilledrectangle($image,50,30,350,62,0x0000b6);
	imagefilledrectangle($image,50,30,50+$barlen,62,0xf0f000);
	imagestring($image,4,195,40,percent_onebyone($now,$total,1),imagecolorallocate($image,0,0,0));
	//make title
		if ($ftitle==1) {
			write_title($image,$title,20);
		}
	//output
		if ($saveto='page') {
			header('Content-type: image/png');
			imagepng($image);
		}
		else if ($saveto='value') {
			return $image;
		}
		else {
			imagepng($image,'image.png');
		}
}
function graph_str($image,$x,$y,$string,$type=0,$style=null,$ttfile=null){
	if ($type==0){
		if ($style==null){
			$style=array(16,16,0);
		}
		require_once 'dotFont.php';
		$image=dotfont_str($image,$x,$y,$string,$style[0],$style[1],$style[2]);
		return $image;
	}else if ($type==1){
		if ($style==null){
			$style=array(12,0,0);
		}
		$image=imagettftext($image,$style[0],$style[2],$x,$y,$style[1],'simhei.ttf',$string);
		return $image;
	}else{
		if ($style==null){
			$style=array(4,16,16,0);
		}
		$image=imagestring($image,$style[0],$style[1],$style[2],$string,$style[3]);
		return $image;
	}
}
graph_line(array(array(1,6,7,9,3,3),array(2,0,1,6,0,9)));
function graph_line($data,$flags=15,$title='',$saveto='page',$background='default',$bgres='null'){
	//parse flags
	$flags+=32;
	$flags=decbin($flags);
	//$f3d=substr($flags,-1,1);
	$fexam=substr($flags,-1,1);
	$ftitle=substr($flags,-2,1);
	$fdel=substr($flags,-3,1);
	$fnum=substr($flags,-4,1);
	//parse data before drawing
	//make a max value
	foreach ($data as $sub){
		$max[]=max($sub);
	}
	$max=max($max);
	$maxtest=$max;
	$max=round($max,-(log10($max)));
	if ($max<$maxtest){
		$max+=pow(10,(log10($maxtest))-1);
	}
	$hei=array();
	foreach ($data as $sub){
		$heisub=array();
		foreach ($sub as $val){
			$heisub[]=250-$val/$max;
		}
		$hei[]=$heisub;
	}
	//create a image area
	if ($background=='default'){
		$image = imagecreatetruecolor(400, 400);
		imagefill($image,0,0,imagecolorallocate($image,255,255,255));
	}else if ($background=='gdimg'){
		$image=$bgres;
	}else{
		$image=imagecreatefrompng ($background);
	}
	//allocate colors
	$color=allocate_colors($image);
	//draw some line
	imageline($image,60,25,60,250,0);
	imageline($image,60,250,360,250,0);
	imageline($image,360,250,350,240,0);
	imageline($image,360,250,350,260,0);
	imageline($image,60,25,50,35,0);
	imageline($image,60,25,70,35,0);
	imageline($image,0,292,400,292,0);
	//$numy=array(260,220,180,140,100,60);
	$num=0;
	$ni=0;
	for ($ny=50;$ny<=250;$ny+=20){
		imageline($image,60,$ny,350,$ny,0);
		$nx=60-strlen($num)*8;
		/*if ($ni==0){
		$nx-=16;
		}
		if ($ni==10){
		$nx-=16;
		}*/
		imagestring($image,4,$nx,280-$ny+8,round($num),0);
		$num+=$max/10;
		$ni++;
	}
	//draw
	$x=65;
	for ($i=0;$i<=count($sub);$i++);{
		imageline($image,$x,250,$x,250,0);
		$x+=300/(count($data,COUNT_RECURSIVE));
	}
	$heilast=false;
	$x=65;
	$colid=0;
	foreach ($hei as $heisub){
		foreach ($heisub as $height){
			if ($heilast==false){
				imageline($image,$x,$height,$x,$height,0);
			}else{
				imageline($image,$x,$height,$x+300/(count($data,COUNT_RECURSIVE)),$heilast,0);
				$x+=300/(count($data,COUNT_RECURSIVE));
				$heilast=$height;
			}
		}
		$colid+=2;
		$heilast=false;
	}
	//example for graph
	if ($fexam==1) {
		$x1=60;
		$y1=269;
		$y2=$y1+16;
		$x3=95;
		$y3=285;
		$cid=0;
		$i=0;
		foreach ($data as $key=>$val) {
			//$key = iconv('gb2312','utf-8',$key);
	/*		if ($fpc==1) {
				$per=percent_onebyone($val,$total,1);
				$key.=' '.$per;
			}
*/
			$x2=$x1+16;
			imagefilledrectangle($image,$x1,$y1,$x2,$y2,$color[$cid]);
			//imagestring ($image,4,$x3,$y3,$key,$black);
			graph_str ($image,$key,$x3,$y3,0,array(16,16,0));
			$cid=$cid+2;
			$x1=$x2+strlen($key)*10+22;
			$x4=$x3+strlen($key)*10;
			$x3=$x4+40;
			if ($x3>350) 
			{
				if ($i+1==floor(($i+2)/2)) {
					$y2=$y1;
					$y1=$y2;
				}
				$y1=$y2+25;
				$y2=$y1+20;
				$y3=$y3+25;
				$x1=50;
				$x3=80;
			}
		}
	}
		//make title
	if ($ftitle==1) {
		write_title($image,$title,20);
	}
	//output
		if ($saveto='page') {
			header('Content-type: image/png');
			imagepng($image);
		}
		else if ($saveto='value') {
			return $image;
		}
		else {
			imagepng($image,'image.png');
		}
}
?> 