{assign var='m_index' value='feMenu_'|cat:$block.ID}
{assign var='sCookie' value=$smarty.cookies}

<fieldset class="side_block{if $side_first} side_block_first{/if}">
	<legend class="header" onclick="action_block('{$block.ID}');">
		{if $name}{$name}{else}{$block.name}{/if}
	</legend>
	
	<div class="body{if $sCookie.$m_index == 'hide'} hide{/if}" {if $no_padding}style="padding: 0;"{/if} id="block_content_{$block.ID}">
		<div>