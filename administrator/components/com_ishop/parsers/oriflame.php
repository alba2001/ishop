<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */


// No direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/parser.php'; 

/**
 * Oriflame
 */
class IshopParserOriflame extends IshopParser
{


    public function __construct() {
        $this->site_alias = 'oriflame';
        $this->base_link = 'http://ru.oriflame.com';
        $this->base_category_link = 'http://ru.oriflame.com/';
        parent::__construct();
    }
    
    protected function create_categories()
    {
        $data = $this->get_data();
        $data['categories'] = array();
        $data['proc'] = 'create_top_categories';
        $this->set_data($data);
        return array(2,  JText::_('COM_ISHOP_CREATE_TOP_CATEGORIES'));
    }
    
     protected function create_top_categories()
    {
        $root_category = $this->get_parent_category_id('1');
        if(!$root_category)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_FIND_ROOT_CATEGORY'));
        }
        $html = file_get_html($this->base_category_link);
        $li_categories = $html->find('li.top-menu-item');
        $categories = array();
        foreach($li_categories as $li_category)
        {
            $a = $li_category->find('a');
            $data_prod_cat = 'data-prod-cat';
            $data_brand_cat = 'data-brand-cat';
            $parce_data = array(
                'data-prod-cat'=>$li_category->$data_prod_cat,
                'data-brand-cat'=>$li_category->$data_brand_cat,
            );
            $category_name = trim($a[0]->innertext);
            $alias = $this->get_alias($category_name);
            $path = $root_category->path.'/'.$alias;
            $category = array(
                'name' => $category_name,
                'alias' => $alias,
                'category_sourse_path' => $this->base_link.$a[0]->href,
                'parent_id' => $root_category->id,
                'path' => $path,
                'parce_data' =>  json_encode($parce_data),
                'level' =>  '2',
                'zavod' => $this->site_id,
                'parser_id' => $this->site_id,
            );
            
            $category['id'] = $this->create_category($category);
            if(!$category['id'])
            {
                // Записываем категорию
                return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGORY'));
            }
            $categories[] = $category;
        }

        $data = $this->get_data();
        $data['categories'] = $categories;
        $data['proc'] = 'create_sub_categories';
        $this->set_data($data);
        return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES'));
        
    }
   
    /**
     * Создаем подкатегории
     * @return array 
     */
    protected function create_sub_categories()
    {
        $data = $this->get_data();
        
        // Если закончились категории, выходим из парсинга
        if(!$data['categories'])
        {
            return array(1,JText::_('COM_ISHOP_PARSE_SUCCES'));
        }
        
        $category = array_shift($data['categories']);
        
        $html = file_get_html($category['category_sourse_path']);
        $ul = $html->find('ul#menuLv3Brands');
        $lis = $ul[0]->find('li');
        $sub_categories = array();
        foreach ($lis as $li)
        {
            $li_a = $li->find('a.mnLv3ExpandingItm');
            if(isset($li_a[0]))
            {
                
                $category_name = trim($li_a[0]->innertext);
                $alias = $this->get_alias($category_name);
                $path = $category['path'].'/'.$alias;
                $sub_category = array(
                    'name' => $category_name,
                    'alias' => $alias,
                    'category_sourse_path' => '',
                    'parent_id' => $category['id'],
                    'path' => $path,
                    'parce_data' =>  '',
                    'level' =>  '3',
                    'site_alias' => $this->site_alias,
                );

                $sub_category['id'] = $this->create_category($sub_category);
                if(!$sub_category['id'])
                {
                    // Записываем категорию
                    return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGORY'));
                }

                // Ищем подкатегории
                $sub_sub_categories = array();
                $ul3 = $li->find('ul.mnLv3SecondLevel');
                if(isset($ul3[0]))
                {
                    $li3s = $ul3[0]->find('li');
                    foreach($li3s as $li3)
                    {
                        $li3_a = $li3->find('a');
                        if(isset($li3_a[0]))
                        {
                            $category_name = trim($li3_a[0]->innertext);
                            $alias = $this->get_alias($category_name);
                            $path = $sub_category['path'].'/'.$alias;
                            $sub_sub_category = array(
                                'name' => $category_name,
                                'alias' => $alias,
                                'category_sourse_path' => $this->base_link.$li3_a[0]->href,
                                'parent_id' => $sub_category['id'],
                                'path' => $path,
                                'parce_data' =>  '',
                                'level' =>  '4',
                                'site_alias' => $this->site_alias,
                            );
                            $sub_sub_category['id'] = $this->create_category($sub_sub_category);
                            if(!$sub_category['id'])
                            {
                                // Записываем категорию
                                return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGORY'));
                            }
                            $sub_sub_categories[] = $sub_sub_category;
                        }
                    }
                }
                $sub_category['sub_sub_categories'] = $sub_sub_categories;
                $sub_categories[] = $sub_category;
            }
        }
        
        $msg = JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES');
        if(isset($sub_categories[0]))
        {
            $data['sub_categories'] = $sub_categories;
            $data['proc'] = 'parce_sub_categories';
            $msg = JText::_('COM_ISHOP_PARSE_SUB').': '.$sub_categories[0]['name'];
        }
        $this->set_data($data);
        
        return array(2,  $msg);
    }
    
    
    protected function parce_sub_categories()
    {
        $data = $this->get_data();
        // Если закончились подкатегории, выходим за новыпи подкатегориями
        if(!$data['sub_categories'])
        {
            $data['proc'] = 'create_sub_categories';
            $this->set_data($data);
            return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES'));
        }
        
        $sub_category = $data['sub_category'] = array_shift($data['sub_categories']);
        
        $msg = JText::_('COM_ISHOP_PARSE_SUB_CATEGORIES');
        if(isset($sub_category['sub_sub_categories'][0]))
        {
            $data['proc'] = 'parce_sub_sub_categories';
            $msg = JText::_('COM_ISHOP_PARSE').': '.$sub_category['sub_sub_categories'][0]['name'];
        }
        $this->set_data($data);
        
        return array(2,  $msg);
    }
    
    protected function parce_sub_sub_categories()
    {
        $data = $this->get_data();
        
        // Если закончились под подкатегории, выходим на следующую подкатегорию
        $sub_category = & $data['sub_category'];
        if(!$sub_category['sub_sub_categories'])
        {
            $data['proc'] = 'parce_sub_categories';
            $this->set_data($data);
            $msg = JText::_('COM_ISHOP_END_PARSE_SUB_CATEGORIES');
            if(isset($data['sub_categories'][0]))
            {
                $msg = JText::_('COM_ISHOP_PARSE').$data['sub_categories'][0]['name'];
            }
            return array(2,  $msg);
        }
        $sub_sub_category = array_shift($sub_category['sub_sub_categories']);
        $data['sub_sub_category'] = $sub_sub_category;
        
        $msg = JText::_('COM_ISHOP_END_PARSE').': '.$sub_category['name'];
        if(isset($sub_category['sub_sub_categories'][0]))
        {
            $msg = JText::_('COM_ISHOP_PARSE').': '.$sub_category['sub_sub_categories'][0]['name'];
            $data['proc'] = 'parce_items';
        }
        $this->set_data($data);
        
        return array(2,  $msg);
    }
    
    
    protected function parce_items()
    {
        $data = $this->get_data();
        
        $sub_sub_category =& $data['sub_sub_category'];
        if(!is_array($sub_sub_category['category_sourse_path']))
        {
            $sourse_path = $sub_sub_category['category_sourse_path'];
            $sub_sub_category['category_sourse_path'] = array();
            $html = file_get_html($sourse_path);
            // Обрабатываем пагинацию
            $navigation_spans = $html->find('span.page-navigation-number');
            if(count($navigation_spans))
            {
                foreach ($navigation_spans as $navigation_span)
                {
                    $navigation_a = $navigation_span->find('a');
                    if(isset($navigation_a[0]->href))
                    {
                        $sub_sub_category['category_sourse_path'][] = $this->base_link.$navigation_a[0]->href;
                    }
                }
            }
        }
        elseif(!$sub_sub_category['category_sourse_path'])
        {
            $data['proc'] = 'parce_sub_sub_categories';
            $this->set_data($data);
            return array(2,  'На следующую подкатегорию');
        }
        else
        {
            $sourse_path = array_shift($sub_sub_category['category_sourse_path']);
            $html = file_get_html($sourse_path);
        }
        
            $div_product_list = $html->find('div.product-list-subwrapper');
            $ul_product_list = $div_product_list[0]->find('ul.product-list');
            
            $li_product_list = array();
            if(isset($ul_product_list[0]))
            {
                $li_product_list = $ul_product_list[0]->find('li.product-item');
            }
            
            $product_list = array();
            foreach($li_product_list as $li_product)
            {
                // Код продукта
                $attr = 'data-prod-code';
                $code =  $li_product->$attr;

                // Ссылка на карточку товара
                $li_a = $li_product->find('a.name');
                if(!isset($li_a[0]))
                {
                    continue;
                }
                $href = $this->base_link.$li_a[0]->href;

                // Ссылка на рисунок превьюшки
                $li_img = $li_product->find('img.product-image');
                $src = isset($li_img[0]->src)?$this->base_link.$li_img[0]->src:'';

                // Краткое описание
                $dopinfo = $li_product->find('div.name');
                $dopinfo = isset($dopinfo[0]->innertext)?trim($dopinfo[0]->innertext):'';

                // Наименование бренда
                $div_brand = $li_product->find('div.category-name');
                $brand_name = isset($div_brand[0]->innertext)?trim($div_brand[0]->innertext):'';

                // Стоимость
                $div_price = $li_product->find('div.price');
                $span_price = $div_price[0]->find('span.notranslate');
                $price = isset($span_price[0]->innertext)?trim(str_replace('p.','',trim($span_price[0]->innertext))):'';

                // Стоимость без скидки
                $div_discount = $li_product->find('div.discount-price');
                $discont = isset($div_discount[0]->innertext)?trim(str_replace('p.','',trim($div_discount[0]->innertext))):'';

                $dopinfo = array(
                    'brand_name' => $brand_name,
                    'img_small' => $src,
                    'price' => $price,
                    'discont' => $discont,
                );

                $item_data = array(
                    'code' => $code,
                    'href' => $href,
                    'dopinfo' => $dopinfo,
                    'cena_mag' => $discont,
                    'cena_tut' => $price,
                    'dopinfo' => json_encode($dopinfo),
                );
                $product_list[] = $item_data;
            }
            if($product_list)
            {
                $data['product_list'] = $product_list;
                $data['proc'] = 'parce_item';
                $msg = JText::_('COM_ISHOP_PARSE').': '.$product_list[0]['dopinfo'];
            }
            else
            {
                $data['proc'] = 'parce_sub_sub_categories';
                $msg = '';
            }
            
        
        $this->set_data($data);
        
        return array(2,  $msg);
    }
    
    protected function parce_item()
    {
        $data = $this->get_data();
        
        if(!$data['product_list'])
        {
            $data['proc'] = 'parce_items';
            $this->set_data($data);
            return array(2,  'На следующую страницу');
        }
        
        $product = array_shift($data['product_list']);
        $dopinfo = json_decode($product['dopinfo'],TRUE);
        
        $html = file_get_html($product['href']);
        $main_div = $html->find('div.main-product-info');
        
        // Основной рисунок
        $div_left = $main_div[0]->find('div.left-box');
        $img = $div_left[0]->find('img.main-product-image');
        $img_src = $this->base_link.$img[0]->src;
        
        $div_right = $main_div[0]->find('div.right-box');
        
        // Наименование
        $name_div = $div_right[0]->find('div.local-name');
        $product_name = $name_div[0]->innertext;
        
        // Цена
        if(!$product['cena_tut'])
        {
            $price_div = $div_right[0]->find('div.price');
            if(isset($price_div[0]))
            {
                $price_span = $price_div[0]->find('span');
                $product['cena_tut'] = isset($price_span[0]->innertext)?
                    trim(str_replace('p.','',trim($price_span[0]->innertext))):
                    '';
            }
        }
        
        $info_row_divs = $div_right[0]->find('div.product-info-row');
        foreach($info_row_divs as $info_row_div)
        {
            // Размер
            $size_div = $info_row_div->find('div.size');
            if(isset($size_div[0]))
            {
                $size_span = $size_div[0]->find('span.value');
                $size = preg_replace('/\s+/', ' ',$size_span[0]->innertext);
            }
            
            // Код
            $code_div = $info_row_div->find('div.code');
            if(isset($code_div[0]))
            {
                $code_span = $code_div[0]->find('span.value');
                $code = $code_span[0]->innertext;
            }
        }
        
        // Описание
        $div_accordion = $div_right[0]->find('div.accordion');
        $accordion_h3s = $div_accordion[0]->find('h3');
        $content_divs = array();
        $accordion_divs = $div_accordion[0]->find('div');
        foreach ($accordion_divs as $div)
        {
            if($div->class == 'content')
            {
                $content_divs[] = $div;
            }
        }
        $count = count($accordion_h3s)-1;
        $dopinfo = array();
        for($i=0; $i < $count; $i++)
        {
            $header = $accordion_h3s[$i]->innertext;
            $dopinfo[] = array(
                'header' => $header,
                'content' => $content_divs[$i]->innertext,
            );
        }
        $desc = $content_divs[0]->innertext;
        
        $dopinfo['img_medium'] = $img_src;
        $dopinfo['img_large'] = $img_src;
        $dopinfo['size'] = $size;
        $dopinfo['code'] = $code;
        $dopinfo['dopinfo'] = $dopinfo;
        
        $product['dopinfo'] = json_encode($dopinfo);
        $product['name'] = $product_name;
        $product['desc'] = $desc;
        $product['alias'] = JApplication::stringURLSafe($product_name);
        $product['category_id'] = $data['sub_sub_category']['id'];
        $product['site_alias'] = $this->site_alias;

        if(!$this->add_item($product))
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_SAVE_PRODUCT'));
        }
        $msg = $this->get_msg($product);
        $this->set_data($data);
        
        return array(2,  $msg.'<hr>');
    }
    
}
