<?php namespace lang\partial;

/**
 * Compile-time transformation which generates a constructor.
 * 
 * ```php
 * use lang\partial\Sortable;
 *
 * class Example extends \lang\Object {
 *   use Example\including\Constructor;
 *
 *   private $firstName, $lastName;
 * }
 * ```
 *
 * This generates the following code:
 *
 * ```php
 * public function __construct($firstName, $lastName) {
 *   $this->firstName= $firstName;
 *   $this->lastName= $lastName;
 * }
 * ```
 *
 * The parameters appear in the order the fields are declared: top to bottom,
 * left to right inside the source code.
 *
 * @test  xp://lang.partial.unittest.ConstructorTest
 */
class Constructor extends Transformation {

  /**
   * Creates trait body
   *
   * @param  lang.mirrors.TypeMirror $mirror
   * @return string
   */
  protected function body($mirror) {
    $signature= $assignments= '';
    foreach ($this->instanceFields($mirror) as $field) {
      $n= $field->name();
      $signature.= ', $'.$n;
      $assignments.= '$this->'.$n.'= $'.$n.';';
    }
    return 'public function __construct('.substr($signature, 2).') { '.$assignments.' }';
  }
}