<?php
/**
 * @version   3.2.16 February 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class GantrySplitmenuLayout extends AbstractRokMenuLayout
{
    protected $theme_path;
    protected $params;

    private $activeid;

    public function __construct(&$args)
    {
        parent::__construct($args);
        global $gantry;
        $theme_rel_path = "/html/mod_roknavmenu/themes/gantry-splitmenu";
        $this->theme_path = $gantry->templatePath . $theme_rel_path;
        $this->args['theme_path'] = $this->theme_path;
        $this->args['theme_rel_path'] = $gantry->templateUrl. $theme_rel_path;
        $this->args['theme_url'] = $this->args['theme_rel_path'];
    }

    public function stageHeader()
    {
        global $gantry;

        //don't include class_sfx on 3rd level menu
        $this->args['class_sfx'] =  (array_key_exists('startlevel', $this->args) && $this->args['startLevel']==1) ? '' : $this->args['class_sfx'];
        $this->activeid = (array_key_exists('splitmenu_fusion_enable_current_id', $this->args) && $this->args['splitmenu_fusion_enable_current_id']== 0) ? false : true;

        JHtml::_('behavior.mootools');
        if ($this->isIe(6)) {
            $gantry->addScript(JURI::Root(true).'/modules/mod_roknavmenu/themes/fusion/js/sfhover.js');
        }
    }

    function isIe($version = false)
    {
        $agent=$_SERVER['HTTP_USER_AGENT'];
        $found = strpos($agent,'MSIE ');
        if ($found) {
                if ($version) {
                    $ieversion = substr(substr($agent,$found+5),0,1);
                    if ($ieversion == $version) return true;
                    else return false;
                } else {
                    return true;
                }

            } else {
                    return false;
            }
        if (stristr($agent, 'msie'.$ieversion)) return true;
        return false;
    }


    protected function renderItem(JoomlaRokMenuNode &$item, RokMenuNodeTree &$menu)
    {

        $item_params = $item->getParams();
        //not so elegant solution to add subtext
        $item_subtext = $item_params->get('splitmenu_item_subtext','');
        if ($item_subtext=='') $item_subtext = false;
        else $item->addLinkClass('subtext');
        ?>
        <li <?php if($item->hasListItemClasses()) : ?>class="<?php echo $item->getListItemClasses()?>"<?php endif;?> <?php if($item->hasCssId() && $this->activeid):?>id="<?php echo $item->getCssId();?>"<?php endif;?>>
            <?php if ($item->getType() == 'menuitem') : ?>
                <a <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?>"<?php endif;?> <?php if($item->hasLink()):?>href="<?php echo $item->getLink();?>"<?php endif;?> <?php if($item->hasTarget()):?>target="<?php echo $item->getTarget();?>"<?php endif;?> <?php if ($item->hasAttribute('onclick')): ?>onclick="<?php echo $item->getAttribute('onclick'); ?>"<?php endif; ?><?php if ($item->hasLinkAttribs()): ?> <?php echo $item->getLinkAttribs(); ?><?php endif; ?>>
                    <span>
                    <?php echo $item->getTitle();?>
                    <?php if (!empty($item_subtext)) :?>
                    <em><?php echo $item_subtext; ?></em>
                    <?php endif; ?>
                    <?php if ($item->getParent() == 0 && $item->hasChildren()): ?>
                    <span class="daddyicon"></span>
                    <?php endif; ?>
					<?php if ($item->getCssId()): ?>
					<div class="arrow-indicator"></div>
					<?php endif; ?>
                    </span>
                </a>
            <?php elseif($item->getType() == 'separator') : ?>
                <span <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?> nolink"<?php endif;?>>
                    <span>
                    <?php echo $item->getTitle();?>
                    <?php if (!empty($item_subtext)) :?>
                    <em><?php echo $item_subtext; ?></em>
                    <?php endif; ?>
                    <?php if ($item->getParent() == 0 && $item->hasChildren()): ?>
                    <span class="daddyicon"></span>
                    <?php endif; ?>
                    </span>
                </span>
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
<?php if ($menu->getChildren()) : ?>
<div class="rt-splitmenu">
	<div class="rt-menubar">
		<div class="rt-menubar2">
		    <ul class="menu<?php echo $this->args['class_sfx']; ?> level1" <?php if(array_key_exists('tag_id',$this->args)):?><?php endif;?>>
		        <?php foreach ($menu->getChildren() as $item) :  ?>
		             <?php $this->renderItem($item, $menu); ?>
		        <?php endforeach; ?>
		    </ul>
		</div>
	</div>
</div>
<?php endif; ?>
<?php
        return ob_get_clean();
    }
}