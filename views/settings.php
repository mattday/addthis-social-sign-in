<div class="wrap">
<div class="icon32 addthis" id="icon-edit"><br></div>
<h2>AddThis Social Sign In</h2>
<?php  
global $addthis_addjs;
echo $addthis_addjs->getAtPluginPromoText();
?>
<form method="post" action="options.php" class="addthis-ssi-form">

<?php wp_nonce_field('update-options'); ?>

<table class="form-table" border="0">	
	<tr valign="top">		
		<td align="right" colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td class="addthis-fld-td"><label class="addthis-ssi-field-label">Facebook App ID : </label>
		<br/>
		<label class="addthis-ssi-instructions"><a href="javascript:void(0);" id="fb-swap-ins" >Instructions</a></label></td>		
		<td>
			<input name="addthis_ssi_fbid" class="addthis-ssi-field" type="text" id="addthis_ssi_fbid" value="<?php echo get_option('addthis_ssi_fbid'); ?>" />
			<span class="addthis-pubid-spn"><label class="addthis-ssi-field-label">AddThis Profile ID : </label><input class="addthis-ssi-field" value="<?php global $addthis_addjs;echo $addthis_addjs->pubid;?>"type="text" id="" disabled="disabled"/></span>
		</td>
	</tr>	
	<tr>		
		<td class="addthis-ssi-instructions" colspan="2">		
			<ol id="fb-instructions">
				<li>Login to your <a href="https://developers.facebook.com/apps" target="_blank">Facebook developer account</a>.</li>
				<li>Create a new project or select one that you've created before.</li>
				<li>Copy the project's "App ID/API key" and paste it into the field above.</li>
				<li>In the app settings page, fill in the field "Website with Facebook Login Site URL" to:<br/><label class="addthis-ssi-code">https://www.addthis.com/secure/ssi_callback</label></li>
			</ol>
		</td>
	</tr>
	<tr>
		<td><label class="addthis-ssi-field-label">Twitter Consumer Key : </label>
			<br/>
			<label class="addthis-ssi-field-label">Twitter Consumer Secret : </label>
			<br/>
			<label class="addthis-ssi-instructions"><a href="javascript:void(0);" id="tw-swap-ins">Instructions</a></label>
		</td>	
		<td>
		<?php if( extension_loaded('curl') ) : ?>
			<input name="addthis_ssi_twkey" class="addthis-ssi-field" type="text" id="addthis_ssi_twkey" value="<?php echo get_option('addthis_ssi_twkey'); ?>" />
			<br/>
			<input name="addthis_ssi_tw_secret" class="addthis-ssi-field" type="text" id="addthis_ssi_tw_secret" value="<?php echo get_option('addthis_ssi_tw_secret'); ?>" />
		<?php else :?>
		<label class="addthis-ssi-instructions">Please enable cURL to use Twitter</label>
		<?php endif;?>	
		</td>
	</tr>		
	<tr>		
		<td class="addthis-ssi-instructions" colspan="2">		
			<ol id="tw-instructions">
				<li>Login to your <a href="https://dev.twitter.com/apps" target="_blank">Twitter developer account</a>.</li>
				<li>Create a new project or select one that you've created before.</li>
				<li>Copy the project's "Consumer key" and "Consumer Secret (will not be exposed on frontend)" and paste it into the fields above.</li>
				<li>In the app settings page, set the "Callback URL" to:<br/><label class="addthis-ssi-code">https://www.addthis.com</label></li>
			</ol>
		</td>
	</tr>	
	<tr>
		<td><label class="addthis-ssi-field-label">Google Client ID : </label>
		<br/>
		<label class="addthis-ssi-instructions"><a href="javascript:void(0);" id="g-swap-ins">Instructions</a></label>
		</td>	
		<td>
			<input name="addthis_ssi_googleid" class="addthis-ssi-field" type="text" id="addthis_ssi_googleid" value="<?php echo get_option('addthis_ssi_googleid'); ?>" />
		</td>
	</tr>
	<tr>		
		<td class="addthis-ssi-instructions" colspan="2">		
			<ol id="g-instructions">
				<li>Log in to your <a href="https://code.google.com/apis/console" target="_blank">Google developer account</a>.</li>
				<li>Create a new project or select one that you've created before.</li>
				<li>Select API Access tab on left menu</li>
				<li>
					If you created a new project, fill out the basic details and make sure to specify "Web application" for Application Type and then click "more options"<br/> next to "Your site or hostname" and set "Authorized Redirect URIs" to:<br/>
					<label class="addthis-ssi-code">
					https://www.addthis.com/secure/ssi_callback?isNewGen=false<br/>
					https://www.addthis.com/secure/ssi_callback?isNewGen=true
					</label>
				</li>
				<li>If you have an existing project, find the section "Client ID for web applications".</li>
				<li>
					Click "Edit settings..." and set the "Authorized Redirect URIs" field to:<br/>
					<label class="addthis-ssi-code">
					https://www.addthis.com/secure/ssi_callback?isNewGen=false<br/>
					https://www.addthis.com/secure/ssi_callback?isNewGen=true
					</label>
				</li>
				<li>Finally, copy the "Client ID" to the field above.</li>				
			</ol>
		</td>
	</tr>	
	<tr><td><label class="addthis-ssi-field-label">Linkedin API Key : </label>
			<br/>
			<label class="addthis-ssi-field-label">Linkedin Secret Key : </label>
			<br/>
			<label class="addthis-ssi-instructions"><a href="javascript:void(0);" id="ln-swap-ins">Instructions</a></label>
		</td>	
		<td>
		<?php if( extension_loaded('curl') ) : ?>
			<input name="addthis_ssi_linkedin_key" class="addthis-ssi-field" type="text" id="addthis_ssi_ln_apikey" value="<?php echo get_option('addthis_ssi_linkedin_key'); ?>" />
			<br/>
			<input name="addthis_ssi_linkedin_secret" class="addthis-ssi-field" type="text" id="addthis_ssi_ln_secretkey" value="<?php echo get_option('addthis_ssi_linkedin_secret'); ?>" />
		<?php else :?>
		<label class="addthis-ssi-instructions">Please enable cURL to use Linkedin</label>
		<?php endif;?>	
		</td>
	</tr>
	<tr>		
		<td class="addthis-ssi-instructions" colspan="2">
		
			<ol id="ln-instructions">
				<li>Login to your <a href="https://www.linkedin.com/secure/developer" target="_blank">Linkedin developer account</a>.</li>
				<li>Create a new application or select one that you've created before.</li>
				<li>Copy the application's "API Key" and "Secret Key (will not be exposed on frontend)" and paste it into the fields above.</li>
			</ol>
		</td>
	</tr>	
	<tr><td colspan="2"><label class="addthis-ssi-field-label">Yahoo! : </label>
	<?php if( extension_loaded('curl') ) : ?>
		<input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_yahoo_enabled" id="addthis_ssi_yahoo_enabled" value="1" <?php echo get_option('addthis_ssi_yahoo_enabled') ? 'checked="checked"' : ""; ?>/>
	<?php else :?>
		<label class="addthis-ssi-instructions">Please enable cURL to use Yahoo</label>
	<?php endif;?>	
	</td></tr>
	
	<tr><td colspan="2"><hr class="addthis-ssi-line"></hr></td></tr>
	<tr><td>	
	<label class="addthis-ssi-field-label">User Role : </label></td>
		<td>
			<select name="addthis_default_user_role" class="addthis-ssi-field">
			<option value="">Default</option>
			<?php 
				$roles = array_reverse( get_role_names() );				
				foreach( $roles as $role => $role_title ) {
					echo '<option '.((get_option('addthis_default_user_role') == $role) ? 'selected="selected"' : "").' value="'.$role.'">'.$role_title.'</option>';
				}			
			?>
			</select>
		</td>
	</tr>	
	<tr><td><label class="addthis-ssi-field-label">Override default redirect URL : </label></td>
	<td>
			<input name="addthis_ssi_redirect_url" class="addthis-ssi-field" type="text" id="addthis_ssi_redirect_url" value="<?php echo get_option('addthis_ssi_redirect_url'); ?>" />
	</td>
	</tr>
	<tr><td colspan="2"><label class="addthis-ssi-instructions">( Change post login redirect URL to your own. )</label></td></tr>
	<tr>
		<td colspan="2"><label class="addthis-ssi-field-label">Enable welcome message</label>&nbsp;<input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_welcome_enabled" id="addthis_ssi_welcome_enabled" value="1" <?php echo get_option('addthis_ssi_welcome_enabled') ? 'checked="checked"' : ""; ?>/> 
		</td>
	</tr>
	<tr><td colspan="2"><label class="addthis-ssi-instructions">( Show welcome user message after login if tag method is used. )</label></td></tr>
	<tr>
		<td colspan="2"><label class="addthis-ssi-field-label">Use avatar of connected accounts</label>&nbsp;<input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_thumbnail_enabled" id="addthis_ssi_thumbnail_enabled" value="1" <?php echo get_option('addthis_ssi_thumbnail_enabled') ? 'checked="checked"' : ""; ?>/>
		</td>
	</tr>
	<tr><td colspan="2"><label class="addthis-ssi-instructions">( Profile picture of connected accounts will be used as avatar. )</label></td></tr>
	<tr><td><label class="addthis-ssi-field-label">Button Label : </label></td>
	<td>
			<input name="addthis_ssi_button_text" class="addthis-ssi-field" type="text" id="addthis_ssi_button_text" value="<?php echo htmlentities( get_option('addthis_ssi_button_text') ); ?>" style="width:365px"/>
	</td>
	</tr>
	<tr>
		<td colspan="2"><label class="addthis-ssi-field-label">Enable login popup</label>&nbsp;<input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_popup_enabled" id="addthis_ssi_popup_enabled" value="1" <?php echo get_option('addthis_ssi_popup_enabled') ? 'checked="checked"' : ""; ?>/>
		</td>
	</tr>
	<tr><td colspan="2"><label class="addthis-ssi-instructions">( Require user to provide an email address if we're not provided with one during authentication (i.e. Twitter/LinkedIn do not provide an email address) )</label></td></tr>
	<tr>
		<td><label class="addthis-ssi-field-label">Notice Text</label></td>
		<td>
			<input name="addthis_ssi_email_exist_text" class="addthis-ssi-field" type="text" id="addthis_ssi_email_exist_text" value="<?php echo htmlentities( get_option('addthis_ssi_email_exist_text') ); ?>" style="width:600px"/>
		</td>
	</tr>
	<tr><td colspan="2"><label class="addthis-ssi-instructions">( Notice showing to the users when email address is already registered. )</label></td></tr>
	<tr>
		<td colspan="2">
		<input type="hidden" name="action" value="update"/>
		<input type="hidden" name="page_options" value="addthis_ssi_fbid, addthis_ssi_twkey, addthis_ssi_tw_secret, addthis_ssi_googleid, addthis_ssi_linkedin_key, addthis_ssi_linkedin_secret, addthis_ssi_yahoo_enabled, addthis_default_user_role, addthis_ssi_redirect_url, addthis_ssi_welcome_enabled, addthis_ssi_thumbnail_enabled, addthis_ssi_button_text, addthis_ssi_popup_enabled, addthis_ssi_email_exist_text"/>

		<p class="submit">
			<input type="submit" value="Save Changes" class="button-primary">
		</p>
		</td>
	</tr>	
	<tr>
		<td class="addthis-ssi-instructions" colspan="2"><label class="addthis-ssi-field-label">Important : </label>We’ll automatically add login buttons to the standard Wordpress screen, but if you’d like to add buttons elsewhere on your theme, <br/>copy and paste the tag <label class="addthis-ssi-field-label"><i>&lt;?php addthis_ssi();?&gt;</i></label> into your templates. Wherever you add them, a login button will appear</td>
	</tr>		
</table>
</form>
</div>
<script type="text/javascript">

(function($){
	$(document).ready(function() {
		
		$(".addthis-ssi-form").submit(function(){
			if( $("#addthis_ssi_redirect_url").val() != "" ) {	
				var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
			
				if(!pattern.test($("#addthis_ssi_redirect_url").val())){
					$("#addthis_ssi_redirect_url").css('border-color','#CC0000');
					$("#addthis_ssi_redirect_url").focus();
					return false;
				} else {
					$("#addthis_ssi_redirect_url").css('border-color','');
				}
			}		 
        });
	});
})(jQuery);

</script>