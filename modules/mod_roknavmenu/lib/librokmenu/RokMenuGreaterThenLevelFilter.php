<?php
/**
 * @version   1.9 February 3, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokMenuGreaterThenLevelFilter  extends RecursiveFilterIterator  {
   protected $level;

    public function __construct(RecursiveIterator $recursiveIter, $end) {
        $this->level = $end;
        parent::__construct($recursiveIter);
    }
    public function accept() {
        return $this->hasChildren() || $this->current()->getLevel() > $this->level;
    }

    public function getChildren() {
        return new self($this->getInnerIterator()->getChildren(), $this->level);
    }
}
