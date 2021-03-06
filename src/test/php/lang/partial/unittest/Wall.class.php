<?php namespace lang\partial\unittest;

use lang\partial\{Accessors, ToString};

/**
 * Used as fixture in the "AccessorsTest" class
 */
class Wall {
  use Wall\including\ToString;
  use Wall\including\Accessors;

  /** @type string */
  private $name;

  /** @type string */
  private $type;

  /** @type lang.partial.unittest.Post[] */
  private $posts;

  /**
   * Constructor
   *
   * @param  string $name
   * @param  string $type
   * @param  lang.partial.unittest.Post[] $posts
   */
  public function __construct($name, $type, array $posts) {
    $this->name= $name;
    $this->type= $type;
    $this->posts= $posts;
  }
}