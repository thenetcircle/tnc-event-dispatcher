<?php

namespace Tnc\Service\EventDispatcher;

/**
 * TncCore_Class_Serializable
 *
 * @package    tncCorePlugin
 *
 * @author     The NetCircle
 */
interface Serializable
{
  /**
   * Serialize instance to textual representation.
   *
   * @return string
   */
  public function serialize();

  /**
   * Unserialize textual representation back to this instance.
   *
   * @param string $string
   *
   * @return Serializable
   */
  public function unserialize($string);
}
