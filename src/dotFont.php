<?php
/**
* 读取汉字点阵数据
*

$im=imagecreatetruecolor(256,256);
$back = imagecolorallocate($im,255,255,255);
imagefill($im,0,0,$back);
dotstring('中华',$im,0,16,16,25,25);
function dotstring($string,$im,$sx,$sy,$x,$y) {
	/*$im=0;
	$im=imagecreatetruecolor(8*strlen($string),16);
	$back = imagecolorallocate($im,255,255,255);
	imagefill($im,0,0,$back);
	for ($i=0;$i<strlen($string);$i=$i+2) {
		$str=substr($string,$i,2);
		$im=dotfont($str,'hzk16h',$sx,$sy,$im,$y,8*$i+$x);
	}
	return $im;
}
 function dotfont($str,$file,$sx,$sy,$im,$x,$y) {
	$font_file_name = "hzk16h"; // 点阵字库文件名
	$font_width = 16; // 单字宽度
	$font_height = 16; // 单字高度
	$start_offset = 0; // 偏移
	
	$fp = fopen($font_file_name, "rb");
	
	$offset_size = $font_width * $font_height / 8;
	$string_size = $font_width * $font_height;
	$dot_string = "";0
	
	for ($i = 0; $i < strlen($str); $i ++)
	{
		if (ord($str{$i}) > 160)
		{
	// 先求区位码，然后再计算其在区位码二维表中的位置，进而得出此字符在文件中的偏移
			$offset = ((ord($str{$i}) - 0xa1) * 94 + ord($str{$i + 1}) - 0xa1) * $offset_size;
			$i ++;
		}
		else
		{
			$offset = (ord($str{$i}) + 156 - 1) * $offset_size;
		}
	
	// 读取其点阵数据
		fseek($fp, $start_offset + $offset, SEEK_SET);
		$bindot = fread($fp, $offset_size);
	
	for ($j = 0; $j < $offset_size; $j ++)
	{
	// 将二进制点阵数据转化为字符串
		$dot_string .= sprintf("%08b", ord($bindot{$j}));
	}
}
 
fclose($fp);
 
//echo $dot_string;
$point=0;
for ($xi=$x;$xi<$sx+$x;$xi++) {
		for ($yi=$y;$yi<$sy+$y;$yi++) {
			if (substr($dot_string,$point,1)==1) {
				imagesetpixel ($im,$yi,$xi,0);
			}
			$point++;
		}
	}
	return $im;
 }
 header('Content-type: image/png');
imagepng($im); */
function dotfont_str($image,$x,$y,$string,$sx=16,$sy=16,$color=0,$hzfont='hzk16h',$ascfont='ASC16'){
	$stroffset=0;
	$len=$sx*$sy;
	$strl=strlen($string)-1;
	for($stroffset=0;$stroffset<$strl;$stroffset++){
		if (ord(substr($string,$stroffset,1))<0x80);{
			$len=$len/2;
			$char=substr($string,$stroffset,1);
			dotfont_char($len,$char,$ascfont);
			$len=$len*2;
		}
		if (ord(substr($string,$stroffset,1))>0x80){
			$char=substr($string,$stroffset,2);
			dotfont_char($len,$char,$hzfont);
			$stroffset++;
		}
		$point=0;
	}
	for ($xi=$x;$xi<$sx+$x;$xi++) {
		for ($yi=$y;$yi<$sy+$y;$yi++) {
			if (substr($char,$point,1)==1) {
				imagesetpixel ($image,$yi,$xi,0);
			}
			$point++;
		}
	}
}
function dotfont_char($len,$char,$font){
	$fp=fopen($font,'rb');
	$dot_string='';
	if (strlen($char==2)){
		$offset = ((ord(substr($char,0,1)) - 0xa1) * 94 + ord(substr($char,0,1)) - 0xa1) * $len;
	}else{
		$offset=$char*$len;
	}
	fseek($fp,$offset);
	$bindot=fread($fp,$len);
	for ($j = 0; $j < $len; $j ++){
		$dot_string .= sprintf("%08b", ord($bindot{$j}));
	}
	return $dot_string;
}
?>