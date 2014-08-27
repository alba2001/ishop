<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

/**
Уход за лицом, Уход за телом, Макияж, Парфюмерия, Для мужчин
 */

// No direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/parser.php'; 

/**
 * Marykay
 */
class IshopParserMarykay extends IshopParser
{


    public function __construct() {
        $this->site_alias = 'marykay';
        $this->base_link = 'http://www.marykay.ru';
        $this->base_category_link = 'http://www.marykay.ru/ru-RU/_layouts/MaryKayCoreCatalog/ProductsAndShop.aspx?dsNav=N:2000040';
        parent::__construct();
    }
    
    protected function get_top_categories_array()
    {
        $data = preg_replace('/\s+/', '', $this->get_category_start_data());
        $category_names = explode(',', $data);
        
        $html = file_get_html($this->base_category_link);
        $h2_titles = $html->find('h2.s-pn-title');
        $categories = array();
        foreach ($h2_titles as $h2_title)
        {
            $a = $h2_title->find('a');
            $span = $a[0]->find('span');
            if(in_array(preg_replace('/\s+/', '', $span[0]->innertext), $category_names))
            {
                $categories[] = array(
                    'name' => trim($span[0]->innertext),
                    'category_sourse_path' => $this->base_link.$a[0]->href
                );
            }
        }
        
        return $categories;
    }
    
    protected function create_categories()
    {
        $data = $this->get_data();
        
        $data['categories'] = $this->get_top_categories_array();
        $data['proc'] = 'create_top_categories';
        $this->set_data($data);
        return array(2,  JText::_('COM_ISHOP_CREATE_TOP_CATEGORIES'));
    }
    
    protected function create_top_categories_items()
    {
        $data = $this->get_data();
        
        if(!isset($data['top_categories_items_created']))
        {
            $data['top_categories_items_created'] = TRUE;
            $data['top_categories'] = $data['categories'];
        }
        
        // Если закончились топовые категории, выходим из парсинга
        if(!$data['top_categories'])
        {
            $data['proc'] = 'create_sub_categories';
            $this->set_data($data);
            return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES'));
        }
        
        $category = array_shift($data['top_categories']);
        $data['sub_sub_category'] = $category;
        
        $msg = JText::_('COM_ISHOP_PARSE').': '.$category['name'];
        $data['proc'] = 'parce_items';
        $data['ret_proc'] = 'create_top_categories_items';

        $this->set_data($data);
        
        return array(2,  $msg);
        
    }

    private function _get_sub_sub_categories($column_div,$category)
    {
        $lis = $column_div->find('li');
        $categories = array();
        foreach($lis as $li)
        {
            $a = $li->find('a');
            $name = $a[0]->innertext;
            $category_sourse_path = $a[0]->href;
            $alias = $this->get_alias($name);
            $path = $category['path'].'/'.$alias;
            $sub_cat = array(
                'name'=>$name,
                'parent_id'=>$category['id'],
                'path'=>$path,
                'level' => '3',
                'alias'=>$alias,
                'site_alias' => $this->site_alias,
                'category_sourse_path' => $this->base_link.$category_sourse_path,
            );
            $sub_cat['id'] = $this->create_category($sub_cat);
            $categories[] = $sub_cat;
        }
        return $categories;
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
        
        /**
         * УБРАТЬ!!! ВРЕМЕННО!!!
         */
//        $data['top_categories_items_created'] = TRUE;
        /**
         * УБРАТЬ!!! ВРЕМЕННО!!!
         */
        
        // Парсинг категорий верхнего уровня
        if(!isset($data['top_categories_items_created']))
        {
            $data['proc'] = 'create_top_categories_items';
            $this->set_data($data);
            return array(2,  JText::_('COM_ISHOP_CREATE_TOP_CATEGORIES_ITEMS'));
        }
        
        $category = array_shift($data['categories']);
        
        $html = file_get_html($category['category_sourse_path']);
        $div_container = $html->find('div.s-pn-container');
        $div_active = $div_container[0]->find('div.is-active');
        $div_dropdown = $div_active[0]->find('div.s-pn-dropdown');
        $column_divs = $div_dropdown[0]->find('div.s-pn-column');
        $sub_categories = array();
        foreach ($column_divs as $column_div)
        {
            $h3_name = $column_div->find('h3');
            $name = $h3_name[0]->innertext;
            $a_source = $column_div->find('a.s-pn-more');
            $category_sourse_path = $a_source[0]->href;
            $alias = $this->get_alias($name);
            $path = $category['path'].'/'.$alias;
            $sub_cat = array(
                'name'=>$name,
                'parent_id'=>$category['id'],
                'path'=>$path,
                'level' => '3',
                'alias'=>$alias,
                'site_alias' => $this->site_alias,
                'category_sourse_path' => $this->base_link.$category_sourse_path,
            );
            $sub_cat['id'] = $this->create_category($sub_cat);
            $sub_cat['sub_sub_categories'] = $this->_get_sub_sub_categories($column_div,$sub_cat);
            $sub_categories[] = $sub_cat;
            if(!$sub_cat['id'])
            {
                // Ошибка записи категории категорию
                return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_SUB_CATEGORY'));
            }
        }
        
        $msg = JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES');
        if(isset($sub_categories[0]))
        {
            $data['to_parce_sub_categories'] = $sub_categories;
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
        // Если закончились подкатегории, выходим за новыми подкатегориями
        if(!$data['sub_categories'])
        {
            $data['proc'] = 'create_sub_categories';
            $this->set_data($data);
            return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES'));
        }
        
        /**
         * УБРАТЬ!!! ВРЕМЕННО!!!
         */
//        $data['sub_categories_items_created'] = TRUE;
        /**
         * УБРАТЬ!!! ВРЕМЕННО!!!
         */
        
        // Парсинг подкатегорий верхнего уровня
        if(!isset($data['sub_categories_items_created']))
        {
            $data['proc'] = 'create_sub_categories_items';
            $this->set_data($data);
            return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES_ITEMS'));
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

    protected function create_sub_categories_items()
    {
        $data = $this->get_data();
        
        if(!isset($data['sub_categories_items_created']))
        {
            $data['sub_categories_items_created'] = TRUE;
        }
        
        // Если закончились под категории, выходим из парсинга
        if(!$data['to_parce_sub_categories'])
        {
            $data['proc'] = 'create_sub_categories';
            $this->set_data($data);
            return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES'));
        }
        
        $category = array_shift($data['to_parce_sub_categories']);
        $data['sub_sub_category'] = $category;
        
        $msg = JText::_('COM_ISHOP_PARSE').': '.$category['name'];
        $data['proc'] = 'parce_items';
        $data['ret_proc'] = 'create_sub_categories_items';

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
            $navigation_div = $html->find('div.pages');
            if(isset($navigation_div[0]))
            {
                $navigation_as = $navigation_div[0]->find('a.page');
                if(count($navigation_as))
                {
                    foreach ($navigation_as as $navigation_a)
                    {
                        if(isset($navigation_a->href))
                        {
                            $sub_sub_category['category_sourse_path'][] = $this->base_link.$navigation_a->href;
                        }
                    }
                }
            }
        }
        elseif(!$sub_sub_category['category_sourse_path'])
        {
            $data['proc'] = 'parce_sub_sub_categories';
            if(isset($data['ret_proc']))
            {
                $data['proc'] = $data['ret_proc'];
                unset($data['ret_proc']);
            }
            $this->set_data($data);
            return array(2,  'На следующую подкатегорию');
        }
        else
        {
            $sourse_path = array_shift($sub_sub_category['category_sourse_path']);
            $html = file_get_html($sourse_path);
        }
        
            $div_product_list = $html->find('div.m-endeca-records-list');
            $ul_product_list = $div_product_list[0]->find('div.m-product-list-item');
            
            $product_list = array();
            foreach($ul_product_list as $li_product)
            {

                // Ссылка на карточку товара
                $h2_li_product = $li_product->find('h2.s-product-title');
                $li_a = $h2_li_product[0]->find('a');
                if(!isset($li_a[0]))
                {
                    continue;
                }
                $href = $this->base_link.$li_a[0]->href;

                $product_list[] = $href;
            }
            if($product_list)
            {
                $data['product_list'] = $product_list;
                $data['proc'] = 'parce_item';
                $msg = JText::_('COM_ISHOP_PARSE').': '.$product_list[0];
            }
            else
            {
                if(isset($data['ret_proc']))
                {
                    $data['proc'] = $data['ret_proc'];
                    unset($data['ret_proc']);
                }
                else
                {
                    $data['proc'] = 'parce_sub_sub_categories';
                }
                $msg = 'Продлжаем с '.$data['proc'];
            }
            
        
        $this->set_data($data);
        
        return array(2,  $msg);
    }
    
    /**
     * Парсинг карточки товара
     */
    protected function parce_item()
    {
        $data = $this->get_data();
        
        if(!$data['product_list'])
        {
            $data['proc'] = 'parce_items';
            $this->set_data($data);
            return array(2,  'На следующую страницу');
        }
        
        $href = array_shift($data['product_list']);
        $category_id = $data['sub_sub_category']['id'];
        
        $product = $this->_parce_item($href, $category_id);
        
        if(!$this->add_item($product))
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_SAVE_PRODUCT'));
        }
        $msg = $this->get_msg($product);
        $this->set_data($data);
        
        return array(2,  $msg.'<hr>');
    }
    
    private function _parce_item($href, $category_id)
    {
        $html = file_get_html($href);
        
        // Основной рисунок
        $div_img = $html->find('div.m-detail-prd-img');
        $a_img = $div_img[0]->find('a');
        $img_src = $this->base_link.$a_img[0]->href;
        
        
        // Наименование
        $div_name = $html->find('div.m-detail-prd-hd');
        $h1_name = $div_name[0]->find('h1');
        $product_name = $h1_name[0]->innertext;
        
        // Если есть подзаголовок, то берем и его
        $div_sub_name = $div_name[0]->find('p.s-ref');
        if(isset($div_sub_name[0]))
        {
            $sub_name = $div_sub_name[0]->innertext;
        }
        
        // Код продукта (здесь отсутствует, придумываем свой)
        $code = md5($product_name);
            
//        var_dump($href);
        // Стоимость
        $span_price = $html->find('span.s-price-container');
        $price_html = $span_price[0]->innertext;
        preg_match('/<\/span>(.+)<span class="fn-offscreen">/', $price_html, $regs);
        $price = preg_replace('/[^0-9]+/', '',$regs[1]);
        
        // Описание
        $div_desc = $html->find('div.s-prd-desc');
        $desc = isset($div_desc[0])?$div_desc[0]->innertext:'';
        
        $dopinfo = array(
            'sub_name' => $sub_name,
            'img_large' => $img_src,
        );
        $product = array(
            'name' => $product_name,
            'code' => $code,
            'cena_tut' => $price,
            'cena_mag' => $price,
            'desc' => $desc,
            'alias' => JApplication::stringURLSafe($product_name),
            'category_id' => $category_id,
            'site_alias' => $this->site_alias,
            'dopinfo' => json_encode($dopinfo),
        );
        
        return $product;
    }
}
