<?php

namespace Wenprise;

class Cleaner
{
    /**
     * @var array
     */
    public $dashboard_widgets_to_remove = [];

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
        add_action('wp_dashboard_setup', [$this, 'remove_dashboard_widgets']);
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


    /**
     * 执行移除仪表盘小工具操作
     *
     * @return $this
     */
    function remove_dashboard_widgets(): Cleaner
    {
        global $wp_meta_boxes;

        foreach ($this->dashboard_widgets_to_remove as $item) {
            $paths = $this->search_keys_path_by_value($wp_meta_boxes, $item);

            foreach ($paths as $path) {
                $keys = explode('.', $path);
                unset($wp_meta_boxes[ $keys[ 0 ] ]     [ $keys[ 1 ] ]   [ $keys[ 2 ] ]   [ $keys[ 3 ] ]);
            }
        }

        return $this;

    }


    /**
     * 移除顶级菜单
     *
     * @param string|array $slug
     *
     * @return $this
     */
    function remove_menu($slug): Cleaner
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
    function remove_submenu($parent, $index): Cleaner
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
    function remove_meta_box($id, $screen, $context): Cleaner
    {
        $this->metaboxes_to_remove[] = [
            'id'      => $id,
            'screen'  => $screen,
            'context' => $context,
        ];

        return $this;
    }


    /**
     * @param $widget_id
     *
     * @return $this
     */
    function remove_dashboard_widget($widget_id): Cleaner
    {
        $this->dashboard_widgets_to_remove[] = $widget_id;

        return $this;
    }


    /**
     * @param $array
     * @param $search_key
     * @param $carry
     *
     * @return array
     */
    function search_keys_path_by_value($array, $search_key, $carry = null): array
    {

        $fullKeys = []; //that's the initial array we'll be returning
        foreach ($array as $key => $val) //begin looping first level of array
        {
            //if that's our desired key, add it to the carry (in case it's present) and save in te array
            if ($key == $search_key) {
                $fullKeys[] = $carry ? "$carry.$key" : $key;
            } elseif (is_array($val)) {
                //else we'll do recursion
                //our function returns an array, so we'll merge the results with the results previously gathered in our $fullKeys array
                //our new array to search is the $val array, so we put that in
                //if we had a carry as the key from a previous iteration, just concat it with the current key (add a dot if needed) and that's our new carry!
                $fullKeys = array_merge($fullKeys, $this->search_keys_path_by_value($val, $search_key, $carry ? "$carry.$key" : $key));
            }
        }

        return $fullKeys;
    }
}
