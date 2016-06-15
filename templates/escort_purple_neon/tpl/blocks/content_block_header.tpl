<div class="content_block">
	<div class="header">
		<div class="name">{if $name}{$name}{else}{$block.name}{/if}</div>
		<div class="line"><div></div></div>
	</div>
	
	<div class="body inner" {if !$name}id="block_content_{$block.ID}"{/if} {if $no_padding}style="padding: 0;"{/if}>