<div class="content_block">
	<div class="header"><span>{if $name}{$name}{else}{$block.name}{/if}</span></div>
	<div class="body inner" {if !$name}id="block_content_{$block.ID}"{/if} {if $no_padding}style="padding: 0;"{/if}>