<?php
/*******************************************************************************
* Software: UFPDF, Unicode Free PDF generator                                  *
* Version:  0.1                                                                *
*           based on FPDF 1.52 by Olivier PLATHEY                              *
* Date:     2004-09-01                                                         *
* Author:   Steven Wittens <steven@acko.net>                                   *
* License:  GPL                                                                *
*                                                                              *
* UFPDF is a modification of FPDF to support Unicode through UTF-8.            *
*                                                                              *
*******************************************************************************/

if(!class_exists('UFPDF'))
{
	define('UFPDF_VERSION','0.1');
	
	@require_once "fpdf/fpdf.php";
	
	class UFPDF extends FPDF
	{
		var $outlines=array();
		var $OutlineRoot;
		var $HREF='';
		var $angle;
		/*******************************************************************************
		*                                                                              *
		*                               Public methods                                 *
		*                                                                              *
		*******************************************************************************/
		function UFPDF($orientation='P',$unit='mm',$format='A4')
		{
		  FPDF::FPDF($orientation, $unit, $format);
		}
		
		function GetStringWidth($s)
		{
		  //Get width of a string in the current font
		  $s = (string)$s;
		  $codepoints=$this->utf8_to_codepoints($s);
		  $cw=&$this->CurrentFont['cw'];
		  $w=0;
		  foreach($codepoints as $cp)
			$w+=$cw[$cp];
		  return $w*$this->FontSize/1000;
		}
		
		function AddFont($family,$style='',$file='')
		{
		  //Add a TrueType or Type1 font
		  $family=strtolower($family);
		  if($family=='arial')
			$family='helvetica';
		  $style=strtoupper($style);
		  if($style=='IB')
			$style='BI';
		  if(isset($this->fonts[$family.$style]))
			$this->Error('Font already added: '.$family.' '.$style);
		  if($file=='')
			$file=str_replace(' ','',$family).strtolower($style).'.php';
		  if(defined('FPDF_FONTPATH'))
			$file=FPDF_FONTPATH.$file;
		  include($file);
		  if(!isset($name))
			$this->Error('Could not include font definition file');
		  $i=count($this->fonts)+1;
		  $this->fonts[$family.$style]=array('i'=>$i,'type'=>$type,'name'=>$name,'desc'=>$desc,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'file'=>$file,'ctg'=>$ctg);
		  if($file)
		  {
			if($type=='TrueTypeUnicode')
			  $this->FontFiles[$file]=array('length1'=>$originalsize);
			else
			  $this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
		  }
		}
		
		function Text($x,$y,$txt)
		{
		  //Output a string
		  $s=sprintf('BT %.2f %.2f Td %s Tj ET',$x*$this->k,($this->h-$y)*$this->k,$this->_escapetext($txt));
		  if($this->underline and $txt!='')
			$s.=' '.$this->_dounderline($x,$y,$this->GetStringWidth($txt),$txt);
		  if($this->ColorFlag)
			$s='q '.$this->TextColor.' '.$s.' Q';
		  $this->_out($s);
		}
		
		function AcceptPageBreak()
		{
		  //Accept automatic page break or not
		  return $this->AutoPageBreak;
		}
		
		function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
		{
		  //Output a cell
		  $k=$this->k;
		  if($this->y+$h>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
		  {
			//Automatic page break
			$x=$this->x;
			$ws=$this->ws;
			if($ws>0)
			{
			  $this->ws=0;
			  $this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			$this->x=$x;
			if($ws>0)
			{
			  $this->ws=$ws;
			  $this->_out(sprintf('%.3f Tw',$ws*$k));
			}
		  }
		  if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		  $s='';
		  if($fill==1 or $border==1)
		  {
			if($fill==1)
			  $op=($border==1) ? 'B' : 'f';
			else
			  $op='S';
			$s=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		  }
		  if(is_string($border))
		  {
			$x=$this->x;
			$y=$this->y;
			if(is_int(strpos($border,'L')))
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'T')))
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
			if(is_int(strpos($border,'R')))
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
			if(is_int(strpos($border,'B')))
			  $s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		  }
		  if($txt!='')
		  {
			$width = $this->GetStringWidth($txt);
			if($align=='R')
			  $dx=$w-$this->cMargin-$width;
			elseif($align=='C')
			  $dx=($w-$width)/2;
			else
			  $dx=$this->cMargin;
			if($this->ColorFlag)
			  $s.='q '.$this->TextColor.' ';
			$txtstring=$this->_escapetext($txt);
			$s.=sprintf('BT %.2f %.2f Td %s Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txtstring);
			if($this->underline)
			  $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$width,$txt);
			if($this->ColorFlag)
			  $s.=' Q';
			if($link)
			  $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$width,$this->FontSize,$link);
		  }
		  if($s)
			$this->_out($s);
		  $this->lasth=$h;
		  if($ln>0)
		  {
			//Go to next line
			$this->y+=$h;
			if($ln==1)
			  $this->x=$this->lMargin;
		  }
		  else
			$this->x+=$w;
		}
		
		/*******************************************************************************
		*                                                                              *
		*                              Protected methods                               *
		*                                                                              *
		*******************************************************************************/
		
		function _puttruetypeunicode($font) {
		  //Type0 Font
		  $this->_newobj();
		  $this->_out('<</Type /Font');
		  $this->_out('/Subtype /Type0');
		  $this->_out('/BaseFont /'. $font['name'] .'-UCS');
		  $this->_out('/Encoding /Identity-H');
		  $this->_out('/DescendantFonts ['. ($this->n + 1) .' 0 R]');
		  $this->_out('>>');
		  $this->_out('endobj');
		
		  //CIDFont
		  $this->_newobj();
		  $this->_out('<</Type /Font');
		  $this->_out('/Subtype /CIDFontType2');
		  $this->_out('/BaseFont /'. $font['name']);
		  $this->_out('/CIDSystemInfo <</Registry (Adobe) /Ordering (UCS) /Supplement 0>>');
		  $this->_out('/FontDescriptor '. ($this->n + 1) .' 0 R');
		  $c = 0;
		  foreach ($font['cw'] as $i => $w) 
			@$widths .= $i .' ['. $w.'] ';
		  $this->_out('/W ['. $widths .']');
		  $this->_out('/CIDToGIDMap '. ($this->n + 2) .' 0 R');
		  $this->_out('>>');
		  $this->_out('endobj');
		
		  //Font descriptor
		  $this->_newobj();
		  $this->_out('<</Type /FontDescriptor');
		  $this->_out('/FontName /'.$font['name']);
		  foreach ($font['desc'] as $k => $v) 
			isset($s) ? $s .= ' /'. $k .' '. $v : $s = ' /'. $k .' '. $v;
		  if ($font['file']) 
			isset($s) ? $s .= ' /FontFile2 '. $this->FontFiles[$font['file']]['n'] .' 0 R' : $s = ' /FontFile2 '. $this->FontFiles[$font['file']]['n'] .' 0 R' ;
		  $this->_out($s);
		  $this->_out('>>');
		  $this->_out('endobj');
		
		  //Embed CIDToGIDMap
		  $this->_newobj();
		  if(defined('FPDF_FONTPATH'))
			$file=FPDF_FONTPATH.$font['ctg'];
		  else
			$file=$font['ctg'];
		  $size=filesize($file);
		  if(!$size)
			$this->Error('Font file not found');
		  $this->_out('<</Length '.$size);
			if(substr($file,-2) == '.z')
			$this->_out('/Filter /FlateDecode');
		  $this->_out('>>');
		  $f = fopen($file,'rb');
		  $this->_putstream(fread($f,$size));
		  fclose($f);
		  $this->_out('endobj');
		}
		
		function _dounderline($x,$y,$width,$txt)
		{
		  //Underline text
		  $up=$this->CurrentFont['up'];
		  $ut=$this->CurrentFont['ut'];
		  $w=$width+$this->ws*substr_count($txt,' ');
		  return sprintf('%.2f %.2f %.2f %.2f re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt);
		}
		
		function _textstring($s)
		{
		  //Convert to UTF-16BE
		  $s = $this->utf8_to_utf16be($s);
		  //Escape necessary characters
		  return '('. strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\')) .')';
		}
		
		function _escapetext($s)
		{
		  //Convert to UTF-16BE
		  $s = $this->utf8_to_utf16be($s, false);
		  //Escape necessary characters
		  return '('. strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\')) .')';
		}
		
		function _putinfo()
		{
			$this->_out('/Producer '.$this->_textstring('UFPDF '. UFPDF_VERSION));
			if(!empty($this->title))
				$this->_out('/Title '.$this->_textstring($this->title));
			if(!empty($this->subject))
				$this->_out('/Subject '.$this->_textstring($this->subject));
			if(!empty($this->author))
				$this->_out('/Author '.$this->_textstring($this->author));
			if(!empty($this->keywords))
				$this->_out('/Keywords '.$this->_textstring($this->keywords));
			if(!empty($this->creator))
				$this->_out('/Creator '.$this->_textstring($this->creator));
			$this->_out('/CreationDate '.$this->_textstring('D:'.date('YmdHis')));
		}
		
		// UTF-8 to UTF-16BE conversion.
		// Correctly handles all illegal UTF-8 sequences.
		function utf8_to_utf16be(&$txt, $bom = true) 
		{
		  $l = strlen($txt);
		  $out = $bom ? "\xFE\xFF" : '';
		  for ($i = 0; $i < $l; ++$i) 
		  {
			$c = ord($txt{$i});
			// ASCII
			if ($c < 0x80) 
			  $out .= "\x00". $txt{$i};
			// Lost continuation byte
			else if ($c < 0xC0) 
			{
			  $out .= "\xFF\xFD";
			  continue;
			}
			// Multibyte sequence leading byte
			else 
			{
			  if ($c < 0xE0) 
				$s = 2;
			  else if ($c < 0xF0) 
				$s = 3;
			  else if ($c < 0xF8) 
				$s = 4;
			  // 5/6 byte sequences not possible for Unicode.
			  else 
			  {
				$out .= "\xFF\xFD";
				while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0)  
					++$i; 
				continue;
			  }
			  
			  $q = array($c);
			  // Fetch rest of sequence
			  while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) 
			  {
				  ++$i; 
				  $q[] = ord($txt{$i}); 
			  }
			  
			  // Check length
			  if (count($q) != $s) 
			  {
				$out .= "\xFF\xFD";        
				continue;
			  }
			  
			  switch ($s) 
			  {
				case 2:
				  $cp = (($q[0] ^ 0xC0) << 6) | ($q[1] ^ 0x80);
				  // Overlong sequence
				  if ($cp < 0x80) 
					$out .= "\xFF\xFD";   
				  else 
				  {
					$out .= chr($cp >> 8);
					$out .= chr($cp & 0xFF);
				  }
				  continue;
		
				case 3:
				  $cp = (($q[0] ^ 0xE0) << 12) | (($q[1] ^ 0x80) << 6) | ($q[2] ^ 0x80);
				  // Overlong sequence
				  if ($cp < 0x800) 
					$out .= "\xFF\xFD";     
				  // Check for UTF-8 encoded surrogates (caused by a bad UTF-8 encoder)
				  else if ($c > 0xD800 && $c < 0xDFFF) 
					$out .= "\xFF\xFD";
				  else 
				  {
					$out .= chr($cp >> 8);
					$out .= chr($cp & 0xFF);
				  }
				  continue;
		
				case 4:
				  $cp = (($q[0] ^ 0xF0) << 18) | (($q[1] ^ 0x80) << 12) | (($q[2] ^ 0x80) << 6) | ($q[3] ^ 0x80);
				  // Overlong sequence
				  if ($cp < 0x10000) 
					$out .= "\xFF\xFD";
				  // Outside of the Unicode range
				  else if ($cp >= 0x10FFFF) 
					$out .= "\xFF\xFD";       
				  else 
				  {
					// Use surrogates
					$cp -= 0x10000;
					$s1 = 0xD800 | ($cp >> 10);
					$s2 = 0xDC00 | ($cp & 0x3FF);
					
					$out .= chr($s1 >> 8);
					$out .= chr($s1 & 0xFF);
					$out .= chr($s2 >> 8);
					$out .= chr($s2 & 0xFF);
				  }
				  continue;
			  }
			}
		  }
		  return $out;
		}
		
		// UTF-8 to codepoint array conversion.
		// Correctly handles all illegal UTF-8 sequences.
		function utf8_to_codepoints(&$txt) 
		{
		  $l = strlen($txt);
		  $out = array();
		  for ($i = 0; $i < $l; ++$i) 
		  {
			$c = ord($txt{$i});
			// ASCII
			if ($c < 0x80) 
			  $out[] = ord($txt{$i});
			// Lost continuation byte
			else if ($c < 0xC0) 
			{
			  $out[] = 0xFFFD;
			  continue;
			}
			// Multibyte sequence leading byte
			else 
			{
			  if ($c < 0xE0) 
				$s = 2;
			  else if ($c < 0xF0) 
				$s = 3;
			  else if ($c < 0xF8) 
				$s = 4;
			  // 5/6 byte sequences not possible for Unicode.
			  else 
			  {
				$out[] = 0xFFFD;
				while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) 
					++$i; 
				continue;
			  }
			  
			  $q = array($c);
			  // Fetch rest of sequence
			  while (ord($txt{$i + 1}) >= 0x80 && ord($txt{$i + 1}) < 0xC0) 
			  { 
				  ++$i; 
				  $q[] = ord($txt{$i}); 
			  }
			  
			  // Check length
			  if (count($q) != $s) 
			  {
				$out[] = 0xFFFD;
				continue;
			  }
			  
			  switch ($s) 
			  {
				case 2:
				  $cp = (($q[0] ^ 0xC0) << 6) | ($q[1] ^ 0x80);
				  // Overlong sequence
				  if ($cp < 0x80) 
					$out[] = 0xFFFD;
				  else 
					$out[] = $cp;
				  continue;
		
				case 3:
				  $cp = (($q[0] ^ 0xE0) << 12) | (($q[1] ^ 0x80) << 6) | ($q[2] ^ 0x80);
				  // Overlong sequence
				  if ($cp < 0x800) 
					$out[] = 0xFFFD;
				  // Check for UTF-8 encoded surrogates (caused by a bad UTF-8 encoder)
				  else if ($c > 0xD800 && $c < 0xDFFF) 
					$out[] = 0xFFFD;
				  else 
					$out[] = $cp;
				  continue;
		
				case 4:
				  $cp = (($q[0] ^ 0xF0) << 18) | (($q[1] ^ 0x80) << 12) | (($q[2] ^ 0x80) << 6) | ($q[3] ^ 0x80);
				  // Overlong sequence
				  if ($cp < 0x10000) 
					$out[] = 0xFFFD;
				  // Outside of the Unicode range
				  else if ($cp >= 0x10FFFF) 
					$out[] = 0xFFFD;
				  else 
					$out[] = $cp;
				  continue;
			  }
			}
		  }
		  return $out;
		}
		
		/*******************************************************************************
		*                                                                              *
		*                              Modify methods                                  *
		*                                                                              *
		*******************************************************************************/		
		function Footer()
		{
			$this->SetY(-15);
			$this->SetTextColor(128);
			$this->Line(10,283,200,283);
			$this->Cell(0,10,'Sayfa '.$this->PageNo(),0,0,'C');
		}
		
		function Bookmark($txt, $level=0, $y=0)
		{
			if($y==-1)
				$y=$this->GetY();
			$this->outlines[]=array('t'=>$txt, 'l'=>$level, 'y'=>($this->h-$y)*$this->k, 'p'=>$this->PageNo());
		}
		
		function BookmarkUTF8($txt, $level=0, $y=0)
		{
			$this->Bookmark($this->_UTF8toUTF16($txt),$level,$y);
		}
		
		function _putbookmarks()
		{
			$nb=count($this->outlines);
			if($nb==0)
				return;
			$lru=array();
			$level=0;
			foreach($this->outlines as $i=>$o)
			{
				if($o['l']>0)
				{
					$parent=$lru[$o['l']-1];
					//Set parent and last pointers
					$this->outlines[$i]['parent']=$parent;
					$this->outlines[$parent]['last']=$i;
					if($o['l']>$level)
					{
						//Level increasing: set first pointer
						$this->outlines[$parent]['first']=$i;
					}
				}
				else
					$this->outlines[$i]['parent']=$nb;
				if($o['l']<=$level and $i>0)
				{
					//Set prev and next pointers
					$prev=$lru[$o['l']];
					$this->outlines[$prev]['next']=$i;
					$this->outlines[$i]['prev']=$prev;
				}
				$lru[$o['l']]=$i;
				$level=$o['l'];
			}
			//Outline items
			$n=$this->n+1;
			foreach($this->outlines as $i=>$o)
			{
				$this->_newobj();
				$this->_out('<</Title '.$this->_textstring($o['t']));
				$this->_out('/Parent '.($n+$o['parent']).' 0 R');
				if(isset($o['prev']))
					$this->_out('/Prev '.($n+$o['prev']).' 0 R');
				if(isset($o['next']))
					$this->_out('/Next '.($n+$o['next']).' 0 R');
				if(isset($o['first']))
					$this->_out('/First '.($n+$o['first']).' 0 R');
				if(isset($o['last']))
					$this->_out('/Last '.($n+$o['last']).' 0 R');
				$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]',1+2*$o['p'],$o['y']));
				$this->_out('/Count 0>>');
				$this->_out('endobj');
			}
			//Outline root
			$this->_newobj();
			$this->OutlineRoot=$this->n;
			$this->_out('<</Type /Outlines /First '.$n.' 0 R');
			$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
			$this->_out('endobj');
		}
		
		function _putresources()
		{
			parent::_putresources();
			$this->_putbookmarks();
		}
		
		function _putcatalog()
		{
			parent::_putcatalog();
			if(count($this->outlines)>0)
			{
				$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
				$this->_out('/PageMode /UseOutlines');
			}
		}
		
		function CreateIndex()
		{
			//Index title
			$this->SetFontSize(20);
			$this->Cell(0,5,'Index',0,1,'C');
			$this->SetFontSize(15);
			$this->Ln(10);
		
			$size=sizeof($this->outlines);
			$PageCellSize=$this->GetStringWidth('s. '.$this->outlines[$size-1]['p'])+2;
			for ($i=0;$i<$size;$i++)
			{
				//Offset
				$level=$this->outlines[$i]['l'];
				if($level>0)
					$this->Cell($level*8);
		
				//Caption
				$str=$this->outlines[$i]['t'];
				$strsize=$this->GetStringWidth($str);
				$avail_size=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-4;
				while ($strsize>=$avail_size)
				{
					$str=substr($str,0,-1);
					$strsize=$this->GetStringWidth($str);
				}
				$this->Cell($strsize+2,$this->FontSize+2,$str);
		
				//Filling dots
				$w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+2);
				$nb=$w/$this->GetStringWidth('.');
				$dots=str_repeat('.',$nb);
				$this->Cell($w,$this->FontSize+2,$dots,0,0,'R');
		
				//Page number
				$this->Cell($PageCellSize,$this->FontSize+2,'s. '.$this->outlines[$i]['p'],0,1,'R');
			}
		}
		
		function HTML2RGB($c, &$r, &$g, &$b)
		{
			static $colors = array('black'=>'#000000','silver'=>'#C0C0C0','gray'=>'#808080','white'=>'#FFFFFF',
								'maroon'=>'#800000','red'=>'#FF0000','purple'=>'#800080','fuchsia'=>'#FF00FF',
								'green'=>'#008000','lime'=>'#00FF00','olive'=>'#808000','yellow'=>'#FFFF00',
								'navy'=>'#000080','blue'=>'#0000FF','teal'=>'#008080','aqua'=>'#00FFFF');
		
			$c=strtolower($c);
			if(isset($colors[$c]))
				$c=$colors[$c];
			if($c[0]!='#')
				$this->Error('Incorrect color: '.$c);
			$r=hexdec(substr($c,1,2));
			$g=hexdec(substr($c,3,2));
			$b=hexdec(substr($c,5,2));
		}
		
		function SetDrawColor($r, $g=-1, $b=-1)
		{
			if(is_string($r))
				$this->HTML2RGB($r,$r,$g,$b);
			parent::SetDrawColor($r,$g,$b);
		}
		
		function SetFillColor($r, $g=-1, $b=-1)
		{
			if(is_string($r))
				$this->HTML2RGB($r,$r,$g,$b);
			parent::SetFillColor($r,$g,$b);
		}
		
		function SetTextColor($r,$g=-1,$b=-1)
		{
			if(is_string($r))
				$this->HTML2RGB($r,$r,$g,$b);
			parent::SetTextColor($r,$g,$b);
		}
		
		function Rotate($angle, $x=-1, $y=-1)
		{
			if($x==-1)
				$x=$this->x;
			if($y==-1)
				$y=$this->y;
			if($this->angle!=0)
				$this->_out('Q');
			$this->angle=$angle;
			if($angle!=0)
			{
				$angle*=M_PI/180;
				$c=cos($angle);
				$s=sin($angle);
				$cx=$x*$this->k;
				$cy=($this->h-$y)*$this->k;
				$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
			}
		}
		
		function Header()
		{
			$this->Cell(50);
			$this->Cell(90,10,"DEPO SORGU:".date ("Y-m-d"),1,0,'C');
			$this->Ln(20);
			$this->SetFont('Arial','',50);
			$this->SetTextColor("#D9D8D6");
			$this->RotatedText(35,190,'K U Y A S  Y A Z I L I M',45);
			$this->SetFont('Arial','',14);
		}
		
		function RotatedText($x, $y, $txt, $angle)
		{
			//Text rotated around its origin
			$this->Rotate($angle,$x,$y);
			$this->Text($x,$y,$txt);
			$this->Rotate(0);
		}
	//End of class
	}

}
?>
