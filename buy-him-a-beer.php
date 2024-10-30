<?php
/*
Plugin Name: Buy Him A Beer
Plugin URI: http://wordpress.org/plugins/buy-him-a-beer/
Description: This plugin allows users to add a "Buy Him a Beer" button to their website
Version: 1.01
Author: Mark Richmond
Author URI: http://markjrichmond.com
License: GPL2
*/

function bhab_add_external() {
	wp_register_style('bhab-style', plugins_url('style.css', __FILE__));
	wp_enqueue_style('bhab-style');
}
add_action('wp_enqueue_scripts', 'bhab_add_external');

function bhab_add_admin() {
	wp_register_style('bhab-style', plugins_url('style.css', __FILE__));
	wp_enqueue_style('bhab-style');
	wp_register_script('bhab-main', plugins_url('admin.js', __FILE__));
	wp_enqueue_script('bhab-main');
}
add_action('admin_init','bhab_add_admin');


class BHAB_Widget extends WP_Widget {
	const TYPE_LINK = 0;
	const TYPE_BUTTON = 1;

	// Register widget with WordPress.
	function __construct() {
		parent::__construct(
			'BHAB_widget', // Base ID
			'Buy Him a Beer', // Name
			array( 'description' => __( 'A link for visitors to buy you beer', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$name = (isset($instance['name'])) ? $instance['name'] : '';
		$email = (isset($instance['email'])) ? $instance['email'] : '';
		$type = (isset($instance['type'])) ? $instance['type'] : self::TYPE_BUTTON;	
		$button_color = (isset($instance['button_color'])) ? $instance['button_color'] : 'primary';	

		echo $args['before_widget'];
		echo $args['before_title'] . 'Beer' . $args['after_title'];
		
		$url = 'http://buyhimabeer.com/buy?name=' . urlencode($name) . '&email=' . urlencode($email);	

		if ($type == self::TYPE_LINK) {
			echo '<a target="_blank" href="' . $url . '">Buy ' . $name . ' a Beer</a>';
		}
		else if ($type == self::TYPE_BUTTON) {
			echo '<a target="_blank" href="' . $url . '">';
			echo '<button class="bhab-btn bhab-btn-' . $button_color . '">';
			echo '<div class="bhab-btn-icon"></div>';
			echo '<p class="bhab-btn-text">Buy ' . $name . ' a Beer</p>';
			echo '</button></a>';
		}

		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$name = (isset($instance['name'])) ? $instance['name'] : '';
		$email = (isset($instance['email'])) ? $instance['email'] : '';
		$type = (isset($instance['type'])) ? $instance['type'] : self::TYPE_BUTTON;
		$button_color = (isset($instance['button_color'])) ? $instance['button_color'] : 'primary';
		?>

		<p>
		<label for="<?php echo $this->get_field_name('name'); ?>"><?php _e('Display Name:'); ?></label> 
		<input onkeyup="bhab_build_preview(this)" class="widefat" id="<?php echo $this->get_field_id('name'); ?>" 
		name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo esc_attr($name); ?>" />
		</p>
		<p>	
		<label for="<?php echo $this->get_field_name('email'); ?>"><?php _e('Paypal Email:'); ?></label> 
		<input onkeyup="bhab_build_preview(this)" class="widefat" id="<?php echo $this->get_field_id('email'); ?>" 
		name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo esc_attr($email); ?>" />
		</p>
		
		<p>
		<input type="radio" class="radio" name="<?php echo $this->get_field_name('type'); ?>" 
		value="<?php echo self::TYPE_LINK; ?>" onchange="bhab_change_type(this, 'link')" 
		<?php if ($type == self::TYPE_LINK) echo "checked"; ?>/>
		<label for="<?php echo $this->get_field_name('type'); ?>"><?php _e('Link'); ?></label> 
		<input type="radio" class="radio" name="<?php echo $this->get_field_name('type'); ?>" 
		value="<?php echo self::TYPE_BUTTON; ?>" onchange="bhab_change_type(this, 'button')" 
		<?php if ($type == self::TYPE_BUTTON) echo "checked"; ?>/>
		<label for="<?php echo $this->get_field_name('type'); ?>"><?php _e('Button'); ?></label> 
		</p>

		<p <?php if ($type != self::TYPE_BUTTON) echo 'style="display: none;"'; ?>>
		<label for="<?php echo $this->get_field_name('button_color'); ?>"><?php _e('Button Color'); ?></label> 
		<select onchange="bhab_build_preview(this)" class="select" name="<?php echo $this->get_field_name('button_color'); ?>">
			<option value="primary" <?php if ($button_color == 'primary') echo 'selected'; ?>>Blue</option>
			<option value="info" <?php if ($button_color == 'info') echo 'selected'; ?>>Light Blue</option>
			<option value="success" <?php if ($button_color == 'success') echo 'selected'; ?>>Green</option>
			<option value="warning" <?php if ($button_color == 'warning') echo 'selected'; ?>>Yellow</option>
			<option value="danger" <?php if ($button_color == 'danger') echo 'selected'; ?>>Red</option>
			<option value="inverse" <?php if ($button_color == 'inverse') echo 'selected'; ?>>Black</option>	
		</select>
		</p>

		<div class="bhab-preview-wrapper">
			<p class="bhab-preview-title">Preview</p>
			<div class="bhab-preview"></div>		
		</div>
		
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['name'] = (!empty($new_instance['name'])) ? strip_tags($new_instance['name']) : '';
		$instance['email'] = (!empty($new_instance['email'])) ? strip_tags($new_instance['email']) : '';
		$instance['type'] = (!empty($new_instance['type'])) ? strip_tags($new_instance['type']) : '';
		$instance['button_color'] = (!empty($new_instance['button_color'])) ? strip_tags($new_instance['button_color']) : '';
		
		return $instance;
	}

}


// register widget
function register_bhab_widget() {
    register_widget('BHAB_Widget');
}
add_action('widgets_init', 'register_bhab_widget');

?>
