<?php namespace lang\kind;

use util\Objects;

/**
 * The `ValueObject` trait ensures `equals()` and `toString()` are implemented for this 
 * value object in a generic way.
 *
 * @test  xp://lang.kind.unittest.ValueObjectTest
 */
trait ValueObject {

  /**
   * Returns whether a given value is equal to this value
   *
   * @param  var $cmp
   * @return bool
   */
  public function equals($cmp) {
    if ($cmp instanceof self) {
      $thisVars= (array)$this;
      $cmpVars= (array)$cmp;
      unset($thisVars['__id'], $cmpVars['__id']);
      return Objects::equal($thisVars, $cmpVars);
    }
    return false;
  }

  /**
   * Creates a string representation
   *
   * @return string
   */
  public function toString() {
    $thisVars= get_object_vars($this);
    unset($thisVars['__id']);
    return $this->getClassName().'@'.Objects::stringOf($thisVars);
  }
}