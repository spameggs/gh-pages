<!-- langiages selector block -->

{if $languages|@count > 1}
	<div id="languages">
		<div>
			<ul>
				{foreach from=$languages item=lang_code}
					{if $lang_code.Code != $smarty.const.RL_LANG_CODE}
						<li><a title="{$lang_code.name}" href="{$rlBaseLang}{if $config.mod_rewrite}{$lang_code.dCode}{$pageLink}{else}index.php?language={$lang_code.Code}{/if}">{$lang_code.name}</a></li>
					{/if}
				{/foreach}
			</ul>
			<div>
				<span>{$languages[$smarty.const.RL_LANG_CODE].name} <img alt="" src="{$rlTplBase}img/blank.gif" /></span>
			</div>
		</div>
	</div>
{/if}

<!-- langiages selector block end -->