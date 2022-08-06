<?php

namespace Wenprise;

class Cleaner
{
    /**
     * @var array
     */
    public $dashboard_wdigets = [];
    
    /**
     * @var array
     */
    public $menus_to_remove = [];


    /**
     * @var array
     */
    public $submenus_to_remove = [];


    /**
     * @var array
     */
    public $metaboxes_to_remove = [];


    /**
     * AdminMenuManager constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'do_remove'], PHP_INT_MAX);
        add_action('wp_dashboard_setup', [$this, 'remove_dashboard_widgets'] );
    }


    /**
     * 执行移除操作
     */
    public function do_remove()
    {
        global $submenu;

        foreach ($this->menus_to_remove as $item) {
            remove_menu_page($item);
        }

        foreach ($this->submenus_to_remove as $parent => $indexes_or_slug) {

            if ( ! is_int($indexes_or_slug) && ! is_array($indexes_or_slug)) {
                remove_submenu_page($parent, $indexes_or_slug);
            } else {
                $indexes = (array)$indexes_or_slug;

                foreach ($indexes as $index) {
                    unset($submenu[ $parent ][ $index ]);
                }
            }

        }

        foreach ($this->metaboxes_to_remove as $metabox_to_remove) {
            remove_meta_box($metabox_to_remove[ 'id' ], $metabox_to_remove[ 'screen' ], $metabox_to_remove[ 'context' ]);
        }
    }
    
    
    function remove_dashboard_widgets(){
    }


    /**
     * 移除顶级菜单
     *
     * @param string|array $slug
     *
     * @return $this
     */
    function remove_menu($slug)
    {
        if (is_array($slug)) {
            $this->menus_to_remove = array_merge($this->menus_to_remove, $slug);
        } else {
            $this->menus_to_remove[] = $slug;
        }

        return $this;
    }


    /**
     * 移除子菜单
     *
     * @param string       $parent
     * @param string|array $index
     *
     * @return $this
     */
    function remove_submenu($parent, $index)
    {
        $this->submenus_to_remove[ $parent ] = $index;

        return $this;
    }


    /**
     * 移除Metabox
     *
     * @param $id
     * @param $screen
     * @param $context
     *
     * @return $this
     */
    function remove_meta_box($id, $screen, $context)
    {
        $this->metaboxes_to_remove[] = [
            'id'      => $id,
            'screen'  => $screen,
            'context' => $context,
        ];

        return $this;
    }
    
    
    function remove_dashboard_widget($widget_id){
        $this->metaboxes_to_remove[]  = $widget_id;
        
        return $this;
    }
    
    function search_keys_path_by_value($search_value, $array, $id_path) {
  
        // Iterating over main array
        foreach ($array as $key1 => $val1) {

            $temp_path = $id_path;

            // Adding current key to search path
            array_push($temp_path, $key1);

            // Check if this value is an array
            // with atleast one element
            if(is_array($val1) and count($val1)) {

                // Iterating over the nested array
                foreach ($val1 as $key2 => $val2) {

                    if($val2 == $search_value) {

                        // Adding current key to search path
                        array_push($temp_path, $key2);

                        return join(" --> ", $temp_path);
                    }
                }
            }

            elseif($val1 == $search_value) {
                return join(" --> ", $temp_path);
            }
        }

        return null;
    }
}
