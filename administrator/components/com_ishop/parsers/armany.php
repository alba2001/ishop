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
 * Armany
 */
class IshopParserArmany extends IshopParser
{

    private $_ar_top_cat_names = array();
    
    public function __construct() {
        $this->site_alias = 'armany';
        $this->base_link = 'http://www.amway.ru';
        $this->base_category_link = 'http://www.amway.ru/';
        $this->_ar_top_cat_names = array(
            'Правильное питание',
            'Красота и уход за телом',
            'Товары для дома',
        );
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
        $root_category = $this->get_parent_category_id();
        if(!$root_category)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_FIND_ROOT_CATEGORY'));
        }
        $html = file_get_html($this->base_category_link);
        $li_categories = $html->find('li.menu_l1');
        $categories = array();
        foreach($li_categories as $li_category)
        {
            $a = $li_category->find('a.menu_l1');
            $span_0 = $a[0]->find('span.nodename_wrapper');
            $span = $span_0[0]->find('span');
            
            $category_name = $span[0]->innertext;
            if(!in_array($category_name, $this->_ar_top_cat_names))
            {
                continue;
            }
            $alias = $this->get_alias($category_name);
            $path = $root_category->path.'/'.$alias;
            $category = array(
                'name' => $category_name,
                'alias' => $alias,
                'category_sourse_path' => $this->base_link.$a[0]->href,
                'parent_id' => $root_category->id,
                'path' => $path,
                'level' =>  '2',
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
        $ul = $html->find('ul.menu_ul');
        $lis = $ul[0]->find('li');
        $sub_categories = array();
        foreach ($lis as $li)
        {
            $li_a = $li->find('a');
            if(isset($li_a[0]))
            {
                
                $category_name = trim($li_a[0]->innertext);
                $alias = $this->get_alias($category_name);
                $path = $category['path'].'/'.$alias;
                $sub_category = array(
                    'name' => $category_name,
                    'alias' => $alias,
                    'category_sourse_path' => $this->base_link.$li_a[0]->href,
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
        
        $msg = JText::_('COM_ISHOP_PARSE').': '.$sub_category['name'];
        $data['proc'] = 'parce_items';
        $data['product_list'] = array();
            
        $this->set_data($data);
        
        return array(2,  $msg);
    }
    
    
    protected function parce_items()
    {
        $data = $this->get_data();
        
        $sub_category =& $data['sub_category'];
        
        if(!$sub_category['category_sourse_path'])
        {
            $data['proc'] = 'parce_sub_categories';
            $msg = 'На следующую подкатегорию';
            $this->set_data($data);
        
            return array(2,  $msg);
        }
        
        // Ищем пагинацию
        if(!is_array($sub_category['category_sourse_path']))
        {
            $sourse_path = $sub_category['category_sourse_path'];
            $html = file_get_html($sourse_path);
            $sub_category['category_sourse_path'] = array();
            $table_pager_pages = $html->find('div.table_pager_pages');
            if(isset($table_pager_pages[0]))
            {
                $a_table_pager_number_link = $table_pager_pages[0]->find('a.table_pager_number_link');
                if(isset($a_table_pager_number_link[0]))
                {
                    foreach ($a_table_pager_number_link as $a_pagelink)
                    {
                        $sub_category['category_sourse_path'][] = $sourse_path.$a_pagelink->href;
                    }
                }
            }
        }
        else
        {
            $sourse_path = array_shift(str_replace('amp;', '', $sub_category['category_sourse_path']));
            $html = file_get_html($sourse_path);
        }
        
        if(!is_object($html))
        {
            echo 'HTML: ';
            var_dump($sourse_path);
            exit;
        }
        $table_linear_view = $html->find('table.linear_view');
        $tr_product_list = $table_linear_view[0]->find('tr');
            
        $product_list = $data['product_list'];
        foreach($tr_product_list as $tr_product)
        {
                // Ссылка на карточку товара
            $div_name = $tr_product->find('div.product_name');
            if(!isset($div_name[0]))
            {
                continue;
            }
            $_a = $div_name[0]->find('a');
            if(!isset($_a[0]))
            {
                continue;
            }
            $href = $this->base_link.$_a[0]->href;
            $product_info_cell = $tr_product->find('td.product_info_cell');
            $product_info_box = $product_info_cell[0]->find('div.product_info_box');
            
            // Краткое описание
            $short_desc_div = $product_info_box[0]->find('div.short_description');
            $short_desc = isset($short_desc_div[0])?$short_desc_div[0]->innertext:'';
            
            // Размеры
            $product_size_div = $product_info_box[0]->find('div.product_size');
            $product_size = isset($product_size_div[0])?$product_size_div[0]->innertext:'';
            
            // Оригинальный артикул
            $product_sku_div = $product_info_box[0]->find('div.product_sku');
            $origin_article = isset($product_sku_div[0])?$product_sku_div[0]->innertext:'';
            if($origin_article)
            {
                preg_match('/(\d+)/', $origin_article, $matches);
                if(isset($matches[1]))
                {
                    $origin_article = $matches[1];
                }
            }

            $dopinfo = array(
                'size' => $product_size,
                'short_desc' => $short_desc,
                'origin_article' => $origin_article,
            );

                $item_data = array(
                    'code' => $origin_article,
                    'href' => $href,
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
            $data['proc'] = 'parce_sub_categories';
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
        $product_details_tab = $html->find('table.product_details_tab');
        $tr = $product_details_tab[0]->find('tr.row_1');
        
        // Основной рисунок
        $product_details_thumbnail = $tr[0]->find('td.product_details_thumbnail');
        $a_img = $product_details_thumbnail[0]->find('a#product_details_zoom_image');
        $img_src = $this->base_link.$a_img[0]->href;
        
        $product_details_content = $tr[0]->find('td.product_details_content');
        
        // Наименование
        $name_h1 = $product_details_content[0]->find('h1');
        $product_name = $name_h1[0]->innertext;
        
        // Цена
        $product['cena_tut'] = ''; // Нет цены на этом сайте
            
        
        // Описание
        $product_description_tabs = $product_details_content[0]->find('div.product_description_tabs');
        $desc = $product_description_tabs[0]->innertext;
        
        $dopinfo['img_medium'] = $img_src;
        $dopinfo['img_large'] = $img_src;
        
        $product['dopinfo'] = json_encode($dopinfo);
        $product['name'] = $product_name;
        $product['desc'] = $desc;
        $product['alias'] = JApplication::stringURLSafe($product_name);
        $product['category_id'] = $data['sub_category']['id'];
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
