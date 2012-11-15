<div class="wrap">
<div class="icon32 addthis" id="icon-edit"><br></div>
<h2>AddThis Social Sign In</h2>
<?php  
global $addthis_addjs;
echo $addthis_addjs->getAtPluginPromoText();
?>
<form method="post" action="options.php" class="addthis-ssi-form">

<?php wp_nonce_field('update-options'); ?>

<table class="form-table">
	
	<tr valign="top">		
		<td align="right" colspan="2">
			<label class="addthis-ssi-field-label">AddThis Profile ID : </label><input class="addthis-ssi-field" value="<?php global $addthis_addjs;echo $addthis_addjs->pubid;?>"type="text" id="" disabled="disabled"/>
		</td>
	</tr>

	<tr><td><label class="addthis-ssi-field-label">Facebook App ID : </label></td></tr>
	
	<tr valign="top">		
		<td>
			<input name="addthis_ssi_fbid" class="addthis-ssi-field" type="text" id="addthis_ssi_fbid" value="<?php echo get_option('addthis_ssi_fbid'); ?>" />
		</td>
	</tr>	
	<tr>		
		<td class="addthis-ssi-instructions">
		<a href="javascript:void(0);" id="fb-swap-ins">Instructions</a>
			<ol id="fb-instructions">
				<li>Login to your <a href="https://developers.facebook.com/apps" target="_blank">Facebook developer account</a>.</li>
				<li>Create a new project or select one that you've created before.</li>
				<li>Copy the project's "App ID/API key" and paste it into the field above.</li>
				<li>In the app settings page, fill in the field "Website with Facebook Login Site URL" to:<br/><label class="addthis-ssi-code">https://www.addthis.com/secure/ssi_callback</label></li>
			</ol>
		</td>
	</tr>
	
	<tr><td><label class="addthis-ssi-field-label">Twitter App Key : </label></td></tr>
	<tr valign="top">		
		<td>
			<input name="addthis_ssi_twkey" class="addthis-ssi-field" type="text" id="addthis_ssi_twkey" value="<?php echo get_option('addthis_ssi_twkey'); ?>" />
		</td>
	</tr>
	<tr>		
		<td class="addthis-ssi-instructions">
		<a href="javascript:void(0);" id="tw-swap-ins">Instructions</a>
			<ol id="tw-instructions">
				<li>Login to your <a href="https://dev.twitter.com/apps" target="_blank">Twitter developer account</a>.</li>
				<li>Create a new project or select one that you've created before.</li>
				<li>Copy the project's "Consumer key" and paste it into the field above.</li>
				<li>In the app settings page, set the "Callback URL" to:<br/><label class="addthis-ssi-code">https://www.addthis.com</label></li>
			</ol>
		</td>
	</tr>
	
	<tr><td><label class="addthis-ssi-field-label">Google Client ID : </label></td></tr>
	<tr valign="top">		
		<td>
			<input name="addthis_ssi_googleid" class="addthis-ssi-field" type="text" id="addthis_ssi_googleid" value="<?php echo get_option('addthis_ssi_googleid'); ?>" />
		</td>
	</tr>
	<tr>		
		<td class="addthis-ssi-instructions">
		<a href="javascript:void(0);" id="g-swap-ins">Instructions</a>
			<ol id="g-instructions">
				<li>Log in to your <a href="https://code.google.com/apis/console" target="_blank">Google developer account</a>.</li>
				<li>Create a new project or select one that you've created before.</li>
				<li>Go to the proejct's API Access page and copy the project's "Client ID" and paste it into the field above.</li>
				<li>
					On the API project settings page, set the "Authorized Redirect URIs" field to:<br/>
					<label class="addthis-ssi-code">
					https://www.addthis.com/secure/ssi_callback?isNewGen=false<br/>
					https://www.addthis.com/secure/ssi_callback?isNewGen=true
					</label>
				</li>
			</ol>
		</td>
	</tr>
	
	
	
	<tr><td>
	<hr class="addthis-ssi-line"></hr>
	<label class="addthis-ssi-field-label">User Role : </label></td></tr>
	<tr>
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
	
	<tr><td><input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_redirect_enabled" id="addthis_ssi_redirect_enabled" value="1" <?php echo get_option('addthis_ssi_redirect_enabled') ? 'checked="checked"' : ""; ?>/>&nbsp; <label class="addthis-ssi-field-label">Override default redirect URL : </label></td></tr>
	<tr valign="top">		
		<td>
			<input name="addthis_ssi_redirect_url" class="addthis-ssi-field" type="text" id="addthis_ssi_redirect_url" value="<?php echo get_option('addthis_ssi_redirect_url'); ?>" />
			<label class="addthis-ssi-instructions">( Change post login redirect URL to your own. )</label>
		</td>
	</tr>
	
	<tr>
		<td colspan="2"><input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_welcome_enabled" id="addthis_ssi_welcome_enabled" value="1" <?php echo get_option('addthis_ssi_welcome_enabled') ? 'checked="checked"' : ""; ?>/>&nbsp; <label class="addthis-ssi-field-label">Enable welcome message</label>
		<label class="addthis-ssi-instructions">( Show welcome user message after login if tag method is used. )</label>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" class="addthis-ssi-checkbox" name="addthis_ssi_thumbnail_enabled" id="addthis_ssi_thumbnail_enabled" value="1" <?php echo get_option('addthis_ssi_thumbnail_enabled') ? 'checked="checked"' : ""; ?>/>&nbsp; <label class="addthis-ssi-field-label">Use avatar of connected accounts</label>
		<label class="addthis-ssi-instructions">( Profile picture of connected accounts will be used as avatar. )</label>
		</td>
	</tr>
	
	<tr>
		<td>
		<input type="hidden" name="action" value="update"/>
		<input type="hidden" name="page_options" value="addthis_ssi_pubid, addthis_ssi_fbid, addthis_ssi_twkey, addthis_ssi_googleid, addthis_default_user_role, addthis_ssi_redirect_enabled, addthis_ssi_redirect_url, addthis_ssi_welcome_enabled, addthis_ssi_thumbnail_enabled"/>

		<p class="submit">
			<input type="submit" value="Save Changes" class="button-primary">
		</p>
		</td>
	</tr>
	
	<tr>
		<td class="addthis-ssi-instructions"><label class="addthis-ssi-field-label">Important : </label>We’ll automatically add login buttons to the standard Wordpress screen, but if you’d like to add buttons elsewhere on your theme, <br/>copy and paste the tag <label class="addthis-ssi-field-label"><i>&lt;?php addthis_ssi();?&gt;</i></label> into your templates. Wherever you add them, a login button will appear</td>
	</tr>		
</table>

</form>

</div>
