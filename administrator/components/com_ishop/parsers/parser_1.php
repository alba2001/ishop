<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

/**
 *$opts=array('http'=>array('method'=>"GET",'header'=>"Accept-language: en\r\n"."Cookie: cookie_name=cookie_value\r\n",'user_agent'=>'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10.4; en-US; rv:1.9.2.28) Gecko/20120306 Firefox/3.6.28'));
$context=stream_context_create($opts);
$data=file_get_html('http://site.ru/',false,$context); 
 */
// No direct access
defined('_JEXEC') or die;

include_once('simple_html_dom.php');
require_once JPATH_ADMINISTRATOR.'/components/com_ishop/models/category.php'; 
require_once JPATH_ADMINISTRATOR.'/components/com_ishop/models/product.php'; 
jimport('joomla.filesystem.file');

/**
 * zavod parser class.
 * Красная Пресня
 */
class IshopParseZavod_1
{

    private $_file_data;
    private $_file_category;
    private $_base_link;
    private $_base_category_link;
    private $_zavod_id;


    public function __construct() {
        $this->_zavod_id = '1';
        $this->_file_data = JPATH_ROOT.DS.'tmp'.DS.'parse_'.$this->_zavod_id.'_data.txt';
        $this->_file_category = JPATH_ROOT.DS.'tmp'.DS.'parse_'.$this->_zavod_id.'_category.txt';
        $this->_base_link = 'http://www.kazanova.su';
        $this->_base_category_link = 'http://www.kazanova.su/shop/';
    }
/**
 * =============================================================================
 * Этап создания категорий 
 * ============================================================================= 
 */


    /**
     * Список категорий
     * заполняется вручную
     * @return type 
     */
    private function _get_categories_array()
    {
        $data = $this->_get_data();
        $category_sourse_list = $data['category_sourse_list'];
        if(!$category_sourse_list)
        {
            // Не введен список категорий
            return $this->_get_categories_array_();

        }
        $categories = array();
        
        $category_data = array();
        foreach ($category_sourse_list as $category)
        {
            if($category)
            {
                $category_data = explode('^', $category);
//                if(strpos($category_data[1], 'http'))
                {
                    $categories[] = array(
                        'link'=>$category_data[1],
                        'name'=>$category_data[0]
                    );
                }
            }
        }

        return $categories;
    }
    
    
    private function _get_categories()
    {
        $_categories = $this->_get_categories_array();
        if(!$_categories)
        {
            return array(0,  JText::_('COM_ISHOP_CATEGORY_LIST_NOT_ENTERED'));
        }
        
        // Преобразовываем массив к удобному для обработки виду
        // и, не теряя времени, записываем категории в БД
        $categories = array();
        $k = 0;
        foreach($_categories as $category)
        {
            // Создаем категорию
            $name = $category['name'];
            
            list($category_id, $category_path) = $this->_create_category($name);
            
            if(!$category_id)
            {
                // Не смогли сохранить категорию
                return array(0,  JText::_('COM_ISHOP_CANOT_SAVE_CATEGORY'));
            }
            $categories[] = array(
                'name' =>  $name,
                'link' =>  $category['link'],
                'category_id' => $category_id,
                'category_path' => $category_path,
            );
            $k++;
        }
        
        return $categories;
    }
    

    /**
    *  Создаем категорию
    * @param string тайтл категории
    * @param int ID родительской категории
    * @param string as int уровень вложености категории
    */
    private function _create_category($name, $parent_id = 0, $parent_path = '', $level = 2)
    {
        $category_data = $this->_get_category_data();
        
        if(!$parent_id)
        {
            $parent_id = $category_data['parent_id'][0];
        }
        if(!$parent_path)
        {
            $parent_path = $category_data['path'][0];
        }
        $category_model = new IshopModelCategory;
        $category_save_data = array(
            'name'=>  $name,
            'parent_path'=>$parent_path,
            'parent_id'=>$parent_id,
            'level'=>$level,
            'zavod'=>$this->_zavod_id,
        );
        // Сохраняем категорию
        list($result, $category_created) = $category_model->create_category($category_save_data);
        if(!$result)
        {
            // Не смогли сохранить категорию
            return array(0,0);
        }
        $category_path = $parent_path.'/'.JApplication::stringURLSafe($name);
        return  array($category_created['id'], $category_path);
    }
    
    /**
     * Создание категорий
     */
    public function main_page()
    {
        $data = $this->_get_data();
        
        if(!$data)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_PARSE_PAGE'));
        }
        
        // Получаем список категорий
        $data['categories'] = $this->_get_categories();
        
        if(!$data['categories'][0])
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGOIRIES'));
        }
        
        //Устанавливаем город
        $link = 'http://www.kazanova.su//bitrix/templates/kazanova_su/includes/ajax_auth.php?change_city=1&id=ekb';
        file_get_html($link);
        
        // Сохраняем данные
        $data['func'][0] = "get_category_page";
        $this->_set_data($data);
        
         // Переходим на первую страницу категории
        $link = $data['categories'][0]['link'];
        return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
    }
    
/**
 * =============================================================================
 * Этап парсинга 
 * ============================================================================= 
 */

    /**
     * Страница главной категории
     * @return type 
     */
    public function get_category_page()
    {
        
        $data = $this->_get_data();
        if(!$data)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_PARSE_PAGE'));
        }
        // Если закончились категории, выходим из парсинга
        if(!$data['categories'])
        {
            return array(1,JText::_('COM_ISHOP_PARSE_SUCCES'));
        }
        
        $data['category'] = array_shift($data['categories']);
        
        
        // Сохраняем данные
        array_unshift($data['func'], 'get_category_items_page');
        $this->_set_data($data);

        $link = $data['category']['link'];
        return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
    }

    /**
     * Страница подкатегории
     * @return type 
     */
    public function get_category_items_page()
    {
        $category_model = new IshopModelCategory;
        $data = $this->_get_data();
        if(!$data)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_PARSE_PAGE'));
        }
        
        $subcategories = array();
        
        $html = file_get_html($data['category']['link']);
        
        // Если есть пагинация
        $pages = $html->find('div.modern-page-navigation');
        $pagelinks = '';
        $page_links = array();
        if(isset($pages[0]))
        {
            $pagelinks = $pages[0]->find('a');
            foreach($pagelinks as $pagelink)
            {
                $page_links[] = $this->_base_link.$pagelink->href;
            }
        }
            
        // Если нет подкатегорий
        if(!$html->find('div.b-catalog-rubrics_item'))
        {

            $category_created_items = array();
            $html = $html->find('div.catalog-section');
            $html = $html[0];
            do
            {
                $catalog_items = $html->find('div.b-catalog-box_item');
                foreach ($catalog_items as $catalog_item)
                {
                    $links = $catalog_item->find('a');
                    $images = $links[0]->find('img');
                    $category_created_items[] = array(
                        'link' =>  $this->_base_link.$links[0]->href,
                        'img_small' => $this->_base_link.$images[0]->src,
                    );
                }
                if(isset($page_links[0]))
                {
                    $page_link = $page_links[0];
                    array_shift($page_links);
                    if(isset($page_links[0])) // Чтобы лишний раз не парсить страницу
                    {
                        $html = file_get_html($page_link);
                    }
                }
            }
            while (isset($page_links[0]));
            $data['category']['items'] = $category_created_items;
            $data['category']['id'] = $data['category']['category_id'];
            $subcategories[] = $data['category'];
        }
        else
        {
            $selected_lis = $html->find('li.root-selected');
            $selected_uls = $selected_lis[0]->find('ul.sub');
            $selected_sub_lis = $selected_uls[0]->find('li');

            if($selected_sub_lis)
            {
                for($i = 0; $i < count($selected_sub_lis); $i++)
                {
                    $selected_sub_li = $selected_sub_lis[$i];
                    $a_item_titles = $selected_sub_li->find('a');
                    $span_item_titles = $a_item_titles[0]->find('span.b-sub-hold');
                    if(!isset($span_item_titles[0]))
                    {
                        echo '461';
                        var_dump($data['category']);
                        exit;
                    }
                    $item_title = iconv("windows-1251", "utf-8", trim($span_item_titles[0]->innertext));
                    $category_save_data = array(
                        'name'=>  $item_title,
                        'parent_path'=>$data['category']['category_path'],
                        'parent_id'=>$data['category']['category_id'],
                        'level'=>3,
                        'zavod'=>$this->_zavod_id,
                    );

                    // Сохраняем подкатегорию
                    list($result, $category_created) = $category_model->create_category($category_save_data);
                    if(!$result)
                    {
                        // Не смогли сохранить подкатегорию
                        return array(0,  JText::_('COM_ISHOP_CAN_NOT_SAVE_SUBCATEGORY'));
                    }
                    $category_created['key'] = $i;
                    $category_created['link'] = $a_item_titles[0]->href;

                    $html_2 = file_get_html($category_created['link']);
                    $b_catalog_boxes = $html_2->find('div.b-catalog-box');
                    foreach ($b_catalog_boxes as $box)
                    {
                        $catalog_items = $box->find('div.b-catalog-box_item');
                        if($catalog_items)
                        {
                            $category_created['items'] = array();
                            foreach ($catalog_items as $catalog_item)
                            {
                                $links = $catalog_item->find('a');
                                $images = $links[0]->find('img');
                                $category_created['items'][] = array(
                                    'link' =>  $this->_base_link.$links[0]->href,
                                    'img_small' =>  $this->_base_link.$images[0]->src,
                                );
                            }
                            $subcategories[] = $category_created;
                            break;
                        }
                    }

                }
            }

        }
            

        // Сохраняем данные
        array_shift($data['func']);
        if($subcategories)
        {
            array_unshift($data['func'], 'parse_subcategories');
            $data['subcategories'] = $subcategories;
        }
        $this->_set_data($data);
        
        $link = '';
        if(isset($data['categories'][0]))
        {
            $link = $data['categories'][0]['link'];
        }
        return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
        
    }


    public function parse_subcategories()
    {
        $data = $this->_get_data();
        if(!$data)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_PARSE_PAGE'));
        }
        
        // Если закончились подкатегории переходим на уровень категорий
        if(!$data['subcategories'])
        {
            array_shift($data['func']);
            $this->_set_data($data);
            $link = '';
            if(isset($data['categories'][0]))
            {
                $link = $data['categories'][0]['link'];
            }
            return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
        }
        
        $data['subcategory'] = array_shift($data['subcategories']);
        array_unshift($data['func'], 'parse_page');
        
        // Сохраняем данные
        array_unshift($data['func'],'parse_page');
        $data['page_items'] = $data['subcategory']['items'];
        $this->_set_data($data);

        $link = $data['page_items'][0]['link'];
        return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
        
    }

    /**
     * Парсинг страницы карточки изделия
     */
    public function parse_page()
    {
        $data = $this->_get_data();
        if(!$data)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_PARSE_PAGE'));
        }
        /**
         *  Если закончились карточки товаров
         *  переходим на верхний уровень
         */
        if(!$data['page_items'])
        {
            array_shift($data['func']);
            $this->_set_data($data);
            
            $link = 'End parse item page';
            return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
            
        }
        
        $page_item = array_shift($data['page_items']);
        
        $html = file_get_html($page_item['link']);
        
        // Находим  и записываем необходимые данные для карточки изделия
        
        // Рисунок
        $img_div = $html->find('div.b-catalog-item_main-img');
        $img_a = $img_div[0]->find('a.g-image-link');
        
        // Костыль, попали не туда
        if(!isset($img_a[0]))
        {
            $this->_set_data($data);
            return array(2,  JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
        }
        
        $medium_image = $img_a[0]->find('img');
        $desc = array(
            'img_medium'=>$this->_base_link.$medium_image[0]->src,
            'img_large'=> $this->_base_link.$img_a[0]->href,
            'img_small'=>$page_item['img_small'],
            'item_link'=>$page_item['link'],
        );
        
        // Цена
        $b_price_boxes = $html->find('div.b-price-box_value');
        $b_price_boxe_spans = $b_price_boxes[0]->find('span');
        $price = iconv("windows-1251", "utf-8", trim($b_price_boxe_spans[0]->innertext));
        $match = '/(.+)&nbsp;руб./';
        if(preg_match($match, $price, $regs))
        {
            $price = implode('',explode('&nbsp;', $regs[1]));
        }
        
        // Заголовок
        $b_navigations = $html->find('div.b-content');
        $titles = $b_navigations[0]->find('h1');
        
        // Описание
        $i_descriptions = $html->find('div#i-description');
        $ps = $i_descriptions[0]->find('p');
        $match = '/^.+span\>\s?(.+)\<br.+span\>\s?(.+)/';
        $artikul = $code = '';
        if(preg_match($match, $ps[0]->innertext, $regs))
        {
            $artikul = iconv("windows-1251", "utf-8", trim($regs[1]));
            $code = iconv("windows-1251", "utf-8", trim($regs[2]));
        }
        $name = iconv("windows-1251", "utf-8", trim($titles[0]->innertext));
        $data_item = array(
            'artikul'=>$artikul,
            'name'=>$name,
            'alias'=> JApplication::stringURLSafe($name),
            'desc'=>  json_encode($desc),
            'code'=>$code,
            'cena_mag'=>$price,
            'opisanije'=>$i_descriptions[0]->innertext,
            'category_id'=>$data['subcategory']['id'],
            'zavod_id'=>$this->_zavod_id
        );
        
        if(!$this->_add_items($data_item))
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_SAVE_PRODUCT'));
        }
        $msg = $this->_get_msg($data_item);
        
        $this->_set_data($data);
        
        $link = isset($data['page_items'][0]['link'])?$data['page_items'][0]['link']:'Переходим к следующей категории';
        return array(2,  $msg.'<hr>'.JText::_('COM_ISHOP_OPEN_PAGE').': '.$link); // Продолжаем парсинг
        
    }


    /**
     * Вывод сообщения с параметрами изделия
     * @param type $data_item
     * @return string
     */
    private function _get_msg($data_item)
    {
        $desc = json_decode($data_item['desc'],TRUE);
        $msg = '';
        if(isset($desc['img_small']) AND $desc['img_small'])
        {
            
            $msg .= '<img src="'.$desc['img_small'].'" height="100" style="float:left;">';
        }
        $color = 'green';
        $msg .= '
            <table style="color:'.$color.'">
                <tr>
                    <th>Наименование</th>
                    <td>'.$data_item['name'].'</td>
                </tr>
                <tr>
                    <th>Артикул</th>
                    <td>'.$data_item['artikul'].'</td>
                </tr>
                <tr>
                    <th>Код</th>
                    <td>'.$data_item['code'].'</td>
                </tr>
                <tr>
                    <th>Цена</th>
                    <td>'.$data_item['cena_mag'].'</td>
                </tr>
            </table>
            ';
        return $msg;
    }

    /**
     * Записываем изделие в базу
     * @param array данные об изделии
     * @return boolean
     */
    private function _add_items($data_item)
    {
        $result = TRUE;
        if($data_item['alias'])
        {
            $product_model = new IshopModelProduct;
             // Сохраняем продукт
            $product_id = $product_model->save_product($data_item);
             if(!$product_id)
             {
                 $result = FALSE;
             }
        }
        return $result;
    }
    
  
    /**
     * Сохраняем данные перед выходом
     * @param array $data 
     */
    private function _set_data($data)
    {


        JFactory::getApplication()->setUserState('com_ishop.parse', $data);
        if (!JFile::write($this->_file_data, json_encode($data)))
        {
            return FALSE;
        }
        return TRUE;
    }
    
   
    /**
     * Берем сохраненные данные
     * @param array $data 
     */
    private function _get_data()
    {
        $data = JFactory::getApplication()->getUserState('com_ishop.parse', array());
        if(JFile::exists($this->_file_data))
        {
            $data = json_decode(JFile::read($this->_file_data),TRUE);
        }
        return $data;
    }
    
    /**
     * Берем сохраненные данные категории
     * @param array $data 
     */
    private function _get_category_data()
    {
        $category_data = JFactory::getApplication()->getUserState('com_ishop.category_data', array());
        if(JFile::exists($this->_file_category))
        {
            $category_data = json_decode(JFile::read($this->_file_category),TRUE);
        }
        return $category_data;
    }
    
    /**
     * Конвертация символов
     * @param string $string
     * @return string 
     */
    private function _iconv($string)
    {
        try 
        {
            $string = iconv("windows-1251", "utf-8", trim($string));
        }
        catch (Exception $e) 
        {
            echo 'Поймано исключение: ', $e->getMessage(), "\n";
            exit;
        }
        
        return $string;
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * Список категорий
     * заполняется вручную
     * @return type 
     */
    private function _get_categories_array_()
    {
        
        $categories = array(
            array(
                'link'=>'http://www.kazanova.su/shop/analniie_stimulyatorii',
                'name'=>'Анальные Стимуляторы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/massajerii_prostatii',
                'name'=>'Массажеры простаты'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/badi',
                'name'=>'БАД'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/duhi_s_feromonami',
                'name'=>'Духи с Феромонами'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/prezervativii',
                'name'=>'Презервативы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/lyubrikantii',
                'name'=>'Смазки (Любриканты)'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/vibratorii',
                'name'=>'Вибраторы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/klitoralniie_stimulyatorii',
                'name'=>'Стимуляторы и вибраторы для клитора'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/kuklii',
                'name'=>'Куклы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/masturbatorii',
                'name'=>'Мужские мастурбаторы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/mebel_dlya_seksa__lav_mashinii__ka',
                'name'=>'Приспособления для секса'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/nasadki_i_koltsa',
                'name'=>'Насадки и кольца'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/naborii',
                'name'=>'Наборы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/pompii',
                'name'=>'Помпы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/straponii',
                'name'=>'Страпоны'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/uvelichenie_penisa',
                'name'=>'Увеличение пениса'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/faloimitatorii',
                'name'=>'Фалоимитаторы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/shariki_vaginalniie',
                'name'=>'Шарики вагинальные'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/eroticheskoe-belje',
                'name'=>'Эротическое бельё'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/bdsm',
                'name'=>'Товары для BDSM'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/literatura',
                'name'=>'Книги и журналы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/suvenirii',
                'name'=>'Секс сувениры'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/batareiki',
                'name'=>'Батарейки'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/aksessuari',
                'name'=>'Аксессуары'
            )
        );
        
        return $categories;
    }
    
    /**
     * Список категорий
     * заполняется вручную
     * @return type 
     */
    private function _get_categories_array__()
    {
        $categories = array(
            array(
                'link'=>'http://www.kazanova.su/shop/idei-dlya-podarkov',
                'name'=>'Идеи для подарков'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/zeleniy-kofe',
                'name'=>'Зеленый кофе'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/kuklii',
                'name'=>'Куклы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/masturbatorii',
                'name'=>'Мужские мастурбаторы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/mebel_dlya_seksa__lav_mashinii__ka',
                'name'=>'Приспособления для секса'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/nasadki_i_koltsa',
                'name'=>'Эротические насадки и кольца'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/naborii',
                'name'=>'Наборы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/pompii',
                'name'=>'Помпы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/straponii',
                'name'=>'Страпоны'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/faloimitatorii',
                'name'=>'Фалоимитаторы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/shariki_vaginalniie',
                'name'=>'Шарики вагинальные'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/eroticheskoe-belje',
                'name'=>'Эротическое бельё'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/bdsm',
                'name'=>'Товары для BDSM'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/literatura',
                'name'=>'Книги и журналы'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/suvenirii',
                'name'=>'Секс сувениры'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/batareiki',
                'name'=>'Батарейки'
            ),
            array(
                'link'=>'http://www.kazanova.su/shop/aksessuari',
                'name'=>'Аксессуары'
            )
        );
        
        return $categories;
    }
    
}
