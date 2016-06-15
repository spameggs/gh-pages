<!-- referent number look up block -->
<form name="refnumber_lookup" action="" method="post">
	<input style="width: 90%;" type="text" maxlength="{$config.ref_tpl|count_characters}" id="ref_input" value="{$lang.ref_label}" />
	<input type="submit" value="{$lang.search}" style="margin-top: 10px;" />
</form>
<script type="text/javascript">
var refnumber_input_default = "{$lang.ref_label}";
{literal}
	$(document).ready(function(){
		$('form[name=refnumber_lookup] input[type=text]').focus(function(){
			if ( $(this).val() == refnumber_input_default )
			{
				$(this).val('');
			}
		}).blur(function(){
			if ( $(this).val() == '' )
			{
				$(this).val(refnumber_input_default);
			}
		});
		$('form[name=refnumber_lookup]').submit(function(){
			if ( $('#ref_input').val() )
			{
				$('form[name=refnumber_lookup] input[type=submit]').val(lang['loading']);
				xajax_refSearch( $('form[name=refnumber_lookup] input[type=text]').val() );
			}
			return false;
		});
	});
{/literal}
</script>
<!-- referent number look up block end -->