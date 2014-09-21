<?php

	// jimport('incase.init');

	class incase {
		public $test ='(-o-)';

		protected static $incase;

		public static function getInstance() {
		    if (self::$incase === null) {
		    	self::$incase = new self();
		    }

		   	return self::$incase;
		}

		private function __construct() {
		}

		/* TEXT */
			// склонение слова
			function getEnd($number, $titles=array('товар', 'товара', 'товаров')) {
			   $cases = array (2, 0, 1, 1, 1, 2);
			   return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
			}

			// подстрока в строке
			function strpos($str,$substr) {
				$result = strpos ($str, $substr);
				if ($result === FALSE)
					return false;
				else
					return true;
			}

			// форматирование чисел
			function format($number=0, $decimals=false) {
				return $decimals ? number_format($number, 2, '.', ' ') : number_format($number, 0, '.', ' ');
			}

			function strimwidth($text, $width, $append_string = '...') {
				return trim( mb_strimwidth($text, 0, $width, $append_string) );
			}
		/* TEXT */

		/* FILES */

			//remote exist
			function exist($input){
				$Headers = get_headers($input);
				if( strpos($Headers[0], '200') )
					return true;
			}

			//remote filesize
			function filesize($url){
				$ch = curl_init($url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, TRUE);
				curl_setopt($ch, CURLOPT_NOBODY, TRUE);

				$data = curl_exec($ch);
				$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

				curl_close($ch);
				return $size;
			}
		/* FILES */

		/* JOOMLA */

			function getTemplate(){
				$app = JFactory::getApplication();
				return $app->getTemplate();
			}
			function getMenu(){
				$app = JFactory::getApplication();
				return $app->getMenu();
			}
			function getActive(){
				$app = JFactory::getApplication();
				return $app->getMenu()->getActive();
			}
			function frontpage(){
				$menu = & JSite::getMenu();
				if ($menu->getActive() == $menu->getDefault()) {
					return true;
				}
				return false;
			}
			function getAlias(){
				$this->app = JFactory::getApplication();
				$active = $this->app->getMenu()->getActive();
				return $active->alias;
			}
			function getProductById($id){
				JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
				$model = JModel::getInstance('Product', 'VirtueMartModel');
				$product = $model->getProduct($id, true, false, true, $quantity);
				return $product;
			}
			function getLinkById($id){
				$link = self::getProductById($id);
				return $link->link;
			}
			function getImageById($id){
				$db   =& JFactory::getDBO();
				$sql  = "   SELECT
				                   b.`file_url`
				           FROM
				                   ".$db->nameQuote('#__virtuemart_product_medias')." AS a
				           INNER JOIN
				                   ".$db->nameQuote('#__virtuemart_medias')." AS b ON a.`virtuemart_media_id` = b.`virtuemart_media_id`
				           WHERE
				                   a.".$db->nameQuote('virtuemart_product_id')." = ".$id."
				           AND
				                   b.".$db->nameQuote('file_mimetype')." = 'image/jpeg'
				           AND
				                   b.".$db->nameQuote('file_type')." = 'product'
				           AND
				                   b.".$db->nameQuote('file_is_forSale')." = '0'";

				$query = $db->setQuery($sql);
				$row  = $db->loadResultArray();
				if($db->getErrorNum()) {
				   JError::raiseError( 500, $db->stderr());
				}
				if(empty($row)) $row[] = JURI::base() . '/images/stories/virtuemart/product/empty.jpg';
				return $row[0];
			}
			function getImage($id, $quantity = NULL){
	                        $opt = '';
				preg_match('#::(.+):#iU', $id, $r);
	                        if(isset($r[1]))
	                        {
	                            $opt = $r[1];
	                        }
				JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
				$model = JModel::getInstance('Product', 'VirtueMartModel');
				$product = $model->getProduct($id, true, false, true, $quantity);
				if ( empty($opt) ){
					foreach ($product->customfieldsCart[0]->options as $key => $value) {
						if (!empty($value->custom_param)) {
							$arr = array('black.jpg', 'black1.jpg', 'white.jpg', 'white1.jpg');
							if (!in_array($value->custom_param, $arr)){
								return 'images/stories/virtuemart/product/' . $value->custom_param;
							}
						}
					}
					// return $product->images[0]->file_url;
					return self::getImageById($id);
				}else{
					$image = $product->customfieldsCart[0]->options[$opt]->custom_param;
					if( !empty($image) ){
						return 'images/stories/virtuemart/product/' . $image;
					}
				}
			}
		/* JOOMLA */

		/* HELPERS */

			/*<link rel="stylesheet" type="text/css" href="<?=incase::noCache('/templates/gantry/css-compiled/screen.css')?>" />*/
			function noCache($what){
				$change = md5(filemtime( ltrim($what, '/') ));
				return $what . '?' . $change;
			}
			function clog($what){
				echo '<script type="text/javascript">console.log("'.$what.'");</script>';
			}
			function debug_to_console( $data ) {
			   if ( is_array( $data ) ) {
			   	$output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
			   } else {
			      $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
			   }
			   echo $output;
			}
			function flog($what){
				self::debug_to_console($what);
				
				$file = fopen($_SERVER['DOCUMENT_ROOT'] . "/flog.txt", "a+");
				fwrite($file, $what."\r\n");
				fclose($file);
			}
		/* HELPERS */

		/* IMAGES */
function file_url($url){
  $parts = parse_url($url);
  $path_parts = array_map('rawurldecode', explode('/', $parts['path']));

  return
    $parts['scheme'] . '://' .
    $parts['host'] .
    implode('/', array_map('rawurlencode', $path_parts))
  ;
}
                        
			function thumb($input, $id, $a=0, $b=0, $proportional=TRUE){
				$dummy = 'images/dummy.png';
                                $ds = DIRECTORY_SEPARATOR;
				$_dir = 'images'.$ds.'com_ishop'.$ds;
				$dir = JPATH_ROOT.$ds.$_dir;
				$host = str_replace('/administrator', '', JUri::base()).$_dir;
                                
                                if (!preg_match('/gif|ico|jpg|jpeg|png|tiff|tif$/', strtolower($input), $regs))
                                {
//                                    return $dummy;
                                }

				// create cache folder
				if (!file_exists($dir))
                                {
                                    @mkdir($dir, 0777, true);
                                }
                                
				$ext = isset($regs[0])?'.'.$regs[0]:'.jpg';

				$img_base = $img_now = 'ishop_'.$id.$ext;
                                if($a AND $b )
                                {
                                    $img_now = 'ishop_'.$id.'_'.$a.'_'.$b;
                                    $img_now .= $proportional?'_a':'';
                                    $img_now .= $ext;
                                }
                                
                                $file_img_name = $dir.$img_now;
                                if(file_exists($file_img_name))
                                {
                                    return $host.$img_now;
                                }
                                
                                

				// create thumb
                                try {
                                        $file_img_base = $dir.$img_base;
                                        if(!file_exists($file_img_base) OR !filesize($file_img_base))
                                        {
                                            file_put_contents($file_img_base, file_get_contents(self::file_url($input)));
                                        }
                                        
                                        $url_img_base = $host.$img_base;
                                        
                                        $thumb = PhpThumbFactory::create($url_img_base);
                                        if($proportional == true){
                                                $thumb->adaptiveResize($a, $b);
                                        }else{
                                                $thumb->resize($a, $b);
                                        }
                                        $thumb->save($file_img_name);
                                        return $host.$img_now;
                                } catch (Exception $e) {
                                        if ( file_exists($dummy) ){
                                                return self::thumb($dummy, $a, $b, true);
                                        }else{
                                                return 'http://dummyimage.com/'.$a.'x'.$b.'/000/fff.png&text=/images/dummy.png';
                                        }
                                }
			}
		/* IMAGES */

	}
?>