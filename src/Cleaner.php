<?php

namespace Wenprise\MenuManager;

class Cleaner
{
    /**
     * @var array
     */
    public $to_remove = [];


    /**
     * @var array
     */
    public $submenu_to_remove = [];


    /**
     * @var array
     */
    public $metabox_to_remove = [];


    /**
     * AdminMenuManager constructor.
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'do_remove']);
    }


    /**
     * 执行移除操作
     */
    public function do_remove()
    {
        global $submenu;

        foreach ($this->to_remove as $item) {
            remove_menu_page($item);
        }

        foreach ($this->submenu_to_remove as $parent => $indexes) {
            $indexes = (array)$indexes;

            foreach ($indexes as $index) {
                unset($submenu[ $parent ][ $index ]);
            }
        }
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
            $this->to_remove = array_merge($this->to_remove, $slug);
        } else {
            $this->to_remove[] = $slug;
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
        $this->submenu_to_remove[ $parent ] = $index;

        return $this;
    }
}