<?php namespace lang\kind\transformation;

/**
 * Compile-time `ValueObject` transformation which generates public accessors
 * based on fields included in your class definition as well as equals() and 
 * toString() methods.
 *
 * Include this transformation in your class as follows:
 * 
 * ```php
 * class Example extends \lang\Object {
 *   use \lang\kind\ValueObject‹namespace\of\Example›;
 *   private $name, $id;
 * }
 * ```
 *
 * In the above example, a `name()` and `id()` method are generated.
 *
 * @test  xp://lang.kind.unittest.ValueObjectTest
 */
class ValueObject extends Transformation {

  /**
   * Creates trait body
   *
   * @param  lang.XPClass
   * @return string
   */
  protected function body($class) {
    $unit= '';
    foreach ($class->getFields() as $field) {
      if (!($field->getModifiers() & MODIFIER_STATIC)) {
        $unit.= 'public function '.$field->getName().'() { return $this->'.$field->getName().'; }';
      }
    }

    // equals()
    $unit.= 'public function equals($cmp) {
      if ($cmp instanceof self) {
        $thisVars= (array)$this;
        $cmpVars= (array)$cmp;
        unset($thisVars[\'__id\'], $cmpVars[\'__id\']);
        return \util\Objects::equal($thisVars, $cmpVars);
      }
      return false;
    }';

    // toString()
    $unit.= 'public function toString() {
      $thisVars= get_object_vars($this);
      unset($thisVars[\'__id\']);
      return $this->getClassName().\'@\'.\util\Objects::stringOf($thisVars);
    }';

    return $unit;
  }
}