<?php
/**
 * @version   3.2.16 February 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class GantrySuckerfishLayout extends AbstractRokMenuLayout
{
    protected $theme_path;
    protected $params;

    private $activeid;

    public function __construct(&$args)
    {
        parent::__construct($args);
        global $gantry;
        $theme_rel_path = "/html/mod_roknavmenu/themes/gantry-suckerfish";
        $this->theme_path = $gantry->templatePath . $theme_rel_path;
        $this->args['theme_path'] = $this->theme_path;
        $this->args['theme_rel_path'] = $gantry->templateUrl. $theme_rel_path;
        $this->args['theme_url'] = $this->args['theme_rel_path'];
    }

    public function stageHeader()
    {
        global $gantry;
        $gantry->addStyle('suckerfish.css');
    }

    protected function renderItem(JoomlaRokMenuNode &$item, RokMenuNodeTree &$menu)
    {
        $item_params = $item->getParams();
        //not so elegant solution to add subtext
        $item_subtext = $item_params->get('splitmenu_item_subtext','');
        ?>
        <li id="<?php echo $item->getCssId();?>">
            <?php if ($item->getType() == 'menuitem') : ?>
                <a <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?>"<?php endif;?> <?php if($item->hasLink()):?>href="<?php echo $item->getLink();?>"<?php endif;?> <?php if($item->hasTarget()):?>target="<?php echo $item->getTarget();?>"<?php endif;?> <?php if ($item->hasAttribute('onclick')): ?>onclick="<?php echo $item->getAttribute('onclick'); ?>"<?php endif; ?><?php if ($item->hasLinkAttribs()): ?> <?php echo $item->getLinkAttribs(); ?><?php endif; ?>>
                    <span>
                    <?php echo $item->getTitle();?>
                    <?php if (!empty($item_subtext)) :?>
                    <em><?php echo $item_subtext; ?></em>
                    <?php endif; ?>
                    </span>
                </a>
            <?php elseif($item->getType() == 'separator') : ?>
                <a href="#" <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?> nolink"<?php endif;?>><span <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?> nolink"<?php endif;?>>
                    <span>
                    <?php echo $item->getTitle();?>
                    <?php if (!empty($item_subtext)) :?>
                    <em><?php echo $item_subtext; ?></em>
                    <?php endif; ?>
                    </span>
                </span></a>
            <?php endif; ?>
            <?php if ($item->hasChildren()): ?>
                    <ul class="level<?php echo intval($item->getLevel())+2; ?>">
                        <?php foreach ($item->getChildren() as $child) : ?>
                            <?php $this->renderItem($child, $menu); ?>
                        <?php endforeach; ?>
                    </ul>
            <?php endif; ?>
        </li>
        <?php
    }


    public function renderMenu(&$menu) {
        ob_start();
        ?>
       <ul class="menutop level1" <?php if(array_key_exists('tag_id',$this->args)):?>id="<?php echo  $this->args['tag_id'];?>"<?php endif;?>>
            <?php foreach ($menu->getChildren() as $item) :  ?>
                <?php $this->renderItem($item, $menu); ?>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }
}