<script type="text/javascript">
	
	<?php
	
	$akey = isset($this->cparams->akey)?$this->cparams->akey:$this->params->akey;
	
	$akey = empty($akey)?JText::_('All'):$akey;
	
	?>
	
	$jd(function()	{
		
		$jd('.joomd_alphabets>span').each(function(index) {
			
			if($jd(this).text().toLowerCase() == "<?php echo $akey; ?>")
				$jd(this).addClass('active');
			
		});
		
		$jd('.joomd_alphabets span').live('click', function()	{
			
			$jd('.joomd_alphabets span').removeClass('active');
			
			$jd(this).addClass('active');
			
			$jd("input[name='akey']").val($jd(this).text());
			
			filterlist(this);
			
		});
					
	});

</script>

<div class="joomd_alphabets">

<?php
	
	$alph = str_replace(' ', '', JText::_('ALPHABET_ARRAY'));
	
	if($alph == 'ALPHABET_ARRAY')
		$arr = array();
	else
		$arr = explode(',', $alph);
	
	if(!count($arr))
		$arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
	
	array_unshift($arr, '#');
	array_push($arr, JText::_('ALL'));
	
	echo '<span>'.implode('</span> <span>', $arr).'</span>';

?>
<input type="hidden" name="akey" value="<?php echo $akey; ?>" />
</div>