<?php namespace lang\partial\unittest;

use lang\partial\{Accessors, Builder};

/**
 * Used by InstanceCreationTest
 */
class Book {
  use Book\including\Accessors;
  use Book\including\Builder;

  private $name, $author, $isbn;

  /**
   * Creates a new book
   *
   * @param  string $name
   * @param  lang.partial.unittest.Author $author
   * @param  lang.partial.unittest.Isbn $isbn
   */
  public function __construct($name, Author $author, Isbn $isbn= null) {
    $this->name= $name;
    $this->author= $author;
    $this->isbn= $isbn;
  }
}