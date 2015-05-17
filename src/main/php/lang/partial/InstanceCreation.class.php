<?php namespace lang\partial;

use lang\mirrors\TypeMirror;
use lang\ClassLoader;
use lang\IllegalArgumentException;

abstract class InstanceCreation extends \lang\Object {
  private static $creations= [];

  /**
   * Creates a new instance creation fluent interface for a given class
   *
   * @param  var $class Either a lang.XPClass or a string
   * @return lang.XPClass
   */
  public static final function typeOf($class) {
    $mirror= new TypeMirror($class);
    $type= $mirror->name();

    if (!isset(self::$creations[$type])) {
      if (!$mirror->kind()->isClass() || $mirror->modifiers()->isAbstract()) {
        throw new IllegalArgumentException('Class '.$type.' is not instantiable');
      }

      $constructor= $mirror->constructor();
      if (!$constructor->present()) {
        throw new IllegalArgumentException('Class '.$type.' does not have a constructor');
      }

      $setters= $args= '';
      foreach ($constructor->parameters() as $parameter) {
        $name= $parameter->name();
        if ($parameter->isOptional()) {
          $setters.= 'public $'.$name.'= '.var_export($parameter->defaultValue(), true).';';
        } else {
          $setters.= 'public $'.$name.';';
        }
        $setters.= 'public function '.$name.'($value) { $this->'.$name.'= $value; return $this; }';
        $args.= ', $this->'.$name;
      }

      self::$creations[$type]= ClassLoader::defineClass($type.'Creation', 'lang.partial.InstanceCreation', [], '{
        public function create() { return new \\'.$mirror->reflect->name.'('.substr($args, 2).'); }
        '.$setters.'
      }');
    }
    return self::$creations[$type];
  }

  /**
   * Creates a new instance creation fluent interface for a given class
   *
   * @param  var $class Either a lang.XPClass or a string
   * @return self
   */
  public static final function of($class) {
    return self::typeOf($class)->newInstance();
  }

  /**
   * Creates the instance
   *
   * @return lang.Generic
   */
  public abstract function create();

  /** @return string */
  public function toString() {
    return $this->getClassName().'('.implode(', ', array_keys(get_object_vars($this))).')';
  }

  /**
   * Returns whether a given value equals this instance
   *
   * @param  var $value
   * @return bool
   */
  public function equals($value) {
    return $value instanceof self && $this->getClassName() === $value->getClassName();
  }
}