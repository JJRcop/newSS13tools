<?php
class PNGMetadataExtractor {
    private static $pngSig;

    private static $crcSize;

    private static $textChunks;

    const VERSION = 1;
    const MAX_CHUNK_SIZE = 3145728; // 3 megabytes

    public function getMetadata( $filename ) {
        self::$pngSig = pack( "C8", 137, 80, 78, 71, 13, 10, 26, 10 );
        self::$crcSize = 4;
        /* based on list at http://owl.phy.queensu.ca/~phil/exiftool/TagNames/PNG.html#TextualData
         * and http://www.w3.org/TR/PNG/#11keywords
         */
        self::$textChunks = array(
            'xml:com.adobe.xmp' => 'xmp',
            # Artist is unofficial. Author is the recommended
            # keyword in the PNG spec. However some people output
            # Artist so support both.
            'artist' => 'Artist',
            'model' => 'Model',
            'make' => 'Make',
            'author' => 'Artist',
            'comment' => 'PNGFileComment',
            'description' => 'ImageDescription',
            'title' => 'ObjectName',
            'copyright' => 'Copyright',
            # Source as in original device used to make image
            # not as in who gave you the image
            'source' => 'Model',
            'software' => 'Software',
            'disclaimer' => 'Disclaimer',
            'warning' => 'ContentWarning',
            'url' => 'Identifier', # Not sure if this is best mapping. Maybe WebStatement.
            'label' => 'Label',
            'creation time' => 'DateTimeDigitized',
            /* Other potentially useful things - Document */
        );

        $frameCount = 0;
        $loopCount = 1;
        $text = array();
        $duration = 0.0;
        $bitDepth = 0;
        $colorType = 'unknown';

        if ( !$filename ) {
            throw new Exception( __METHOD__ . ": No file name specified" );
        } elseif ( !file_exists( $filename ) || is_dir( $filename ) ) {
            throw new Exception( __METHOD__ . ": File $filename does not exist" );
        }

        $fh = fopen( $filename, 'rb' );

        if ( !$fh ) {
            return;
        }

        // Check for the PNG header
        $buf = fread( $fh, 8 );
        if ( $buf != self::$pngSig ) {
            throw new Exception( __METHOD__ . ": Not a valid PNG file; header: $buf" );
        }

        // Read chunks
        while ( !feof( $fh ) ) {
            $buf = fread( $fh, 4 );
            if ( !$buf || strlen( $buf ) < 4 ) {
                throw new Exception( __METHOD__ . ": Read error" );
            }
            $chunk_size = unpack( "N", $buf );
            $chunk_size = $chunk_size[1];

            if ( $chunk_size < 0 ) {
                throw new Exception( __METHOD__ . ": Chunk size too big for unpack" );
            }

            $chunk_type = fread( $fh, 4 );
            if ( !$chunk_type || strlen( $chunk_type ) < 4 ) {
                throw new Exception( __METHOD__ . ": Read error" );
            }

            if ( $chunk_type == "IHDR" ) {
                $buf = self::read( $fh, $chunk_size );
                if ( !$buf || strlen( $buf ) < $chunk_size ) {
                    throw new Exception( __METHOD__ . ": Read error" );
                }
                $bitDepth = ord( substr( $buf, 8, 1 ) );
                // Detect the color type in British English as per the spec
                // http://www.w3.org/TR/PNG/#11IHDR
                switch ( ord( substr( $buf, 9, 1 ) ) ) {
                    case 0:
                        $colorType = 'greyscale';
                        break;
                    case 2:
                        $colorType = 'truecolour';
                        break;
                    case 3:
                        $colorType = 'index-coloured';
                        break;
                    case 4:
                        $colorType = 'greyscale-alpha';
                        break;
                    case 6:
                        $colorType = 'truecolour-alpha';
                        break;
                    default:
                        $colorType = 'unknown';
                        break;
                }
            } elseif ( $chunk_type == "acTL" ) {
                $buf = fread( $fh, $chunk_size );
                if ( !$buf || strlen( $buf ) < $chunk_size || $chunk_size < 4 ) {
                    throw new Exception( __METHOD__ . ": Read error" );
                }

                $actl = unpack( "Nframes/Nplays", $buf );
                $frameCount = $actl['frames'];
                $loopCount = $actl['plays'];
            } elseif ( $chunk_type == "fcTL" ) {
                $buf = self::read( $fh, $chunk_size );
                if ( !$buf || strlen( $buf ) < $chunk_size ) {
                    throw new Exception( __METHOD__ . ": Read error" );
                }
                $buf = substr( $buf, 20 );
                if ( strlen( $buf ) < 4 ) {
                    throw new Exception( __METHOD__ . ": Read error" );
                }

                $fctldur = unpack( "ndelay_num/ndelay_den", $buf );
                if ( $fctldur['delay_den'] == 0 ) {
                    $fctldur['delay_den'] = 100;
                }
                if ( $fctldur['delay_num'] ) {
                    $duration += $fctldur['delay_num'] / $fctldur['delay_den'];
                }
            } elseif ( $chunk_type == "iTXt" ) {
                // Extracts iTXt chunks, uncompressing if necessary.
                $buf = self::read( $fh, $chunk_size );
                $items = array();
                if ( preg_match(
                    '/^([^\x00]{1,79})\x00(\x00|\x01)\x00([^\x00]*)(.)[^\x00]*\x00(.*)$/Ds',
                    $buf, $items )
                ) {
                    /* $items[1] = text chunk name, $items[2] = compressed flag,
                     * $items[3] = lang code (or ""), $items[4]= compression type.
                     * $items[5] = content
                     */

                    // Theoretically should be case-sensitive, but in practise...
                    $items[1] = strtolower( $items[1] );
                    if ( !isset( self::$textChunks[$items[1]] ) ) {
                        // Only extract textual chunks on our list.
                        fseek( $fh, self::$crcSize, SEEK_CUR );
                        continue;
                    }

                    $items[3] = strtolower( $items[3] );
                    if ( $items[3] == '' ) {
                        // if no lang specified use x-default like in xmp.
                        $items[3] = 'x-default';
                    }

                    // if compressed
                    if ( $items[2] == "\x01" ) {
                        if ( function_exists( 'gzuncompress' ) && $items[4] === "\x00" ) {
                            //MediaWiki\suppressWarnings();
                            $items[5] = gzuncompress( $items[5] );
                            //MediaWiki\restoreWarnings();

                            if ( $items[5] === false ) {
                                // decompression failed
                                wfDebug( __METHOD__ . ' Error decompressing iTxt chunk - ' . $items[1] . "\n" );
                                fseek( $fh, self::$crcSize, SEEK_CUR );
                                continue;
                            }
                        } else {
                            wfDebug( __METHOD__ . ' Skipping compressed png iTXt chunk due to lack of zlib,'
                                . " or potentially invalid compression method\n" );
                            fseek( $fh, self::$crcSize, SEEK_CUR );
                            continue;
                        }
                    }
                    $finalKeyword = self::$textChunks[$items[1]];
                    $text[$finalKeyword][$items[3]] = $items[5];
                    $text[$finalKeyword]['_type'] = 'lang';
                } else {
                    // Error reading iTXt chunk
                    throw new Exception( __METHOD__ . ": Read error on iTXt chunk" );
                }
            } elseif ( $chunk_type == 'tEXt' ) {
                $buf = self::read( $fh, $chunk_size );

                // In case there is no \x00 which will make explode fail.
                if ( strpos( $buf, "\x00" ) === false ) {
                    throw new Exception( __METHOD__ . ": Read error on tEXt chunk" );
                }

                list( $keyword, $content ) = explode( "\x00", $buf, 2 );
                if ( $keyword === '' || $content === '' ) {
                    throw new Exception( __METHOD__ . ": Read error on tEXt chunk" );
                }

                // Theoretically should be case-sensitive, but in practise...
                $keyword = strtolower( $keyword );
                if ( !isset( self::$textChunks[$keyword] ) ) {
                    // Don't recognize chunk, so skip.
                    fseek( $fh, self::$crcSize, SEEK_CUR );
                    continue;
                }
                //MediaWiki\suppressWarnings();
                $content = iconv( 'ISO-8859-1', 'UTF-8', $content );
               // MediaWiki\restoreWarnings();

                if ( $content === false ) {
                    throw new Exception( __METHOD__ . ": Read error (error with iconv)" );
                }

                $finalKeyword = self::$textChunks[$keyword];
                $text[$finalKeyword]['x-default'] = $content;
                $text[$finalKeyword]['_type'] = 'lang';
            } elseif ( $chunk_type == 'zTXt' ) {
                if ( function_exists( 'gzuncompress' ) ) {
                    $buf = self::read( $fh, $chunk_size );

                    // In case there is no \x00 which will make explode fail.
                    if ( strpos( $buf, "\x00" ) === false ) {
                        throw new Exception( __METHOD__ . ": Read error on zTXt chunk" );
                    }

                    list( $keyword, $postKeyword ) = explode( "\x00", $buf, 2 );
                    if ( $keyword === '' || $postKeyword === '' ) {
                        throw new Exception( __METHOD__ . ": Read error on zTXt chunk" );
                    }
                    // Theoretically should be case-sensitive, but in practise...
                    $keyword = strtolower( $keyword );

                    if ( !isset( self::$textChunks[$keyword] ) ) {
                        // Don't recognize chunk, so skip.
                        fseek( $fh, self::$crcSize, SEEK_CUR );
                        continue;
                    }
                    $compression = substr( $postKeyword, 0, 1 );
                    $content = substr( $postKeyword, 1 );
                    if ( $compression !== "\x00" ) {
                        wfDebug( __METHOD__ . " Unrecognized compression method in zTXt ($keyword). Skipping.\n" );
                        fseek( $fh, self::$crcSize, SEEK_CUR );
                        continue;
                    }

                    //MediaWiki\suppressWarnings();
                    $content = gzuncompress( $content );
                   // MediaWiki\restoreWarnings();

                    if ( $content === false ) {
                        // decompression failed
                        wfDebug( __METHOD__ . ' Error decompressing zTXt chunk - ' . $keyword . "\n" );
                        fseek( $fh, self::$crcSize, SEEK_CUR );
                        continue;
                    }

                    //MediaWiki\suppressWarnings();
                    $content = iconv( 'ISO-8859-1', 'UTF-8', $content );
                    //MediaWiki\restoreWarnings();

                    if ( $content === false ) {
                        throw new Exception( __METHOD__ . ": Read error (error with iconv)" );
                    }

                    $finalKeyword = self::$textChunks[$keyword];
                    $text[$finalKeyword]['x-default'] = $content;
                    $text[$finalKeyword]['_type'] = 'lang';
                } else {
                    wfDebug( __METHOD__ . " Cannot decompress zTXt chunk due to lack of zlib. Skipping.\n" );
                    fseek( $fh, $chunk_size, SEEK_CUR );
                }
            } elseif ( $chunk_type == 'tIME' ) {
                // last mod timestamp.
                if ( $chunk_size !== 7 ) {
                    throw new Exception( __METHOD__ . ": tIME wrong size" );
                }
                $buf = self::read( $fh, $chunk_size );
                if ( !$buf || strlen( $buf ) < $chunk_size ) {
                    throw new Exception( __METHOD__ . ": Read error" );
                }

                // Note: spec says this should be UTC.
                $t = unpack( "ny/Cm/Cd/Ch/Cmin/Cs", $buf );
                $strTime = sprintf( "%04d%02d%02d%02d%02d%02d",
                    $t['y'], $t['m'], $t['d'], $t['h'],
                    $t['min'], $t['s'] );

                $exifTime = time();

                if ( $exifTime ) {
                    $text['DateTime'] = $exifTime;
                }
            } elseif ( $chunk_type == 'pHYs' ) {
                // how big pixels are (dots per meter).
                if ( $chunk_size !== 9 ) {
                    throw new Exception( __METHOD__ . ": pHYs wrong size" );
                }

                $buf = self::read( $fh, $chunk_size );
                if ( !$buf || strlen( $buf ) < $chunk_size ) {
                    throw new Exception( __METHOD__ . ": Read error" );
                }

                $dim = unpack( "Nwidth/Nheight/Cunit", $buf );
                if ( $dim['unit'] == 1 ) {
                    // Need to check for negative because php
                    // doesn't deal with super-large unsigned 32-bit ints well
                    if ( $dim['width'] > 0 && $dim['height'] > 0 ) {
                        // unit is meters
                        // (as opposed to 0 = undefined )
                        $text['XResolution'] = $dim['width']
                            . '/100';
                        $text['YResolution'] = $dim['height']
                            . '/100';
                        $text['ResolutionUnit'] = 3;
                        // 3 = dots per cm (from Exif).
                    }
                }
            } elseif ( $chunk_type == "IEND" ) {
                break;
            } else {
                fseek( $fh, $chunk_size, SEEK_CUR );
            }
            fseek( $fh, self::$crcSize, SEEK_CUR );
        }
        fclose( $fh );

        if ( $loopCount > 1 ) {
            $duration *= $loopCount;
        }

        if ( isset( $text['DateTimeDigitized'] ) ) {
            // Convert date format from rfc2822 to exif.
            foreach ( $text['DateTimeDigitized'] as $name => &$value ) {
                if ( $name === '_type' ) {
                    continue;
                }

                // @todo FIXME: Currently timezones are ignored.
                // possibly should be wfTimestamp's
                // responsibility. (at least for numeric TZ)
                $formatted = wfTimestamp( TS_EXIF, $value );
                if ( $formatted ) {
                    // Only change if we could convert the
                    // date.
                    // The png standard says it should be
                    // in rfc2822 format, but not required.
                    // In general for the exif stuff we
                    // prettify the date if we can, but we
                    // display as-is if we cannot or if
                    // it is invalid.
                    // So do the same here.

                    $value = $formatted;
                }
            }
        }

        return array(
            'frameCount' => $frameCount,
            'loopCount' => $loopCount,
            'duration' => $duration,
            'text' => $text,
            'bitDepth' => $bitDepth,
            'colorType' => $colorType,
        );
    }

    public function getSprites( $filename ) {
        $data = $this->getSpriteData($filename);
    }

    public function loadImage( $filename ) {
        $data = $this->getMetadata($filename)['text']['ImageDescription']['x-default'];
        $data = explode("\n", trim($data));
        $width = 32;
        $height = 32;
        $version = 0;

        $state = "";
        $dirs = "";
        $frames = "";
        $delay = "";
        $loop = "";
        $hotspot = "";
        $rewind = "";
        $movement = "";
        $first = true;

        $sprites = [];

        foreach($data as $i => $line){
          $line = trim($line);
          $dataPair = explode("=", $line);
          if(count($dataPair) != 2){
            continue;
          }
          //print "<br>$key - $value";
          
          $key = trim($dataPair[0]);
          $value = trim($dataPair[1]);
          
          if($key == "version"){
            $version = intval($value);
            continue;
          }
          if($key == "width"){
            $width = intval($value);
            continue;
          }
          if($key == "height"){
            $height = intval($value);
            continue;
          }
          if($key == "state"){
            if($first){
              $first = false;
            }else{
              $sprites[] = Array(
                "state" => $state, 
                "dirs" => $dirs, 
                "frames" => $frames, 
                "delay" => $delay, 
                "loop" => $loop, 
                "hotspot" => $hotspot, 
                "movement" => $movement, 
                "rewind" => $rewind
              );
            }
            $state = "";
            $dirs = "";
            $frames = "";
            $delay = "";
            $loop = "";
            $rewind = "";
            $hotspot = "";
            $movement = "";
            if(startsWith($value, '"')){
              $value = substr($value, 1);
            }
            if(endsWith($value, '"')){
              $value = substr($value, 0, strlen($value) -1);
            }
            $state = $value;
            continue;
          }
          if($key == "dirs"){
            $dirs = $value;
            continue;
          }
          if($key == "frames"){
            $frames = $value;
            continue;
          }
          if($key == "loop"){
            $loop = $value;
            continue;
          }
          if($key == "delay"){
            $delay = $value;
            continue;
          }
          if($key == "hotspot"){
            $hotspot = $value;
            continue;
          }
          if($key == "movement"){
            $movement = $value;
            continue;
          }
          if($key == "rewind"){
            $rewind = $value;
            continue;
          }
          print "<br><font color='red'>$key - $value</font>";
        }

        if(!$first){
          $sprites[] = Array(
                "state" => $state, 
                "dirs" => $dirs, 
                "frames" => $frames, 
                "delay" => $delay, 
                "loop" => $loop, 
                "hotspot" => $hotspot, 
                "movement" => $movement, 
                "rewind" => $rewind
              );
        }
        $image = imagecreatefrompng($filename);
        imagesavealpha($image, true);
        $trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $trans_colour);
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);
        $spritesX = $imageWidth / $width;
        $spritesY = $imageHeight / $height;

        $spriteNumber = 0;
        foreach($sprites as $i => &$spriteData){
          $spriteName = $spriteData["state"];
          $spriteName = preg_replace('/[^A-Za-z0-9 _ .-]/', '', $spriteName);
          //$spriteName = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $spriteName);
          //$spriteName = preg_replace("([\.]{2,})", '', $spriteName);

          $frames = intval($spriteData["frames"]);
          $dirs = intval($spriteData["dirs"]);
          $rewind = intval($spriteData["rewind"]);
          if(!$frames){
            $frames = 1;
          }
          if(!$dirs){
            $dirs = 1;
          }
          if(!$rewind){
            $rewind = 0;
          }
          
          // print "<h2>Sprite: $spriteName</h2>";
          for($dir = 0; $dir < $dirs; $dir++){
            // print "<h3>Dir: $dir (spriteNum = $spriteNumber)</h3>";
            if($frames == 1){ 
              $sprite = imagecreatetruecolor($width, $height);
              imagesavealpha($sprite, true);
              $trans_colour = imagecolorallocatealpha($sprite, 0, 0, 0, 127);
              imagefill($sprite, 0, 0, $trans_colour);
              
              $posX = ($spriteNumber + $dir) % $spritesX;
              $posY = floor(($spriteNumber + $dir) / $spritesX);
              // print "<br>posX = $posX; posY = $posY<br>";
              
                imagecopy($sprite, $image, 0, 0, $posX * $width, $posY * $height, $width, $height);
                ob_start(); 
                imagepng($sprite, NULL, 0);
                $spriteData['base64'] = base64_encode(ob_get_contents());
                $spriteData['X'] = $posX;
                $spriteData['Y'] = $posY;
                $spriteData['dir'][] = base64_encode(ob_get_contents());
                ob_end_clean();
                
            }else{
                
              $gifFrameList = Array();
              for($frameNum = 0; $frameNum < $frames; $frameNum++){
                $sprite = imagecreatetruecolor($width, $height);
                imagesavealpha($sprite, true);
                $trans_colour = imagecolortransparent($sprite, 127<<24);
                imagefill($sprite, 0, 0, $trans_colour);
                
                $posX = ($spriteNumber + ($dirs * $frameNum) + $dir) % $spritesX;
                $posY = floor(($spriteNumber + ($dirs * $frameNum) + $dir) / $spritesX);
                //print "<br>posX = $posX; posY = $posY<br>";
                imagecopy($sprite, $image, 0, 0, $posX * $width, $posY * $height, $width, $height);
                // if(!file_exists("$folderName/$spriteName"."_$dir")){
                //   mkdir("$folderName/$spriteName"."_$dir");
                // }
                ob_start(); 
                imagepng($sprite);
                $spriteData['base64'] = base64_encode(ob_get_contents());
                $spriteData['dir'][] = base64_encode(ob_get_contents());
                ob_end_clean();
                $gifFrameList[] = $sprite;
              }
              
              $delayList = Array();
              $delay = $spriteData["delay"];
              $delays = explode(",", $delay);
              foreach($delays as $i => $dl){
                $dl = intval(trim($dl));
                $delayList[] = $dl * 10;
              }
              
              if($rewind){
                //print "REWIND";
                for($i = count($gifFrameList) - 2; $i > 0; $i--){
                  $gifFrameList[] = $gifFrameList[$i];
                }
                for($i = count($delayList) - 2; $i > 0; $i--){
                  $delayList[] = $delayList[$i];
                }
                //print_r($gifFrameList);
              }
              
              $gc = new GifCreator();
              $gc->create($gifFrameList, $delayList, 0);
              $gifBinary = $gc->getGif();
              $spriteData['base64'] = base64_encode($gifBinary);
            }
          }
          $spriteNumber += $frames * $dirs;
        }
        return $sprites;
    }


    private static function read( $fh, $size ) {
        if ( $size > self::MAX_CHUNK_SIZE ) {
            throw new Exception( __METHOD__ . ': Chunk size of ' . $size .
                ' too big. Max size is: ' . self::MAX_CHUNK_SIZE );
        }

        return fread( $fh, $size );
    }
}

/**
 * Create an animated GIF from multiple images
 * 
 * @version 1.0
 * @link https://github.com/Sybio/GifCreator
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Clément Guillemain
 */
class GifCreator
{
    /**
     * @var string The gif string source (old: this->GIF)
     */
    private $gif;
    
    /**
     * @var string Encoder version (old: this->VER)
     */
  private $version;
    
    /**
     * @var boolean Check the image is build or not (old: this->IMG)
     */
    private $imgBuilt;

    /**
     * @var array Frames string sources (old: this->BUF)
     */
  private $frameSources;
    
    /**
     * @var integer Gif loop (old: this->LOP)
     */
  private $loop;
    
    /**
     * @var integer Gif dis (old: this->DIS)
     */
  private $dis;
    
    /**
     * @var integer Gif color (old: this->COL)
     */
  private $colour;
    
    /**
     * @var array (old: this->ERR)
     */
  private $errors;
 
    // Methods
    // ===================================================================================
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reset();
        
        // Static data
        $this->version = 'GifCreator: Under development';
        $this->errors = array(
            'ERR00' => 'Does not supported function for only one image.',
        'ERR01' => 'Source is not a GIF image.',
        'ERR02' => 'You have to give resource image variables, image URL or image binary sources in $frames array.',
        'ERR03' => 'Does not make animation from animated GIF source.',
        );
    }

  /**
     * Create the GIF string (old: GIFEncoder)
     * 
     * @param array $frames An array of frame: can be file paths, resource image variables, binary sources or image URLs
     * @param array $durations An array containing the duration of each frame
     * @param integer $loop Number of GIF loops before stopping animation (Set 0 to get an infinite loop)
     * 
     * @return string The GIF string source
     */
  public function create($frames = array(), $durations = array(), $loop = 0)
    {
    if (!is_array($frames) && !is_array($GIF_tim)) {
            
            throw new \Exception($this->version.': '.$this->errors['ERR00']);
    }
        
    $this->loop = ($loop > -1) ? $loop : 0;
    $this->dis = 2;
        
    for ($i = 0; $i < count($frames); $i++) {
      
      if (is_resource($frames[$i])) { // Resource var
                
                $resourceImg = $frames[$i];
                
                ob_start();
                imagegif($frames[$i]);
                $this->frameSources[] = ob_get_contents();
                ob_end_clean();
                
            } elseif (is_string($frames[$i])) { // File path or URL or Binary source code
           
                if (file_exists($frames[$i]) || filter_var($frames[$i], FILTER_VALIDATE_URL)) { // File path
                    
                    $frames[$i] = file_get_contents($frames[$i]);                    
                }
                
                $resourceImg = imagecreatefromstring($frames[$i]);
                
                ob_start();
                imagegif($resourceImg);
                $this->frameSources[] = ob_get_contents();
                ob_end_clean();
                 
      } else { // Fail
                
                throw new \Exception($this->version.': '.$this->errors['ERR02'].' ('.$mode.')');
      }
            
            if ($i == 0) {
                
                $colour = imagecolortransparent($resourceImg);
            }
            
      if (substr($this->frameSources[$i], 0, 6) != 'GIF87a' && substr($this->frameSources[$i], 0, 6) != 'GIF89a') {
       
                throw new \Exception($this->version.': '.$i.' '.$this->errors['ERR01']);
      }
            
      for ($j = (13 + 3 * (2 << (ord($this->frameSources[$i] { 10 }) & 0x07))), $k = TRUE; $k; $j++) {
       
        switch ($this->frameSources[$i] { $j }) {
            
          case '!':
                    
            if ((substr($this->frameSources[$i], ($j + 3), 8)) == 'NETSCAPE') {
                            
                            throw new \Exception($this->version.': '.$this->errors['ERR03'].' ('.($i + 1).' source).');
            }
                        
          break;
                        
          case ';':
                    
            $k = false;
          break;
        }
      }
            
            unset($resourceImg);
    }
    
        if (isset($colour)) {
            
            $this->colour = $colour;
                                    
        } else {
            
            $red = $green = $blue = 0;
            $this->colour = ($red > -1 && $green > -1 && $blue > -1) ? ($red | ($green << 8) | ($blue << 16)) : -1;
        }
        
    $this->gifAddHeader();
        
    for ($i = 0; $i < count($this->frameSources); $i++) {
      
      $this->addGifFrames($i, $durations[$i]);
    }
        
    $this->gifAddFooter();
        
        return $this->gif;
  }
    
    // Internals
    // ===================================================================================
    
  /**
     * Add the header gif string in its source (old: GIFAddHeader)
     */
  public function gifAddHeader()
    {
    $cmap = 0;

    if (ord($this->frameSources[0] { 10 }) & 0x80) {
      
      $cmap = 3 * (2 << (ord($this->frameSources[0] { 10 }) & 0x07));

      $this->gif .= substr($this->frameSources[0], 6, 7);
      $this->gif .= substr($this->frameSources[0], 13, $cmap);
      $this->gif .= "!\377\13NETSCAPE2.0\3\1".$this->encodeAsciiToChar($this->loop)."\0";
    }
  }
    
  /**
     * Add the frame sources to the GIF string (old: GIFAddFrames)
     * 
     * @param integer $i
     * @param integer $d
     */
  public function addGifFrames($i, $d)
    {
    $Locals_str = 13 + 3 * (2 << (ord($this->frameSources[ $i ] { 10 }) & 0x07));

    $Locals_end = strlen($this->frameSources[$i]) - $Locals_str - 1;
    $Locals_tmp = substr($this->frameSources[$i], $Locals_str, $Locals_end);

    $Global_len = 2 << (ord($this->frameSources[0 ] { 10 }) & 0x07);
    $Locals_len = 2 << (ord($this->frameSources[$i] { 10 }) & 0x07);

    $Global_rgb = substr($this->frameSources[0], 13, 3 * (2 << (ord($this->frameSources[0] { 10 }) & 0x07)));
    $Locals_rgb = substr($this->frameSources[$i], 13, 3 * (2 << (ord($this->frameSources[$i] { 10 }) & 0x07)));

    $Locals_ext = "!\xF9\x04".chr(($this->dis << 2) + 0).chr(($d >> 0 ) & 0xFF).chr(($d >> 8) & 0xFF)."\x0\x0";

    if ($this->colour > -1 && ord($this->frameSources[$i] { 10 }) & 0x80) {
      
      for ($j = 0; $j < (2 << (ord($this->frameSources[$i] { 10 } ) & 0x07)); $j++) {
       
        if (ord($Locals_rgb { 3 * $j + 0 }) == (($this->colour >> 16) & 0xFF) &&
          ord($Locals_rgb { 3 * $j + 1 }) == (($this->colour >> 8) & 0xFF) &&
          ord($Locals_rgb { 3 * $j + 2 }) == (($this->colour >> 0) & 0xFF)
        ) {
          $Locals_ext = "!\xF9\x04".chr(($this->dis << 2) + 1).chr(($d >> 0) & 0xFF).chr(($d >> 8) & 0xFF).chr($j)."\x0";
          break;
        }
      }
    }
        
    switch ($Locals_tmp { 0 }) {
      
      case '!':
            
        $Locals_img = substr($Locals_tmp, 8, 10);
        $Locals_tmp = substr($Locals_tmp, 18, strlen($Locals_tmp) - 18);
                                
      break;
                
      case ',':
            
        $Locals_img = substr($Locals_tmp, 0, 10);
        $Locals_tmp = substr($Locals_tmp, 10, strlen($Locals_tmp) - 10);
                                
      break;
    }
        
    if (ord($this->frameSources[$i] { 10 }) & 0x80 && $this->imgBuilt) {
      
      if ($Global_len == $Locals_len) {
       
        if ($this->gifBlockCompare($Global_rgb, $Locals_rgb, $Global_len)) {
            
          $this->gif .= $Locals_ext.$Locals_img.$Locals_tmp;
                    
        } else {
            
          $byte = ord($Locals_img { 9 });
          $byte |= 0x80;
          $byte &= 0xF8;
          $byte |= (ord($this->frameSources[0] { 10 }) & 0x07);
          $Locals_img { 9 } = chr($byte);
          $this->gif .= $Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp;
        }
                
      } else {
       
        $byte = ord($Locals_img { 9 });
        $byte |= 0x80;
        $byte &= 0xF8;
        $byte |= (ord($this->frameSources[$i] { 10 }) & 0x07);
        $Locals_img { 9 } = chr($byte);
        $this->gif .= $Locals_ext.$Locals_img.$Locals_rgb.$Locals_tmp;
      }
            
    } else {
      
      $this->gif .= $Locals_ext.$Locals_img.$Locals_tmp;
    }
        
    $this->imgBuilt = true;
  }
    
  /**
     * Add the gif string footer char (old: GIFAddFooter)
     */
  public function gifAddFooter()
    {
    $this->gif .= ';';
  }
    
  /**
     * Compare two block and return the version (old: GIFBlockCompare)
     * 
     * @param string $globalBlock
     * @param string $localBlock
     * @param integer $length
     * 
     * @return integer
   */
  public function gifBlockCompare($globalBlock, $localBlock, $length)
    {
    for ($i = 0; $i < $length; $i++) {
      
      if ($globalBlock { 3 * $i + 0 } != $localBlock { 3 * $i + 0 } ||
        $globalBlock { 3 * $i + 1 } != $localBlock { 3 * $i + 1 } ||
        $globalBlock { 3 * $i + 2 } != $localBlock { 3 * $i + 2 }) {
        
                return 0;
      }
    }

    return 1;
  }
    
  /**
     * Encode an ASCII char into a string char (old: GIFWord)
     * 
     * $param integer $char ASCII char
     * 
     * @return string
   */
  public function encodeAsciiToChar($char)
    {
    return (chr($char & 0xFF).chr(($char >> 8) & 0xFF));
  }
    
    /**
     * Reset and clean the current object
     */
    public function reset()
    {
        $this->frameSources;
        $this->gif = 'GIF89a'; // the GIF header
        $this->imgBuilt = false;
        $this->loop = 0;
        $this->dis = 2;
        $this->colour = -1;
    }
    
    // Getter / Setter
    // ===================================================================================
    
  /**
     * Get the final GIF image string (old: GetAnimation)
     * 
     * @return string
   */
  public function getGif()
    {
    return $this->gif;
  }
}