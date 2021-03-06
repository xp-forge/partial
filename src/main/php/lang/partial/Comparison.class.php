<?php namespace lang\partial;

use util\Comparator;

class Comparison implements Comparator {
  private $function;

  /**
   * Creates a new comparison based on a given function.
   *
   * @param  function(var, var): int $function
   */
  public function __construct($function) {
    $this->function= $function;
  }

  /**
   * Compares two instances, a and b, using the function passed to the
   * constructor.
   *
   * @param  var $a
   * @param  var $b
   */
  public function compare($a, $b) {
    $f= $this->function;
    return $f($a, $b);
  }

  /**
   * Combines this comparison with another
   *
   * @param  util.Comparator $next
   * @return  self
   */
  public function then(Comparator $next) {
    return new self(function($a, $b) use($next) {
      if (0 === ($r= $this->compare($a, $b))) {
        return $next->compare($a, $b);
      } else {
        return $r;
      }
    });
  }

  /**
   * Reverses this comparison.
   *
   * @return  self
   */
  public function reverse() {
    return new self(function($a, $b) {
      return -1 * $this->compare($a, $b);
    });
  }
}