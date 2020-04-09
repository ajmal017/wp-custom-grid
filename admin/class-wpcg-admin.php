<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class WP_Custom_Grid_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Custom_Grid_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Custom_Grid_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpcg-admin.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( 'gridstack', plugin_dir_url( __FILE__ ) . 'css/gridstack.min.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( 'gridstyle', plugin_dir_url( __FILE__ ) . 'css/grid-style.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Custom_Grid_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Custom_Grid_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpcg-admin.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'gridstackjs', plugin_dir_url( __FILE__ ) . 'js/gridstack.all.js', array( 'jquery' ), $this->version, false );
	}



}

//WP List Table Class extended
	if ( ! class_exists( 'WP_List_Table_Modified' ) ) {
		//Plugin dir name 
		//https://wordpress.stackexchange.com/questions/185519/how-to-get-the-current-plugin-name/239497
		$plugin =  plugin_dir_path(  dirname( __FILE__ , 1 ) ) ;

		require_once( $plugin.'/includes/class-wp-list-table-modified.php' );
	}

class Grid_List extends WP_List_Table_Modified {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Grid', 'wpcg' ), //singular name of the listed records
			'plural'   => __( 'Gridss', 'wpcg' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

	}
	
	public static function get_customers( $per_page = 5, $page_number = 1 ) {

	  global $wpdb;

	  $sql = "SELECT * FROM {$wpdb->prefix}wcg_table";

	  if ( ! empty( $_REQUEST['orderby'] ) ) {
		$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	  }

	  $sql .= " LIMIT $per_page";

	  $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );

	  return $result;
	}
	
	public static function delete_customer( $id ) {
	  global $wpdb;

	  $err = $wpdb->delete(
		"{$wpdb->prefix}wcg_table",
		[ 'g_id' => $id ],
		[ '%d' ]
	  );
	
	}
	
	public static function record_count() {
	  global $wpdb;

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}wcg_table";

	  return $wpdb->get_var( $sql );
	}
	
	public function no_items() {
	  _e( 'No grids created.', 'wpcg' );
	}
	
	function column_name( $item ) {

	  // create a nonce
	  $delete_nonce = wp_create_nonce( 'wpcg_delete_grid' );

	  $title = '<strong>' . $item['g_name'] . '</strong>';

	  $actions = [
		'delete' => sprintf( '<a href="?page=%s&action=%s&grid=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['g_id'] ), $delete_nonce )
	  ];

	  return $title . $this->row_actions( $actions );
	}
	
	public function column_default( $item, $column_name ) {
	  switch ( $column_name ) {
		case 'g_id':
		case 'g_name':
		case 'g_layout':
		case 'g_layout_posts':
		  return $item[ $column_name ];
		default:
		  return print_r( $item, true ); //Show the whole array for troubleshooting purposes
	  }
	}

	function column_cb( $item ) {
	  return sprintf(
		'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['g_id']
	  );
	}
	
	function get_columns() {
	  $columns = [
		'cb'      => '<input type="checkbox" />',
		'g_id'	=> __('ID', 'sp'), 
		'g_name'    => __( 'Name', 'sp' ),
		'g_layout' => __( 'Layout', 'sp' ),
		'g_layout_posts'    => __( 'Posts', 'sp' )
	  ];

	  return $columns;
	}
	
	public function get_sortable_columns() {
	  $sortable_columns = array(
		'g_id' => array( 'g_id', true ),
		'g_name' => array( 'g_name', true ),
		'g_layout' => array( 'g_layout', false )
	  );

	  return $sortable_columns;
	}
	
	public function get_bulk_actions() {
	  $actions = [
		'bulk-delete' => 'Delete'
	  ];

	  return $actions;
	}
	
	public function prepare_items() {

	  $this->_column_headers = $this->get_column_info();

	  /** Process bulk action */
	  $this->process_bulk_action();

	  $per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();

	  $this->set_pagination_args( [
		'total_items' => $total_items, //WE have to calculate the total number of items
		'per_page'    => $per_page //WE have to determine how many items to show on a page
	  ] );


	  $this->items = self::get_customers( $per_page, $current_page );
	}
	
	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {

		// In our file that handles the request, verify the nonce.
		$nonce = esc_attr( $_REQUEST['_wpnonce'] );

		if ( ! wp_verify_nonce( $nonce, 'wpcg_delete_grid' ) ) {
		  die( 'Go get a life script kiddies' );
		}
		else {
			

		  self::delete_customer( absint( $_GET['g_id'] ) );

		  wp_redirect( esc_url( add_query_arg() ) );
		  exit;
		}

	  }

	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		   || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {

		$delete_ids = esc_sql( $_POST['bulk-delete'] );


		// loop over the array of record IDs and delete them
		foreach ( $delete_ids as $id ) {
			self::delete_customer( $id );

		}

		wp_redirect( esc_url( add_query_arg([]) ) );

		exit;
	  }
	}
	
	
}//class ends

class WPCG_Plugin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $grids_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
		add_action( 'admin_menu', [ $this, 'plugin_sub_menu' ] );
	}


public static function set_screen( $status, $option, $value ) {
	return $value;
}

public function plugin_menu() {

	$hook = add_menu_page(
		'WP Custom Grids',
		'WPCG Options',
		'manage_options',
		'wpcg_options.php',
		[ $this, 'plugin_settings_page' ]
	);

	add_action( "load-$hook", [ $this, 'screen_option' ] );

}

public function plugin_sub_menu() {

//For the submenu to work, we have to duplicate the top level menu and then add a submenu 
//https://developer.wordpress.org/reference/functions/add_submenu_page/#comment-446
add_submenu_page( 'wpcg_options.php', 'Grids', 'Grids',
    'manage_options', 'wpcg_options.php');
$hook1 = add_submenu_page( 
	'wpcg_options.php', 
	'Edit Grid', 
	'Edit Grid',
    'manage_options', 
	'edit-grid.php',
	[ $this, 'edit_grid_page' ]
);
add_action( "load-$hook1", [ $this, 'screen_option' ] );

}
	

public function screen_option() {

	$option = 'per_page';
	$args   = [
		'label'   => 'Grids',
		'default' => 5,
		'option'  => 'grid_per_page'
	];

	add_screen_option( $option, $args );

	$this->grids_obj = new Grid_List();
}
	
public function plugin_settings_page() {
	?>
	<div class="wrap">
		<h2>Grids</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							$this->grids_obj->prepare_items();
							$this->grids_obj->display(); ?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
<?php
}
	
public function edit_grid_page() {
	

	if(isset($_POST['savelayout'])){

		$gname = $_POST['layoutname'];
		$gdesc = $_POST['layoutdesc'];
		$glayout = " ' ".$_POST['layout']." ' ";
		
		
	global $wpdb;
	$table = $wpdb->prefix.'wcg_table';
	$data = array('g_name' => $gname, 'g_desc' => $gdesc, 'g_layout_posts' => $glayout );
	$format = array('%s','%s','%s');
	$wpdb->insert($table,$data);
	$my_id = $wpdb->insert_id;

	echo $my_id ."<br>";
	
		if(isset($my_id)){
	$layouts = $wpdb->get_row( "SELECT * FROM $table WHERE g_id = ".$my_id );
	print_r($layouts);

	$title = $layouts->g_name;
	$desc = $layouts->g_desc;
	$layout = $layouts->g_layout_posts;
		
	echo $title ."<br>".$desc ." <br>".$layout ;			
		}

	

	}elseif (isset($_GET['l_id'])){
	global $wpdb;
	$table = $wpdb->prefix.'wcg_table';
	$my_id = $_GET['l_id'];
	$layouts = $wpdb->get_row( "SELECT * FROM $table WHERE g_id = ".$my_id );

	$title = $layouts->g_name;
	$desc = $layouts->g_desc;
	$layout = $layouts->g_layout_posts;		
		
	}
	?>
	<div class="wrap">
		<h2>Edit Grids</h2>

	<form action=" " method="post">
		<input type="hidden" value="savelayout" name="savelayout"><br>
		<textarea id="saved-data" cols="0" rows="0" name="layout"><?php if(isset($layout)) echo stripslashes($layout); ?></textarea>	<br>
		<button type="submit">Save Layout</button><br>
		<label for="layoutname">Title</label><br>
		<input type="text" name="layoutname" id="layoutname" value="<?php if(isset($title)) echo $title ; ?>"><br>
		<label for="layoutdesc">Description</label><br>
		<textarea cols="50" rows="3" name="layoutdesc" id="layoutdesc"><?php if(isset($desc)) echo $desc ; ?></textarea>
	</form>
<div class="row">
<?php 
	
	$args = array(
  'numberposts' => 12,
		'orderby' => 'ID',
		'order' => 'DESC'
);
 
$latest_posts = get_posts( $args );
?>
	<div class="wpcg-wrap">
<?php
foreach($latest_posts as $post){
	$bg = get_the_post_thumbnail_url($post->ID);
	?>
	<div  data-pid="<?php echo $post->ID; ?>" class="text-center text-white newWidget grid-stack-item ui-draggable ui-draggable-handle wpcg-post" style="background-image:url(' <?php echo $bg; ?>')"  >
		<div class="col-md-1 card-body grid-stack-item-content">
              <div>
		<?php echo $post->ID; ?>
			</div>
		</div>
	
	</div>
	
	<?php
}
	
	?>
	
	<div style="clear: both;"></div>
	<button class="button" onClick="saveGrid()">Save</button>
        <div class="col-md-2 d-none d-md-block" style="border-right: 1px dashed white;">
          <div id="trash" style="padding: 15px; margin: 15px; border: 1px solid #ff9900;" class="text-center text-white ui-droppable">
            <div>
              <span>Drop here to remove widget!</span>
            </div>
          </div>

        </div>
        <div class="col-sm-12 col-md-10" style="padding-bottom: 25px;">
          <div style="padding: 15px; border: 1px solid white;">
            <div class="grid-stack  grid-stack-animate ui-droppable grid-stack-12 grid-layout" id="advanced-grid" data-gs-width="12" data-gs-animate="yes" data-gs-column="12" data-gs-current-row="6" >
				
			  <div class="grid-stack-item" data-gs-x="0" data-gs-y="0" data-gs-width="4" data-gs-height="2">
				<div class="grid-stack-item-content">col1</div>
			  </div>

            </div>
          </div>
        </div>
      </div>
		

	</div>
<div style="clear: both;"></div>
 	<script type="text/javascript">

    var advGrid = GridStack.init(
		{
      alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
        navigator.userAgent
      ),
      resizable: {
        handles: 'e, se, s, sw, w'
      },
	  float: true,
	  verticalMargin:0,
      removable: '#trash',
      removeTimeout: 100,
      acceptWidgets: '.newWidget'
    	},  '#advanced-grid');
    
    $('.newWidget').draggable({
      revert: 'invalid',
      scroll: false,
      appendTo: 'body',
      helper: 'clone'
    });

	var grid = GridStack.init();
		

		
	saveGrid = function() {
	 
      serializedData = [];

      grid.engine.nodes.forEach(function(node) {
        serializedData.push({
          x: node.x,
          y: node.y,
          width: node.width,
          height: node.height,
		  pid: node.el.dataset.pid //post ID
        });
      });
		
		console.log(serializedData);
//      document.querySelector('#saved-data').value = JSON.stringify(serializedData, null, '  ');
		      document.querySelector('#saved-data').value = JSON.stringify(serializedData);

    };
  </script>
		</div>
	</div>
<?php
}
	
public static function get_instance() {
	if ( ! isset( self::$instance ) ) {
		self::$instance = new self();
	}

	return self::$instance;
}
	

}

add_action( 'plugins_loaded', function () {
	WPCG_Plugin::get_instance();
} );



