<?php
/*
Plugin Name: Zupot - Atendimento Online
Plugin URI: http://www.zupot.com/
Description: Permite adicionar atendimento online via chat e por ticket em seu site WordPress de forma simples.
Version: 1.0
Author: Zupot Apps
Author URI: http://www.zupot.com/
*/


register_activation_hook(__FILE__, 'zupot_activation');
register_deactivation_hook(__FILE__, 'zupot_deactivation');

    if (is_admin()) {
        add_action( 'admin_init', 'zupot_api_settings' );
		add_action('admin_menu',  'zupot_admin_menu');
    }

function zupot_admin_menu() {
	add_options_page('Zupot', 'Zupot', 8,'wordpresszupot', 'zupot_options_page');
}

function zupot_options_page() {
	echo'
	
	<script type="text/javascript" src="http://login.zupot.com/js/colorpicker/jscolor.js"></script>
	<script type="text/javascript" src="http://login.zupot.com/js/slidebar/slideControl.js"></script>
	
	<script>
			function updatepreview(movechatvalue){
				document.getElementById("chattab").style.right=movechatvalue+"%";
				document.getElementById("previewchattab").style.right=movechatvalue+"%";
			}
	</script>


	<div id="previewchattab" style="position: fixed; right:6%; bottom: 4%; _position: absolute; _top: expression(eval(document.compatMode && document.compatMode=="CSS1Compat")? documentElement.scrollTop +(documentElement.clientHeight-this.clientHeight):document.body.scrollTop +(document.body.clientHeight-this.clientHeight)); height:34px; width:190px; ">
	<img style="position:relative;" src="http://www.zupot.com/wp-content/uploads/2013/04/previewbtn.png"/>
	</div>

	<div id="chattab" style="border-top-left-radius: 10px; border-top-right-radius: 10px; background-color:#2CD32C; position: fixed; right:5%; bottom: 0; _position: absolute; _top: expression(eval(document.compatMode && document.compatMode=="CSS1Compat")? documentElement.scrollTop +(documentElement.clientHeight-this.clientHeight):document.body.scrollTop +(document.body.clientHeight-this.clientHeight)); height:34px; ">
	<a style="text-decoration: none;" target="_blank">
	<img style="border:none; position:relative; left:3%; top:-8%;" border="0" align="left" src="http://www.zupot.com/wp-content/uploads/2013/04/chat-bubble.png";/>
	<img src="http://www.zupot.com/zc/teste/genbtn.php?i=btnbottomcustom&amp;lang=en" style="border:none; left:-3%; position:relative; top:5px;" border="0" align="left"/>	</a>
</div>
	
		
	<div class="wrap">
		<h2>Configura&ccedil;&atilde;o Zupot</h2><br/>
		<img src="http://www.zupot.com/wp-content/uploads/2013/04/logozupotcolor.png"/><br/>
		Crie sua conta gratuita <a target="_blank" href="http://www.zupot.com">clicando aqui</a><br/>
        	<p>Para encontrar seu c&oacute;digo de cliente e chave, fa&ccedil;a o login no Painel de Controle da Zupot e depois v&aacute; em "C&oacute;digo do Bot&atilde;o"</p>
		<div style="color:Red;display:none" id="zupot_div">Wrong zupot ID</div>
		<form method="post" action="options.php" onsubmit="return checkzupotId()">';
			wp_nonce_field('update-options');
			 echo '
			<table class="form-table">
				<tr valign="top">';
				$settings = zupot_settings_list();
				foreach ($settings as $setting) {
					echo '<th scope="row">'.$setting['display'].'</th>
					<td>';
					if ($setting['type']=='selectbox') {
						$str = explode(",",$setting['option']);
						echo '<select name="'.$setting['name'].'" >';
						for($i=0;$i<count($str);$i++)
						{	
							$selected="";
							if(get_option($setting['name'])==$str[$i])
								$selected='selected';
							echo "<option value='".$str[$i]."' ".$selected." >".$str[$i]."</option>";
						}
						echo '</select>';
					}
					else if ($setting['type']=='slider') {
						echo '<input _type="slider" name="'.$setting['name'].'" id="'.$setting['id'].'" value="'.get_option($setting['name']).'" minimum="0" maximum="100" onslide="updatepreview(value);"/>';
					}
					
					else if ($setting['type']=='radio') {
						echo 'Sim <input type="'.$setting['type'].'" name="'.$setting['name'].'" value="1" ';
						if (get_option($setting['name'])==1) { echo 'checked="checked" />'; } else { echo ' />'; }
						echo 'N&atilde;o <input type="'.$setting['type'].'" name="'.$setting['name'].'" value="0" ';
						if (get_option($setting['name'])==0) { echo 'checked="checked" />'; } else { echo ' />'; }
					} else { echo '<input type="'.$setting['type'].'" name="'.$setting['name'].'"  onchange="'.$setting['onchange'].'"  class="'.$setting['class'].'" id="'.$setting['id'].'" value="'.get_option($setting['name']).'" />'; }
					echo ' (<em>'.$setting['hint'].'</em>)</td></tr>';
				}
			
			echo '</table>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="';
			foreach ($settings as $setting) {
				echo $setting['name'].',';
			}
			echo '" /><p class="submit"><input type="submit" class="button-primary" value="Salvar" /></p>
		</form>';
	echo '</div>';
}

function zupot_settings_list() {
	$settings = array(
		array(
			'display' => 'Passo 1: ',
			'name'    => 'zupot_field_c',
			'id'      => 'zupot_field_c',
			'value'   => '',
			'type'    => 'textbox',
            'hint'    => 'Digite o C&oacutedigo de Cliente'
		),
		array(
			'display' => 'Passo 2: ',
			'name'    => 'zupot_field_k',
			'id'      => 'zupot_field_k',
			'value'   => '',
			'type'    => 'textbox',
            'hint'    => 'Digite sua chave'
		),
		array(
			'display' => 'Passo 3: ',
			'name'    => 'colorfield',
			'id'      => 'colorfield',
			'class'   => 'color {pickerPosition:\'top\'}',
			'onchange'   => 'document.getElementById(\'chattab\').style.backgroundColor=\'#\'+this.color;',
			'value'   => '2CD32C',
			'type'    => 'textbox',
            'hint'    => 'Escolha a cor do bot&atilde;o'
		),
		array(
			'display' => 'Passo 4: ',
			'name'    => 'dirSlider',
			'id'      => 'dirSlider',
			'value'   => '5',
			'type'    => 'slider',
            'hint'    => 'Escolha a posi&ccedil;&atilde;o do bot&atilde;o'
		),
		array(
			'display' => 'Habilitado',
			'name'    => 'zupot_show_code',
			'value'   => '0',
			'type'    => 'radio',
            'hint'    => 'Mostrar ou n&atilde;o o bot&atilde;o online'
		),
	);
	return $settings;
}

function zupot_api_settings() {
	$settings = zupot_settings_list();
	foreach ($settings as $setting) {
		register_setting($setting['name'], $setting['value']);
	}
}

function zupot_activation() {
	$settings = zupot_settings_list();
	foreach ($settings as $setting) {
		update_option($setting['name'], $setting['value']);
	}
}

function zupot_deactivation() {
	$settings = zupot_settings_list();
	foreach ($settings as $setting) {
		delete_option($setting['name']);
	}
}

add_filter( 'page_template', 'zupot_redirect_template' );
function zupot_redirect_template($template) {
	//$templates = array('zupot-redirect.php');
	//$template = locate_plugin_template($templates);
	return $template;
}


add_action('wp_footer', 'zupot_wp_footer');
function zupot_wp_footer() {
    $is_show = get_option('zupot_show_code');
    if($is_show==1)
	{
		$buffer = '<script src="http://login.zupot.com/b.php?t=2&l=pt&c='.get_option('zupot_field_c').'&k='.get_option('zupot_field_k').'&x='.get_option('colorfield').'&p='.get_option('dirSlider').'"></script>';
		ob_start();
		eval('?>' . $buffer);
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}
}