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

abstract class IshopParser
{

    protected $site_alias = '';
    protected $site_id = 0;
    protected $site = '';
    protected $data = array();
    
    protected $file_data;
    protected $file_category;


    public function __construct() {
        $this->site = $this->_get_site();
        $this->file_data = JPATH_ROOT.'/tmp/parse_'.$this->site_alias.'_data.txt';
    }

    /**
     * Точка входа парсера
     * 
     * @return aray
     */
    public function parce()
    {
        if(!$this->site->base_url)
        {
            // Не найден сайт-источник по его алиасу
            return array(0,  JText::_('COM_ISHOP_SITE_NOT_FIND'));
        }
        
        // Проверяем на начало парсинга (загрузка ли стартовой страницы)
        $start = JRequest::getInt('start',0);
        if($start)
        {
            // Инициируем начало парсинга
            $this->start();
        }
        $data = $this->get_data();
        if(!isset($data['proc']))
        {
            return array(0,  JText::_('COM_ISHOP_DATA_EXIST'));
        }
        $proc = $data['proc'];
        return $this->$proc();
        
    }

    /**
     * Возврат ИД сайта
     * @return intrger
     */
    protected function get_site_id()
    {
        if(!$this->site_id)
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(TRUE)
                    ->select('`id`')
                    ->from('#__ishop_sites')
                    ->where('`alias` = "'.$this->site_alias.'"')
            ;
            $db->setQuery($query);
            $this->site_id = $db->loadResult();
        }
        
        return $this->site_id;
    }
    
    protected function get_parent_category_id()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE)
                ->select('*')
                ->from('#__ishop_categories')
                ->where('`parent_id` = 1')
                ->where('`alias` = "'.$this->site_alias.'"')
        ;
        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * Установка ИД сайта
     * @param integer $id 
     */
    public function set_site_id($id)
    {
        $this->site_id = (int)$id;
    }

    /**
     * Начальные установки данных парсера 
     */
    protected function start()
    {
        $file_data = $this->file_data;
        // Удаляем файлы дампа данных
        jimport('joomla.filesystem.file');
        if(JFile::exists($file_data))
        {
            JFile::delete($file_data);
        }
        $data = array(
            'proc'=>'create_categories'
        );
        $this->set_data($data);
    }

    protected function create_categories()
    {
        $data = $this->get_data();
        $data['categories'] = array();
        $data['proc'] = 'parce_category';
        $this->set_data($data);
        return array(2,  JText::_('COM_ISHOP_PARCE_CREATE_CATEGORIES'));
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
        var_dump($data['categories']);
        exit;
        // Топовые категории
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
                // Ошибка записи категории категорию
                return array(0,  JText::_('COM_ISHOP_CAN_NOT_CREATE_CATEGORY'));
            }
            $categories[] = $category;
        }

        // Подкатегории 1-го уровня
        $data['categories'] = $categories;
        $data['proc'] = 'create_sub_categories';
        $this->set_data($data);
        return array(2,  JText::_('COM_ISHOP_CREATE_SUB_CATEGORIES'));
        
    }
    
    protected function parce_category()
    {
        return array(1,  JText::_('ZAGLUSHKA'));
    }

    protected function create_sub_categories()
    {
        return array(1,  JText::_('ZAGLUSHKA'));
    }
    protected function parce_items()
    {
        return array(1,  JText::_('PARSE_ITEMS_ZAGLUSHKA'));
    }
    protected function parce_item()
    {
        return array(1,  JText::_('PARSE_ITEM_ZAGLUSHKA'));
    }

    /**
    * Параметры сайта источника
    * 
    * @return object
    */
    private function _get_site()
    {
        if(!$this->site)
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(TRUE)
                    ->select('*')
                    ->from('`#__ishop_sites`')
                    ->where('`alias` = "'.$this->site_alias.'"')
            ;
            $db->setQuery($query);

            $this->site = $db->loadObject();
            if(!$this->site)
            {
                $this->site = new stdClass();
                $this->site->base_url = '';
            }
        }
        return $this->site;
    }

    
    /**
     * ЧПУ путь к категории
     * @param string $category_name
     * @param string $parent_path
     * @return string 
     */
    protected function get_alias($category_name)
    {
        return JApplication::stringURLSafe($category_name);
    }

    /**
     * Вычисление ИД категории
     * @param type $category_path
     * @return type 
     */
    protected function get_category_id($category_path)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE)
                ->select('`id`')
                ->from('`#__ishop_categories`')
                ->where('`path` = "'.$category_path.'"')
        ;
        $db->setQuery($query);
        
        return $db->loadResult();
    }

    /**
    *  Создаем категорию
    * @param array данные категории
    * @return ineger ID сохраненной категории
    */
   protected function create_category($data)
    {
        $id = $this->get_category_id($data['path']);
        if($id)
        {
            $data['id'] = $id;
        }
        // Сохраняем категорию
        $category_model = new IshopModelCategory;
        $result = $category_model->create_category($data);
        if(!$result)
        {
            // Не смогли сохранить категорию
            return 0;
        }
        return  $result;
    }

    
/**
 * =============================================================================
 * Вспомогательные методы
 * ============================================================================= 
 */
    protected function get_category_start_data()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(TRUE)
                ->select('`products`')
                ->from('#__ishop_sites')
                ->where('`alias` = "'.$this->site_alias.'"')
        ;
        $db->setQuery($query);
        return $db->loadResult();

    }

/**
     * Выбор списка категорий из БД
     * @return array
     */
    protected function get_top_categories_array()
    {
        $data = $this->get_category_start_data();

        $rows = explode(',', $data);
        $categories = array();
        foreach ($rows as $row)
        {
            $item = explode('=', $row);
            if(isset($item[1]))
            {
                $categories[] = array(
                    'name' => trim($item[0]),
                    'category_sourse_path' => trim($item[1])
                );
            }
        }
        
        return $categories;
    }


    /**
     * Записываем изделие в базу
     * @param array данные об изделии
     * @return boolean
     */
    protected function add_item($data_item)
    {

        $result = TRUE;
        if($data_item['alias'])
        {
            $product_model = new IshopModelProduct;
             // Сохраняем продукт
            $product_id = $product_model->save_product($data_item);
             if(!$product_id)
             {
//                 $db = JFactory::getDbo();
//                 var_dump($db);
//                 exit;
                 $result = FALSE;
             }
        }
        return $result;
    }
    
  
    /**
     * Сохраняем данные перед выходом
     * @param array $data 
     */
    protected function set_data($data)
    {
        if (!JFile::write($this->file_data, json_encode($data)))
        {
            return FALSE;
        }
        return TRUE;
    }
    
   
    /**
     * Берем сохраненные данные
     * @param array $data 
     */
    protected function get_data()
    {
        if(!$this->data)
        {
            if(JFile::exists($this->file_data))
            {
                $this->data = json_decode(JFile::read($this->file_data),TRUE);
            }
        }
        return $this->data;
    }
    
    /**
     * Конвертация символов
     * @param string $string
     * @return string 
     */
    protected function _iconv($string)
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
     * Вывод сообщения с параметрами изделия
     * @param type $data_item
     * @return string
     */
    protected function get_msg($data_item)
    {
        $dopinfo = json_decode($data_item['dopinfo'],TRUE);
        $msg = '';
        if(isset($dopinfo['img_large']) AND $dopinfo['img_large'])
        {
            
            $msg .= '<img src="'.$dopinfo['img_large'].'" height="100" width="100" style="float:left;">';
        }
        $color = 'green';
        $msg .= '
            <table style="color:'.$color.'">
                <tr>
                    <th>Наименование</th>
                    <td>'.$data_item['name'].'</td>
                </tr>
                <tr>
                    <th>Код</th>
                    <td>'.$data_item['code'].'</td>
                </tr>
                <tr>
                    <th>Цена</th>
                    <td>'.$data_item['cena_tut'].'('.$data_item['cena_mag'].')</td>
                </tr>
                <tr>
                    <th>Описание</th>
                    <td>'.$data_item['desc'].'</td>
                </tr>
            </table>
            ';
        return $msg;
    }
    
    
    
}
