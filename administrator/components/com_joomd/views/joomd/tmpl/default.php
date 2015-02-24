<?php

/*------------------------------------------------------------------------
# com_joomd - JoomD
# ------------------------------------------------------------------------
# author    Danish Babu - Joomla6Teen Inc
# copyright Copyright (C) 2012 joomla6teen.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomla6teen.com
# Technical Support:  Forum - http://www.joomla6teen.com/Discussions/latest/joomd.html
-----------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access'); ?>

<div id="joomdpanel">
<div class="add_items_x_tool"><?php echo JText::_('CHARTS_N_STATS'); ?></div>
<div style="background: #F5F5F5;border: 1px solid #DBDBDB;border-radius: 4px 4px 4px 4px;margin-top: -2px; padding:20px 15px 0px; margin-bottom:10px;">
	<div class="jd_cp_blocks">
    
    <?php
	
		for($i=0;$i<count($this->panel);$i++)	{
			$excol = ($i%4<>0 or !$i)?'':' style="margin-right:0px;"';
			echo '<div class="jd_cp_block"'.$excol.'>';
			echo '<div class="jd_joomd_box">';
			echo '<h2>'.$this->panel[$i]->label.'</h2>';
			echo $this->panel[$i]->panelhtml;
			echo '</div></div>';
			
		}
	
	?>
    
    <div class="clr"></div>
    </div>
    </div>

	<div class="cpanel-left">
        
        <div class="cpanel_chart">
        	
			<script type="text/javascript">
			
			if(typeof google !== "undefined")
	            google.setOnLoadCallback(drawChart);
			  
			$jd('.chart_x_tool span').live('click', function(event)	{
				$jd('.chart_x_tool>span').removeClass('active');
				$jd(this).addClass('active');
				drawChart();
			});
			  
              function drawChart() {
				  
				var ar = $jd('.chart_x_tool>span.active').attr('class').split(' ')[0];
				  
				$jd.ajax({
					  url: "index.php",
					  type: "POST",
					  dataType:"json",
					  data: {'option':'com_joomd', 'task':'drawchart', 'ar':ar, 'abase':1},
					  /*beforeSend: function()	{
						$jd("#joomdpanel .loadingblock").show();
					  },
					  complete: function()	{
						$jd("#joomdpanel .loadingblock").hide();
					  },*/
					  success: function(data)	{
						  
						if(data.result == "success")	{
							
							var hitdata = google.visualization.arrayToDataTable(data.items);
					
							var hitoptions = {
							  title: '<?php JText::_('ITEM_HITS'); ?>',
							  hAxis: {title: $jd('.chart_x_tool>span.active').text(),  titleTextStyle: {color: 'red'}},
							  backgroundColor:'#f5f5f5',
							  colors: ['blue']
							};
					
							var chart = new google.visualization.AreaChart(document.getElementById('item_chart_div'));
							chart.draw(hitdata, hitoptions);
						
						}
						else
							displayalert(data.error, "error");
							
					  }/*,
					  error: function(jqXHR, textStatus, errorThrown)	{
						displayalert(textStatus, "error");
					  }*/
				});
				  
                
              }
            </script>
            
            <div class="chart_x_tool">
            	<span class="day active"><?php echo JText::_('DAY'); ?></span>
            	<span class="week"><?php echo JText::_('WEEK'); ?></span>
                <span class="month"><?php echo JText::_('MONTH'); ?></span>
                <span class="year"><?php echo JText::_('YEAR'); ?></span>
            </div>
            
            <div id="item_chart_div"></div>
        
        </div>
            
	</div>
    
    <div class="cpanel-right">
    		 <?php
		
		echo $this->accordion->startPane('accordian', array('collapsible'=>true));
					
			echo $this->accordion->startPanel('order_'.$i, JText::_('WELCOMETEXT'));
			echo JText::_('WELCOMELONGTEXT');
			echo $this->accordion->endPanel();
			
			echo $this->accordion->startPanel('order_'.$i, JText::_('J16_SUPPORT'));
			echo JText::_('SUPPORT_TEXT');
			echo $this->accordion->endPanel();
					
		echo $this->accordion->endPane();
		
		?>
    </div>
	
<div class="clr"></div></div>