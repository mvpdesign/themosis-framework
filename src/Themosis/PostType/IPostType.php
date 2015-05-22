<?php
namespace Themosis\PostType;


interface IPostType {

    /**
     * Method called to build a post type instance.
     *
     * @param $name The custom post type name.
     * @param $plural The custom post type plural display name.
     * @param $singular The custom post type singular display name.
     * @return \Themosis\PostType\IPostType
     */
    public function make($name, $plural, $singular);

    /**
     * Method called to register the post type into WordPress.
     *
     * @param array $params
     * @return mixed
     */
    public function set(array $params = array());

    /**
     * Method to return defined post type properties.
     *
     * @param null $property
     * @return mixed
     */
    public function get($property = null);

}