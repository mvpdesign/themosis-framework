<?php
namespace Themosis\PostType;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;

class PostTypeBuilder implements IPostType {

    /**
     * PostTypeData instance.
     *
     * @var \Themosis\Core\DataContainer
     */
    private $datas;

    /**
     * Event object.
     */
    private $event;

    /**
     * The registered custom post type.
     *
     * @var Object|\WP_Error
     */
    private $postType;

    /**
     * Build a custom post type.
     *
     * @param DataContainer $datas The post type properties.
     */
    public function __construct(DataContainer $datas)
    {
        $this->datas = $datas;
        $this->event = Action::listen('init', $this, 'register');
    }

    /**
     * Define a new custom post type.
     *
     * @param string $name The post type slug name.
     * @param string $plural The post type plural name for display.
     * @param string $singular The post type singular name for display.
     * @throws PostTypeException
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function make($name, $plural, $singular)
    {
        $params = compact('name', 'plural', 'singular');

        foreach ($params as $key => $param)
        {
            if (!is_string($param))
            {
                throw new PostTypeException('Invalid custom post type parameter "'.$key.'". Accepts string only.');
            }
        }

        // Set main properties.
        $this->datas['name'] = $name;
        $this->datas['args'] = $this->setDefaultArguments($plural, $singular);

        return $this;
    }

    /**
     * Set the custom post type. A user can also override the
     * arguments by passing an array of custom post type arguments.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     * @param array $params The custom post type arguments.
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function set(array $params = array())
    {
        // Override custom post type arguments if given.
        $this->datas['args'] = array_merge($this->datas['args'], $params);

        // Trigger the init event in order to register the custom post type.
        $this->event->dispatch();

        return $this;
    }

    /**
     * Triggered by the 'init' action event.
     * Register a WordPress custom post type.
     *
     * @return void
     */
    public function register()
    {
        $this->postType = register_post_type($this->datas['name'], $this->datas['args']);
    }

    /**
     * Returns the custom post type slug name.
     *
     * @deprecated
     * @return string
     */
    public function getSlug()
    {
        return $this->datas['name'];
    }

    /**
     * @param null $property
     * @return array
     * @throws PostTypeException
     */
    public function get($property = null)
    {
        $name = array(
            'name'  => $this->datas['name']
        );

        $properties = array_merge($name, $this->datas['args']);

        // If no property asked, return all defined properties.
        if (is_null($property) || empty($property))
        {
            return $properties;
        }

        // If property exists, return it.
        if (isset($properties[$property]))
        {
            return $properties[$property];
        }

        throw new PostTypeException("Property '{$property}' does not exist on the '{$properties['name']}' custom post type.");
    }

    /**
     * Allow a user to change the title placeholder text.
     *
     * @param string $title The title placeholder text.
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function setTitle($title)
    {
        $name = $this->getSlug();

        add_filter('enter_title_here', function($default) use($name, $title)
        {
            $screen = get_current_screen();

            if ($name == $screen->post_type)
            {
                $default = $title;
            }

            return $default;
        });

        return $this;
    }

    /**
     * Set the custom post type default arguments.
     *
     * @param string $plural The post type plural display name.
     * @param string $singular The post type singular display name.
     * @return array
     */
    private function setDefaultArguments($plural, $singular)
    {
        $labels = array(
            'name' => __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'singular_name' => __($singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'add_new' => __('Add New', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'add_new_item' => __('Add New '. $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'edit_item' => __('Edit '. $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'new_item' => __('New ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'all_items' => __('All ' . $plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'view_item' => __('View ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'search_items' => __('Search ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'not_found' =>  __('No '. $singular .' found', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'not_found_in_trash' => __('No '. $singular .' found in Trash', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'parent_item_colon' => '',
            'menu_name' => __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN)
        );

        $defaults = array(
            'label' 		=> __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'labels' 		=> $labels,
            'description'	=> '',
            'public'		=> true,
            'menu_position'	=> 20,
            'has_archive'	=> true
        );

        return $defaults;
    }

} 