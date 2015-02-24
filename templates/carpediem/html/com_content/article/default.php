<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   3.2.16 February 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();
?>
<div class="rt-article"><div class="rt-article-bg">
	<div class="item-page<?php echo $this->pageclass_sfx?>">
		<?php /** Begin Page Title **/ if ($this->params->get('show_page_heading', 1)) : ?>
		<h1 class="title">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
		<?php /** End Page Title **/  endif; ?>
		<?php /** Begin Article Title **/ if ($params->get('show_title')) : ?>
		<h2 class="title">
			<?php if ($params->get('link_titles') && !empty($this->item->readmore_link)) : ?>
			<a href="<?php echo $this->item->readmore_link; ?>">
				<?php echo $this->escape($this->item->title); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->title); ?>
			<?php endif; ?>
		</h2>
		<?php /** End Article Title **/ endif; ?>

		<?php /** Begin Article Icons **/ if ($canEdit ||  $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
			<div class="rt-article-icons">
				<ul class="actions">
				<?php if (!$this->print) : ?>
					<?php if ($params->get('show_print_icon')) : ?>
					<li class="print-icon">
						<?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?>
					</li>
					<?php endif; ?>

					<?php if ($params->get('show_email_icon')) : ?>
					<li class="email-icon">
						<?php echo JHtml::_('icon.email',  $this->item, $params); ?>
					</li>
					<?php endif; ?>
				
					<?php if ($canEdit) : ?>
					<li class="edit-icon">
						<?php echo JHtml::_('icon.edit', $this->item, $params); ?>
					</li>
					<?php endif; ?>
					<?php else : ?>
					<li>
						<?php echo JHtml::_('icon.print_screen',  $this->item, $params); ?>
					</li>
				<?php endif; ?>
				</ul>
			</div>
		<?php /** End Article Icons **/ endif; ?>

		<?php  if (!$params->get('show_intro')) :
			echo $this->item->event->afterDisplayTitle;
		endif; ?>

		<?php echo $this->item->event->beforeDisplayContent; ?>

		<?php $useDefList = (($params->get('show_author')) OR ($params->get('show_category')) OR ($params->get('show_parent_category'))
			OR ($params->get('show_create_date')) OR ($params->get('show_modify_date')) OR ($params->get('show_publish_date'))
			OR ($params->get('show_hits'))); ?>

		<?php /** Begin Article Info **/ if ($useDefList) : ?>
		 <dl class="rt-articleinfo">
		 <!--<dt class="rt-articleinfo-desc"><?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?></dt>-->
		<?php endif; ?>
		<?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root') : ?>
		<dd class="rt-parent-category">
			<?php	$title = $this->escape($this->item->parent_title);
					$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';?>
			<?php if ($params->get('link_parent_category') AND $this->item->parent_slug) : ?>
				<?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
			<?php endif; ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_category')) : ?>
		<dd class="rt-category">
			<?php 	$title = $this->escape($this->item->category_title);
					$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
			<?php if ($params->get('link_category') AND $this->item->catslug) : ?>
				<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_create_date')) : ?>
		<dd class="rt-date-posted">
			<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date',$this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_modify_date')) : ?>
		<dd class="rt-date-modified">
			<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_publish_date')) : ?>
		<dd class="rt-date-published">
			<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
		<?php endif; ?>
		<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
		<dd class="rt-author"> 
			<?php $author =  $this->item->author; ?>
			<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>

			<?php if (!empty($this->item->contactid ) &&  $params->get('link_author') == true):?>
				<?php 	echo JText::sprintf('COM_CONTENT_WRITTEN_BY' , 
				 JHtml::_('link',JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid),$author)); ?>

			<?php else :?>
				<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
			<?php endif; ?>
		</dd>
		<?php endif; ?>	
		<?php if ($params->get('show_hits')) : ?>
		<dd class="rt-hits">
			<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
		</dd>
		<?php endif; ?>
		<?php if ($useDefList) : ?>
		 </dl>
		<?php /** End Article Info **/ endif; ?>

		<?php if (isset ($this->item->toc)) : ?>
			<?php echo $this->item->toc; ?>
		<?php endif; ?>

		<?php if ($params->get('access-view')):?>
			<?php echo $this->item->text; ?>
			
		<?php //optional teaser intro text for guests ?>
		<?php elseif ($params->get('show_noauth') == true AND  $user->get('guest') ) : ?>
			<?php echo $this->item->introtext; ?>
			<?php //Optional link to let them register to see the whole article. ?>
			<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
				$link1 = JRoute::_('index.php?option=com_users&view=login');
				$link = new JURI($link1);?>
				<p class="readmore">
				<a href="<?php echo $link; ?>">
				<?php $attribs = json_decode($this->item->attribs);  ?> 
				<?php 
				if ($attribs->alternative_readmore == null) :
					echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
				elseif ($readmore = $this->item->alternative_readmore) :
					echo $readmore;
					if ($params->get('show_readmore_title', 0) != 0) :
					    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
					endif;
				elseif ($params->get('show_readmore_title', 0) == 0) :
					echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');	
				else :
					echo JText::_('COM_CONTENT_READ_MORE');
					echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
				endif; ?></a>
				</p>
			<?php endif; ?>
		<?php endif; ?>

		<?php echo $this->item->event->afterDisplayContent; ?>
	</div>
</div></div>