<?php
/*
Plugin Name: NoAd.pl Paypal Widget Plugin
Plugin URI: http://www.wpexplorer.com/
Description: A plugin that adds widget with options to display paypal.com account balance great to show how much % of goal is done you can use tags: [currency] [balance] [percent] and [goal]. 
Version: 1.0
Author: noadpl
Author URI: http://www.noad.pl/
License: GPL donations can be made to paypal: donations@noad.pl
*/

// paypal class start

class Paypal
{
    /**
     * API Version
     */
    const VERSION = 51.0;

    /**
     * List of valid API environments
     * @var array
     */
    private $allowedEnvs = array(
        'beta-sandbox',
        'live',
        'sandbox'
    );

    /**
     * Config storage from constructor
     * @var array
     */
    private $config = array();

    /**
     * URL storage based on environment
     * @var string
     */
    private $url;

    /**
     * Build PayPal API request
     * 
     * @param string $username
     * @param string $password
     * @param string $signature
     * @param string $environment
     */
    public function __construct($username, $password, $signature, $environment = 'live')
    {
        if (!in_array($environment, $this->allowedEnvs)) {
            throw new Exception('Specified environment is not allowed.');
        }
        $this->config = array(
            'username'    => $username,
            'password'    => $password,
            'signature'   => $signature,
            'environment' => $environment
        );
    }

    /**
     * Make a request to the PayPal API
     * 
     * @param  string $method API method (e.g. GetBalance)
     * @param  array  $params Additional fields to send in the request (e.g. array('RETURNALLCURRENCIES' => 1))
     * @return array
     */
    public function call($method, array $params = array())
    {
        $fields = $this->encodeFields(array_merge(
            array(
                'METHOD'    => $method,
                'VERSION'   => self::VERSION,
                'USER'      => $this->config['username'],
                'PWD'       => $this->config['password'],
                'SIGNATURE' => $this->config['signature']
            ),
            $params
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        if (!$response) {
            throw new Exception('Failed to contact PayPal API: ' . curl_error($ch) . ' (Error No. ' . curl_errno($ch) . ')');
        }
        curl_close($ch);
        parse_str($response, $result);
        return $this->decodeFields($result);
    }


    /**
     * Prepare fields for API
     * 
     * @param  array  $fields
     * @return array
     */
    private function encodeFields(array $fields)
    {
        return array_map('urlencode', $fields);
    }

    /**
     * Make response readable
     * 
     * @param  array  $fields
     * @return array
     */
    private function decodeFields(array $fields)
    {
        return array_map('urldecode', $fields);
    }

    /**
     * Get API url based on environment
     * 
     * @return string
     */
    private function getUrl()
    {
        if (is_null($this->url)) {
            switch ($this->config['environment']) {
                case 'sandbox':
                case 'beta-sandbox':
                    $this->url = "https://api-3t.$environment.paypal.com/nvp";
                    break;
                default:
                    $this->url = 'https://api-3t.paypal.com/nvp';
            }
        }
        return $this->url;
    }
}

// paypal API class end

class wp_my_plugin extends WP_Widget {

// constructor
    function wp_my_plugin() {
        parent::WP_Widget(false, $name = __('PayPal balance Widget', 'wp_widget_plugin') );
    }

// widget form creation
function form($instance) {

// Check values
if( $instance) {
     $title = esc_attr($instance['title']);
     $username = esc_attr($instance['username']);
     $password = esc_attr($instance['password']);
     $signature = esc_attr($instance['signature']);
     $goal = esc_attr($instance['goal']);
     $display = esc_textarea($instance['display']);
	 
} else {
	$title = '';
     $username = '';
     $password = '';
     $signature = '';
     $display = 'We have collected: [balance] [currency] of [goal] [currency]';
	 $goal = 100;
}
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('password'); ?>"><?php _e('Password:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('password'); ?>" name="<?php echo $this->get_field_name('password'); ?>" type="text" value="<?php echo $password; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('signature'); ?>"><?php _e('Signature:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('signature'); ?>" name="<?php echo $this->get_field_name('signature'); ?>" type="text" value="<?php echo  $signature; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('goal'); ?>"><?php _e('Goal:', 'wp_widget_plugin'); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('goal'); ?>" name="<?php echo $this->get_field_name('goal'); ?>" type="text" value="<?php echo  $goal; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display: use HTML and tags [balance] [currency] [percent] [goal]', 'wp_widget_plugin'); ?></label><br>
<textarea rows="5" class="widefat" id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>"><?php echo $display; ?></textarea>
</p>

<?php
}

// update widget
function update($new_instance, $old_instance) {
      $instance = $old_instance;
      // Fields
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['username'] = strip_tags($new_instance['username']);
      $instance['password'] = strip_tags($new_instance['password']);
      $instance['signature'] = strip_tags($new_instance['signature']);
      $instance['goal'] = strip_tags($new_instance['goal']);
      $instance['display'] = $new_instance['display'];
     return $instance;
}

// display widget
function widget($args, $instance) {
	   extract( $args );
	   // these are the widget options
	   $title = apply_filters('widget_title', $instance['title']);
	   $username = $instance['username'];
	   $password = $instance['password'];
	   $signature = $instance['signature'];
	   $goal = $instance['goal'];
	   $display = $instance['display'];
	   echo $before_widget;
	   // Display the widget
	   echo '<div class="widget-text wp_widget_plugin_box">';
	
	   // Check if title is set
	   if ( $title ) {
	      echo $before_title . $title . $after_title;
	   }

	$paypal = new Paypal($username, $password, $signature);
	
	$response = $paypal->call('GetBalance');

	$procent =  (($response['L_AMT0'] / $goal ) * 1);
	
	$procent = round($procent, 2);
	
	$balance = $response['L_AMT0'];
	$currency = $response['L_CURRENCYCODE0'];

	$display = str_replace("[currency]","$currency",$display);
	$display = str_replace("[balance]","$balance",$display);
	$display = str_replace("[percent]","$procent",$display);
	$display = str_replace("[goal]","$goal",$display);

/*	
echo ('Zebraliśmy już: '.[balance].' '.[currency]' z '.[goal].' '.[currency].'	   
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="'.[percent].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.[percent].'%">
    <span class="sr-only">'.[percent].'% Complete</span>
  </div>
</div>
<center><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PGG7L64MCXVMQ"><img src="" ></a></center> ';
*/
/*
Zebraliśmy już: [balance] [currency] z [goal] [currency] 
<div class=\"progress\">
  <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"[percent]\"
  aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width:[percent]%\">
    <span class=\"sr-only\">[percent]% Complete</span>
  </div>
</div>
<center><a href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PGG7L64MCXVMQ\"><img src=\"test.img\" ></a></center>
*/
/*
Zebraliśmy już: [balance] [currency] z [goal] [currency] 
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="[percent]"
  aria-valuemin="0" aria-valuemax="100" style="width:[percent]%">
    <span class="sr-only">[percent]% Complete</span>
  </div>
</div>
<center><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PGG7L64MCXVMQ"><img src="test.img" ></a></center>
*/

	   echo "$display";
	   echo '</div>';
	   echo $after_widget;
	  
	}
}
// register widget
add_action('widgets_init', create_function('', 'return register_widget("wp_my_plugin");'));

?>