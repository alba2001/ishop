<?php
/**
 * @version     1.0.0
 * @package     com_ishop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Konstantin Ovcharenko <alba2001@meta.ua> - http://vini-cloud.ru
 */

/**
БЕЛЬЕ = http://www.florange.ru/production/catalog/,
ЮВЕЛИРНАЯ БИЖУТЕРИЯ = http://www.florange.ru/production/catalog2/,
ОДЕЖДА ДЛЯ СНА И ОТДЫХА = http://www.florange.ru/production/sleep/,
МАТЕРИАЛЫ ДЛЯ БИЗНЕСА = http://www.florange.ru/production/business/,
 */

// No direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/parser.php'; 

/**
 * Florange
 */
class IshopParserFlorange extends IshopParser
{


    public function __construct() {
        $this->site_alias = 'florange';
        $this->base_link = 'http://www.florange.ru/';
        $this->base_category_link = 'http://www.florange.ru/production/catalog/';
        parent::__construct();
    }
    
    protected function create_categories()
    {
        $data = $this->get_data();
        
        $data['categories'] = $this->get_top_categories_array();
        $data['proc'] = 'create_top_categories';
        $this->set_data($data);
        return array(2,  JText::_('COM_ISHOP_CREATE_TOP_CATEGORIES'));
    }
    
    protected function create_top_categories()
    {
        $data = $this->get_data();
        
        $root_category = $this->get_parent_category_id('1');
        if(!$root_category)
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_FIND_ROOT_CATEGORY'));
        }
        $categories = array();
        foreach($data['categories'] as $category)
        {
            $alias = $this->get_alias($category['name']);
            $path = $root_category->path.'/'.$alias;
            $_category = array(
                'alias' => $alias,
                'parent_id' => $root_category->id,
                'path' => $path,
                'level' =>  '2',
                'site_alias' => $this->site_alias,
            );
            $category += $_category;
            $category['id'] = $this->create_category($category);
            if(!$category['id'])
            {
                // Записываем категорию
                return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGORY'));
            }
            $categories[] = $category;
        }

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
        
        $html = file_get_html(trim($category['category_sourse_path']));
        $ul = $html->find('ul.nav');
        $top_lis = $ul[0]->find('li');
        foreach ($top_lis as $top_li)
        {
            $current_ul = $top_li->find('ul');
            if(isset($current_ul[0]))
            {
                break;
            }
        }
        
        $lis = $current_ul[0]->find('li');
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
                    // Ошибка записи категории
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
        
        $html = file_get_html($sub_category['category_sourse_path']);
        $div_leftside = $html->find('div.leftside');
        $ul_nav = $div_leftside[0]->find('ul.nav');
        $ul_cat = $ul_nav[0]->find('ul.cat');
        $lis = $ul_cat[0]->find('li');
        $sub_sub_categories = array();
        foreach($lis as $li)
        {
            $a_current = $li->find('a.current');
            if(isset($a_current[0]))
            {
                $inner_lis = $li->find('li');
                foreach ($inner_lis as $inner_li)
                {
                    $li_a = $inner_li->find('a');
                    $category_name = trim($li_a[0]->innertext);
                    $alias = $this->get_alias($category_name);
                    $path = $sub_category['path'].'/'.$alias;
                    $sub_sub_category = array(
                        'name' => $category_name,
                        'alias' => $alias,
                        'category_sourse_path' => $this->base_link.$li_a[0]->href,
                        'parent_id' => $sub_category['id'],
                        'path' => $path,
                        'parce_data' =>  '',
                        'level' =>  '4',
                        'site_alias' => $this->site_alias,
                    );
                    $sub_sub_category['id'] = $this->create_category($sub_sub_category);
                    if(!$sub_sub_category['id'])
                    {
                        // Ошибка записи категории
                        return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGORY'));
                    }
                    $sub_sub_categories[] = $sub_sub_category;
                }


                break;
            }
        }
        
        $data['sub_category']['sub_sub_categories'] = $sub_sub_categories;
        $msg = JText::_('COM_ISHOP_PARSE_SUB_CATEGORIES');
        if(isset($sub_sub_categories[0]))
        {
            $data['proc'] = 'parce_sub_sub_categories';
            $msg = JText::_('COM_ISHOP_PARSE').': '.$sub_sub_categories[0]['name'];
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
        
        $product = $this->_parce_item($sub_sub_category);
        if(!$this->add_item($product))
        {
            return array(0,  JText::_('COM_ISHOP_CAN_NOT_SAVE_PRODUCT'));
        }
        $msg = $this->get_msg($product);
        
        $data['proc'] = 'parce_sub_sub_categories';
        $this->set_data($data);
        return array(2,  $msg);
    }
    
    protected function parce_item()
    {
        return array(0,  'Для этого сайта метод не используется');
    }
    
    private function _parce_item($sub_sub_category)
    {
        $html = file_get_html($sub_sub_category['category_sourse_path']);
        
        // Основной рисунок
        $slide_ul = $html->find('ul#slide');
        $slide_lis = $slide_ul[0]->find('li');
        $slide_imgs = array();
        foreach($slide_lis as $slide_li)
        {
            $img = $slide_li->find('img');
            $slide_imgs[] = $this->base_link.$img[0]->src;;
        }
        
        $img_src = $slide_imgs[0];
        
        
        // Наименование
        $product_name = $sub_sub_category['name'];
        
        // Код продукта (здесь отсутствует, придумываем свой)
        $code = md5($product_name);
            
        // Стоимость
        $div_oldprice = $html->find('div.oldprice');
        if(!isset($div_oldprice[0]))
        {
            $div_oldprice = $html->find('div.newprice1');
        }
        $cena_mag = $this->_get_price($div_oldprice[0]);
        
        $div_newprice = $html->find('div.newprice');
        if(!isset($div_newprice[0]))
        {
            $cena_tut = $cena_mag;
        }
        else
        {
            $cena_tut = $this->_get_price($div_newprice[0]);
        }
        
        // Описание
        $description_div = $html->find('div.description');
        
        // Таблица размеров
        $tabcontent_div = $description_div[0]->find('div.tabcontent');
        $sizes_table = '';
        if(isset($tabcontent_div[0]))
        {
            $sizes_table = $tabcontent_div[0]->innertext;
        }
        
        $content_ps = $description_div[0]->find('p');
        $content = '';
        foreach($content_ps as $content_p)
        {
            $text = $content_p->innertext;
            if(!preg_match('/Выберите стиль:/', $text)
                    AND !preg_match('/Еще фотографии модели:/', $text)
                )
            {
                $content .= '<p>'.$text.'</p>';
            }
        }
        
        $desc = $content.$sizes_table;
        
        $dopinfo = array(
            'slide_imgs' => $slide_imgs,
            'img_large' => $img_src,
            'sizes_table' => $sizes_table,
            'content' => $content,
        );
        $product = array(
            'name' => $product_name,
            'code' => $code,
            'cena_tut' => $cena_tut,
            'cena_mag' => $cena_mag,
            'desc' => $desc,
            'alias' => JApplication::stringURLSafe($product_name),
            'category_id' => $sub_sub_category['id'],
            'site_alias' => $this->site_alias,
            'dopinfo' => json_encode($dopinfo),
        );
        
        return $product;
    }
    
    private function _get_price($div_price)
    {
        $price = preg_replace('/[^0-9]+/', '',$div_price->innertext);
        return $price;
    }
}
