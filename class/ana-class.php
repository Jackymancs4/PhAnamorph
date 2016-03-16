<?php

class Anamorph {
  
  private $type=false;
  
  public $originalimage=false;
  public $newimage=false;
  public $perimage=false;
    
  public $scale=false;   
  public $or_distance=false;
  public $ver_distance=false;    
              
  public function load ($address, $type="auto") {
           
    if(file_exists($address)) {
        
      if($type=="auto") {
        $ext=explode(".", $address);
        $type=$ext[count($ext)-1];
      }        
        
      switch ($type) { 
        case 'png':
          $this->originalimage=imagecreatefrompng($address);
        break;
        
        case 'jpg':
          $this->originalimage=imagecreatefromjpeg($address);
        break;
      }
      
      $this->type=$type;
      return true;
    } else {
      return false;
    }  
  } 
  
  public function init ($or_distance, $ver_distance, $height=false, $unit="cm" ) {
    if ($this->originalimage!=false) {
      switch ($unit) {      
        case "cm":
          $scale=$height/imagesy($this->originalimage);
    
          $this->or_distance=$or_distance/$scale;
          $this->ver_distance=$ver_distance/$scale;
        break;
        case "px":
    
          $this->or_distance=$or_distance;
          $this->ver_distance=$ver_distance;
        break;                          
      }
    } else {
      return false;
    }        
  }      
  
  public function get_coord ($d, $height, $or_distance, $ver_distance, $or_origin=0, $ver_origin=0) {
    
    $yx=-$ver_distance;
    $yo=$ver_origin;
    
    $xx=$or_origin;
    $xo=$or_distance;
    
    $a=($yx-$yo);
    $b=($xx-$xo);
    $c=$height/2;
    
    $mp=(($a*$b)+($c*sqrt($a*$a+$b*$b-$c*$c)))/($b*$b-$c*$c);
    $mpd=-1/$mp;
    
    $mm=(($a*$b)-($c*sqrt($a*$a+$b*$b-$c*$c)))/($b*$b-$c*$c);
    $mmd=-1/$mm;
    
    $md=$mpd;
    
    $mdq=($md*$md+1);
    
    $xpj=($xx*$mdq+$d*sqrt($mdq))/$mdq;
    $xmj=($xx*$mdq-$d*sqrt($mdq))/$mdq;
    
    $ypj=($yx*$mdq+$md*$d*sqrt($mdq))/$mdq;
    $ymj=($yx*$mdq-$md*$d*sqrt($mdq))/$mdq;
    
    $xj=$xpj;
    $yj=$ypj;
    
    $y=($yj-$yo)/($xj-$xo)*($xx-$xo)+$yo;
    
    $f=$yx-$y;
  
    return $f;
  }

  public function get_coord_light ($d, $height, $xo, $yx) {    
    $yx=-$yx;  
    
    $a=$yx;
    $b=-$xo;
    $c=$height/2;
    
    $m=(($a*$b)+($c*sqrt($a*$a+$b*$b-$c*$c)))/($b*$b-$c*$c);
    $md=-1/$m;
    
    $mdq=($md*$md+1);
    
    $xj=($d*sqrt($mdq))/$mdq;    
    $yj=($yx*$mdq+$md*$d*sqrt($mdq))/$mdq;
    
    $y=$yj/($xj-$xo)*(-$xo);
  
    return $yx-$y;
  }

  public function create_newimage ($height, $width, $br=255, $bg=255, $bb=255) {
      
      $image = imagecreatetruecolor($height, $width); 
      $background = imagecolorallocate($image, $br, $bg, $bb);
      imagefill($image, 0, 0, $background);      
      return $image;

  }  

  public function make_anamorph () {
    
    if ($this->originalimage!=false) {
    
      $newheight=$this->get_coord_light(imagesy($this->originalimage), imagesy($this->originalimage), $this->or_distance, $this->ver_distance);
      $this->newimage = $this->create_newimage(imagesx($this->originalimage), $newheight);    
    
      for ($i=0; $i<imagesy($this->originalimage); $i++) {

        $ana_coord=$this->get_coord_light($i, imagesy($this->originalimage), $this->or_distance, $this->ver_distance);
      
        for ($j=0; $j<imagesx($this->originalimage); $j++) {
      
          $color_index = imagecolorat($this->originalimage, $j, $i);
          $color_tran = imagecolorsforindex($this->originalimage, $color_index);
      
          $pixel=imagecolorallocate($this->originalimage, $color_tran['red'], $color_tran['green'], $color_tran['blue']);    
          imagesetpixel($this->newimage, $j, $ana_coord, $pixel); 
      
        }
      }
      return true;
    } else {
      return false;
    }
  
  }
  
    public function make_anamorph_fill () {
    
    if ($this->originalimage!=false) {
    
      $newheight=$this->get_coord_light(imagesy($this->originalimage), imagesy($this->originalimage), $this->or_distance, $this->ver_distance);
      $this->newimage = $this->create_newimage(imagesx($this->originalimage), $newheight);    
    
      for ($i=0; $i<imagesy($this->originalimage); $i++) {
      
        $ana_coord=$this->get_coord_light($i, imagesy($this->originalimage), $this->or_distance, $this->ver_distance);
        
        imagecopyresampled($this->newimage, $this->originalimage, 0, $ana_coord, 0, $i, imagesx($this->originalimage), ($ana_coord-$i), imagesx($this->originalimage), 2);
        
      }
      return true;
    } else {
      return false;
    }
  
  }
  
  public function make_ana_perspective () {
    if ($this->originalimage!=false && $this->newimage!=false) {
    
      $alfa=(imagesx($this->newimage))/(($this->ver_distance));
      $newlenght=($this->ver_distance+imagesy($this->newimage))*$alfa;
  
      $this->perimage = $this->create_newimage($newlenght, imagesy($this->newimage));
          
      for ($i=0; $i<imagesy($this->newimage); $i++) {
        for($j=0; $j<imagesx($this->newimage); $j++) {
          if($j<=imagesx($this->newimage)/2) { 
            $alfa=$j/(($this->ver_distance));
            $newlenght=($this->ver_distance+$i)*$alfa;
        
            $color_index = imagecolorat($this->newimage, (imagesx($this->newimage)/2)-$j, $i);
            $color_tran = imagecolorsforindex($this->newimage, $color_index);
          
            $pixel=imagecolorallocate($this->newimage, $color_tran['red'], $color_tran['green'], $color_tran['blue']);    
            imagesetpixel($this->perimage, (imagesx($this->perimage)/2)-$newlenght, $i, $pixel);
          } elseif($j>imagesx($this->newimage)/2) { 
            $alfa=($j-(imagesx($this->newimage)/2))/(($this->ver_distance));
            $newlenght=($this->ver_distance+$i)*$alfa;
        
            $color_index = imagecolorat($this->newimage, $j, $i);
            $color_tran = imagecolorsforindex($this->newimage, $color_index);
          
            $pixel=imagecolorallocate($this->newimage, $color_tran['red'], $color_tran['green'], $color_tran['blue']);    
            imagesetpixel($this->perimage, (imagesx($this->perimage)/2)+$newlenght, $i, $pixel);
          }  
        }
      }
      return true;
    } else {
      return false;
    }
  }
  
  public function make_ana_perspective_fill ($type="sub") {
    if ($this->originalimage!=false && $this->newimage!=false) {
      switch($type) {
        case "sub":
          $this->perimage = $this->create_newimage(imagesx($this->newimage), imagesy($this->newimage));            
      
          $alfa=imagesx($this->newimage)/(($this->ver_distance)+imagesy($this->newimage)); 
        
          for ($i=0; $i<imagesy($this->newimage); $i++) {
            $newlenght=($this->ver_distance+$i)*$alfa;
            imagecopyresampled($this->perimage, $this->newimage, (imagesx($this->newimage)-$newlenght)/2, $i, 0, $i, $newlenght, 1, imagesx($this->newimage), 1);
          }
        break;
        case "add":
        
          $alfa=imagesx($this->newimage)/(($this->ver_distance));
          $newlenght=($this->ver_distance+imagesy($this->newimage))*$alfa;
  
          $this->perimage = $this->create_newimage($newlenght, imagesy($this->newimage));
          
          for ($i=0; $i<imagesy($this->newimage); $i++) {
  
            $newlenght=($this->ver_distance+$i)*$alfa;
            imagecopyresampled($this->perimage, $this->newimage, (imagesx($this->perimage)-$newlenght)/2, $i, 0, $i, $newlenght, 1, imagesx($this->newimage), 1);
  
          }
          
        break;
      }
      return true;
    } else {
      return false;
    }
  }
  
  public function reset () {
    $this->type=false;
    
    $this->originalimage=false;
    $this->newimage=false;
    $this->perimage=false;
      
    $this->scale=false;   
    $this->or_distance=false;
    $this->ver_distance=false;
    
    return true; 
  }
  
  public function return_image ($type="auto") {
  
    if($type=="auto") {
      $type=$this->type;
    }
    
      switch ($type) { 
        case 'png':
          header('Content-Type: image/png') ;
          if($this->perimage==false) {
            imagepng($this->newimage);
          } elseif ($this->perimage==false && $this->newimage==false && $this->originalimage!=false) {
            imagepng($this->originalimage);
          } else {
            imagepng($this->perimage);
          }                       
        break;
        case 'jpg':
          header('Content-Type: image/jpeg') ;
          if($this->perimage==false) {
            imagejpeg($this->newimage);
          } elseif ($this->perimage==false && $this->newimage==false && $this->originalimage!=false) {
            imagejpeg($this->originalimage);
          } else {
            imagejpeg($this->perimage);
          }  
        break;
      }
      
      if($this->originalimage!=false) {
        imagedestroy($this->originalimage);
      }
      if($this->newimage!=false) {
        imagedestroy($this->newimage);
      }
      if($this->perimage!=false) {
        imagedestroy($this->perimage);
      }
                  
      return true;
    
  }
  
}



?>